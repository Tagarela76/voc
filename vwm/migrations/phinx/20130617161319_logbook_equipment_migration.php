<?php

use Phinx\Migration\AbstractMigration;

class LogbookEquipmentMigration extends AbstractMigration
{
    
    public function change()
    {
        $table = $this->table('logbook_equipment');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('name', 'string', array('limit' => 2000))
                ->addColumn('facility_id', 'int', array('limit' => 11))
                ->create();
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