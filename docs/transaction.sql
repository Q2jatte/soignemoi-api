-- Début de la transaction
BEGIN;

-- Étape 1: Création de l'utilisateur
INSERT INTO user (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `patient_id`, `doctor_id`, `profile_image_name`, `staff_id`)
VALUES (NULL, 'newuser@test.com', '["ROLE_USER"]', 'password', 'Camille', 'Cottin', NULL, NULL, NULL, NULL);

-- Récupération de l'ID de l'utilisateur nouvellement créé
SET @id_utilisateur := LAST_INSERT_ID();

-- Étape 2: Création du patient associé à l'utilisateur
INSERT INTO patient (`id`, `address`, `user_id`)
VALUES (NULL, 'Place victor Hugo 75016 Paris', @id_utilisateur);

-- Récupération de l'ID du patient nouvellement créé
SET @id_patient := LAST_INSERT_ID();

-- Étape 3: Réservation d'un séjour associé au patient
INSERT INTO stay (`id`, `entrance_date`, `discharge_date`, `reason`, @id_patient, `service_id`, `doctor_id`, `valid_entrance`, `valid_discharge`)
VALUES (NULL, '2024-02-05', '2024-02-06', 'Raison du séjour', '22', '19', '22', NULL, NULL);

-- Fin de la transaction (commit)
COMMIT;
