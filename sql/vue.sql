/* Liste des conges */
CREATE VIEW vue_liste_conges AS 
SELECT c.id_conge, e.nom, e.prenom, tc.type, c.date_demande, c.date_debut, c.date_fin, c.raison, c.date_validation, c.status
FROM conge c
JOIN employe e ON c.id_employe = e.id_employe
JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge WHERE c.status = 21;

/* Durée totale des conges par employé (en jours) */
CREATE VIEW vue_nombre_conge AS
SELECT
    e.id_employe,
    e.nom,
    e.prenom,
    SUM(DATEDIFF(c.date_fin, c.date_debut) + 1) as duree_totale_conges_jours
FROM conge c
JOIN employe e ON c.id_employe = e.id_employe
JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
WHERE c.status = 21 AND tc.type = 'annuel'
AND YEAR(c.date_debut) = YEAR(CURDATE())
AND YEAR(c.date_fin) = YEAR(CURDATE())
GROUP BY e.id_employe, e.nom, e.prenom;


CREATE VIEW vue_employe_departement AS 
SELECT
    e.id_employe,
    e.nom,
    e.prenom,
    e.email,
    e.sexe,
    e.telephone,
    d.nom_departement,
    p.nom as nom_poste,
    c.salaire,
    c.type as type_contrat,
    c.date_debut as date_debut_contrat
FROM employe e
JOIN contrat c ON e.id_employe = c.id_employe
JOIN departement d ON c.id_departement = d.id_departement
JOIN poste p ON c.id_poste = p.id_poste
ORDER BY d.nom_departement, e.nom, e.prenom;


CREATE VIEW vue_conge_en_attente AS
SELECT
    d.id_departement,
    d.nom_departement,
    e.id_employe,
    e.nom,
    e.prenom,
    p.nom as nom_poste,
    c.id_conge,
    tc.type,
    c.date_demande,
    c.date_debut,
    c.date_fin,
    DATEDIFF(c.date_fin, c.date_debut) + 1 as nb_jour,
    c.raison,
    c.status
FROM conge c
JOIN employe e ON c.id_employe = e.id_employe
JOIN contrat ct ON e.id_employe = ct.id_employe
JOIN departement d ON ct.id_departement = d.id_departement
JOIN poste p ON ct.id_poste = p.id_poste
JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
WHERE c.status = 1
ORDER BY d.nom_departement, e.nom, e.prenom;

CREATE VIEW vue_conge_en_attente_rh AS
SELECT
    d.id_departement,
    d.nom_departement,
    e.id_employe,
    e.nom,
    e.prenom,
    p.nom as nom_poste,
    c.id_conge,
    tc.type,
    c.date_demande,
    c.date_debut,
    c.date_fin,
    DATEDIFF(c.date_fin, c.date_debut) + 1 as nb_jour,
    c.raison,
    c.status
FROM conge c
JOIN employe e ON c.id_employe = e.id_employe
JOIN contrat ct ON e.id_employe = ct.id_employe
JOIN departement d ON ct.id_departement = d.id_departement
JOIN poste p ON ct.id_poste = p.id_poste
JOIN type_conge tc ON c.id_type_conge = tc.id_type_conge
WHERE c.status = 11
ORDER BY d.nom_departement, e.nom, e.prenom;