<?php
// classes/Categorie.php
require_once 'Connexion.php';

class Categorie {
    private $pdo;

    public function __construct() {
        $this->pdo = Connexion::getPDO();
    }    public function ajouter($nom, $description = null): bool {
        $sql = "INSERT INTO categories (nom, description) VALUES (:nom, :description)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['nom' => $nom, 'description' => $description]);
    }

    public function lister(): array {
        $sql = "SELECT * FROM categories ORDER BY nom ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    public function modifier($id, $nom, $description = null): bool {
        $sql = "UPDATE categories SET nom = :nom, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id, 'nom' => $nom, 'description' => $description]);
    }

    public function supprimer($id): bool {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}


