<?php

namespace VWM\Apps\UnitType\Manager;

use VWM\Framework\Test as Testing;
use VWM\Apps\Reminder\Entity\Reminder;

class UnitTypeManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        UnitTypeManager::TB_UNIT_TYPE
    );

    public function testGetTimeUnitTypeListByReminderPeriodicity()
    {
        $utManager = \VOCApp::getInstance()->getService('unitType');
        $daily = Reminder::DAILY;
        $weekly = Reminder::WEEKLY;
        $monthly = Reminder::MONTHLY;
        $yearly = Reminder::YEARLY;
        
        $unitTypeList = $utManager->getTimeUnitTypeListByReminderPeriodicity($daily);
        $this->assertTrue(count($unitTypeList) == 1);

        $unitTypeList = $utManager->getTimeUnitTypeListByReminderPeriodicity($weekly);
        $this->assertTrue(count($unitTypeList) == 1);

        $unitTypeList = $utManager->getTimeUnitTypeListByReminderPeriodicity($monthly);
        $this->assertTrue(count($unitTypeList) == 2);

        $unitTypeList = $utManager->getTimeUnitTypeListByReminderPeriodicity($yearly);
        $this->assertTrue(count($unitTypeList) == 3);
    }

}
?>
