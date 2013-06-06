<?php

use Phinx\Migration\AbstractMigration;

class LogbokInspectionTypeMigration extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('inspection_type');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('settings', 'string', array('limit' => 2000))
                ->addColumn('facility_id', 'int', array('limit' => 11))
                ->create();
        
        $table = $this->table('inspection_type2facility');
        $table->addColumn('id', 'integer', array('limit' => 11))
                ->addColumn('inspection_type_id', 'int', array('limit' => 11))
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