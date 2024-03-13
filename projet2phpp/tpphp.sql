-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Serveur: 127.0.0.1
-- Généré le : Mer 13 Mars 2024 à 10:47
-- Version du serveur: 5.5.10
-- Version de PHP: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `tpphp`
--

-- --------------------------------------------------------

--
-- Structure de la table `animation`
--

CREATE TABLE IF NOT EXISTS `animation` (
  `nomAnimation` varchar(50) NOT NULL,
  PRIMARY KEY (`nomAnimation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `animation`
--

INSERT INTO `animation` (`nomAnimation`) VALUES
('Foot'),
('Natation'),
('Ski');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE IF NOT EXISTS `eleves` (
  `num` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `tel` int(11) NOT NULL,
  `dateNaissance` date NOT NULL,
  `ville` varchar(50) NOT NULL,
  `niveau` varchar(50) NOT NULL,
  `commentaire` varchar(50) NOT NULL,
  `pp` varchar(100) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `eleves`
--

INSERT INTO `eleves` (`num`, `nom`, `adresse`, `tel`, `dateNaissance`, `ville`, `niveau`, `commentaire`, `pp`) VALUES
(1, 'aa', 'bb', 0, '0000-00-00', '', '', '', ''),
(2, 'zz', 'ee', 0, '0000-00-00', '', '', '', ''),
(3, 'yy', 'ff', 0, '0000-00-00', '', '', '', ''),
(150, 'idr', 'kad', 16024, '0000-00-00', '', '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `eleves_v2`
--

CREATE TABLE IF NOT EXISTS `eleves_v2` (
  `prenom` varchar(50) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `date_naiss` date NOT NULL,
  `email` varchar(50) NOT NULL,
  `tel` varchar(50) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `niveau` varchar(50) NOT NULL,
  `commentaire` varchar(50) NOT NULL,
  `moyenne` float NOT NULL,
  `identifiant` int(11) NOT NULL AUTO_INCREMENT,
  `animation` varchar(50) NOT NULL,
  PRIMARY KEY (`identifiant`),
  KEY `animation` (`animation`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Contenu de la table `eleves_v2`
--

INSERT INTO `eleves_v2` (`prenom`, `photo`, `nom`, `date_naiss`, `email`, `tel`, `adresse`, `ville`, `niveau`, `commentaire`, `moyenne`, `identifiant`, `animation`) VALUES
('dsf', 'logo_password.png', 'zz', '2024-03-09', 'aa.bb@gmail.com', '0622765974', 'aacc', 'Toulouse', 'BTS', 'abc', 0, 10, 'Foot'),
('qfsdsq', 'logo_login.png', 'zz', '2024-03-01', 'aa.zefe@gmail.com', '0688765974', 'qdsfdsf', 'Marseille', 'BTS', 'sdgg', 0, 11, 'Ski'),
('rzrjrj', 'pp.jpg', 'much', '2024-03-10', 'rezzhay@gmail.com', '0654564165', 'qfsdf', 'Toulouse', 'Licence', 'sdgfs', 0, 12, 'Natation');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(50) NOT NULL,
  `motdepasse` varchar(67) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`login`, `motdepasse`) VALUES
('aa', 'aa'),
('bb', 'bb'),
('masterrr', 'mastermdp'),
('mohaaa', 'mohamdp');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `eleves_v2`
--
ALTER TABLE `eleves_v2`
  ADD CONSTRAINT `eleves_v2_ibfk_1` FOREIGN KEY (`animation`) REFERENCES `animation` (`nomAnimation`) ON DELETE CASCADE ON UPDATE CASCADE;
