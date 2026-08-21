<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Validator;
use App\Repositories\BienRepository;

final class HomeController extends Controller
{
    public function index(): string
    {
        $biens = (new BienRepository())->featured(6);
        return $this->view('home', [
            'titre' => 'Agence Immobilière — Location à Dakar',
            'biens' => $biens,
        ]);
    }

    public function services(): string
    {
        return $this->view('services', ['titre' => 'Nos services']);
    }

    public function apropos(): string
    {
        return $this->view('apropos', ['titre' => 'À propos']);
    }

    public function contact(): string
    {
        return $this->view('contact', ['titre' => 'Contact']);
    }

    public function contactEnvoi(): string
    {
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required('nom', 'Nom')
          ->required('email', 'Email')->email('email', 'Email')
          ->required('message', 'Message')->min('message', 'Message', 10);

        if ($v->fails()) {
            foreach ($v->messages() as $m) {
                Flash::error($m);
            }
            $this->flashOld(['nom', 'email', 'message']);
            redirect('contact');
        }

        // Dans une vraie app : envoi d'email / enregistrement. Ici on confirme.
        $this->clearOld();
        Flash::success('Merci, votre message a bien été envoyé. Nous vous répondrons vite.');
        redirect('contact');
    }
}
