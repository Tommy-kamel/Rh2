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