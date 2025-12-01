<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système RH</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i data-feather="shield" class="login-icon"></i>
                <h1>Système de Gestion RH</h1>
                <p>Connectez-vous à votre compte</p>
            </div>
            
            <!-- Tabs pour choisir le type de connexion -->
            <div class="login-tabs">
                <button class="tab-btn active" data-tab="admin">
                    <i data-feather="briefcase"></i>
                    <span>Administrateur</span>
                </button>
                <button class="tab-btn" data-tab="employe">
                    <i data-feather="user"></i>
                    <span>Employé</span>
                </button>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i data-feather="alert-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulaire Admin -->
            <form action="/login/admin" method="POST" class="login-form active" id="admin-form">
                <div class="form-group">
                    <label for="admin-username">
                        <i data-feather="user"></i>
                        Nom d'utilisateur
                    </label>
                    <input 
                        type="text" 
                        id="admin-username" 
                        name="nom_utilisateur" 
                        class="form-control" 
                        placeholder="Entrez votre nom d'utilisateur"
                        value="rh_user"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="admin-password">
                        <i data-feather="lock"></i>
                        Mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="admin-password" 
                        name="mot_de_passe" 
                        class="form-control" 
                        placeholder="Entrez votre mot de passe"
                        value="RH"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-feather="log-in"></i>
                    Se connecter
                </button>
            </form>
            
            <!-- Formulaire Employé -->
            <form action="/login/employe" method="POST" class="login-form" id="employe-form">
                <div class="form-group">
                    <label for="employe-email">
                        <i data-feather="mail"></i>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="employe-email" 
                        name="email" 
                        class="form-control" 
                        placeholder="Entrez votre email"
                        value="marie.rabe@email.com"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="employe-password">
                        <i data-feather="lock"></i>
                        Mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="employe-password" 
                        name="mot_de_passe" 
                        class="form-control" 
                        placeholder="Entrez votre mot de passe"
                        value="123"
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-feather="log-in"></i>
                    Se connecter
                </button>
            </form>
        </div>
    </div>
    
    <script>
        // Initialiser Feather Icons
        feather.replace();
        
        // Gestion des tabs
        const tabBtns = document.querySelectorAll('.tab-btn');
        const forms = document.querySelectorAll('.login-form');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                
                // Retirer active de tous les tabs
                tabBtns.forEach(b => b.classList.remove('active'));
                forms.forEach(f => f.classList.remove('active'));
                
                // Ajouter active au tab cliqué
                btn.classList.add('active');
                document.getElementById(tab + '-form').classList.add('active');
            });
        });
    </script>
</body>
</html>
