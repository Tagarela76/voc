<?php

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;

class InspectionTypeManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionType::TABLE_NAME,
        InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY
    );

    public function testGetFacilityIdsByInspectionTypeId()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $itManager = \VOCApp::getInstance()->getService('inspectionType');
        $inspectionTypeId = 1;
        $facilityIds = $itManager->getFacilityIdsByInspectionTypeId($inspectionTypeId);
        $query = "SELECT facility_id " .
                "FROM " . InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id	= {$db->sqltext($inspectionTypeId)}";
        $db->query($query);
        $result = $db->fetch_all_array();
        $newFacilityIds = array();
        foreach ($result as $r) {
            $newFacilityIds[] = $r['facility_id'];
        }
        $this->assertEquals($facilityIds, $newFacilityIds);
    }

    public function testAssignUnassignInspectionTypeToFacility()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $itManager = \VOCApp::getInstance()->getService('inspectionType');
        $inspectionTypeId = 70;
        $facilityId = 105;
        $itManager->assignInspectionTypeToFacility($inspectionTypeId, $facilityId);

        $query = "SELECT * FROM " . InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} " .
                "AND facility_id = {$db->sqltext($facilityId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        $this->assertEquals($result[0]['inspection_type_id'], $inspectionTypeId);
        $this->assertEquals($result[0]['facility_id'], $facilityId);

        $itManager->unAssignInspectionTypeToFacility($inspectionTypeId, $facilityId);
        $query = "SELECT * FROM " . InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} " .
                "AND facility_id = {$db->sqltext($facilityId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();

        $this->assertTrue(empty($result));
    }

    public function testGetInspectionTypeList()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $itManager = \VOCApp::getInstance()->getService('inspectionType');
        $facilityId = 100;
        $inspectionTypeList = $itManager->getInspectionTypeList($facilityId);
        $this->assertInstanceOf('\VWM\Apps\Logbook\Entity\LogbookInspectionType', $inspectionTypeList[0]);

        $newInspectionTypeList = array();
        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME . " lbt " .
                "LEFT JOIN " . InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY . " it " .
                "ON it.inspection_type_id = lbt.id " .
                "WHERE it.facility_id=" . $facilityId;
        $db->query($query);
        $result = $db->fetch_all_array();
        //get inspection type ids by facility id
        foreach ($result as $r) {
            $inspectionType = new LogbookInspectionType();
            $inspectionType->initByArray($r);
            $newInspectionTypeList[] = $inspectionType;
        }
        $this->assertEquals($newInspectionTypeList, $inspectionTypeList);
    }

}
?>