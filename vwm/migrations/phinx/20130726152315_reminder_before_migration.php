<?php

use Phinx\Migration\AbstractMigration;

class ReminderBeforeMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
     * 
     */
    public function change()
    {
        $this->getAdapter()->beginTransaction();
        
        $table = $this->table('reminder');
        $table->addColumn('beforehand_reminder_date', 'integer', array('limit' => 11, 'default' => null))
              ->addColumn('time_number', 'integer', array('limit' => 11, 'default' => 0))
              ->addColumn('reminder_unit_type_id', 'integer', array('limit' => 11, 'default' => 0))
              ->save();
        
        $this->query("INSERT INTO `unittype` (`unittype_id`, `name`, `unittype_desc`, `formula`, `type_id`, `system`, `unit_class_id`) VALUES (NULL, 'weeks', 'week', NULL, '8', 'time', '7'), (NULL, 'months', 'month', NULL, '8', 'time', '7'), (NULL, 'years', 'years', NULL, '8', 'time', '7')");
        
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