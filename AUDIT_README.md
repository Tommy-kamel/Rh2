# Système de Journalisation d'Audit - RH2

## Vue d'ensemble

Le système de journalisation d'audit (traces d'audit) permet de suivre et enregistrer toutes les actions importantes effectuées dans l'application RH2. Cela inclut les connexions, modifications de données, consultations de statistiques, etc.

## Fonctionnalités

### 1. Journalisation automatique
- **Connexions** : Enregistrement des tentatives de connexion (succès/échec)
- **Modifications de données** : Création, mise à jour, suppression d'employés, contrats, congés, etc.
- **Consultations sensibles** : Accès aux statistiques, données personnelles

### 2. Interface d'administration
- **Consultation des logs** : Interface web pour visualiser l'historique des actions
- **Filtres avancés** : Filtrage par utilisateur, action, table, période
- **Détails des actions** : Visualisation des valeurs avant/après modification

### 3. Sécurité
- **Traçabilité complète** : Chaque action est liée à un utilisateur et horodatée
- **Adresse IP** : Enregistrement de l'adresse IP du client
- **User-Agent** : Informations sur le navigateur utilisé

## Structure de la base de données

### Table `audit_log`

```sql
CREATE TABLE audit_log (
    id_audit INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,  -- ID de l'utilisateur (NULL pour actions anonymes)
    action VARCHAR(255) NOT NULL,  -- Description de l'action
    table_name VARCHAR(100) NULL,  -- Table affectée
    record_id INT NULL,  -- ID de l'enregistrement affecté
    old_values TEXT NULL,  -- Valeurs avant modification (JSON)
    new_values TEXT NULL,  -- Valeurs après modification (JSON)
    ip_address VARCHAR(45) NULL,  -- Adresse IP
    user_agent TEXT NULL,  -- Navigateur utilisé
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Utilisation

### Accès aux logs d'audit
1. Connectez-vous en tant qu'administrateur RH
2. Dans le menu latéral, cliquez sur "Logs d'Audit"
3. Utilisez les filtres pour rechercher des actions spécifiques

### Types d'actions journalisées
- `LOGIN` : Connexion utilisateur
- `LOGOUT` : Déconnexion utilisateur
- `CREATE_*` : Création d'un enregistrement (ex: `CREATE_EMPLOYE`)
- `UPDATE_*` : Mise à jour d'un enregistrement (ex: `UPDATE_CONTRAT`)
- `DELETE_*` : Suppression d'un enregistrement (ex: `DELETE_CONGE`)
- `VIEW_*` : Consultation de données sensibles (ex: `VIEW_STATISTIQUES`)

## Intégration dans le code

### Journalisation manuelle
```php
// Dans un contrôleur ou modèle
$auditModel = Flight::auditLogModel();
$auditModel->logAction(
    'UPDATE_EMPLOYE',           // Action
    'employe',                  // Table
    $id_employe,               // ID enregistrement
    $oldData,                  // Valeurs anciennes
    $newData                   // Valeurs nouvelles
);
```

### Journalisation automatique via middleware
Le système journalise automatiquement les requêtes HTTP importantes (POST, PUT, DELETE) via un middleware configuré dans `bootstrap.php`.

## Configuration

### Activation/Désactivation
Le système est activé par défaut. Pour le désactiver, commentez le middleware dans `app/config/bootstrap.php`.

### Permissions
Seuls les utilisateurs avec `user_type = 'admin'` peuvent accéder aux logs d'audit.

## Maintenance

### Nettoyage des logs
Pour éviter la croissance excessive de la table, implémentez une politique de rétention :

```sql
-- Supprimer les logs de plus de 2 ans
DELETE FROM audit_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL 2 YEAR);
```

### Archivage
Pour les environnements de production, considérez l'archivage des logs dans des fichiers ou une base de données séparée.

## Sécurité et conformité

### RGPD
- Les logs contiennent des données personnelles (adresses IP, User-Agent)
- Respectez les délais de conservation légaux
- Permettez la suppression des données sur demande

### Audit de sécurité
- Surveillez les tentatives de connexion échouées
- Alertez sur les modifications sensibles
- Vérifiez régulièrement l'intégrité des logs

## Développement

### Ajout de nouvelles actions
1. Définissez le type d'action (ex: `EXPORT_DATA`)
2. Ajoutez la journalisation dans le contrôleur approprié
3. Mettez à jour la documentation

### Extension du middleware
Modifiez le middleware dans `bootstrap.php` pour journaliser d'autres types de requêtes ou ajouter des conditions spécifiques.

## Dépannage

### Logs non enregistrés
- Vérifiez les permissions de la base de données
- Vérifiez que la table `audit_log` existe
- Vérifiez les erreurs PHP dans les logs du serveur

### Performance
- Indexez la colonne `timestamp` pour les recherches par date
- Considérez la pagination pour les gros volumes de données
- Archivez régulièrement les anciens logs

---

**Note** : Ce système fournit une base solide pour l'audit. Adaptez-le selon vos besoins spécifiques de conformité et de sécurité.