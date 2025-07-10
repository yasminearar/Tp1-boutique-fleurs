<?php
namespace App\Controllers;

use App\Views\View;

/**
 * Classe Controller de base
 * Fournit des méthodes communes à tous les contrôleurs
 */
abstract class Controller {
    /**
     * Rend une vue avec les données spécifiées
     *
     * @param string $template Nom du template à rendre
     * @param array $data Données à passer au template
     * @return string HTML rendu
     */
    protected function render(string $template, array $data = []): string {
        return View::render($template, $data);
    }
    
    /**
     * Affiche une vue et termine le script
     *
     * @param string $template Nom du template à rendre
     * @param array $data Données à passer au template
     */
    protected function display(string $template, array $data = []): void {
        View::display($template, $data);
    }
      /**
     * Redirige vers l'URL spécifiée
     *
     * @param string $url URL de redirection (relative à la racine du site)
     */    protected function redirect(string $url): void {        // Préfixe l'URL avec le chemin de base si elle ne commence pas par http:// ou https://
        if (!preg_match('/^https?:\/\//', $url)) {
            $basePath = BASE; // Utiliser la constante BASE définie dans config.php
            // Éviter les doubles slashes
            $url = $basePath . ($url[0] === '/' ? $url : '/' . $url);
        }
        
        // Debug information
        error_log("Redirection vers: " . $url);
        
        // Nettoyage du buffer de sortie qui pourrait empêcher la redirection
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Forcer l'arrêt de tout traitement et rediriger
        header("Location: $url", true, 302);
        echo '<script>window.location.href="' . $url . '";</script>';
        echo 'Si vous n\'êtes pas redirigé automatiquement, <a href="' . $url . '">cliquez ici</a>.';
        exit;
    }
    
    /**
     * Vérifie si la requête est de type POST
     *
     * @return bool True si la requête est de type POST
     */
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Vérifie si la requête est de type GET
     *
     * @return bool True si la requête est de type GET
     */
    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Récupère une valeur de $_GET avec filtrage
     *
     * @param string $key Clé dans $_GET
     * @param int $filter Filtre à appliquer (constantes FILTER_*)
     * @param mixed $default Valeur par défaut
     * @return mixed Valeur filtrée ou valeur par défaut
     */
    protected function getParam(string $key, int $filter = FILTER_DEFAULT, $default = null) {
        return filter_input(INPUT_GET, $key, $filter) ?: $default;
    }
    
    /**
     * Récupère une valeur de $_POST avec filtrage
     *
     * @param string $key Clé dans $_POST
     * @param int $filter Filtre à appliquer (constantes FILTER_*)
     * @param mixed $default Valeur par défaut
     * @return mixed Valeur filtrée ou valeur par défaut
     */
    protected function postParam(string $key, int $filter = FILTER_DEFAULT, $default = null) {
        return filter_input(INPUT_POST, $key, $filter) ?: $default;
    }
    
    /**
     * Récupère toutes les valeurs de $_POST
     *
     * @return array Données POST
     */
    protected function getAllPostParams(): array {
        return $_POST;
    }
    
    /**
     * Affiche une page d'erreur
     *
     * @param int $code Code HTTP de l'erreur
     * @param string $message Message d'erreur
     */
    protected function error(int $code, string $message): void {
        http_response_code($code);
        $this->display('error', [
            'code' => $code,
            'message' => $message
        ]);
    }
    
    /**
     * Ajoute un message de notification en session
     *
     * @param string $message Message à afficher
     * @param string $type Type de message (success, error, info, warning)
     */
    protected function addFlashMessage(string $message, string $type = 'info'): void {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        
        $_SESSION['flash_messages'][] = [
            'message' => $message,
            'type' => $type
        ];
    }
    
    /**
     * Récupère et supprime les messages de notification
     *
     * @return array Messages de notification
     */
    protected function getFlashMessages(): array {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        
        return $messages;
    }
    
    /**
     * Vérifie si un utilisateur est authentifié
     * 
     * @return bool True si authentifié
     */
    protected function isAuthenticated(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Récupère l'utilisateur connecté
     * 
     * @return array|null Utilisateur connecté ou null
     */
    protected function getCurrentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     * 
     * @return int|null ID utilisateur ou null
     */
    protected function getCurrentUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Vérifie si l'utilisateur a un privilège spécifique
     * 
     * @param string $privilege Privilège à vérifier
     * @return bool True si l'utilisateur a le privilège
     */
    protected function hasPrivilege(string $privilege): bool {
        return isset($_SESSION['privilege']) && $_SESSION['privilege'] === $privilege;
    }
    
    /**
     * Vérifie si l'utilisateur est admin
     * 
     * @return bool True si admin
     */
    protected function isAdmin(): bool {
        return $this->hasPrivilege('admin');
    }
    
    /**
     * Redirige vers la page de connexion si non authentifié
     */
    protected function requireAuth(): void {
        if (!$this->isAuthenticated()) {
            $this->addFlashMessage('Vous devez être connecté pour accéder à cette page', 'error');
            $this->redirect('/login');
        }
    }
    
    /**
     * Vérifie l'authentification sauf pour les pages autorisées sans connexion
     * Utilisé au début de chaque méthode contrôleur qui doit être protégée
     * 
     * @param string $route La route actuelle
     */
    public function checkAuthentication(string $route = ''): void {
        // Les routes autorisées sans authentification
        $publicRoutes = [
            '/',             // Page d'accueil
            '/login',        // Page de connexion
            '/authenticate', // Processus d'authentification 
            '/register',     // Page d'inscription
            '/force-logout', // Pour debug
            '/logout'        // Déconnexion
        ];
        
        // Log pour débogage
        error_log("checkAuthentication - Route actuelle: " . $route);
        
        // Vérification spéciale pour la page d'accueil - plusieurs URLs peuvent y conduire
        $isHomePage = ($route === '/' || $route === '/Tp1-boutique-fleurs/' || $route === '/Tp1-boutique-fleurs' || $route === '');
        
        error_log("checkAuthentication - Est page d'accueil: " . ($isHomePage ? 'OUI' : 'NON'));
        error_log("checkAuthentication - Route est publique: " . ((in_array($route, $publicRoutes) || $isHomePage) ? 'OUI' : 'NON'));
        error_log("checkAuthentication - Utilisateur authentifié: " . ($this->isAuthenticated() ? 'OUI' : 'NON'));
        
        // Si la route n'est pas dans les routes publiques et n'est pas la page d'accueil, exiger l'authentification
        if (!in_array($route, $publicRoutes) && !$isHomePage && !$this->isAuthenticated()) {
            // Stocker l'URL que l'utilisateur essayait d'accéder pour pouvoir y revenir après connexion
            $_SESSION['requested_page'] = $route;
            
            error_log("checkAuthentication - Redirection vers login car route non publique et utilisateur non authentifié");
            $this->addFlashMessage('Vous devez être connecté pour accéder à cette page', 'error');
            $this->redirect('/login');
        }
    }
    
    /**
     * Redirige vers la page d'accueil si non admin
     */
    protected function requireAdmin(): void {
        $this->requireAuth();
        
        if (!$this->isAdmin()) {
            $this->addFlashMessage('Accès refusé. Privilèges administrateur requis', 'error');
            $this->redirect('/');
        }
    }
}
