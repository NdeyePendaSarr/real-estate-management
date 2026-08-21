<?php

declare(strict_types=1);

/**
 * Chargement des variables d'environnement (fichier .env non versionné) et
 * exposition d'une configuration typée. Aucune dépendance externe.
 */

$envPath = dirname(__DIR__) . '/.env';
if (is_readable($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        [$name, $value] = array_pad(explode('=', $line, 2), 2, '');
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($name !== '' && getenv($name) === false) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

return [
    'app' => [
        'env'   => getenv('APP_ENV') ?: 'production',
        'debug' => filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOL),
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'agence_immobiliere',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
    ],
    'uploads' => [
        'dir'      => dirname(__DIR__) . '/public/uploads',
        'web'      => '/uploads',
        'max_size' => 4 * 1024 * 1024, // 4 Mo
        'mimes'    => ['image/jpeg', 'image/png', 'image/webp'],
    ],
];
