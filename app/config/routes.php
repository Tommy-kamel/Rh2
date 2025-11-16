<?php

use app\controllers\admin\AuthController as AdminAuthController;
use app\controllers\admin\DashboardController as AdminDashboardController;
use app\controllers\admin\RhDashboardController;
use app\controllers\admin\CalendrierController;
use app\controllers\admin\StatistiquesController;
use app\controllers\employe\AuthController as EmployeAuthController;
use app\controllers\employe\DashboardController as EmployeDashboardController;
use app\controllers\employe\CongeController as EmployeCongeController;
use flight\Engine;
use flight\net\Router;

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
$router->post('/admin/conges/valider', [$Admin_Dashboard_Controller, 'validerConge']);
$router->get('/admin/conges/refuser/@id_conge', [$Admin_Dashboard_Controller, 'refuserConge']);
$router->get('/admin/conges/details/@id_conge', [$Admin_Dashboard_Controller, 'voirDetailsConge']);
$router->get('/admin/conges/modifier/@id_conge', [$Admin_Dashboard_Controller, 'modifierConge']);
$router->post('/admin/conges/modifier/@id_conge', [$Admin_Dashboard_Controller, 'traiterModifierConge']);

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