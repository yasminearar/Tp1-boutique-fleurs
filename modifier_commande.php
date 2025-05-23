<?php
require_once 'classes/Connexion.php';
require_once 'classes/Commande.php';

$pdo = Connexion::getPDO();
$commandeManager = new Commande();
$message = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID manquant.");
}

$id = $_GET['id'];
$commande = $commandeManager->trouverParId($id);
$clients = $pdo->query("SELECT id, nom, prenom FROM clients")->fetchAll(PDO::FETCH_ASSOC);

if (!$commande) {
    die("Commande introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_client = $_POST['id_client'] ?? '';
    $statut = $_POST['statut'] ?? 'en cours';

    $ok = $commandeManager->modifier($id, $id_client, $statut);
    if ($ok) {
        header("Location: commande_index.php");
        exit;
    } else {
        $message = "Échec de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une commande</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Modifier la commande</h1>
    <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <form method="post">
        <label for="id_client">Client :</label>
        <select name="id_client" id="id_client" required>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['id'] ?>" <?= $client['id'] == $commande['id_client'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="statut">Statut :</label>
        <select name="statut" id="statut">
            <option value="en cours" <?= $commande['statut'] == 'en cours' ? 'selected' : '' ?>>en cours</option>
            <option value="expédiée" <?= $commande['statut'] == 'expédiée' ? 'selected' : '' ?>>expédiée</option>
            <option value="livrée" <?= $commande['statut'] == 'livrée' ? 'selected' : '' ?>>livrée</option>
        </select>

        <button type="submit">Enregistrer</button>
    </form>

    <br>
    <a href="commande_index.php"><button>Retour</button></a>
</div>
</body>
</html>
