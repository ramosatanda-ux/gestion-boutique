<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AchatController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CodeBarreController;
use App\Http\Controllers\CategorieController;

// ─── Page d'accueil → redirige vers login ────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Dashboard ───────────────────────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// ─── Profil (tous les rôles) ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Clients (admin + user — pas caissiere) ──────────────────────────────────
Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::get('/clients/debiteurs',              [ClientController::class, 'debiteurs'])->name('clients.debiteurs');
    Route::get('/clients/{client}/historique/pdf',[ClientController::class, 'historiquePdf'])->name('clients.historique.pdf');
    Route::get('/clients/{client}/payer',         [ClientController::class, 'payerForm'])->name('clients.payer.form');
    Route::post('/clients/{client}/payer',        [ClientController::class, 'payer'])->name('clients.payer');
    Route::resource('clients', ClientController::class)->except(['show']);
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
});

// ─── Facture PDF (tous les rôles — la caissière en a besoin après une vente POS)
Route::middleware('auth')->group(function () {
    Route::get('/ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');
});

// ─── Ventes (admin + user — pas caissiere) ───────────────────────────────────
Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::resource('ventes', VenteController::class)->only(['index', 'create', 'store']);
    Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
});

// ─── POS (tous les rôles connectés : admin, user, caissiere) ─────────────────
Route::middleware('auth')->group(function () {
    Route::get('/pos',                 [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/historique',      [PosController::class, 'historique'])->name('pos.historique');
    Route::get('/pos/rechercher',      [PosController::class, 'rechercherProduit'])->name('pos.rechercher');
    Route::post('/pos/scanner',        [PosController::class, 'scannerCodeBarre'])->name('pos.scanner');
    Route::post('/pos/vente',          [PosController::class, 'enregistrerVente'])->name('pos.vente');
    Route::post('/pos/decoder-image',  [PosController::class, 'decoderImage'])->name('pos.decoder');
});

// ─── Admin uniquement ────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // Catégories
    Route::resource('categories', CategorieController::class)->only(['index', 'store', 'update', 'destroy']);

    // Produits (CRUD complet)
    Route::resource('produits', ProduitController::class)->except(['show']);

    // Achats (ajout seulement, pas de modification pour l'intégrité comptable)
    Route::resource('achats', AchatController::class)->only(['index', 'create', 'store']);

    // Fournisseurs (sans show — le résumé est dans edit)
    Route::resource('fournisseurs', FournisseurController::class)->except(['show']);

    // Utilisateurs
    Route::resource('users', UserController::class)->except(['show']);

    // Rapports
    Route::get('/rapports',                [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/export/ventes',  [RapportController::class, 'exportVentesExcel'])->name('rapports.export.ventes');
    Route::get('/rapports/export/stocks',  [RapportController::class, 'exportStocksExcel'])->name('rapports.export.stocks');
    Route::get('/rapports/export/complet', [RapportController::class, 'exportRapportCompletExcel'])->name('rapports.export.complet');
    Route::get('/rapports/export/pdf',     [RapportController::class, 'exportRapportPdf'])->name('rapports.export.pdf');

    // Code-barres
    Route::post('/codebarre/{id}/generer', [CodeBarreController::class, 'generer'])->name('codebarre.generer');
    Route::post('/codebarre/generer-tous', [CodeBarreController::class, 'genererTous'])->name('codebarre.generer-tous');
    Route::get('/codebarre/imprimer',      [CodeBarreController::class, 'imprimer'])->name('codebarre.imprimer');
});

require __DIR__.'/auth.php';
