-- Adminer 5.4.2 MySQL 8.4.8 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `img_path` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `birth` int NOT NULL,
  `job_title_id` int NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `employees` (`id`, `img_path`, `name`, `birth`, `job_title_id`, `description`) VALUES
(16,	'1773868413__ (50).jpeg',	'Элиот Алдерсон',	1986,	9,	'Инженер по безопасности зданий и ифроструктурных узлов'),
(17,	'1773868870_Tʜᴇ Nᴀʀʀᴀᴛᴏʀ _ Iᴄᴏɴ.jpeg',	'Рассказчик',	1973,	10,	'Отвечает за порядок в процессах и деньгах. Следит, чтобы проекты сдавали в срок и не выходили за рамки бюджета.'),
(18,	'1773869089__ (51).jpeg',	'Патрик Бэйтмен',	1962,	7,	'Отвечает за то, чтобы все процессы в бюро работали слаженно и без сбоев. Следит за соблюдением сроков'),
(20,	'1773869621_saul goodman.jpeg',	'Джеймс Макгил',	1960,	11,	'Штатный юрист'),
(21,	'1773869745_BCS.jpeg',	'Майкл Эрматрауд',	1947,	1,	'ф'),
(23,	'1773869852_antónio chigolho.jpeg',	'Антон Чигур',	1980,	1,	'ф'),
(24,	'1773869868_Tony Soprano.jpeg',	'Энтони Сопрано',	1970,	1,	'ф'),
(25,	'1773870063__ (52).jpeg',	'Джек',	1970,	2,	'ф'),
(26,	'1773870383_Joaquin Phoenix.jpeg',	'Артур Флек',	1970,	2,	'ф'),
(27,	'1773870461__ (53).jpeg',	'Джон Константин',	1970,	2,	'ф'),
(28,	'1773870605__ (55).jpeg',	'Густаво Фринг',	1970,	2,	'ф'),
(29,	'1773870676_ᴊɪᴍ ᴄᴀʀʀᴇʏ.jpeg',	'Труман Бербанк',	1970,	2,	'ф');

DROP TABLE IF EXISTS `job_title`;
CREATE TABLE `job_title` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `job_title` (`id`, `name`) VALUES
(1,	'Архитектор'),
(2,	'Инженер'),
(3,	'Дизайнер'),
(4,	'Генеральный деректор'),
(5,	'Старший архитектор'),
(6,	'Директор по операциям'),
(7,	'Председатель совета директоров'),
(8,	'Менеджер проектов'),
(9,	'Инженер безопасности'),
(10,	'консультантом по страховым выплатам'),
(11,	'Старший юрист');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `FIO` varchar(255) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `interes` varchar(255) DEFAULT NULL,
  `vk` varchar(255) DEFAULT NULL,
  `blood` varchar(3) DEFAULT NULL,
  `resus` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `FIO`, `date`, `address`, `sex`, `interes`, `vk`, `blood`, `resus`, `email`, `password`) VALUES
(6,	'Иванов Иван Иванович',	'2001-03-02',	'Москва',	'М',	'sdfghjkl;',	'https://vk.com/feed',	'I',	'+',	'email11@gmail.com',	'$2y$10$olY28NnXeXyae314jY3ese.EhQBaI1OQG0PGxzOl1.3ki91YbxlUy'),
(7,	'Попов Дмитрий Александрович',	'31/07/2005',	'Волгоград, ...',	'М',	'-',	'https://vk.com/axawys',	'I',	'+',	'axawys@gmail.com',	'$2y$10$abcOclz.22MohHsozeS50OKY9l/3fUDU4Wu3JEoFzA6V.g3VZQ6Nq');

-- 2026-03-18 21:56:32 UTC
