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

    public static function any(string $uri, array $action): void {
        self::$routes['GET'][$uri] = $action;
        self::$routes['POST'][$uri] = $action;
    }    // Méthode pour dispatch les requêtes
    public static function dispatch(): void {
        error_log("Route::dispatch - Début du dispatching");
        if (isset($_GET['url'])) {
            $uri = '/' . $_GET['url'];
            error_log("Route::dispatch - URL depuis _GET['url']: " . $uri);
        } else {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            error_log("Route::dispatch - URL depuis REQUEST_URI: " . $uri);
        }

        $basePath = dirname($_SERVER['SCRIPT_NAME']);

        $appBasePath = BASE;
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

        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }
        
        error_log('URI après: ' . $uri);

        $method = $_SERVER['REQUEST_METHOD'];

        if (isset(self::$routes[$method][$uri])) {
            $controllerName = self::$routes[$method][$uri][0];
            $action = self::$routes[$method][$uri][1];

            $controller = new $controllerName();
            if (method_exists($controller, 'checkAuthentication')) {
                error_log("Route.php - Vérification de l'authentification pour la route: " . $uri);
                $controller->checkAuthentication($uri);
            }

            $controller->$action();
            return;
        }

        foreach (self::$routes[$method] ?? [] as $route => $action) {
            $pattern = self::convertRouteToRegex($route);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $controllerName = $action[0];
                $methodName = $action[1];

                $controller = new $controllerName();

                if (method_exists($controller, 'checkAuthentication')) {
                    $controller->checkAuthentication($uri);
                }

                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        header("HTTP/1.0 404 Not Found");
        try {
            echo \App\Views\View::render('error', [
                'code' => 404,
                'message' => 'La page que vous recherchez n\'existe pas.'
            ]);
        } catch (\Exception $e) {
            echo "<h1>404 - Page non trouvée</h1>";
            echo "<p>La page que vous recherchez n'existe pas.</p>";
            echo "<p><a href='/'>Retourner à l'accueil</a></p>";
        }
    }

    private static function convertRouteToRegex(string $route): string {
        if (substr($route, 0, 1) !== '/') {
            $route = '/' . $route;
        }

        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);

        return "#^$pattern$#";
    }
}
