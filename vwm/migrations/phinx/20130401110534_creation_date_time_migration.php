<?php

use Phinx\Migration\AbstractMigration;

class CreationDateTimeMigration extends AbstractMigration
{
    
    public function change(){
        $exist->$this->hasTable('work_order');
        if($exist){
            $sql = "ALTER TABLE  `work_order` ".
                   "CHANGE  `creation_time` ".
                   "`creation_time` DATE NULL DEFAULT NULL";
            $rows = $this->query($sql);
        }  else {
            die('table does not exist');
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