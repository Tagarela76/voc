INSERT INTO `company_level_label` (
`id` ,
`label_id` ,
`name4display` ,
`default_label_text`
)
VALUES (
NULL ,  'spent_time',  'Spent Time Label',  'Spent Time'
);


UPDATE `browse_category_entity` SET  `default_value` =  'r_o_description,add_job,product_name,description,contact,r_o_vin_number,spent_time,voc,unit_type,creation_date' WHERE  `browse_category_entity`.`id` =1;