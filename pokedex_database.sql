-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : sam. 03 avr. 2021 à 19:03
-- Version du serveur :  10.3.28-MariaDB-1:10.3.28+maria~focal
-- Version de PHP : 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pokedex`
--

-- --------------------------------------------------------

--
-- Structure de la table `Attack`
--

CREATE TABLE `Attack` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `Attack`
--

INSERT INTO `Attack` (`id`, `name`, `type_id`) VALUES
(1, 'Absorb', 1),
(2, 'Acid', 2),
(3, 'Agility', 14),
(4, 'Amnesia', 14),
(5, 'Barrage', 3),
(6, 'Barrier', 14),
(7, 'Bite', 14),
(8, 'Confuse ray', 9),
(9, 'Cut', 3),
(10, 'Earthquake', 6),
(11, 'Thunderbolt', 12),
(12, 'Fire Blast', 13),
(13, 'Quick Attack', 3),
(14, 'Bubble', 11),
(15, 'Fly', 5),
(16, 'Lovely Kiss', 3),
(17, 'Light Screen', 14),
(18, 'Smockscreen', 3),
(19, 'Dig', 6);

-- --------------------------------------------------------

--
-- Structure de la table `Pokemon`
--

CREATE TABLE `Pokemon` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Pokemon_Attack`
--

CREATE TABLE `Pokemon_Attack` (
  `pokemon_id` int(11) NOT NULL,
  `attack_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Pokemon_Team`
--

CREATE TABLE `Pokemon_Team` (
  `pokemon_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Pokemon_Type`
--

CREATE TABLE `Pokemon_Type` (
  `type_id` int(11) NOT NULL,
  `pokemon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Role`
--

CREATE TABLE `Role` (
  `id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Team`
--

CREATE TABLE `Team` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `Type`
--

CREATE TABLE `Type` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `Type`
--

INSERT INTO `Type` (`id`, `name`) VALUES
(1, 'Grass'),
(2, 'Poison'),
(3, 'Normal'),
(4, 'Fighting'),
(5, 'Flying'),
(6, 'Ground'),
(7, 'Rock'),
(8, 'Bug'),
(9, 'Ghost'),
(10, 'Steel'),
(11, 'Water'),
(12, 'Electric'),
(13, 'Fire'),
(14, 'Psychic'),
(15, 'Ice'),
(16, 'Dragon'),
(17, 'Dark'),
(18, 'Fairy');

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Attack`
--
ALTER TABLE `Attack`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`);

--
-- Index pour la table `Pokemon`
--
ALTER TABLE `Pokemon`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Pokemon_Attack`
--
ALTER TABLE `Pokemon_Attack`
  ADD PRIMARY KEY (`pokemon_id`,`attack_id`),
  ADD KEY `attack_id` (`attack_id`);

--
-- Index pour la table `Pokemon_Team`
--
ALTER TABLE `Pokemon_Team`
  ADD PRIMARY KEY (`pokemon_id`,`team_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Index pour la table `Pokemon_Type`
--
ALTER TABLE `Pokemon_Type`
  ADD PRIMARY KEY (`pokemon_id`,`type_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Index pour la table `Role`
--
ALTER TABLE `Role`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Team`
--
ALTER TABLE `Team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `Type`
--
ALTER TABLE `Type`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `team_id` (`team_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Attack`
--
ALTER TABLE `Attack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `Pokemon`
--
ALTER TABLE `Pokemon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `Role`
--
ALTER TABLE `Role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Team`
--
ALTER TABLE `Team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Type`
--
ALTER TABLE `Type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Attack`
--
ALTER TABLE `Attack`
  ADD CONSTRAINT `Attack_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `Type` (`id`);

--
-- Contraintes pour la table `Pokemon_Attack`
--
ALTER TABLE `Pokemon_Attack`
  ADD CONSTRAINT `Pokemon_Attack_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pokemon_Attack_ibfk_2` FOREIGN KEY (`attack_id`) REFERENCES `Attack` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `Pokemon_Team`
--
ALTER TABLE `Pokemon_Team`
  ADD CONSTRAINT `Pokemon_Team_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pokemon_Team_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `Team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `Pokemon_Type`
--
ALTER TABLE `Pokemon_Type`
  ADD CONSTRAINT `Pokemon_Type_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `Pokemon` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Pokemon_Type_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `Type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `Team`
--
ALTER TABLE `Team`
  ADD CONSTRAINT `Team_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`);

--
-- Contraintes pour la table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `Role` (`id`),
  ADD CONSTRAINT `User_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `Team` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
