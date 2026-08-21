<?php

declare(strict_types=1);

namespace App\Core;

/** Messages éphémères affichés au chargement suivant (succès / erreur). */
final class Flash
{
    public static function set(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    public static function success(string $m): void { self::set('success', $m); }
    public static function error(string $m): void { self::set('error', $m); }

    /** Récupère et vide tous les messages. */
    public static function pull(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }
}
