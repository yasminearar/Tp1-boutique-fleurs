<?php
require_once 'classes/Connexion.php';
require_once 'classes/Plante.php';

session_start();
$message = '';
$planteManager = new Plante();

// Vérification de l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID de plante non fourni.";
    header("Location: plante_index.php");
    exit;
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($id === false) {
    $_SESSION['message'] = "ID de plante invalide.";
    header("Location: plante_index.php");
    exit;
}

// Récupération de la plante
$plante = $planteManager->trouverParId($id);
if (!$plante) {
    $_SESSION['message'] = "Plante introuvable.";
    header("Location: plante_index.php");
    exit;
}

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmation']) && $_POST['confirmation'] === 'oui') {
        try {
            $ok = $planteManager->supprimer($id);
            if ($ok) {
                $_SESSION['message'] = "La plante a été supprimée avec succès.";
                $_SESSION['message_type'] = 'success';
                header("Location: plante_index.php");
                exit;
            } else {
                $message = "Une erreur est survenue lors de la suppression de la plante.";
            }
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        header("Location: plante_index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer une plante - <?= htmlspecialchars($plante['nom']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Supprimer la plante</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="confirmation-box">
            <p class="warning-text">Attention ! Cette action est irréversible.</p>
            <p>Êtes-vous sûr de vouloir supprimer la plante suivante ?</p>
            
            <div class="plante-info">
                <h2><?= htmlspecialchars($plante['nom']) ?></h2>
                <?php if (!empty($plante['image_url'])): ?>
                    <img src="<?= htmlspecialchars($plante['image_url']) ?>" 
                         alt="<?= htmlspecialchars($plante['nom']) ?>" 
                         class="preview-image">
                <?php endif; ?>
                <p><strong>Prix :</strong> <?= number_format($plante['prix'], 2) ?> $</p>
                <p><strong>Stock :</strong> <?= $plante['stock'] ?></p>
                <?php if (!empty($plante['description'])): ?>
                    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($plante['description'])) ?></p>
                <?php endif; ?>
            </div>

            <form method="post" class="delete-form">
                <input type="hidden" name="confirmation" value="oui">
                <div class="form-buttons">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous vraiment sûr de vouloir supprimer cette plante ?')">
                        Confirmer la suppression
                    </button>
                    <a href="plante_index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
