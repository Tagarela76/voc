<?php

use Phinx\Migration\AbstractMigration;

class ToUtf extends AbstractMigration
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
        $tables = $this->fetchAll('SHOW TABLES');
        if ($tables) {
            foreach ($tables as $table) {
                $this->execute("ALTER TABLE `{$table[0]}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
            }
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
