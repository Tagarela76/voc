<?php

use Phinx\Migration\AbstractMigration;

class LogbookUnitTypeMigration extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('logbook_record');
        $table->addColumn('unittype_id', 'integer', array('default' => NULL))
                ->save();
        
        $exists = $this->hasTable('unit_class');
        if ($exists) {
            $rows = $this->query("INSERT INTO  `unit_class` (`id` ,`name` ,`description`)VALUES (NULL ,  'Temperature',  'Temperature')");
        }else{
            die('table unit_class is not exist');
        }
        
        $exists = $this->hasTable('type');
        if ($exists) {
            $rows = $this->query("INSERT INTO  `type` (`type_id` ,`type_desc`)VALUES (NULL ,  'Temperature')");
        }else{
            die('table type is not exist');
        }
        
        $exists = $this->hasTable('unittype');
        if ($exists) {
            $rows = $this->query("INSERT INTO `unittype` (`unittype_id`, `name`, `unittype_desc`, `formula`, `type_id`, `system`, `unit_class_id`) VALUES (NULL, 'C', 'celsius', NULL, '11', NULL, '9')");
            $rows = $this->query("INSERT INTO `unittype` (`unittype_id`, `name`, `unittype_desc`, `formula`, `type_id`, `system`, `unit_class_id`) VALUES (NULL, 'F', 'fahrenheit', NULL, '11', NULL, '9')");
        }else{
            die('table unittype is not exist');
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