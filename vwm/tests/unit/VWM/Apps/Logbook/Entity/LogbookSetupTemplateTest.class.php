<?php

use VWM\Framework\Test as Testing;
use \VWM\Apps\Logbook\Entity\LogbookSetupTemplate;
use VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager;
use VWM\Framework\Model;

class LogbookSetupTemplateTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookSetupTemplate::TABLE_NAME
    );

    public function testSave()
    {
        $logbookName = 'TestName';
        $db = VOCApp::getInstance()->getService('db');
        $logbookSetupTemplate = new LogbookSetupTemplate();
        $logbookSetupTemplate->setName($logbookName);
        $id = $logbookSetupTemplate->save();
        $query = "SELECT * FROM " . LogbookSetupTemplate::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $result = $db->fetch_all_array();
        $this->assertEquals($result[0]['name'], $logbookName);

        //check update
        $newLogbookName = 'newTestName';
        $logbookSetupTemplate->setName($newLogbookName);
        $logbookSetupTemplate->save();
        $query = "SELECT * FROM " . LogbookSetupTemplate::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $result = $db->fetch_all_array();
        $this->assertEquals($result[0]['name'], $newLogbookName);
    }

    public function testGetFasilityIds()
    {
        $db = VOCApp::getInstance()->getService('db');
        $logbookSetupTemplateId = 1;

        $logbookSetupTemplate = new LogbookSetupTemplate();
        $logbookSetupTemplate->setId($logbookSetupTemplateId);
        $logbookSetupTemplate->load();

        $facilityIds = $logbookSetupTemplate->getFacilityIds();

        $query = "SELECT facility_id FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY .
                " WHERE logbook_setup_template_id = {$db->sqltext($logbookSetupTemplateId)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        $newFacilityIds = array();
        foreach ($rows as $row){
            $newFacilityIds[] = $row['facility_id'];
        }
        $newFacilityIds = implode(',', $newFacilityIds);
        $this->assertEquals($facilityIds, $newFacilityIds);
    }
    
    public function testDelete()
    {
        $logbookName = 'TestName';
        $db = VOCApp::getInstance()->getService('db');
        
        $logbookSetupTemplate = new LogbookSetupTemplate();
        $logbookSetupTemplate->setName($logbookName);
        $id = $logbookSetupTemplate->save();
        $query = "SELECT * FROM " . LogbookSetupTemplate::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $this->assertFalse($db->num_rows() == 0);
        
        $logbookSetupTemplate->delete();
        
        $query = "SELECT * FROM " . LogbookSetupTemplate::TABLE_NAME . " " .
                "WHERE id={$db->sqltext($id)}";
        $db->query($query);
        $this->assertTrue($db->num_rows() == 0);
        
    }

}
?>
