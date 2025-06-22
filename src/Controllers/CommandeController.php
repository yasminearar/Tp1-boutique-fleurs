<?php
namespace App\Controllers;

use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Client;
use App\Models\Plante;

class CommandeController extends Controller {
    /**
     * Affiche la liste des commandes
     */
    public function index() {
        // Récupérer toutes les commandes avec les détails des clients
        $commandes = Commande::withClientDetails();
        
        $this->display('commande/index', [
            'pageTitle' => 'Liste des commandes',
            'commandes' => $commandes
        ]);
    }
    
    /**
     * Affiche les détails d'une commande
     * 
     * @param int $id ID de la commande
     */
    public function show($id) {
        $commandeData = Commande::find($id);
        
        if (!$commandeData) {
            $this->error(404, 'Commande non trouvée');
            return;
        }
          // Récupérer le client de cette commande
        $client = Commande::getClient($id);
        
        // Récupérer les détails de la commande (plantes commandées)
        $details = CommandeDetail::getDetailsWithPlantes($id);
          $this->display('commande/show', [
            'pageTitle' => 'Commande #' . $id,
            'details' => $details,
            'plantes_commande' => $details,  // Ajout de cette ligne pour compatibilité avec le template
            'commande' => $commandeData,
            'client' => $client
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'une commande
     */
    public function create() {
        // Récupérer tous les clients pour le sélecteur
        $clients = Client::all();
        // Récupérer les plantes en stock
        $plantes = Plante::inStock();
        
        $this->display('commande/create', [
            'pageTitle' => 'Nouvelle commande',
            'clients' => $clients,
            'plantes' => $plantes
        ]);
    }    /**
     * Affiche le formulaire de création d'une commande pour un client spécifique
     * 
     * @param int $clientId ID du client
     */
    public function createForClient($clientId) {
        // Récupérer le client spécifique
        $client = Client::find($clientId);
        
        if (!$client) {
            $this->error(404, 'Client non trouvé');
            return;
        }
        
        // Récupérer les plantes en stock
        $plantes = Plante::inStock();
        
        $this->display('commande/create', [
            'pageTitle' => 'Nouvelle commande pour ' . $client['prenom'] . ' ' . $client['nom'],
            'client' => $client,
            'plantes' => $plantes,
            'clientPreselectionne' => true
        ]);
    }
        /**
     * Traite la soumission du formulaire de création
     */    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/commandes');
            return;
        }
        
        // Récupérer les données du formulaire
        $clientId = (int) $this->postParam('client_id');
        $total = (float) $this->postParam('total');
        $plantesCommandees = isset($_POST['plantes']) ? $_POST['plantes'] : [];
          // Préparer les données de la commande pour la validation
        $dataToValidate = [
            'id_client' => $clientId,
            'date_commande' => date('Y-m-d H:i:s'),
            'statut' => 'en cours',
            'total' => $total,
            'plantes' => $plantesCommandees // Pour la validation des plantes
        ];
        
        // Validation personnalisée des données
        $errors = $this->validateCommande($dataToValidate);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $clients = Client::all();
            $plantes = Plante::inStock();
            $this->display('commande/create', [
                'pageTitle' => 'Nouvelle commande',
                'commande' => [
                    'client_id' => $clientId
                ],
                'clients' => $clients,
                'plantes' => $plantes,
                'plantesCommandees' => $plantesCommandees,
                'errors' => $errors
            ]);
            return;
        }
        
        // Préparer les données pour l'insertion en base de données (sans la clé 'plantes')
        $dataToInsert = [
            'id_client' => $clientId,
            'date_commande' => date('Y-m-d H:i:s'),
            'statut' => 'en cours',
            'total' => $total
        ];
        
        // Créer la commande
        $commandeId = Commande::create($dataToInsert);
        
        if ($commandeId) {
            // Ajouter les détails de la commande
            $this->saveCommandeDetails($commandeId, $plantesCommandees);
            
            $this->addFlashMessage('Commande créée avec succès', 'success');
            $this->redirect('/commandes');
        } else {
            $this->addFlashMessage('Erreur lors de la création de la commande', 'error');
            $clients = Client::all();
            $plantes = Plante::inStock();
            $this->display('commande/create', [
                'pageTitle' => 'Nouvelle commande',
                'commande' => [
                    'client_id' => $clientId
                ],
                'clients' => $clients,
                'plantes' => $plantes,
                'plantesCommandees' => $plantesCommandees,
                'errors' => ['general' => 'Erreur lors de la création de la commande.']
            ]);
        }
    }
    
    /**
     * Valide les données du formulaire de commande
     * 
     * @param array $data Les données à valider
     * @return array Les erreurs de validation
     */
    private function validateCommande(array $data): array {
        $errors = [];
        
        // Validation du client
        if (empty($data['id_client'])) {
            $errors['client_id'] = 'Veuillez sélectionner un client.';
        } else {
            $client = Client::find($data['id_client']);
            if (!$client) {
                $errors['client_id'] = 'Le client sélectionné n\'existe pas.';
            }
        }
        
        // Validation des plantes (au moins une plante doit être sélectionnée)
        $hasPlante = false;
        if (isset($data['plantes']) && is_array($data['plantes'])) {
            foreach ($data['plantes'] as $planteId => $quantite) {
                if ((int)$quantite > 0) {
                    $hasPlante = true;
                    break;
                }
            }
        }
        
        if (!$hasPlante) {
            $errors['plantes'] = 'Veuillez sélectionner au moins une plante.';
        }
        
        return $errors;
    }
    
    /**
     * Enregistre les détails de la commande et met à jour le stock
     * 
     * @param int $commandeId ID de la commande
     * @param array $plantes Tableau associatif des plantes commandées [planteId => quantite]
     * @return void
     */    private function saveCommandeDetails(int $commandeId, array $plantes): void {
        foreach ($plantes as $planteId => $quantite) {
            $planteId = (int)$planteId;
            $quantite = (int)$quantite;
            
            if ($quantite > 0) {
                // Récupérer le prix de la plante
                $prix = Plante::getPrix($planteId);
                if ($prix) {
                    // Ajouter le détail
                    $detailId = CommandeDetail::ajouterDetail(
                        $commandeId,
                        $planteId,
                        $quantite,
                        $prix
                    );
                    
                    // Mettre à jour le stock
                    if ($detailId) {
                        Plante::decreaseStock($planteId, $quantite);
                    }
                }
            }
        }
    }
    
    /**
     * Met à jour le statut d'une commande
     * 
     * @param int $id ID de la commande
     */    public function updateStatus($id) {
        if (!$this->isPost()) {
            $this->redirect('/commandes');
            return;
        }
        
        $commandeData = Commande::find($id);
        
        if (!$commandeData) {
            $this->error(404, 'Commande non trouvée');
            return;
        }
        
        $statut = $this->postParam('statut');
        $result = Commande::updateStatut($id, $statut);
        
        if ($result) {
            $this->addFlashMessage('Statut de la commande mis à jour avec succès', 'success');
        } else {
            $this->addFlashMessage('Erreur lors de la mise à jour du statut', 'error');
        }
        
        $this->redirect('/commande/' . $id);
    }
    
    /**
     * Affiche le formulaire d'édition d'une commande
     * 
     * @param int $id ID de la commande
     */
    public function edit($id) {
        $commandeData = Commande::find($id);
        
        if (!$commandeData) {
            $this->error(404, 'Commande non trouvée');
            return;
        }
        
        // Récupérer le client de cette commande
        $client = Commande::getClient($id);
        
        // Récupérer les détails de la commande (plantes commandées)
        $details = CommandeDetail::getDetailsWithPlantes($id);
        
        $this->display('commande/edit', [
            'pageTitle' => 'Modifier la commande #' . $id,
            'commande' => $commandeData,
            'details' => $details,
            'client' => $client
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de modification
     * 
     * @param int $id ID de la commande
     */
    public function update($id) {
        if (!$this->isPost()) {
            $this->redirect('/commandes');
            return;
        }
        
        $commandeData = Commande::find($id);
        
        if (!$commandeData) {
            $this->error(404, 'Commande non trouvée');
            return;
        }
        
        // Récupérer les données du formulaire
        $statut = $this->postParam('statut');
        $notes = $this->postParam('notes');
        
        // Préparer les données pour la mise à jour
        $dataToUpdate = [
            'statut' => $statut
        ];
        
        // Ajouter les notes si elles sont définies
        if ($notes) {
            $dataToUpdate['notes'] = $notes;
        }
        
        // Mettre à jour la commande
        $result = Commande::update($id, $dataToUpdate);
        
        if ($result) {
            $this->addFlashMessage('Commande mise à jour avec succès', 'success');
            $this->redirect('/commande/' . $id);
        } else {
            $this->addFlashMessage('Erreur lors de la mise à jour de la commande', 'error');
            
            // Récupérer le client et les détails pour réafficher le formulaire
            $client = Commande::getClient($id);
            $details = CommandeDetail::getDetailsWithPlantes($id);
            
            $this->display('commande/edit', [
                'pageTitle' => 'Modifier la commande #' . $id,
                'commande' => $commandeData,
                'details' => $details,
                'client' => $client,
                'errors' => ['general' => 'Erreur lors de la mise à jour de la commande.']
            ]);
        }    }
}
