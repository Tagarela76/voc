<?php

namespace VWM\Hierarchy;

use VWM\Framework\Test\DbTestCase;

class FacilityManagerTest extends DbTestCase
{

    protected $fixtures = array(
        Company::TABLE_NAME,
        Facility::TABLE_NAME,
        Department::TABLE_NAME,
        Facility::TB_PROCESS,
        TB_DEFAULT,
        TB_TYPE,
        TB_UNITCLASS,
        TB_UNITTYPE
    );

    public function testGetAllFacilityList()
    {
        $facilityManager = new FacilityManager();
        $facilityList = $facilityManager->getAllFacilityList();
        $this->assertTrue($facilityList[0] instanceof Facility);
        $this->assertEquals(count($facilityList), 2);
    }

}
?>
