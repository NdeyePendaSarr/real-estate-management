<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Contrôleur de base : raccourcis de rendu, d'entrée et de redirection.
 */
abstract class Controller
{
    protected function view(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        // Titre par défaut si non fourni.
        $data += ['titre' => 'Agence Immobilière'];
        return View::render($template, $data, $layout);
    }

    /** Entrée POST brute (à valider/échapper ensuite). */
    protected function input(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }

    /** Entrée GET brute. */
    protected function query(string $key, string $default = ''): string
    {
        return trim((string) ($_GET[$key] ?? $default));
    }

    /** Mémorise les anciennes entrées pour repeupler un formulaire. */
    protected function flashOld(array $only = []): void
    {
        $data = $only === [] ? $_POST : array_intersect_key($_POST, array_flip($only));
        unset($data['_token'], $data['password'], $data['password_confirm']);
        $_SESSION['_old'] = $data;
    }

    protected function clearOld(): void
    {
        unset($_SESSION['_old']);
    }
}
