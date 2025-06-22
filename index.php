<?php
// Démarrer la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'vendor/autoload.php';
require_once 'config.php';

// Redirection vers le contrôleur frontal dans /public
include_once 'public/index.php';
?>
