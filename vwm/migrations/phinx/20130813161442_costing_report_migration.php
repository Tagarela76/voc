<?php

use Phinx\Migration\AbstractMigration;

class CostingReportMigration extends AbstractMigration
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
         $rows = $this->query("INSERT INTO report SET name='Costing Report', type='costing', description='costing report of Work order with process'");
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