<?php

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use \VWM\Apps\Logbook\Entity\LogbookEquipment;

class LogbookEquipmentTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookEquipment::TABLE_NAME
    );

    public function testSave()
    {
        $db = VOCApp::getInstance()->getService('db');
        //INSERT
        $equipmentName = 'testInsertEquipment';
        $facilityId = 1;
        $logbookEquipment = new LogbookEquipment();
        $logbookEquipment->setFacilityId($facilityId);
        $logbookEquipment->setEquipDesc($equipmentName);
        $id = $logbookEquipment->save();

        $query = "SELECT * FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE equipment_id = {$db->sqltext($id)} LIMIT 1";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($facilityId, $rows[0]['facility_id']);
        $this->assertEquals($equipmentName, $rows[0]['equip_desc']);
        
        //UPDATE
        $equipmentNewName = 'testUpdateEquipment';
        $newFacilityId = 2;
        $logbookEquipment = new LogbookEquipment();
        $logbookEquipment->setId($id);
        $logbookEquipment->load();
        
        $logbookEquipment->setFacilityId($newFacilityId);
        $logbookEquipment->setEquipDesc($equipmentNewName);
        $logbookEquipment->save();
        
        $query = "SELECT * FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE equipment_id={$db->sqltext($id)} LIMIT 1";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($equipmentNewName, $rows[0]['equip_desc']);
        $this->assertEquals($newFacilityId, $rows[0]['facility_id']);
        
    }
    
    public function testDelete()
    {
        $db = VOCApp::getInstance()->getService('db');
        $equipmentName = 'testInsertEquipment';
        $facilityId = 1;
        $logbookEquipment = new LogbookEquipment();
        $logbookEquipment->setFacilityId($facilityId);
        $logbookEquipment->setEquipDesc($equipmentName);
        $id = $logbookEquipment->save();

        $query = "SELECT * FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE equipment_id={$db->sqltext($id)} LIMIT 1";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertTrue(count($rows)>0);
        
        $logbookEquipment->delete();
        
        $query = "SELECT * FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE equipment_id={$db->sqltext($id)} LIMIT 1";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertFalse(count($rows)>0);
    }

}
?>
