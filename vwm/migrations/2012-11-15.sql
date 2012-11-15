INSERT INTO `filter` (
`id` ,
`filter_class` ,
`field_name` ,
`name_in_table` ,
`parent` ,
`autocomplete`
)
VALUES (
NULL , 'date', 'Last seen date', 'meeting_date', 'contacts', 'no'
);

INSERT INTO `filter` (`id`, `filter_class`, `field_name`, `name_in_table`, `parent`, `autocomplete`) VALUES (NULL, 'text', 'City', 'city', 'contacts', 'no');

ALTER TABLE `contacts` ADD `shop_type` SMALLINT NOT NULL DEFAULT '0';

ALTER TABLE `contacts` CHANGE `comments` `comments` VARCHAR( 300 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;