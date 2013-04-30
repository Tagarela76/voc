<?php

use Phinx\Migration\AbstractMigration;

class ReplacedBubles extends AbstractMigration
{
    
    public function change()
    {
        $table = $this->table('logbook_record');
        $table->addColumn('replaced_bulbs', 'boolean', array('default' => 0))
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