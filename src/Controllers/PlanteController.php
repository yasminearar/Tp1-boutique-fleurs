<?php
namespace App\Controllers;

use App\Models\Plante;
use App\Models\Categorie;

class PlanteController extends Controller {
    /**
     * Affiche la liste des plantes
     */    public function index() {        // Récupérer le paramètre categorie de l'URL (si présent)
        // Si 'categorie' est présent mais vide, c'est l'option "Sélectionner une catégorie"
        $categorieId = isset($_GET['categorie']) ? (($_GET['categorie'] === '') ? null : (int)$_GET['categorie']) : 0;
        
        // Récupérer toutes les catégories pour le filtre
        $categorie = new Categorie();
        $categories = $categorie->select();
        
        // Récupérer les plantes (avec filtre par catégorie si applicable)
        $plante = new Plante();
        if ($categorieId && $categorieId > 0) {
            // Si une catégorie est spécifiée, filtrer les plantes par cette catégorie
            $plantes = $plante->getByCategory($categorieId);
        } else {
            // Sinon, récupérer toutes les plantes avec leur catégorie
            $plantes = $plante->withCategories();
        }
        
        $this->display('plante/index', [
            'pageTitle' => 'Nos plantes',
            'plantes' => $plantes,
            'categories' => $categories,
            'current_categorie' => $categorieId
        ]);
    }
    
    /**
     * Affiche les détails d'une plante
     * 
     * @param int $id ID de la plante
     */    public function show($id) {        
        $plante = new Plante();
        $planteData = $plante->selectId($id);
        
        if (!$planteData) {
            $this->error(404, 'Plante non trouvée');
            return;
        }
        
        // Récupérer la catégorie de la plante
        $categorieModel = new Categorie();
        $categorie = $categorieModel->selectId($planteData['id_categorie']);
        
        // Rendre compatible le nom du champ image avec les templates
        if (isset($planteData['image_url'])) {
            $planteData['image'] = $planteData['image_url'];
        }
        
        $this->display('plante/show', [
            'pageTitle' => $planteData['nom'],
            'plante' => $planteData,
            'categorie' => $categorie
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'une plante
     */    public function create() {
        // Récupérer toutes les catégories pour le menu déroulant
        $categorieModel = new Categorie();
        $categories = $categorieModel->select();
        
        $this->display('plante/create', [
            'pageTitle' => 'Ajouter une plante',
            'categories' => $categories
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de création
     */    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/plantes');
            return;
        }
        
        $data = [
            'nom' => $this->postParam('nom'),
            'description' => $this->postParam('description'),
            'prix' => (float) $this->postParam('prix'),
            'id_categorie' => (int) $this->postParam('id_categorie'),
            'image_url' => $this->postParam('image'),
            'stock' => (int) $this->postParam('stock')
        ];
        
        // Validation des données
        $plante = new Plante();
        $errors = $plante->validate($data);
        
        if (!empty($errors)) {
            $categorieModel = new Categorie();
            $categories = $categorieModel->select();
            $this->display('plante/create', [
                'pageTitle' => 'Ajouter une plante',
                'plante' => $data,
                'categories' => $categories,
                'errors' => $errors
            ]);
            return;
        }
        
        $id = $plante->insert($data);
        
        if ($id) {
            $this->addFlashMessage('Plante ajoutée avec succès', 'success');
            $this->redirect('/plante/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de l\'ajout de la plante', 'error');
            $categorieModel = new Categorie();
            $categories = $categorieModel->select();
            $this->display('plante/create', [
                'pageTitle' => 'Ajouter une plante',
                'plante' => $data,
                'categories' => $categories
            ]);
        }
    }
    
    /**
     * Affiche le formulaire d'édition
     * 
     * @param int $id ID de la plante
     */    public function edit($id) {        
        $plante = new Plante();
        $planteData = $plante->selectId($id);
        
        if (!$planteData) {
            $this->error(404, 'Plante non trouvée');
            return;
        }
        
        // Récupérer toutes les catégories pour le menu déroulant
        $categorieModel = new Categorie();
        $categories = $categorieModel->select();
        
        // Rendre compatible le nom du champ image avec les templates
        if (isset($planteData['image_url'])) {
            $planteData['image'] = $planteData['image_url'];
        }
        
        $this->display('plante/edit', [
            'pageTitle' => 'Modifier la plante: ' . $planteData['nom'],
            'plante' => $planteData,
            'categories' => $categories
        ]);
    }
    
    /**
     * Traite la soumission du formulaire d'édition
     * 
     * @param int $id ID de la plante
     */    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('/plantes');
            return;
        }
        
        $data = [
            'nom' => $this->postParam('nom'),            
            'description' => $this->postParam('description'),
            'prix' => (float) $this->postParam('prix'),
            'id_categorie' => (int) $this->postParam('id_categorie'),
            'image_url' => $this->postParam('image'),
            'stock' => (int) $this->postParam('stock')
        ];
        
        // Validation des données
        $plante = new Plante();
        $errors = $plante->validate($data);
        
        if (!empty($errors)) {
            $categorieModel = new Categorie();
            $categories = $categorieModel->select();
            $this->display('plante/edit', [
                'pageTitle' => 'Modifier la plante',
                'plante' => array_merge(['id' => $id], $data),
                'categories' => $categories,
                'errors' => $errors
            ]);
            return;
        }
        
        $result = $plante->update($data, $id);
        
        if ($result) {
            $this->addFlashMessage('Plante modifiée avec succès', 'success');
            $this->redirect('/plante/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de la modification de la plante', 'error');
            $categorieModel = new Categorie();
            $categories = $categorieModel->select();
            $this->display('plante/edit', [
                'pageTitle' => 'Modifier la plante',
                'plante' => array_merge(['id' => $id], $data),
                'categories' => $categories
            ]);
        }
    }
    
    /**
     * Supprime une plante
     * 
     * @param int $id ID de la plante
     */    public function delete($id) {
        $plante = new Plante();
        $planteData = $plante->selectId($id);
        
        if (!$planteData) {
            $this->error(404, 'Plante non trouvée');
            return;
        }
        
        $result = $plante->delete($id);
        
        if ($result) {
            $this->addFlashMessage('Plante supprimée avec succès', 'success');
        } else {
            $this->addFlashMessage('Erreur lors de la suppression de la plante', 'error');
        }
        
        $this->redirect('/plantes');
    }
    
    /**
     * Recherche de plantes
     */    public function search() {
        $keyword = $this->getParam('q');
        // Si 'categorie' est présent mais vide, c'est l'option "Sélectionner une catégorie"
        $categorieId = isset($_GET['categorie']) ? (($_GET['categorie'] === '') ? null : (int)$_GET['categorie']) : 0;
        
        if (empty($keyword)) {
            // Si pas de mot-clé mais une catégorie, rediriger vers la liste filtrée par catégorie
            if ($categorieId && $categorieId > 0) {
                $this->redirect('/plantes?categorie=' . $categorieId);
            } else {
                $this->redirect('/plantes');
            }
            return;
        }
        
        // Récupérer toutes les catégories pour le filtre
        $categorieModel = new Categorie();
        $categories = $categorieModel->select();
        
        // Rechercher les plantes avec le filtre de catégorie optionnel
        $planteModel = new Plante();
        $plantes = $planteModel->search($keyword, $categorieId > 0 ? $categorieId : null);
        
        // Construire le titre avec les informations de filtrage
        $pageTitle = 'Résultats de recherche pour "' . htmlspecialchars($keyword) . '"';
        if ($categorieId) {
            foreach ($categories as $cat) {
                if ($cat['id'] == $categorieId) {
                    $pageTitle .= ' dans la catégorie "' . htmlspecialchars($cat['nom']) . '"';
                    break;
                }
            }
        }
        
        $this->display('plante/search', [
            'pageTitle' => $pageTitle,
            'keyword' => $keyword,
            'plantes' => $plantes,
            'categories' => $categories,
            'current_categorie' => $categorieId,
            'search_query' => $keyword
        ]);
    }
}
