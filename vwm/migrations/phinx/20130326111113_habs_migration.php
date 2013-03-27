<?php

use Phinx\Migration\AbstractMigration;

class HabsMigration extends AbstractMigration
{
    
    public function change()
    {
        $table = $this->table('component');
        if ($table) {
            $table->addColumn('HAPs', 'integer', array('default' => 0));

            $rows = $this->query("INSERT INTO `report` (`report_id`, `name`, `type`, `description`) " . 
                "VALUES (NULL, 'HAPs', 'hapsCoat', 'Hazardous Air Pollutant ')");
        }else{
            die('table is not exist');
        }
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
