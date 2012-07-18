ALTER TABLE `pfp2company` ENGINE = InnoDB;

ALTER TABLE `pfp2company` ADD FOREIGN KEY ( `company_id` ) REFERENCES `company` (
`company_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;