<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Rendu de vues PHP avec un layout commun. Les données sont extraites en
 * variables locales ; la vue produit un $contenu injecté dans le layout.
 */
final class View
{
    private const VIEWS = __DIR__ . '/../../views/';

    public static function render(string $template, array $data = [], ?string $layout = 'layout'): string
    {
        $contenu = self::renderPartial($template, $data);

        if ($layout === null) {
            return $contenu;
        }

        return self::renderPartial($layout, array_merge($data, ['contenu' => $contenu]));
    }

    public static function renderPartial(string $template, array $data = []): string
    {
        $file = self::VIEWS . str_replace('.', '/', $template) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Vue introuvable : {$template}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    /** Envoie directement une vue au navigateur. */
    public static function display(string $template, array $data = [], ?string $layout = 'layout'): void
    {
        echo self::render($template, $data, $layout);
    }
}
