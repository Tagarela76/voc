<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookEquipment;
use VWM\Hierarchy\Facility;

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
                "WHERE facility_id = {$db->sqltext($facilityId)} ".
                "AND voc_emissions = 0";

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
                "WHERE facility_id = {$db->sqltext($facilityId)} ".
                "AND voc_emissions = 0";
        $db->query($query);
        $result = $db->fetch(0);

        return $result->count;
    }
    
    /**
     * 
     * get equipment and logbookEquipment by Facility Id
     * 
     * @param int $facilityId
     * 
     * @return boolean|array
     */
    public function getAllEquipmentListByFacilityId($facilityId = null)
    {
        if (is_null($facilityId)) {
            return false;
        }
        $db = \VOCApp::getInstance()->getService('db');
        
        $facility = new Facility($db, $facilityId);
        $departments = $facility->getDepartments();
        $departmentIds = array();
        foreach($departments as $department){
            $departmentIds[] = $department->getDepartmentId();
        }
        $departmentIds = implode(',', $departmentIds);
        
        $query = "SELECT * FROM " . LogbookEquipment::TABLE_NAME . " " .
                "WHERE facility_id = {$db->sqltext($facilityId)} ".
                "OR department_id IN({$departmentIds}) ORDER BY facility_id";
        $db->query($query);
        $rows = $db->fetch_all_array();
        $equipmentList = array();
        
        foreach($rows as $row){
            $equipment = array(
                'id' => $row['equipment_id'],
                'description' => $row['equip_desc'],
                'permit' => $row['permit']
            );
            $equipmentList[] = $equipment;
        }
        return $equipmentList;
    }

}
?>
