<?php

namespace app\models\employe;

use Flight;

class NotificationModel
{
    /**
     * Récupère les notifications d'un employé
     */
    public function getNotifications($id_employe, $limit = 50)
    {
        $sql = "SELECT * FROM notifications
                WHERE id_employe = :id_employe
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = Flight::db()->prepare($sql);
        $stmt->bindValue(':id_employe', $id_employe, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les notifications non lues d'un employé
     */
    public function getUnreadNotifications($id_employe)
    {
        $sql = "SELECT COUNT(*) as count FROM notifications
                WHERE id_employe = :id_employe AND lu = FALSE";

        $stmt = Flight::db()->prepare($sql);
        $stmt->execute(['id_employe' => $id_employe]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead($id_notification, $id_employe)
    {
        $sql = "UPDATE notifications SET lu = TRUE
                WHERE id_notification = :id_notification AND id_employe = :id_employe";

        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'id_notification' => $id_notification,
            'id_employe' => $id_employe
        ]);
    }

    /**
     * Marque toutes les notifications comme lues pour un employé
     */
    public function markAllAsRead($id_employe)
    {
        $sql = "UPDATE notifications SET lu = TRUE WHERE id_employe = :id_employe";

        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute(['id_employe' => $id_employe]);
    }

    /**
     * Crée une nouvelle notification
     */
    public function createNotification($id_employe, $titre, $message, $type = 'general')
    {
        $sql = "INSERT INTO notifications (id_employe, titre, message, type, created_at)
                VALUES (:id_employe, :titre, :message, :type, NOW())";

        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'id_employe' => $id_employe,
            'titre' => $titre,
            'message' => $message,
            'type' => $type
        ]);
    }

    /**
     * Supprime une notification
     */
    public function deleteNotification($id_notification, $id_employe)
    {
        $sql = "DELETE FROM notifications
                WHERE id_notification = :id_notification AND id_employe = :id_employe";

        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([
            'id_notification' => $id_notification,
            'id_employe' => $id_employe
        ]);
    }
}