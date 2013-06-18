<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookEquipment;

class LogbookEquipmentManager
{

    public function getLogbookEquipmentListByFacilityId($facilityId = null, $pagination = null)
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

        $logbookEquipmenteList = array();

        foreach ($rows as $row) {
            $logbookEquipmente = new LogbookEquipment();
            $logbookEquipmente->initByArray($row);
            $newLogbookEquipmenteList[] = $logbookEquipmente;
        }
        return $newLogbookEquipmenteList;
    }

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
