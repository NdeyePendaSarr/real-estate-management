<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Repositories\ReservationRepository;

final class ReservationController extends Controller
{
    public function index(): string
    {
        Auth::requireLogin();
        $reservations = (new ReservationRepository())->forUser((int) Auth::id());
        return $this->view('compte.reservations', [
            'titre'        => 'Mes réservations',
            'reservations' => $reservations,
        ]);
    }

    public function store(): string
    {
        Auth::requireLogin();
        Csrf::verify();

        $bienId = (int) $this->input('bien_id');
        $debut  = $this->input('date_debut');
        $fin    = $this->input('date_fin');

        // Validation des dates (données venant du client).
        $erreurs = $this->validerDates($debut, $fin);
        if ($erreurs !== []) {
            foreach ($erreurs as $m) {
                Flash::error($m);
            }
            redirect('biens/' . $bienId);
        }

        // Disponibilité + insertion dans une transaction verrouillée (anti-race condition,
        // et refus si le bien n'est pas disponible).
        $resultat = (new ReservationRepository())
            ->createIfAvailable($bienId, (int) Auth::id(), $debut, $fin);

        if ($resultat === 'ok') {
            Flash::success('Votre demande de réservation a été enregistrée. Elle est en attente de confirmation.');
            redirect('mes-reservations');
        }

        Flash::error(match ($resultat) {
            'introuvable'  => 'Ce bien est introuvable.',
            'indisponible' => 'Ce bien n\'est plus disponible à la réservation.',
            'conflit'      => 'Ce bien est déjà réservé sur cette période.',
            default        => 'Une erreur est survenue, merci de réessayer.',
        });
        redirect('biens/' . $bienId);
    }

    public function annuler(array $params): string
    {
        Auth::requireLogin();
        Csrf::verify();

        $id = (int) ($params['id'] ?? 0);
        $resultat = (new ReservationRepository())->cancelForUser($id, (int) Auth::id());

        match ($resultat) {
            'ok'            => Flash::success('Réservation annulée.'),
            'deja_annulee'  => Flash::error('Cette réservation est déjà annulée.'),
            'non_autorisee' => Flash::error('Vous ne pouvez pas annuler cette réservation.'),
            default         => Flash::error('Réservation introuvable.'),
        };
        redirect('mes-reservations');
    }

    /** @return string[] messages d'erreur */
    private function validerDates(string $debut, string $fin): array
    {
        $erreurs = [];
        $d = \DateTimeImmutable::createFromFormat('!Y-m-d', $debut);
        $f = \DateTimeImmutable::createFromFormat('!Y-m-d', $fin);
        $aujourdhui = new \DateTimeImmutable('today');

        if (!$d || !$f) {
            return ['Dates invalides.'];
        }
        if ($d < $aujourdhui) {
            $erreurs[] = 'La date de début ne peut pas être dans le passé.';
        }
        if ($f <= $d) {
            $erreurs[] = 'La date de fin doit être après la date de début.';
        }
        return $erreurs;
    }
}
