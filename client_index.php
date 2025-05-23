<?php
require_once 'classes/Connexion.php';
require_once 'classes/Client.php';

$clientManager = new Client();
$clients = $clientManager->lister();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des clients</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Liste des clients</h1>

    <a href="ajout_client.php"><button>Ajouter un client</button></a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client['id'] ?></td>
                    <td><?= htmlspecialchars($client['nom']) ?></td>
                    <td><?= htmlspecialchars($client['prenom']) ?></td>
                    <td><?= htmlspecialchars($client['email']) ?></td>
                    <td><?= htmlspecialchars($client['adresse']) ?></td>
                    <td><?= htmlspecialchars($client['telephone']) ?></td>
                    <td>
                        <a href="modifier_client.php?id=<?= $client['id'] ?>"><button>Modifier</button></a>
                        <a href="supprimer_client.php?id=<?= $client['id'] ?>" onclick="return confirm('Supprimer ce client ?');">
                            <button>Supprimer</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php"><button>Retour à l’accueil</button></a>
</div>
</body>
</html>
