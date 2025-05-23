<?php
require_once 'classes/Connexion.php';
require_once 'classes/Client.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    $clientManager = new Client();
    $ok = $clientManager->ajouter($nom, $prenom, $email, $adresse, $telephone);
    
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
    <title>Ajouter un client</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un client</h1>

        <form method="post" class="form-plante">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="adresse">Adresse :</label>
                <textarea name="adresse" id="adresse"></textarea>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="text" name="telephone" id="telephone">
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Ajouter le client</button>
                <a href="client_index.php" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </form>
    </div>
</body>
</html>
