#user
CREATE USER 'iamroot'@'localhost' IDENTIFIED BY 'iamroot';
GRANT ALL PRIVILEGES ON * . * TO 'iamroot'@'localhost';
FLUSH PRIVILEGES;


#database
CREATE DATABASE playlist;

CREATE TABLE IF NOT EXISTS `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `thumbnail` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `playlist_to_video` (
  `playlist_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#exemple
INSERT INTO `playlist` (`id`, `name`, `created`, `modified`) VALUES
(1, 'Disney', '2018-08-01 00:00:00', '2018-08-01 00:00:00'),
(2, 'Pixar', '2018-08-01 00:00:00', '2018-08-01 00:00:00'),
(3, 'Ghibli', '2018-08-01 00:00:00', '2018-08-01 00:00:00');

INSERT INTO `video` (`id`, `title`, `thumbnail`, `created`, `modified`) VALUES
(1, 'Raiponse', 'raiponse.com', '2010-12-01 00:00:00', '2018-08-01 00:00:00'),
(2, 'Roi lion', 'roilion.com', '1994-11-09 00:00:00', '2018-08-01 00:00:00'),
(3, 'Cendrillon', 'cendrillon.com', '1958-12-17 00:00:00', '2018-08-01 00:00:00'),
(4, 'Toy Story', 'toystory.html', '1996-03-27 00:00:00', '2018-08-01 00:00:00'),
(5, 'Cars', 'cars.html', '2006-06-14 00:00:00', '2018-08-01 00:00:00'),
(6, 'Princesse Mononoké', 'princessemononoke.html', '2000-01-12 00:00:00', '2018-08-01 00:00:00'),
(7, 'Mon voisin Totoro', 'monvoisintotoro.html', '1988-04-16 00:00:00', '2018-08-01 00:00:00');

INSERT INTO `playlist_to_video` (`playlist_id`, `video_id`, `order`) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(2, 4, 1),
(2, 5, 2),
(3, 6, 1),
(3, 7, 2);
