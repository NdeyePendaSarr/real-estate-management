<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Registry;
use PDO;

/** Réservations de biens par les clients. */
final class ReservationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Registry::pdo();
    }

    /**
     * Vérifie qu'aucune réservation active (non annulée) ne chevauche la
     * période demandée pour ce bien. Deux périodes se chevauchent si
     * debut1 <= fin2 ET debut2 <= fin1.
     */
    public function hasConflict(int $bienId, string $debut, string $fin): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM reservations
             WHERE bien_id = :b AND statut <> 'annulee'
               AND date_debut <= :fin AND :debut <= date_fin
             LIMIT 1"
        );
        $stmt->execute(['b' => $bienId, 'debut' => $debut, 'fin' => $fin]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(int $bienId, int $userId, string $debut, string $fin): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO reservations (bien_id, utilisateur_id, date_debut, date_fin)
             VALUES (:b, :u, :d, :f)'
        );
        $stmt->execute(['b' => $bienId, 'u' => $userId, 'd' => $debut, 'f' => $fin]);
        return (int) $this->db->lastInsertId();
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, b.titre, b.type, b.ville, b.prix
             FROM reservations r
             JOIN biens b ON b.id = r.bien_id
             WHERE r.utilisateur_id = :u
             ORDER BY r.cree_le DESC"
        );
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query(
            "SELECT r.*, b.titre, b.type,
                    u.nom, u.prenom, u.email
             FROM reservations r
             JOIN biens b ON b.id = r.bien_id
             JOIN utilisateurs u ON u.id = r.utilisateur_id
             ORDER BY r.cree_le DESC"
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM reservations WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function updateStatut(int $id, string $statut): void
    {
        $stmt = $this->db->prepare('UPDATE reservations SET statut = :s WHERE id = :id');
        $stmt->execute(['s' => $statut, 'id' => $id]);
    }

    /** Un client ne peut annuler que sa propre réservation. */
    public function cancelForUser(int $id, int $userId): void
    {
        $stmt = $this->db->prepare(
            "UPDATE reservations SET statut = 'annulee'
             WHERE id = :id AND utilisateur_id = :u"
        );
        $stmt->execute(['id' => $id, 'u' => $userId]);
    }

    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE statut = :s');
        $stmt->execute(['s' => $statut]);
        return (int) $stmt->fetchColumn();
    }
}
