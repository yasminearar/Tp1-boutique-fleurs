<?php
require_once 'classes/Connexion.php';
require_once 'classes/Categorie.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID non fourni.");
}

$id = $_GET['id'];
$categorieManager = new Categorie();
$ok = $categorieManager->supprimer($id);

if ($ok) {
    header("Location: liste_categories.php");
    exit;
} else {
    echo "Erreur lors de la suppression de la catégorie.";
}
