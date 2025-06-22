<?php
/**
 * Fichier de configuration des routes
 * Ce fichier définit toutes les routes de l'application
 */

use App\Routes\Route;
use App\Controllers\HomeController;
use App\Controllers\PlanteController;
use App\Controllers\CategorieController;
use App\Controllers\ClientController;
use App\Controllers\CommandeController;

// Routes pour la page d'accueil
Route::get('/', [HomeController::class, 'index']);

// Routes pour les plantes
Route::get('/plantes', [PlanteController::class, 'index']);
Route::get('/plante/create', [PlanteController::class, 'create']);
Route::post('/plante/store', [PlanteController::class, 'store']);
Route::get('/plantes/search', [PlanteController::class, 'search']);
Route::get('/plante/{id}', [PlanteController::class, 'show']);
Route::get('/plante/{id}/edit', [PlanteController::class, 'edit']);
Route::post('/plante/{id}/update', [PlanteController::class, 'update']);
Route::post('/plante/{id}/delete', [PlanteController::class, 'delete']);

// Routes pour les catégories
Route::get('/categories', [CategorieController::class, 'index']);
Route::get('/categorie/create', [CategorieController::class, 'create']);
Route::post('/categorie/store', [CategorieController::class, 'store']);
Route::get('/categorie/{id}', [CategorieController::class, 'show']);
Route::get('/categorie/{id}/edit', [CategorieController::class, 'edit']);
Route::post('/categorie/{id}/update', [CategorieController::class, 'update']);
Route::post('/categorie/{id}/delete', [CategorieController::class, 'delete']);

// Routes pour les clients
Route::get('/clients', [ClientController::class, 'index']);
Route::get('/client/create', [ClientController::class, 'create']);
Route::post('/client/store', [ClientController::class, 'store']);
Route::get('/clients/search', [ClientController::class, 'search']);
Route::get('/client/{id}', [ClientController::class, 'show']);
Route::get('/client/{id}/edit', [ClientController::class, 'edit']);
Route::post('/client/{id}/update', [ClientController::class, 'update']);
Route::post('/client/{id}/delete', [ClientController::class, 'delete']);

// Routes pour les commandes
Route::get('/commandes', [CommandeController::class, 'index']);
Route::get('/commande/create', [CommandeController::class, 'create']);
Route::get('/client/{id}/commande/create', [CommandeController::class, 'createForClient']);
Route::post('/commande/store', [CommandeController::class, 'store']);
Route::get('/commande/{id}', [CommandeController::class, 'show']);
Route::get('/commande/{id}/edit', [CommandeController::class, 'edit']);
Route::post('/commande/{id}/update', [CommandeController::class, 'update']);
// La fonction updateStatus est maintenant gérée directement via la méthode update
