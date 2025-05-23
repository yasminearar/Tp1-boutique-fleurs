<?php
// classes/Plante.php
require_once 'Connexion.php';

class Plante {
    private $pdo;

    public function __construct() {
        $this->pdo = Connexion::getPDO();
    }

    public function ajouter($nom, $description, $prix, $taille, $exposition, $stock, $image_url, $id_categorie): bool {
        $sql = "INSERT INTO plantes (nom, description, prix, taille, exposition, stock, image_url, id_categorie)
                VALUES (:nom, :description, :prix, :taille, :exposition, :stock, :image_url, :id_categorie)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'prix' => $prix,
            'taille' => $taille,
            'exposition' => $exposition,
            'stock' => $stock,
            'image_url' => $image_url,
            'id_categorie' => $id_categorie
        ]);
    }

    public function lister(): array {
        $sql = "SELECT p.*, c.nom AS nom_categorie
                FROM plantes p
                LEFT JOIN categories c ON p.id_categorie = c.id
                ORDER BY p.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $sql = "SELECT * FROM plantes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifier($id, $nom, $description, $prix, $taille, $exposition, $stock, $image_url, $id_categorie): bool {
        $sql = "UPDATE plantes SET nom = :nom, description = :description, prix = :prix,
                taille = :taille, exposition = :exposition, stock = :stock,
                image_url = :image_url, id_categorie = :id_categorie WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nom' => $nom,
            'description' => $description,
            'prix' => $prix,
            'taille' => $taille,
            'exposition' => $exposition,
            'stock' => $stock,
            'image_url' => $image_url,
            'id_categorie' => $id_categorie
        ]);
    }

    public function supprimer($id): bool {
        $sql = "DELETE FROM plantes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}


