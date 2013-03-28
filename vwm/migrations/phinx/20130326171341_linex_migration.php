<?php

use Phinx\Migration\AbstractMigration;

class LinexMigration extends AbstractMigration
{
    
     public function change()
    {
        $table = $this->table('work_order');
        $table->addColumn('creation_time', 'varchar', array('null'=>true,'default' => null))
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