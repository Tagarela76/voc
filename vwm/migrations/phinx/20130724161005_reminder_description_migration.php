<?php

use Phinx\Migration\AbstractMigration;

class ReminderDescriptionMigration extends AbstractMigration
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
        $table = $this->table('reminder');
        
        $table->addColumn('description', 'string', array('limit' => 2000, 'default' => null))
              ->save();
        
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