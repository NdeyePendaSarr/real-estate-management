<?php

declare(strict_types=1);

/**
 * Configuration de l'application.
 *
 * Les variables sont chargées depuis le fichier .env sans dépendre de
 * getenv()/putenv(), certains hébergeurs mutualisés pouvant limiter
 * la gestion des variables d'environnement.
 */

$envPath = dirname(__DIR__) . '/.env';

$env = [];

if (is_readable($envPath)) {
    $lines = file(
        $envPath,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);

            // Ignorer les commentaires
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Vérifier qu'il existe bien un =
            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);

            $name = trim($name);

            // Supprimer les guillemets éventuels
            $value = trim(
                $value,
                " \t\n\r\0\x0B\"'"
            );

            if ($name !== '') {
                $env[$name] = $value;
            }
        }
    }
}

/**
 * Configuration de l'application.
 */
return [
    'app' => [
        'env' => $env['APP_ENV'] ?? 'production',

        'debug' => filter_var(
            $env['APP_DEBUG'] ?? 'false',
            FILTER_VALIDATE_BOOL
        ),
    ],

    /**
     * Configuration de la base de données.
     */
    'db' => [
        'host' => $env['DB_HOST'] ?? '127.0.0.1',
        'name' => $env['DB_NAME'] ?? 'agence_immobiliere',
        'user' => $env['DB_USER'] ?? 'root',
        'pass' => $env['DB_PASS'] ?? '',
    ],

    /**
     * Configuration des uploads.
     */
    'uploads' => [
        'dir' => dirname(__DIR__) . '/public/uploads',

        'web' => '/uploads',

        'max_size' => 4 * 1024 * 1024,

        'mimes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],
    ],
];