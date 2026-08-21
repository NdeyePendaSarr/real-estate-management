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

    /**
     * Crée une réservation de façon sûre face aux accès concurrents.
     *
     * La vérification de disponibilité et l'insertion sont exécutées dans une
     * même transaction, sous un verrou sur la ligne du bien (SELECT ... FOR
     * UPDATE). Deux demandes simultanées sur le même bien sont ainsi
     * sérialisées : la seconde attend la fin de la première, ce qui élimine la
     * race condition « les deux vérifient libre, les deux insèrent ».
     *
     * @return string 'ok' | 'introuvable' | 'indisponible' | 'conflit' | 'erreur'
     */
    public function createIfAvailable(int $bienId, int $userId, string $debut, string $fin): string
    {
        try {
            $this->db->beginTransaction();

            // Verrou sur le bien : sérialise les réservations concurrentes du même bien.
            $stmt = $this->db->prepare('SELECT statut FROM biens WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $bienId]);
            $bien = $stmt->fetch();

            if (!$bien) {
                $this->db->rollBack();
                return 'introuvable';
            }
            if ($bien['statut'] !== 'disponible') {
                $this->db->rollBack();
                return 'indisponible';
            }

            // Chevauchement, évalué sous le verrou.
            $conflit = $this->db->prepare(
                "SELECT 1 FROM reservations
                 WHERE bien_id = :b AND statut <> 'annulee'
                   AND date_debut <= :fin AND :debut <= date_fin
                 LIMIT 1"
            );
            $conflit->execute(['b' => $bienId, 'fin' => $fin, 'debut' => $debut]);
            if ($conflit->fetchColumn()) {
                $this->db->rollBack();
                return 'conflit';
            }

            $ins = $this->db->prepare(
                'INSERT INTO reservations (bien_id, utilisateur_id, date_debut, date_fin)
                 VALUES (:b, :u, :d, :f)'
            );
            $ins->execute(['b' => $bienId, 'u' => $userId, 'd' => $debut, 'f' => $fin]);

            $this->db->commit();
            return 'ok';
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Réservation impossible : ' . $e->getMessage());
            return 'erreur';
        }
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
