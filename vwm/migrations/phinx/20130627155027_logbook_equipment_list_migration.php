<?php

use Phinx\Migration\AbstractMigration;

class LogbookEquipmentListMigration extends AbstractMigration
{
    public function change()
    {
        $exists = $this->hasTable('equipment');
        if ($exists) {
            $rows = $this->query("ALTER TABLE  `equipment` ADD  `voc_emissions` TINYINT NOT NULL DEFAULT  '1'");
            $rows = $this->query("ALTER TABLE  `equipment` ADD  `facility_id` INT( 11 ) NULL DEFAULT NULL");
        } else {
            die('table unit_class is not exist');
        }
    }

    /**
     * Migrate Up.
     */
    public function up()
    {
        
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        
    }

}