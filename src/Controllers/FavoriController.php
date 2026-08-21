<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Repositories\FavoriRepository;

final class FavoriController extends Controller
{
    public function index(): string
    {
        Auth::requireLogin();
        $biens = (new FavoriRepository())->forUser((int) Auth::id());
        return $this->view('compte.favoris', [
            'titre' => 'Mes favoris',
            'biens' => $biens,
        ]);
    }

    public function toggle(array $params): string
    {
        Auth::requireLogin();
        Csrf::verify();

        $bienId = (int) ($params['id'] ?? 0);
        $ajoute = (new FavoriRepository())->toggle((int) Auth::id(), $bienId);

        Flash::success($ajoute ? 'Ajouté à vos favoris.' : 'Retiré de vos favoris.');
        redirect($this->input('retour') ?: 'biens/' . $bienId);
    }
}
