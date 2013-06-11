<?php

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;
use \VWM\Apps\Logbook\Manager\InspectionTypeManager;
use \VWM\Apps\Logbook\Manager\LogbookSetupTemplateManager;

class InspectionTypeManagerTest extends Testing\DbTestCase
{

    protected $fixtures = array(
        LogbookInspectionType::TABLE_NAME,
        InspectionTypeManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE,
        LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY
    );

    public function testGetFacilityIdsByInspectionTypeId()
    {
        
        $db = \VOCApp::getInstance()->getService('db');
        //$itManager = \VOCApp::getInstance()->getService('inspectionType');
        $itManager = new InspectionTypeManager();
        $inspectionTypeId = 1;
        $facilityIds = $itManager->getFacilityIdsByInspectionTypeId($inspectionTypeId);
        $query = "SELECT lt2f.facility_id " .
                "FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " lt2f " .
                "LEFT JOIN ".LogbookSetupTemplateManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE." it2lt ".
                "ON it2lt.logbook_setup_template_id = lt2f.logbook_setup_template_id ".
                "LEFT JOIN ".LogbookInspectionType::TABLE_NAME ." it ".
                "ON it2lt.inspection_type_id = it.id ".
                "WHERE it.id	= {$db->sqltext($inspectionTypeId)}";
        $db->query($query);
        $result = $db->fetch_all_array();
        $newFacilityIds = array();
        foreach ($result as $r) {
            if (!in_array($r['facility_id'], $newFacilityIds)) {
                $newFacilityIds[] = $r['facility_id'];
            }
        }
        $this->assertEquals($facilityIds, $newFacilityIds);
    }

    public function testAssignUnassignInspectionTypeToLogbookTemplate()
    {
        $db = \VOCApp::getInstance()->getService('db');
        //$itManager = \VOCApp::getInstance()->getService('inspectionType');
        $itManager = new InspectionTypeManager();
        $inspectionTypeId = 70;
        $logbookTemplateId = 105;
        $itManager->assignInspectionTypeToInspectionTemplate($inspectionTypeId, $logbookTemplateId);

        $query = "SELECT * FROM " . InspectionTypeManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE . " " .
                "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} " .
                "AND logbook_setup_template_id = {$db->sqltext($logbookTemplateId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        $this->assertEquals($result[0]['inspection_type_id'], $inspectionTypeId);
        $this->assertEquals($result[0]['logbook_setup_template_id'], $logbookTemplateId);

        $itManager->unAssignInspectionTypeFromInspectionTemplate($inspectionTypeId, $logbookTemplateId);
        $query = "SELECT * FROM " . InspectionTypeManager::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} " .
                "AND logbook_setup_template_id = {$db->sqltext($logbookTemplateId)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();

        $this->assertTrue(empty($result));
    }
    
    public function testGetInspectionTypeList()
    {
        $db = \VOCApp::getInstance()->getService('db');
        //$itManager = \VOCApp::getInstance()->getService('inspectionType');
        $itManager = new InspectionTypeManager();
        $templateId = 1;
        $inspectionTypeList = $itManager->getInspectionTypeList($templateId);
        $this->assertInstanceOf('\VWM\Apps\Logbook\Entity\LogbookInspectionType', $inspectionTypeList[0]);

        $newInspectionTypeList = array();
        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME . " lbt " .
                "LEFT JOIN " . InspectionTypeManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE . " it " .
                "ON it.inspection_type_id = lbt.id " .
                "WHERE it.logbook_setup_template_id=" . $templateId;
        $db->query($query);
        $result = $db->fetch_all_array();
        //get inspection type ids by facility id
        foreach ($result as $r) {
            $inspectionType = new LogbookInspectionType();
            $inspectionType->setId($r['inspection_type_id']);
            $inspectionType->load();
            $newInspectionTypeList[] = $inspectionType;
        }
        $this->assertEquals($newInspectionTypeList, $inspectionTypeList);
    }

}
?>