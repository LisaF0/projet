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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.facture : ~7 rows (environ)
/*!40000 ALTER TABLE `facture` DISABLE KEYS */;
INSERT INTO `facture` (`id`, `ordering_id`, `facture_reference`, `created_at`, `lastname`, `firstname`, `city`, `zipcode`, `address`, `user_id`) VALUES
	(1, 2, '14012021-6000778d23076', '2021-01-14 17:55:41', 'Foret', 'Lisa', 'Erstein', '67150', '23 Rue de la Digue', 6),
	(2, 27, '18012021-60055444ec85f', '2021-01-18 10:26:28', 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa', 12),
	(3, 28, '20012021-6007f84b5bdc5', '2021-01-20 10:30:51', 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa', 13),
	(5, 29, '20012021-6007f996c7175', '2021-01-20 10:36:22', 'Foret', 'Lisa', 'Erstein', '67150', '23 Rue de la Digue', 2),
	(6, 30, '20012021-6008000d4d452', '2021-01-20 11:03:57', 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa', 14),
	(7, 32, '20012021-600833b206f82', '2021-01-20 14:44:18', 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa', 15),
	(8, 34, '22012021-600af501f3ad7', '2021-01-22 16:53:37', 'Lisa', 'Lisa', 'Lisa', '12312', 'Lisa', 3);
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
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.ordering : ~34 rows (environ)
/*!40000 ALTER TABLE `ordering` DISABLE KEYS */;
INSERT INTO `ordering` (`id`, `ship_address_id`, `user_id`, `facture_id`, `ordering_reference`, `created_at`, `ordering_status`, `stripe_session_id`) VALUES
	(1, 1, 6, NULL, '14012021-600075a74269d', '2021-01-14 17:47:35', 0, NULL),
	(2, 1, 6, 1, '14012021-6000778d2306e', '2021-01-14 17:55:41', 3, 'cs_test_b15bFDevUlhdBdgRYhNRG4cXOu1HLw85VNr0RnGcx1pG7gma3seZinYnHC'),
	(3, 1, 6, NULL, '14012021-600086140ae2d', '2021-01-14 18:57:40', 0, NULL),
	(4, 1, 6, NULL, '14012021-600086712c21d', '2021-01-14 18:59:13', 0, NULL),
	(5, 1, 6, NULL, '14012021-6000867e35e1d', '2021-01-14 18:59:26', 0, NULL),
	(6, 1, 6, NULL, '14012021-6000869a3be8c', '2021-01-14 18:59:54', 0, NULL),
	(7, 2, 12, NULL, '18012021-60054cf18c058', '2021-01-18 09:55:13', 0, NULL),
	(8, 2, 12, NULL, '18012021-60054d46829a8', '2021-01-18 09:56:38', 0, NULL),
	(9, 2, 12, NULL, '18012021-60054d7b8e3bb', '2021-01-18 09:57:31', 0, NULL),
	(10, 2, 12, NULL, '18012021-60054daa47d1b', '2021-01-18 09:58:18', 0, NULL),
	(11, 2, 12, NULL, '18012021-60054dc15fe69', '2021-01-18 09:58:41', 0, NULL),
	(12, 2, 12, NULL, '18012021-60054ddd2e0f1', '2021-01-18 09:59:09', 0, NULL),
	(13, 2, 12, NULL, '18012021-60054e3125029', '2021-01-18 10:00:33', 0, NULL),
	(14, 2, 12, NULL, '18012021-60054e5e0693a', '2021-01-18 10:01:18', 0, NULL),
	(15, 2, 12, NULL, '18012021-60054e7849f5a', '2021-01-18 10:01:44', 0, NULL),
	(16, 2, 12, NULL, '18012021-60054eac922fd', '2021-01-18 10:02:36', 0, NULL),
	(17, 2, 12, NULL, '18012021-60054ec522352', '2021-01-18 10:03:01', 0, NULL),
	(18, 2, 12, NULL, '18012021-60054eccbdfaf', '2021-01-18 10:03:08', 0, NULL),
	(19, 2, 12, NULL, '18012021-60054edfce1b1', '2021-01-18 10:03:27', 0, NULL),
	(20, 2, 12, NULL, '18012021-600552872fc89', '2021-01-18 10:19:03', 0, NULL),
	(21, 2, 12, NULL, '18012021-600553b043cf8', '2021-01-18 10:24:00', 0, NULL),
	(22, 2, 12, NULL, '18012021-600553b762617', '2021-01-18 10:24:07', 0, NULL),
	(23, 2, 12, NULL, '18012021-600553e039570', '2021-01-18 10:24:48', 0, 'cs_test_a142XOAOA54ecEFnoxD5gGNapypp4xfZdxFfkgPd5ul0brT09GRso7lYNj'),
	(24, 2, 12, NULL, '18012021-600553f101001', '2021-01-18 10:25:05', 0, NULL),
	(25, 2, 12, NULL, '18012021-60055406f0738', '2021-01-18 10:25:26', 0, 'cs_test_a1dOxpY0c3jrImHxtHNrBlh0OmumKMZH8l1isIR7pJ0Ol8HMZM0KvK6GDy'),
	(26, 2, 12, NULL, '18012021-6005543ca9020', '2021-01-18 10:26:20', 0, NULL),
	(27, 2, 12, 2, '18012021-60055444ec858', '2021-01-18 10:26:28', 1, 'cs_test_a1CziyXeqQzj1xuATBWZ461DgW3BxFmrEHx7cghdIqiPYWYi3sp2cPxMmu'),
	(28, 3, 13, 3, '20012021-6007f84b5bdbd', '2021-01-20 10:30:51', 1, 'cs_test_b1UNGZkovqdObJUKbhEEeAp5RT9hm2LkTx1vzMKmKxcgj3ThFV3VsVbwk5'),
	(29, 4, 2, 5, '20012021-6007f996c716e', '2021-01-20 10:36:22', 1, 'cs_test_a1BZyfI45275WPAyaBN1yhn2u8RvpjJfFDldihJ9ZsWVbKWWg4Bd1TDfRt'),
	(30, 5, 14, 6, '20012021-6008000d4d44a', '2021-01-20 11:03:57', 1, 'cs_test_b13B8kEaLEdBHkaAVxRPhfYrtgiW61nCoIRcHlXKzBAkn3rIDUMFrPnxrs'),
	(31, 7, 7, NULL, '20012021-600801d96368e', '2021-01-20 11:11:37', 0, 'cs_test_a1zwsksw1LpnXeluHLf8k7Cd0KwycRu9qEpDI4pDh4HQoqyWyDj8WMezpa'),
	(32, 8, 15, 7, '20012021-600833b206f7a', '2021-01-20 14:44:18', 3, 'cs_test_b17AGbWjvfhs2bmifgrykH4rPPn1yF4fbpaorjhuVSkgGOkJFLUDYWuQfZ'),
	(33, 10, 3, NULL, '22012021-600af4870a6db', '2021-01-22 16:51:35', 0, NULL),
	(34, 10, 3, 8, '22012021-600af501f3acf', '2021-01-22 16:53:37', 1, 'cs_test_b1qHVcPvDW3BmcINZAgJmwIXYQSE5wxHHjfThEgkh8PcVSjGUG2npBLQ4S');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.product : ~26 rows (environ)
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` (`id`, `appellation_id`, `type_id`, `domain_id`, `reference`, `name`, `description`, `unit_price`, `unit_stock`, `available`, `photo`, `year`, `activate`) VALUES
	(21, 1, 2, 1, 'VIN5ffe0fdb1e1f8', 'Corton Perrières', 'Vin fin et élégant, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire et silicieux. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 25.39, 42, 1, 'cortonPerrieres2016Chapuis.jpg', '2016', 1),
	(22, 1, 2, 3, 'VIN5ffe1054424bb', 'Corton Perrières', 'Vin fin et élégant, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire et silicieux. Élevage en fût de 16 mois (30% de fût neuf). Vendange manuelle.', 29.99, 26, 1, 'cortonPerrieres2016Meuneveaux.jpg', '2016', 1),
	(23, 1, 2, 2, 'VIN5ffe10e3856d8', 'Corton Renardes', 'Vin rond et puissant, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle.', 24, 55, 1, 'cortonRenardes2018Colin.jpg', '2018', 1),
	(24, 1, 2, 4, 'VIN5ffe111d65ada', 'Corton Bressandes', 'Vin puissant avec du caractère, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire avec une terre rougeâtre (sol ferrugineux). Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 120, 15, 1, 'cortonBressandes2017Poisot.jpg', '2017', 1),
	(25, 1, 2, 4, 'VIN5ffe111decf59', 'Corton Bressandes', 'Vin puissant avec du caractère, issu du côteau de la montagne de Corton sur un terroir argilo-calcaire avec une terre rougeâtre (sol ferrugineux). Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 120, 13, 1, 'cortonBressandes2017Poisot.jpg', '2017', 1),
	(26, 1, 2, 5, 'VIN5ffe114cba8cf', 'Corton Rouge', 'Compromis entre la finesse et l’élégance, ce corton rouge est implanté sur un terroir argilo-calcaire et marneux des Cortons Charlemagne de la montagne de Corton. Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 52.12, 50, 1, 'cortonRouge2018FollinArbelet.jpg', '2018', 1),
	(27, 1, 2, 4, 'VIN5ffe119056416', 'Romanée Saint-Vivant', 'La Romanée Saint-Vivant fait partie de l’élite des grands crus de la Côte de Nuit, le terroir est sur un sol brun calcaire fortement argileux. Non filtré. Élevage en fût de 18 mois (50% de fût neuf). Vendange manuelle.', 150, 10, 1, 'romaneeSaintVivant2016Poisot.jpg', '2016', 1),
	(28, 2, 2, 2, 'VIN5ffe11fa8c2a5', 'Savigny-les-Beaune 1er Cru "Les Peuillets"', 'Vin tout en souplesse, issu d’un terroir argilo-calcaire. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle. Température de service : 14 à 16°C', 15, 100, 1, 'savignyLesBeaune1erCruLesPeuillets2017Colin.jpg', '2017', 1),
	(29, 2, 2, 2, 'VIN5ffe123b43e07', 'Beaune 1er Cru "Les Avaux"', 'Vin fin et rond en bouche, issu d’un terroir calcaire et marneux. Élevage en fût de 16 mois (10% de fût neuf). Vendanges manuelle.', 16, 120, 1, 'beaun1erCruLesAvaux2017Colin.jpg', '2017', 1),
	(30, 2, 2, 5, 'VIN5ffe12916a99f', 'Pernand-Vergelesses 1er Cru "Les Fichots"', 'Vin rond et structuré, issu d’un terroir calcaire et très argileux. Élevage en fût de 18 mois (0% de fût neuf). Non filtré. Vendange manuelle.', 23.44, 62, 1, 'pernandVergelesses1erCruLesFichots2018FollinArbelet.jpg', '2018', 1),
	(31, 2, 2, 4, 'VIN5ffe12d7e8df1', 'Pernand-Vergelesses 1er Cru "En Caradeux"', 'Vin rond et structuré, issu d’un terroir calcaire, marneux et siliceux. Non filtré. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 30.01, 30, 1, 'pernandVergelesses1erCruEnCaradeux2017Poisot.jpg', '2017', 1),
	(32, 2, 2, 3, 'VIN5ffe13593bc77', 'Aloxe-Corton 1er Cru', 'Assemblage de plusieurs parcelles de 1er cru (Les Fournières et Les Guérets), issu d’un terroir argilo-calcaire. Élevage en fût de 16 mois (15% de fût neuf). Vendange manuelle.', 13.5, 69, 1, 'aloxeCorton1erCru2013Meuneveaux.jpg', '2013', 1),
	(33, 2, 2, 1, 'VIN5ffe138653f11', 'Aloxe-Corton 1er Cru', 'Assemblage de plusieurs parcelles (Les Valozières, Les Vercots et Les Guérets), issu d’un terroir argilo-calcaire et marneux. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 16.5, 64, 1, 'aloxeCorton1erCru2016Chapuis.jpg', '2016', 1),
	(34, 2, 2, 5, 'VIN5ffe13c8eaeff', 'Aloxe-Corton 1er Cru "Le Clos du Chapitre"', 'Vin soyeux et harmonieux, issu d’un terroir argilo-calcaire et graveleux au centre du village d’Aloxe-Corton. Élevage en fût de 18 mois (30% de fût neuf). Non filtré. Vendange manuelle.', 22.22, 61, 1, 'aloxeCorton1erCruLeClosDuChapitre2018FollinArbelet.jpg', '2018', 1),
	(35, 2, 2, 5, 'VIN5ffe1413e3aad', 'Aloxe-Corton 1er Cru "Les Vercots"', 'Vin puissant et structuré, issu d’un terroir d’argile compacte et profonde. Élevage en fût de 18 mois (30% de fût neuf). Non filtré. Vendange manuelle.', 43.34, 52, 1, 'aloxeCorton1erCruLesVercots2018FollinArbelet.jpg', '2018', 1),
	(36, 4, 2, 2, 'VIN5ffe14476ca3b', 'Bourgogne', 'Tendre et accessible, ce vin se situe sur la plaine des appellations régionales de la Côte de Beaune. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle.', 4.99, 200, 1, 'bourgogne2018Colin.jpg', '2018', 1),
	(37, 3, 2, 2, 'VIN5ffe1471cf236', 'Aloxe-Corton', 'Rond et charnue, ce vin se situe à l’entrée du village d’Aloxe-Corton sur un terroir argilo-calcaire. Élevage en fût de 16 mois (10% de fût neuf). Vendange manuelle.', 8.99, 70, 1, 'aloxeCorton2018Colin.jpg', '2018', 1),
	(38, 3, 2, 3, 'VIN5ffe14b2df609', 'Aloxe-Corton', 'Fin et élégant, ce vin se situe à l’entrée du village d’Aloxe-Corton sur un terroir argilo-calcaire. Élevage en fût de 12 mois (0% de fût neuf). Vendange manuelle.', 7.5, 68, 1, 'aloxeCorton2018Meuneveaux.jpg', '2018', 1),
	(39, 3, 2, 1, 'VIN5ffe14e25b1f8', 'Aloxe-Corton', 'Minéralité et caractère, ce vin se situe à l’entrée du village d’Aloxe-Corton sur un terroir argilo-calcaire. Élevage en fût de 18 mois (10% de fût neuf). Vendange manuelle.', 10.99, 63, 1, 'aloxeCorton2016Chapuis.jpg', '2016', 1),
	(40, 1, 1, 1, 'VIN5ffe1545be940', 'Corton Charlemagne', 'Compromis entre la minéralité et la rondeur, le Corton Charlemagne fait partie des meilleurs Grands Crus Blancs de la Côte de Beaune. Ce vin est issu d’un terroir argilo-calcaire et marneux. Élevage en fût de 12 mois (10% de fût neuf). Vendange manuelle.', 150, 17, 1, 'cortonCharlemagne2017Chapuis.jpg', '2017', 1),
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.product_ordering : ~61 rows (environ)
/*!40000 ALTER TABLE `product_ordering` DISABLE KEYS */;
INSERT INTO `product_ordering` (`id`, `ordering_id`, `product_id`, `quantity`) VALUES
	(1, 1, 33, 1),
	(2, 1, 23, 5),
	(3, 2, 33, 1),
	(4, 2, 23, 5),
	(5, 3, 21, 1),
	(6, 3, 23, 1),
	(7, 3, 28, 1),
	(8, 3, 29, 1),
	(9, 3, 39, 1),
	(10, 3, 40, 1),
	(11, 4, 21, 1),
	(12, 4, 23, 1),
	(13, 4, 28, 1),
	(14, 4, 29, 1),
	(15, 4, 39, 1),
	(16, 4, 40, 1),
	(17, 5, 21, 1),
	(18, 5, 23, 1),
	(19, 5, 28, 1),
	(20, 5, 29, 1),
	(21, 5, 39, 1),
	(22, 5, 40, 1),
	(23, 6, 21, 1),
	(24, 6, 23, 1),
	(25, 6, 28, 1),
	(26, 6, 29, 1),
	(27, 6, 39, 1),
	(28, 6, 40, 1),
	(29, 7, 22, 1),
	(30, 8, 22, 1),
	(31, 9, 22, 1),
	(32, 10, 22, 1),
	(33, 11, 22, 1),
	(34, 12, 22, 1),
	(35, 13, 22, 1),
	(36, 14, 22, 1),
	(37, 15, 22, 1),
	(38, 16, 22, 1),
	(39, 17, 22, 1),
	(40, 18, 22, 1),
	(41, 19, 22, 1),
	(42, 20, 22, 1),
	(43, 21, 22, 1),
	(44, 22, 22, 1),
	(45, 23, 22, 1),
	(46, 24, 22, 1),
	(47, 25, 22, 1),
	(48, 26, 22, 1),
	(49, 27, 22, 1),
	(50, 28, 21, 1),
	(51, 28, 22, 2),
	(52, 28, 40, 3),
	(53, 29, 22, 1),
	(54, 30, 21, 3),
	(55, 30, 25, 1),
	(57, 32, 21, 3),
	(58, 32, 25, 1),
	(59, 33, 32, 3),
	(60, 33, 21, 1),
	(61, 34, 32, 3),
	(62, 34, 21, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.ship_address : ~11 rows (environ)
/*!40000 ALTER TABLE `ship_address` DISABLE KEYS */;
INSERT INTO `ship_address` (`id`, `user_id`, `lastname`, `firstname`, `city`, `zipcode`, `address`) VALUES
	(1, 6, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(2, 12, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(3, 13, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(4, 2, 'Foret', 'Lisa', 'Erstein', '67150', '23 Rue de la Digue'),
	(5, 14, 'Lisa', 'Lisa', 'Lisa', '12345', 'Lisa'),
	(6, 14, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(7, 7, 'Emeline', 'Emeline', 'Erstein', '67150', '22 rue de la digue'),
	(8, 15, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(9, 15, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(10, 3, 'Lisa', 'Lisa', 'Lisa', '67152', 'Lisa'),
	(11, 3, 'Lisa', 'Lisa', 'Lisa', '12345', 'Lisa');
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetlf.user : ~9 rows (environ)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
	(2, 'lisa@lisa.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$MGtYOTF5OXQ1T3kuT1dGOQ$810ffvXrGAL0KAMmmR5Bfr65QjKQdfuIaQQPGKrvn+w'),
	(3, 'alexis@alexis.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$RUdwRGlDUW03bkpUVk4vUQ$tJZ8GB2FsCFJRcfuiMb0fcxha0rnkDxOzDguODZoH2M'),
	(6, 'admin@admin.fr', '["ROLE_USER", "ROLE_ADMIN"]', '$argon2id$v=19$m=65536,t=4,p=1$d0hZUGI5R3N0S2JmSmxkTg$dhCsdYPSHh+9ADBE4zJSzOfCyRX+MQ9UpHvuVuYn5mw'),
	(7, 'emeline@emeline.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$N3dVLmt6RWc2SjBKZWhEeQ$oNruKkB4woeTHd7HOywJcFKG+DXVK89aWheKlsX7pjk'),
	(11, 'matthieu@matthieu.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$bjNFWExldlduSzNEV2U0cw$cPvdWbOsBNWjFbdhseC0eo71qvN7VwFMektvnJOqlr0'),
	(12, 'maxime@maxime.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$WXV0OEVybkZsdTFjczR2VQ$8FHMO1x6gNtAbOWyhTO5IRB7lT/oSlwuLCgNlKIAryA'),
	(13, 'user@user.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$MTRod1RaMS9md01xMER4Uw$7T4wL9DQDjPetIwipM/Sr4V58cDBKQU6rqkn5xhp/Fs'),
	(14, 'pierre@pierre.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$WjgzaEdVNEFJQnlKaUgzWg$tDUWRER1ZpelziwOet3/tq8+QvXpFs5lutzguCrvY7k'),
	(15, 'romain@romain.fr', '["ROLE_USER"]', '$argon2id$v=19$m=65536,t=4,p=1$M1RUMThZZlhidHhtYmoxSA$PW4s7KIetUclivY9l6MZ5NMAB6aOG23VLDH+LoEM584');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
