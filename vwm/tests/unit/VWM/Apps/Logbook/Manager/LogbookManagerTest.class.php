<?php
namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Apps\Logbook\Entity\LogbookRecord;

class LogbookManagerTest extends Testing\DbTestCase
{
    protected $fixtures = array(
        LogbookInspectionPerson::TABLE_NAME,
        LogbookRecord::TABLE_NAME
    );
    
    public function testGetLogbookInspectionPersonListByFacilityId()
    {
        
        $facilityId = 1;
        $manager = new LogbookManager();
        $inspectionPersonList = $manager->getLogbookInspectionPersonListByFacilityId($facilityId);
        $this->assertEquals(count($inspectionPersonList), 3);
        
        $this->assertEquals($inspectionPersonList[0]->getName(), 'Tagarela');
        $this->assertEquals($inspectionPersonList[1]->getName(), 'Mika');
        $this->assertEquals($inspectionPersonList[2]->getName(), 'Mulan');
    }
    
    public function testGetRecurringLogbookList()
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $facilityId = 2;
        
        $recurringLogbookList = $lbManager->getRecurringLogbookList($facilityId);
        
        $this->assertTrue($recurringLogbookList[0] instanceof LogbookRecord);
        $this->assertEquals(count($recurringLogbookList), 1);
        
        $recurringLogbookList = $lbManager->getRecurringLogbookList();
        
        $this->assertTrue($recurringLogbookList[0] instanceof LogbookRecord);
        $this->assertEquals(count($recurringLogbookList), 2);
    }

}
?>