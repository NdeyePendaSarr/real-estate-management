<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Registry;

/**
 * Échappe une valeur pour un affichage HTML sûr (anti-XSS).
 * À utiliser SYSTÉMATIQUEMENT autour de toute donnée dynamique en sortie.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Jeton CSRF courant. */
function csrf_token(): string
{
    return Csrf::token();
}

/** Champ caché à insérer dans chaque formulaire POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/** URL absolue depuis la racine de l'application. */
function url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

/** URL d'un asset public (css, js, images). */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** URL web d'une image de bien (chemin relatif stocké en base). */
function upload_url(string $file): string
{
    return url(ltrim($file, '/'));
}

/** Redirection HTTP puis arrêt du script. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/** Formate un prix entier en FCFA lisible : 1500000 → "1 500 000 FCFA". */
function format_prix(int|string|null $prix): string
{
    if ($prix === null || $prix === '') {
        return '—';
    }
    return number_format((float) $prix, 0, ',', ' ') . ' FCFA';
}

/** Ancienne valeur d'un champ (repopulation de formulaire après erreur). */
function old(string $key, string $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

/** Libellé lisible d'un statut de réservation. */
function statut_label(string $statut): string
{
    return match ($statut) {
        'en_attente' => 'En attente',
        'confirmee'  => 'Confirmée',
        'annulee'    => 'Annulée',
        'disponible' => 'Disponible',
        'loue'       => 'Loué',
        default      => ucfirst($statut),
    };
}
