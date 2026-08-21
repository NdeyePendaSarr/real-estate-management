<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Protection CSRF : un jeton par session, comparé à temps constant.
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function check(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['_csrf'])
            && hash_equals($_SESSION['_csrf'], $token);
    }

    /** Vérifie le jeton d'une requête POST, sinon coupe court (403). */
    public static function verify(): void
    {
        if (!self::check($_POST['_token'] ?? null)) {
            http_response_code(419);
            exit('Session expirée ou requête non autorisée. Reviens en arrière et réessaie.');
        }
    }
}
