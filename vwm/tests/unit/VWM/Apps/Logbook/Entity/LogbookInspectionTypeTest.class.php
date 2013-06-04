<?php

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Entity\InspectionTypeSettings;
use VWM\Apps\Logbook\Entity\InspectionSubTypeSettings;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;

class LogbookInspectionTypeTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionType::TABLE_NAME,
        InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY
    );

    public function testSave()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $typeName = 'testInspectionTypeName';
        $permit = 1;
        $subTypes = array();
        //first sub type
        $subTypeName = 'testSubType';
        $notes = 1;
        $qty = 1;
        $gauge = 0;
        $subType = new InspectionSubTypeSettings;
        $subType->setName($subTypeName);
        $subType->setNotes($notes);
        $subType->setQty($qty);
        $subType->setValueGauge($gauge);
        $subTypes[] = $subType->getAttributes();

        //create inspection setting
        $inspectionTypeSettings = new InspectionTypeSettings();
        $inspectionTypeSettings->setTypeName($typeName);
        $inspectionTypeSettings->setPermit($permit);
        $inspectionTypeSettings->setSubtypes($subTypes);
        $inspectionTypeSettingsToJson = $inspectionTypeSettings->toJson();

        $facilityId = 120;
        $inspectionType = new LogbookInspectionType();
        $inspectionType->setInspectionTypeRaw($inspectionTypeSettingsToJson);
        $inspectionType->setFacilityIds($facilityId);
        $id = $inspectionType->save();

        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME . " " .
                "WHERE id=" . $id;
        $db->query($query);
        $result = $db->fetch_all_array();
        $newInspectionType = $result[0]['settings'];
        $newInspectionType = json_decode($newInspectionType);

        $this->assertEquals($newInspectionType->permit, $permit);
        $this->assertEquals($newInspectionType->typeName, $typeName);
        $this->assertEquals($newInspectionType->subtypes[0]->name, $subTypeName);
        $this->assertEquals($newInspectionType->subtypes[0]->qty, $qty);
        $this->assertEquals($newInspectionType->subtypes[0]->notes, $notes);
        $this->assertEquals($newInspectionType->subtypes[0]->valueGauge, $gauge);
    }

    public function testAssignInspectionTypeToFacility()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $id = 6;
        $facilityId = 100;
        $ltManager = \VOCApp::getInstance()->getService('inspectionType');
        //check save facility ti inspection type
        $ltManager->assignInspectionTypeToFacility($id, $facilityId);
        $query = "SELECT * FROM " . InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id=" . $id;
        $db->query($query);
        $result = $db->fetch_all_array();
        $typeFacilityId = $result[0]['facility_id'];
        $typeId = $result[0]['inspection_type_id'];
        $this->assertEquals($typeFacilityId, $facilityId);
        $this->assertEquals($typeId, $id);
    }

}
?>
