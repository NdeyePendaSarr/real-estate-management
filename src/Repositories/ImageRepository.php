<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Registry;
use PDO;

/** Images rattachées à un bien. */
final class ImageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Registry::pdo();
    }

    public function forBien(int $bienId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM images WHERE bien_id = :id ORDER BY principale DESC, id ASC'
        );
        $stmt->execute(['id' => $bienId]);
        return $stmt->fetchAll();
    }

    public function add(int $bienId, string $fichier, bool $principale = false): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO images (bien_id, fichier, principale) VALUES (:b, :f, :p)'
        );
        $stmt->execute(['b' => $bienId, 'f' => $fichier, 'p' => $principale ? 1 : 0]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM images WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM images WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
