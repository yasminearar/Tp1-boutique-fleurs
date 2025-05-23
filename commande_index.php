<?php
require_once 'classes/Connexion.php';
require_once 'classes/Commande.php';

$commandeManager = new Commande();
$commandes = $commandeManager->lister();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des commandes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Liste des commandes</h1>
    <a href="ajout_commande.php"><button>Nouvelle commande</button></a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><?= $cmd['id'] ?></td>
                    <td><?= htmlspecialchars($cmd['nom'] . ' ' . $cmd['prenom']) ?></td>
                    <td><?= $cmd['date_commande'] ?></td>
                    <td><?= $cmd['statut'] ?></td>
                    <td>
                        <a href="modifier_commande.php?id=<?= $cmd['id'] ?>"><button>Modifier</button></a>
                        <a href="supprimer_commande.php?id=<?= $cmd['id'] ?>" onclick="return confirm('Supprimer cette commande ?');">
                            <button>Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="index.php"><button>Accueil</button></a>
</div>
</body>
</html>
