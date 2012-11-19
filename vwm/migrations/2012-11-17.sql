UPDATE  `browse_category_entity` SET  `default_value` = 'Product Name,Add Job,Description,R/O Description,Contact,R/O VIN number,VOC,Unit type,Creation Date' WHERE  `browse_category_entity`.`id` =1;

CREATE TABLE IF NOT EXISTS `company_level_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label_id` varchar(120) NOT NULL,
  `name4display` varchar(120) NOT NULL,
  `default_label_text` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `industry_type2label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_level_label_id` int(11) NOT NULL,
  `label_text` varchar(120) NOT NULL,
  `industry_type_id` int(11) NOT NULL,
  `last_update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `industry_type2label` ADD FOREIGN KEY ( `company_level_label_id` ) REFERENCES `company_level_label` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `industry_type2label` ADD FOREIGN KEY ( `industry_type_id` ) REFERENCES `industry_type` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'repair_order',  'Repair Order Label',  'Repair Order'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'add_job',  'Add Job Label',  'Add Job'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'product_name',  'Product Name Label',  'Product Name'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'r_o_description',  'R/O Description Label',  'R/O Description'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'description',  'Description Label',  'Description'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'contact',  'Contact Label',  'Contact'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'r_o_vin_number',  'R/O VIN number Label',  'R/O VIN number'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'voc',  'VOC Label',  'VOC'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'creation_date',  'Creation Date Label',  'Creation Date'
);

INSERT INTO  `voc`.`company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'unit_type',  'Unit type Label',  'Unit Yype'
);