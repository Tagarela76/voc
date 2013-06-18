<?php

use Phinx\Migration\AbstractMigration;

class FinalLogbookMifration extends AbstractMigration
{

    public function change()
    {
        $exists = $this->hasTable('logbook_record');
        if ($exists) {
            $rows = $this->query("ALTER TABLE `logbook_record` DROP `inspection_type`");
            $rows = $this->query("ALTER TABLE  `logbook_record` CHANGE  `equipmant_id`  `equipment_id` INT( 255 ) NOT NULL");
            
        }else{
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