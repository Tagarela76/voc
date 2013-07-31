<?php

namespace VWM\Apps\UnitType\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\Reminder;

class UnitTypeManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        UnitTypeManager::TB_UNIT_TYPE
    );

    public function testGetTimeUnitTypeListByPeriodicity()
    {
        $utManager = \VOCApp::getInstance()->getService('unitType');
        $daily = Reminder::DAILY;
        $weekly = Reminder::WEEKLY;
        $monthly = Reminder::MONTHLY;
        $yearly = Reminder::YEARLY;
        
        $unitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($daily);
        $this->assertTrue(count($unitTypeList) == 1);

        $unitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($weekly);
        $this->assertTrue(count($unitTypeList) == 1);

        $unitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($monthly);
        $this->assertTrue(count($unitTypeList) == 2);

        $unitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($yearly);
        $this->assertTrue(count($unitTypeList) == 3);
    }
    
    public function testGetUnitTypesByPeriodicity()
    {
        $utManager = \VOCApp::getInstance()->getService('unitType');
        $daily = Reminder::DAILY;
        $yearly = Reminder::YEARLY;
        
        $unitTypes = array();
        
        $condition = $utManager->getUnitTypesByPeriodicity($unitTypes, $daily);
        $this->assertEquals($condition[0], "'days'");
        $condition = $utManager->getUnitTypesByPeriodicity($unitTypes, $yearly);
        $this->assertTrue(count($condition) == 3);
    }

}
?>
