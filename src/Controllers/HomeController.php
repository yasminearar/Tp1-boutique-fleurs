<?php
namespace App\Controllers;

class HomeController extends Controller {
    /**
     * Affiche la page d'accueil
     */    public function index() {
        // Récupérer les compteurs pour la page d'accueil
        require_once ROOT_DIR . '/classes/Connexion.php';
        $pdo = \Connexion::getPDO();
        $totalPlantes = $pdo->query("SELECT COUNT(*) FROM plantes")->fetchColumn();
        $totalClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
        $totalCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
        $totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        
        $this->display('home', [
            'pageTitle' => 'Boutique de Plantes - Administration',
            'total_plantes' => $totalPlantes,
            'total_clients' => $totalClients,
            'total_commandes' => $totalCommandes,
            'total_categories' => $totalCategories
        ]);
    }
}
