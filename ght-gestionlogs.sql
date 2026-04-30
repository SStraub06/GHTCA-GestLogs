-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 30 avr. 2026 à 12:28
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ght-gestionlogs`
--

-- --------------------------------------------------------

--
-- Structure de la table `connexions`
--

DROP TABLE IF EXISTS `connexions`;
CREATE TABLE IF NOT EXISTS `connexions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_poste` int NOT NULL,
  `nom_utilisateur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serveur_auth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_connexion` datetime DEFAULT NULL,
  `date_deconnexion` datetime DEFAULT NULL,
  `uptime` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imprimante_defaut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `liste_imprimantes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lecteurs_reseaux` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `temps_execution_script` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_poste` (`id_poste`),
  KEY `idx_user` (`nom_utilisateur`(250)),
  KEY `idx_date` (`date_connexion`)
) ENGINE=MyISAM AUTO_INCREMENT=151087 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `connexions`
--

INSERT INTO `connexions` (`id`, `id_poste`, `nom_utilisateur`, `adresse_ip`, `serveur_auth`, `date_connexion`, `date_deconnexion`, `uptime`, `imprimante_defaut`, `liste_imprimantes`, `lecteurs_reseaux`, `temps_execution_script`) VALUES
(151075, 1, 'user_gueb_01', '10.10.1.11', '\\\\AD-GUEBWILLER', '2023-11-14 06:12:00', '2023-11-14 14:48:00', '8 h 36 min', 'HP LaserJet GUEB', 'HP LaserJet GUEB|PDFCreator|XPS Writer', 'H:|S:|T:', '0.11 s'),
(151076, 1, 'user_gueb_02', '10.10.1.12', '\\\\AD-GUEBWILLER', '2026-03-22 17:42:00', '2026-03-22 23:10:00', '5 h 28 min', 'HP LaserJet GUEB', 'HP LaserJet GUEB|PDFCreator', 'H:|S:', '0.09 s'),
(151077, 2, 'user_ensi_01', '10.20.2.21', '\\\\AD-ENSISHEIM', '2024-02-03 22:48:00', '2024-02-04 06:05:00', '7 h 17 min', 'Brother ENSI', 'Brother ENSI|PDFCreator', 'H:|S:|U:', '0.12 s'),
(151078, 2, 'user_ensi_02', '10.20.2.22', '\\\\AD-ENSISHEIM', '2026-04-12 08:33:00', '2026-04-12 16:02:00', '7 h 29 min', 'Brother ENSI', 'Brother ENSI|PDFCreator|XPS Writer', 'H:|S:', '0.10 s'),
(151079, 3, 'user_munst_01', '10.30.3.31', '\\\\AD-MUNSTER', '2023-09-18 03:05:00', '2023-09-18 11:59:00', '8 h 54 min', 'Canon MUNST', 'Canon MUNST|PDFCreator', 'H:|S:|T:', '0.14 s'),
(151080, 3, 'user_munst_02', '10.30.3.32', '\\\\AD-MUNSTER', '2026-01-27 14:37:00', '2026-01-27 20:55:00', '6 h 18 min', 'Canon MUNST', 'Canon MUNST|PDFCreator|XPS Writer', 'H:|S:', '0.13 s'),
(151081, 4, 'user_rib_01', '10.40.4.41', '\\\\AD-RIB', '2024-06-09 05:22:00', '2024-06-09 13:40:00', '8 h 18 min', 'HP LaserJet RIB', 'HP LaserJet RIB|PDFCreator', 'H:|S:|U:', '0.12 s'),
(151082, 4, 'user_rib_02', '10.40.4.42', '\\\\AD-RIB', '2026-03-03 19:58:00', '2026-03-04 02:10:00', '6 h 12 min', 'HP LaserJet RIB', 'HP LaserJet RIB|PDFCreator|XPS Writer', 'H:|S:', '0.11 s'),
(151083, 5, 'user_soultz_01', '10.50.5.51', '\\\\AD-SOULTZ', '2023-12-21 07:14:00', '2023-12-21 15:02:00', '7 h 48 min', 'Lexmark SOULTZ', 'Lexmark SOULTZ|PDFCreator', 'H:|S:|T:', '0.10 s'),
(151084, 5, 'user_soultz_02', '10.50.5.52', '\\\\AD-SOULTZ', '2026-04-18 12:33:00', '2026-04-18 19:44:00', '7 h 11 min', 'Lexmark SOULTZ', 'Lexmark SOULTZ|PDFCreator|XPS Writer', 'H:|S:', '0.12 s'),
(151085, 6, 'user_pasteur_01', '10.60.6.61', '\\\\AD-PASTEUR', '2024-01-11 04:55:00', '2024-01-11 13:20:00', '8 h 25 min', 'HP LaserJet PAST', 'HP LaserJet PAST|PDFCreator', 'H:|S:|U:', '0.11 s'),
(151086, 6, 'user_pasteur_02', '10.60.6.62', '\\\\AD-PASTEUR', '2026-04-27 09:48:00', '2026-04-27 17:31:00', '7 h 43 min', 'HP LaserJet PAST', 'HP LaserJet PAST|PDFCreator|XPS Writer', 'H:|S:', '0.10 s');

-- --------------------------------------------------------

--
-- Structure de la table `postes`
--

DROP TABLE IF EXISTS `postes`;
CREATE TABLE IF NOT EXISTS `postes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_poste` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse_mac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_disque` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ram_gio` int DEFAULT NULL,
  `cpu_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cpu_freq_ghz` decimal(5,2) DEFAULT NULL,
  `os_version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `os_build` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `os_arch` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_firefox` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_chrome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_internet_explorer` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_dotnet` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_client_citrix` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_edictee` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_cws` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_philips_speech_drivers` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_dragon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_office` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_trend_micro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `version_cryptolib` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_site` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_poste` (`nom_poste`),
  UNIQUE KEY `nom_poste_2` (`nom_poste`),
  KEY `id_site` (`id_site`)
) ENGINE=MyISAM AUTO_INCREMENT=4680 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `postes`
--

INSERT INTO `postes` (`id`, `nom_poste`, `adresse_mac`, `type_disque`, `ram_gio`, `cpu_model`, `cpu_freq_ghz`, `os_version`, `os_build`, `os_arch`, `version_firefox`, `version_chrome`, `version_internet_explorer`, `version_dotnet`, `version_client_citrix`, `version_edictee`, `version_cws`, `version_philips_speech_drivers`, `version_dragon`, `version_office`, `version_trend_micro`, `version_cryptolib`, `id_site`) VALUES
(1, 'POSTE-GUEBWILLER-01', 'A1B2C3D4E5F6', 'SSD', 16, 'Intel Core i5-10400', '2.90', 'WIN_10', '19045', 'X64', '118.0', '120.0', '11.0.19041.1', '4.8.09037', '23.11', 'Absent', '5.3', '4.2', '15.3', '365 x64', '20.0.0', 'Absent', 1),
(2, 'POSTE-ENSISHEIM-01', 'B2C3D4E5F6A1', 'SSD', 8, 'Intel Core i3-9100', '3.60', 'WIN_10', '19045', 'X64', '117.0', '119.0', '11.0.19041.1', '4.8.09037', '23.10', '7.0', 'Absent', '4.0', '15.0', '2019 x64', '20.0.0', 'Absent', 2),
(3, 'POSTE-MUNSTER-01', 'C3D4E5F6A1B2', 'HDD', 8, 'Intel Core i5-7500', '3.40', 'WIN_10', '19045', 'X64', '119.0', '118.0', '11.0.19041.1', '4.8.09037', '23.11', '7.0', '5.2', 'Absent', '15.2', '2016 x64', '20.0.0', 'Absent', 3),
(4, 'POSTE-RIBEAUVILLE-01', 'D4E5F6A1B2C3', 'SSD', 32, 'Intel Core i7-10700', '2.90', 'WIN_11', '22631', 'X64', '121.0', '121.0', 'Absent', '4.8.09037', '23.12', '7.1', '5.4', '4.3', '15.4', '365 x64', '20.0.0', 'Absent', 4),
(5, 'POSTE-SOULTZ-01', 'E5F6A1B2C3D4', 'SSD', 16, 'AMD Ryzen 5 3600', '3.60', 'WIN_10', '19045', 'X64', '118.0', '120.0', '11.0.19041.1', '4.8.09037', '23.11', '7.0', 'Absent', '4.1', '15.1', '2019 x64', '20.0.0', 'Absent', 5),
(6, 'POSTE-PASTEUR-01', 'F6A1B2C3D4E5', 'SSD', 8, 'Intel Core i5-8400', '2.80', 'WIN_10', '19045', 'X64', '120.0', '119.0', '11.0.19041.1', '4.8.09037', '23.10', '7.0', '5.3', '4.2', 'Absent', '365 x64', '20.0.0', 'Absent', 6);

-- --------------------------------------------------------

--
-- Structure de la table `sites`
--

DROP TABLE IF EXISTS `sites`;
CREATE TABLE IF NOT EXISTS `sites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sites`
--

INSERT INTO `sites` (`id`, `nom`) VALUES
(1, 'Guebwiller'),
(2, 'Ensisheim_NeufBrisach'),
(3, 'Munster'),
(4, 'Ribeauvillé'),
(5, 'Soultz_Issenheim'),
(6, 'Pasteur');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `site_id` int DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `site_id`, `is_admin`) VALUES
(1, 'samuel.straub', '$2y$10$2SbVAVrpdiWJ0kUUaeAupuQXaUNs3ZT.ppXY5ikGUn4NURGE/oDd2', NULL, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
