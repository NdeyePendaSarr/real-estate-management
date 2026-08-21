<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Registry;
use PDO;

/** Favoris d'un utilisateur (contrainte d'unicité en base : pas de doublon). */
final class FavoriRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Registry::pdo();
    }

    /** Bascule un favori et renvoie true s'il est désormais ajouté. */
    public function toggle(int $userId, int $bienId): bool
    {
        if ($this->exists($userId, $bienId)) {
            $stmt = $this->db->prepare(
                'DELETE FROM favoris WHERE utilisateur_id = :u AND bien_id = :b'
            );
            $stmt->execute(['u' => $userId, 'b' => $bienId]);
            return false;
        }
        $stmt = $this->db->prepare(
            'INSERT INTO favoris (utilisateur_id, bien_id) VALUES (:u, :b)'
        );
        $stmt->execute(['u' => $userId, 'b' => $bienId]);
        return true;
    }

    public function exists(int $userId, int $bienId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT 1 FROM favoris WHERE utilisateur_id = :u AND bien_id = :b'
        );
        $stmt->execute(['u' => $userId, 'b' => $bienId]);
        return (bool) $stmt->fetchColumn();
    }

    /** IDs des biens favoris (pour marquer les cartes). */
    public function idsForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT bien_id FROM favoris WHERE utilisateur_id = :u'
        );
        $stmt->execute(['u' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Biens favoris complets, avec image principale. */
    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, (
                 SELECT i.fichier FROM images i
                 WHERE i.bien_id = b.id ORDER BY i.principale DESC, i.id ASC LIMIT 1
             ) AS image
             FROM favoris f
             JOIN biens b ON b.id = f.bien_id
             WHERE f.utilisateur_id = :u
             ORDER BY f.cree_le DESC"
        );
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }
}
