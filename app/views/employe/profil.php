<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        /* Styles spécifiques pour la page de profil */
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 20px;
        }
        
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f8f9fa;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #6c757d;
            font-size: 48px;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #343a40;
        }
        
        .profile-title {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .profile-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            padding: 15px 20px;
            font-weight: 600;
            color: #495057;
        }
        
        .profile-card .card-body {
            padding: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            color: #6c757d;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-photo {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
<div class="app-container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="main-content">
        <header class="main-header">
            <h1 class="page-title">
                <i data-feather="user"></i>
                Mon Profil
            </h1>
        </header>
        <div class="content-wrapper">
            <div class="profile-container">
                <!-- En-tête du profil avec photo -->
                <div class="profile-header">
                    <div class="profile-photo">
                        <i data-feather="user"></i>
                    </div>
                    <h2 class="profile-name"><?= htmlspecialchars($infos['prenom'] ?? '') ?> <?= htmlspecialchars($infos['nom'] ?? '') ?></h2>
                    <p class="profile-title"><?= htmlspecialchars($infos['nom_departement'] ?? '') ?></p>
                </div>
                
                <!-- Informations personnelles -->
                <div class="profile-card">
                    <div class="card-header">
                        <i data-feather="info" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                        Informations personnelles
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <div class="info-value"><?= htmlspecialchars($infos['email'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Téléphone</span>
                                <div class="info-value"><?= htmlspecialchars($infos['telephone'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date de naissance</span>
                                <div class="info-value"><?= htmlspecialchars($infos['date_naissance'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Sexe</span>
                                <div class="info-value"><?= htmlspecialchars($infos['sexe'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informations professionnelles -->
                <div class="profile-card">
                    <div class="card-header">
                        <i data-feather="briefcase" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                        Informations professionnelles
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Département</span>
                                <div class="info-value"><?= htmlspecialchars($infos['nom_departement'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Numéro CNAPS</span>
                                <div class="info-value"><?= htmlspecialchars($infos['numero_cnaps'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Adresse -->
                <div class="profile-card">
                    <div class="card-header">
                        <i data-feather="map-pin" style="width: 18px; height: 18px; margin-right: 8px;"></i>
                        Adresse
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <span class="info-label">Adresse complète</span>
                            <div class="info-value"><?= htmlspecialchars($infos['adresse'] ?? '') ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
              
            </div>
        </div>
    </main>
</div>
<script>
    feather.replace();
    
    // Script pour gérer le téléchargement de photo de profil
    document.addEventListener('DOMContentLoaded', function() {
        const profilePhoto = document.querySelector('.profile-photo');
        
        profilePhoto.addEventListener('click', function() {
            // Créer un input file caché
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            
            fileInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        // Créer une image pour remplacer l'icône
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.className = 'profile-photo';
                        img.style.objectFit = 'cover';
                        
                        // Remplacer l'élément actuel par l'image
                        profilePhoto.parentNode.replaceChild(img, profilePhoto);
                    }
                    
                    reader.readAsDataURL(e.target.files[0]);
                }
            });
            
            document.body.appendChild(fileInput);
            fileInput.click();
            document.body.removeChild(fileInput);
        });
    });
</script>
</body>
</html>
