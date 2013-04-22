<?php

namespace VWM\Apps\Logbook\Manager;

use \VWM\Apps\Logbook\Entity\LogbookInspectionPerson;
use \VWM\Apps\Logbook\Entity\LogbookRecord;

class LogbookManager
{

    const FILENAME = '/modules/classes/VWM/Apps/Logbook/Resources/inspectionTypes.json';

    /**
     * getting Inspection type and subtype from file
     * 
     * @return std[]
     */
    public function getInspectionType()
    {
        //get current directory
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        //decode json information
        $typeList = json_decode($json);
        return $typeList->inspectionTypes;
    }

    /**
     * get sub type list by type description
     * 
     * @param string $typeDescription
     * 
     * @return boolean| std[]
     */
    public function getInspectionSubTypeByTypeDescription($typeDescription = null){
        //get current directory
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        //decode json information
        $typeList = json_decode($json);
        if(!isset($typeDescription) || $typeDescription==''){
           return $typeList->inspectionTypes[0]->subtypes;
        }
        foreach($typeList->inspectionTypes as $type){
            if($type->typeName == $typeDescription){
                return $type->subtypes;
            }
        }
        return false;
    }
    
    /**
     * 
     * get description list
     * 
     * @return std[]
     */
    public function getLogbookDescriptionsList()
    {
        //get current directory
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        //decode json information
        $typeList = json_decode($json);
        return $typeList->description;
    }

    /**
     * 
     * getting inspection Type structure in json string
     * 
     * @return string
     */
    public function getInspectionTypeListInJson()
    {
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        return $json;
    }

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
        $query = "SELECT * FROM ". LogbookInspectionPerson::TABLE_NAME." ".
                 "WHERE facility_Id = {$db->sqltext($facilityId)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        foreach($rows as $row){
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
    
    
    public function getGaugeList()
    {
        $gaugeList = array(
            0 => 'Temperature Gauge',
            1 => 'Manometer Gauge',
            2 => 'Clarifier Gauge'
        );
        
        return $gaugeList;
    }
                

}
?>