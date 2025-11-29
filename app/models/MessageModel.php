<?php

namespace app\models;

use Flight;

class MessageModel
{
    /**
     * Créer une nouvelle conversation
     */
    public function createConversation($id_employe, $sujet)
    {
        $sql = "INSERT INTO messagerie_conversations (id_employe, sujet) VALUES (?, ?)";
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe, $sujet]);
        return Flight::db()->lastInsertId();
    }

    /**
     * Obtenir toutes les conversations d'un employé
     */
    public function getConversationsByEmploye($id_employe)
    {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM messagerie_messages m 
                        WHERE m.id_conversation = c.id_conversation 
                        AND m.sender_type = 'rh' AND m.lu = FALSE) as unread_count,
                       (SELECT m.message FROM messagerie_messages m 
                        WHERE m.id_conversation = c.id_conversation 
                        ORDER BY m.created_at DESC LIMIT 1) as dernier_message,
                       (SELECT m.created_at FROM messagerie_messages m 
                        WHERE m.id_conversation = c.id_conversation 
                        ORDER BY m.created_at DESC LIMIT 1) as dernier_message_date
                FROM messagerie_conversations c
                WHERE c.id_employe = ?
                ORDER BY c.updated_at DESC";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_employe]);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir toutes les conversations pour le RH
     */
    public function getConversationsForRH($id_user_rh = null)
    {
        if ($id_user_rh) {
            // Conversations assignées à cet utilisateur RH
            $sql = "SELECT c.*, e.nom, e.prenom,
                           (SELECT COUNT(*) FROM messagerie_messages m 
                            WHERE m.id_conversation = c.id_conversation 
                            AND m.sender_type = 'employe' AND m.lu = FALSE) as unread_count,
                           (SELECT m.message FROM messagerie_messages m 
                            WHERE m.id_conversation = c.id_conversation 
                            ORDER BY m.created_at DESC LIMIT 1) as dernier_message,
                           (SELECT m.created_at FROM messagerie_messages m 
                            WHERE m.id_conversation = c.id_conversation 
                            ORDER BY m.created_at DESC LIMIT 1) as dernier_message_date
                    FROM messagerie_conversations c
                    INNER JOIN employe e ON c.id_employe = e.id_employe
                    WHERE c.id_user_rh = ?
                    ORDER BY c.updated_at DESC";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([$id_user_rh]);
        } else {
            // Toutes les conversations (pour admin RH)
            $sql = "SELECT c.*, e.nom, e.prenom, u.nom_utilisateur as rh_name,
                           (SELECT COUNT(*) FROM messagerie_messages m 
                            WHERE m.id_conversation = c.id_conversation 
                            AND m.sender_type = 'employe' AND m.lu = FALSE) as unread_count,
                           (SELECT m.message FROM messagerie_messages m 
                            WHERE m.id_conversation = c.id_conversation 
                            ORDER BY m.created_at DESC LIMIT 1) as dernier_message,
                           (SELECT m.created_at FROM messagerie_messages m 
                            WHERE m.id_conversation = c.id_conversation 
                            ORDER BY m.created_at DESC LIMIT 1) as dernier_message_date
                    FROM messagerie_conversations c
                    INNER JOIN employe e ON c.id_employe = e.id_employe
                    LEFT JOIN user u ON c.id_user_rh = u.id_user
                    ORDER BY c.updated_at DESC";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }

    /**
     * Obtenir une conversation avec ses messages
     */
    public function getConversation($id_conversation)
    {
        $sql = "SELECT c.*, e.nom, e.prenom, e.photo
                FROM messagerie_conversations c
                INNER JOIN employe e ON c.id_employe = e.id_employe
                WHERE c.id_conversation = ?";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_conversation]);
        return $stmt->fetch();
    }

    /**
     * Obtenir les messages d'une conversation
     */
    public function getMessages($id_conversation)
    {
        $sql = "SELECT m.*, 
                       CASE 
                           WHEN m.sender_type = 'employe' THEN e.nom
                           WHEN m.sender_type = 'rh' THEN u.nom_utilisateur
                       END as sender_name,
                       CASE 
                           WHEN m.sender_type = 'employe' THEN e.photo
                           ELSE NULL
                       END as sender_photo
                FROM messagerie_messages m
                LEFT JOIN employe e ON m.sender_type = 'employe' AND m.sender_id = e.id_employe
                LEFT JOIN user u ON m.sender_type = 'rh' AND m.sender_id = u.id_user
                WHERE m.id_conversation = ?
                ORDER BY m.created_at ASC";
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$id_conversation]);
        return $stmt->fetchAll();
    }

    /**
     * Envoyer un message
     */
    public function sendMessage($id_conversation, $sender_type, $sender_id, $message)
    {
        $sql = "INSERT INTO messagerie_messages (id_conversation, sender_type, sender_id, message) 
                VALUES (?, ?, ?, ?)";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([$id_conversation, $sender_type, $sender_id, $message]);
    }

    /**
     * Marquer les messages comme lus
     */
    public function markAsRead($id_conversation, $sender_type)
    {
        // Marquer comme lus tous les messages de l'autre partie
        $opposite_type = $sender_type === 'employe' ? 'rh' : 'employe';
        $sql = "UPDATE messagerie_messages 
                SET lu = TRUE 
                WHERE id_conversation = ? AND sender_type = ? AND lu = FALSE";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([$id_conversation, $opposite_type]);
    }

    /**
     * Assigner un utilisateur RH à une conversation
     */
    public function assignRH($id_conversation, $id_user_rh)
    {
        $sql = "UPDATE messagerie_conversations 
                SET id_user_rh = ?, status = 'en_cours' 
                WHERE id_conversation = ?";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([$id_user_rh, $id_conversation]);
    }

    /**
     * Changer le statut d'une conversation
     */
    public function updateStatus($id_conversation, $status)
    {
        $sql = "UPDATE messagerie_conversations SET status = ? WHERE id_conversation = ?";
        $stmt = Flight::db()->prepare($sql);
        return $stmt->execute([$status, $id_conversation]);
    }

    /**
     * Obtenir le nombre de messages non lus
     */
    public function getUnreadCount($user_type, $user_id)
    {
        if ($user_type === 'employe') {
            $sql = "SELECT COUNT(*) as count 
                    FROM messagerie_messages m
                    INNER JOIN messagerie_conversations c ON m.id_conversation = c.id_conversation
                    WHERE c.id_employe = ? AND m.sender_type = 'rh' AND m.lu = FALSE";
        } else {
            $sql = "SELECT COUNT(*) as count 
                    FROM messagerie_messages m
                    INNER JOIN messagerie_conversations c ON m.id_conversation = c.id_conversation
                    WHERE (c.id_user_rh = ? OR c.id_user_rh IS NULL) 
                    AND m.sender_type = 'employe' AND m.lu = FALSE";
        }
        
        $stmt = Flight::db()->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}
