<?php

use Phinx\Migration\AbstractMigration;

class Logbook extends AbstractMigration
{
   public function change()
   {
        // create the table
            $table = $this->table('logbook_record');
        $table->addColumn('id', 'integer')
                ->addColumn('facility_id', 'integer')
                ->addColumn('departmet_id', 'integer', array('default' => null))
                ->addColumn('inspection_type', 'string', array('limit' => 2000))
                ->addColumn('inspection_sub_type', 'string', array('limit' => 2000))
                ->addColumn('inspection_person_id', 'integer')
                ->addColumn('date_time', 'integer')
                ->addColumn('description', 'string', array('limit' => 2000))
                ->addColumn('description_notes', 'string', array('default' => null, 'limit' => 2000))
                ->addColumn('permit', 'tinyint')
                ->addColumn('sub_type_notes', 'string', array('default' => null, 'limit' => 2000))
                ->addColumn('qty', 'integer', array('default' => null))
                ->addColumn('gauge_type', 'integer', array('default' => null))
                ->addColumn('gauge_value', 'integer', array('default' => null))
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