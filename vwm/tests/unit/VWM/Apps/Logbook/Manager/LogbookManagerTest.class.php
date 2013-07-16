<?php
namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Logbook\Manager\LogbookManager;
use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;

class LogbookManagerTest extends Testing\DbTestCase
{
    protected $fixtures = array(
        LogbookInspectionPerson::TABLE_NAME
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

}
?>