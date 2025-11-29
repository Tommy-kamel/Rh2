<?php

namespace chatbot\Admin;

require_once __DIR__ . '/AdminChatbotModel.php';

class AdminChatbotController
{
    private $model;
    private $db;

    public function __construct($db = null)
    {
        // Obtenir la connexion DB
        if ($db === null) {
            $db = \Flight::get('db');
            if ($db === null) {
                // Fallback: créer une connexion directe
                $config = require __DIR__ . '/../../app/config/config.php';
                $dsn = 'mysql:host=' . $config['database']['host'] . ';dbname=' . $config['database']['dbname'] . ';charset=utf8mb4';
                $db = new \PDO($dsn, $config['database']['user'], $config['database']['password']);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
        }
        $this->db = $db;
        $this->model = new AdminChatbotModel($this->db);
    }

    /**
     * Afficher l'interface du chatbot admin
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Autoriser admin et tous les utilisateurs RH (peu importe le département)
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::redirect('/login');
            return;
        }

        \Flight::render('admin/admin_chatbot', [
            'page_title' => 'Assistant IA RH',
            'user_type' => $_SESSION['user_type'],
            'user_name' => $_SESSION['user_name'] ?? 'Admin'
        ]);
    }

    /**
     * Répondre aux questions fréquentes via chatbot
     */
    public function sendMessage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Autoriser admin
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        $message = \Flight::request()->data->message ?? '';
        $message = htmlspecialchars(trim($message));

        if (empty($message)) {
            \Flight::json(['response' => 'Veuillez entrer une question.']);
            return;
        }

        // For admin UI we use privileged handler that can query HR data
        try {
            $response = $this->model->getAdminResponse($message);
        } catch (\Exception $e) {
            error_log('Erreur getAdminResponse: ' . $e->getMessage());
            $response = $this->model->getResponse($message);
        }

        \Flight::json(['response' => $response]);
    }

    /**
     * Générer un document RH automatiquement
     */
    public function generateDocument()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Debug: Logger les informations de session
        error_log("Session user_type: " . ($_SESSION['user_type'] ?? 'non défini'));
        error_log("Session complete: " . json_encode($_SESSION));

        // Autoriser admin
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé - Type utilisateur: ' . ($_SESSION['user_type'] ?? 'non défini')]);
            return;
        }

        $type = \Flight::request()->data->type ?? '';
        $employeId = \Flight::request()->data->employe_id ?? null;
        $params = \Flight::request()->data->params ?? [];

        if (empty($type)) {
            \Flight::json(['error' => 'Type de document requis']);
            return;
        }

        $result = $this->model->generateDocument($type, $employeId, $params);
        \Flight::json($result);
    }

    /**
     * Prédire le turnover des employés
     */
    public function predictTurnover()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        $employeId = \Flight::request()->data->employe_id ?? null;
        $result = $this->model->predictTurnover($employeId);
        \Flight::json($result);
    }

    /**
     * Détecter les anomalies sur les heures ou la paie
     */
    public function detectAnomalies()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        $type = \Flight::request()->data->type ?? 'all'; // 'hours', 'salary', 'all'
        $period = \Flight::request()->data->period ?? 'month'; // 'week', 'month', 'year'
        
        $result = $this->model->detectAnomalies($type, $period);
        \Flight::json($result);
    }

    /**
     * Recommander des candidats pour un poste
     */
    public function recommendCandidates()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        $posteId = \Flight::request()->data->poste_id ?? null;
        $jobDescription = \Flight::request()->data->job_description ?? '';
        $skills = \Flight::request()->data->skills ?? [];

        if (empty($posteId) && empty($jobDescription)) {
            \Flight::json(['error' => 'Poste ou description requis']);
            return;
        }

        $result = $this->model->recommendCandidates($posteId, $jobDescription, $skills);
        \Flight::json($result);
    }

    /**
     * Analyser un CV uploadé
     */
    public function analyzeCV()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        if (!isset($_FILES['cv'])) {
            \Flight::json(['error' => 'Aucun fichier uploadé']);
            return;
        }

        $file = $_FILES['cv'];
        $result = $this->model->analyzeCV($file);
        \Flight::json($result);
    }

    /**
     * Obtenir les statistiques du chatbot
     */
    public function getStatistics()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        $stats = $this->model->getChatbotStatistics();
        \Flight::json($stats);
    }

    /**
     * Obtenir la liste des postes
     */
    public function getPostes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        try {
            if ($this->db === null) {
                \Flight::json(['error' => 'Base de données non disponible']);
                return;
            }

            $stmt = $this->db->query("SELECT id_poste, nom FROM poste ORDER BY nom");
            $postes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            \Flight::json(['success' => true, 'postes' => $postes]);
        } catch (\Exception $e) {
            \Flight::json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Obtenir la liste des employés
     */
    public function getEmployes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        try {
            if ($this->db === null) {
                \Flight::json(['error' => 'Base de données non disponible']);
                return;
            }

            $stmt = $this->db->query("SELECT id_employe, nom, prenom FROM employe ORDER BY nom, prenom");
            $employes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            \Flight::json(['success' => true, 'employes' => $employes]);
        } catch (\Exception $e) {
            \Flight::json(['error' => $e->getMessage()]);
        }
    }
}
