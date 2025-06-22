<?php
namespace App\Utils;

// Page d'accueil temporaire pour tester la configuration initiale
// Charger la configuration si elle n'est pas déjà chargée
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../../config.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= SITE_NAME ?> - Configuration</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f9f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #27ae60;
            text-align: center;
        }
        .check-item {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 5px solid #27ae60;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        .next-steps {
            margin-top: 30px;
            padding: 20px;
            background-color: #eafbea;
            border-radius: 5px;
        }
    </style>
</head>
<body>    <div class="container">
        <h1>Configuration de <?= SITE_NAME ?></h1>
          <div class="check-item">
            <h2>Configuration</h2>
            <?php 
            if (defined('BASE') && defined('SITE_NAME')) {
                echo '<p class="success">✅ La configuration est correctement chargée.</p>';
            } else {
                echo '<p class="error">❌ La configuration n\'est pas correctement chargée.</p>';
            }
            ?>
        </div>
        
        <div class="check-item">
            <h2>Structure des dossiers</h2>
            <?php
            $folders = [
                'src/Controllers',
                'src/Models',
                'src/Views',
                'src/Routes',
                'src/Utils',
                'templates',
                'public'
            ];
            
            $allExist = true;
            foreach ($folders as $folder) {
                if (!is_dir(ROOT_DIR . '/' . $folder)) {
                    echo '<p class="error">❌ Le dossier ' . $folder . ' n\'existe pas.</p>';
                    $allExist = false;
                }
            }
            
            if ($allExist) {
                echo '<p class="success">✅ Tous les dossiers nécessaires sont présents.</p>';
            }
            ?>
        </div>
          <div class="check-item">
            <h2>Configuration de la base de données</h2>
            <?php
            if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
                echo '<p class="success">✅ La configuration de la base de données est présente.</p>';
                
                // Test de la connexion à la base de données
                try {
                    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                    $pdo = new \PDO($dsn, DB_USER, DB_PASS);
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    echo '<p class="success">✅ La connexion à la base de données a réussi.</p>';
                } catch (\PDOException $e) {
                    echo '<p class="error">❌ La connexion à la base de données a échoué : ' . $e->getMessage() . '</p>';
                }
            } else {
                echo '<p class="error">❌ La configuration de la base de données est manquante.</p>';
            }
            ?>
        </div>        <div class="check-item">
            <h2>Système de routage</h2>
            <p>Le système de routage a été mis en place. Vous pouvez maintenant accéder à votre page d'accueil en utilisant le lien suivant :</p>
            <p><a href="<?= BASE_URL ?>/" style="color: #27ae60; font-weight: bold; text-decoration: none; border: 1px solid #27ae60; padding: 8px 15px; border-radius: 4px;">Accéder à la page d'accueil</a></p>
            <p>Si le lien fonctionne, cela signifie que votre système de routage est correctement configuré!</p>
        </div>
        
        <div class="check-item">
            <h2>Configuration Twig</h2>
            <p>Twig a été configuré pour le rendu des vues. Les fichiers suivants ont été créés :</p>
            <ul style="margin-left: 20px; list-style-type: disc;">
                <li><code>src/Views/View.php</code> - Classe pour gérer le rendu des templates</li>
                <li><code>templates/layouts/base.twig</code> - Template de base avec header et footer</li>
                <li><code>templates/home.twig</code> - Template pour la page d'accueil</li>
                <li><code>templates/error.twig</code> - Template pour les pages d'erreur</li>
                <li><code>public/css/style.css</code> - Feuille de style CSS de base</li>
            </ul>
            <p style="margin-top: 10px;">Avant d'accéder à la page d'accueil, assurez-vous d'exécuter la commande suivante dans votre terminal :</p>
            <div style="background-color: #f1f1f1; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <code>composer install</code>
            </div>
            <p>Cette commande installera Twig et ses dépendances.</p>
        </div>        <div class="check-item">
            <h2>Modèles (Models)</h2>
            <p>Les modèles ont été créés pour interagir avec la base de données :</p>
            <ul style="margin-left: 20px; list-style-type: disc;">
                <li><code>src/Models/CRUD.php</code> - Classe abstraite de base avec les opérations CRUD</li>
                <li><code>src/Models/Categorie.php</code> - Modèle pour la table 'categories'</li>
                <li><code>src/Models/Client.php</code> - Modèle pour la table 'clients'</li>
                <li><code>src/Models/Plante.php</code> - Modèle pour la table 'plantes'</li>
                <li><code>src/Models/Commande.php</code> - Modèle pour la table 'commandes'</li>
            </ul>
            <p>La classe CRUD fournit les méthodes suivantes :</p>
            <ul style="margin-left: 20px; list-style-type: disc;">
                <li><code>all()</code> - Récupère tous les enregistrements</li>
                <li><code>find($id)</code> - Récupère un enregistrement par son ID</li>
                <li><code>where($criteria)</code> - Filtre les enregistrements selon des critères</li>
                <li><code>create($data)</code> - Crée un nouvel enregistrement</li>
                <li><code>update($id, $data)</code> - Met à jour un enregistrement existant</li>
                <li><code>delete($id)</code> - Supprime un enregistrement</li>
                <li><code>raw($sql, $params)</code> - Exécute une requête SQL personnalisée</li>
                <li><code>count($criteria)</code> - Compte le nombre d'enregistrements</li>
                <li><code>paginate($page, $perPage, $orderBy)</code> - Récupère des enregistrements avec pagination</li>
                <li><code>exists($criteria)</code> - Vérifie si un enregistrement existe</li>
            </ul>
            
            <div style="margin-top: 15px; padding: 10px; background-color: #f0f8ff; border-radius: 5px; border-left: 5px solid #1e88e5;">
                <h3 style="color: #1e88e5;">Méthodes spécifiques ajoutées à chaque modèle</h3>
                
                <h4>Modèle Plante</h4>
                <ul style="margin-left: 20px; list-style-type: disc;">
                    <li><code>getCategorie($planteId)</code> - Récupère la catégorie d'une plante</li>
                    <li><code>search($keyword)</code> - Recherche des plantes par nom ou description</li>
                    <li><code>withCategories()</code> - Récupère les plantes avec leur catégorie</li>
                    <li><code>inStock()</code> - Récupère les plantes en stock</li>
                    <li><code>withFormattedPrices()</code> - Récupère les plantes avec prix formaté</li>
                    <li><code>decreaseStock($planteId, $quantity)</code> - Diminue le stock d'une plante</li>
                </ul>
                
                <h4>Modèle Categorie</h4>
                <ul style="margin-left: 20px; list-style-type: disc;">
                    <li><code>getPlantesByCategorie($categorieId)</code> - Récupère les plantes d'une catégorie</li>
                    <li><code>withPlantCount()</code> - Récupère les catégories avec nombre de plantes</li>
                    <li><code>withPlantsInStock()</code> - Récupère les catégories avec plantes en stock</li>
                    <li><code>hasPlants($categorieId)</code> - Vérifie si une catégorie a des plantes</li>
                </ul>
                
                <h4>Modèle Client</h4>
                <ul style="margin-left: 20px; list-style-type: disc;">
                    <li><code>getCommandesByClient($clientId)</code> - Récupère les commandes d'un client</li>
                    <li><code>getNomComplet($clientId)</code> - Récupère le nom complet du client</li>
                    <li><code>search($keyword)</code> - Recherche des clients par nom/prénom/email</li>
                    <li><code>withCommandCount()</code> - Récupère les clients avec nombre de commandes</li>
                    <li><code>withOrders()</code> - Récupère les clients ayant passé des commandes</li>
                </ul>
                
                <h4>Modèle Commande</h4>
                <ul style="margin-left: 20px; list-style-type: disc;">
                    <li><code>getClient($commandeId)</code> - Récupère le client d'une commande</li>
                    <li><code>withClientDetails($statut)</code> - Récupère les commandes avec détails client</li>
                    <li><code>updateStatut($commandeId, $statut)</code> - Met à jour le statut d'une commande</li>
                    <li><code>getStatsByStatut()</code> - Récupère les statistiques par statut</li>
                    <li><code>getRecent($limit)</code> - Récupère les commandes récentes</li>
                </ul>
            </div>
            
            <p>Tous les modèles implémentent également une méthode <code>validate($data)</code> pour valider les données avant insertion ou mise à jour.</p>
        </div>        <div class="check-item">
            <h2>Contrôleurs (Controllers)</h2>
            <p>Les contrôleurs ont été créés pour gérer les actions de l'application :</p>
            <ul style="margin-left: 20px; list-style-type: disc;">
                <li><code>src/Controllers/Controller.php</code> - Classe abstraite de base pour tous les contrôleurs</li>
                <li><code>src/Controllers/HomeController.php</code> - Contrôleur pour la page d'accueil</li>
                <li><code>src/Controllers/PlanteController.php</code> - Contrôleur pour gérer les plantes</li>
                <li><code>src/Controllers/CategorieController.php</code> - Contrôleur pour gérer les catégories</li>
                <li><code>src/Controllers/ClientController.php</code> - Contrôleur pour gérer les clients</li>
                <li><code>src/Controllers/CommandeController.php</code> - Contrôleur pour gérer les commandes</li>
            </ul>
            <p>La classe Controller de base fournit les méthodes suivantes :</p>
            <ul style="margin-left: 20px; list-style-type: disc;">
                <li><code>render($template, $data)</code> - Génère le HTML d'un template Twig</li>
                <li><code>display($template, $data)</code> - Affiche un template Twig</li>
                <li><code>redirect($url)</code> - Redirige vers une URL</li>
                <li><code>isPost(), isGet()</code> - Vérifie le type de requête</li>
                <li><code>getParam(), postParam(), getAllPostParams()</code> - Récupère des paramètres GET/POST</li>
                <li><code>error($code, $message)</code> - Affiche une page d'erreur</li>
                <li><code>addFlashMessage($message, $type)</code> - Ajoute un message flash</li>
                <li><code>getFlashMessages()</code> - Récupère les messages flash</li>
            </ul>
            <p>Les routes ont également été mises à jour dans <code>src/Routes/web.php</code> pour les différentes actions CRUD.</p>
        </div>

        <div class="next-steps">
            <h2>Prochaines étapes</h2>
            <p>La structure MVC complète est maintenant en place! Voici les prochaines étapes :</p>
            <ol>
                <li><del>Mettre en place le système de routage</del> ✅</li>
                <li><del>Configurer Twig pour le rendu des vues</del> ✅</li>
                <li><del>Créer les modèles de base</del> ✅</li>
                <li><del>Refactorisation des modèles</del> ✅</li>
                <li><del>Développer les contrôleurs</del> ✅</li>
                <li>Créer les templates Twig pour chaque vue</li>
                <li>Améliorer l'interface utilisateur avec CSS et JavaScript</li>
                <li>Ajouter la validation côté client</li>
                <li>Implémenter les fonctionnalités de panier d'achat</li>
            </ol>
        </div>
    </div>
</body>
</html>
