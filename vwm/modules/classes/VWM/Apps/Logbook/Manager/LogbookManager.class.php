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

    public function getGaugeList($facilityId)
    {
        //temperature gauge
        $temperatureGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::TEMPERATURE_GAUGE);
        $temperatuteGauge = array(
            'id' => LogbookRecord::TEMPERATURE_GAUGE,
            'name' => 'Temperature Gauge',
            'min' => $temperatureGaugeRange['min_gauge_range'],
            'max' => $temperatureGaugeRange['max_gauge_range']
        );
        //manometer gauge
        $manometerGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::MANOMETER_GAUGE);
        $manometerGauge = array(
            'id' => LogbookRecord::MANOMETER_GAUGE,
            'name' => 'Manometer Gauge',
            'min' => $manometerGaugeRange['min_gauge_range'],
            'max' => $manometerGaugeRange['max_gauge_range']
        );

        //clarifier gauge
        $clarifierGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::CLARIFIER_GAUGE);
        $clarifierGauge = array(
            'id' => LogbookRecord::CLARIFIER_GAUGE,
            'name' => 'Clarifier Gauge',
            'min' => $clarifierGaugeRange['min_gauge_range'],
            'max' => $clarifierGaugeRange['max_gauge_range']
        );


        $gaugeList = array(
            0 => $temperatuteGauge,
            1 => $manometerGauge,
            2 => $clarifierGauge
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