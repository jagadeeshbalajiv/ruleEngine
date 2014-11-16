SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `product_rule_engine` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `product_rule_engine`;

CREATE TABLE IF NOT EXISTS `table_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

INSERT INTO `table_category` (`category_id`, `category_name`) VALUES
(1, 'Puma'),
(2, 'Nike'),
(3, 'Adidas'),
(4, 'Lee Cooper'),
(5, 'Woodland'),
(6, 'Crocodile');

CREATE TABLE IF NOT EXISTS `table_product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(8,2) NOT NULL,
  `product_special_price` decimal(8,2) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

INSERT INTO `table_product` (`product_id`, `product_name`, `product_price`, `product_special_price`) VALUES
(1, 'Puma - running shoe', '1000.00', '900.00'),
(2, 'Puma - sport shoe', '3500.00', '3300.00'),
(3, 'Nike - running shoe', '2000.00', '1900.00'),
(4, 'Nike - sports shoe', '5400.00', '5350.00'),
(5, 'Nike - Casual shoes', '5000.00', '0.00'),
(6, 'Adidas - Casual shoes', '2500.00', '1950.00'),
(7, 'Adidas - Sport shoes', '3000.00', '2800.00'),
(8, 'Lee cooper - Trousers', '2800.00', '0.00'),
(9, 'Lee cooper - Casual shirts', '1300.00', '1250.00'),
(10, 'Woodland - Shoe ', '3300.00', '2828.00'),
(11, 'Woodland - sandals', '2800.00', '0.00');

CREATE TABLE IF NOT EXISTS `table_product_category` (
  `f_product_id` int(11) NOT NULL,
  `f_category_id` int(11) NOT NULL,
  UNIQUE KEY `product_id_category_id_mapping_index` (`f_product_id`,`f_category_id`),
  KEY `foreign_key_category_id` (`f_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `table_product_category` (`f_product_id`, `f_category_id`) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 2),
(5, 2),
(6, 3),
(7, 3),
(8, 4),
(9, 4),
(10, 5),
(11, 5);

CREATE TABLE IF NOT EXISTS `table_rule` (
  `rule_id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(100) NOT NULL,
  `rule_data` longtext NOT NULL,
  `is_rule_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

INSERT INTO `table_rule` (`rule_id`, `rule_name`, `rule_data`, `is_rule_active`) VALUES
(1, 'Rule1', '{total >} "1000" && {category in} "1,2"  && {category not in} "5"  ==> {free shipping} && {%Discount} "20" ', 1);


ALTER TABLE `table_product_category`
  ADD CONSTRAINT `foreign_key_category_id` FOREIGN KEY (`f_category_id`) REFERENCES `table_category` (`category_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `foreign_key_product_id` FOREIGN KEY (`f_product_id`) REFERENCES `table_product` (`product_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
