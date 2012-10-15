ALTER TABLE `new_product_request` CHANGE `status` `status` TINYINT NOT NULL;
ALTER TABLE `new_product_request` ADD `last_update_time` DATETIME NULL;