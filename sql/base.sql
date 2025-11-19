DROP DATABASE IF EXISTS rh2;
CREATE DATABASE rh2;
USE rh2;

CREATE TABLE employe(
    id_employe INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    prenom VARCHAR(255),
    date_naissance DATE,
    email VARCHAR(255),
    mot_de_passe VARCHAR(255),
    sexe VARCHAR(255),
    telephone VARCHAR(255),
    adresse VARCHAR(255),
    numero_cnaps INT
);

ALTER TABLE employe
    ADD COLUMN cin VARCHAR(50) DEFAULT NULL,
    ADD COLUMN photo VARCHAR(255) DEFAULT NULL,
    ADD COLUMN lieu_naissance VARCHAR(255) DEFAULT NULL;

CREATE TABLE departement(
   id_departement INT PRIMARY KEY AUTO_INCREMENT,
   nom_departement VARCHAR(50)
);

CREATE TABLE user (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    id_departement INT,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    FOREIGN KEY(id_departement) REFERENCES departement(id_departement)
);

CREATE TABLE poste (
    id_poste INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50)
);

CREATE TABLE contrat (
    id_contrat INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    salaire DECIMAL(15,2),
    date_debut DATE,
    date_fin DATE,
    type enum("CDD", "CDI", "Essai"),
    id_poste int,
    id_departement int,
    FOREIGN KEY (id_poste) REFERENCES poste(id_poste),
    FOREIGN KEY (id_departement) REFERENCES departement(id_departement),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE documents (
    id_documents INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    nom_document VARCHAR(255),
    chemin_document VARCHAR(255),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE type_conge (
    id_type_conge INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(255),
    pourcentage_salaire INT
);

CREATE TABLE conge (
    id_conge INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    id_type_conge INT,
    date_demande DATE,
    date_debut DATE,
    date_fin DATE,
    raison VARCHAR(255),
    date_validation DATE,
    status INT, /* 1: en attente, 11: valide par le chef de departement, 21: valide par le rh, 0:refuse */
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY(id_type_conge) REFERENCES type_conge(id_type_conge)
);

CREATE TABLE pointage (
    id_pointage INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    date_heure_arrive DATETIME,
    date_heure_depart DATETIME,
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE heure_pointage ( /* regle de gestion */
    id_heure_pointage INT AUTO_INCREMENT PRIMARY KEY,
    id_poste INT,
    heure_debut TIME,
    heure_fin TIME,
    FOREIGN KEY(id_poste) REFERENCES poste(id_poste)
);

CREATE TABLE jour_ferier (
    id_jour_ferier INT AUTO_INCREMENT PRIMARY KEY,
    date_jour_ferier DATE
);

CREATE TABLE heure_sup (
    id_heure_sup INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM("week-end","nuit","jour_ferie", "imprevu"),
    pourcentage_majoration INT
);

CREATE TABLE heure_sup_employe (
    id_heure_sup_employe INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    id_heure_sup INT,
    date_heure_sup DATE,
    nombre_minutes INT,
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe),
    FOREIGN KEY(id_heure_sup) REFERENCES heure_sup(id_heure_sup)
);

CREATE TABLE absence (
    id_absence INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    date_absence DATE,
    estdeductible BOOLEAN,
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe)
    
);

CREATE TABLE retard (
    id_retard INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    date_retard DATE,
    duree_retard INT,
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE prime_divers (
    id_prime INT PRIMARY KEY AUTO_INCREMENT,
    id_employe INT,
    motif text,
    montant_prime DOUBLE,
    date_prime DATE,
    type enum("rendement", "diver"),
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE taux_irsa (
    id_taux_irsa INT AUTO_INCREMENT PRIMARY KEY,
    salaire_minimal DOUBLE,
    salaire_maximal DOUBLE,
    taux INT
);

CREATE TABLE fiche_paie (
    id_fiche_paie INT AUTO_INCREMENT PRIMARY KEY,
    date_fiche DATE,
    id_employe INT,
    absence_mois INT,
    heure_sup DOUBLE,
    salaire_brut DOUBLE,
    cnaps DOUBLE,
    retenue_sanitaire DOUBLE,
    revenue_imposable DOUBLE,
    total_irsa DOUBLE,
    total_retenu DOUBLE,
    net_a_payer DOUBLE,
    net_du_mois DOUBLE,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

-- Table pour les réponses du chatbot RH
CREATE TABLE chatbot_responses(
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(255) NOT NULL,
    response TEXT NOT NULL,
    is_dynamic BOOLEAN DEFAULT FALSE
);


CREATE TABLE chatbot_synonyms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword_id INT,
    synonym VARCHAR(255),
    FOREIGN KEY (keyword_id) REFERENCES chatbot_responses(id)
);

-- Vue pour calculer le nombre total de jours de congé par employé
CREATE VIEW vue_nombre_conge AS
SELECT id_employe, SUM(DATEDIFF(date_fin, date_debut) + 1) as duree_totale_conges_jours
FROM conge
WHERE status = 21
GROUP BY id_employe;

CREATE TABLE contrat_history (
    id_contrat_history INT AUTO_INCREMENT PRIMARY KEY,
    id_contrat INT,
    id_employe INT,
    salaire DECIMAL(15,2),
    date_debut DATE,
    date_fin DATE,
    type ENUM('CDD','CDI','Essai'),
    id_poste INT,
    id_departement INT,
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE poste_history (
    id_poste_history INT AUTO_INCREMENT PRIMARY KEY,
    id_employe INT,
    id_poste INT,
    id_departement INT,
    date_debut DATE,
    date_fin DATE,
    motif VARCHAR(255),
    changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(id_employe) REFERENCES employe(id_employe)
);

ALTER TABLE documents
    ADD COLUMN type_document VARCHAR(100) DEFAULT NULL,
    ADD COLUMN uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- ...existing code...
ALTER TABLE contrat
    ADD COLUMN periode_essai_jours INT DEFAULT 0,
    ADD COLUMN renouvellement_count INT DEFAULT 0;
-- ...existing code...
CREATE TABLE fiche_paie (
    id_fiche_paie INT AUTO_INCREMENT PRIMARY KEY,
    date_fiche DATE,
    id_employe INT,
    absence_mois INT,
    heure_sup DOUBLE,
    salaire_brut DOUBLE,
    cnaps DOUBLE,
    retenue_sanitaire DOUBLE,
    revenue_imposable DOUBLE,
    total_irsa DOUBLE,
    total_retenu DOUBLE,
    net_a_payer DOUBLE,
    net_du_mois DOUBLE,
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);
ALTER TABLE absence ADD COLUMN estdeductible BOOLEAN DEFAULT FALSE;

INSERT INTO poste (nom) VALUES 
('Développeur'),
('Commercial'),
('Comptable');

-- Insérer les règles d'heure de pointage pour chaque poste
INSERT INTO heure_pointage (id_poste, heure_debut, heure_fin) VALUES 
(1, '08:00:00', '17:00:00'), -- Développeur : 8h-17h
(2, '08:30:00', '17:30:00'), -- Commercial : 8h30-17h30
(3, '07:30:00', '16:30:00'); -- Comptable : 7h30-16h30

-- Insérer les employés
INSERT INTO employe (nom, prenom, date_naissance, email, mot_de_passe, sexe, telephone, adresse, numero_cnaps) VALUES 
('Rakoto', 'Jean', '1990-05-15', 'jean.rakoto@entreprise.mg', 'motdepasse123', 'Homme', '+261 34 12 345 67', 'Lot IVB 123 Antananarivo', 123456),
('Rasoa', 'Marie', '1992-08-22', 'marie.rasoa@entreprise.mg', 'motdepasse123', 'Femme', '+261 33 12 345 68', 'Lot V 456 Antananarivo', 123457),
('Randria', 'Paul', '1988-12-10', 'paul.randria@entreprise.mg', 'motdepasse123', 'Homme', '+261 32 12 345 69', 'Lot VI 789 Antananarivo', 123458);

-- Insérer les contrats (CDI)
INSERT INTO contrat (id_employe, salaire, date_debut, date_fin, type, id_poste) VALUES 
(1, 1200000.00, '2023-01-15', NULL, 'CDI', 1),  -- Jean Rakoto - Développeur
(2, 900000.00, '2023-03-20', NULL, 'CDI', 2),   -- Marie Rasoa - Commercial
(3, 1100000.00, '2022-11-10', NULL, 'CDI', 3); -- Paul Randria - Comptable


INSERT INTO heure_sup (type, pourcentage_majoration) VALUES 
('week-end', 50),
('nuit', 25),
('jour_ferie', 100),
('imprevu', 30);


INSERT INTO type_conge (type, pourcentage_salaire) VALUES 
('Congé annuel', 100),
('Congé maladie', 80),
('Congé sans solde', 0);

INSERT INTO conge (id_employe, id_type_conge, date_demande, date_debut, date_fin, raison, date_validation, status) VALUES 
(1, 1, '2025-01-10', '2025-02-01', '2025-02-05', 'Vacances familiales', '2025-01-15', 21),
(1, 1, '2025-03-15', '2025-04-01', '2025-04-03', 'Repos', '2025-03-20', 21),
(2, 1, '2025-02-01', '2025-02-15', '2025-02-20', 'Vacances', '2025-02-05', 21),
(2, 2, '2025-03-10', '2025-03-11', '2025-03-13', 'Maladie', '2025-03-10', 21);


