<?php

use Phinx\Migration\AbstractMigration;

class SortColumMigration extends AbstractMigration
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
        
        $table = $this->table('preformulated_products');
        $table
              ->addColumn('weight_letter_sort', 'string', array('limit' => 255, 'default' => null))
              ->addColumn('weight_number_sort', 'integer', array('limit' => 11, 'default' => 0))
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