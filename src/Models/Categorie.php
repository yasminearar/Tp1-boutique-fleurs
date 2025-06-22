<?php
namespace App\Models;

use \PDO;

/**
 * Modèle pour la table 'categories'
 */
class Categorie extends CRUD {
    /**
     * Table associée au modèle
     */
    protected $table = 'categories';
    
    /**
     * Colonnes autorisées pour l'insertion/mise à jour
     */
    protected $fillable = ['nom', 'description'];
      /**
     * Récupère toutes les plantes appartenant à cette catégorie
     * 
     * @param int $categorieId ID de la catégorie
     * @return array Liste des plantes de cette catégorie
     */
    public function getPlantesByCategorie(int $categorieId): array {
        $plante = new Plante();
        return $plante->getByCategory($categorieId);
    }
      
    /**
     * Validation des données pour la création ou la mise à jour d'une catégorie
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation (vide si pas d'erreurs)
     */
    public function validate(array $data): array {
        $errors = [];
        
        if (empty($data['nom'])) {
            $errors['nom'] = "Le nom de la catégorie est obligatoire.";
        } elseif (strlen($data['nom']) > 100) {
            $errors['nom'] = "Le nom de la catégorie ne doit pas dépasser 100 caractères.";
        }
        
        // La description est optionnelle, mais si présente, vérifier qu'elle n'est pas trop longue
        if (!empty($data['description']) && strlen($data['description']) > 1000) {
            $errors['description'] = "La description ne doit pas dépasser 1000 caractères.";
        }
        
        return $errors;
    }    /**
     * Récupère les catégories avec le nombre de plantes associées
     * 
     * @return array Catégories avec le compteur de plantes
     */    public function withPlantCount(): array {
        $sql = "SELECT c.*, COUNT(p.id) as nombre_plantes 
                FROM $this->table c 
                LEFT JOIN plantes p ON c.id = p.id_categorie 
                GROUP BY c.id 
                ORDER BY c.nom ASC";
        
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les catégories qui ont au moins une plante en stock
     * 
     * @return array Catégories avec des plantes en stock
     */    public function withPlantsInStock(): array {
        $sql = "SELECT DISTINCT c.* 
                FROM $this->table c 
                INNER JOIN plantes p ON c.id = p.id_categorie 
                WHERE p.stock > 0 
                ORDER BY c.nom ASC";
        
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifie si une catégorie a des plantes associées
     * 
     * @param int $categorieId ID de la catégorie
     * @return bool True si la catégorie a des plantes, false sinon
     */    public function hasPlants(int $categorieId): bool {
        $sql = "SELECT COUNT(*) as count FROM plantes WHERE id_categorie = :id";
        $stmt = $this->prepare($sql);
        $stmt->execute(['id' => $categorieId]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
}
