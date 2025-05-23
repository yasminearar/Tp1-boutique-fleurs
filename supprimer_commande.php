<?php
require_once 'classes/Connexion.php';
require_once 'classes/Commande.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID non fourni.");
}

$commandeManager = new Commande();
$ok = $commandeManager->supprimer($_GET['id']);

if ($ok) {
    header("Location: commande_index.php");
    exit;
} else {
    echo "Erreur lors de la suppression.";
}
