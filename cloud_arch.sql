-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 06 sep. 2024 à 11:21
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cloud_arch`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20240828104920', '2024-08-31 15:09:21', 175),
('DoctrineMigrations\\Version20240828133645', '2024-08-31 15:09:22', 17),
('DoctrineMigrations\\Version20240828142946', '2024-08-31 15:09:22', 36),
('DoctrineMigrations\\Version20240828153522', '2024-08-31 15:09:22', 36),
('DoctrineMigrations\\Version20240904100329', '2024-09-04 10:03:39', 109);

-- --------------------------------------------------------

--
-- Structure de la table `file`
--

DROP TABLE IF EXISTS `file`;
CREATE TABLE IF NOT EXISTS `file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` double NOT NULL,
  `format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latest_changes` datetime DEFAULT NULL,
  `creation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8C9F3610A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `file`
--

INSERT INTO `file` (`id`, `user_id`, `name`, `size`, `format`, `latest_changes`, `creation`) VALUES
(3, 1, 'yow.png.png', 19486, 'image/png', '2024-09-01 15:46:08', '2024-08-31 15:36:24'),
(8, 1, 'Keycloak.png', 336641, 'image/png', NULL, '2024-09-02 14:07:39'),
(9, 1, 'Keycloak.png', 336641, 'image/png', NULL, '2024-09-02 14:08:22'),
(10, 1, 'Keycloak.png', 336641, 'image/png', NULL, '2024-09-02 14:08:31'),
(11, 1, 'Keycloak.png', 336641, 'image/png', NULL, '2024-09-02 14:08:39'),
(18, 1, 'Design sans titre (2).png', 19486, 'image/png', NULL, '2024-09-04 08:01:29'),
(21, 1, '004 Exercice 4.mp4', 21593283, 'video/mp4', NULL, '2024-09-04 14:40:13'),
(22, 5, 'KeycloakTest.png', 336641, 'image/png', '2024-09-05 13:06:24', '2024-09-05 13:06:12');

-- --------------------------------------------------------

--
-- Structure de la table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `object` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_ht` double NOT NULL,
  `quantity` int DEFAULT NULL,
  `unit_price_ht` double DEFAULT NULL,
  `amount_tva` double NOT NULL,
  `total_amount` double NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_90651744A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `invoice`
--

INSERT INTO `invoice` (`id`, `user_id`, `object`, `description`, `amount_ht`, `quantity`, `unit_price_ht`, `amount_tva`, `total_amount`, `date`) VALUES
(1, 1, 'Abonnement', 'Abonnement initial de 20 Go', 16.6, 1, 16.6, 3.4, 20, '2024-08-31 15:14:45'),
(5, 5, 'Abonnement', 'Abonnement initial de 20 Go', 16.6, 1, 16.6, 3.4, 20, '2024-09-02 14:11:51'),
(10, 1, 'Stockage', 'Ajout de 20 Go de stockage supplémentaire', 16.6, 1, 16.6, 3.4, 20, '2024-09-03 14:27:38'),
(17, 15, 'Abonnement', 'Abonnement initial de 20 Go', 16.6, 1, 16.6, 3.4, 20, '2024-09-04 10:09:41');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` json NOT NULL,
  `street_number` int NOT NULL,
  `street_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_storage` int NOT NULL,
  `registration_date` date NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `firstname`, `lastname`, `email`, `password`, `image_filename`, `roles`, `street_number`, `street_address`, `postal_code`, `city`, `country`, `total_storage`, `registration_date`, `phone`) VALUES
(1, 'John', 'Doe', 'John.doe@test.com', '$2y$13$gSydSat0CB3iSzavAuW7xuI3UDQ8cRdwCMaRwdl7jNqWdGSlzHahW', 'keycloak-66d6e00a77374.png', '[\"ROLE_ADMIN\", \"ROLE_USER\"]', 65, 'ST HILAIRE', '75010', 'Paris', 'FR', 40, '2024-09-03', '06123456789'),
(5, 'Test', 'Test', 'test@test.com', '$2y$13$c3/77cEGv/m4mmV4WLhiquRRafVWva3TKbm1x.w54KcPPEiWOeCKu', NULL, '[\"ROLE_USER\"]', 1, 'test', '75010', 'Paris', 'FR', 20, '2024-09-02', '06123456789'),
(15, 'test', 'test', 'eddy_john.972@hotmail.fr', '$2y$13$X7R2hsWBo605IcnyAlgcZezNgAPKFdE7FPXIHgtffp9SbU3utz34O', 'keycloak-66d882ddd5276.png', '[\"ROLE_USER\"]', 1, 'test', '75270', 'test', 'BS', 20, '2024-09-04', '06123456789');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `FK_8C9F3610A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK_90651744A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
