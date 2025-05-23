<?php
require_once 'classes/Connexion.php';
require_once 'classes/Plante.php';

$pdo = Connexion::getPDO();
$planteManager = new Plante();
$totalPlantes = $pdo->query("SELECT COUNT(*) FROM plantes")->fetchColumn();
$totalClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$totalCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique de Plantes - Administration</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="welcome-header">
            <h1>Administration de la Boutique de Plantes</h1>
            <p>Gérez votre inventaire, vos clients et vos commandes</p>
        </div>

        <div class="welcome-text">
            <p>Bienvenue dans l'interface d'administration de votre boutique de plantes. 
            Utilisez les différentes sections ci-dessous pour gérer votre boutique.</p>
        </div>

        <div class="nav-grid">
            <!-- Section Plantes -->
            <div class="nav-card">
                <i class="fas fa-leaf fa-3x" style="color: #2c7a4b;"></i>
                <h2>Plantes</h2>
                <p>Gérez votre catalogue de plantes</p>
                <p><strong><?= $totalPlantes ?></strong> plantes en stock</p>
                <a href="plante_index.php" class="btn btn-primary">Gérer les plantes</a>
            </div>

            <!-- Section Catégories -->
            <div class="nav-card">
                <i class="fas fa-tags fa-3x" style="color: #2c7a4b;"></i>
                <h2>Catégories</h2>
                <p>Organisez vos plantes par catégories</p>
                <p><strong><?= $totalCategories ?></strong> catégories</p>
                <a href="categories_index.php" class="btn btn-primary">Gérer les catégories</a>
            </div>

            <!-- Section Clients -->
            <div class="nav-card">
                <i class="fas fa-users fa-3x" style="color: #2c7a4b;"></i>
                <h2>Clients</h2>
                <p>Gérez votre base clients</p>
                <p><strong><?= $totalClients ?></strong> clients enregistrés</p>
                <a href="client_index.php" class="btn btn-primary">Gérer les clients</a>
            </div>

            <!-- Section Commandes -->
            <div class="nav-card">
                <i class="fas fa-shopping-cart fa-3x" style="color: #2c7a4b;"></i>
                <h2>Commandes</h2>
                <p>Suivez les commandes de vos clients</p>
                <p><strong><?= $totalCommandes ?></strong> commandes</p>
                <a href="commande_index.php" class="btn btn-primary">Gérer les commandes</a>
            </div>
        </div>    </div>

    <footer class="footer">
        <div class="footer__links">
            <a href="#"><i class="fas fa-home"></i> Accueil</a>
            <a href="#"><i class="fas fa-info-circle"></i> À propos</a>
            <a href="#"><i class="fas fa-envelope"></i> Contact</a>
        </div>
        <p>© 2024 Boutique de Plantes - Tous droits réservés</p>
        <p>Interface d'administration</p>
    </footer>
</body>
</html>
