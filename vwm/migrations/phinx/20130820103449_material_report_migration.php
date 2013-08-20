<?php

use Phinx\Migration\AbstractMigration;

class MaterialReportMigration extends AbstractMigration
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
         $rows = $this->query("INSERT INTO report SET name='Costing Report by Product', type='costingProduct', description='Costing report by Product in Work Order'");
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