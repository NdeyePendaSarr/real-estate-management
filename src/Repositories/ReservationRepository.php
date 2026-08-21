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

    /**
     * Changement de statut côté admin, robuste.
     * - Vérifie que la réservation existe (rowCount / SELECT).
     * - À la confirmation, revalide l'absence de conflit avec une autre
     *   réservation CONFIRMÉE du même bien, sous verrou (transaction).
     *
     * @return string 'ok' | 'introuvable' | 'conflit' | 'erreur'
     */
    public function changeStatut(int $id, string $statut): string
    {
        // en_attente / annulee : pas de risque de double-réservation.
        if ($statut !== 'confirmee') {
            $stmt = $this->db->prepare('UPDATE reservations SET statut = :s WHERE id = :id');
            $stmt->execute(['s' => $statut, 'id' => $id]);
            return $stmt->rowCount() > 0 ? 'ok' : 'introuvable';
        }

        // Confirmation : re-validation du conflit sous verrou.
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'SELECT bien_id, date_debut, date_fin FROM reservations WHERE id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $id]);
            $res = $stmt->fetch();
            if (!$res) {
                $this->db->rollBack();
                return 'introuvable';
            }

            // Verrou sur le bien : sérialise les confirmations concurrentes.
            $lock = $this->db->prepare('SELECT id FROM biens WHERE id = :b FOR UPDATE');
            $lock->execute(['b' => $res['bien_id']]);

            // Une autre réservation DÉJÀ CONFIRMÉE chevauche-t-elle la période ?
            $conflit = $this->db->prepare(
                "SELECT 1 FROM reservations
                 WHERE bien_id = :b AND id <> :id AND statut = 'confirmee'
                   AND date_debut <= :fin AND :debut <= date_fin
                 LIMIT 1"
            );
            $conflit->execute([
                'b'     => $res['bien_id'],
                'id'    => $id,
                'fin'   => $res['date_fin'],
                'debut' => $res['date_debut'],
            ]);
            if ($conflit->fetchColumn()) {
                $this->db->rollBack();
                return 'conflit';
            }

            $upd = $this->db->prepare("UPDATE reservations SET statut = 'confirmee' WHERE id = :id");
            $upd->execute(['id' => $id]);

            $this->db->commit();
            return 'ok';
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Confirmation réservation impossible : ' . $e->getMessage());
            return 'erreur';
        }
    }

    /**
     * Annulation par le client, avec distinction des cas.
     * @return string 'ok' | 'introuvable' | 'non_autorisee' | 'deja_annulee'
     */
    public function cancelForUser(int $id, int $userId): string
    {
        $stmt = $this->db->prepare(
            'SELECT utilisateur_id, statut FROM reservations WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();

        if (!$res) {
            return 'introuvable';
        }
        if ((int) $res['utilisateur_id'] !== $userId) {
            return 'non_autorisee';
        }
        if ($res['statut'] === 'annulee') {
            return 'deja_annulee';
        }

        $upd = $this->db->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = :id");
        $upd->execute(['id' => $id]);
        return 'ok';
    }

    public function countByStatut(string $statut): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM reservations WHERE statut = :s');
        $stmt->execute(['s' => $statut]);
        return (int) $stmt->fetchColumn();
    }
}
