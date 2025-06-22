<?php
namespace App\Models;

/**
 * Modèle pour la table 'commande_details'
 */
class CommandeDetail extends CRUD {
    /**
     * @var string Nom de la table associée au modèle
     */
    protected static string $table = 'commande_details';
    
    /**
     * Crée un nouveau détail de commande
     * 
     * @param int $commandeId ID de la commande
     * @param int $planteId ID de la plante
     * @param int $quantite Quantité commandée
     * @param float $prixUnitaire Prix unitaire au moment de la commande
     * @return int ID du détail créé
     */
    public static function ajouterDetail(int $commandeId, int $planteId, int $quantite, float $prixUnitaire): int {
        return self::create([
            'id_commande' => $commandeId,
            'id_plante' => $planteId,
            'quantite' => $quantite,
            'prix_unitaire' => $prixUnitaire
        ]);
    }
    
    /**
     * Récupère les détails d'une commande
     * 
     * @param int $commandeId ID de la commande
     * @return array Détails de la commande
     */
    public static function getDetailsByCommande(int $commandeId): array {
        return self::where(['id_commande' => $commandeId]);
    }
    
    /**
     * Récupère les détails d'une commande avec les informations des plantes
     * 
     * @param int $commandeId ID de la commande
     * @return array Détails de la commande avec informations des plantes
     */    public static function getDetailsWithPlantes(int $commandeId): array {
        self::initDb();
        
        $sql = "SELECT cd.*, p.nom, p.image_url, p.description, c.nom as categorie_nom 
                FROM commande_details cd
                JOIN plantes p ON cd.id_plante = p.id
                LEFT JOIN categories c ON p.id_categorie = c.id
                WHERE cd.id_commande = :id_commande
                ORDER BY cd.id ASC";
                
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute(['id_commande' => $commandeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Supprime tous les détails liés à une commande
     * 
     * @param int $commandeId ID de la commande
     * @return bool Succès de l'opération
     */
    public static function deleteByCommandeId(int $commandeId): bool {
        self::initDb();
        
        $sql = "DELETE FROM " . self::$table . " WHERE id_commande = :id_commande";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute(['id_commande' => $commandeId]);
    }
}
