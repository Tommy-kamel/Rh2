<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Documents - <?= htmlspecialchars($employee['prenom'].' '.$employee['nom']) ?></title>
<link rel="stylesheet" href="/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
<style>
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
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* === Titres === */
h2 {
    color: #1a1a2e;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 30px;
    border-bottom: 3px solid #2874f0;
    padding-bottom: 15px;
}

/* === Bouton retour === */
.btn-light {
    background: white;
    color: #1a1a2e;
    border: 2px solid #2874f0;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-light:hover {
    background: #2874f0;
    color: white;
    transform: translateX(-5px);
}

/* === Liste de documents === */
.list-group {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(40, 116, 240, 0.1);
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
    padding: 20px;
    background: #f8f9fa;
    transition: all 0.3s;
}

.list-group-item:first-child {
    border-radius: 10px 10px 0 0;
}

.list-group-item:last-child {
    border-radius: 0 0 10px 10px;
    border-bottom: none;
}

.list-group-item:hover {
    background: #f0f7ff;
    transform: translateX(5px);
    /* border-left: 4px solid #2874f0; */
    padding-left: 16px;
}

.list-group-item a {
    color: #2874f0;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s;
}

.list-group-item a:hover {
    color: #1a5dd6;
    text-decoration: underline;
}

/* === Formulaire upload === */
form {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    border: 2px dashed #2874f0;
    margin-top: 30px;
}

.form-control {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #2874f0;
    box-shadow: 0 0 0 4px rgba(40, 116, 240, 0.1);
}

.form-control::file-selector-button {
    background: linear-gradient(135deg, #2874f0 0%, #1a5dd6 100%);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    margin-right: 15px;
    transition: all 0.3s;
}

.form-control::file-selector-button:hover {
    background: linear-gradient(135deg, #1a5dd6 0%, #0f3460 100%);
    transform: translateY(-2px);
}

/* === Boutons === */
.btn-primary {
    background: linear-gradient(135deg, #2874f0 0%, #1a5dd6 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s;
    cursor: pointer;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1a5dd6 0%, #0f3460 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(40, 116, 240, 0.4);
}

/* === Textes === */
.text-muted {
    color: #6c757d !important;
    font-style: italic;
    padding: 20px;
    text-align: center;
}

small.text-muted {
    color: #6c757d !important;
    font-size: 0.875rem;
}

/* === Espacements === */
.mb-2 {
    margin-bottom: 15px;
}

.mb-3 {
    margin-bottom: 20px;
}

.py-4 {
    padding-top: 30px;
    padding-bottom: 30px;
}

/* === Responsive === */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }
    
    .container {
        padding: 20px;
    }
    
    h2 {
        font-size: 1.4rem;
    }
    
    .list-group-item {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }
    
    form {
        padding: 20px;
    }
    
    .btn-primary {
        width: 100%;
    }
}

/* === Animation === */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.list-group-item {
    animation: fadeIn 0.4s ease-out;
}

.list-group-item:nth-child(1) { animation-delay: 0.1s; }
.list-group-item:nth-child(2) { animation-delay: 0.2s; }
.list-group-item:nth-child(3) { animation-delay: 0.3s; }
.list-group-item:nth-child(4) { animation-delay: 0.4s; }
.list-group-item:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
<div class="app-container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    
    <main class="main-content">
        <header class="main-header">
            <h1 class="page-title">
                <i data-feather="folder"></i>
                Documents RH - <?= htmlspecialchars($employee['prenom'].' '.$employee['nom']) ?>
            </h1>
        </header>
        
        <div class="content-wrapper">
            <div class="container">
                <a href="/employes/<?= $employee['id_employe'] ?>" class="btn btn-light mb-3">
                    <i data-feather="arrow-left"></i>
                    Retour profil
                </a>

    <?php if (!empty($documents)): ?>
        <ul class="list-group mb-3">
            <?php foreach ($documents as $d): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="<?= htmlspecialchars($d['chemin_document']) ?>" target="_blank"><?= htmlspecialchars($d['nom_document']) ?></a>
                    <small class="text-muted"><?= htmlspecialchars($d['type_document'] ?? '') ?> <?= !empty($d['uploaded_at']) ? ' — '.date('d/m/Y', strtotime($d['uploaded_at'])) : '' ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">Aucun document</p>
    <?php endif; ?>

                <form method="POST" action="/employes/<?= $employee['id_employe'] ?>/documents/upload" enctype="multipart/form-data">
                    <div class="mb-2"><input type="file" name="document" class="form-control" required></div>
                    <div class="mb-2"><input name="type" placeholder="Type (CIN, diplôme...)" class="form-control"></div>
                    <button class="btn btn-primary">
                        <i data-feather="upload"></i>
                        Téléverser
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script>feather.replace();</script>
</body>
</html>