<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Repositories\BienRepository;
use App\Repositories\ReservationRepository;
use App\Repositories\UtilisateurRepository;

final class DashboardController extends Controller
{
    public function index(): string
    {
        Auth::requireRole('commercial', 'admin');

        $reservations = new ReservationRepository();

        return $this->view('admin.dashboard', [
            'titre'       => 'Tableau de bord',
            'nbBiens'     => (new BienRepository())->count(),
            'nbClients'   => (new UtilisateurRepository())->countClients(),
            'nbEnAttente' => $reservations->countByStatut('en_attente'),
            'nbConfirmees'=> $reservations->countByStatut('confirmee'),
        ]);
    }
}
