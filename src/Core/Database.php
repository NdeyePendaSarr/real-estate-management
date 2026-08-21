<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Point d'accès unique à la connexion PDO (MySQL/MariaDB).
 * Requêtes préparées, exceptions activées, fetch associatif par défaut.
 */
final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function connection(array $config): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['name']
        );

        try {
            self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('Connexion base de données impossible : ' . $e->getMessage());
            http_response_code(500);
            exit('Service momentanément indisponible.');
        }

        return self::$instance;
    }
}
