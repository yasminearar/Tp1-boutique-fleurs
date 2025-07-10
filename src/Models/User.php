<?php
namespace App\Models;

/**
 * Modèle pour la table 'users'
 */
class User extends CRUD {
    /**
     * Table associée au modèle
     */
    protected $table = 'users';

    protected $fillable = ['name', 'username', 'password', 'email', 'privilege_id'];
    
    /**
     * Authentifie un utilisateur
     * 
     * @param string $username Nom d'utilisateur ou email
     * @param string $password Mot de passe en clair
     * @return array|false Utilisateur authentifié ou false
     */
    public function authenticate(string $username, string $password) {
        $sql = "SELECT u.*, p.privilege 
                FROM $this->table u 
                LEFT JOIN privileges p ON u.privilege_id = p.id 
                WHERE u.username = :username OR u.email = :username";
        
        $stmt = $this->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Authentifie un utilisateur et crée sa session
     * 
     * @param string $username Nom d'utilisateur ou email
     * @param string $password Mot de passe en clair
     * @return bool Succès de l'authentification
     */
    public function checkUser(string $username, string $password): bool {
        $user = $this->authenticate($username, $password);
        
        if ($user) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['privilege_id'] = $user['privilege_id'];
            $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
            $_SESSION['LAST_ACTIVITY'] = time();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie si un nom d'utilisateur existe
     * 
     * @param string $username Nom d'utilisateur
     * @return bool True si existe
     */
    public function usernameExists(string $username): bool {
        $sql = "SELECT COUNT(*) FROM $this->table WHERE username = :username";
        $stmt = $this->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Vérifie si un email existe
     * 
     * @param string $email Email
     * @return bool True si existe
     */
    public function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) FROM $this->table WHERE email = :email";
        $stmt = $this->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $data Données utilisateur
     * @return bool|int ID de l'utilisateur créé ou false
     */
    public function createUser(array $data) {
        // Hasher le mot de passe
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->insert($data);
    }
    
    /**
     * Récupère un utilisateur avec son privilège
     * 
     * @param int $userId ID de l'utilisateur
     * @return array|false Utilisateur avec privilège ou false
     */
    public function getUserWithPrivilege(int $userId) {
        $sql = "SELECT u.*, p.privilege 
                FROM $this->table u 
                LEFT JOIN privileges p ON u.privilege_id = p.id 
                WHERE u.id = :id";
        
        $stmt = $this->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();
        
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    /**
     * Met à jour le mot de passe d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param string $newPassword Nouveau mot de passe
     * @return bool Succès de la mise à jour
     */
    public function updatePassword(int $userId, string $newPassword): bool {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE $this->table SET password = :password WHERE id = :id";
        $stmt = $this->prepare($sql);
        
        return $stmt->execute([
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }
}
