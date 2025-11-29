<?php

namespace app\controllers\admin;

use app\models\admin\AuditLogModel;
use Flight;

class AuditController
{
    private $auditModel;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
    }

    /**
     * Affiche la liste des logs d'audit
     */
    public function index()
    {
        // Vérifier les permissions (seulement pour les admins RH)
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::redirect('/login');
            return;
        }

        // Récupérer les paramètres de filtrage
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $filters = [];
        if (!empty($_GET['user_id'])) $filters['user_id'] = $_GET['user_id'];
        if (!empty($_GET['action'])) $filters['action'] = $_GET['action'];
        if (!empty($_GET['table_name'])) $filters['table_name'] = $_GET['table_name'];
        if (!empty($_GET['date_debut'])) $filters['date_debut'] = $_GET['date_debut'];
        if (!empty($_GET['date_fin'])) $filters['date_fin'] = $_GET['date_fin'];

        // Récupérer les logs
        $logs = $this->auditModel->getAuditLogs($limit, $offset, $filters);
        $total = $this->auditModel->countAuditLogs($filters);
        $totalPages = ceil($total / $limit);

        // Récupérer la liste des utilisateurs pour le filtre
        $users = Flight::db()->query("SELECT id_user, nom_utilisateur FROM user ORDER BY nom_utilisateur")->fetchAll();

        Flight::render('admin/audit_logs', [
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'filters' => $filters,
            'users' => $users
        ]);
    }

    /**
     * API pour récupérer les détails d'un log spécifique
     */
    public function getLogDetails($id)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::json(['error' => 'Accès non autorisé'], 403);
            return;
        }

        $stmt = Flight::db()->prepare("SELECT * FROM audit_log WHERE id_audit = ?");
        $stmt->execute([$id]);
        $log = $stmt->fetch();

        if (!$log) {
            Flight::json(['error' => 'Log non trouvé'], 404);
            return;
        }

        // Décoder les valeurs JSON
        if ($log['old_values']) {
            $log['old_values'] = json_decode($log['old_values'], true);
        }
        if ($log['new_values']) {
            $log['new_values'] = json_decode($log['new_values'], true);
        }

        Flight::json($log);
    }
}