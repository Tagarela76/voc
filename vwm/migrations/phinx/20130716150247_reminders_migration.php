<?php

use Phinx\Migration\AbstractMigration;

class RemindersMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     * */
    public function change()
    {
        $this->getAdapter()->beginTransaction();
        
        $exists = $this->hasTable('reminder');
        if ($exists) {
            $rows = $this->query("ALTER TABLE `reminder` ADD  `priority` INT NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE  `reminder` ADD  `type` VARCHAR( 2000 ) NULL DEFAULT NULL");
            $rows = $this->query("ALTER TABLE  `reminder` ADD  `appointment` INT( 11 ) NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE  `reminder` ADD  `periodicity` INT( 11 ) NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE  `reminder` ADD  `active` TINYINT NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE  `reminder` ADD  `delivery_date` INT( 11 ) NULL DEFAULT NULL AFTER  `date`");
        } else {
            die('table reminder is not exist');
        }
        
        $this->getAdapter()->commitTransaction();
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