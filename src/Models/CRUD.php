<?php
namespace App\Models;

abstract class CRUD extends \PDO {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];    final public function __construct() {
        parent::__construct('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';port=3306;charset=' . DB_CHARSET, DB_USER, DB_PASS);
    }
    
    /**
     * Récupère tous les enregistrements de la table
     * 
     * @param string|null $field Champ pour le tri
     * @param string $order Direction du tri ('asc' ou 'desc')
     * @return array|false Tableau d'enregistrements ou false en cas d'erreur
     */
    final public function select($field = null, $order = 'asc') {
        if($field == null) {
            $field = $this->primaryKey;
        }
        $sql = "SELECT * FROM $this->table ORDER BY $field $order";
        if($stmt = $this->query($sql)) {
            return $stmt->fetchAll();        } else {
            return false;
        }
    }
    
    /**
     * Récupère un enregistrement par son ID
     * 
     * @param int $value ID de l'enregistrement à récupérer
     * @return array|false Enregistrement trouvé ou false si non trouvé
     */
    final public function selectId($value) {
        $sql = "SELECT * FROM $this->table WHERE $this->primaryKey = :$this->primaryKey";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":$this->primaryKey", $value);
        $stmt->execute();
        $count = $stmt->rowCount();        if ($count == 1) {
            return $stmt->fetch();
        } else {
            return false;
        } 
    }
    
    // La méthode 'where' est remplacée par 'unique' dans la séance 18 pour la recherche par critères spécifiques
    
    /**
     * Insère un nouvel enregistrement dans la table
     * 
     * @param array $data Données à insérer
     * @return int|false ID de l'enregistrement inséré ou false en cas d'erreur
     */
    final public function insert($data) {
        $data_keys = array_fill_keys($this->fillable, '');
        $data = array_intersect_key($data, $data_keys);
 
        $fieldName = implode(', ', array_keys($data));
        $fieldValue = ":".implode(', :', array_keys($data));
        $sql = "INSERT INTO $this->table ($fieldName) VALUES ($fieldValue);";

        $stmt = $this->prepare($sql);
        foreach($data as $key=>$value) {
            $stmt->bindValue(":$key", $value);
        }        if($stmt->execute()) {
            return $this->lastInsertId();
        } else {
            return false;
        } 
    }
    
    /**
     * Met à jour un enregistrement existant
     * 
     * @param array $data Données à mettre à jour
     * @param int $id ID de l'enregistrement à mettre à jour
     * @return bool True si la mise à jour a réussi, false sinon
     */
    final public function update($data, $id) {
        $data_keys = array_fill_keys($this->fillable, '');
        $data = array_intersect_key($data, $data_keys);

        $fieldName = null;
        foreach($data as $key=>$value) {
            $fieldName .= "$key = :$key, ";
        }
        $fieldName = rtrim($fieldName, ', ');
        $sql = "UPDATE $this->table SET $fieldName WHERE $this->primaryKey = :$this->primaryKey";
        $data[$this->primaryKey] = $id;

        $stmt = $this->prepare($sql);
        foreach($data as $key=>$value) {
            $stmt->bindValue(":$key", $value);
        }        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Supprime un enregistrement existant
     * 
     * @param int $value ID de l'enregistrement à supprimer
     * @return bool True si la suppression a réussi, false sinon
     */
    final public function delete($value) {
        $sql = "DELETE FROM $this->table WHERE $this->primaryKey = :$this->primaryKey";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":$this->primaryKey", $value);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Exécute une requête SQL personnalisée
     * 
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return array Résultats de la requête
     */
    public function raw(string $sql, array $params = []): array {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre d'enregistrements dans la table
     * 
     * @param array $criteria Critères de filtrage facultatifs
     * @return int Nombre d'enregistrements
     */
    public function count(array $criteria = []): int {
        if (empty($criteria)) {
            $sql = "SELECT COUNT(*) as count FROM $this->table";
            $stmt = $this->query($sql);
        } else {
            $sql = "SELECT COUNT(*) as count FROM $this->table WHERE ";
            $conditions = [];
            $values = [];
            
            foreach ($criteria as $key => $value) {
                $conditions[] = "$key = :$key";
                $values[$key] = $value;
            }
            
            $sql .= implode(' AND ', $conditions);
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
        }
        
        return (int)$stmt->fetch()['count'];
    }

    /**
     * Récupère des enregistrements avec pagination
     * 
     * @param int $page Numéro de page
     * @param int $perPage Nombre d'enregistrements par page
     * @param array $orderBy Ordre de tri
     * @return array Tableau d'enregistrements avec métadonnées de pagination
     */
    public function paginate(int $page = 1, int $perPage = 10, array $orderBy = []): array {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM $this->table";
        
        // Ajouter l'ordre de tri
        if (!empty($orderBy)) {
            $sql .= " ORDER BY ";
            $orderClauses = [];
            
            foreach ($orderBy as $column => $direction) {
                $orderClauses[] = "$column $direction";
            }
            
            $sql .= implode(", ", $orderClauses);
        } else {
            $sql .= " ORDER BY $this->primaryKey ASC";
        }
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $items = $stmt->fetchAll();
        $total = $this->count();
        $lastPage = ceil($total / $perPage);
        
        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'data' => $items
        ];
    }

    /**
     * Vérifie si un enregistrement existe
     * 
     * @param array $criteria Critères de recherche
     * @return bool True si l'enregistrement existe, false sinon
     */
    public function exists(array $criteria): bool {
        return $this->count($criteria) > 0;
    }

    /**
     * Vérifie si une valeur est unique dans un champ spécifique
     * 
     * @param string $field Nom du champ
     * @param mixed $value Valeur à vérifier
     * @return array|false Résultat si trouvé, false sinon
     */
    public function unique($field, $value) {
        $sql = "SELECT * FROM $this->table WHERE $field = :$field";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":$field", $value);
        $stmt->execute();
        $count = $stmt->rowCount();
        if($count == 1) {
            return $stmt->fetch();
        } else {
            return false;
        }
    }
      // Aucun alias statique n'est nécessaire car la classe suit désormais l'approche de la séance 18
}
