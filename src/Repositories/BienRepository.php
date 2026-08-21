<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Registry;
use PDO;

/**
 * Accès aux biens (appartements et villas unifiés). Toutes les requêtes sont
 * préparées ; aucune donnée utilisateur n'est concaténée dans le SQL.
 */
final class BienRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Registry::pdo();
    }

    /**
     * Recherche filtrée + pagination. Les filtres sont liés en paramètres.
     * @return array{items: array, total: int}
     */
    public function search(array $filtres, int $page = 1, int $parPage = 9): array
    {
        $where = ['b.archive = 0'];
        $params = [];

        if (!empty($filtres['type'])) {
            $where[] = 'b.type = :type';
            $params['type'] = $filtres['type'];
        }
        if (!empty($filtres['ville'])) {
            $where[] = 'b.ville = :ville';
            $params['ville'] = $filtres['ville'];
        }
        if (!empty($filtres['statut'])) {
            $where[] = 'b.statut = :statut';
            $params['statut'] = $filtres['statut'];
        }
        if (($filtres['prix_min'] ?? '') !== '') {
            $where[] = 'b.prix >= :prix_min';
            $params['prix_min'] = (int) $filtres['prix_min'];
        }
        if (($filtres['prix_max'] ?? '') !== '') {
            $where[] = 'b.prix <= :prix_max';
            $params['prix_max'] = (int) $filtres['prix_max'];
        }
        if (!empty($filtres['q'])) {
            $where[] = '(b.titre LIKE :q OR b.description LIKE :q)';
            $params['q'] = '%' . $filtres['q'] . '%';
        }

        $clause = implode(' AND ', $where);

        // Total (pour la pagination).
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM biens b WHERE {$clause}");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        // Page courante, avec l'image principale.
        $page = max(1, $page);
        $offset = ($page - 1) * $parPage;

        $sql = "SELECT b.*, (
                    SELECT i.fichier FROM images i
                    WHERE i.bien_id = b.id
                    ORDER BY i.principale DESC, i.id ASC LIMIT 1
                ) AS image
                FROM biens b
                WHERE {$clause}
                ORDER BY b.cree_le DESC, b.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', $parPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return ['items' => $stmt->fetchAll(), 'total' => $total];
    }

    /** Quelques biens à mettre en avant sur l'accueil. */
    public function featured(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT b.*, (
                SELECT i.fichier FROM images i
                WHERE i.bien_id = b.id ORDER BY i.principale DESC, i.id ASC LIMIT 1
             ) AS image
             FROM biens b
             WHERE b.statut = 'disponible' AND b.archive = 0
             ORDER BY b.cree_le DESC, b.id DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM biens WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $bien = $stmt->fetch();
        return $bien ?: null;
    }

    /** Liste distincte des villes présentes (pour le filtre). */
    public function villes(): array
    {
        return $this->db->query('SELECT DISTINCT ville FROM biens ORDER BY ville')
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    public function all(): array
    {
        return $this->db->query(
            "SELECT b.*, (SELECT COUNT(*) FROM images i WHERE i.bien_id = b.id) AS nb_images
             FROM biens b ORDER BY b.cree_le DESC, b.id DESC"
        )->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO biens (type, titre, description, prix, ville, chambres, surface, statut)
             VALUES (:type, :titre, :description, :prix, :ville, :chambres, :surface, :statut)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare(
            'UPDATE biens SET type = :type, titre = :titre, description = :description,
                prix = :prix, ville = :ville, chambres = :chambres, surface = :surface,
                statut = :statut
             WHERE id = :id'
        );
        $stmt->execute($data);
    }

    /**
     * Archive un bien plutôt que de le supprimer : préserve l'historique
     * (réservations, images) et le retire simplement du site public.
     */
    public function archive(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE biens SET archive = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** Restaure un bien archivé. */
    public function restore(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE biens SET archive = 0 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM biens WHERE archive = 0')->fetchColumn();
    }
}
