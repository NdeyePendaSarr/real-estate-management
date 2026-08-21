<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Repositories\BienRepository;
use App\Repositories\FavoriRepository;
use App\Repositories\ImageRepository;

final class BienController extends Controller
{
    public function index(): string
    {
        $repo = new BienRepository();

        $filtres = [
            'type'     => $this->query('type'),
            'ville'    => $this->query('ville'),
            'statut'   => $this->query('statut'),
            'prix_min' => $this->query('prix_min'),
            'prix_max' => $this->query('prix_max'),
            'q'        => $this->query('q'),
        ];
        $page = max(1, (int) $this->query('page', '1'));
        $parPage = 9;

        $res = $repo->search($filtres, $page, $parPage);

        $favoris = Auth::check()
            ? (new FavoriRepository())->idsForUser((int) Auth::id())
            : [];

        return $this->view('biens.index', [
            'titre'      => 'Nos biens à louer',
            'biens'      => $res['items'],
            'total'      => $res['total'],
            'page'       => $page,
            'parPage'    => $parPage,
            'pages'      => (int) ceil($res['total'] / $parPage),
            'filtres'    => $filtres,
            'villes'     => $repo->villes(),
            'favoris'    => $favoris,
        ]);
    }

    public function show(array $params): string
    {
        $id = (int) ($params['id'] ?? 0);
        $bien = (new BienRepository())->find($id);

        if ($bien === null) {
            http_response_code(404);
            return $this->view('errors.404', ['titre' => 'Bien introuvable']);
        }

        $images = (new ImageRepository())->forBien($id);
        $estFavori = Auth::check()
            && (new FavoriRepository())->exists((int) Auth::id(), $id);

        return $this->view('biens.show', [
            'titre'     => $bien['titre'],
            'bien'      => $bien,
            'images'    => $images,
            'estFavori' => $estFavori,
        ]);
    }
}
