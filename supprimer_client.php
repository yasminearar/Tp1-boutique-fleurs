<?php
require_once 'classes/Connexion.php';
require_once 'classes/Client.php';

$clientManager = new Client();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: client_index.php");
    exit;
}

$id = $_GET['id'];
$client = $clientManager->trouverParId($id);

if (!$client) {
    header("Location: client_index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmation']) && $_POST['confirmation'] === 'oui') {
        $ok = $clientManager->supprimer($id);
        if ($ok) {
            header("Location: client_index.php");
            exit;
        }
    } else {
        header("Location: client_index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un client</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Supprimer le client</h1>

        <div class="confirmation-box">
            <p>Êtes-vous sûr de vouloir supprimer ce client ?</p>
            
            <div class="plante-info">
                <h2><?= htmlspecialchars($client['prenom']) ?> <?= htmlspecialchars($client['nom']) ?></h2>
                <p><strong>Email :</strong> <?= htmlspecialchars($client['email']) ?></p>
                <?php if (!empty($client['telephone'])): ?>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone']) ?></p>
                <?php endif; ?>
                <?php if (!empty($client['adresse'])): ?>
                    <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($client['adresse'])) ?></p>
                <?php endif; ?>
            </div>

            <form method="post" class="delete-form">
                <input type="hidden" name="confirmation" value="oui">
                <div class="form-buttons">
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                    <a href="client_index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
