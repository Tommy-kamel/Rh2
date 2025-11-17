<?php

use app\controllers\admin\AuthController as AdminAuthController;
use app\controllers\admin\DashboardController as AdminDashboardController;
use app\controllers\admin\RhDashboardController;
use app\controllers\admin\CalendrierController;
use app\controllers\admin\StatistiquesController;
use app\controllers\employe\AuthController as EmployeAuthController;
use app\controllers\employe\DashboardController as EmployeDashboardController;
use app\controllers\employe\CongeController as EmployeCongeController;
use app\controllers\paiement\PaiementEmployeController;
use app\controllers\paiement\FichePaiementController;
use app\controllers\paiement\DetailPaiementController;
use app\controllers\paiement\PrimesGlobalController;
use app\controllers\paiement\HistoriqueFicheController;
use app\controllers\paiement\DetailPaiementPDFController;
use app\controllers\admin\EmployeeController;
use flight\Engine;
use flight\net\Router;


require_once('route_rojo.php');
/** 
 * @var Router $router 
 * @var Engine $app
 */

// Routes de connexion Admin
$Admin_Auth_Controller = new AdminAuthController();
$router->get('/login', [$Admin_Auth_Controller, 'afficherPageConnexion']);
$router->post('/login/admin', [$Admin_Auth_Controller, 'traiterConnexion']);

// Routes de connexion Employé
$Employe_Auth_Controller = new EmployeAuthController();
$router->post('/login/employe', [$Employe_Auth_Controller, 'traiterConnexion']);

// Déconnexion (commune)
$router->get('/logout', function() use ($Admin_Auth_Controller) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'employe') {
        $Employe_Auth_Controller = new EmployeAuthController();
        $Employe_Auth_Controller->deconnexion();
    } else {
        $Admin_Auth_Controller->deconnexion();
    }
});

// Routes Admin
$Admin_Dashboard_Controller = new AdminDashboardController();
$router->get('/dashboard', [$Admin_Dashboard_Controller, 'afficher']);
$router->get('/conges/attente', [$Admin_Dashboard_Controller, 'listeCongesAttente']);
$router->get('/conges/historique', [$Admin_Dashboard_Controller, 'listeCongesHistorique']);
$router->post('/admin/conges/valider', [$Admin_Dashboard_Controller, 'validerConge']);
$router->get('/admin/conges/refuser/@id_conge', [$Admin_Dashboard_Controller, 'refuserConge']);
$router->get('/admin/conges/details/@id_conge', [$Admin_Dashboard_Controller, 'voirDetailsConge']);
$router->get('/admin/conges/modifier/@id_conge', [$Admin_Dashboard_Controller, 'modifierConge']);
$router->post('/admin/conges/modifier/@id_conge', [$Admin_Dashboard_Controller, 'traiterModifierConge']);
$router->get('/admin/conges/suggestions/@id_conge', [$Admin_Dashboard_Controller, 'getSuggestionsConge']);

// Routes RH (Ressources Humaines)
$Rh_Dashboard_Controller = new RhDashboardController();
$router->get('/rh/dashboard', [$Rh_Dashboard_Controller, 'afficher']);
$router->get('/rh/conges/attente', [$Rh_Dashboard_Controller, 'listeCongesAttente']);
$router->get('/rh/conges/refuser/@id_conge', [$Rh_Dashboard_Controller, 'refuserConge']);
$router->get('/rh/conges/details/@id_conge', [$Rh_Dashboard_Controller, 'voirDetailsConge']);
$router->get('/rh/conges/modifier/@id_conge', [$Rh_Dashboard_Controller, 'modifierConge']);
$router->post('/rh/conges/modifier/@id_conge', [$Rh_Dashboard_Controller, 'traiterModifierConge']);

// Routes Calendrier
$Calendrier_Controller = new CalendrierController();
$router->get('/calendrier', [$Calendrier_Controller, 'afficher']);

// Routes Statistiques
$Statistiques_Controller = new StatistiquesController();
$router->get('/statistiques', [$Statistiques_Controller, 'afficherStatistiques']);

// API Statistiques (optionnel pour utilisation AJAX)
$router->get('/api/statistiques/genre', [$Statistiques_Controller, 'getStatistiquesGenre']);
$router->get('/api/statistiques/age', [$Statistiques_Controller, 'getStatistiquesAge']);
$router->get('/api/statistiques/departement', [$Statistiques_Controller, 'getStatistiquesDepartement']);
$router->get('/api/statistiques/contrat', [$Statistiques_Controller, 'getStatistiquesContrat']);
$router->get('/api/statistiques/resume', [$Statistiques_Controller, 'getResumeEffectifs']);

// Routes Employé
$Employe_Dashboard_Controller = new EmployeDashboardController();
$router->get('/employe/dashboard', [$Employe_Dashboard_Controller, 'afficher']);

$EmployeCongeController = new EmployeCongeController();
$router->post('/employe/conges/demander', [$EmployeCongeController, 'addConge']);

$EmployeeController = new EmployeeController();
$router->get('/employes', [$EmployeeController, 'liste']);
$router->get('/employes/ajouter', [$EmployeeController, 'ajouter']);              // <-- page ajout employé (form)
$router->post('/employes/ajouter', [$EmployeeController, 'store']);  
$router->get('/employes/@id', [$EmployeeController, 'afficher']);

// pages séparées
$router->get('/employes/@id/contrat', [$EmployeeController, 'contrat']);
$router->get('/employes/@id/postes', [$EmployeeController, 'postes']);
$router->get('/employes/@id/documents', [$EmployeeController, 'documents']);

// actions déjà présentes (création/renouvellement/terminer/ajout poste/upload document)
$router->post('/employes/@id/contrat/creer', [$EmployeeController, 'creerContrat']);
$router->post('/employes/@id/contrat/renouveler', [$EmployeeController, 'renouvelerContrat']);
$router->post('/employes/@id/contrat/terminer', [$EmployeeController, 'terminerContrat']);
$router->post('/employes/@id/poste/ajouter', [$EmployeeController, 'ajouterPosteHistory']);
$router->post('/employes/@id/documents/upload', [$EmployeeController, 'uploadDocument']);


$router->get('/employes/ajouter', [$EmployeeController, 'ajouter']);              // <-- page ajout employé (form)
$router->post('/employes/ajouter', [$EmployeeController, 'store']);              // <-- enregistrement nouvel employé

// ...existing code...
// ...existing code...
// ...existing code...

// Redirection page d'accueil
$router->get('/', function() {
    Flight::redirect('/login');
});
 



// $router->get('/hello-world/@name', function($name) {
// 	echo '<h1>Hello world! Oh hey '.$name.'!</h1>';
// });

// $router->group('/api', function() use ($router, $app) {
// 	$Api_Example_Controller = new ApiExampleController($app);
// 	$router->get('/users', [ $Api_Example_Controller, 'getUsers' ]);
// 	$router->get('/users/@id:[0-9]', [ $Api_Example_Controller, 'getUser' ]);
// 	$router->post('/users/@id:[0-9]', [ $Api_Example_Controller, 'updateUser' ]);
	
// });

// liste des employee pour voir la fiche de paie
$paiementemploye = new PaiementEmployeController();
$ficheemploye = new FichePaiementController();
$detailsfiche = new DetailPaiementController();
$primeglobal = new PrimesGlobalController();
$historique = new HistoriqueFicheController();
$detailPDF = new DetailPaiementPDFController();

Flight::route('GET /paiement/employes', [$paiementemploye, 'liste']);
Flight::route('GET /paiement/irsa', [$paiementemploye, 'listeIrsa']);
Flight::route('GET /paiement/fiche/', [$ficheemploye, 'fiche']);
Flight::route('GET /paiement/enregistrer_fiche/', [$ficheemploye, 'enregistrerFiche']);
Flight::route('GET /paiement/prime', [$primeglobal, 'index']);
Flight::route('GET /paiement/historique', [$historique, 'index']);

// Détails pour un employé et un mois
Flight::route('GET /paiement/details/@id_employe/@type/@mois_annee', [$detailsfiche, 'show']);
Flight::route('GET /paiement/fiche_irsa', [$detailsfiche, 'showIrsa']);

// exportation
Flight::route('/paiement/fichepdf', [$paiementemploye, 'fichePDF']);
Flight::route('/paiement/fiche-excel', [$paiementemploye, 'ficheExcel']);
Flight::route('/paiement/fiche-excel-xml', [$paiementemploye, 'ficheExcelXML']);
Flight::route('/paiement/fichepdf/@id_employe/@type/@mois_annee', [$detailPDF, 'showPDF']);
