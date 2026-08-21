<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Amorçage de l'application : autoloading, session durcie, configuration,
 * connexion et gestion des erreurs. Renvoie l'instance du routeur prête.
 */

// --- Autoloading : Composer si présent, sinon autoloader PSR-4 maison. ---
$vendor = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (is_file($vendor)) {
    require $vendor;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = dirname(__DIR__) . '/' . $relative . '.php';
        if (is_file($file)) {
            require $file;
        }
    });
    require __DIR__ . '/helpers.php';
}

// --- Configuration ---
$config = require dirname(__DIR__, 2) . '/config/config.php';

// --- Affichage des erreurs selon l'environnement ---
error_reporting(E_ALL);
ini_set('display_errors', $config['app']['debug'] ? '1' : '0');

// --- Session durcie ---
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => ($_SERVER['HTTPS'] ?? '') === 'on',
]);
session_start();

// --- Connexion + registre ---
$pdo = Database::connection($config['db']);
Registry::boot($config, $pdo);

return new Router();
