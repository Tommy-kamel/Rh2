<?php

namespace app\controllers\admin;

use app\models\MessageModel;
use Flight;

class MessageController
{
    private $messageModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    /**
     * Afficher la liste des conversations pour le RH
     */
    public function index()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::redirect('/login');
            return;
        }

        $id_user = $_SESSION['user_id'];
        $is_rh = isset($_SESSION['nom_departement']) && $_SESSION['nom_departement'] === 'Ressources Humaines';

        // Si c'est un admin RH, voir toutes les conversations, sinon seulement celles assignées
        $conversations = $this->messageModel->getConversationsForRH($is_rh ? null : $id_user);

        Flight::render('admin/messages', [
            'conversations' => $conversations,
            'is_rh' => $is_rh
        ]);
    }

    /**
     * Afficher une conversation spécifique
     */
    public function show($id_conversation)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::redirect('/login');
            return;
        }

        $conversation = $this->messageModel->getConversation($id_conversation);

        if (!$conversation) {
            Flight::redirect('/admin/messages');
            return;
        }

        // Assigner automatiquement le RH s'il n'est pas encore assigné
        if ($conversation['id_user_rh'] === null) {
            $this->messageModel->assignRH($id_conversation, $_SESSION['user_id']);
            $conversation['id_user_rh'] = $_SESSION['user_id'];
        }

        // Marquer les messages employé comme lus
        $this->messageModel->markAsRead($id_conversation, 'rh');

        $messages = $this->messageModel->getMessages($id_conversation);

        Flight::render('admin/conversation', [
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    /**
     * Envoyer un message dans une conversation
     */
    public function sendMessage($id_conversation)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }

        $message = $_POST['message'] ?? '';
        
        if (empty($message)) {
            Flight::json(['success' => false, 'message' => 'Message vide'], 400);
            return;
        }

        $id_user = $_SESSION['user_id'];
        $result = $this->messageModel->sendMessage($id_conversation, 'rh', $id_user, $message);

        Flight::json(['success' => $result]);
    }

    /**
     * Changer le statut d'une conversation
     */
    public function updateStatus($id_conversation)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            Flight::json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }

        $status = $_POST['status'] ?? '';
        
        if (!in_array($status, ['ouvert', 'en_cours', 'ferme'])) {
            Flight::json(['success' => false, 'message' => 'Statut invalide'], 400);
            return;
        }

        $result = $this->messageModel->updateStatus($id_conversation, $status);

        Flight::json(['success' => $result]);
    }
}
