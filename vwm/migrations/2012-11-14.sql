CREATE TABLE IF NOT EXISTS `meeting_with_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meeting_date` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `meeting_with_contact` ADD `last_update_time` DATETIME NULL;

ALTER TABLE `meeting_with_contact`
  ADD CONSTRAINT `meeting_with_contact_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `meeting_with_contact_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`);