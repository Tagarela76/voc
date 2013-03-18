<?php

use Phinx\Migration\AbstractMigration;

class AddUnitTypeMigration extends AbstractMigration
{
    
    public function change()
    {
        $exists = $this->hasTable('users');
        if ($exists) {
            $rows = $this->query("INSERT INTO `unittype` (`unittype_id`, `name`, `unittype_desc`, `formula`, `type_id`, `system`, `unit_class_id`) VALUES (NULL, 'SF', 'Square feet', NULL, '7', 'metric', '8')");
            $rows = $this->query("UPDATE `unittype` SET  `unit_class_id` =  '8' WHERE `unittype_id` = 46");
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