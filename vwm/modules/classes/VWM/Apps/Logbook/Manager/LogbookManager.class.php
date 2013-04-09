<?php

namespace VWM\Apps\Logbook\Manager;

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
    
    public function getLogbookDescriptionsList(){
         //get current directory
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        //decode json information
        $typeList = json_decode($json);
        return $typeList->description;
    }
    
    public function getInspectionTypeListInJson(){
        $path = getcwd();
        //get file content
        $json = file_get_contents($path . self::FILENAME);
        return $json;
    }

}
?>