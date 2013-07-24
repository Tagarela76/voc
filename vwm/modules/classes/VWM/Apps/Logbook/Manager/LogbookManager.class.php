<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use VWM\Apps\Logbook\Entity\LogbookRecord;
use VWM\Apps\Logbook\Entity\LogbookPendingRecord;

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
    public function getLogbookInspectionPersonListByFacilityId($facilityId, $deleted = null, \Pagination $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionPersonList = array();
        $query = "SELECT * FROM " . LogbookInspectionPerson::TABLE_NAME . " " .
                "WHERE facility_Id = {$db->sqltext($facilityId)}";
               
        if(isset($deleted)){
           $query .= " AND deleted = {$deleted}";
        }        
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
    public function getCountLogbookInspectionPersonListByFacilityId($facilityId, $deleted = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionPersonList = array();
        $query = "SELECT count(*) count FROM " . LogbookInspectionPerson::TABLE_NAME . " " .
                "WHERE facility_Id = {$db->sqltext($facilityId)}";
                
        if (isset($deleted)) {
            $query.= " AND deleted = {$deleted}";
        }
        
        $db->query($query);
        $count = $db->fetch(0);

        return $count->count;
    }

    /**
     * 
     * @param int $facilityId
     * @param string $type
     * @param Pagination $pagination
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookRecord
     */
    public function getLogbookListByFacilityId($facilityId, $type = null, $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $logbookList = array();
        $query = "SELECT * FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "facility_id = {$db->sqltext($facilityId)}";

        if ($type == 'equipment') {
            $query .= " AND equipment_id <> 0 ";
        }
        if ($type == 'facility') {
            $query .= " AND equipment_id = 0 ";
        }
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
     * @param string $type
     * 
     * @return int
     */
    public function getCountLogbooksByFacilityId($facilityId, $filter = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT count(*) logbookListcCount FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "facility_id = {$db->sqltext($facilityId)}";
        if($filter == 'equipment'){
           $query .= " AND equipment_id <> 0 " ;
        }
        if($filter == 'facility'){
           $query .= " AND equipment_id = 0 " ;
        }
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
        
        //propane gauge
        if(!is_null($facilityId)){
            $propanGaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::PROPANE_GAS_GAUGE);
        }
        $propanGasGauge = array(
            'id' => LogbookRecord::PROPANE_GAS_GAUGE,
            'name' => 'Propan Gas Gauge',
            'min' => $propanGaugeRange['gauge_value_to'],
            'max' => $propanGaugeRange['gauge_value_to']+LogbookRecord::GAUGE_RANGE_STEP
        );
        
        //time gauge
       if(!is_null($facilityId)){
            $gaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::TIME_GAUGE);
        }
        $timeGauge = array(
            'id' => LogbookRecord::TIME_GAUGE,
            'name' => 'Time Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );
        
        //Fuel gauge
       if(!is_null($facilityId)){
            $gaugeRange = $this->getLogbookRange($facilityId, LogbookRecord::FUEL_GAUGE);
        }
        $fuelGauge = array(
            'id' => LogbookRecord::FUEL_GAUGE,
            'name' => 'Fuel Gauge',
            'min' => $gaugeRange['min_gauge_range'],
            'max' => $gaugeRange['max_gauge_range']
        );
        
        $gaugeList = array(
            0 => $temperatuteGauge,
            1 => $manometerGauge,
            2 => $clarifierGauge,
            3 => $gasGauge,
            4 => $electricGauge,
            5 => $propanGasGauge,
            6 => $timeGauge,
            7 => $fuelGauge,
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
    
    public function getLogbookListByEquipmentId($equipmentId, $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $logbookList = array();
        $query = "SELECT * FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "equipment_id = {$db->sqltext($equipmentId)}";
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
    
    public function getCountLogbooksByEquipmentId($equipmentId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT count(*) logbookListCount FROM " . LogbookRecord::TABLE_NAME . " WHERE " .
                "equipment_id = {$db->sqltext($equipmentId)}";
        $db->query($query);
        $row = $db->fetch(0);

        return $row->logbookListCount;
    }
    
    /**
     * 
     * get filter for Logbook List
     * 
     * @return array();
     */
    public function getFilterList()
    {
        return array(
            0 => array(
                'id' => 'all',
                'name' => 'All',
            ),
            1 => array(
                'id' => 'equipment',
                'name' => 'Equipment  Inspection Types',
            ),
            2 => array(
                'id' => 'facility',
                'name' => 'Facility Health & Safety (H&S)',
            )
        );
    }
    
    /**
     * 
     * get Count Recurring logbook List
     * 
     * @param int $facilityId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookRecord
     */
    public function getCountRecurringLogbookList($facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT count(*) count FROM ".LogbookRecord::TABLE_NAME." ".
                 "WHERE is_recurring = 1";
        
        if(isset($facilityId)){
            $query.=" AND facility_id = {$db->sqltext($facilityId)}";
        }
        $db->query($query);
        
        $count = $db->fetch(0);
        return $count->count;
    }
    
    /**
     * 
     * get Recurring logbook List
     * 
     * @param int $facilityId
     * @param Pagination $pagination
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookRecord
     */
    public function getRecurringLogbookList($facilityId = null, \Pagination $pagination)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $recurringLogbookList = array();
        
        $query = "SELECT * FROM ".LogbookRecord::TABLE_NAME." ".
                 "WHERE is_recurring = 1";
        
        if(isset($facilityId)){
            $query.=" AND facility_id = {$db->sqltext($facilityId)}";
        }
        
        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        $db->query($query);
        
        $rows = $db->fetch_all_array();
        
        foreach ($rows as $row){
            $recurringLogbook = new LogbookRecord();
            $recurringLogbook->initByArray($row);
            $recurringLogbookList[] = $recurringLogbook;
        }
        
        return $recurringLogbookList;
    }
    
     /**
     * 
     * get Logbook periodicity
     * 
     * @return array()
     */
    public function getLogbookPeriodicityList()
    {
        return array(
            0 =>array(
             'id' => LogbookRecord::DAILY,
             'description' => 'daily'
            ),
            1 =>array(
             'id' => LogbookRecord::WEEKLY,
             'description' => 'weekly'
            ),
            2 =>array(
             'id' => LogbookRecord::MONTHLY,
             'description' => 'monthly'
            ),
            3 =>array(
             'id' => LogbookRecord::YEARLY,
             'description' => 'yearly'
            ),
        );
    }
    
    /**
     * get recurring logbooks with has been created today
     */
    public function getCurrentRecurringLogbookList($facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $currentRecurringLogbook = array();
        //get current date
        $date = date('m/d/Y', time());
        $date = explode('/', $date);
        
        //get date in unix considering time
        $dateTimeFrom = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
        $dateTimeTo = mktime(23, 59, 59, $date[0], $date[1], $date[2]);
        
        $sql = "SELECT * FROM ".  LogbookRecord::TABLE_NAME." ".
               "WHERE is_recurring = 1 ".
               "AND next_date>={$db->sqltext($dateTimeFrom)} ".
               "AND next_date<={$db->sqltext($dateTimeTo)}";
               
        if(!is_null($facilityId)){
            $sql.=" AND facility_id = {$db->sqltext($facilityId)}";
        }
        $db->query($sql); 
        
        if ($db->num_rows() == 0) {
            return $currentRecurringLogbook;
        }
        $rows = $db->fetch_all_array();
        foreach($rows as $row){
            $logbookRecord = new LogbookRecord();
            $logbookRecord->initByArray($row);
            $currentRecurringLogbook[] = $logbookRecord;
        }
        return $currentRecurringLogbook;
    }
    
     /**
     * 
     * Get next Logbook Date
     * 
     * @param int $periodicity
     * @param int $currentDate
     * 
     * @return int
     */
    public function getNextLogbookDate($periodicity, $currentDate)
    {
        $log = \VOCApp::getInstance()->getService('errorLogger');
        switch ($periodicity) {
            case LogbookRecord::DAILY :
                $date = strtotime("+1 days", $currentDate);
                break;
            case LogbookRecord::WEEKLY :
                $date = strtotime("+1 week", $currentDate);
                break;
            case LogbookRecord::MONTHLY :
                $date = strtotime("+1 month", $currentDate);
                break;
            case LogbookRecord::YEARLY :
                $date = strtotime("+1 year", $currentDate);
                break;
            default :
                $log->addError('Inccorect tab of logbook periodicity! Periodicity Id='.$periodicity.' namespace:VWM\Apps\Logbook\Manager. function:getNextLogbookDate');
                return false;
                break;
        }
        
        return $date;
    }
    
    /**
     * 
     * function for calculate nex logbook Date
     * 
     * @param int $periodicity
     * @param int $date
     * 
     * @return int
     */
    public function calculateNextLogbookDate($periodicity, $date)
    {
        //get current Date;
        $currentDate = date('d/m/Y' ,time());
        $currentDate = explode('/', $currentDate);
        //use time 23:59:59
        $currentDate = mktime(23, 59, 59, $currentDate[1], $currentDate[0], $currentDate[2]);
        
        //update next logbook date while date less then current date
        while ($date <= $currentDate){
            $date = $this->getNextLogbookDate($periodicity, $date);
            if(!$date){
                break;
            }
        }
        return $date;
    }
    
    /**
     * 
     * get logbook record to do list by facility id
     * 
     * @param int $facilityId
     * @param \Pagination $pagination
     * 
     * @return boolean|\VWM\Apps\Logbook\Entity\LogbookPendingRecord[]
     */
    public function getLogbookPendingRecordListByFacilityId($facilityId, \Pagination $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $logbookRecordPendingList = array();
        
        if(is_null($facilityId)){
            return false;
        }
        
        $query = "SELECT * FROM ".LogbookPendingRecord::TABLE_NAME." ".
                 "WHERE facility_id = {$db->sqltext($facilityId)}";
                 
        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        
        $db->query($query);
        $rows = $db->fetch_all_array();
        foreach($rows as $row){
            $logbookPendingRecord = new LogbookPendingRecord();
            $logbookPendingRecord->initByArray($row);
            $logbookRecordPendingList[] = $logbookPendingRecord;
        }
        return $logbookRecordPendingList;
    }
    
    /**
     * 
     * get count Logbook Record To Do
     * 
     * @param int $facilityId
     * 
     * @return boolean|int
     */
    public function getCountLogbookPendingRecordListByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        
        if(is_null($facilityId)){
            return false;
        }
        
        $query = "SELECT count(*) count FROM ".LogbookPendingRecord::TABLE_NAME." ".
                 "WHERE facility_id = {$db->sqltext($facilityId)} LIMIT 1";
        
        $db->query($query);
        $count = $db->fetch(0);
        
        return $count->count;
    }
    
    /**
     * 
     * delete all logbooksTodo by parent id
     * call this function when we cancel recurring in logbook
     * 
     * @param int $parentId
     */
    public function deleteAllLogbookPendingRecordByParentId($parentId = null)
    {
        if(is_null($parentId)){
            return false;
        }
        
        $db = \VOCApp::getInstance()->getService('db');
        
        $query = "DELETE FROM ".LogbookPendingRecord::TABLE_NAME." ".
                 "WHERE parent_id = {$db->sqltext($parentId)}";
                 
        $db->query($query);        
    }
    

}
?>