<?php

use Phinx\Migration\AbstractMigration;

class LogbookTemplateMigration extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('inspection_description');
        $table->addColumn('id', 'integer', array('limit' => 20))
                ->addColumn('description_settings', 'string', array('limit' => 2000))
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