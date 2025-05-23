<?php
require_once 'classes/Connexion.php';

$pdo = Connexion::getPDO();

$sql = "SELECT p.*, c.nom AS nom_categorie
        FROM plantes p
        LEFT JOIN categories c ON p.id_categorie = c.id
        ORDER BY p.id DESC";
$stmt = $pdo->query($sql);
$plantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des plantes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Liste des plantes</h1>

    <a href="ajout_plante.php"><button>Ajouter une nouvelle plante</button></a>

    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Catégorie</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plantes as $plante): ?>
                <tr>
                    <td>
                        <?php if ($plante['image_url']): ?>
                            <img class="plante-image" src="<?= htmlspecialchars($plante['image_url']) ?>" alt="plante">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($plante['nom']) ?></td>
                    <td><?= htmlspecialchars($plante['description']) ?></td>
                    <td><?= $plante['prix'] ?> $</td>
                    <td><?= $plante['stock'] ?></td>
                    <td><?= htmlspecialchars($plante['nom_categorie']) ?></td>
                    <td>
                        <a href="modifier_plante.php?id=<?= $plante['id'] ?>"><button>Modifier</button></a>
                        <a href="supprimer_plante.php?id=<?= $plante['id'] ?>" onclick="return confirm('Confirmer la suppression ?');">
                            <button>Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>

