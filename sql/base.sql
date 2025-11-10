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
    duree INT,
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
    status INT, /* 1: en attente, 11: valider par le chef de departement, 21: valider par le rh */
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
    FOREIGN KEY (id_employe) REFERENCES employe(id_employe)
);

CREATE TABLE taux_irsa (
    id_taux_irsa INT AUTO_INCREMENT PRIMARY KEY,
    salaire_minimal DOUBLE,
    salaire_maximal DOUBLE,
    taux INT
);

INSERT INTO departement (nom_departement) VALUES
('Ressources Humaines'),
('Production'),
('Achat et vente'),
('Gestion de stock'),
('Gestion d''immobilisation');

INSERT INTO user (id_departement,nom_utilisateur, mot_de_passe) VALUES 
(1,'rh_user', 'RH'),
(2,'production_user', 'PROD'),
(3,'achat_vente_user', 'ACHAT'),
(4,'stock_user', 'STOCK'),
(5,'immobilisation_user', 'IMMO');

INSERT INTO poste (nom) VALUES
('Directeur Général'),
('Chef de Département RH'),
('Développeur'),
('Comptable'),
('Commercial'),
('Technicien'),
('Secrétaire'),
('Manager Production');

INSERT INTO employe (nom, prenom, date_naissance, email,mot_de_passe, sexe, telephone, adresse, numero_cnaps) VALUES
('Rakoto', 'Jean', '1990-01-15', 'jean.rakoto@email.com', '123', 'Homme', '0341234567', 'Antananarivo', 123456789),
('Rabe', 'Marie', '1985-05-20', 'marie.rabe@email.com', '123', 'Femme', '0339876543', 'Toamasina', 987654321),
('Andrianaivo', 'Paul', '1992-03-10', 'paul.andrianaivo@email.com', '123', 'Homme', '0324567890', 'Fianarantsoa', 456789123),
('Rasoa', 'Sophie', '1988-11-25', 'sophie.rasoa@email.com', '123', 'Femme', '0345678901', 'Mahajanga', 789123456),
('Randria', 'Marc', '1995-07-08', 'marc.randria@email.com', '123', 'Homme', '0330123456', 'Toliara', 321654987),
('Ravelojaona', 'Alice', '1987-12-03', 'alice.ravelojaona@email.com', '123', 'Femme', '0342345678', 'Antsirabe', 654321987),
('Razafindrakoto', 'Pierre', '1993-08-22', 'pierre.razafindrakoto@email.com', '123', 'Homme', '0333456789', 'Antsiranana', 789456123),
('Ramanantsoa', 'Lucie', '1991-04-17', 'lucie.ramanantsoa@email.com', '123', 'Femme', '0324567891', 'Morondava', 147258369),
('Andrianarivo', 'Thomas', '1989-09-30', 'thomas.andrianarivo@email.com', '123', 'Homme', '0345678902', 'Nosy Be', 963852741),
('Ratsimbazafy', 'Sarah', '1994-02-14', 'sarah.ratsimbazafy@email.com', '123', 'Femme', '0336789012', 'Ambatolampy', 852741963),
('Rakotomalala', 'Michel', '1986-11-11', 'michel.rakotomalala@email.com', '123', 'Homme', '0327890123', 'Manakara', 741852963),
('Razafindrazaka', 'Julie', '1996-06-28', 'julie.razafindrazaka@email.com', '123', 'Femme', '0348901234', 'Sambava', 369258147),
('Andriantsiferana', 'David', '1984-07-05', 'david.andriantsiferana@email.com', '123', 'Homme', '0339012345', 'Moramanga', 258369147),
('Rabenandrasana', 'Emma', '1997-01-19', 'emma.rabenandrasana@email.com', '123', 'Femme', '0320123456', 'Ihosy', 147369258),
('Rakotoarisoa', 'Antoine', '1983-10-08', 'antoine.rakotoarisoa@email.com', '123', 'Homme', '0341234568', 'Betafo', 963147852);

-- Exemples de contrats
INSERT INTO contrat (id_employe, salaire, date_debut, date_fin, type, id_poste, id_departement) VALUES
(1, 3500.00, '2022-01-01', NULL, 'CDI', 2, 1),
(2, 2800.00, '2023-03-15', '2024-03-14', 'CDD', 8, 2),
(3, 2200.00, '2024-06-01', NULL, 'CDI', 3, 3),
(4, 1800.00, '2023-11-01', '2024-05-01', 'Essai', 6, 4),
(5, 2400.00, '2021-09-01', NULL, 'CDI', 4, 5),
(6, 3200.00, '2023-01-15', NULL, 'CDI', 1, 1),
(7, 2600.00, '2023-08-01', '2024-07-31', 'CDD', 3, 2),
(8, 1900.00, '2024-02-01', NULL, 'CDI', 7, 3),
(9, 3000.00, '2022-10-01', NULL, 'CDI', 5, 3),
(10, 2100.00, '2024-01-01', '2024-06-30', 'Essai', 6, 4),
(11, 2700.00, '2023-05-01', NULL, 'CDI', 4, 5),
(12, 1700.00, '2024-03-01', '2024-08-31', 'CDD', 6, 2),
(13, 3800.00, '2021-12-01', NULL, 'CDI', 1, 1),
(14, 2000.00, '2024-04-01', NULL, 'CDI', 7, 4),
(15, 2500.00, '2023-09-01', '2024-08-31', 'CDD', 5, 3);
