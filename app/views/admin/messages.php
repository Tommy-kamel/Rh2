<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie RH - RH2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
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

        .filter-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            background: white;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: #4c6ef5;
            color: white;
            border-color: #4c6ef5;
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
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .conversation-info h5 {
            font-weight: 600;
            font-size: 1.125rem;
            color: #1e293b;
            margin: 0 0 0.25rem 0;
        }

        .conversation-employee {
            font-size: 0.875rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .conversation-date {
            font-size: 0.875rem;
            color: #64748b;
        }

        .conversation-preview {
            color: #64748b;
            font-size: 0.9375rem;
            margin-bottom: 0.75rem;
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

        .assigned-rh {
            font-size: 0.8125rem;
            color: #6366f1;
            display: flex;
            align-items: center;
            gap: 0.375rem;
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
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="messages-container">
            <h1 class="mb-4">Messagerie RH</h1>

            <div class="filter-tabs">
                <button class="filter-btn active" onclick="filterConversations('all')">
                    Toutes (<?php echo count($conversations); ?>)
                </button>
                <button class="filter-btn" onclick="filterConversations('ouvert')">
                    En attente (<?php echo count(array_filter($conversations, fn($c) => $c['status'] === 'ouvert')); ?>)
                </button>
                <button class="filter-btn" onclick="filterConversations('en_cours')">
                    En cours (<?php echo count(array_filter($conversations, fn($c) => $c['status'] === 'en_cours')); ?>)
                </button>
                <button class="filter-btn" onclick="filterConversations('ferme')">
                    Fermées (<?php echo count(array_filter($conversations, fn($c) => $c['status'] === 'ferme')); ?>)
                </button>
            </div>

            <div id="conversationsContainer">
                <?php if (empty($conversations)): ?>
                    <div class="empty-state">
                        <i data-feather="inbox"></i>
                        <h3>Aucune conversation</h3>
                        <p>Il n'y a aucune conversation pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <div class="conversation-card" data-status="<?php echo $conv['status']; ?>" onclick="window.location.href='/admin/messages/<?php echo $conv['id_conversation']; ?>'">
                            <div class="conversation-header">
                                <div class="conversation-info">
                                    <h5><?php echo htmlspecialchars($conv['sujet']); ?></h5>
                                    <div class="conversation-employee">
                                        <i data-feather="user" style="width: 14px; height: 14px;"></i>
                                        <?php echo htmlspecialchars($conv['nom'] . ' ' . $conv['prenom']); ?>
                                    </div>
                                </div>
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
                                <?php if (isset($conv['rh_name']) && $conv['rh_name']): ?>
                                    <span class="assigned-rh">
                                        <i data-feather="user-check" style="width: 14px; height: 14px;"></i>
                                        <?php echo htmlspecialchars($conv['rh_name']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();

        function filterConversations(status) {
            const cards = document.querySelectorAll('.conversation-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>