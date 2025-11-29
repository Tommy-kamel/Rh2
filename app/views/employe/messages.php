<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Messages - RH2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }
        .messages-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .conversation-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .conversation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: #4c6ef5;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .conversation-title {
            font-weight: 600;
            font-size: 1.125rem;
            color: #1e293b;
        }

        .conversation-date {
            font-size: 0.875rem;
            color: #64748b;
        }

        .conversation-preview {
            color: #64748b;
            font-size: 0.9375rem;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .conversation-footer {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .status-ouvert {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .status-en_cours {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .status-ferme {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .unread-badge {
            background: #4c6ef5;
            color: white;
            padding: 0.25rem 0.625rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .new-conversation-btn {
            background: #4c6ef5;
            color: white;
            border: none;
            padding: 0.875rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .new-conversation-btn:hover {
            background: #3b5bdb;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 110, 245, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #64748b;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="messages-container">
            <div class="d-flex justify-content-between align-items-center mb-4" style="gap: 2rem;">
                <h1>Mes Messages</h1>
                <button class="new-conversation-btn" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                    <i data-feather="plus"></i>
                    Nouveau message
                </button>
            </div>

            <?php if (empty($conversations)): ?>
                <div class="empty-state">
                    <i data-feather="inbox"></i>
                    <h3>Aucune conversation</h3>
                    <p>Vous n'avez pas encore de messages. Commencez une conversation avec le RH.</p>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                    <div class="conversation-card" onclick="window.location.href='/employe/messages/<?php echo $conv['id_conversation']; ?>'">
                        <div class="conversation-header">
                            <h5 class="conversation-title"><?php echo htmlspecialchars($conv['sujet']); ?></h5>
                            <span class="conversation-date">
                                <?php echo $conv['dernier_message_date'] ? date('d/m/Y H:i', strtotime($conv['dernier_message_date'])) : date('d/m/Y', strtotime($conv['created_at'])); ?>
                            </span>
                        </div>
                        
                        <?php if ($conv['dernier_message']): ?>
                            <div class="conversation-preview">
                                <?php echo htmlspecialchars(substr($conv['dernier_message'], 0, 150)); ?>...
                            </div>
                        <?php endif; ?>

                        <div class="conversation-footer">
                            <span class="status-badge status-<?php echo $conv['status']; ?>">
                                <?php 
                                    $status_labels = [
                                        'ouvert' => 'En attente',
                                        'en_cours' => 'En cours',
                                        'ferme' => 'Fermé'
                                    ];
                                    echo $status_labels[$conv['status']];
                                ?>
                            </span>
                            <?php if ($conv['unread_count'] > 0): ?>
                                <span class="unread-badge">
                                    <?php echo $conv['unread_count']; ?> nouveau<?php echo $conv['unread_count'] > 1 ? 'x' : ''; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Nouvelle Conversation -->
    <div class="modal fade" id="newConversationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau message au RH</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="newConversationForm">
                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="sujet" name="sujet" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="createConversation()">Envoyer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();

        function createConversation() {
            const form = document.getElementById('newConversationForm');
            const formData = new FormData(form);

            fetch('/employe/messages/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/employe/messages/' + data.id_conversation;
                } else {
                    alert('Erreur: ' + data.message);
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