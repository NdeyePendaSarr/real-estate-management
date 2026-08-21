-- ============================================================================
-- Jeu de données de démonstration (anonymisé).
-- Mot de passe de TOUS les comptes de démo : Demo1234!
-- À exécuter APRÈS schema.sql.
-- ============================================================================

USE agence_immobiliere;

-- --- Utilisateurs (hash bcrypt de « Demo1234! ») ---
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES
('DIOP',   'Awa',      'client@demo.example',     '+221770000001', '$2y$10$fMwiWiWEHGPvylg6rYSKTeK1mQmQmhRhf.Bh5eLwKMo5wsx3jkAuy', 'client'),
('NDIAYE', 'Cheikh',   'client2@demo.example',    '+221770000002', '$2y$10$fMwiWiWEHGPvylg6rYSKTeK1mQmQmhRhf.Bh5eLwKMo5wsx3jkAuy', 'client'),
('SOW',    'Aminata',  'commercial@demo.example', '+221770000003', '$2y$10$fMwiWiWEHGPvylg6rYSKTeK1mQmQmhRhf.Bh5eLwKMo5wsx3jkAuy', 'commercial'),
('BA',     'Ibrahima', 'admin@demo.example',      '+221770000004', '$2y$10$fMwiWiWEHGPvylg6rYSKTeK1mQmQmhRhf.Bh5eLwKMo5wsx3jkAuy', 'admin');

-- --- Biens (2 appartements + 5 villas) ---
INSERT INTO biens (id, type, titre, description, prix, ville, chambres, surface, statut) VALUES
(1, 'appartement', 'Appartement haut standing au Plateau',
 'Appartement haut standing dans un immeuble récent au Plateau, Dakar. 6e étage, 3 chambres climatisées avec placards et salle de bain, grand salon donnant sur un balcon avec vue mer, cuisine équipée, toilette visiteur et chauffe-eau. Commodités : parking, ascenseur, groupe électrogène et sécurité 24h/24.',
 1500000, 'Dakar', 3, 140, 'disponible'),
(2, 'appartement', 'Appartement F4 au Point E',
 'Appartement de type F4 de 191 m² au Point E, Dakar. 3 grandes chambres avec placards et salle d''eau, grand salon, cuisine équipée avec buanderie, espace familial, grande terrasse privée, toilette visiteur. Ascenseur, groupe électrogène, salle polyvalente, conciergerie, gardiennage 24h/24.',
 2000000, 'Dakar', 3, 191, 'disponible'),
(3, 'villa', 'Villa avec piscine à Mermoz',
 'Villa à Mermoz composée d''un salon, d''une cuisine bien équipée, de toilettes, avec vue sur la mer, 10 chambres, un grand jardin et une grande piscine.',
 1500000, 'Dakar', 10, 400, 'disponible'),
(4, 'villa', 'Villa meublée à Gorée (courte durée)',
 'Très belle maison de type colonial à Gorée, en face de la banque, proche plage, restaurants et embarcadère. 5 chambres climatisées avec salle de bain, un salon, une salle à manger, cuisine équipée. Extérieur calme. Location meublée. Durée maximum : 3 mois.',
 150000, 'Gorée', 5, 220, 'disponible'),
(5, 'villa', 'Villa R+1 au Point E',
 'Grande villa non meublée en R+1 au Point E, zone très accessible en bordure de route. 4 chambres, 2 salons, grande cuisine, double garage, jardin, 3 salles d''eau, toilette visiteur.',
 1700000, 'Dakar', 4, 300, 'loue'),
(6, 'villa', 'Villa 4 chambres au Virage',
 'Maison de 4 chambres salon équipées de split, salle à manger, grande cuisine. Toutes les chambres avec rangements, 3 salles de bain, toilette visiteur et une pour le personnel, double garage, surpresseur et jardin.',
 1500000, 'Dakar', 4, 280, 'disponible'),
(7, 'villa', 'Villa R+2 aux Mamelles',
 'Splendide villa R+2 avec de belles finitions aux Mamelles Extension : 5 chambres dont 3 avec salle d''eau et chauffe-eau, grand salon climatisé, buanderie, cuisine africaine et américaine, espace familial, deux terrasses, réservoir d''eau, jardin, interphone.',
 900000, 'Dakar', 5, 350, 'disponible');

-- --- Images (chemins relatifs à /public) ---
INSERT INTO images (bien_id, fichier, principale) VALUES
(1, 'assets/images/appart-plateau-1.jpg', 1),
(1, 'assets/images/appart-plateau-2.jpg', 0),
(2, 'assets/images/appart-pointe-1.jpg', 1),
(2, 'assets/images/appart-pointe-2.jpg', 0),
(3, 'assets/images/villa-mermoz-1.jpg', 1),
(3, 'assets/images/villa-mermoz-2.jpg', 0),
(4, 'assets/images/villa-goree-1.jpg', 1),
(4, 'assets/images/villa-goree-2.jpg', 0),
(5, 'assets/images/villa-pointe-1.jpg', 1),
(5, 'assets/images/villa-pointe-2.jpg', 0),
(6, 'assets/images/villa-virage-1.jpg', 1),
(6, 'assets/images/villa-virage-2.jpg', 0),
(7, 'assets/images/villa-mamelles-1.jpg', 1),
(7, 'assets/images/villa-mamelles-2.jpg', 0);

-- --- Favoris (client Awa) ---
INSERT INTO favoris (utilisateur_id, bien_id) VALUES
(1, 3), (1, 7);

-- --- Réservations ---
INSERT INTO reservations (bien_id, utilisateur_id, date_debut, date_fin, statut) VALUES
(2, 1, '2026-09-01', '2026-09-30', 'en_attente'),
(7, 2, '2026-10-05', '2026-10-20', 'confirmee');

-- --- Paiement lié à la réservation confirmée ---
INSERT INTO paiements (reservation_id, montant, date_paiement) VALUES
(2, 900000, '2026-10-01');
