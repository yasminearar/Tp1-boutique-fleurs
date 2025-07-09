<?php
namespace App\Models;

/**
 * Modèle pour la table 'commande_details'
 */
class CommandeDetail extends CRUD {
    /**
     * Table associée au modèle
     */
    protected $table = 'commande_details';
    
    /**
     * Colonnes autorisées pour l'insertion/mise à jour
     */
    protected $fillable = ['id_commande', 'id_plante', 'quantite', 'prix_unitaire'];
    
    /**
     * Crée un nouveau détail de commande
     * 
     * @param int $commandeId ID de la commande
     * @param int $planteId ID de la plante
     * @param int $quantite Quantité commandée
     * @param float $prixUnitaire Prix unitaire au moment de la commande
     * @return int ID du détail créé
     */
    public function ajouterDetail(int $commandeId, int $planteId, int $quantite, float $prixUnitaire): int {
        return $this->insert([
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
    public function getDetailsByCommande(int $commandeId): array {
        $sql = "SELECT * FROM $this->table WHERE id_commande = :id_commande";
        $stmt = $this->prepare($sql);
        $stmt->execute(['id_commande' => $commandeId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les détails d'une commande avec les informations des plantes
     * 
     * @param int $commandeId ID de la commande
     * @return array Détails de la commande avec informations des plantes
     */
    public function getDetailsWithPlantes(int $commandeId): array {
        $sql = "SELECT cd.*, p.nom, p.image_url, p.description, c.nom as categorie_nom 
                FROM $this->table cd
                JOIN plantes p ON cd.id_plante = p.id
                LEFT JOIN categories c ON p.id_categorie = c.id
                WHERE cd.id_commande = :id_commande
                ORDER BY cd.id ASC";
                
        $stmt = $this->prepare($sql);
        $stmt->execute(['id_commande' => $commandeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Supprime tous les détails liés à une commande
     * 
     * @param int $commandeId ID de la commande
     * @return bool Succès de l'opération
     */
    public function deleteByCommandeId(int $commandeId): bool {
        $sql = "DELETE FROM $this->table WHERE id_commande = :id_commande";
        $stmt = $this->prepare($sql);
        return $stmt->execute(['id_commande' => $commandeId]);
    }
}
