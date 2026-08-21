<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Authentification par session. Un utilisateur porte un rôle
 * ('client', 'commercial', 'admin') qui pilote les accès.
 */
final class Auth
{
    /** Ouvre une session authentifiée (régénère l'ID pour éviter la fixation). */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'     => (int) $user['id'],
            'nom'    => $user['nom'],
            'prenom' => $user['prenom'],
            'email'  => $user['email'],
            'role'   => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function is(string ...$roles): bool
    {
        return self::check() && in_array($_SESSION['user']['role'], $roles, true);
    }

    /** Exige une session ; sinon redirige vers la connexion. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            Flash::error('Connecte-toi pour accéder à cette page.');
            redirect('connexion');
        }
    }

    /** Exige un rôle précis ; sinon 403. */
    public static function requireRole(string ...$roles): void
    {
        self::requireLogin();
        if (!self::is(...$roles)) {
            http_response_code(403);
            exit('Accès refusé.');
        }
    }
}
