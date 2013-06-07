<?php

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager;
use VWM\Apps\Logbook\Entity\LogbookSetupTemplate;
use VWM\Hierarchy\Facility;

class LogbookSetupTemplateManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookSetupTemplateManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE,
        LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY,
        LogbookSetupTemplate::TABLE_NAME,
        Facility::TABLE_NAME
    );

    public function testGetLogbookTemplateListByFacilityIds()
    {
        //check for one Facility
        $facilityId = 1;

        //$logbookTemplateManager = VOCApp::getInstance()->getService('logbookSetupTemplate');
        $logbookTemplateManager = new LogbookSetupTemplateManager();
        $db = VOCApp::getInstance()->getService('db');
        
        $logbookTemplateList = $logbookTemplateManager->getLogbookTemplateListByFacilityIds($facilityId);
        
        $query = "SELECT logbook_setup_template_id FROM ".LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY." ".
                 "WHERE facility_id = {$db->sqltext($facilityId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch(0);
        $logbookSetupTemplateId = $result->logbook_setup_template_id;
        
        $query = "SELECT name FROM ".LogbookSetupTemplate::TABLE_NAME." ".
                 "WHERE id = {$db->sqltext($logbookSetupTemplateId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch(0);
        
        $this->assertEquals($logbookTemplateList[0]->getName(), $result->name);
        
        //check for many facilities
        $facilityIds = array(2,3);
        $facilityIdsString = implode(',', $facilityIds);

        $logbookTemplateList = $logbookTemplateManager->getLogbookTemplateListByFacilityIds($facilityIdsString);
        
        $query = "SELECT logbook_setup_template_id FROM ".LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY." ".
                 "WHERE facility_id IN({$db->sqltext($facilityIdsString)})";
        $db->query($query);
        $result = $db->fetch_all();
        
        $logbookSetupTemplateIds = array();
        foreach ($result as $r){
            $logbookSetupTemplateIds[] = $r->logbook_setup_template_id;
        }
        
        $logbookSetupTemplateIdsString = implode(',', $logbookSetupTemplateIds);
        
        $query = "SELECT * FROM ".LogbookSetupTemplate::TABLE_NAME." ".
                 "WHERE id IN({$db->sqltext($logbookSetupTemplateIdsString)})";
        $db->query($query);
        $result = $db->fetch_all();
        
        $this->assertEquals($logbookTemplateList[0]->getName(), $result[0]->name);
        $this->assertEquals($logbookTemplateList[1]->getName(), $result[1]->name);
        $this->assertEquals($logbookTemplateList[0]->getId(), $result[0]->id);
        $this->assertEquals($logbookTemplateList[1]->getId(), $result[1]->id);
        
        //check all facilities
        $logbookTemplateList = $logbookTemplateManager->getLogbookTemplateListByFacilityIds();
        $this->assertEquals(count($logbookTemplateList), 3);
    }

    public function testAssignLogbookTemplateToFacility()
    {
        $logbookTemplateId = 7;
        $facilityId = 1;

        //$logbookTemplateManager = VOCApp::getInstance()->getService('logbookSetupTemplate');
        $logbookTemplateManager = new LogbookSetupTemplateManager();
        $db = VOCApp::getInstance()->getService('db');

        $logbookTemplateManager->assignLogbookTemplateToFacility($logbookTemplateId, $facilityId);

        $query = "SELECT * FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " " .
                "WHERE logbook_setup_template_id = {$db->sqltext($logbookTemplateId)} " .
                "AND facility_id = {$db->sqltext($facilityId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch(0);

        $this->assertFalse($db->num_rows() == 0);
        $this->assertEquals($result->logbook_setup_template_id, $logbookTemplateId);
        $this->assertEquals($result->facility_id, $facilityId);
    }

    public function testUnAssignLogbookTemplateToFacility()
    {
        $logbookTemplateId = 7;
        $facilityId = 1;

        //$logbookTemplateManager = VOCApp::getInstance()->getService('logbookSetupTemplate');
        $logbookTemplateManager = new LogbookSetupTemplateManager();
        $db = VOCApp::getInstance()->getService('db');
        
        //assign Logbook Template To Facility
        $logbookTemplateManager->assignLogbookTemplateToFacility($logbookTemplateId, $facilityId);

        $query = "SELECT * FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " " .
                "WHERE logbook_setup_template_id = {$db->sqltext($logbookTemplateId)} " .
                "AND facility_id = {$db->sqltext($facilityId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch(0);
        $this->assertFalse($db->num_rows() == 0);
        
        //unAssign Logbook Template To Facility
        $logbookTemplateManager->unAssignLogbookTemplateFromFacility($logbookTemplateId);
        $query = "SELECT * FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " " .
                "WHERE logbook_setup_template_id = {$db->sqltext($logbookTemplateId)} " .
                "AND facility_id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $result = $db->fetch_all_array();
        
        $this->assertTrue($db->num_rows() == 0);
    }
    
    public function testGetFacilityListByLogbookSetupTemplateId()
    {
        $db = VOCApp::getInstance()->getService('db');
        $logbookSetupTemplateId = 1;

        $logbookSetupTemplateManager = new LogbookSetupTemplateManager();
        $facilityList = $logbookSetupTemplateManager->getFacilityListByLogbookSetupTemplateId($logbookSetupTemplateId);
        
        $query = "SELECT * FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY .
                " WHERE logbook_setup_template_id = {$db->sqltext($logbookSetupTemplateId)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $newFacilityList = array();
        foreach ($rows as $row){
            $facility = new Facility($db, $row['facility_id']);
            $newFacilityList[] = $facility;
        }
        $this->assertEquals($facilityList, $newFacilityList);
    }
    /* public function testAssignInspectionTypeToLogbookTemplate()
      {
      $inspectionTypeId = 1;
      $inspectionTemplateId = 125;
      $logbookTemplateManager = VOCApp::getInstance()->getService('logbookSetupTemplate');
      $db = VOCApp::getInstance()->getService('db');
      $logbookTemplateManager->assignInspectionTypeToLogbookTemplate($inspectionTypeId, $facilityId);

      $query = "SELECT * FROM ".LogbookSetupTemplateManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE." ".
      "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} ".
      "AND logbook_setup_template_id	 = {$db->sqltext($inspectionTypeId)}";
      } */
}
?>
