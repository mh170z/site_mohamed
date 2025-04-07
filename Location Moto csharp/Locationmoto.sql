-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 28 mars 2025 à 16:29
-- Version du serveur : 5.7.24
-- Version de PHP : 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `locationmontagne`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `idArticle` int(11) NOT NULL,
  `nomArticle` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `tarif` decimal(7,2) NOT NULL,
  `quantiteStock` int(4) NOT NULL,
  `image` varchar(250) DEFAULT NULL,
  `idCategorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `article`
--
INSERT INTO `moto` (`idMoto`, `nomMoto`, `description`, `tarif`, `quantiteStock`, `image`, `idCategorie`) VALUES
(1, 'Yamaha MT-07', 'La Yamaha MT-07 est un modèle incontournable, parfaite pour les trajets urbains et les sorties sur route. Avec son moteur bicylindre de 689cc, elle offre une excellente maniabilité et des performances accrues pour un pilotage agréable et sécurisé.', '79.00', 15, 'c9b02be3-b503-4c89-9e56-d84cd5f06093.png', 2),
(2, 'KTM Duke 390', 'La KTM Duke 390 est une moto légère mais puissante, idéale pour les motards à la recherche d’une conduite sportive et dynamique. Son moteur monocylindre de 373cc est conçu pour offrir une expérience de conduite fluide et excitante.', '65.00', 25, 'f4d57c56-53c5-4b71-b42a-2be3e23a2a9d.jpeg', 3);


-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `idCategorie` int(11) NOT NULL,
  `nomCategorie` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`idCategorie`, `nomCategorie`) VALUES
(1, 'Batons'),
(2, 'Sac à dos'),
(3, 'VTT');

-- --------------------------------------------------------

--
-- Structure de la table `etatlocation`
--

CREATE TABLE `etatlocation` (
  `idEtatLocation` int(11) NOT NULL,
  `nomEtatLocation` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `etatlocation`
--

INSERT INTO `etatlocation` (`idEtatLocation`, `nomEtatLocation`) VALUES
(1, 'En attente de paiement'),
(2, 'Payée'),
(3, 'En cours'),
(4, 'Retournée'),
(5, 'Annulée');

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE `facture` (
  `idFacture` int(11) NOT NULL,
  `dateFacture` date NOT NULL,
  `montant` decimal(7,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `facture`
--

INSERT INTO `facture` (`idFacture`, `dateFacture`, `montant`) VALUES
(1, '2025-02-18', '1900.00'),
(2, '2025-02-18', '3900.00'),
(3, '2025-03-22', '1200.00'),
(4, '2025-03-25', '12290.00');

-- --------------------------------------------------------

--
-- Structure de la table `location`
--

CREATE TABLE `location` (
  `idLocation` int(11) NOT NULL,
  `dateLocation` date NOT NULL,
  `dateDebutLocation` date NOT NULL,
  `dateFinLocation` date NOT NULL,
  `dateRetourArticle` date DEFAULT NULL,
  `idUser` int(11) NOT NULL,
  `idEtatLocation` int(11) NOT NULL,
  `idFacture` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `location`
--

INSERT INTO `location` (`idLocation`, `dateLocation`, `dateDebutLocation`, `dateFinLocation`, `dateRetourArticle`, `idUser`, `idEtatLocation`, `idFacture`) VALUES
(3, '2025-02-18', '2025-02-20', '2025-02-22', '2025-03-22', 3, 4, 1),
(4, '2025-02-18', '2025-02-18', '2025-02-19', NULL, 3, 5, 2),
(5, '2025-03-22', '2025-03-22', '2025-03-24', NULL, 4, 2, 3),
(6, '2025-03-25', '2025-03-26', '2025-03-29', NULL, 3, 2, 4);

-- --------------------------------------------------------

--
-- Structure de la table `locationarticle`
--

CREATE TABLE `locationarticle` (
  `idLocation` int(11) NOT NULL,
  `idArticle` int(11) NOT NULL,
  `quantite` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `locationarticle`
--

INSERT INTO `locationarticle` (`idLocation`, `idArticle`, `quantite`) VALUES
(3, 1, 2),
(4, 1, 5),
(5, 3, 1),
(6, 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `idUser` int(11) NOT NULL,
  `nom` varchar(40) NOT NULL,
  `prenom` varchar(40) NOT NULL,
  `email` varchar(40) NOT NULL,
  `login` varchar(40) NOT NULL,
  `password` varchar(200) NOT NULL,
  `dateNaiss` date NOT NULL,
  `adresse` varchar(80) NOT NULL,
  `estEmploye` tinyint(1) NOT NULL DEFAULT '0',
  `idVille` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user`
--
INSERT INTO `user` (`idUser`, `nom`, `prenom`, `email`, `login`, `password`, `dateNaiss`, `adresse`, `estEmploye`, `idVille`) VALUES
(1, 'Dupont', 'Pierre', 'pierre.dupont@example.com', 'pierre01', '$2a$12$CAw24K.ND7O9z3HfaA.XmI0lMcl9f3Rf2JFPwZs8tD6rrbLgNlqUG', '1990-03-15', '45 rue des Champs', 1, 2),
(2, 'Martin', 'Sophie', 'sophie.martin@example.com', 'sophie_m', '$2a$12$CAw24K.ND7O9z3HfaA.XmI0lMcl9f3Rf2JFPwZs8tD6rrbLgNlqUG', '1985-07-22', '12 avenue des Fleurs', 0, 3),
(3, 'Lemoine', 'Julien', 'julien.lemoine@example.com', 'julien85', '$2a$12$CAw24K.ND7O9z3HfaA.XmI0lMcl9f3Rf2JFPwZs8tD6rrbLgNlqUG', '1992-10-30', '3 rue de la Liberté', 0, 1),
(4, 'Benoit', 'Claire', 'claire.benoit@example.com', 'claire_b', '$2a$12$CAw24K.ND7O9z3HfaA.XmI0lMcl9f3Rf2JFPwZs8tD6rrbLgNlqUG', '1994-01-19', '56 boulevard des Roses', 0, 2);


-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

CREATE TABLE `ville` (
  `idVille` int(11) NOT NULL,
  `nomVille` varchar(40) NOT NULL,
  `codePostal` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`idVille`, `nomVille`, `codePostal`) VALUES
(1, 'Viry-Châtillon', '91170'),
(2, 'Grigny', '91350'),
(3, 'Savigny-sur-orge', '91600'),
(4, 'Ris Orangis', '91150');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`idArticle`),
  ADD KEY `Article_Categorie_FK` (`idCategorie`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`idCategorie`);

--
-- Index pour la table `etatlocation`
--
ALTER TABLE `etatlocation`
  ADD PRIMARY KEY (`idEtatLocation`);

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`idFacture`);

--
-- Index pour la table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`idLocation`),
  ADD KEY `Location_User_FK` (`idUser`),
  ADD KEY `Location_EtatLocation0_FK` (`idEtatLocation`),
  ADD KEY `Location_Facture1_FK` (`idFacture`);

--
-- Index pour la table `locationarticle`
--
ALTER TABLE `locationarticle`
  ADD PRIMARY KEY (`idLocation`,`idArticle`),
  ADD KEY `locationArticle_Article0_FK` (`idArticle`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idUser`),
  ADD KEY `User_Ville_FK` (`idVille`);

--
-- Index pour la table `ville`
--
ALTER TABLE `ville`
  ADD PRIMARY KEY (`idVille`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `idArticle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `idCategorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `etatlocation`
--
ALTER TABLE `etatlocation`
  MODIFY `idEtatLocation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
  MODIFY `idFacture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `location`
--
ALTER TABLE `location`
  MODIFY `idLocation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `idUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `ville`
--
ALTER TABLE `ville`
  MODIFY `idVille` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `Article_Categorie_FK` FOREIGN KEY (`idCategorie`) REFERENCES `categorie` (`idCategorie`);

--
-- Contraintes pour la table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `Location_EtatLocation0_FK` FOREIGN KEY (`idEtatLocation`) REFERENCES `etatlocation` (`idEtatLocation`),
  ADD CONSTRAINT `Location_Facture1_FK` FOREIGN KEY (`idFacture`) REFERENCES `facture` (`idFacture`),
  ADD CONSTRAINT `Location_User_FK` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`);

--
-- Contraintes pour la table `locationarticle`
--
ALTER TABLE `locationarticle`
  ADD CONSTRAINT `locationArticle_Article0_FK` FOREIGN KEY (`idArticle`) REFERENCES `article` (`idArticle`),
  ADD CONSTRAINT `locationArticle_Location_FK` FOREIGN KEY (`idLocation`) REFERENCES `location` (`idLocation`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `User_Ville_FK` FOREIGN KEY (`idVille`) REFERENCES `ville` (`idVille`);

DELIMITER $$
--
-- Évènements
--
CREATE DEFINER=`root`@`localhost` EVENT `update_location_status` ON SCHEDULE EVERY 1 DAY STARTS '2025-02-17 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE location 
    SET idEtatLocation = 3  -- État "En cours"
    WHERE idEtatLocation = 2  -- État "Payée"
    AND DATE(dateDebutLocation) = CURRENT_DATE();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
