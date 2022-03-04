CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  INDEX (`hash`)
);
