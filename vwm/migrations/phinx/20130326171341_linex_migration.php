<?php

use Phinx\Migration\AbstractMigration;

class LinexMigration extends AbstractMigration
{
    
     public function change()
    {
        $table = $this->table('work_order');
        $table->addColumn('creation_time', 'datetime', array('null'=>true,'default' => null));
              
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