<?php
// Point d'entrée principal de l'application

// Démarrer la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger l'autoloader de Composer
require_once 'vendor/autoload.php';

// Charger le fichier de configuration
require_once 'config.php';

use App\Routes\Route;

// Définir des alias pour compatibilité avec le code existant
define('BASE_URL', BASE);
define('ASSETS_URL', ASSET);

// Charger les routes définies
require_once ROOT_DIR . '/src/Routes/web.php';

// Dispatcher la requête vers le contrôleur approprié
Route::dispatch();
?>
