<?php
require_once 'classes/Connexion.php';

$pdo = Connexion::getPDO();
$sql = "SELECT * FROM plantes ORDER BY nom ASC";
$plantes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique de Plantes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<aside class="aside-details">
    <div class="detail">
        <img class="detail__img" src="images/monstera.jpg" alt="Monstera Deliciosa">
        <h2 class="detail__nom">Monstera Deliciosa</h2>
        <h3 class="detail__description">Plante tropicale élégante, idéale pour la décoration intérieure.</h3>
        <button class="detail__btn">Découvrir</button>
    </div>
</aside>

<section class="produits-section">
    <div class="section-header">
        <h1>Boutique de Plantes d'Intérieur</h1>
        <div class="filtres">
            <div class="element tri" id="tri-alpha-croissant">Par ordre alphabétique (A-Z)</div>
            <div class="element tri" id="tri-alpha-decroissant">Par ordre alphabétique (Z-A)</div>
        </div>
    </div>

    <div class="grille-produits">
        <?php foreach ($plantes as $plante): ?>
            <div class="produit">
                <?php if (!empty($plante['image_url']) && file_exists('images/' . $plante['image_url'])): ?>
                    <img src="images/<?= htmlspecialchars($plante['image_url']) ?>" alt="<?= htmlspecialchars($plante['nom']) ?>" class="produit__img">
                <?php else: ?>
                    <img src="images/default.jpg" alt="image indisponible" class="produit__img">
                <?php endif; ?>

                <h3 class="produit__nom"><?= htmlspecialchars($plante['nom']) ?></h3>
                <p class="produit__prix">Prix : <?= number_format($plante['prix'], 2) ?> €</p>
                <button class="produit__btn">Détails</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>
</body>
</html>
