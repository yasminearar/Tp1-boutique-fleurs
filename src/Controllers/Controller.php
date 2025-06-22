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
}
