<?php

use Phinx\Migration\AbstractMigration;

class WoUnitTypeMigration extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('work_order');
        $table->addColumn('profit_unit_type', 'integer', array('default' => 0))
                ->save();
        $table->addColumn('overhead_unit_type', 'integer', array('default' => 0))
                ->save();
        $sql = "ALTER TABLE  `work_order` " .
                "CHANGE  `overhead`  `overhead` FLOAT NOT NULL DEFAULT  '0'";
        $rows = $this->query($sql);
        $sql = "ALTER TABLE  `work_order` " .
                "CHANGE  `profit`  `profit` FLOAT NOT NULL DEFAULT  '0'";
        $rows = $this->query($sql);
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