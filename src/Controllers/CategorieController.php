<?php
namespace App\Controllers;

use App\Models\Categorie;
use App\Models\Plante;

class CategorieController extends Controller {    /**
     * Affiche la liste des catégories
     */
    public function index() {
        // Récupération des catégories avec le nombre de plantes par catégorie
        $categorie = new Categorie();
        $categories = $categorie->withPlantCount();
        
        $this->display('categorie/index', [
            'pageTitle' => 'Catégories de plantes',
            'categories' => $categories
        ]);
    }
      /**
     * Affiche les détails d'une catégorie
     * 
     * @param int $id ID de la catégorie
     */
    public function show($id) {
        $categorie = new Categorie();
        $categorieData = $categorie->selectId($id);
        
        if (!$categorieData) {
            $this->error(404, 'Catégorie non trouvée');
            return;
        }
        
        // Récupérer les plantes de cette catégorie
        $plante = new Plante();
        $plantes = $plante->getByCategory($id);
        
        $this->display('categorie/show', [
            'pageTitle' => 'Catégorie: ' . $categorieData['nom'],
            'categorie' => $categorieData,
            'plantes' => $plantes
        ]);
    }
    
    /**
     * Affiche le formulaire de création de catégorie
     */
    public function create() {
        $this->requireAdmin();
        $this->display('categorie/create', [
            'pageTitle' => 'Créer une catégorie'
        ]);
    }
      /**
     * Traite la soumission du formulaire de création
     */
    public function store() {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('/categories');
            return;
        }
        
        $data = [
            'nom' => $this->postParam('nom'),
            'description' => $this->postParam('description')
        ];

        $categorie = new Categorie();
        $errors = $categorie->validate($data);
        
        if (!empty($errors)) {
            $this->display('categorie/create', [
                'pageTitle' => 'Créer une catégorie',
                'categorie' => $data,
                'errors' => $errors
            ]);
            return;
        }

        $id = $categorie->insert($data);
        
        if ($id) {
            $this->addFlashMessage('Catégorie créée avec succès', 'success');
            $this->redirect('/categorie/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de la création de la catégorie', 'error');
            $this->display('categorie/create', [
                'pageTitle' => 'Créer une catégorie',
                'categorie' => $data
            ]);
        }
    }

    public function edit($id) {
        $this->requireAdmin();
        
        $categorie = new Categorie();
        $categorieData = $categorie->selectId($id);
        
        if (!$categorieData) {
            $this->error(404, 'Catégorie non trouvée');
            return;
        }
        
        $this->display('categorie/edit', [
            'pageTitle' => 'Modifier la catégorie: ' . $categorieData['nom'],
            'categorie' => $categorieData
        ]);
    }    /**
     * Traite la soumission du formulaire d'édition
     * 
     * @param int $id ID de la catégorie
     */
    public function update($id) {
        $this->requireAdmin();
        if (!$this->isPost()) {
            $this->redirect('/categories');
            return;
        }
        
        $data = [
            'nom' => $this->postParam('nom'),
            'description' => $this->postParam('description')
        ];

        $categorie = new Categorie();
        $errors = $categorie->validate($data);
        
        if (!empty($errors)) {
            $this->display('categorie/edit', [
                'pageTitle' => 'Modifier la catégorie',
                'categorie' => array_merge(['id' => $id], $data),
                'errors' => $errors
            ]);
            return;
        }

        $result = $categorie->update($data, $id);
        
        if ($result) {
            $this->addFlashMessage('Catégorie modifiée avec succès', 'success');
            $this->redirect('/categorie/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de la modification de la catégorie', 'error');
            $this->display('categorie/edit', [
                'pageTitle' => 'Modifier la catégorie',
                'categorie' => array_merge(['id' => $id], $data)
            ]);
        }
    }

    public function delete($id) {
        $this->requireAdmin();
        $categorie = new Categorie();
        $categorieData = $categorie->selectId($id);
        
        if (!$categorieData) {
            $this->error(404, 'Catégorie non trouvée');
            return;
        }

        $plante = new Plante();
        $plantes = $plante->getByCategory($id);
        
        if (count($plantes) > 0) {
            $this->addFlashMessage('Impossible de supprimer cette catégorie car elle contient des plantes', 'error');
            $this->redirect('/categorie/' . $id);
            return;
        }
        
        $result = $categorie->delete($id);
        
        if ($result) {
            $this->addFlashMessage('Catégorie supprimée avec succès', 'success');
        } else {
            $this->addFlashMessage('Erreur lors de la suppression de la catégorie', 'error');
        }
        
        $this->redirect('/categories');
    }
}
