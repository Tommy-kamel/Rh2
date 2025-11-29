<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant IA RH - Admin</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link rel="stylesheet" href="/assets/css/rh-dashboard.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .chatbot-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            padding: 20px;
            height: calc(100vh - 150px);
        }
        
        .features-sidebar {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .features-sidebar h3 {
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
            font-weight: 600;
        }
        
        .feature-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border: none;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin-bottom: 8px;
            color: #495057;
            font-weight: 500;
            font-size: 14px;
            text-align: left;
        }
        
        .feature-btn:hover {
            background: #e9ecef;
            transform: translateX(4px);
        }
        
        .feature-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .feature-btn i {
            width: 18px;
            height: 18px;
        }
        
        .features-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .tab-btn {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            color: #666;
            font-weight: 500;
        }
        
        .tab-btn:hover {
            color: #4CAF50;
            background: #f5f5f5;
        }
        
        .tab-btn.active {
            color: #4CAF50;
            border-bottom-color: #4CAF50;
        }
        
        .tab-btn i {
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }
        
        .content-area {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            height: 100%;
        }
        
        .main-panels-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px 20px;
            background: #f5f7fa;
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 0;
        }
        
        .message {
            display: flex;
            margin-bottom: 0;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message.user {
            justify-content: flex-end;
        }
        
        .message.bot {
            justify-content: flex-start;
        }
        
        .message-content {
            max-width: 75%;
            padding: 14px 18px;
            border-radius: 8px;
            word-wrap: break-word;
            line-height: 1.5;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        .message.bot .message-content {
            background: #ffffff;
            color: #2c3e50;
            border: 1px solid #e1e8ed;
            border-bottom-left-radius: 2px;
        }
        
        .message.user .message-content {
            background: #2c3e50;
            color: #ffffff;
            border-bottom-right-radius: 2px;
        }
        
        .chat-input-area {
            padding: 20px;
            border-top: 1px solid #e1e8ed;
            background: #ffffff;
        }
        
        .input-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        .input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #d1d9e0;
            border-radius: 6px;
            font-size: 14px;
            background: #ffffff;
            color: #2c3e50;
            transition: all 0.2s ease;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #4a5568;
            box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.1);
        }
        
        .input-group input::placeholder {
            color: #95a5a6;
        }
        
        .input-group button {
            padding: 12px 24px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .input-group button:hover {
            background: #34495e;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.2);
        }
        
        .input-group button:active {
            transform: translateY(0);
        }
        
        .feature-panel {
            display: none;
            height: 100%;
            overflow: hidden;
        }
        
        .feature-panel.active {
            display: flex;
            flex-direction: column;
        }
        
        .feature-panel#panel-chatbot {
            padding: 0;
            background: #ffffff;
            overflow: hidden;
        }
        
        .feature-panel#panel-chatbot.active {
            display: flex;
        }
        
        .feature-panel#panel-documents,
        .feature-panel#panel-turnover,
        .feature-panel#panel-anomalies,
        .feature-panel#panel-candidates,
        .feature-panel#panel-analyze-cv {
            background: #ffffff;
            overflow-y: auto;
            padding: 0;
        }
        
        .feature-panel .chat-header {
            flex-shrink: 0;
        }
        
        .feature-panel > div:not(.chat-header):not(.chat-messages):not(.chat-input-area) {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #45a049;
        }
        
        .results-container {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .anomaly-item,
        .candidate-item {
            padding: 15px;
            margin-bottom: 10px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #4CAF50;
        }
        
        .anomaly-item.high {
            border-left-color: #f44336;
        }
        
        .anomaly-item.medium {
            border-left-color: #ff9800;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-success {
            background: #4CAF50;
            color: white;
        }
        
        .badge-warning {
            background: #ff9800;
            color: white;
        }
        
        .badge-danger {
            background: #f44336;
            color: white;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4CAF50;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header">
                <h1 class="page-title">
                    <i data-feather="cpu"></i>
                    Assistant IA RH
                </h1>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($user_name ?? 'Admin') ?></span>
                </div>
            </header>
            
            <div class="content-wrapper">
                <div class="chatbot-container">
                    <!-- Sidebar des fonctionnalités -->
                    <div class="features-sidebar">
                        <h3 style="margin-bottom: 20px; font-size: 16px; color: #666;">Fonctionnalités IA</h3>
                        
                        <button class="feature-btn active" data-feature="chatbot">
                            <i data-feather="message-circle"></i>
                            <span>Chatbot FAQ</span>
                        </button>
                        
                        <button class="feature-btn" data-feature="documents">
                            <i data-feather="file-text"></i>
                            <span>Générer Documents</span>
                        </button>
                        
                        <button class="feature-btn" data-feature="turnover">
                            <i data-feather="trending-up"></i>
                            <span>Prédiction Turnover</span>
                        </button>
                        
                        <button class="feature-btn" data-feature="anomalies">
                            <i data-feather="alert-triangle"></i>
                            <span>Détection Anomalies</span>
                        </button>
                        
                        <button class="feature-btn" data-feature="candidates">
                            <i data-feather="users"></i>
                            <span>Recommandation CV</span>
                        </button>
                        
                        <button class="feature-btn" data-feature="analyze-cv">
                            <i data-feather="upload"></i>
                            <span>Analyser CV</span>
                        </button>
                    </div>
                    
                    <!-- Zone principale -->
                    <div class="main-panels-container">
                        <!-- Panel Chatbot FAQ -->
                        <div class="feature-panel active" id="panel-chatbot">
                            <div class="chat-header">
                                <h2 style="margin: 0; font-size: 20px;">Chatbot RH - Questions Fréquentes</h2>
                                <p style="margin: 5px 0 0; opacity: 0.9; font-size: 14px;">Posez vos questions sur les congés, paie, pointage, etc.</p>
                            </div>
                            
                            <div class="chat-messages" id="chatMessages">
                                <div class="message bot">
                                    <div class="message-content">
                                        Bonjour ! Je suis votre assistant RH intelligent. Comment puis-je vous aider aujourd'hui ?
                                    </div>
                                </div>
                            </div>
                            
                            <div class="chat-input-area">
                                <div class="input-group">
                                    <input type="text" id="chatInput" placeholder="Posez votre question..." />
                                    <button onclick="sendChatMessage()">
                                        <i data-feather="send"></i>
                                        Envoyer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Panel Génération Documents -->
                        <div class="feature-panel" id="panel-documents">
                            <div class="chat-header">
                                <h2 style="margin: 0; font-size: 20px;">Génération Automatique de Documents RH</h2>
                            </div>
                            <div style="padding: 20px;">
                                <div class="form-group">
                                    <label>Type de document</label>
                                    <select id="docType" class="form-control">
                                        <option value="contrat">Contrat de travail</option>
                                        <option value="attestation">Attestation de travail</option>
                                        <option value="fiche_paie">Fiche de paie</option>
                                        <option value="certificat">Certificat</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Employé</label>
                                    <input type="text" id="docEmployeSearch" class="form-control" list="employesList1" placeholder="Rechercher par nom, prénom ou ID..." />
                                    <datalist id="employesList1"></datalist>
                                    <input type="hidden" id="docEmployeId" />
                                </div>
                                
                                <button class="btn-primary" onclick="generateDocument()">Générer Document</button>
                                
                                <div id="docResults" class="results-container" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <!-- Panel Prédiction Turnover -->
                        <div class="feature-panel" id="panel-turnover">
                            <div class="chat-header">
                                <h2 style="margin: 0; font-size: 20px;">Prédiction de Turnover</h2>
                            </div>
                            <div style="padding: 20px;">
                                <div class="form-group">
                                    <label>Analyse</label>
                                    <select id="turnoverType" class="form-control">
                                        <option value="global">Analyse globale (tous les employés)</option>
                                        <option value="specific">Employé spécifique</option>
                                    </select>
                                </div>
                                
                                <div class="form-group" id="turnoverEmployeGroup" style="display: none;">
                                    <label>Employé</label>
                                    <input type="text" id="turnoverEmployeSearch" class="form-control" list="employesList2" placeholder="Rechercher par nom, prénom ou ID..." />
                                    <datalist id="employesList2"></datalist>
                                    <input type="hidden" id="turnoverEmployeId" />
                                </div>
                                
                                <button class="btn-primary" onclick="predictTurnover()">Analyser</button>
                                
                                <div id="turnoverResults" class="results-container" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <!-- Panel Détection Anomalies -->
                        <div class="feature-panel" id="panel-anomalies">
                            <div class="chat-header">
                                <h2 style="margin: 0; font-size: 20px;">Détection d'Anomalies</h2>
                            </div>
                            <div style="padding: 20px;">
                                <div class="form-group">
                                    <label>Type d'anomalies</label>
                                    <select id="anomalyType" class="form-control">
                                        <option value="all">Toutes</option>
                                        <option value="hours">Heures de travail</option>
                                        <option value="salary">Paie</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Période</label>
                                    <select id="anomalyPeriod" class="form-control">
                                        <option value="week">Dernière semaine</option>
                                        <option value="month">Dernier mois</option>
                                        <option value="year">Dernière année</option>
                                    </select>
                                </div>
                                
                                <button class="btn-primary" onclick="detectAnomalies()">Détecter</button>
                                
                                <div id="anomalyResults" class="results-container" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <!-- Panel Recommandation Candidats -->
                        <div class="feature-panel" id="panel-candidates">
                            <div class="chat-header">
                                <h2 style="margin: 0; font-size: 20px;">Recommandation de Candidats</h2>
                            </div>
                            <div style="padding: 20px;">
                                <div class="form-group">
                                    <label>Poste (optionnel)</label>
                                    <select id="candidatePosteId" class="form-control">
                                        <option value="">-- Sélectionner un poste --</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Description du poste</label>
                                    <textarea id="candidateJobDesc" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Compétences requises (séparées par des virgules)</label>
                                    <input type="text" id="candidateSkills" class="form-control" placeholder="Java, PHP, MySQL..." />
                                </div>
                                
                                <button class="btn-primary" onclick="recommendCandidates()">Rechercher</button>
                                
                                <div id="candidateResults" class="results-container" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <!-- Panel Analyser CV -->
                        <div class="feature-panel" id="panel-analyze-cv">
                            <div class="chat-header">
                                <h2 style="margin: 0; font-size: 20px;">Analyser un CV</h2>
                            </div>
                            <div style="padding: 20px;">
                                <div class="form-group">
                                    <label>Uploader un CV (PDF, DOC, DOCX)</label>
                                    <input type="file" id="cvFile" class="form-control" accept=".pdf,.doc,.docx" />
                                </div>
                                
                                <button class="btn-primary" onclick="analyzeCV()">Analyser</button>
                                
                                <div id="cvResults" class="results-container" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="/css/bootstrap.bundle.min.js"></script>
    <script>
        feather.replace();
        
        // Charger la liste des postes au chargement de la page
        loadPostes();
        loadEmployes();
        
        function loadPostes() {
            fetch('/admin/chatbot/statistics')
            .then(r => r.json())
            .then(data => {
                // Cette requête pourrait être améliorée avec un endpoint dédié
                // Pour l'instant, on fait une requête simple pour récupérer les postes
            })
            .catch(err => console.error('Erreur chargement postes'));
            
            // Requête directe pour obtenir les postes
            fetch('/admin/chatbot/get-postes', {
                method: 'GET',
                headers: {'Content-Type': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('candidatePosteId');
                if (data.success && data.postes) {
                    data.postes.forEach(poste => {
                        const option = document.createElement('option');
                        option.value = poste.id_poste;
                        option.textContent = poste.nom;
                        select.appendChild(option);
                    });
                }
            })
            .catch(err => console.error('Erreur chargement postes:', err));
        }
        
        function loadEmployes() {
            fetch('/admin/chatbot/get-employes', {
                method: 'GET',
                headers: {'Content-Type': 'application/json'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.employes) {
                    // Remplir les deux datalists
                    const datalist1 = document.getElementById('employesList1');
                    const datalist2 = document.getElementById('employesList2');
                    
                    data.employes.forEach(emp => {
                        const displayText = `${emp.id_employe} - ${emp.nom} ${emp.prenom}`;
                        
                        const option1 = document.createElement('option');
                        option1.value = displayText;
                        option1.setAttribute('data-id', emp.id_employe);
                        datalist1.appendChild(option1);
                        
                        const option2 = document.createElement('option');
                        option2.value = displayText;
                        option2.setAttribute('data-id', emp.id_employe);
                        datalist2.appendChild(option2);
                    });
                }
            })
            .catch(err => console.error('Erreur chargement employés:', err));
        }
        
        // Gérer la sélection d'employé dans le champ de génération de documents
        document.getElementById('docEmployeSearch').addEventListener('input', function() {
            const value = this.value;
            const match = value.match(/^(\d+)\s-/);
            if (match) {
                document.getElementById('docEmployeId').value = match[1];
            }
        });
        
        // Gérer la sélection d'employé dans le champ de turnover
        document.getElementById('turnoverEmployeSearch').addEventListener('input', function() {
            const value = this.value;
            const match = value.match(/^(\d+)\s-/);
            if (match) {
                document.getElementById('turnoverEmployeId').value = match[1];
            }
        });
        
        // Gestion des onglets
        document.querySelectorAll('.feature-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Retirer active de tous
                document.querySelectorAll('.feature-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.feature-panel').forEach(p => p.classList.remove('active'));
                
                // Activer le sélectionné
                this.classList.add('active');
                const feature = this.dataset.feature;
                document.getElementById('panel-' + feature).classList.add('active');
            });
        });
        
        // Afficher/masquer champ employé pour turnover
        document.getElementById('turnoverType').addEventListener('change', function() {
            const group = document.getElementById('turnoverEmployeGroup');
            group.style.display = this.value === 'specific' ? 'block' : 'none';
        });
        
        // Chatbot
        function sendChatMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Afficher message utilisateur
            addMessage(message, 'user');
            input.value = '';
            
            // Envoyer au serveur
            fetch('/admin/chatbot/message', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({message})
            })
            .then(r => r.json())
            .then(data => {
                addMessage(data.response || data.error, 'bot');
            })
            .catch(err => {
                addMessage('Erreur de connexion', 'bot');
            });
        }
        
        function addMessage(text, type) {
            const container = document.getElementById('chatMessages');
            const div = document.createElement('div');
            div.className = 'message ' + type;
            div.innerHTML = `<div class="message-content">${text}</div>`;
            container.appendChild(div);
            container.scrollTop = container.scrollHeight;
        }
        
        // Enter pour envoyer
        document.getElementById('chatInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendChatMessage();
        });
        
        // Génération de documents
        function generateDocument() {
            const type = document.getElementById('docType').value;
            const employeId = document.getElementById('docEmployeId').value;
            
            if (!employeId) {
                alert('Veuillez saisir un ID employé');
                return;
            }
            
            showLoading('docResults');
            
            fetch('/admin/chatbot/generate-document', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({type, employe_id: employeId, params: {}})
            })
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('docResults');
                if (data.success) {
                    container.innerHTML = `<div class="alert alert-success">Document généré avec succès !</div>`;
                } else {
                    container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                }
            });
        }
        
        // Prédiction turnover
        function predictTurnover() {
            const type = document.getElementById('turnoverType').value;
            const employeId = type === 'specific' ? document.getElementById('turnoverEmployeId').value : null;
            
            showLoading('turnoverResults');
            
            fetch('/admin/chatbot/predict-turnover', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({employe_id: employeId})
            })
            .then(r => r.json())
            .then(data => {
                displayTurnoverResults(data);
            });
        }
        
        function displayTurnoverResults(data) {
            const container = document.getElementById('turnoverResults');
            
            if (data.error) {
                container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            if (data.data.employe) {
                // Résultat individuel
                const d = data.data;
                container.innerHTML = `
                    <h4>${d.employe}</h4>
                    <p><strong>Score de risque:</strong> ${d.score}/100</p>
                    <p><strong>Niveau:</strong> <span class="badge badge-${d.risk_level === 'Élevé' ? 'danger' : d.risk_level === 'Moyen' ? 'warning' : 'success'}">${d.risk_level}</span></p>
                    <p><strong>Facteurs:</strong></p>
                    <ul>${d.factors.map(f => `<li>${f}</li>`).join('')}</ul>
                    <p><strong>Recommandation:</strong> ${d.recommendation}</p>
                `;
            } else {
                // Résultat global
                const d = data.data;
                let html = `<h4>Analyse Globale - ${d.total_employes} employés</h4>`;
                
                html += `<div class="anomaly-item high">
                    <strong>Risque Élevé:</strong> ${d.high_risk.count} (${d.high_risk.percentage}%)
                </div>`;
                
                html += `<div class="anomaly-item medium">
                    <strong>Risque Moyen:</strong> ${d.medium_risk.count} (${d.medium_risk.percentage}%)
                </div>`;
                
                html += `<div class="anomaly-item">
                    <strong>Risque Faible:</strong> ${d.low_risk.count} (${d.low_risk.percentage}%)
                </div>`;
                
                container.innerHTML = html;
            }
        }
        
        // Détection anomalies
        function detectAnomalies() {
            const type = document.getElementById('anomalyType').value;
            const period = document.getElementById('anomalyPeriod').value;
            
            showLoading('anomalyResults');
            
            fetch('/admin/chatbot/detect-anomalies', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({type, period})
            })
            .then(r => r.json())
            .then(data => {
                displayAnomalyResults(data);
            });
        }
        
        function displayAnomalyResults(data) {
            const container = document.getElementById('anomalyResults');
            
            if (data.error) {
                container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            let html = '<h4>Anomalies Détectées</h4>';
            
            const anomalies = data.data;
            let totalCount = 0;
            
            Object.keys(anomalies).forEach(category => {
                const items = anomalies[category];
                if (items && items.length > 0) {
                    items.forEach(item => {
                        totalCount += item.count;
                        html += `
                            <div class="anomaly-item ${item.severity}">
                                <strong>${item.type}</strong>
                                <span class="badge badge-${item.severity === 'high' ? 'danger' : 'warning'}">${item.count}</span>
                                <p style="margin: 5px 0 0; font-size: 13px;">Détails disponibles dans les données</p>
                            </div>
                        `;
                    });
                }
            });
            
            if (totalCount === 0) {
                html += '<p class="alert alert-success">Aucune anomalie détectée !</p>';
            }
            
            container.innerHTML = html;
        }
        
        // Recommandation candidats
        function recommendCandidates() {
            const posteId = document.getElementById('candidatePosteId').value || null;
            const jobDesc = document.getElementById('candidateJobDesc').value;
            const skills = document.getElementById('candidateSkills').value.split(',').map(s => s.trim()).filter(s => s);
            
            showLoading('candidateResults');
            
            fetch('/admin/chatbot/recommend-candidates', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({poste_id: posteId, job_description: jobDesc, skills})
            })
            .then(r => r.json())
            .then(data => {
                displayCandidateResults(data);
            });
        }
        
        function displayCandidateResults(data) {
            const container = document.getElementById('candidateResults');
            
            if (data.error) {
                container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                return;
            }
            
            let html = '<h4>Candidats Recommandés</h4>';
            
            data.data.forEach((item, index) => {
                const c = item.candidate;
                html += `
                    <div class="candidate-item">
                        <h5>${index + 1}. ${c.nom} ${c.prenom}</h5>
                        <p><strong>Score:</strong> ${item.score}/100 
                        <span class="badge badge-${item.match_level === 'Excellent' ? 'success' : item.match_level === 'Bon' ? 'warning' : 'danger'}">${item.match_level}</span></p>
                        <p><strong>Justification:</strong> ${item.justification || item.factors.join(', ')}</p>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }
        
        // Analyser CV
        function analyzeCV() {
            const fileInput = document.getElementById('cvFile');
            
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Veuillez sélectionner un fichier');
                return;
            }
            
            showLoading('cvResults');
            
            const formData = new FormData();
            formData.append('cv', fileInput.files[0]);
            
            fetch('/admin/chatbot/analyze-cv', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('cvResults');
                if (data.success) {
                    const d = data.data;
                    let html = `
                        <h4>Analyse du CV</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nom:</strong> ${d.nom_complet}</p>
                                <p><strong>Email:</strong> ${d.email}</p>
                                <p><strong>Téléphone:</strong> ${d.telephone}</p>
                                <p><strong>Poste visé:</strong> ${d.poste_vise || 'Non spécifié'}</p>
                                <p><strong>Score global:</strong> <span class="badge badge-${d.score_global >= 70 ? 'success' : d.score_global >= 40 ? 'warning' : 'danger'}">${d.score_global}/100</span></p>
                            </div>
                            <div class="col-md-6">`;
                    
                    if (d.competences && d.competences.length > 0) {
                        html += `<p><strong>Compétences (${d.competences.length}):</strong></p>
                                <ul class="list-unstyled">`;
                        d.competences.slice(0, 10).forEach(skill => {
                            html += `<li><i data-feather="check-circle" class="text-success"></i> ${skill}</li>`;
                        });
                        if (d.competences.length > 10) {
                            html += `<li><em>+ ${d.competences.length - 10} autres...</em></li>`;
                        }
                        html += `</ul>`;
                    }
                    
                    if (d.langues && d.langues.length > 0) {
                        html += `<p><strong>Langues:</strong> ${d.langues.join(', ')}</p>`;
                    }
                    
                    html += `</div></div>`;
                    
                    if (d.formation && d.formation.length > 0) {
                        html += `<div class="mt-3">
                                    <p><strong>Formation (${d.formation.length}):</strong></p>
                                    <ul>`;
                        d.formation.forEach(edu => {
                            html += `<li>${edu}</li>`;
                        });
                        html += `</ul></div>`;
                    }
                    
                    if (d.experience && d.experience.length > 0) {
                        html += `<div class="mt-3">
                                    <p><strong>Expérience professionnelle (${d.experience.length}):</strong></p>
                                    <ul>`;
                        d.experience.forEach(exp => {
                            html += `<li>${exp}</li>`;
                        });
                        html += `</ul></div>`;
                    }
                    
                    container.innerHTML = html;
                    feather.replace();
                } else {
                    container.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                }
            });
        }
        
        function showLoading(containerId) {
            const container = document.getElementById(containerId);
            container.style.display = 'block';
            container.innerHTML = '<div class="loading"><div class="spinner"></div><p>Analyse en cours...</p></div>';
        }
    </script>
</body>
</html>
