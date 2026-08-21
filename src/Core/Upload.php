<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Upload d'images sécurisé. Contrairement à l'ancien code (qui déplaçait le
 * fichier tel quel), on valide le type réel (finfo), la taille, l'extension,
 * et on génère un nom aléatoire — impossible d'uploader/exécuter un .php.
 */
final class Upload
{
    /** @return array{files: string[], errors: string[]} */
    public static function images(array $fileInput): array
    {
        $config = Registry::config()['uploads'];
        $dir = $config['dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $extParMime = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        $files = [];
        $errors = [];

        // Normalise en liste (le champ peut être multiple).
        $noms = (array) ($fileInput['name'] ?? []);
        $count = count($noms);

        for ($i = 0; $i < $count; $i++) {
            $err = $fileInput['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            if ($err === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($err !== UPLOAD_ERR_OK) {
                $errors[] = "Échec de l'upload d'une image (code {$err}).";
                continue;
            }

            $tmp = $fileInput['tmp_name'][$i];
            $size = (int) ($fileInput['size'][$i] ?? 0);

            if ($size <= 0 || $size > $config['max_size']) {
                $errors[] = 'Chaque image doit peser moins de '
                    . round($config['max_size'] / 1024 / 1024) . ' Mo.';
                continue;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmp) ?: '';
            if (!in_array($mime, $config['mimes'], true)) {
                $errors[] = 'Format non autorisé (JPG, PNG ou WebP uniquement).';
                continue;
            }

            $ext = $extParMime[$mime];
            $nom = bin2hex(random_bytes(16)) . '.' . $ext;

            if (!is_uploaded_file($tmp) || !move_uploaded_file($tmp, $dir . '/' . $nom)) {
                $errors[] = 'Enregistrement de l\'image impossible.';
                continue;
            }
            $files[] = 'uploads/' . $nom;
        }

        return ['files' => $files, 'errors' => $errors];
    }

    /** Supprime physiquement un fichier uploadé (best-effort). */
    public static function remove(string $fichier): void
    {
        $path = Registry::config()['uploads']['dir'] . '/' . basename($fichier);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
