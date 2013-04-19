<?php

use Phinx\Migration\AbstractMigration;

class InspectionPerson extends AbstractMigration
{
    public function change()
   {
        // create the table
        $table = $this->table('inspection_persons');
        $table->addColumn('id', 'integer')
                ->addColumn('facility_id', 'integer')
                ->addColumn('name', 'string')
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