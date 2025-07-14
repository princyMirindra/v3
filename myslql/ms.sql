CREATE DATABASE IF NOT EXISTS db_s2_ETU003927;
USE db_s2_ETU003927;

CREATE TABLE membre (
    id_membre INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    genre ENUM('Homme', 'Femme', 'Autre') NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    ville VARCHAR(100) NOT NULL,
    mdp VARCHAR(255) NOT NULL,
    image_profil VARCHAR(255)
);

CREATE TABLE categorie_object (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE objet (
    id_objet INT AUTO_INCREMENT PRIMARY KEY,
    nom_objet VARCHAR(100) NOT NULL,
    id_categorie INT NOT NULL,
    id_membre INT NOT NULL,




ALTER TABLE objet ADD COLUMN date_disponible DATE;
ALTER TABLE objet ADD COLUMN disponible TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE emprunt ADD COLUMN etat_retour ENUM('OK', 'ABIMÉ') DEFAULT NULL;


    FOREIGN KEY (id_categorie) REFERENCES categorie_object(id_categorie),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);


CREATE TABLE images_object (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    nom_image VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet)
);

CREATE TABLE emprunt (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT NOT NULL,
    id_membre INT NOT NULL,
    date_emprunt DATE NOT NULL,
    date_retour DATE,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

INSERT INTO categorie_object (nom_categorie) VALUES 
('esthétique'), ('bricolage'), ('mécanique'), ('cuisine');

INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
('Jean Dupont', '1990-05-15', 'Homme', 'jean.dupont@email.com', 'Paris', 'password123', 'jean.jpg'),
('Marie Martin', '1985-08-22', 'Femme', 'marie.martin@email.com', 'Lyon', 'password123', 'marie.jpg'),
('Pierre Durand', '1992-11-30', 'Homme', 'pierre.durand@email.com', 'Marseille', 'password123', 'pierre.jpg'),
('Sophie Lambert', '1988-03-10', 'Femme', 'sophie.lambert@email.com', 'Toulouse', 'password123', 'sophie.jpg');

INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Perceuse électrique', 2, 1),
('Tournevis set', 2, 1),
('Pinceau maquillage', 1, 1);

INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Plaque à pâtisserie', 4, 2),
('Mascara', 1, 2),
('Scie sauteuse', 2, 2);

INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Fouet électrique', 4, 3),
('Pied à coulisse', 3, 3),
('Éplucheur', 4, 3);

INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Balance de cuisine', 4, 4),
('Pince à épiler', 1, 4),
('Valise à douille', 3, 4);

INSERT INTO images_object (id_objet, nom_image) VALUES
(1, 'perceuse.jpg'), (2, 'tournevis.jpg'), (3, 'pinceau.jpg'),
(4, 'plaque_patisserie.jpg'), (5, 'mascara.jpg'), (6, 'scie.jpg'),
(7, 'fouet.jpg'), (8, 'pied_coulisse.jpg'), (9, 'eplucheur.jpg'),
(10, 'balance.jpg'), (11, 'pince_epiler.jpg'), (12, 'valise_douille.jpg');

INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
(1, 2, '2025-06-01', '2025-06-15'),
(3, 4, '2025-06-05', '2025-06-20'),
(4, 3, '2025-06-10', NULL),
(5, 1, '2025-06-15', '2025-06-30'),
(6, 3, '2025-06-20', NULL),
(7, 4, '2025-06-25', '2025-07-10'),
(8, 2, '2025-07-01', NULL),
(9, 1, '2025-07-05', NULL),
(10, 2, '2025-07-10', NULL),
(11, 3, '2025-07-12', NULL);