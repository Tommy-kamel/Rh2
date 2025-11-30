<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Ajouter un employé</title>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* === Variables CSS === */
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #e8ecff;
        --secondary: #667eea;
        --accent: #764ba2;
        --text-dark: #2d3748;
        --text-light: #718096;
        --bg-light: #f7fafc;
        --border: #e2e8f0;
        --success: #48bb78;
        --error: #f56565;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* === Reset et styles de base === */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        
    }

    body {
        /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
        margin: 0;
        padding: 0;
    }

    .app-container {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex: 1;
        margin-left: var(--sidebar-width);
        background: #f8fafc;
    }

    .main-header {
        background: white;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .content-wrapper {
        padding: 2rem;
    }

    .container {
        background: white;
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 0 auto;
    }

    /* === Bouton retour === */
    .btn-light {
        background: white;
        border: 2px solid var(--border);
        color: var(--text-dark);
        font-weight: 500;
        transition: var(--transition);
        border-radius: var(--radius);
        padding: 12px 20px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    .btn-light:hover {
        background: var(--bg-light);
        border-color: var(--primary);
        color: var(--primary);
        transform: translateX(-2px);
    }

    /* === Formulaire === */
    .form-container {
        background: var(--bg-light);
        border-radius: var(--radius);
        padding: 30px;
        border: 1px solid var(--border);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        font-size: 1rem;
        transition: var(--transition);
        background: white;
        font-family: 'Inter', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(32, 65, 211, 0.1);
        background: white;
    }

    .form-control:invalid:not(:focus):not(:placeholder-shown) {
        border-color: var(--error);
    }

    /* === Upload de photo === */
    .photo-upload {
        border: 2px dashed var(--border);
        border-radius: var(--radius);
        padding: 30px;
        text-align: center;
        transition: var(--transition);
        background: white;
        cursor: pointer;
    }

    .photo-upload:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .photo-upload i {
        font-size: 3rem;
        color: var(--text-light);
        margin-bottom: 15px;
        display: block;
    }

    .photo-upload input[type="file"] {
        display: none;
    }

    .photo-preview {
        max-width: 150px;
        max-height: 150px;
        border-radius: var(--radius);
        margin: 15px auto;
        display: none;
        border: 3px solid white;
        box-shadow: var(--shadow);
    }

    /* === Bouton de soumission === */
    .btn-primary {
        background: var(--primary);
        color: white;
        border: none;
        padding: 16px 40px;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 1.1rem;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* === Indicateurs de champ requis === */
    .required::after {
        content: " *";
        color: var(--error);
    }

    /* === Messages d'erreur === */
    .error-message {
        color: var(--error);
        font-size: 0.85rem;
        margin-top: 5px;
        display: none;
    }

    .form-control:invalid:not(:focus):not(:placeholder-shown) + .error-message {
        display: block;
    }

    /* === Section de formulaire === */
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--border);
    }

    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--primary);
    }

    /* === Animation === */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-group {
        animation: fadeInUp 0.5s ease-out;
    }

    .form-group:nth-child(even) {
        animation-delay: 0.1s;
    }

    .form-group:nth-child(odd) {
        animation-delay: 0.2s;
    }

    /* === Responsive === */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        
        .container {
            padding: 20px;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        h2 {
            font-size: 1.8rem;
        }
        
        .form-container {
            padding: 20px;
        }
        
        .photo-upload {
            padding: 20px;
        }
        
        .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }

    /* === États de chargement === */
    .btn-primary.loading {
        position: relative;
        color: transparent;
    }

    .btn-primary.loading::after {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
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
                <i data-feather="user-plus"></i>
                Ajouter un employé
            </h1>
        </header>
        
        <div class="content-wrapper">
            <div class="container">
                <a href="/employes" class="btn btn-light">
                    <i data-feather="arrow-left"></i>
                    Retour à la liste
                </a>

                <form method="POST" action="/employes/ajouter" enctype="multipart/form-data" class="form-container" id="employeeForm">
        <!-- Section Informations personnelles -->
        <div class="form-section">
            <h3 class="section-title">
                <i data-feather="user"></i>
                Informations personnelles
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">Nom</label>
                    <input type="text" name="nom" class="form-control" required placeholder="Entrez le nom">
                    <div class="error-message">Veuillez saisir le nom de l'employé</div>
                </div>

                <div class="form-group">
                    <label class="form-label required">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required placeholder="Entrez le prénom">
                    <div class="error-message">Veuillez saisir le prénom de l'employé</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_naissance" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Sexe</label>
                    <select name="sexe" class="form-control">
                        <option value="">Sélectionnez...</option>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section Coordonnées -->
        <div class="form-section">
            <h3 class="section-title">
                <i data-feather="mail"></i>
                Coordonnées
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@entreprise.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control" placeholder="+261 XX XX XXX XX">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Adresse</label>
                    <textarea name="adresse" class="form-control" rows="3" placeholder="Adresse complète"></textarea>
                </div>
            </div>
        </div>

        <!-- Section Documents -->
        <div class="form-section">
            <h3 class="section-title">
                <i data-feather="file-text"></i>
                Documents d'identité
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">CIN</label>
                    <input type="text" name="cin" class="form-control" placeholder="Numéro de CIN">
                </div>

                <div class="form-group">
                    <label class="form-label">Numéro CNAPS</label>
                    <input type="text" name="numero_cnaps" class="form-control" placeholder="Numéro CNAPS">
                </div>
            </div>
        </div>

        <!-- Section Sécurité -->
        <div class="form-section">
            <h3 class="section-title">
                <i data-feather="lock"></i>
                Accès et sécurité
            </h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe sécurisé">
                    <small style="color: var(--text-light); font-size: 0.85rem; margin-top: 5px; display: block;">
                        Laissez vide pour générer un mot de passe automatiquement
                    </small>
                </div>
            </div>
        </div>

        <!-- Section Photo -->
        <div class="form-section">
            <h3 class="section-title">
                <i data-feather="camera"></i>
                Photo de profil
            </h3>
            
            <div class="form-group full-width">
                <div class="photo-upload" onclick="document.getElementById('photoInput').click()">
                    <i data-feather="upload-cloud"></i>
                    <h4>Cliquez pour uploader une photo</h4>
                    <p class="text-muted">Formats supportés: JPG, PNG, GIF (max. 5MB)</p>
                    <img id="photoPreview" class="photo-preview" alt="Aperçu de la photo">
                    <input type="file" id="photoInput" name="photo" accept="image/*" class="form-control">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i data-feather="user-plus"></i>
            Créer l'employé
        </button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();

    document.querySelectorAll('.has-submenu > .menu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const parent = link.parentElement;
                const submenu = parent.querySelector('.submenu');
                // Fermer tous les autres sous-menus
                document.querySelectorAll('.submenu').forEach(sub => {
                    if (sub !== submenu) {
                        sub.classList.remove('show');
                        sub.parentElement.classList.remove('open');
                    }
                });
                // Toggle le sous-menu actuel
                submenu.classList.toggle('show');
                parent.classList.toggle('open');
            });
        });

    // Gestion de l'aperçu de la photo
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('photoPreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Validation du formulaire
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = 'var(--error)';
            } else {
                field.style.borderColor = 'var(--border)';
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        } else {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = 'Création en cours...';
        }
    });

    // Animation des champs au focus
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });
</script>
</body>
</html>