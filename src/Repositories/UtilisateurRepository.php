<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Registry;
use PDO;

/** Comptes utilisateurs (clients, commerciaux, admin) unifiés. */
final class UtilisateurRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Registry::pdo();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateurs WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateurs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM utilisateurs WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role)
             VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :role)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function setStatut(int $id, string $statut): void
    {
        $stmt = $this->db->prepare('UPDATE utilisateurs SET statut = :s WHERE id = :id');
        $stmt->execute(['s' => $statut, 'id' => $id]);
    }

    public function countClients(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'"
        )->fetchColumn();
    }
}
