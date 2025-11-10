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
WHERE c.status = 21
AND YEAR(c.date_debut) = YEAR(CURDATE())
AND YEAR(c.date_fin) = YEAR(CURDATE())
GROUP BY e.id_employe, e.nom, e.prenom;