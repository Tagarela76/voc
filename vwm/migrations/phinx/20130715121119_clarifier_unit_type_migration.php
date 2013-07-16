<?php

use Phinx\Migration\AbstractMigration;

class ClarifierUnitTypeMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->getAdapter()->beginTransaction();
        
        $exists = $this->hasTable('unit_class');
        if ($exists) {
            $rows = $this->query("INSERT INTO  `unit_class` (`id` ,`name` ,`description`)VALUES (NULL ,  'Acidity',  'Acidity')");
        }else{
            die('table unit_class is not exist');
        }
        
        $exists = $this->hasTable('type');
        if ($exists) {
            $rows = $this->query("INSERT INTO  `type` (`type_id` ,`type_desc`)VALUES (NULL ,  'Acidity')");
        }else{
            die('table type is not exist');
        }
        
        $exists = $this->hasTable('unittype');
        if ($exists) {
            $rows = $this->query("INSERT INTO  `unittype` (`unittype_id` ,`name` ,`unittype_desc` ,`formula` ,`type_id` ,`system` ,`unit_class_id`)VALUES (NULL ,  'pH',  'pH', NULL ,  '11',  'metric',  '10')");
        }else{
            die('table unittype is not exist');
        }
        
        $this->getAdapter()->commitTransaction();
    
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}