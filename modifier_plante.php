<?php
require_once 'classes/Connexion.php';
require_once 'classes/Plante.php';

$pdo = Connexion::getPDO();
$planteManager = new Plante();
$message = "";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID manquant.");
}

$id = $_GET['id'];
$plante = $planteManager->trouverParId($id);

if (!$plante) {
    die("Plante introuvable.");
}

// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT);
    $taille = trim($_POST['taille'] ?? '');
    $exposition = trim($_POST['exposition'] ?? '');
    $stock = filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT);
    $image_url = trim($_POST['image_url'] ?? '');
    $id_categorie = filter_var($_POST['id_categorie'] ?? null, FILTER_VALIDATE_INT);

    // Validation basique
    if (empty($nom) || $prix <= 0) {
        $message = "Le nom et le prix sont requis. Le prix doit être supérieur à 0.";
    } else {
        $ok = $planteManager->modifier($id, $nom, $description, $prix, $taille, $exposition, $stock, $image_url, $id_categorie);
        if ($ok) {
            header("Location: plante_index.php");
            exit;
        } else {
            $message = "Échec de la mise à jour.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une plante</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
        <h1>Modifier la plante</h1>

        <?php if ($message): ?>
            <div class="alert <?= strpos($message, 'succès') !== false ? 'alert-success' : 'alert-error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-plante">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" 
                       value="<?= htmlspecialchars($plante['nom']) ?>" 
                       required minlength="2" maxlength="100">
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description" 
                          rows="4"><?= htmlspecialchars($plante['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix ($) :</label>
                <input type="number" name="prix" id="prix" 
                       step="0.01" min="0.01"
                       value="<?= $plante['prix'] ?>" required>
            </div>

            <div class="form-group">
                <label for="taille">Taille :</label>
                <input type="text" name="taille" id="taille" 
                       value="<?= htmlspecialchars($plante['taille']) ?>" 
                       maxlength="50">
            </div>

            <div class="form-group">
                <label for="exposition">Exposition :</label>
                <select name="exposition" id="exposition">
                    <option value="Soleil" <?= $plante['exposition'] == 'Soleil' ? 'selected' : '' ?>>Soleil</option>
                    <option value="Mi-ombre" <?= $plante['exposition'] == 'Mi-ombre' ? 'selected' : '' ?>>Mi-ombre</option>
                    <option value="Ombre" <?= $plante['exposition'] == 'Ombre' ? 'selected' : '' ?>>Ombre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="stock">Stock :</label>
                <input type="number" name="stock" id="stock" 
                       value="<?= $plante['stock'] ?>" 
                       min="0" required>
            </div>

            <div class="form-group">
                <label for="image_url">Image :</label>
                <input type="text" name="image_url" id="image_url" 
                       value="<?= htmlspecialchars($plante['image_url']) ?>"
                       pattern=".*\.(jpg|jpeg|png|gif)$"
                       title="L'URL doit pointer vers une image (jpg, jpeg, png ou gif)">
                <?php if (!empty($plante['image_url'])): ?>
                    <img src="<?= htmlspecialchars($plante['image_url']) ?>" 
                         alt="Aperçu" class="preview-image">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="id_categorie">Catégorie :</label>
                <select name="id_categorie" id="id_categorie" required>
                    <option value="">Sélectionnez une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" 
                                <?= ($cat['id'] == $plante['id_categorie']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="plante_index.php" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </form>
    </div>
</body>
</html>
