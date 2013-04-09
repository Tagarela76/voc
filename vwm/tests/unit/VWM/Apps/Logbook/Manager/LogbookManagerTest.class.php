<?php

use VWM\Framework\Test as Testing;
use VWM\Apps\Logbook\Manager\LogbookManager;

class LogbookManagerTest extends Testing\DbTestCase
{
    public function testGetInspectionType(){
        $manager = new LogbookManager();
        $inspectionTypeList = $manager->getInspectionType();
        $this->assertEquals(count($inspectionTypeList), 8);
    }
}
?>