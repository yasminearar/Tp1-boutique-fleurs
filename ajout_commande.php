<?php
require_once 'classes/Connexion.php';
require_once 'classes/Commande.php';

$pdo = Connexion::getPDO();
$clients = $pdo->query("SELECT id, nom, prenom FROM clients ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_client = $_POST['id_client'];
    $statut = $_POST['statut'] ?? 'en cours';

    $commandeManager = new Commande();
    $ok = $commandeManager->ajouter($id_client, $statut);
    
    if ($ok) {
        header("Location: commande_index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle commande</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Nouvelle commande</h1>

        <form method="post" class="form-plante">
            <div class="form-group">
                <label for="id_client">Client :</label>
                <select name="id_client" id="id_client" required>
                    <option value="">-- Sélectionner un client --</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>">
                            <?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="statut">Statut de la commande :</label>
                <select name="statut" id="statut">
                    <option value="en cours">En cours</option>
                    <option value="expédiée">Expédiée</option>
                    <option value="livrée">Livrée</option>
                </select>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Créer la commande</button>
                <a href="commande_index.php" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </form>
    </div>
</body>
</html>
