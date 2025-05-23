<?php
// classes/Commande.php
require_once 'Connexion.php';

class Commande {
    private $pdo;

    public function __construct() {
        $this->pdo = Connexion::getPDO();
    }

    public function ajouter($id_client, $statut = 'en cours'): bool {
        $sql = "INSERT INTO commandes (id_client, statut)
                VALUES (:id_client, :statut)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id_client' => $id_client,
            'statut' => $statut
        ]);
    }

    public function lister(): array {
        $sql = "SELECT commandes.*, clients.nom, clients.prenom
                FROM commandes
                INNER JOIN clients ON commandes.id_client = clients.id
                ORDER BY commandes.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function trouverParId($id) {
        $sql = "SELECT * FROM commandes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifier($id, $id_client, $statut): bool {
        $sql = "UPDATE commandes SET id_client = :id_client, statut = :statut
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'id_client' => $id_client,
            'statut' => $statut
        ]);
    }

    public function supprimer($id): bool {
        $sql = "DELETE FROM commandes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
