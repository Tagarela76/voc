<?php

use Phinx\Migration\AbstractMigration;

class HabsMigration extends AbstractMigration
{
    
     public function change()
    {
        $table = $this->table('component');
        if ($table) {
            $rows = $this->query("ALTER TABLE  `component` ADD  `HAPs` INT( 255 ) NOT NULL DEFAULT  '0'");
        }else{
            die('table is not exist');
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