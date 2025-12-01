INSERT INTO absence (id_absence, id_employe, date_absence, estdeductible) VALUES
(1, 1, '2025-11-05', 1),
(2, 1, '2025-11-18', 1),
(3, 2, '2025-11-08', 0),
(4, 3, '2025-11-22', 1),
(5, 3, '2025-11-25', 1);

INSERT INTO retard (id_retard, id_employe, date_retard, duree_retard) VALUES
(1, 1, '2025-11-04', 30),
(2, 1, '2025-11-12', 45),
(3, 2, '2025-11-07', 15),
(4, 2, '2025-11-20', 60),
(5, 3, '2025-11-14', 90);

INSERT INTO heure_sup (id_heure_sup, type, pourcentage_majoration) VALUES
(1, 'week-end', 100),
(2, 'nuit', 30),
(3, 'jour_ferie', 100),
(4, 'imprevu', 30);

INSERT INTO heure_sup_employe (id_heure_sup_employe, id_employe, id_heure_sup, date_heure_sup, nombre_minutes) VALUES
(1, 1, 4, '2025-11-03', 780),
(2, 1, 2, '2025-11-05', 240),
(3, 2, 1, '2025-11-09', 480),
(4, 2, 3, '2025-11-11', 360),
(5, 3, 4, '2025-11-15', 600),
(6, 3, 2, '2025-11-16', 180);

INSERT INTO prime_divers (id_prime, id_employe, motif, montant_prime, date_prime, type) VALUES
(1, 1, 'Prime de performance Q4', 150000, '2025-11-01', 'rendement'),
(2, 1, 'Prime transport', 50000, '2025-11-01', 'diver'),
(3, 2, 'Prime exceptionnelle', 200000, '2025-11-05', 'rendement'),
(4, 2, 'Prime logement', 80000, '2025-11-05', 'diver'),
(5, 3, 'Prime de responsabilité', 300000, '2025-11-01', 'rendement'),
(6, 3, 'Prime repas', 100000, '2025-11-01', 'diver');

------------- COMPETENCE ENTREPRISE ---------------

INSERT INTO competence (nom_competence, description) VALUES
('Programmation Java', 'Maîtrise du langage Java, POO, et frameworks Java.'),
('Programmation Python', 'Connaissance de Python, automatisation et data analysis.'),
('Gestion de projet', 'Planification, gestion équipe, suivi des tâches.'),
('Analyse de données', 'Statistiques, dashboards, Excel, SQL.'),
('Communication', 'Communication orale et écrite efficace.'),
('Leadership', 'Capacité à diriger une équipe.'),
('Comptabilité', 'Gestion financière, bilan, opérations comptables.'),
('Support Technique', 'Assistance informatique, diagnostic et résolution.'),
('Rédaction de rapports', 'Capacité à rédiger des documents professionnels.');

INSERT INTO employe_competence (id_employe, id_competence, niveau) VALUES

(1, 1, 4),  -- Java
(1, 3, 3),  -- Gestion de projet
(1, 5, 4),  -- Communication

(2, 2, 4),  -- Python
(2, 4, 5),  -- Analyse de données
(2, 5, 3),  -- Communication

(3, 7, 4),  -- Comptabilité
(3, 9, 5),  -- Rédaction de rapports
(3, 5, 3),  -- Communication

(4, 8, 5),  -- Support technique
(4, 1, 2),  -- Java
(4, 5, 3);  -- Communication

INSERT INTO employe_competence (id_employe, id_competence, niveau) VALUES
-- Employé 1 (Jean Rakoto) - Compétences supplémentaires
(1, 2, 3),  -- Python
(1, 4, 2),  -- Analyse de données
(1, 6, 3),  -- Leadership

-- Employé 2 (Marie Rabe) - Compétences supplémentaires
(2, 1, 3),  -- Java
(2, 3, 4),  -- Gestion de projet
(2, 6, 4),  -- Leadership

-- Employé 3 (Paul Andrianaivo) - Compétences supplémentaires
(3, 3, 3),  -- Gestion de projet
(3, 6, 4),  -- Leadership
(3, 8, 2),  -- Support technique

-- Employé 4 (Sophie Rasoa) - Compétences supplémentaires
(4, 2, 3),  -- Python
(4, 4, 4),  -- Analyse de données
(4, 9, 3),  -- Rédaction de rapports

-- Employé 5 (Marc Randria)
(5, 7, 5),  -- Comptabilité
(5, 9, 4),  -- Rédaction de rapports
(5, 5, 4),  -- Communication
(5, 3, 3),  -- Gestion de projet
(5, 6, 3),  -- Leadership

-- Employé 6 (Alice Ravelojaona)
(6, 1, 5),  -- Java
(6, 2, 4),  -- Python
(6, 3, 5),  -- Gestion de projet
(6, 6, 5),  -- Leadership
(6, 5, 4),  -- Communication

-- Employé 7 (Pierre Razafindrakoto)
(7, 1, 4),  -- Java
(7, 2, 3),  -- Python
(7, 4, 4),  -- Analyse de données
(7, 5, 3),  -- Communication
(7, 9, 3),  -- Rédaction de rapports

-- Employé 8 (Lucie Ramanantsoa)
(8, 2, 4),  -- Python
(8, 4, 5),  -- Analyse de données
(8, 5, 4),  -- Communication
(8, 9, 4),  -- Rédaction de rapports
(8, 3, 3),  -- Gestion de projet

-- Employé 9 (Thomas Andrianarivo)
(9, 5, 5),  -- Communication
(9, 6, 4),  -- Leadership
(9, 3, 4),  -- Gestion de projet
(9, 7, 3),  -- Comptabilité
(9, 9, 4),  -- Rédaction de rapports

-- Employé 10 (Sarah Ratsimbazafy)
(10, 8, 4), -- Support technique
(10, 1, 3), -- Java
(10, 2, 3), -- Python
(10, 5, 3), -- Communication
(10, 9, 2), -- Rédaction de rapports

-- Employé 11 (Michel Rakotomalala)
(11, 7, 4), -- Comptabilité
(11, 9, 5), -- Rédaction de rapports
(11, 5, 4), -- Communication
(11, 3, 3), -- Gestion de projet
(11, 6, 3), -- Leadership

-- Employé 12 (Julie Razafindrazaka)
(12, 8, 5), -- Support technique
(12, 1, 2), -- Java
(12, 2, 3), -- Python
(12, 5, 4), -- Communication
(12, 4, 3), -- Analyse de données

-- Employé 13 (David Andriantsiferana)
(13, 1, 5), -- Java
(13, 2, 4), -- Python
(13, 3, 5), -- Gestion de projet
(13, 6, 5), -- Leadership
(13, 4, 4), -- Analyse de données

-- Employé 14 (Emma Rabenandrasana)
(14, 8, 4), -- Support technique
(14, 5, 4), -- Communication
(14, 9, 3), -- Rédaction de rapports
(14, 2, 3), -- Python
(14, 4, 3), -- Analyse de données

-- Employé 15 (Antoine Rakotoarisoa)
(15, 5, 5), -- Communication
(15, 6, 4), -- Leadership
(15, 3, 4), -- Gestion de projet
(15, 7, 3), -- Comptabilité
(15, 9, 4); -- Rédaction de rapports

-- Ajout de compétences supplémentaires
INSERT INTO competence (nom_competence, description) VALUES
('Gestion du temps', 'Capacité à organiser et prioriser les tâches efficacement'),
('Résolution de problèmes', 'Aptitude à analyser et résoudre des problèmes complexes'),
('Travail d''équipe', 'Collaboration efficace au sein d''une équipe'),
('Adaptabilité', 'Capacité à s''adapter aux changements et nouvelles technologies'),
('Créativité', 'Pensée innovante et génération d''idées nouvelles'),
('Gestion du stress', 'Capacité à travailler sous pression'),
('Négociation', 'Compétences en négociation et relations clients'),
('Formation', 'Capacité à former et accompagner d''autres collaborateurs'),
('Gestion de budget', 'Compétences en gestion budgétaire et financière'),
('Marketing digital', 'Connaissance des outils et stratégies de marketing en ligne');

-- Attribution des nouvelles compétences à certains employés
INSERT INTO employe_competence (id_employe, id_competence, niveau) VALUES
-- Nouvelles compétences pour les managers et chefs de projet
(1, 10, 4),  -- Gestion du temps
(1, 11, 4),  -- Résolution de problèmes
(6, 10, 5),  -- Gestion du temps
(6, 11, 5),  -- Résolution de problèmes
(6, 12, 5),  -- Travail d'équipe
(13, 10, 5), -- Gestion du temps
(13, 11, 5), -- Résolution de problèmes
(13, 12, 5), -- Travail d'équipe

-- Compétences transversales pour tous les employés
(2, 12, 4),  -- Travail d'équipe
(3, 12, 4),  -- Travail d'équipe
(4, 12, 4),  -- Travail d'équipe
(5, 12, 4),  -- Travail d'équipe
(7, 12, 4),  -- Travail d'équipe
(8, 12, 4),  -- Travail d'équipe
(9, 12, 5),  -- Travail d'équipe
(10, 12, 4), -- Travail d'équipe
(11, 12, 4), -- Travail d'équipe
(12, 12, 4), -- Travail d'équipe
(14, 12, 4), -- Travail d'équipe
(15, 12, 5), -- Travail d'équipe

-- Compétences spécifiques pour les rôles commerciaux et relation clients
(9, 16, 4),  -- Négociation
(15, 16, 4), -- Négociation
(2, 16, 3),  -- Négociation

-- Compétences de formation pour les seniors
(1, 17, 3),  -- Formation
(6, 17, 4),  -- Formation
(13, 17, 4), -- Formation

-- Gestion de budget pour les comptables et managers
(3, 18, 5),  -- Gestion de budget
(5, 18, 4),  -- Gestion de budget
(11, 18, 4), -- Gestion de budget
(6, 18, 3),  -- Gestion de budget
(13, 18, 4); -- Gestion de budget

INSERT INTO poste_libre (id_poste, id_departement, date_publication, date_expiration, description) VALUES
(1, 1, '2025-01-10', '2025-02-10', 'Recherche développeur Java sénior.'),
(2, 1, '2025-01-15', '2025-02-20', 'Besoin un analyste data pour missions BI.'),
(3, 2, '2025-01-20', '2025-02-25', 'Poste de comptable confirmé.'),
(4, 1, '2025-01-25', '2025-12-28', 'Technicien support pour assistance interne.'),
(1, 1, '2025-01-10', '2025-12-01', 'Chef de projet Java junior.');

INSERT INTO poste_competence (id_poste, id_competence, niveau_requis) VALUES
(1, 1, 3), -- poste 1 nécessite Programmation Java niveau 3
(1, 5, 3), -- et Communication niveau 3
(2, 2, 4), -- poste 2 nécessite Python niveau 4
(2, 4, 3), -- et Analyse de données niveau 3
(3, 7, 4); -- poste 3 : Comptabilité niveau 4
