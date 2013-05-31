<?php

namespace VWM\Apps\Logbook\Manager;

use \VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use \VWM\Apps\Logbook\Entity\LogbookRecord;

class LogbookManager
{
    const FILENAME = '/modules/classes/VWM/Apps/Logbook/Resources/inspectionTypes.json';
    

    /**
     * 
     * @param int $facilityId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookInspectionPerson[]
     */
    public function getLogbookInspectionPersonListByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionPersonList = array();
        $query = "SELECT * FROM " . LogbookInspectionPerson::TABLE_NAME . " " .
                "WHERE facility_Id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        foreach ($rows as $row) {
            $inspectionPerson = new LogbookInspectionPerson();
            $inspectionPerson->initByArray($row);
            $inspectionPersonList[] = $inspectionPerson;
        }

        return $inspectionPersonList;
    }

    /**
     * 
     * @param int $facilityId
     * @param Pagination $pagination
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookRecord
     */
    public function getLogbookListByFacilityId($facilityId, $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');

        $logbookList = array();
        $query = "SELECT * FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "facility_id = {$db->sqltext($facilityId)}";
        $query.=' GROUP BY date_time DESC';
        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        
        $db->query($query);
        $rows = $db->fetch_all_array();

        foreach ($rows as $row) {
            $logbook = new LogbookRecord();
            $logbook->initByArray($row);
            $logbookList[] = $logbook;
        }

        return $logbookList;
    }

    /**
     * 
     * get logbook List count by facility Id
     * 
     * @param int $facilityId
     * 
     * @return int
     */
    public function getCountLogbooksByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT count(*) logbookListcCount FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "facility_id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $row = $db->fetch(0);

        return $row->logbookListcCount;
    }

    /**
     * 
     * @param int $facilityId
     * @return array
     */
    public function getGaugeList($facilityId)
    {
        //default values
        $gaugeRange = array(
            'min_gauge_range'=>  LogbookRecord::MIN_GAUGE_RANGE,
            'max_gauge_range'=> LogbookRecord::MAX_GAUGE_RANGE
        );
        //temperature gauge
        if(!is_null($facilityId)){
            $gaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::TEMPERATURE_GAUGE);
        }
        $temperatuteGauge = array(
            'id' => LogbookRecord::TEMPERATURE_GAUGE,
            'name' => 'Temperature Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );
        //manometer gauge
        if(!is_null($facilityId)){
            $gaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::MANOMETER_GAUGE);
        }
        $manometerGauge = array(
            'id' => LogbookRecord::MANOMETER_GAUGE,
            'name' => 'Manometer Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );

        //clarifier gauge
        if(!is_null($facilityId)){
            $gaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::CLARIFIER_GAUGE);
        }
        $clarifierGauge = array(
            'id' => LogbookRecord::CLARIFIER_GAUGE,
            'name' => 'Clarifier Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );
        
        //gas gauge 
        if(!is_null($facilityId)){
            $gaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::GAS_GAUGE);
        }
        $gasGauge = array(
            'id' => LogbookRecord::GAS_GAUGE,
            'name' => 'Gas Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );
        
        //electric gauge 
        if(!is_null($facilityId)){
            $electricGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::ELECTRIC_GAUGE);
        }
        $electricGauge = array(
            'id' => LogbookRecord::ELECTRIC_GAUGE,
            'name' => 'Electric Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );

        $gaugeList = array(
            0 => $temperatuteGauge,
            1 => $manometerGauge,
            2 => $clarifierGauge,
            3 => $gasGauge,
            4 => $electricGauge
        );

        return $gaugeList;
    }

    /**
     * 
     * get logbook Range by facilityId and gauge type
     * 
     * @param int $facilityId
     * @param int $gaugeType
     * 
     * @return int[]
     */
    private function getLogbookRange($facilityId, $gaugeType)
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($facilityId) || is_null($gaugeType)) {
            return false;
        }
        $query = "SELECT min_gauge_range, max_gauge_range " .
                "FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "facility_id = {$db->sqltext($facilityId)} AND " .
                "gauge_type = {$db->sqltext($gaugeType)} LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();

        if (is_null($result[0]['min_gauge_range'])) {
            $result[0]['min_gauge_range'] = 0;
        }
        if (is_null($result[0]['max_gauge_range'])) {
            $result[0]['max_gauge_range'] = 100;
        }

        return $result[0];
    }

}
?>