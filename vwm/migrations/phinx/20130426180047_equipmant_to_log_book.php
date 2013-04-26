<?php

use Phinx\Migration\AbstractMigration;

class EquipmantToLogBook extends AbstractMigration
{
       public function change(){
        $table = $this->table('logbook_record');
        $table->addColumn('equipmant_id', 'integer', array('null'=>true,'default' => null))
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