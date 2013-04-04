<?php

use Phinx\Migration\AbstractMigration;

class LinexTotalsMigration extends AbstractMigration
{
    public function change(){
        $table = $this->table('work_order');
        $table->addColumn('overhead', 'integer', array('default' => 0))
              ->save();
        $table->addColumn('profit', 'integer', array('default' => 0))
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