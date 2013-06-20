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
     * @param \Pagination $pagination
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookInspectionPerson[]
     */
    public function getLogbookInspectionPersonListByFacilityId($facilityId, \Pagination $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionPersonList = array();
        $query = "SELECT * FROM " . LogbookInspectionPerson::TABLE_NAME . " " .
                "WHERE facility_Id = {$db->sqltext($facilityId)}";
                
        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        
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
     * get inspection person count
     * 
     * @param int $facilityId
     * 
     * @return int
     */
    public function getCountLogbookInspectionPersonListByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionPersonList = array();
        $query = "SELECT count(*) count FROM " . LogbookInspectionPerson::TABLE_NAME . " " .
                "WHERE facility_Id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $count = $db->fetch(0);
       
        return $count->count;
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
        $query.=' ORDER BY date_time DESC';
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
            'min' => $gaugeRange['gauge_value_to'],
            'max' => $gaugeRange['gauge_value_to']+LogbookRecord::GAUGE_RANGE_STEP
        );
        
        //electric gauge 
        if(!is_null($facilityId)){
            $electricGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::ELECTRIC_GAUGE);
        }
        $electricGauge = array(
            'id' => LogbookRecord::ELECTRIC_GAUGE,
            'name' => 'Electric Gauge',
            'min' => $electricGaugeRange['gauge_value_to'],
            'max' => $electricGaugeRange['gauge_value_to']+LogbookRecord::GAUGE_RANGE_STEP
        );
        
        if(!is_null($facilityId)){
            $propanGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::PROPANE_GAS_GAUGE);
        }
        $propanGasGauge = array(
            'id' => LogbookRecord::PROPANE_GAS_GAUGE,
            'name' => 'Propan Gas Gauge',
            'min' => $propanGaugeRange['gauge_value_to'],
            'max' => $propanGaugeRange['gauge_value_to']+LogbookRecord::GAUGE_RANGE_STEP
        );
        
        //var_dump($electricGaugeRange);//die();
        $gaugeList = array(
            0 => $temperatuteGauge,
            1 => $manometerGauge,
            2 => $clarifierGauge,
            3 => $gasGauge,
            4 => $electricGauge,
            5 => $propanGasGauge
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
        $query = "SELECT min_gauge_range, max_gauge_range, gauge_value_to " .
                "FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "facility_id = {$db->sqltext($facilityId)} AND " .
                "gauge_type = {$db->sqltext($gaugeType)} ".
                "ORDER BY id DESC LIMIT 1";
        $db->query($query);
        $result = $db->fetch_all_array();
        
        if (is_null($result[0]['min_gauge_range'])) {
            $result[0]['min_gauge_range'] = LogbookRecord::MIN_GAUGE_RANGE;
        }
        if (is_null($result[0]['max_gauge_range'])) {
            $result[0]['max_gauge_range'] = LogbookRecord::MAX_GAUGE_RANGE;
        }
        //get last inserted value
        if (is_null($result[0]['gauge_value_to'])) {
            $result[0]['gauge_value_to'] = LogbookRecord::MIN_GAUGE_RANGE;
        }

        return $result[0];
    }
    

}
?>