<?php

use Phinx\Migration\AbstractMigration;

class AddDeletedMigration extends AbstractMigration
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
        $exists = $this->hasTable('inspection_persons');
        if ($exists) {
            $rows = $this->query("ALTER TABLE  `inspection_persons` ADD  `deleted` TINYINT NOT NULL DEFAULT  '0'");
        } else {
            die('table inspection_persons is not exist');
        }
        
        $exists = $this->hasTable('logbook_description');
        if ($exists) {
            $rows = $this->query("ALTER TABLE  `logbook_description` ADD  `deleted` TINYINT NOT NULL DEFAULT  '0'");
        } else {
            die('table logbook_description is not exist');
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