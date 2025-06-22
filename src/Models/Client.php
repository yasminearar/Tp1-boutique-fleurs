<?php
namespace App\Models;

/**
 * Modèle pour la table 'clients'
 */
class Client extends CRUD {
    /**
     * @var string Nom de la table associée au modèle
     */
    protected static string $table = 'clients';
    
    /**
     * Récupère toutes les commandes d'un client
     * 
     * @param int $clientId ID du client
     * @return array Liste des commandes du client
     */
    public static function getCommandesByClient(int $clientId): array {
        return Commande::where(['id_client' => $clientId]);
    }
    
    /**
     * Validation des données pour la création ou la mise à jour d'un client
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation (vide si pas d'erreurs)
     */
    public static function validate(array $data): array {
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
            
            self::initDb();
            $stmt = self::$pdo->prepare("SELECT id FROM " . self::$table . " WHERE email = :email AND id != :id");
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
    public static function getNomComplet(int $clientId): string {
        self::initDb();
        
        $sql = "SELECT prenom, nom FROM " . self::$table . " WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
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
    public static function search(string $keyword): array {
        self::initDb();
        
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE nom LIKE :keyword 
                OR prenom LIKE :keyword 
                OR email LIKE :keyword
                ORDER BY nom ASC, prenom ASC";
                
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['keyword' => "%$keyword%"]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les clients avec leur nombre de commandes
     * 
     * @return array Clients avec le compteur de commandes
     */
    public static function withCommandCount(): array {
        self::initDb();
        
        $sql = "SELECT c.*, COUNT(co.id) as nombre_commandes 
                FROM " . self::$table . " c 
                LEFT JOIN commandes co ON c.id = co.id_client 
                GROUP BY c.id 
                ORDER BY c.nom ASC, c.prenom ASC";
        
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les clients qui ont passé au moins une commande
     * 
     * @return array Clients avec au moins une commande
     */
    public static function withOrders(): array {
        self::initDb();
        
        $sql = "SELECT DISTINCT c.* 
                FROM " . self::$table . " c 
                INNER JOIN commandes co ON c.id = co.id_client 
                ORDER BY c.nom ASC, c.prenom ASC";
        
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll();
    }
}
