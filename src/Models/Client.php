<?php
namespace App\Models;

/**
 * Modèle pour la table 'clients'
 */
class Client extends CRUD {
    /**
     * @var string Nom de la table associée au modèle
     */
    protected $table = 'clients';
    
    /**
     * Colonnes autorisées pour l'insertion/mise à jour
     */
    protected $fillable = ['nom', 'prenom', 'email', 'adresse', 'telephone'];
    
    /**
     * Récupère toutes les commandes d'un client
     * 
     * @param int $clientId ID du client
     * @return array Liste des commandes du client
     */
    public function getCommandesByClient(int $clientId): array {
        $sql = "SELECT * FROM commandes WHERE id_client = :clientId ORDER BY id DESC";
        $stmt = $this->prepare($sql);
        $stmt->execute(['clientId' => $clientId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Validation des données pour la création ou la mise à jour d'un client
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation (vide si pas d'erreurs)
     */
    public function validate(array $data): array {
        $errors = [];
        
        // Validation du nom
        if (empty($data['nom'])) {
            $errors['nom'] = "Le nom est obligatoire.";
        } elseif (strlen($data['nom']) > 100) {
            $errors['nom'] = "Le nom ne doit pas dépasser 100 caractères.";
        }
        
        // Validation du prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = "Le prénom est obligatoire.";
        } elseif (strlen($data['prenom']) > 100) {
            $errors['prenom'] = "Le prénom ne doit pas dépasser 100 caractères.";
        }
        
        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (strlen($data['email']) > 150) {
            $errors['email'] = "L'email ne doit pas dépasser 150 caractères.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email n'est pas valide.";
        } else {
            // Vérifier si l'email existe déjà (sauf si c'est le même client)
            $clientId = $data['id'] ?? 0;
            
            $stmt = $this->prepare("SELECT id FROM $this->table WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $data['email'], 'id' => $clientId]);
            
            if ($stmt->rowCount() > 0) {
                $errors['email'] = "Cet email est déjà utilisé par un autre client.";
            }
        }
          // Validation du téléphone (optionnel, mais format valide si fourni)
        if (!empty($data['telephone']) && strlen($data['telephone']) > 20) {
            $errors['telephone'] = "Le numéro de téléphone ne doit pas dépasser 20 caractères.";
        }
        
        return $errors;
    }
    /**
     * Récupère le nom complet du client
     * 
     * @param int $clientId ID du client
     * @return string Nom complet du client
     */
    public function getNomComplet(int $clientId): string {
        $sql = "SELECT prenom, nom FROM $this->table WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->execute(['id' => $clientId]);
        $client = $stmt->fetch();
        
        if (!$client) {
            return '';
        }
        
        return $client['prenom'] . ' ' . $client['nom'];
    }
    
    /**
     * Recherche des clients par nom, prénom ou email
     * 
     * @param string $keyword Mot-clé de recherche
     * @return array Résultats de la recherche
     */
    public function search(string $keyword): array {
        $sql = "SELECT * FROM $this->table 
                WHERE nom LIKE :keyword 
                OR prenom LIKE :keyword 
                OR email LIKE :keyword
                ORDER BY nom ASC, prenom ASC";
                
        $stmt = $this->prepare($sql);
        $stmt->execute(['keyword' => "%$keyword%"]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les clients avec leur nombre de commandes
     * 
     * @return array Clients avec le compteur de commandes
     */
    public function withCommandCount(): array {
        $sql = "SELECT c.*, COUNT(co.id) as nombre_commandes 
                FROM $this->table c 
                LEFT JOIN commandes co ON c.id = co.id_client 
                GROUP BY c.id 
                ORDER BY c.nom ASC, c.prenom ASC";
        
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les clients qui ont passé au moins une commande
     * 
     * @return array Clients avec au moins une commande
     */
    public function withOrders(): array {
        $sql = "SELECT DISTINCT c.* 
                FROM $this->table c 
                INNER JOIN commandes co ON c.id = co.id_client 
                ORDER BY c.nom ASC, c.prenom ASC";
        
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
}
