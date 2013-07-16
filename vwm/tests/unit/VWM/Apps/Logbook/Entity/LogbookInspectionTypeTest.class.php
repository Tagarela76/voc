<?php
namespace VWM\Apps\Logbook\Entity;

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Entity\InspectionTypeSettings;
use VWM\Apps\Logbook\Entity\InspectionSubTypeSettings;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;

class LogbookInspectionTypeTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionType::TABLE_NAME
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
    
    public function testDelete()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $typeName = 'testDeleteInspectionTypeName';
        $permit = 1;
        $subTypes = array();
        //create inspection setting
        $inspectionTypeSettings = new InspectionTypeSettings();
        $inspectionTypeSettings->setTypeName($typeName);
        $inspectionTypeSettings->setPermit($permit);
        $inspectionTypeSettings->setSubtypes($subTypes);
        $inspectionTypeSettingsToJson = $inspectionTypeSettings->toJson();

        $facilityId = 120;
        $inspectionType = new LogbookInspectionType();
        $inspectionType->setInspectionTypeRaw($inspectionTypeSettingsToJson);
        
        $id = $inspectionType->save();

        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME . " " .
                "WHERE id=" . $id;
        $db->query($query);
        $this->assertFalse($db->num_rows() == 0);
        $inspectionType->delete();
        $this->assertTrue($db->num_rows() == 0);
    }

    

}
?>
