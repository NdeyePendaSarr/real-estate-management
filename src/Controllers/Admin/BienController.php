<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Upload;
use App\Core\Validator;
use App\Repositories\BienRepository;
use App\Repositories\ImageRepository;

final class BienController extends Controller
{
    private const TYPES = ['appartement', 'villa'];
    private const STATUTS = ['disponible', 'loue'];

    public function index(): string
    {
        Auth::requireRole('commercial', 'admin');
        return $this->view('admin.biens.index', [
            'titre' => 'Gestion des biens',
            'biens' => (new BienRepository())->all(),
        ]);
    }

    public function create(): string
    {
        Auth::requireRole('commercial', 'admin');
        return $this->view('admin.biens.form', [
            'titre'  => 'Ajouter un bien',
            'bien'   => null,
            'action' => url('admin/biens'),
        ]);
    }

    public function store(): string
    {
        Auth::requireRole('commercial', 'admin');
        Csrf::verify();

        $data = $this->valider();
        if ($data === null) {
            redirect('admin/biens/nouveau');
        }

        $repo = new BienRepository();
        $id = $repo->create($data);

        $this->traiterImages($id, true);

        $this->clearOld();
        Flash::success('Bien ajouté avec succès.');
        redirect('admin/biens');
    }

    public function edit(array $params): string
    {
        Auth::requireRole('commercial', 'admin');
        $id = (int) ($params['id'] ?? 0);
        $bien = (new BienRepository())->find($id);
        if ($bien === null) {
            http_response_code(404);
            return $this->view('errors.404', ['titre' => 'Bien introuvable']);
        }

        return $this->view('admin.biens.form', [
            'titre'  => 'Modifier le bien',
            'bien'   => $bien,
            'images' => (new ImageRepository())->forBien($id),
            'action' => url('admin/biens/' . $id),
        ]);
    }

    public function update(array $params): string
    {
        Auth::requireRole('commercial', 'admin');
        Csrf::verify();

        $id = (int) ($params['id'] ?? 0);
        if ((new BienRepository())->find($id) === null) {
            http_response_code(404);
            return $this->view('errors.404', ['titre' => 'Bien introuvable']);
        }

        $data = $this->valider();
        if ($data === null) {
            redirect('admin/biens/' . $id . '/modifier');
        }

        (new BienRepository())->update($id, $data);
        $this->traiterImages($id, false);

        $this->clearOld();
        Flash::success('Bien mis à jour.');
        redirect('admin/biens');
    }

    public function destroy(array $params): string
    {
        Auth::requireRole('commercial', 'admin');
        Csrf::verify();

        $id = (int) ($params['id'] ?? 0);
        $images = (new ImageRepository())->forBien($id);

        (new BienRepository())->delete($id); // CASCADE supprime images/favoris/résa en base
        foreach ($images as $img) {
            Upload::remove($img['fichier']);
        }

        Flash::success('Bien supprimé.');
        redirect('admin/biens');
    }

    /** Valide les champs du bien ; renvoie les données prêtes ou null si erreurs. */
    private function valider(): ?array
    {
        $v = new Validator($_POST);
        $v->required('titre', 'Titre')->min('titre', 'Titre', 3)
          ->required('description', 'Description')->min('description', 'Description', 10)
          ->required('prix', 'Prix')->numeric('prix', 'Prix')
          ->required('ville', 'Ville')
          ->in('type', 'Type', self::TYPES)
          ->in('statut', 'Statut', self::STATUTS)
          ->numeric('chambres', 'Chambres');

        if ($v->fails()) {
            foreach ($v->messages() as $m) {
                Flash::error($m);
            }
            $this->flashOld(['titre', 'description', 'prix', 'ville', 'type', 'statut', 'chambres', 'surface']);
            return null;
        }

        return [
            'type'        => $this->input('type'),
            'titre'       => $this->input('titre'),
            'description' => $this->input('description'),
            'prix'        => (int) $this->input('prix'),
            'ville'       => $this->input('ville'),
            'chambres'    => (int) ($this->input('chambres') ?: 1),
            'surface'     => $this->input('surface') !== '' ? (int) $this->input('surface') : null,
            'statut'      => $this->input('statut'),
        ];
    }

    /** Upload sécurisé des images ; la première image d'un bien neuf devient principale. */
    private function traiterImages(int $bienId, bool $premierePrincipale): void
    {
        if (empty($_FILES['images']['name'][0])) {
            return;
        }

        $res = Upload::images($_FILES['images']);
        foreach ($res['errors'] as $err) {
            Flash::error($err);
        }

        $imgRepo = new ImageRepository();
        $dejaImages = $imgRepo->forBien($bienId) !== [];
        foreach ($res['files'] as $index => $fichier) {
            $principale = $premierePrincipale && !$dejaImages && $index === 0;
            $imgRepo->add($bienId, $fichier, $principale);
        }
    }
}
