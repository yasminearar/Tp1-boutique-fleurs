<?php
namespace App\Providers;

class Auth {
    /**
     * Vérifie si la session est active et valide
     * @param bool $redirect Rediriger vers la page de login si la session n'est pas valide
     * @return bool True si la session est valide
     */
    static public function session($redirect = true) {
        if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] == md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'])) {
            return true;
        } else {
            if ($redirect) {
                $currentPage = $_SERVER['REQUEST_URI'] ?? '';
                if (strpos($currentPage, BASE . '/login') === false) {
                    header('Location: ' . BASE . '/login');
                    exit();
                }
            }
            return false;
        }
    }
    
    /**
     * Vérifie si l'utilisateur a le privilège spécifié
     * @param int $id ID du privilège à vérifier
     * @param bool $redirect Rediriger vers la page de login si l'utilisateur n'a pas le privilège
     * @return bool True si l'utilisateur a le privilège
     */
    static public function privilege($id, $redirect = true) {
        if(isset($_SESSION['privilege_id']) && $_SESSION['privilege_id'] == $id) {
            return true;
        } else {
            if ($redirect) {
                $currentPage = $_SERVER['REQUEST_URI'] ?? '';
                if (strpos($currentPage, BASE . '/login') === false) {
                    header('Location: ' . BASE . '/login');
                    exit();
                }
            }
            return false;
        }
    }
    
    /**
     * Initialise les paramètres de session sécurisés
     * À appeler avant session_start()
     */
    static public function init() {
        ini_set('session.cookie_lifetime', 7200);
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_domain', '');
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Lax');
        session_name('BOUTIQUE_PLANTES_SESSION');
    }
    
    /**
     * Termine la session en toute sécurité
     */
    static public function logout() {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: ' . BASE . '/login');
        exit();
    }
}
?>
