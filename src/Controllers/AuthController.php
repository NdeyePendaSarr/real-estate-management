<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Validator;
use App\Repositories\UtilisateurRepository;

final class AuthController extends Controller
{
    public function showLogin(): string
    {
        if (Auth::check()) {
            redirect('/');
        }
        return $this->view('auth.login', ['titre' => 'Connexion']);
    }

    public function login(): string
    {
        Csrf::verify();

        $email = $this->input('email');
        $password = $this->input('password');

        $repo = new UtilisateurRepository();
        $user = $repo->findByEmail($email);

        if ($user === null || !password_verify($password, $user['mot_de_passe'])) {
            Flash::error('Identifiants incorrects.');
            $this->flashOld(['email']);
            redirect('connexion');
        }

        if (($user['statut'] ?? 'actif') !== 'actif') {
            Flash::error('Ce compte est désactivé.');
            redirect('connexion');
        }

        Auth::login($user);
        $this->clearOld();
        Flash::success('Bienvenue, ' . $user['prenom'] . ' !');

        // Redirection selon le rôle.
        redirect(in_array($user['role'], ['commercial', 'admin'], true) ? 'admin' : 'compte');
    }

    public function showRegister(): string
    {
        if (Auth::check()) {
            redirect('/');
        }
        return $this->view('auth.register', ['titre' => 'Créer un compte']);
    }

    public function register(): string
    {
        Csrf::verify();

        $v = new Validator($_POST);
        $v->required('nom', 'Nom')
          ->required('prenom', 'Prénom')
          ->required('email', 'Email')->email('email', 'Email')
          ->required('password', 'Mot de passe')->min('password', 'Mot de passe', 8);

        if ($this->input('password') !== $this->input('password_confirm')) {
            $v->addError('password_confirm', 'Les mots de passe ne correspondent pas.');
        }

        $repo = new UtilisateurRepository();
        if (!$v->fails() && $repo->emailExists($this->input('email'))) {
            $v->addError('email', 'Un compte existe déjà avec cet email.');
        }

        if ($v->fails()) {
            foreach ($v->messages() as $m) {
                Flash::error($m);
            }
            $this->flashOld(['nom', 'prenom', 'email', 'telephone']);
            redirect('inscription');
        }

        $id = $repo->create([
            'nom'          => $this->input('nom'),
            'prenom'       => $this->input('prenom'),
            'email'        => $this->input('email'),
            'telephone'    => $this->input('telephone') ?: null,
            'mot_de_passe' => password_hash($this->input('password'), PASSWORD_DEFAULT),
            'role'         => 'client',
        ]);

        $this->clearOld();
        Auth::login($repo->find($id) ?? []);
        Flash::success('Compte créé. Bienvenue !');
        redirect('compte');
    }

    public function logout(): string
    {
        Csrf::verify();
        Auth::logout();
        Flash::success('Vous êtes déconnecté.');
        redirect('/');
    }
}
