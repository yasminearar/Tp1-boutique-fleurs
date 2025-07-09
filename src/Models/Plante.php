<?php
namespace App\Models;

/**
 * Modèle pour la table 'plantes'
 */
class Plante extends CRUD {
    /**
     * Table associée au modèle
     */
    protected $table = 'plantes';
    
    /**
     * Colonnes autorisées pour l'insertion/mise à jour
     */
    protected $fillable = ['nom', 'description', 'prix', 'image_url', 'id_categorie', 'stock', 'taille', 'exposition'];
      /**
     * Récupère la catégorie associée à cette plante
     * 
     * @param int $planteId ID de la plante
     * @return array|false Catégorie associée ou false si non trouvée
     */    public function getCategorie(int $planteId) {
        $sql = "SELECT id_categorie FROM $this->table WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->execute(['id' => $planteId]);
        $plante = $stmt->fetch();
        
        if (!$plante || !isset($plante['id_categorie']) || empty($plante['id_categorie'])) {
            return false;
        }
        
        $categorie = new Categorie();
        return $categorie->selectId($plante['id_categorie']);
    }
    
    /**
     * Recherche des plantes par nom ou description
     * 
     * @param string $keyword Mot-clé de recherche
     * @param int|null $categorieId ID de catégorie optionnel pour filtrer
     * @return array Résultats de la recherche
     */
    public function search(string $keyword, ?int $categorieId = null): array {
        $params = ['keyword' => "%$keyword%"];
          if ($categorieId) {
            $sql = "SELECT p.*, c.nom as categorie_nom 
                   FROM $this->table p 
                   LEFT JOIN categories c ON p.id_categorie = c.id
                   WHERE (p.nom LIKE :keyword OR p.description LIKE :keyword) 
                   AND p.id_categorie = :categorieId";
            $params['categorieId'] = $categorieId;
        } else {
            $sql = "SELECT p.*, c.nom as categorie_nom 
                   FROM $this->table p 
                   LEFT JOIN categories c ON p.id_categorie = c.id
                   WHERE p.nom LIKE :keyword OR p.description LIKE :keyword";
        }
        
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les plantes avec leur catégorie associée
     * 
     * @return array Liste des plantes avec leur catégorie
     */    public function withCategories(): array {
        $sql = "SELECT p.*, c.nom as categorie_nom 
               FROM $this->table p 
               LEFT JOIN categories c ON p.id_categorie = c.id";
        
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }/**
     * Validation des données pour la création ou la mise à jour d'une plante
     * 
     * @param array $data Données à valider
     * @return array Erreurs de validation (vide si pas d'erreurs)
     */
    public function validate(array $data): array {
        $errors = [];
        
        // Validation du nom
        if (empty($data['nom'])) {
            $errors['nom'] = "Le nom de la plante est obligatoire.";
        } elseif (strlen($data['nom']) > 100) {
            $errors['nom'] = "Le nom de la plante ne doit pas dépasser 100 caractères.";
        }
        
        // Validation du prix
        if (empty($data['prix'])) {
            $errors['prix'] = "Le prix est obligatoire.";
        } elseif (!is_numeric($data['prix']) || $data['prix'] < 0) {
            $errors['prix'] = "Le prix doit être un nombre positif.";
        }
        
        // Validation du stock
        if (isset($data['stock']) && ($data['stock'] < 0 || !is_numeric($data['stock']))) {
            $errors['stock'] = "Le stock doit être un nombre positif.";
        }
        
        // Validation de la taille
        if (!empty($data['taille']) && strlen($data['taille']) > 50) {
            $errors['taille'] = "La taille ne doit pas dépasser 50 caractères.";
        }
        
        // Validation de l'exposition
        if (!empty($data['exposition']) && strlen($data['exposition']) > 100) {
            $errors['exposition'] = "L'exposition ne doit pas dépasser 100 caractères.";
        }
        
        // Validation de l'URL de l'image
        if (!empty($data['image_url']) && strlen($data['image_url']) > 255) {
            $errors['image_url'] = "L'URL de l'image ne doit pas dépasser 255 caractères.";
        }          // Validation de la catégorie
        if (!empty($data['id_categorie'])) {
            $categorie = new Categorie();
            $cat = $categorie->selectId($data['id_categorie']);
            if (!$cat) {
                $errors['id_categorie'] = "La catégorie sélectionnée n'existe pas.";
            }
        }
        
        return $errors;
    }
    
    /**
     * Récupère les plantes en stock
     * 
     * @return array Plantes en stock
     */
    public function inStock(): array {
        $sql = "SELECT * FROM $this->table WHERE stock > 0";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère les plantes avec leur prix formaté
     * 
     * @return array Plantes avec prix formaté
     */
    public function withFormattedPrices(): array {
        $plantes = $this->select();
        
        foreach ($plantes as &$plante) {
            $plante['prix_formatte'] = number_format($plante['prix'], 2, ',', ' ') . ' $';
        }
        
        return $plantes;
    }    /**
     * Diminue le stock d'une plante
     * 
     * @param int $planteId ID de la plante
     * @param int $quantity Quantité à déduire
     * @return bool True si l'opération a réussi, false sinon
     */
    public function decreaseStock(int $planteId, int $quantity = 1): bool {
        // Vérifier que le stock est suffisant
        $sql = "SELECT stock FROM $this->table WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->execute(['id' => $planteId]);
        $plante = $stmt->fetch();
        
        if (!$plante || $plante['stock'] < $quantity) {
            return false;
        }
        
        // Mettre à jour le stock
        $sql = "UPDATE $this->table SET stock = stock - :quantity WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->execute([
            'id' => $planteId,
            'quantity' => $quantity
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Récupère le prix d'une plante
     * 
     * @param int $planteId ID de la plante
     * @return float Prix de la plante ou 0 si non trouvée
     */
    public function getPrix(int $planteId): float {
        $sql = "SELECT prix FROM $this->table WHERE id = :id";
        $stmt = $this->prepare($sql);
        $stmt->execute(['id' => $planteId]);
        $plante = $stmt->fetch();
        
        return $plante ? (float)$plante['prix'] : 0.0;
    }
    
    /**
     * Récupère les plantes d'une catégorie spécifique
     * 
     * @param int $categorieId ID de la catégorie
     * @return array Liste des plantes de la catégorie
     */    public function getByCategory($categorieId): array {
        $sql = "SELECT p.*, c.nom as categorie_nom 
               FROM $this->table p 
               LEFT JOIN categories c ON p.id_categorie = c.id
               WHERE p.id_categorie = :categorieId";
        
        $stmt = $this->prepare($sql);
        $stmt->execute(['categorieId' => $categorieId]);
        return $stmt->fetchAll();
    }
}
