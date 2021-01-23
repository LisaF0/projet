-- --------------------------------------------------------
-- Hôte :                        localhost
-- Version du serveur:           5.7.24 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Listage de la structure de la base pour projetlf
CREATE DATABASE IF NOT EXISTS `projetlf` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `projetlf`;

-- Listage de la structure de la table projetlf. appellation
CREATE TABLE IF NOT EXISTS `appellation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.appellation : ~4 rows (environ)
/*!40000 ALTER TABLE `appellation` DISABLE KEYS */;
INSERT INTO `appellation` (`id`, `name`) VALUES
	(1, 'grand cru'),
	(2, 'premier cru'),
	(3, 'communale'),
	(4, 'régionnale');
/*!40000 ALTER TABLE `appellation` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. domain
CREATE TABLE IF NOT EXISTS `domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.domain : ~5 rows (environ)
/*!40000 ALTER TABLE `domain` DISABLE KEYS */;
INSERT INTO `domain` (`id`, `name`) VALUES
	(1, 'chapuis'),
	(2, 'colin'),
	(3, 'meuneveaux'),
	(4, 'poisot'),
	(5, 'follin-arbelet');
/*!40000 ALTER TABLE `domain` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. facture
CREATE TABLE IF NOT EXISTS `facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordering_id` int(11) DEFAULT NULL,
  `facture_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FE8664108E6C7DE4` (`ordering_id`),
  CONSTRAINT `FK_FE8664108E6C7DE4` FOREIGN KEY (`ordering_id`) REFERENCES `ordering` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.facture : ~0 rows (environ)
/*!40000 ALTER TABLE `facture` DISABLE KEYS */;
/*!40000 ALTER TABLE `facture` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. ordering
CREATE TABLE IF NOT EXISTS `ordering` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ship_address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `facture_id` int(11) DEFAULT NULL,
  `ordering_reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `ordering_status` int(11) NOT NULL,
  `stripe_session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7B3133677F2DEE08` (`facture_id`),
  KEY `IDX_7B3133672F219D8E` (`ship_address_id`),
  KEY `IDX_7B313367A76ED395` (`user_id`),
  CONSTRAINT `FK_7B3133672F219D8E` FOREIGN KEY (`ship_address_id`) REFERENCES `ship_address` (`id`),
  CONSTRAINT `FK_7B3133677F2DEE08` FOREIGN KEY (`facture_id`) REFERENCES `facture` (`id`),
  CONSTRAINT `FK_7B313367A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.ordering : ~0 rows (environ)
/*!40000 ALTER TABLE `ordering` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordering` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appellation_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` double NOT NULL,
  `unit_stock` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activate` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D34A04AD7CDE30DD` (`appellation_id`),
  KEY `IDX_D34A04ADC54C8C93` (`type_id`),
  KEY `IDX_D34A04AD115F0EE5` (`domain_id`),
  CONSTRAINT `FK_B3BA5A5A115F0EE5` FOREIGN KEY (`domain_id`) REFERENCES `domain` (`id`),
  CONSTRAINT `FK_B3BA5A5A7CDE30DD` FOREIGN KEY (`appellation_id`) REFERENCES `appellation` (`id`),
  CONSTRAINT `FK_B3BA5A5AC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.product : ~26 rows (environ)
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` (`id`, `appellation_id`, `type_id`, `domain_id`, `reference`, `name`, `description`, `unit_price`, `unit_stock`, `available`, `photo`, `year`, `activate`) VALUES
	(21, 1, 2, 1, 'VIN5ffe0fdb1e1f8', 'Corton Perrières', 'Vin fin et élégant, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire et silicieux. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 25.39, 50, 1, 'cortonPerrieres2016Chapuis.jpg', '2016', 1),
	(22, 1, 2, 3, 'VIN5ffe1054424bb', 'Corton Perrières', 'Vin fin et élégant, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire et silicieux. Élevage en fût de 16 mois (30% de fût neuf). Vendange manuelle.', 29.99, 30, 1, 'cortonPerrieres2016Meuneveaux.jpg', '2016', 1),
	(23, 1, 2, 2, 'VIN5ffe10e3856d8', 'Corton Renardes', 'Vin rond et puissant, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle.', 24, 60, 1, 'cortonRenardes2018Colin.jpg', '2018', 1),
	(24, 1, 2, 4, 'VIN5ffe111d65ada', 'Corton Bressandes', 'Vin puissant avec du caractère, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire avec une terre rougeâtre (sol ferrugineux). Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 120, 15, 1, 'cortonBressandes2017Poisot.jpg', '2017', 1),
	(25, 1, 2, 4, 'VIN5ffe111decf59', 'Corton Bressandes', 'Vin puissant avec du caractère, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire avec une terre rougeâtre (sol ferrugineux). Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 120, 15, 1, 'cortonBressandes2017Poisot.jpg', '2017', 1),
	(26, 1, 2, 5, 'VIN5ffe114cba8cf', 'Corton Rouge', 'Compromis entre la finesse et l’élégance, ce corton rouge est implanté sur un terroir argilo-calcaire et marneux des Cortons Charlemagne de la montagne de Corton. Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 52.12, 50, 1, 'cortonRouge2018FollinArbelet.jpg', '2018', 1),
	(27, 1, 2, 4, 'VIN5ffe119056416', 'Romanée Saint-Vivant', 'La Romanée Saint-Vivant fait partie de l’élite des grands crus de la Côte de Nuit, le terroir est sur un sol brun calcaire fortement argileux. Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 150, 10, 1, 'romaneeSaintVivant2016Poisot.jpg', '2016', 1),
	(28, 2, 2, 2, 'VIN5ffe11fa8c2a5', 'Savigny-les-Beaune 1er Cru "Les Peuillets"', 'Vin tout en souplesse, issu d’un terroir argilo-calcaire. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle. Température de service : 14 à 16°C', 15, 100, 1, 'savignyLesBeaune1erCruLesPeuillets2017Colin.jpg', '2017', 1),
	(29, 2, 2, 2, 'VIN5ffe123b43e07', 'Beaune 1er Cru "Les Avaux"', 'Vin fin et rond en bouche, issu d’un terroir calcaire et marneux. Élevage en fût de 16 mois (10% de fût neuf). Vendanges manuelle.', 16, 120, 1, 'beaun1erCruLesAvaux2017Colin.jpg', '2017', 1),
	(30, 2, 2, 5, 'VIN5ffe12916a99f', 'Pernand-Vergelesses 1er Cru "Les Fichots"', 'Vin rond et structuré, issu d’un terroir calcaire et très argileux. Élevage en fût de 18 mois (0% de fût neuf). Non filtré. Vendange manuelle.', 23.44, 62, 1, 'pernandVergelesses1erCruLesFichots2018FollinArbelet.jpg', '2018', 1),
	(31, 2, 2, 4, 'VIN5ffe12d7e8df1', 'Pernand-Vergelesses 1er Cru "En Caradeux"', 'Vin rond et structuré, issu d’un terroir calcaire, marneux et siliceux. Non filtré. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 30.01, 30, 1, 'pernandVergelesses1erCruEnCaradeux2017Poisot.jpg', '2017', 1),
	(32, 2, 2, 3, 'VIN5ffe13593bc77', 'Aloxe-Corton 1er Cru', 'Assemblage de plusieurs parcelles de 1er cru (Les Fournières et Les Guérets), issu d’un terroir argilo-calcaire. Élevage en fût de 16 mois (15% de fût neuf). Vendange manuelle.', 13.5, 72, 1, 'aloxeCorton1erCru2013Meuneveaux.jpg', '2013', 1),
	(33, 2, 2, 1, 'VIN5ffe138653f11', 'Aloxe-Corton 1er Cru', 'Assemblage de plusieurs parcelles (Les Valozières, Les Vercots et Les Guérets), issu d’un terroir argilo-calcaire et marneux. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 16.5, 65, 1, 'aloxeCorton1erCru2016Chapuis.jpg', '2016', 1),
	(34, 2, 2, 5, 'VIN5ffe13c8eaeff', 'Aloxe-Corton 1er Cru "Le Clos du Chapitre"', 'Vin soyeux et harmonieux, issu d’un terroir argilo-calcaire et graveleux au centre du village d’Aloxe-Corton. Élevage en fût de 18 mois (30% de fût neuf). Non filtré. Vendange manuelle.', 22.22, 61, 1, 'aloxeCorton1erCruLeClosDuChapitre2018FollinArbelet.jpg', '2018', 1),
	(35, 2, 2, 5, 'VIN5ffe1413e3aad', 'Aloxe-Corton 1er Cru "Les Vercots"', 'Vin puissant et structuré, issu d’un terroir d’argile compacte et profonde. Élevage en fût de 18 mois (30% de fût neuf). Non filtré. Vendange manuelle.', 43.34, 52, 1, 'aloxeCorton1erCruLesVercots2018FollinArbelet.jpg', '2018', 1),
	(36, 4, 2, 2, 'VIN5ffe14476ca3b', 'Bourgogne', 'Tendre et accessible, ce vin se situe sur la plaine des appellations régionales de la Côte de Beaune. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle.', 4.99, 200, 1, 'bourgogne2018Colin.jpg', '2018', 1),
	(37, 3, 2, 2, 'VIN5ffe1471cf236', 'Aloxe-Corton', 'Rond et charnue, ce vin se situe à l’entrée du village d’Aloxe-Corton sur un terroir argilo-calcaire. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle.', 8.99, 70, 1, 'aloxeCorton2018Colin.jpg', '2018', 1),
	(38, 3, 2, 3, 'VIN5ffe14b2df609', 'Aloxe-Corton', 'Fin et élégant, ce vin se situe à l’entrée du village d’Aloxe-Corton sur un terroir argilo-calcaire. Élevage en fût de 12 mois (0% de fût neuf). Vendange manuelle.', 7.5, 68, 1, 'aloxeCorton2018Meuneveaux.jpg', '2018', 1),
	(39, 3, 2, 1, 'VIN5ffe14e25b1f8', 'Aloxe-Corton', 'Minéralité et caractère, ce vin se situe à l’entrée du village d’Aloxe-Corton sur un terroir argilo-calcaire. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 10.99, 63, 1, 'aloxeCorton2016Chapuis.jpg', '2016', 1),
	(40, 1, 1, 1, 'VIN5ffe1545be940', 'Corton Charlemagne', 'Compromis entre la minéralité et la rondeur, le Corton Charlemagne fait partie des meilleurs Grands Crus Blancs de la Côte de Beaune. Ce vin est issu d’un terroir argilo-calcaire et marneux. Élevage en fût de 12 mois (10% de fût neuf). Vendange manuelle.', 150, 20, 1, 'cortonCharlemagne2017Chapuis.jpg', '2017', 1),
	(41, 1, 1, 4, 'VIN5ffe156feb2f2', 'Corton Charlemagne', 'Compromis entre la minéralité et la rondeur, le Corton Charlemagne fait partie des meilleurs Grands Crus Blancs de la Côte de Beaune. Ce vin est issu d’un terroir argilo-calcaire et marneux. Élevage en fût de 18 mois (36% de fût neuf) . Vendange manuelle.', 523, 12, 1, 'cortonCharlemagne2017Poisot.jpg', '2017', 1),
	(42, 1, 1, 3, 'VIN5ffe15b59c51d', 'Corton Blanc', 'Vin rond et gras, le corton Banc est issu de la montagne de Corton sur l’appellation « Les Chaumes » sur un terroir d’argile profonde et compacte. Élevage en fût de 16 mois (30% de fût neuf) . Vendange manuelle.', 8.52, 26, 1, 'cortonBlanc2017Meuneveaux.jpg', '2017', 1),
	(43, 3, 1, 1, 'VIN5ffe15e34a370', 'Chorey-les-Beaune', 'Vin minérale, issu d’un terroir argilo-calcaire de la Côte de Beaune. Élevage en fût de 12 mois (0% de fût neuf) . Vendange manuelle.', 12.99, 58, 1, 'choreyLesBeaune2018Chapuis.jpg', '2018', 1),
	(44, 3, 1, 3, 'VIN5ffe161badea1', 'Aloxe-Corton Blanc', 'Vin rond et gras, issu d’un terroir argilo-calcaire de la Côte de Beaune. Les Aloxe-Corton Blancs sont de petites productions. Élevage en fût de 16 mois (20% de fût neuf) . Vendange manuelle.', 59.99, 72, 1, 'aloxeCortonBlanc2018Meuneveaux.jpg', '2018', 1),
	(45, 3, 1, 4, 'VIN5ffe165757735', 'Pernand-Vergelesses Blanc', 'Vin minéral et harmonieux, issu d’un terroir argilo-calcaire et siliceux de la Côte de Beaune, ce vin se situe sur le lieu-dit « En Caradeux ». Non filtré. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 41.41, 15, 1, 'pernandVergelessesBlanc2018Poisot.jpg', '2018', 1),
	(46, 3, 1, 5, 'VIN5ffe169142bc7', 'Pernand-Vergelesses Blanc', 'Vin minéral et harmonieux, issu d’un terroir argilo-calcaire et siliceux de la Côte de Beaune, ce vin se situe sur le lieu-dit « En Caradeux ». Non filtré. Élevage en fût de 18 mois (0% de fût neuf). Vendange manuelle.', 33.33, 82, 1, 'pernandVergelessesBlanc2018FollinArbelet.jpg', '2018', 1);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. product_ordering
CREATE TABLE IF NOT EXISTS `product_ordering` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordering_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B08159F08E6C7DE4` (`ordering_id`),
  KEY `IDX_B08159F04584665A` (`product_id`),
  CONSTRAINT `FK_B08159F04584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `FK_B08159F08E6C7DE4` FOREIGN KEY (`ordering_id`) REFERENCES `ordering` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.product_ordering : ~0 rows (environ)
/*!40000 ALTER TABLE `product_ordering` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_ordering` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. ship_address
CREATE TABLE IF NOT EXISTS `ship_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3635FECA76ED395` (`user_id`),
  CONSTRAINT `FK_3635FECA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.ship_address : ~0 rows (environ)
/*!40000 ALTER TABLE `ship_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `ship_address` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. type
CREATE TABLE IF NOT EXISTS `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.type : ~2 rows (environ)
/*!40000 ALTER TABLE `type` DISABLE KEYS */;
INSERT INTO `type` (`id`, `name`) VALUES
	(1, 'blanc'),
	(2, 'rouge');
/*!40000 ALTER TABLE `type` ENABLE KEYS */;

-- Listage de la structure de la table projetlf. user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.user : ~8 rows (environ)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
	(2, 'lisa@lisa.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$MGtYOTF5OXQ1T3kuT1dGOQ$810ffvXrGAL0KAMmmR5Bfr65QjKQdfuIaQQPGKrvn+w'),
	(3, 'alexis@alexis.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$RUdwRGlDUW03bkpUVk4vUQ$tJZ8GB2FsCFJRcfuiMb0fcxha0rnkDxOzDguODZoH2M'),
	(4, 'sdf@er.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$NDF1cC9JWTE2ODBBRTVraw$XBHoGMUidlY1//GvC4H9qosOENtsYcYkO49cF/PwI+o'),
	(6, 'admin@admin.fr', '["ROLE_USER", "ROLE_ADMIN"]', '$argon2id$v=19$m=65536,t=4,p=1$d0hZUGI5R3N0S2JmSmxkTg$dhCsdYPSHh+9ADBE4zJSzOfCyRX+MQ9UpHvuVuYn5mw'),
	(7, 'emeline@emeline.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$N3dVLmt6RWc2SjBKZWhEeQ$oNruKkB4woeTHd7HOywJcFKG+DXVK89aWheKlsX7pjk'),
	(11, 'matthieu@matthieu.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$bjNFWExldlduSzNEV2U0cw$cPvdWbOsBNWjFbdhseC0eo71qvN7VwFMektvnJOqlr0'),
	(12, 'maxime@maxime.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$WXV0OEVybkZsdTFjczR2VQ$8FHMO1x6gNtAbOWyhTO5IRB7lT/oSlwuLCgNlKIAryA'),
	(13, 'utilisateur@utilisateur.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$VlptbTZzejIzc1NhSE01Tw$jVze/LsXkUBfVMmeOSGj91t7bxX4THo1HESxV4wldCY');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
