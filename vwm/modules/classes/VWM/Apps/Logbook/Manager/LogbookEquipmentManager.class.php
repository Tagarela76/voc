<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookEquipment;

class LogbookEquipmentManager
{

    /**
     * 
     * get logbook Equipment List by Facility Id
     * 
     * @param int $facilityId
     * @param \Pagination $pagination
     * 
     * @return boolean|\VWM\Apps\Logbook\Entity\LogbookEquipment
     */
    public function getLogbookEquipmentListByFacilityId($facilityId = null, \Pagination $pagination = null)
    {
        if (is_null($facilityId)) {
            return false;
        }
        
        $db = \VOCApp::getInstance()->getService('db');

        $query = "SELECT * FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE facility_id = {$db->sqltext($facilityId)}";

        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        $db->query($query);
        $rows = $db->fetch_all_array();

        $logbookEquipmentList = array();
        
        foreach ($rows as $row) {
            $logbookEquipment = new LogbookEquipment();
            $logbookEquipment->initByArray($row);
            $logbookEquipmentList[] = $logbookEquipment;
        }
        return $logbookEquipmentList;
    }

    /**
     * 
     * get count of logbook equipment by facility id
     * 
     * @param int $facilityId
     * 
     * @return boolean
     */
    public function getCountLogbookEquipmentByFacilityId($facilityId = null)
    {
        if (is_null($facilityId)) {
            return false;
        }
        $db = \VOCApp::getInstance()->getService('db');

        $query = "SELECT count(*) count FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE facility_id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $result = $db->fetch(0);

        return $result->count;
    }

}
?>
