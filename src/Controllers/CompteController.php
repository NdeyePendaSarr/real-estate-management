<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Repositories\ReservationRepository;
use App\Repositories\UtilisateurRepository;

final class CompteController extends Controller
{
    public function index(): string
    {
        Auth::requireLogin();
        $user = (new UtilisateurRepository())->find((int) Auth::id());
        $reservations = (new ReservationRepository())->forUser((int) Auth::id());

        return $this->view('compte.index', [
            'titre'        => 'Mon compte',
            'utilisateur'  => $user,
            'reservations' => $reservations,
        ]);
    }

    public function desactiver(): string
    {
        Auth::requireLogin();
        Csrf::verify();

        (new UtilisateurRepository())->setStatut((int) Auth::id(), 'inactif');
        Auth::logout();
        Flash::success('Votre compte a été désactivé.');
        redirect('/');
    }
}
