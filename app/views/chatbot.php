<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chatbot RH</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Base */
        * {
            font-family: 'Outfit', sans-serif;
        }

        /* Modal shell */
        .chatbot-modal .modal-dialog {
            max-width: 520px;
            margin: 28px auto;
        }
        .chatbot-modal .modal-content {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 48px rgba(0,0,0,0.25);
        }

        /* Header */
        .chatbot-modal .modal-header {
            background: linear-gradient(135deg, #343a40, #212529);
            color: #fff;
            border: 0;
            padding: 18px 22px;
        }
        .chatbot-modal .modal-header .modal-title { font-weight: 600; }
        .chatbot-modal .modal-header .btn-close { filter: invert(1); opacity: .8; }
        .chatbot-modal .modal-header .btn-close:hover { opacity: 1; }

        /* Body */
        .chatbot-body {
            height: 420px;
            overflow-y: auto;
            padding: 20px;
            background: linear-gradient(180deg, #f8f9fa 0%, #eef1f4 100%);
        }

        /* Custom scrollbars */
        .chatbot-body::-webkit-scrollbar { width: 8px; }
        .chatbot-body::-webkit-scrollbar-track { background: #f1f3f5; }
        .chatbot-body::-webkit-scrollbar-thumb { background: #c5ced6; border-radius: 4px; }
        .chatbot-body { scrollbar-width: thin; scrollbar-color: #c5ced6 #f1f3f5; }

        /* Messages */
        .message {
            margin: 10px 0;
            padding: 12px 16px;
            border-radius: 18px;
            max-width: 80%;
            line-height: 1.45;
            font-size: 14px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            animation: slideIn .35s ease-out;
            word-break: break-word;
        }
        .message.bot {
            background: #ffffff;
            color: #495057;
            border: 1px solid #e9ecef;
            margin-right: auto;
        }
        .message.user {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #fff;
            margin-left: auto;
        }
        /* Subtle tails */
        .message.bot::after, .message.user::after {
            content: "";
            position: absolute;
            bottom: -2px;
            width: 10px; height: 10px;
            transform: rotate(45deg);
        }
        .message.bot::after { left: 10px; background: #ffffff; border-left: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef; }
        .message.user::after { right: 10px; background: #0b5ed7; }

        /* Suggestions as chips */
        .suggestions { margin-top: 18px; display: flex; flex-wrap: wrap; gap: 8px; }
        .suggestion-btn {
            padding: 8px 12px;
            background: #f1f3f5;
            border: 1px solid #e9ecef;
            border-radius: 999px;
            cursor: pointer;
            font-size: 13px;
            color: #343a40;
            transition: all .2s ease;
        }
        .suggestion-btn:hover { background: #e9ecef; transform: translateY(-1px); }

        /* Footer */
        .chatbot-modal .modal-footer {
            border: 0;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 18px;
        }
        .chatbot-input {
            flex: 1 1 auto;
            padding: 12px 16px;
            border: 2px solid #dee2e6;
            border-radius: 28px;
            outline: none;
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .chatbot-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13,110,253,.12); }
        .chatbot-modal .modal-footer .btn.btn-primary {
            padding: 10px 16px;
            border-radius: 24px;
            box-shadow: 0 4px 12px rgba(13,110,253,.25);
        }

        /* Animation */
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div id="chatbotModal" class="modal chatbot-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chatbot RH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="chatbot-body" id="chatBody">
                        <div class="message bot">Salut ! Je suis votre assistant RH virtuel. Comment puis-je vous aider aujourd'hui ?</div>
                    </div>
                    <div class="suggestions">
                        <button class="suggestion-btn" onclick="fillSuggestion('Combien de congés me restent-il ?')">Combien de congés me restent-il ?</button>
                        <button class="suggestion-btn" onclick="fillSuggestion('Quel est mon salaire ?')">Quel est mon salaire ?</button>
                        <button class="suggestion-btn" onclick="fillSuggestion('Comment demander un congé ?')">Comment demander un congé ?</button>
                        <button class="suggestion-btn" onclick="fillSuggestion('Comment pointer ma présence ?')">Comment pointer ma présence ?</button>
                        <button class="suggestion-btn" onclick="fillSuggestion('Quand est versée ma paie ?')">Quand est versée ma paie ?</button>
                        <button class="suggestion-btn" onclick="fillSuggestion('Mes informations personnelles')">Mes informations personnelles</button>
                        <button class="suggestion-btn" onclick="fillSuggestion('Aide')">Aide</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="text" id="messageInput" class="chatbot-input" placeholder="Votre question..." onkeypress="sendOnEnter(event)">
                    <button onclick="sendMessage()" class="btn btn-primary">Envoyer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillSuggestion(question) {
            document.getElementById('messageInput').value = question;
        }

        function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) return;

            addMessage('user', message);
            input.value = '';

            fetch('/chatbot/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                addMessage('bot', data.response || 'Erreur');
            })
            .catch(() => addMessage('bot', 'Erreur de connexion'));
        }

        function sendOnEnter(event) {
            if (event.key === 'Enter') sendMessage();
        }

        function addMessage(type, text) {
            const body = document.getElementById('chatBody');
            const msg = document.createElement('div');
            msg.className = 'message ' + type;
            msg.textContent = text;
            body.appendChild(msg);
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>