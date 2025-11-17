<?php

use app\controllers\admin\AuthController as AdminAuthController;
use app\controllers\admin\DashboardController as AdminDashboardController;
use app\controllers\admin\RhDashboardController;
use app\controllers\employe\AuthController as EmployeAuthController;
use app\controllers\employe\DashboardController as EmployeDashboardController;
use app\controllers\employe\CongeController as EmployeCongeController;
use app\controllers\admin\EmployeeController;
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

// Routes RH (Ressources Humaines)
$Rh_Dashboard_Controller = new RhDashboardController();
$router->get('/rh/dashboard', [$Rh_Dashboard_Controller, 'afficher']);

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