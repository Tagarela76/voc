<?php

use Phinx\Migration\AbstractMigration;

class VocPMMigration extends AbstractMigration
{
     public function change(){
        $table = $this->table('component');
        $table->addColumn('VOC_PM', 'tinyint', array('default' => 0))
              ->save();
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