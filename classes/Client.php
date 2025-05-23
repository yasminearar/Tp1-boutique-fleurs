<?php
// classes/Client.php
require_once 'Connexion.php';

class Client {
    private $pdo;

    public function __construct() {
        $this->pdo = Connexion::getPDO();
    }

    public function ajouter($nom, $prenom, $email, $adresse, $telephone): bool {
        $sql = "INSERT INTO clients (nom, prenom, email, adresse, telephone)
                VALUES (:nom, :prenom, :email, :adresse, :telephone)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'adresse' => $adresse,
            'telephone' => $telephone
        ]);
    }

    public function lister(): array {
        $sql = "SELECT * FROM clients ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $sql = "SELECT * FROM clients WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifier($id, $nom, $prenom, $email, $adresse, $telephone): bool {
        $sql = "UPDATE clients SET nom = :nom, prenom = :prenom, email = :email,
                adresse = :adresse, telephone = :telephone WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'adresse' => $adresse,
            'telephone' => $telephone
        ]);
    }

    public function supprimer($id): bool {
        $sql = "DELETE FROM clients WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
