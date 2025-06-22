<?php
namespace App\Models;

/**
 * Modèle pour la table 'commandes'
 */
class Commande extends CRUD {
    /**
     * @var string Nom de la table associée au modèle
     */
    protected static string $table = 'commandes';
    
    /**
     * Liste des statuts possibles pour une commande
     * 
     * @var array
     */
    public const STATUTS = [
        'en cours' => 'En cours',
        'expédiée' => 'Expédiée',
        'livrée' => 'Livrée'
    ];
      /**
     * Récupère le client associé à cette commande
     * 
     * @param int $commandeId ID de la commande
     * @return array|false Client associé ou false si non trouvé
     */
    public static function getClient(int $commandeId) {
        self::initDb();
        
        $sql = "SELECT id_client FROM " . self::$table . " WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['id' => $commandeId]);
        $commande = $stmt->fetch();
        
        if (!$commande || !isset($commande['id_client'])) {
            return false;
        }
        
        return Client::find($commande['id_client']);
    }
    
    /**
     * Récupère les commandes avec les informations du client
     * 
     * @return array Liste des commandes avec les détails du client
     */
    public static function withClients(): array {
        self::initDb();
        
        $sql = "SELECT c.*, cl.nom as client_nom, cl.prenom as client_prenom, cl.email as client_email 
                FROM " . self::$table . " c 
                LEFT JOIN clients cl ON c.id_client = cl.id";
        
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Validation des données pour la création ou la mise à jour d'une commande
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation (vide si pas d'erreurs)
     */
    public static function validate(array $data): array {
        $errors = [];        // Validation de l'ID client (accepte 'client_id' ou 'id_client')
        if (!empty($data['client_id'])) {
            $clientId = $data['client_id'];
        } elseif (!empty($data['id_client'])) {
            $clientId = $data['id_client'];
        } else {
            $clientId = null;
        }
        
        if (empty($clientId)) {
            $errors['client_id'] = "L'identifiant du client est obligatoire.";
        } else {
            $client = Client::find($clientId);
            if (!$client) {
                $errors['client_id'] = "Le client sélectionné n'existe pas.";
            }
        }
          // Validation du statut
        if (!empty($data['statut']) && !array_key_exists($data['statut'], self::STATUTS)) {
            $errors['statut'] = "Le statut sélectionné n'est pas valide.";
        }
        
        return $errors;
    }
    
    /**
     * Récupère les commandes avec les informations des clients
     * 
     * @param string $statut Filtrer par statut (optionnel)
     * @return array Liste des commandes avec les détails du client
     */
    public static function withClientDetails(string $statut = ''): array {
        self::initDb();
        
        $sql = "SELECT c.*, cl.nom as client_nom, cl.prenom as client_prenom, cl.email as client_email 
                FROM " . self::$table . " c 
                LEFT JOIN clients cl ON c.id_client = cl.id";
        
        // Ajouter le filtre de statut si demandé
        if (!empty($statut)) {
            $sql .= " WHERE c.statut = :statut";
        }
        
        $sql .= " ORDER BY c.date_commande DESC";
        
        $stmt = self::$pdo->prepare($sql);
        
        if (!empty($statut)) {
            $stmt->execute(['statut' => $statut]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Met à jour le statut d'une commande
     * 
     * @param int $commandeId ID de la commande
     * @param string $statut Nouveau statut
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public static function updateStatut(int $commandeId, string $statut): bool {
        // Vérifier que le statut est valide
        if (!array_key_exists($statut, self::STATUTS)) {
            return false;
        }
        
        return self::update($commandeId, ['statut' => $statut]);
    }
  
    
    /**
     * Récupère les commandes récentes
     * 
     * @param int $limit Nombre de commandes à récupérer
     * @return array Commandes récentes
     */
    public static function getRecent(int $limit = 5): array {
        self::initDb();
        
        $sql = "SELECT c.*, cl.nom as client_nom, cl.prenom as client_prenom 
                FROM " . self::$table . " c 
                LEFT JOIN clients cl ON c.id_client = cl.id 
                ORDER BY c.date_commande DESC LIMIT :limit";
                
        $stmt = self::$pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
