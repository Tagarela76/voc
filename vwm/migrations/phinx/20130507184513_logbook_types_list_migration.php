<?php

use Phinx\Migration\AbstractMigration;

class LogbookTypesListMigration extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('logbook_setup_template');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('name', 'string', array('limit' => 2000))
                ->create();
        
        $table = $this->table('inspection_type2logbook_setup_template');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('inspection_type_id', 'integer', array('limit' => 11))
                ->addColumn('logbook_setup_template_id', 'integer', array('limit' => 11))
                ->create();
        
        $table = $this->table('logbook_setup_template2facility');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('logbook_setup_template_id', 'integer', array('limit' => 11))
                ->addColumn('facility_id', 'integer', array('limit' => 11))
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