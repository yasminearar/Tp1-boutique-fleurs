<?php
require_once 'classes/Connexion.php';
require_once 'classes/Categorie.php';

$categorieManager = new Categorie();
$categories = $categorieManager->lister();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des catégories</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Liste des catégories</h1>

    <a href="ajout_categorie.php"><button>Ajouter une nouvelle catégorie</button></a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['nom']) ?></td>
                    <td>
                        <a href="modifier_categorie.php?id=<?= $cat['id'] ?>"><button>Modifier</button></a>
                        <a href="supprimer_categorie.php?id=<?= $cat['id'] ?>" onclick="return confirm('Supprimer cette catégorie ?');">
                            <button>Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php"><button>Retour à l'accueil</button></a>
</div>
</body>
</html>
