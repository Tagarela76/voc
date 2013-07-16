<?php
namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookDescription;
use VWM\Apps\Logbook\Manager\LogbookDescriptionManager;

class LogbookDescroptionManagerTest  extends Testing\DbTestCase
{
   protected $fixtures = array(
         LogbookDescription::TABLE_NAME
    );
   
    public function testGetDescriptionListByInspectionTypeId()
    {
        $ldManager = new LogbookDescriptionManager();
        $inspectionTypeId = '100';
        
        $description1 = 'test1';
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description1);
        $logbookDescription->setNotes('0');
        $logbookDescription->save();
        
        
        $description2 = 'test2';
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description2);
        $logbookDescription->setNotes('1');
        $logbookDescription->save();
        
        
        $description3 = 'test3';
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description3);
        $logbookDescription->setNotes('1');
        $logbookDescription->save();
        
        
        $logbookDescriptionList = $ldManager->getDescriptionListByInspectionTypeId($inspectionTypeId);
        
        $this->assertTrue(count($logbookDescriptionList) == 3);
        
        $this->assertEquals($logbookDescriptionList[0]->getDescription(), $description1);
        $this->assertEquals($logbookDescriptionList[1]->getDescription(), $description2);
        $this->assertEquals($logbookDescriptionList[2]->getDescription(), $description3);
        
    }
    
    public function testDeleteDescriptionsByInspectionTypeId()
    {
        $ldManager = new LogbookDescriptionManager();
        $inspectionTypeId = '100';
        
        $description1 = 'test1';
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description1);
        $logbookDescription->setNotes('0');
        $logbookDescription->save();
        
        
        $description2 = 'test2';
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description2);
        $logbookDescription->setNotes('1');
        $logbookDescription->save();
        
        
        $description3 = 'test3';
        $logbookDescription = new LogbookDescription();
        $logbookDescription->setInspectionTypeId($inspectionTypeId);
        $logbookDescription->setDescription($description3);
        $logbookDescription->setNotes('1');
        $logbookDescription->save();
        
        $db = \VOCApp::getInstance()->getService('db');
        
        $query = "SELECT * FROM ".LogbookDescription::TABLE_NAME." ".
                 "WHERE inspection_type_id={$db->sqltext($inspectionTypeId)} ".
                 "AND origin = '".  LogbookDescriptionManager::LOGBOOK_DESCRIPTION_ORIGIN."'";
                 
        $db ->query($query);
        $this->assertFalse($db->num_rows() == 0);
        
        $ldManager->deleteDescriptionsByInspectionTypeId($inspectionTypeId);
        
        $db ->query($query);
        $this->assertTrue($db->num_rows() == 0);
    }
}
?>
