<?php

declare(strict_types=1);

/**
 * Front controller : point d'entrée unique. Toutes les requêtes passent ici
 * (voir .htaccess), sont routées, puis rendues.
 */

// Sert directement les fichiers statiques existants avec le serveur intégré PHP.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

/** @var \App\Core\Router $router */
$router = require __DIR__ . '/../src/Core/bootstrap.php';

require __DIR__ . '/../routes.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
