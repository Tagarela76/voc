<?php

use Phinx\Migration\AbstractMigration;

class ChangeLogbookMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     **/
   public function change()
    {
        $this->getAdapter()->beginTransaction();

        $exists = $this->hasTable('logbook_record');
        if ($exists) {
            $rows = $this->query("ALTER TABLE  `logbook_record` CHANGE  `description`  `description_id` INT( 11 ) NOT NULL");
            $rows = $this->query("ALTER TABLE `logbook_record` DROP `department_id`");
        } else {
            die('table logbook_record is not exist');
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