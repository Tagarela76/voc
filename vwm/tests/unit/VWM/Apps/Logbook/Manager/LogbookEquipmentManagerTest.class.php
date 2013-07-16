<?php
namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Test as Testing;
use VWM\Framework\Model;
use \VWM\Apps\Logbook\Entity\LogbookEquipment;
use VWM\Apps\Logbook\Manager\LogbookEquipmentManager;

class LogbookEquipmentManagerTest extends Testing\DbTestCase
{
    protected $fixtures = array(
        LogbookEquipment::TABLE_NAME
    );
    
    public function testGetLogbookEquipmentListByFacilityId()
    {
        $db = \VOCApp::getInstance()->getService('db');
        
        //check for one Facility
        $facilityId = 1;
        
        $leManager = new LogbookEquipmentManager();
        $logbookEquipmenteList = array();
        $logbookEquipmenteList = $leManager->getLogbookEquipmentListByFacilityId($facilityId);
        
        $query = "SELECT * FROM ".LogbookEquipment::TABLE_NAME. " ".
                 "WHERE facility_id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        $newLogbookEquipmenteList = array();
        
        foreach($rows as $row){
            $logbookEquipmente = new LogbookEquipment();
            $logbookEquipmente->initByArray($row);
            $newLogbookEquipmenteList[] = $logbookEquipmente;
        }
        
        $this->assertEquals($newLogbookEquipmenteList, $logbookEquipmenteList);
        
    }
}
?>
