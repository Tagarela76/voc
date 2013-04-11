<?php

namespace VWM\Apps\Logbook\Manager;

use \VWM\Apps\Logbook\Entity\LogbookInspectionPerson;

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
     * getting sub type list by type number
     * 
     * @param int $typeId
     * 
     * @return std[]
     */
    public function getInspectionSubTypeByTypeNumber($typeId = 0)
    {
        //get current directory
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        //decode json information
        $typeList = json_decode($json);
        return $typeList->inspectionTypes[$typeId]->subtypes;
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

}
?>