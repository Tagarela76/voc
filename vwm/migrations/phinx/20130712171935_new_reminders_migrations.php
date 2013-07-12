<?php

use Phinx\Migration\AbstractMigration;

class NewRemindersMigrations extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
    public function change()
    {
        $exists = $this->hasTable('reminder');
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