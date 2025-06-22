<?php
// Script de vérification de redirection à inclure dans l'en-tête

// Si une commande a été créée avec succès mais que la redirection a échoué
if (isset($_SESSION['commande_success']) && $_SESSION['commande_success']) {
    // Effacer le flag
    $_SESSION['commande_success'] = false;
    
    // URL de redirection
    $redirectUrl = '/Tp1-boutique-fleurs/commandes';
    
    // Log
    error_log("Redirection via check_redirect.php vers: " . $redirectUrl);
    
    // Rediriger
    header("Location: $redirectUrl", true, 302);
    echo '<script>window.location.href="' . $redirectUrl . '";</script>';
    exit;
}
