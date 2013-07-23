<?php

use Phinx\Migration\AbstractMigration;

class LogbookToDoMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     */
    public function change()
    {
        $this->getAdapter()->beginTransaction();
        
        $table = $this->table('logbook_record_to_do');
        $table->addColumn('facility_id', 'integer', array('limit' => 11))
                ->addColumn('inspection_sub_type', 'string', array('limit' => 2000, 'default' => null))
                ->addColumn('inspection_person_id', 'integer', array('limit' => 11))
                ->addColumn('date_time', 'integer', array('limit' => 255))
                ->addColumn('description_id', 'integer', array('limit' => 11))
                ->addColumn('description_notes', 'string', array('limit' => 2000, 'default' => null))
                ->addColumn('sub_type_notes', 'string', array('limit' => 2000, 'default' => null))
                ->addColumn('qty', 'integer', array('limit' => 255, 'default' => null))
                ->addColumn('gauge_type', 'integer',  array('limit' => 11, 'default' => null))
                ->addColumn('gauge_value_from', 'float',  array('default' => 0))
                ->addColumn('gauge_value_to', 'float',  array('default' => 0))
                ->addColumn('equipment_id', 'integer', array('limit' => 11, 'default' => 0))
                ->addColumn('min_gauge_range', 'integer',  array('limit' => 255, 'default' => 0))
                ->addColumn('max_gauge_range', 'integer',  array('limit' => 255, 'default' => 100))
                ->addColumn('inspection_addition_type', 'string', array('limit' => 2000, 'default' => null))
                ->addColumn('unittype_id', 'integer', array('limit' => 255, 'default' => null))
                ->addColumn('inspection_type_id', 'integer', array('limit' => 11, 'default' => null))
                ->addColumn('is_recurring', 'boolean', array('default' => 0))
                ->addColumn('periodicity', 'integer', array('limit' => 11, 'default' => 0))
                ->addColumn('parent_id', 'integer', array('limit' => 255, 'default' => 0))
                ->create();
        
        $exists = $this->hasTable('logbook_record');
        if ($exists) {
            $rows = $this->query("ALTER TABLE  `logbook_record` ADD  `next_date` INT( 11 ) NOT NULL AFTER  `date_time`");
            $rows = $this->query("ALTER TABLE  `logbook_record` ADD  `is_recurring` TINYINT NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE  `logbook_record` ADD  `periodicity` INT( 11 ) NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE  `logbook_record` ADD  `parent_id` INT( 11 ) NOT NULL DEFAULT  '0'");
            $rows = $this->query("ALTER TABLE `logbook_record` DROP `department_id`");
            $rows = $this->query("ALTER TABLE `logbook_record` DROP `replaced_bulbs`");
            $rows = $this->query("ALTER TABLE `logbook_record` DROP `permit`");
        } else {
            die('table logbook_record is not exist');
        }
        
        $this->getAdapter()->commitTransaction();
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