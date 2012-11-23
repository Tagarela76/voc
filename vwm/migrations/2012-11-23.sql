INSERT INTO  `company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'paint_shop_product',  'Paint Shop Products Label',  'Paint Shop Products'
);

INSERT INTO  `company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'body_shop_product',  'Body Shop Products Label',  'Body Shop Products Products'
);

INSERT INTO  `company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'detailing_shop_product',  'Detailing Products Label',  'Detailing Products'
);

UPDATE `localization` SET `string` = 'Data Entry' WHERE `localization`.`id` = 'LABEL_MIX_BOOKMARK';