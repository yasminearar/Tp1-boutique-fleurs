<?php
require_once 'classes/Connexion.php';
require_once 'classes/Categorie.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';

    if (!empty($nom)) {
        $categorieManager = new Categorie();
        $ok = $categorieManager->ajouter($nom);
        $message = $ok ? "Catégorie ajoutée avec succès !" : "Erreur lors de l'ajout.";
    } else {
        $message = "Le champ nom est requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une catégorie</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Ajouter une catégorie</h1>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nom">Nom de la catégorie :</label>
        <input type="text" name="nom" id="nom" required>

        <button type="submit">Ajouter</button>
    </form>

    <br>
    <a href="categories_index.php"><button>Voir les catégories</button></a>
</div>
</body>
</html>
