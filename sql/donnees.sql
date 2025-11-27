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
('Directeur General'),
('Chef de Departement RH'),
('Developpeur'),
('Comptable'),
('Commercial'),
('Technicien'),
('Secretaire'),
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
(2, 2800.00, '2023-03-15', '2025-12-14', 'CDD', 8, 2),
(3, 2200.00, '2024-06-01', NULL, 'CDI', 3, 3),
(4, 1800.00, '2023-11-01', '2025-12-20', 'Essai', 6, 4),
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


INSERT INTO type_conge (type, pourcentage_salaire) VALUES
('annuel', 100),
('sans_solde', 0),
('maladie', 100),
('maternite', 50),
('paternite', 0),
('exceptionnel', 100);

-- Exemples de conges (date_demande minimum 14 jours avant date_debut)
INSERT INTO conge (id_employe, id_type_conge, date_demande, date_debut, date_fin, raison, date_validation, status) VALUES
(1, 1, '2024-10-15', '2024-11-01', '2024-11-15', 'Vacances annuelles', '2024-10-20', 21),
(2, 1, '2024-09-20', '2024-10-10', '2024-10-25', 'Conge annuel', '2024-09-25', 21),
(3, 2, '2024-11-01', '2024-11-15', '2024-11-20', 'Maladie - grippe', '2024-11-05', 11),
(4, 1, '2024-08-15', '2024-09-01', '2024-09-10', 'Vacances ete', '2024-08-20', 21),
(5, 3, '2024-06-01', '2024-06-15', '2024-08-15', 'Conge maternite', '2024-06-05', 21),
(6, 1, '2024-12-01', '2024-12-15', '2024-12-30', 'Noël en famille', NULL, 1),
(7, 4, '2024-07-01', '2024-07-15', '2024-07-17', 'Naissance enfant', '2024-07-05', 21),
(8, 1, '2024-05-15', '2024-06-01', '2024-06-15', 'Voyage personnel', '2024-05-20', 21),
(9, 2, '2024-10-20', '2024-11-05', '2024-11-10', 'Consultation medicale', NULL, 1),
(10, 1, '2024-04-01', '2024-04-15', '2024-04-30', 'Mariage', '2024-04-05', 21),
(11, 5, '2024-03-15', '2024-04-01', '2024-04-03', 'Decès parent', '2024-03-20', 21),
(12, 1, '2024-11-15', '2024-12-01', '2024-12-10', 'Vacances de fin annee', NULL, 1),
(13, 1, '2024-02-01', '2024-02-15', '2024-03-01', 'Voyage touristique', '2024-02-05', 21),
(14, 2, '2024-09-10', '2024-09-25', '2024-09-30', 'Operation chirurgicale', '2024-09-15', 11),
(15, 1, '2024-01-15', '2024-02-01', '2024-02-15', 'Formation professionnelle', '2024-01-20', 21),

-- Conges plus recents (2025-2026)
(1, 1, '2025-09-15', '2025-10-01', '2025-10-15', 'Vacances annuelles 2025', '2025-09-20', 21),
(2, 2, '2025-10-01', '2025-10-15', '2025-10-20', 'Maladie - angine', '2025-10-05', 11),
(3, 1, '2025-08-20', '2025-09-05', '2025-09-20', 'Conge annuel ete 2025', '2025-08-25', 21),
(4, 6, '2025-10-15', '2025-10-29', '2025-10-31', 'Formation externe', '2025-10-18', 21),
(5, 1, '2025-11-01', '2025-11-15', '2025-11-30', 'Vacances de fin annee', NULL, 1),
(6, 2, '2025-09-25', '2025-10-10', '2025-10-15', 'Consultation dentaire', '2025-09-30', 11),
(7, 1, '2025-07-15', '2025-08-01', '2025-08-15', 'Voyage familial', '2025-07-20', 21),
(8, 6, '2025-10-20', '2025-11-05', '2025-11-07', 'Conference technique', NULL, 1),
(9, 1, '2025-06-01', '2025-06-15', '2025-06-30', 'Vacances ete 2025', '2025-06-05', 21),
(10, 2, '2025-11-01', '2025-11-15', '2025-11-18', 'Grippe saisonniere', NULL, 1),
(11, 1, '2025-10-10', '2025-10-25', '2025-11-10', 'Voyage touristique', '2025-10-15', 21),
(12, 6, '2025-09-01', '2025-09-15', '2025-09-17', 'Seminaire entreprise', '2025-09-05', 21),
(13, 1, '2025-08-01', '2025-08-15', '2025-08-30', 'Conge estival', '2025-08-05', 21),
(14, 2, '2025-10-25', '2025-11-10', '2025-11-15', 'Examen medical', NULL, 1),
(15, 1, '2025-07-01', '2025-07-15', '2025-07-31', 'Vacances juillet', '2025-07-05', 21),

-- Conges 2026 (futurs)
(1, 1, '2025-11-15', '2025-12-01', '2025-12-15', 'Noël 2025', NULL, 1),
(2, 1, '2025-12-01', '2025-12-15', '2025-12-31', 'Vacances fin annee', NULL, 1),
(3, 6, '2025-11-20', '2026-01-05', '2026-01-10', 'Formation nouvelle annee', NULL, 1),
(4, 1, '2025-10-30', '2026-01-15', '2026-01-30', 'Vacances hiver', NULL, 1),
(5, 3, '2025-11-10', '2025-11-25', '2026-01-25', 'Conge maternite', '2025-11-15', 11),
(6, 1, '2025-09-30', '2025-11-15', '2025-11-30', 'Voyage prevu', '2025-10-05', 21),
(7, 4, '2025-10-15', '2025-10-29', '2025-10-31', 'Naissance deuxieme enfant', '2025-10-20', 21),
(8, 1, '2025-11-05', '2025-11-20', '2025-12-05', 'Vacances prolongees', NULL, 1),
(9, 2, '2025-10-28', '2025-11-12', '2025-11-17', 'Operation programmee', NULL, 1),
(10, 1, '2025-08-15', '2025-09-01', '2025-09-15', 'Anniversaire mariage', '2025-08-20', 21),

-- Congés supplémentaires futurs (à partir du 17 novembre 2025)
(1, 1, '2025-11-01', '2025-12-01', '2025-12-10', 'Vacances de Noel', NULL, 1),
(2, 1, '2025-11-10', '2025-12-15', '2025-12-31', 'Reveillon du Nouvel An', NULL, 1),
(3, 6, '2025-11-15', '2026-01-05', '2026-01-15', 'Formation avancée', NULL, 1),
(4, 1, '2025-11-20', '2026-01-20', '2026-02-05', 'Vacances hiver', NULL, 1),
(5, 3, '2025-11-25', '2026-02-01', '2026-04-01', 'Congé maternité prolongé', NULL, 1),
(6, 1, '2025-11-30', '2026-02-15', '2026-03-01', 'Voyage en Europe', NULL, 1),
(7, 4, '2025-12-01', '2026-03-01', '2026-03-05', 'Naissance troisième enfant', NULL, 1),
(8, 1, '2025-12-05', '2026-03-10', '2026-03-25', 'Vacances de printemps', NULL, 1),
(9, 2, '2025-12-10', '2026-03-15', '2026-03-20', 'Chirurgie programmée', NULL, 1),
(10, 1, '2025-12-15', '2026-04-01', '2026-04-15', 'Pâques en famille', NULL, 1),
(11, 1, '2025-12-20', '2026-04-20', '2026-05-05', 'Voyage en Asie', NULL, 1),
(12, 6, '2025-12-25', '2026-05-01', '2026-05-10', 'Conférence internationale', NULL, 1),
(13, 1, '2026-01-01', '2026-05-15', '2026-05-30', 'Vacances été anticipées', NULL, 1),
(14, 2, '2026-01-05', '2026-06-01', '2026-06-10', 'Contrôle médical annuel', NULL, 1),
(15, 1, '2026-01-10', '2026-06-15', '2026-07-01', 'Grand voyage familial', NULL, 1),

-- Congés supplémentaires avec statuts validés (21) et en cours (11) - TOUTES DATES FUTURES
(1, 1, '2025-11-18', '2025-12-01', '2025-12-15', 'Vacances prolongées validées', '2025-11-20', 21),
(2, 2, '2025-11-19', '2025-12-05', '2025-12-10', 'Maladie - convalescence', '2025-11-22', 11),
(3, 6, '2025-11-20', '2025-12-10', '2025-12-15', 'Formation validée', '2025-11-25', 21),
(4, 1, '2025-11-21', '2025-12-20', '2026-01-05', 'Congé annuel approuvé', '2025-11-25', 21),
(5, 3, '2025-11-22', '2026-01-10', '2026-03-10', 'Congé maternité en cours', '2025-11-25', 11),
(6, 1, '2025-11-23', '2026-01-15', '2026-01-30', 'Vacances été validées', '2025-11-26', 21),
(7, 4, '2025-11-24', '2026-02-01', '2026-02-05', 'Congé paternité approuvé', '2025-11-27', 21),
(8, 2, '2025-11-25', '2026-02-10', '2026-02-15', 'Consultation médicale en cours', '2025-11-28', 11),
(9, 1, '2025-11-26', '2026-02-20', '2026-03-10', 'Voyage professionnel validé', '2025-11-29', 21),
(10, 6, '2025-11-27', '2026-03-01', '2026-03-05', 'Séminaire approuvé', '2025-11-30', 21),
(10, 1, '2025-08-15', '2025-09-01', '2025-09-15', 'Anniversaire mariage', '2025-08-20', 21);

-- Réponses du chatbot RH
INSERT INTO chatbot_responses (keyword, response, is_dynamic) VALUES
('congé', 'Pour les congés, consultez votre dashboard ou contactez RH. Vous avez droit à 30 jours par an.', 1),
('horaire', 'Les horaires de travail sont de 8h à 17h du lundi au vendredi.', 0),
('paie', 'Votre paie est versée le 30 de chaque mois. Consultez vos bulletins dans "Mes bulletins de paie".', 0),
('absence', 'En cas d\'absence, informez votre supérieur et RH au plus tôt.', 0),
('retard', 'Les retards doivent être justifiés. Plus de 3 retards par mois peuvent affecter votre salaire.', 0),
('prime', 'Les primes sont versées selon les performances et les politiques de l\'entreprise.', 0),
('salaire', 'Votre salaire est indiqué dans votre contrat. Pour les augmentations, discutez avec votre manager.', 1),
('demander congé', 'Guide pour demander un congé.', 1),
('pointer', 'Guide pour pointer votre présence.', 1),
('bulletin', 'Guide pour consulter votre salaire.', 1),
('profil', 'Vos informations personnelles.', 1),
('mes infos', 'Vos informations personnelles.', 1),
('bonjour', 'Salut ! Comment puis-je vous aider avec vos questions RH ?', 0),
('salut', 'Hey ! Que puis-je faire pour vous ?', 0),
('aide', 'Je peux vous aider avec : congés, salaire, horaires, demandes, etc. Posez votre question !', 0);


INSERT INTO chatbot_synonyms (keyword_id, synonym) VALUES
((SELECT id FROM chatbot_responses WHERE keyword = 'congé'), 'vacances'),
((SELECT id FROM chatbot_responses WHERE keyword = 'congé'), 'repos'),
((SELECT id FROM chatbot_responses WHERE keyword = 'congé'), 'absence'),
((SELECT id FROM chatbot_responses WHERE keyword = 'paie'), 'salaire'),
((SELECT id FROM chatbot_responses WHERE keyword = 'paie'), 'rémunération'),
((SELECT id FROM chatbot_responses WHERE keyword = 'paie'), 'paye'),
((SELECT id FROM chatbot_responses WHERE keyword = 'horaire'), 'heures'),
((SELECT id FROM chatbot_responses WHERE keyword = 'horaire'), 'temps de travail'),
((SELECT id FROM chatbot_responses WHERE keyword = 'attestation'), 'certificat'),
((SELECT id FROM chatbot_responses WHERE keyword = 'attestation'), 'document');

INSERT INTO conge (id_employe, id_type_conge, date_demande, date_debut, date_fin, raison, date_validation, status) VALUES 
(1, 1, '2025-01-10', '2025-02-01', '2025-02-05', 'Vacances familiales', '2025-01-15', 21),
(1, 1, '2025-03-15', '2025-04-01', '2025-04-03', 'Repos', '2025-03-20', 21),
(2, 1, '2025-02-01', '2025-02-15', '2025-02-20', 'Vacances', '2025-02-05', 21),
(2, 2, '2025-03-10', '2025-03-11', '2025-03-13', 'Maladie', '2025-03-10', 21);

INSERT INTO heure_pointage (id_poste, heure_debut, heure_fin) VALUES 
(1, '08:00:00', '17:00:00'), -- Développeur : 8h-17h
(2, '08:30:00', '17:30:00'), -- Commercial : 8h30-17h30
(3, '07:30:00', '16:30:00'); -- Comptable : 7h30-16h30

INSERT INTO heure_sup (type, pourcentage_majoration) VALUES 
('week-end', 50),
('nuit', 25),
('jour_ferie', 100),
('imprevu', 30);

-- Additional employees for turnover statistics
INSERT INTO employe (nom, prenom, date_naissance, email, mot_de_passe, sexe, telephone, adresse, numero_cnaps) VALUES
('Dupont', 'Jean', '1990-01-15', 'jean.dupont@email.com', '123', 'Homme', '0341234567', 'Antananarivo', 123456789),
('Martin', 'Marie', '1985-05-20', 'marie.martin@email.com', '123', 'Femme', '0339876543', 'Toamasina', 987654321),
('Bernard', 'Paul', '1992-03-10', 'paul.bernard@email.com', '123', 'Homme', '0324567890', 'Fianarantsoa', 456789123),
('Dubois', 'Sophie', '1988-11-25', 'sophie.dubois@email.com', '123', 'Femme', '0345678901', 'Mahajanga', 789123456),
('Thomas', 'Marc', '1995-07-08', 'marc.thomas@email.com', '123', 'Homme', '0330123456', 'Toliara', 321654987);

-- Contracts for new employees, some ending in November 2025 for turnover
INSERT INTO contrat (id_employe, salaire, date_debut, date_fin, type, id_poste, id_departement) VALUES
((SELECT id_employe FROM employe WHERE nom = 'Dupont' AND prenom = 'Jean'), 3500.00, '2025-01-01', '2025-11-30', 'CDD', 3, 1),
((SELECT id_employe FROM employe WHERE nom = 'Martin' AND prenom = 'Marie'), 2800.00, '2025-03-01', NULL, 'CDI', 4, 2),
((SELECT id_employe FROM employe WHERE nom = 'Bernard' AND prenom = 'Paul'), 2200.00, '2025-06-01', '2025-11-15', 'CDD', 6, 3),
((SELECT id_employe FROM employe WHERE nom = 'Dubois' AND prenom = 'Sophie'), 1800.00, '2025-09-01', NULL, 'CDI', 7, 4),
((SELECT id_employe FROM employe WHERE nom = 'Thomas' AND prenom = 'Marc'), 2400.00, '2025-10-01', '2025-11-27', 'Essai', 5, 5);