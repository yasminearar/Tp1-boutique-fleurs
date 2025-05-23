<?php
require_once 'classes/Connexion.php';
require_once 'classes/Categorie.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de la catégorie non fourni.");
}

$categorieManager = new Categorie();
$id = $_GET['id'];
$categorie = $categorieManager->trouverParId($id);

if (!$categorie) {
    die("Catégorie introuvable.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    if (!empty($nom)) {
        $ok = $categorieManager->modifier($id, $nom);
        if ($ok) {
            header("Location: liste_categories.php");
            exit;
        } else {
            $message = "Erreur lors de la modification.";
        }
    } else {
        $message = "Le champ nom est requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une catégorie</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h1>Modifier la catégorie</h1>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($categorie['nom']) ?>" required>

        <button type="submit">Enregistrer</button>
    </form>

    <br>
    <a href="liste_categories.php"><button>Retour</button></a>
</div>
</body>
</html>
