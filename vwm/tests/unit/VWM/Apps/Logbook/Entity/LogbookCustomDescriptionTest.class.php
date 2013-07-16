<?php
namespace VWM\Apps\Logbook\Entity;

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookCustomDescription;


class LogbookDescriptionTest extends Testing\DbTestCase
{
    protected $fixtures = array(
        LogbookCustomDescription::TABLE_NAME
    );

    public function testSave()
    {
        
       $db = \VOCApp::getInstance()->getService('db');

        // test INSERT
        $description = 'test custom logbook description insert';
        $notes = 1;
        $origin = 'custom_description';
        $inspectionTypeId = 1;
        $facilityId = 1;

        $logbookCustomDescription = new LogbookCustomDescription();
        $logbookCustomDescription->setDescription($description);
        $logbookCustomDescription->setNotes($notes);
        $logbookCustomDescription->setInspectionTypeId($inspectionTypeId);
        $logbookCustomDescription->setFacilityId($facilityId);
        $id = $logbookCustomDescription->save();

        $query = "SELECT * FROM " . LogbookCustomDescription::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($id)} LIMIT 1";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($description, $rows[0]['description']);
        $this->assertEquals($inspectionTypeId, $rows[0]['inspection_type_id']);
        $this->assertEquals($notes, $rows[0]['notes']);
        $this->assertEquals($facilityId, $rows[0]['facility_id']);
        $this->assertEquals($origin, $rows[0]['origin']);
        
        //test UPDATE
        $description = 'test logbook custom description update';
        $notes = 0;
        $inspectionTypeId = 2;
        $facilityId = 2;
        $logbookCustomDescription->setDescription($description);
        $logbookCustomDescription->setNotes($notes);
        $logbookCustomDescription->setInspectionTypeId($inspectionTypeId);
        $logbookCustomDescription->setFacilityId($facilityId);
        $logbookCustomDescription->save();
        
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($description, $rows[0]['description']);
        $this->assertEquals($inspectionTypeId, $rows[0]['inspection_type_id']);
        $this->assertEquals($notes, $rows[0]['notes']);
        $this->assertEquals($facilityId, $rows[0]['facility_id']);
        $this->assertEquals($origin, $rows[0]['origin']);
        
    }
    
    public function testGetAttributes()
    {
        $description = 'test logbook description insert';
        $notes = 1;
        $origin = 'custom_description';
        $inspectionTypeId = 1;
        $facilityId = 1;
        
        $logbookCustomDescription = new LogbookCustomDescription();
        $logbookCustomDescription->setDescription($description);
        $logbookCustomDescription->setNotes($notes);
        $logbookCustomDescription->setInspectionTypeId($inspectionTypeId);
        $logbookCustomDescription->setFacilityId($facilityId);
        $id = $logbookCustomDescription->save();
        $attributes = $logbookCustomDescription->getAttributes();
        
        $this->assertEquals($description, $attributes['description']);
        $this->assertEquals($inspectionTypeId, $attributes['inspection_type_id']);
        $this->assertEquals($notes, $attributes['notes']);
        $this->assertEquals($facilityId, $attributes['facility_id']);
        $this->assertEquals($origin, $attributes['origin']);
        $this->assertEquals($id, $attributes['id']);
    }
    
    public function testDelete()
    {
        $description1 = 'test1';
        $inspectionTypeId = '100';
        $facilityId = 1;
        
        $logbookCustomDescription = new LogbookCustomDescription();
        $logbookCustomDescription->setInspectionTypeId($inspectionTypeId);
        $logbookCustomDescription->setDescription($description1);
        $logbookCustomDescription->setNotes('0');
        $id = $logbookCustomDescription->save();
        
        $db = \VOCApp::getInstance()->getService('db');
        
        $query = "SELECT * FROM ".$logbookCustomDescription::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($id)} ";
        $db ->query($query);
        $this->assertFalse($db->num_rows() == 0);
        
        $logbookCustomDescription->delete();
        
        $db ->query($query);
        $rows = $db->fetch_all_array();
        $this->assertEquals($rows[0]['deleted'], '1');
        
    }

}
?>
