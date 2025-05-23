<?php
require_once 'classes/Connexion.php';
require_once 'classes/Plante.php';

session_start();
$message = '';
$messageType = '';
$errors = [];

// Connexion PDO
$pdo = Connexion::getPDO();

// Récupération des catégories
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

    // Validation
    if (empty($nom)) {
        $errors[] = "Le nom de la plante est requis.";
    }
    if ($prix === false || $prix <= 0) {
        $errors[] = "Le prix doit être un nombre positif.";
    }    if ($stock === false || $stock < 0) {
        $errors[] = "Le stock doit être un nombre positif ou zéro.";
    }
    if (empty($id_categorie)) {
        $errors[] = "Veuillez sélectionner une catégorie.";
    }

    if (empty($errors)) {
        $plante = new Plante();
        try {
            $ok = $plante->ajouter($nom, $description, $prix, $taille, $exposition, $stock, $image_url, $id_categorie);
            if ($ok) {
                $_SESSION['message'] = "La plante a été ajoutée avec succès !";
                $_SESSION['message_type'] = 'success';
                header("Location: plante_index.php");
                exit;
            } else {
                $message = "Une erreur est survenue lors de l'ajout de la plante.";
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une plante</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter une plante</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="form-plante">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" 
                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                       required minlength="2" maxlength="100">
            </div>

            <div class="form-group">
                <label for="description">Description :</label>
                <textarea name="description" id="description" 
                          rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix ($) :</label>
                <input type="number" name="prix" id="prix" 
                       value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>"
                       step="0.01" min="0.01" required>
            </div>

            <div class="form-group">
                <label for="taille">Taille :</label>
                <select name="taille" id="taille">
                    <option value="">Sélectionnez une taille</option>
                    <option value="Petite">Petite</option>
                    <option value="Moyenne">Moyenne</option>
                    <option value="Grande">Grande</option>
                </select>
            </div>

            <div class="form-group">
                <label for="exposition">Exposition :</label>
                <select name="exposition" id="exposition">
                    <option value="">Sélectionnez une exposition</option>
                    <option value="Soleil">Soleil</option>
                    <option value="Mi-ombre">Mi-ombre</option>
                    <option value="Ombre">Ombre</option>
                </select>
            </div>

            <div class="form-group">
                <label for="stock">Stock :</label>
                <input type="number" name="stock" id="stock" 
                       value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>"
                       min="0" required>
            </div>

            <div class="form-group">
                <label for="image_url">Image :</label>
                <input type="text" name="image_url" id="image_url" 
                       value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>"
                       placeholder="images/nom-image.jpg">
                <small>Exemple : images/calathea.jpg, images/monstera.jpg</small>
                <?php if (!empty($_POST['image_url'])): ?>
                    <img src="<?= htmlspecialchars($_POST['image_url']) ?>" 
                         alt="Aperçu" class="preview-image">
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="id_categorie">Catégorie :</label>
                <select name="id_categorie" id="id_categorie" required>
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" 
                                <?= (isset($_POST['id_categorie']) && $_POST['id_categorie'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Ajouter la plante</button>
                <a href="plante_index.php" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </form>
    </div>
</body>
</html>

