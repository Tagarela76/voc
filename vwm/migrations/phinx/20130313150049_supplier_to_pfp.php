<?php

use Phinx\Migration\AbstractMigration;

class SupplierToPfp extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('preformulated_products');
        $table->addColumn('supplier_id', 'integer', array('null'=>true,'default' => null))
              ->save();
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
