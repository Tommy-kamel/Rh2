<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($conversation['sujet']); ?> - Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }

        .conversation-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            height: calc(100vh - 4rem);
            display: flex;
            flex-direction: column;
        }

        .conversation-header {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .conversation-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .conversation-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.75rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .messages-area {
            flex: 1;
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .message {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .message.my-message {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .message-content {
            max-width: 70%;
        }

        .message.my-message .message-content {
            text-align: right;
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .message.my-message .message-header {
            flex-direction: row-reverse;
        }

        .message-sender {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1e293b;
        }

        .message-time {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .message-bubble {
            background: #f1f5f9;
            padding: 0.875rem 1.125rem;
            border-radius: 16px;
            display: inline-block;
            word-wrap: break-word;
        }

        .message.my-message .message-bubble {
            background: #4c6ef5;
            color: white;
        }

        .message-input-area {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .message-input-form {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .message-input-form textarea {
            flex: 1;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.5rem 0.875rem;
            resize: none;
            font-size: 0.875rem;
            min-height: 42px;
        }

        .message-input-form textarea:focus {
            outline: none;
            border-color: #4c6ef5;
        }

        .send-btn {
            background: #4c6ef5;
            color: white;
            border: none;
            padding: 0.5rem 1.125rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .send-btn:hover {
            background: #3b5bdb;
            transform: translateY(-2px);
        }

        .back-btn {
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #4c6ef5;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="conversation-container">
            <a href="/employe/messages" class="back-btn">
                <i data-feather="arrow-left"></i>
                Retour aux messages
            </a>

            <div class="conversation-header">
                <h1><?php echo htmlspecialchars($conversation['sujet']); ?></h1>
                <div class="conversation-meta">
                    <span><i data-feather="calendar" style="width: 14px; height: 14px;"></i> <?php echo date('d/m/Y', strtotime($conversation['created_at'])); ?></span>
                    <span><i data-feather="user" style="width: 14px; height: 14px;"></i> Conversation avec le RH</span>
                </div>
            </div>

            <div class="messages-area" id="messagesArea">
                <?php foreach ($messages as $msg): 
                    $is_my_message = $msg['sender_type'] === 'employe';
                    $avatar_letter = strtoupper(substr($msg['sender_name'] ?? 'U', 0, 1));
                ?>
                    <div class="message <?php echo $is_my_message ? 'my-message' : ''; ?>">
                        <div class="message-avatar">
                            <?php echo $avatar_letter; ?>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender"><?php echo htmlspecialchars($msg['sender_name'] ?? 'Utilisateur'); ?></span>
                                <span class="message-time"><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></span>
                            </div>
                            <div class="message-bubble">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="message-input-area">
                <form class="message-input-form" id="messageForm" onsubmit="sendMessage(event)">
                    <textarea name="message" id="messageInput" rows="1" placeholder="Écrivez votre message..." required></textarea>
                    <button type="submit" class="send-btn">
                        <i data-feather="send" style="width: 16px; height: 16px;"></i>
                        Envoyer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();

        // Scroller vers le bas au chargement
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.scrollTop = messagesArea.scrollHeight;

        function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) return;

            const formData = new FormData();
            formData.append('message', message);

            fetch('/employe/messages/<?php echo $conversation['id_conversation']; ?>/send', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Impossible d\'envoyer le message'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            });
        }
    </script>
</body>
</html>