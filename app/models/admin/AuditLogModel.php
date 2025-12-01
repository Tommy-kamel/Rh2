<?php

namespace app\models\admin;

use Flight;

class AuditLogModel
{
    /**
     * Enregistre une action dans le journal d'audit
     *
     * @param string $action Description de l'action
     * @param string|null $table_name Nom de la table affectée
     * @param int|null $record_id ID de l'enregistrement affecté
     * @param array|null $old_values Valeurs anciennes
     * @param array|null $new_values Valeurs nouvelles
     * @param int|null $user_id ID de l'utilisateur (optionnel, sinon récupéré de la session)
     * @return bool Succès de l'enregistrement
     */
    public function logAction($action, $table_name = null, $record_id = null, $old_values = null, $new_values = null, $user_id = null)
    {
        // Récupérer l'ID utilisateur depuis la session si non fourni
        if ($user_id === null && isset($_SESSION['user_id'])) {
            // Vérifier si c'est un utilisateur admin (dans la table user) ou un employé
            if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'employe') {
                // Pour les employés, ne pas utiliser user_id car ils ne sont pas dans la table user
                $user_id = null;
            } else {
                // Pour les admins, utiliser l'ID de session
                $user_id = $_SESSION['user_id'];
            }
        }

        // Si user_id est fourni, vérifier qu'il existe dans la table user
        if ($user_id !== null) {
            $check_sql = "SELECT id_user FROM user WHERE id_user = ?";
            $check_stmt = Flight::db()->prepare($check_sql);
            $check_stmt->execute([$user_id]);
            if (!$check_stmt->fetch()) {
                // L'utilisateur n'existe pas dans la table user, mettre à null
                $user_id = null;
            }
        }

        // Récupérer l'adresse IP et le User-Agent
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Sérialiser les valeurs si elles sont des tableaux
        $old_values_json = $old_values ? json_encode($old_values) : null;
        $new_values_json = $new_values ? json_encode($new_values) : null;

        $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            $user_id,
            $action,
            $table_name,
            $record_id,
            $old_values_json,
            $new_values_json,
            $ip_address,
            $user_agent
        ]);
    }

    /**
     * Récupère les logs d'audit avec pagination
     *
     * @param int $limit Nombre d'enregistrements par page
     * @param int $offset Offset pour la pagination
     * @param array $filters Filtres optionnels (user_id, action, table_name, date_debut, date_fin)
     * @return array Liste des logs
     */
    public function getAuditLogs($limit = 50, $offset = 0, $filters = [])
    {
        $sql = "SELECT al.*, u.nom_utilisateur
                FROM audit_log al
                LEFT JOIN user u ON al.user_id = u.id_user
                WHERE 1=1";

        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND al.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $sql .= " AND al.action LIKE ?";
            $params[] = '%' . $filters['action'] . '%';
        }

        if (!empty($filters['table_name'])) {
            $sql .= " AND al.table_name = ?";
            $params[] = $filters['table_name'];
        }

        if (!empty($filters['date_debut'])) {
            $sql .= " AND al.timestamp >= ?";
            $params[] = $filters['date_debut'];
        }

        if (!empty($filters['date_fin'])) {
            $sql .= " AND al.timestamp <= ?";
            $params[] = $filters['date_fin'];
        }

        $sql .= " ORDER BY al.timestamp DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Compte le nombre total de logs avec les filtres
     *
     * @param array $filters Filtres optionnels
     * @return int Nombre total de logs
     */
    public function countAuditLogs($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM audit_log WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $sql .= " AND action LIKE ?";
            $params[] = '%' . $filters['action'] . '%';
        }

        if (!empty($filters['table_name'])) {
            $sql .= " AND table_name = ?";
            $params[] = $filters['table_name'];
        }

        if (!empty($filters['date_debut'])) {
            $sql .= " AND timestamp >= ?";
            $params[] = $filters['date_debut'];
        }

        if (!empty($filters['date_fin'])) {
            $sql .= " AND timestamp <= ?";
            $params[] = $filters['date_fin'];
        }

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}