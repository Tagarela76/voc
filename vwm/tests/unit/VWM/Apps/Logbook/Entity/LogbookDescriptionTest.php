<?php
namespace VWM\Apps\Logbook\Entity;

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookDescription;

class LogbookDescriptionTest extends Testing\DbTestCase
{
    protected $fixtures = array(
        LogbookDescription::TABLE_NAME
    );

    public function testSave()
    {
        
       $db = \VOCApp::getInstance()->getService('db');

        // test INSERT
        $description = 'test logbook description insert';
        $notes = 1;
        $origin = 'inspection_type';
        $inspectionTypeId = 1;

        $logbookDescription = new LogbookDescription();
        $logbookDescription->setDescription($description);
        $logbookDescription->setNotes($notes);
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $id = $logbookDescription->save();

        $query = "SELECT * FROM " . LogbookDescription::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($id)} LIMIT 1";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($description, $rows[0]['description']);
        $this->assertEquals($inspectionTypeId, $rows[0]['inspection_type_id']);
        $this->assertEquals($notes, $rows[0]['notes']);
        $this->assertNull($rows[0]['facility_id']);
        $this->assertEquals($origin, $rows[0]['origin']);
        
        //test UPDATE
        $description = 'test logbook description update';
        $notes = 0;
        $inspectionTypeId = 2;
        $logbookDescription->setDescription($description);
        $logbookDescription->setNotes($notes);
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->save();
        
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $this->assertEquals($description, $rows[0]['description']);
        $this->assertEquals($inspectionTypeId, $rows[0]['inspection_type_id']);
        $this->assertEquals($notes, $rows[0]['notes']);
        $this->assertNull($rows[0]['facility_id']);
        $this->assertEquals($origin, $rows[0]['origin']);
        
    }
    
    public function testGetAttributes()
    {
        $description = 'test logbook description insert';
        $notes = 1;
        $origin = 'inspection_type';
        $inspectionTypeId = 1;

        $logbookDescription = new LogbookDescription();
        $logbookDescription->setDescription($description);
        $logbookDescription->setNotes($notes);
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $id = $logbookDescription->save();
        $attributes = $logbookDescription->getAttributes();
        
        $this->assertEquals($description, $attributes['description']);
        $this->assertEquals($inspectionTypeId, $attributes['inspection_type_id']);
        $this->assertEquals($notes, $attributes['notes']);
        $this->assertNull($attributes['facility_id']);
        $this->assertEquals($origin, $attributes['origin']);
        $this->assertEquals($id, $attributes['id']);
    }
    
    public function testDelete()
    {
        $description1 = 'test1';
        $inspectionTypeId = '100';
        
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description1);
        $logbookDescription->setNotes('0');
        $id = $logbookDescription->save();
        
        $db = \VOCApp::getInstance()->getService('db');
        
        $query = "SELECT * FROM ".LogbookDescription::TABLE_NAME." ".
                 "WHERE id={$db->sqltext($id)} ";
        $db ->query($query);
        $this->assertFalse($db->num_rows() == 0);
        
        $logbookDescription->delete();
        
        $db ->query($query);
        $rows = $db->fetch_all_array();
        $this->assertEquals($rows[0]['deleted'], '1');
        
    }

}
?>
