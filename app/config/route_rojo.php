<?php 

use app\controllers\admin\RojoPointageController;
use app\controllers\admin\RojoPaieController;

$paieController = new RojoPaieController();
$pointageController = new RojoPointageController();

$router->get('/pointage/arrivee', [$pointageController, 'pagePointageArrivee']);
$router->post('/pointage/individuel', [$pointageController, 'traiterPointageIndividuel']);

$router->get('/pointage/depart', [$pointageController, 'pagePointageDepart']);
$router->post('/pointage/depart/individuel', [$pointageController, 'traiterDepartIndividuel']);


$router->get('/absents', [$pointageController, 'listeAbsents']);
$router->post('/absents/marquer', [$pointageController, 'marquerAbsent']);


$router->get('/releve-presence', [$pointageController, 'relevePresence']);

$router->post('/pointage/marquer-absent', [$pointageController, 'marquerAbsentManuellement']);


$router->get('/paie/integration', [$paieController, 'pageIntegrationPaie']);
$router->get('/paie/export', [$paieController, 'exporterDonneesPaie']);

$router->get('/paie/fiche', [$paieController, 'pageFicheEmploye']);
$router->get('/paie/fiche/export', [$paieController, 'exporterFicheEmploye']);