# Analyse rapide du projet RH2

Cette application est une solution RH construite avec **Flight PHP v3** (PHP 7.4+). Elle suit une structure MVC légère :

- **Point d’entrée :** `public/index.php` charge le bootstrap (`app/config/bootstrap.php`) qui initialise la config, les services et les routes.
- **Routage :** `app/config/routes.php` définit les URL pour l’authentification, les tableaux de bord (admin, RH, employé), la gestion des congés, le calendrier, les statistiques, la paie et le chatbot.
- **Contrôleurs :** organisés par domaine (`app/controllers/admin`, `app/controllers/employe`, `app/controllers/paiement`) pour séparer la logique métier.
- **Modèles / vues :** `app/models` pour l’accès aux données et `app/views` pour les gabarits.
- **Chatbot :** logique dédiée dans `chatbot/` pour l’assistant RH et la génération de documents.
- **SQL :** schémas et données de base dans `sql/` pour MySQL/MariaDB.

## Dépendances principales
- `flightphp/core` & `flightphp/runway` pour le framework.
- `tecnickcom/tcpdf` pour les PDF (fiches de paie, exports).
- `guzzlehttp/guzzle` pour les appels HTTP.
- `tracy/tracy` pour le debug en développement.

## Démarrage en local
```bash
composer install
composer start   # lance le serveur sur http://localhost:8000
```
Assurez-vous de créer/adapter `app/config/config.php` à partir de `config_sample.php` pour vos paramètres (base de données, etc.).

## Fonctionnalités clés
- Authentification séparée admin/employé et déconnexion commune.
- Dashboards admin/RH : suivi des congés (validation, refus, suggestions), calendrier et statistiques (genre, âge, département, contrat).
- Espace employé : tableau de bord personnel et demandes de congés.
- Paie : liste des employés payables, fiches, détails par mois/type, primes globales, exports PDF/Excel/XML, historique.
- Chatbot RH accessible via `/chatbot` pour assister les utilisateurs.
