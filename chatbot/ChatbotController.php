<?php

namespace chatbot;

require_once __DIR__ . '/ChatbotModel.php';

class ChatbotController
{
    public function __construct()
    {
        // Rien de spécial ici
    }

    // Afficher la vue du chatbot (plus utilisé, modal intégré)
    public function index()
    {
        // Le chatbot est maintenant un modal intégré dans le dashboard
        \Flight::redirect('/employe/dashboard');
    }    // Traiter le message du chatbot
    public function sendMessage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employe') {
            \Flight::json(['error' => 'Non autorisé']);
            return;
        }

        $message = \Flight::request()->data->message ?? '';
        $message = htmlspecialchars(trim($message)); // Sécurité basique

        if (empty($message)) {
            \Flight::json(['response' => 'Veuillez entrer une question.']);
            return;
        }

        // Utiliser le modèle pour obtenir la réponse
        $chatbotModel = new ChatbotModel(\Flight::db());
        $response = $chatbotModel->getResponse(strtolower($message), $_SESSION['user_id']);

        \Flight::json(['response' => $response]);
    }
}