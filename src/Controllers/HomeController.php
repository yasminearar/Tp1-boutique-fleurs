<?php
namespace App\Controllers;

use App\Models\Plante;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Categorie;

class HomeController extends Controller {
    /**
     * Affiche la page d'accueil
     */    public function index() {
        // Récupérer les compteurs pour la page d'accueil en utilisant les modèles modernes
        $plante = new Plante();
        $client = new Client();
        $commande = new Commande();
        $categorie = new Categorie();
        
        $totalPlantes = count($plante->select());
        $totalClients = count($client->select());
        $totalCommandes = count($commande->select());
        $totalCategories = count($categorie->select());
        
        $this->display('home', [
            'pageTitle' => 'Boutique de Plantes - Administration',
            'total_plantes' => $totalPlantes,
            'total_clients' => $totalClients,
            'total_commandes' => $totalCommandes,
            'total_categories' => $totalCategories
        ]);
    }
}
