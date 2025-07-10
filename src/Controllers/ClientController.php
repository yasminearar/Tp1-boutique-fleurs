<?php
namespace App\Controllers;

use App\Models\Client;

class ClientController extends Controller {
    /**
     * Affiche la liste des clients
     */
    public function index() {
        // Récupérer tous les clients avec le nombre de commandes
        $client = new Client();
        $clients = $client->withCommandCount();
        
        $this->display('client/index', [
            'pageTitle' => 'Liste des clients',
            'clients' => $clients
        ]);
    }
    
    /**
     * Affiche les détails d'un client
     * 
     * @param int $id ID du client
     */
    public function show($id) {
        $client = new Client();
        $clientData = $client->selectId($id);
        
        if (!$clientData) {
            $this->error(404, 'Client non trouvé');
            return;
        }
        
        // Récupérer les commandes de ce client
        $commandes = $client->getCommandesByClient($id);
        
        $this->display('client/show', [
            'pageTitle' => 'Client: ' . $client->getNomComplet($id),
            'client' => $clientData,
            'commandes' => $commandes
        ]);
    }

    public function create() {
        $this->requireAuth();
        $this->display('client/create', [
            'pageTitle' => 'Nouveau client'
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de création
     */
    public function store() {
        $this->requireAuth();
        if (!$this->isPost()) {
            $this->redirect('/clients');
            return;
        }
        
        $data = [
            'nom' => $this->postParam('nom'),
            'prenom' => $this->postParam('prenom'),
            'email' => $this->postParam('email'),
            'adresse' => $this->postParam('adresse'),
            'telephone' => $this->postParam('telephone')
        ];

        $client = new Client();
        $errors = $client->validate($data);
        
        if (!empty($errors)) {
            $this->display('client/create', [
                'pageTitle' => 'Nouveau client',
                'client' => $data,
                'errors' => $errors
            ]);
            return;
        }
        
        $id = $client->insert($data);
        
        if ($id) {
            $this->addFlashMessage('Client créé avec succès', 'success');
            $this->redirect('/client/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de la création du client', 'error');
            $this->display('client/create', [
                'pageTitle' => 'Nouveau client',
                'client' => $data
            ]);
        }
    }
    
    /**
     * Affiche le formulaire d'édition
     * 
     * @param int $id ID du client
     */
    public function edit($id) {
        $this->requireAdmin();
        $client = new Client();
        $clientData = $client->selectId($id);
        
        if (!$clientData) {
            $this->error(404, 'Client non trouvé');
            return;
        }
        
        $this->display('client/edit', [
            'pageTitle' => 'Modifier le client: ' . $client->getNomComplet($id),
            'client' => $clientData
        ]);
    }

    public function update($id) {
        $this->requireAdmin();
        error_log('ClientController::update() - ID: ' . $id . ', Method: ' . $_SERVER['REQUEST_METHOD']);
        
        if (!$this->isPost()) {
            error_log('ClientController::update() - Not a POST request, redirecting to /clients');
            $this->redirect('/clients');
            return;
        }
        
        error_log('ClientController::update() - Processing POST request');
        
        $data = [
            'id' => $id,
            'nom' => $this->postParam('nom'),
            'prenom' => $this->postParam('prenom'),
            'email' => $this->postParam('email'),
            'adresse' => $this->postParam('adresse'),
            'telephone' => $this->postParam('telephone')
        ];
        
        // Validation des données
        $client = new Client();
        $errors = $client->validate($data);
        
        if (!empty($errors)) {
            $this->display('client/edit', [
                'pageTitle' => 'Modifier le client',
                'client' => array_merge(['id' => $id], $data),
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer une copie des données sans l'ID pour l'update
        $updateData = $data;
        unset($updateData['id']); // Supprimer l'ID pour éviter les conflits
        
        $result = $client->update($updateData, $id);
        
        if ($result) {
            $this->addFlashMessage('Client modifié avec succès', 'success');
            $this->redirect('/client/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de la modification du client', 'error');
            $this->display('client/edit', [
                'pageTitle' => 'Modifier le client',
                'client' => array_merge(['id' => $id], $data)
            ]);
        }
    }
    
    /**
     * Supprime un client
     * 
     * @param int $id ID du client
     */
    public function delete($id) {
        $this->requireAdmin();
        $client = new Client();
        $clientData = $client->selectId($id);
        
        if (!$clientData) {
            $this->error(404, 'Client non trouvé');
            return;
        }
        
        // Vérifier si le client a des commandes
        $commandes = $client->getCommandesByClient($id);
        
        if (count($commandes) > 0) {
            $this->addFlashMessage('Impossible de supprimer ce client car il a des commandes associées', 'error');
            $this->redirect('/client/' . $id);
            return;
        }
        
        $result = $client->delete($id);
        
        if ($result) {
            $this->addFlashMessage('Client supprimé avec succès', 'success');
        } else {
            $this->addFlashMessage('Erreur lors de la suppression du client', 'error');
        }
        
        $this->redirect('/clients');
    }
    
    /**
     * Recherche de clients
     */
    public function search() {
        $keyword = $this->getParam('q');
        
        if (empty($keyword)) {
            $this->redirect('/clients');
            return;
        }
        
        $client = new Client();
        $clients = $client->search($keyword);
        
        $this->display('client/search', [
            'pageTitle' => 'Résultats de recherche pour "' . htmlspecialchars($keyword) . '"',
            'keyword' => $keyword,
            'clients' => $clients
        ]);
    }
}
