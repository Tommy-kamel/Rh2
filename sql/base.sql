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
