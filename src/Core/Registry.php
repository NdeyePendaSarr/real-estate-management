<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Petit registre applicatif : partage la configuration et la connexion PDO
 * sans recourir à des variables globales dispersées.
 */
final class Registry
{
    private static array $config = [];
    private static ?PDO $pdo = null;

    public static function boot(array $config, PDO $pdo): void
    {
        self::$config = $config;
        self::$pdo = $pdo;
    }

    public static function config(): array
    {
        return self::$config;
    }

    public static function pdo(): PDO
    {
        if (!self::$pdo instanceof PDO) {
            throw new \RuntimeException('PDO non initialisé.');
        }
        return self::$pdo;
    }
}
