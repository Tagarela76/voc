<?php

use Phinx\Migration\AbstractMigration;

class NewReminderUserMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     **/
    public function change()
    {
        $this->getAdapter()->beginTransaction();
        $table = $this->table('reminder_user');
        $table
                ->addColumn('user_id', 'integer', array('limit'=>11,'default'=>null))
                ->addColumn('email', 'string', array('limit'=>2000, 'default'=>NULL))
                ->addColumn('facility_id', 'integer', array('limit'=>11))
                ->create();
        
        $table2 = $this->table('reminder2reminder_user');
        $table2
                ->addColumn('reminder_id', 'integer', array('limit'=>11))
                ->addColumn('reminder_user_id', 'integer', array('limit'=>11))
                ->create();
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