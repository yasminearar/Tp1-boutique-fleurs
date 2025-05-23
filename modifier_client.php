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
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    $ok = $clientManager->modifier($id, $nom, $prenom, $email, $adresse, $telephone);
    
    if ($ok) {
        header("Location: client_index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un client</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier le client</h1>

        <form method="post" class="form-plante">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" 
                       value="<?= htmlspecialchars($client['nom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" 
                       value="<?= htmlspecialchars($client['prenom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" 
                       value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse :</label>
                <textarea name="adresse" id="adresse"><?= htmlspecialchars($client['adresse']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="text" name="telephone" id="telephone" 
                       value="<?= htmlspecialchars($client['telephone']) ?>">
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="client_index.php" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </form>
    </div>
</body>
</html>
