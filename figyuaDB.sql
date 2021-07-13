-- -------------------------------------------------------------
-- TablePlus 3.12.5(364)
--
-- https://tableplus.com/
--
-- Database: test_db
-- Generation Time: 2021-04-13 10:47:50.5050
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `figurine`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `license`;
DROP TABLE IF EXISTS `commande`;
DROP TABLE IF EXISTS `order_item`;
DROP TABLE IF EXISTS `maker`;
DROP TABLE IF EXISTS `carousel`;

CREATE TABLE `figurine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `short_description` text NOT NULL,
  `full_description` text,
  `license_id` int(11) NOT NULL,
  `maker_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `price_reduction` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `image_main` varchar(255) NOT NULL,
  `image_back` varchar(255) DEFAULT NULL,
  `image_left` varchar(255) DEFAULT NULL,
  `image_right` varchar(255) DEFAULT NULL,
  `image_carousel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `license_id` (`license_id`),
  KEY `maker_id` (`maker_id`),
  CONSTRAINT `figurine_ibfk_1` FOREIGN KEY (`license_id`) REFERENCES `license` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `figurine_ibfk_2` FOREIGN KEY (`maker_id`) REFERENCES `maker` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `maker`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `commande` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `total_price` float NOT NULL,
  `billing_address` text NOT NULL,
  `delivery_address` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Confirmée',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_item` (
  `order_id` int(11) NOT NULL,
  `figurine_id` int(11) NOT NULL,
  `figurine_quantity` int(11) NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `figurine_id` (`figurine_id`),
  CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `commande` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`figurine_id`) REFERENCES `figurine` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL,
  `image` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `password` varchar(80) NOT NULL,
  `adress` text DEFAULT NULL,
  `date_of_creation` date NOT NULL,
  `last_update` date NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (name, email, password, adress, date_of_creation, last_update, admin)
		VALUES
      ('admin', 'admin@admin.com', '21232f297a57a5a743894a0e4a801fc3', 'country:France;city:Marseille;zip:13001;street:Labadié;', CURDATE(), CURDATE(), 1),
      ('guest', 'guest@guest.com', '084e0343a0486ff05530df6c705c8bb4', '', CURDATE(), CURDATE(), 0),
      ('plop', 'plop@plop.com', '64a4e8faed1a1aa0bf8bf0fc84938d25', '', CURDATE(), CURDATE(), 0),
      ('plopinette', 'plopinette@plopinette.com', '9880f53e82323f5f7c515e2f06af337b', '', CURDATE(), CURDATE(), 0)
;

INSERT INTO `license` (name)
  VALUES
    ('Dragon Ball Z'),
    ('One Piece'),
    ('Boku no Hero Academia'),
    ('City Hunter')
;

INSERT INTO `maker` (name)
  VALUES
    ('Bandai'),
    ('Bandai Spirits'),
    ('Banpresto'),
    ('MegaHouse'),
    ('Craneking'),
    ('Abystyle')
;

INSERT INTO `figurine` (name, short_description, full_description, license_id, maker_id, price, price_reduction, quantity, image_main)
  VALUES 
    ('Broly le Super Sayan Légendaire',
    'Figurine haut de gamme en dioramma lors du premier affrontement face à Goku',
    'Matière:PVC;Taille:20cm;',
    1,
    1,
    19.99,
    1,
    10,
    'assets/images/figurines/1/1/broly/broly-le-super-sayan-legendaire.jpg'),
    ('Cooler',
     "Figurine haut de gamme en dioramma lors de l'affrontement face à Goku sur namek",
     'Matière:PVC;Taille:22cm;',
     1,
     1,
     39.99,
     1,
     5,
     'assets/images/figurines/1/1/cooler/cooler.png'),
     ('Gotenks Grandista resolution of soldier',
     "Figurine de Gotenks de la collection Grandista : Resolution of Soldiers provenant de l'univers Dragon Ball Z. Livrée avec une tête en Super Saiyan.",
     'Matière:PVC;Taille:20cm;',
     1,
     3,
     34.99,
     1,
     20,
     'assets/images/figurines/1/3/gotenks/gotenks-grandista.jpg'),
     ('Goku ultra instinct ichiban kuji',
     "Figurine de Goku Ultra-Instinct maîtrisé (Migatte no gokui) en position dynamique d'envol, provenant du manga Dragon Ball Super",
     'Matière:PVC;Taille:28cm;',
     1,
     2,
     24.99,
     1,
     25,
     'assets/images/figurines/1/2/gokuui/goku-ultra-instint-ichiban-kuji.jpg'),
     ('Monkey D. Luffy (Luffytaro)',
     "Figurine de Luffy de la collection Figuarts Zero version Luffytaro de l'arc Wano Kuni pour le manga One Piece.",
     'Matière:PVC;Taille:14cm;',
     2,
     1,
     29.99,
     1,
     15,
     'assets/images/figurines/2/1/luffytaro/monkey-d-luffy-luffytaro.jpg'),
     ('Sanji Great Banquet',
     "Figurine de Sanji version Great Banquet de la collection Ichibansho pour le manga One Piece.",
     'Matière:PVC;Taille:14cm;',
     2,
     2,
     29.99,
     1,
     15,
     'assets/images/figurines/2/2/sanji/sanji-great-banquet-.jpg'),
     ('Nico Robin Lady Wano kuni Vol.2',
     "Figurine de Nico Robin de la collection The Grandline Lady pour l'Arc Wano Kuni issue du manga One Piece.",
     'Matière:PVC;Taille:16cm;',
     2,
     3,
     29.99,
     1,
     15,
     'assets/images/figurines/2/3/nicorobin/nico-robin-lady-wano-kuni-vol-2.jpg'),
     ('Ussop (Usohachi)',
     "Figurine de Usopp de la collection Figuarts Zero version Usohachi de l'arc Wano Kuni pour le manga One Piece.",
     'Matière:PVC;Taille:12cm;',
     2,
     3,
     29.99,
     1,
     15,
     'assets/images/figurines/2/3/ussop/usopp-usohachi.jpg'),
     ('Mirio Togata',
     "Figurine de Mirio Togata alias Lemilion de la gamme Ichibansho pour le manga My Hero Academia (Boku no Hero Academia). ",
     'Matière:PVC;Taille:18cm;',
     3,
     2,
     29.99,
     1,
     15,
     'assets/images/figurines/3/2/mirio/mirio-togata.jpg'),
     ('All Might two dimensions',
     "Figurine de All Might issue de la collection Super Master Stars Piece et BWFC version Two Dimension (effet 2D) pour le manga My Hero Academia (Boku no Hero Academia). ",
     'Matière:PVC;Taille:31cm;',
     3,
     3,
     49.99,
     1,
     10,
     'assets/images/figurines/3/3/allmight/all-might-two-dimensions.jpg'),
     ('Katsuki Bakugo',
     "Katsuki Bakugo est prêt à déployer toute la puissance de ses explosions grâce à ses gants en forme de grenade pour devenir le héros numéro un et détrôner All Might.",
     'Matière:PVC;Taille:25cm;',
     3,
     6,
     49.99,
     1,
     10,
     'assets/images/figurines/3/6/bakugo/katsuki-bakugo.jpg'),
     ('Ryo Saeba ARTFX',
     "Figurine de Ryo Saeba (Nicky Larson) tirée du film.",
     'Matière:PVC;Taille:25cm;',
     4,
     5,
     79.99,
     1,
     20,
     'assets/images/figurines/4/5/ryosaebaartfx/ryo-saeba.jpg'),
     ('Ryo et Kaori',
     "Figurine City Hunter de la collection Creator x Creator par Banpresto représentant la fidèle associée de Ryo Saeba (alias Nicky Larson) Kaori Makimura (connue aussi sous le nom de Laura Marconi).",
     'Matière:PVC;Taille:20cm Ryo & 17cm Kaori;',
     4,
     3,
     49.99,
     1,
     10,
     'assets/images/figurines/4/3/ryoetkaori/Ryo-Kaori.jpg')
;

INSERT INTO `commande` (user_id, order_date, total_price, billing_address, delivery_address, status)
  VALUES
    (1, CURDATE(), 104.96, "billing address", "delivery address", "Envoyée"),
    (4, CURDATE(), 119.96, "billing address", "delivery address", "En préparation"),
    (3, CURDATE(), 239.94, "billing address", "delivery address", "Confirmée")
;

INSERT INTO `order_item` (order_id, figurine_id, figurine_quantity)
  VALUES 
    (1, 1, 2),
    (1, 3, 1),
    (1, 9, 1),
    (2, 6, 1),
    (2, 7, 2),
    (2, 8, 1),
    (3, 6, 1),
    (3, 7, 2),
    (3, 13, 3)
;

INSERT INTO `carousel` (image)
  VALUES
    ('assets/images/banner/BrolyBanner.png'),
    ('assets/images/banner/IzukuBanner.png'),
    ('assets/images/banner/LuffyBanner.png'),
    ('assets/images/banner/RyoBanner.png')
;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;