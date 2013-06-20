<?php

use Phinx\Migration\AbstractMigration;

class LogbookDescriptionMigration extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('logbook_description');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('description', 'string', array('limit' => 2000))
                ->addColumn('notes', 'boolean', array('default' => 0))
                ->addColumn('origin', 'string', array('limit' => 2000))
                ->addColumn('inspection_type_id', 'integer', array('limit' => 11))
                ->addColumn('facility_id', 'integer', array('default' => NULL, 'limit' => 11))
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