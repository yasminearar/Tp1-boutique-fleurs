<?php
namespace App\Routes;

class Route {
    // Stockage des routes
    private static array $routes = [];
    
    // Méthode pour définir une route GET
    public static function get(string $uri, array $action): void {
        self::$routes['GET'][$uri] = $action;
    }
    
    // Méthode pour définir une route POST
    public static function post(string $uri, array $action): void {
        self::$routes['POST'][$uri] = $action;
    }
    
    // Méthode pour définir des routes pour toutes les méthodes HTTP
    public static function any(string $uri, array $action): void {
        self::$routes['GET'][$uri] = $action;
        self::$routes['POST'][$uri] = $action;
    }    // Méthode pour dispatch les requêtes
    public static function dispatch(): void {
        // Vérifier d'abord si on a un paramètre 'url' (pour .htaccess comme dans la séance 18)
        if (isset($_GET['url'])) {
            $uri = '/' . $_GET['url'];
        } else {
            // URI actuelle (sans paramètres de requête) - méthode originale
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
          
        // Retirer le chemin de base (si le site est dans un sous-répertoire)
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        
        // Déboguer
        error_log('REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
        error_log('SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME']);
        error_log('Base Path: ' . $basePath);
        error_log('URI avant: ' . $uri);
          // Simplifier: utiliser directement la partie après le BASE_PATH
        $appBasePath = BASE; // Utiliser la constante BASE définie dans config.php
        if (strpos($uri, $appBasePath) === 0) {
            $uri = substr($uri, strlen($appBasePath));
            if (empty($uri)) {
                $uri = '/';
            }
        } elseif ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            if (empty($uri)) {
                $uri = '/';
            }
        }
        
        // Assurer que l'URI commence par /
        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }
        
        error_log('URI après: ' . $uri);
        
        // Méthode de requête HTTP
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Vérifier si la route existe
        if (isset(self::$routes[$method][$uri])) {
            // Récupérer le contrôleur et la méthode
            $controllerName = self::$routes[$method][$uri][0];
            $action = self::$routes[$method][$uri][1];
            
            // Créer une instance du contrôleur
            $controller = new $controllerName();
            
            // Appeler la méthode
            $controller->$action();
            return;
        }
        
        // Vérifier les routes avec paramètres
        foreach (self::$routes[$method] ?? [] as $route => $action) {
            // Convertir la route en expression régulière
            $pattern = self::convertRouteToRegex($route);
            
            // Vérifier si l'URI correspond au pattern
            if (preg_match($pattern, $uri, $matches)) {
                // Supprimer la première correspondance (correspondance complète)
                array_shift($matches);
                
                // Récupérer le contrôleur et la méthode
                $controllerName = $action[0];
                $methodName = $action[1];
                
                // Créer une instance du contrôleur
                $controller = new $controllerName();
                
                // Appeler la méthode avec les paramètres
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }
          // Route non trouvée - Page 404
        header("HTTP/1.0 404 Not Found");
        try {
            // Utiliser Twig pour afficher une page d'erreur 404 stylée
            echo \App\Views\View::render('error', [
                'code' => 404,
                'message' => 'La page que vous recherchez n\'existe pas.'
            ]);
        } catch (\Exception $e) {
            // Fallback au cas où Twig ne fonctionne pas
            echo "<h1>404 - Page non trouvée</h1>";
            echo "<p>La page que vous recherchez n'existe pas.</p>";
            echo "<p><a href='/'>Retourner à l'accueil</a></p>";
        }
    }
      // Convertir une route avec paramètres en expression régulière
    private static function convertRouteToRegex(string $route): string {
        // Assurer que la route commence par /
        if (substr($route, 0, 1) !== '/') {
            $route = '/' . $route;
        }
        
        // Remplacer les paramètres {param} par des groupes de capture
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        
        // Ajouter les délimiteurs et ancres
        return "#^$pattern$#";
    }
}
