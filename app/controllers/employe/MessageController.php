<?php

namespace app\controllers\employe;

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
     * Afficher la liste des conversations de l'employé
     */
    public function index()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            Flight::redirect('/login');
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $conversations = $this->messageModel->getConversationsByEmploye($id_employe);

        Flight::render('employe/messages', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Afficher une conversation spécifique
     */
    public function show($id_conversation)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            Flight::redirect('/login');
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $conversation = $this->messageModel->getConversation($id_conversation);

        // Vérifier que la conversation appartient bien à cet employé
        if (!$conversation || $conversation['id_employe'] != $id_employe) {
            Flight::redirect('/employe/messages');
            return;
        }

        // Marquer les messages RH comme lus
        $this->messageModel->markAsRead($id_conversation, 'employe');

        $messages = $this->messageModel->getMessages($id_conversation);

        Flight::render('employe/conversation', [
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    /**
     * Créer une nouvelle conversation
     */
    public function create()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            Flight::json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }

        $sujet = $_POST['sujet'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($sujet) || empty($message)) {
            Flight::json(['success' => false, 'message' => 'Sujet et message requis'], 400);
            return;
        }

        $id_employe = $_SESSION['user_id'];
        
        // Créer la conversation
        $id_conversation = $this->messageModel->createConversation($id_employe, $sujet);
        
        // Envoyer le premier message
        $this->messageModel->sendMessage($id_conversation, 'employe', $id_employe, $message);

        Flight::json(['success' => true, 'id_conversation' => $id_conversation]);
    }

    /**
     * Envoyer un message dans une conversation
     */
    public function sendMessage($id_conversation)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            Flight::json(['success' => false, 'message' => 'Non autorisé'], 403);
            return;
        }

        $message = $_POST['message'] ?? '';
        
        if (empty($message)) {
            Flight::json(['success' => false, 'message' => 'Message vide'], 400);
            return;
        }

        $id_employe = $_SESSION['user_id'];
        $conversation = $this->messageModel->getConversation($id_conversation);

        // Vérifier que la conversation appartient bien à cet employé
        if (!$conversation || $conversation['id_employe'] != $id_employe) {
            Flight::json(['success' => false, 'message' => 'Conversation non trouvée'], 404);
            return;
        }

        $result = $this->messageModel->sendMessage($id_conversation, 'employe', $id_employe, $message);

        Flight::json(['success' => $result]);
    }
}
