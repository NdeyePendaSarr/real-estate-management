<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Repositories\ReservationRepository;

final class ReservationController extends Controller
{
    public function index(): string
    {
        Auth::requireRole('commercial', 'admin');
        return $this->view('admin.reservations', [
            'titre'        => 'Réservations',
            'reservations' => (new ReservationRepository())->all(),
        ]);
    }

    public function updateStatut(array $params): string
    {
        Auth::requireRole('commercial', 'admin');
        Csrf::verify();

        $id = (int) ($params['id'] ?? 0);
        $statut = $this->input('statut');

        if (!in_array($statut, ['en_attente', 'confirmee', 'annulee'], true)) {
            Flash::error('Statut invalide.');
            redirect('admin/reservations');
        }

        (new ReservationRepository())->updateStatut($id, $statut);
        Flash::success('Statut mis à jour.');
        redirect('admin/reservations');
    }
}
