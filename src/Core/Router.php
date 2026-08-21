<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Routeur minimal : associe (méthode HTTP + motif d'URL) à un couple
 * [Contrôleur, méthode]. Les segments {param} sont capturés et transmis.
 */
final class Router
{
    /** @var array<int, array{method:string, regex:string, params:array, handler:array}> */
    private array $routes = [];

    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, array $handler): void
    {
        $params = [];
        $regex = preg_replace_callback('#\{(\w+)\}#', function ($m) use (&$params) {
            $params[] = $m[1];
            return '([^/]+)';
        }, $pattern);

        $this->routes[] = [
            'method'  => $method,
            'regex'   => '#^' . rtrim($regex, '/') . '/?$#',
            'params'  => $params,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['regex'], $path, $matches)) {
                array_shift($matches);
                $args = array_combine($route['params'], $matches) ?: [];

                [$class, $action] = $route['handler'];
                $controller = new $class();
                echo $controller->$action($args);
                return;
            }
        }

        http_response_code(404);
        echo View::render('errors.404', [], 'layout');
    }
}
