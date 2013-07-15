<?php

use Phinx\Migration\AbstractMigration;

class FilterLogbookEquipment extends AbstractMigration
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
        $exists = $this->hasTable('filter');
        if ($exists) {
            $rows = $this->query("INSERT INTO  `filter` (`id` ,`filter_class` ,`field_name` ,`name_in_table` ,`parent` ,`autocomplete`)VALUES (NULL ,  'text',  'Equipment  Inspection Types ',  'equipment',  'logbookRecord',  'no')");
            $rows = $this->query("INSERT INTO  `filter` (`id` ,`filter_class` ,`field_name` ,`name_in_table` ,`parent` ,`autocomplete`)VALUES (NULL ,  'text',  'Facility Health & Safety (H&S) Inspection Types',  'equipment',  'logbookRecord',  'no')");
        } else {
            die('table equipment is not exist');
        }
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