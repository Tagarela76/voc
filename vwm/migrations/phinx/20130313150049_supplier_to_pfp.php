<?php

use Phinx\Migration\AbstractMigration;

class SupplierToPfp extends AbstractMigration
{
    public function change()
    {
        // create the table
        $table = $this->table('preformulated_products');
        $table->addColumn('supplier_id', 'integer')
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