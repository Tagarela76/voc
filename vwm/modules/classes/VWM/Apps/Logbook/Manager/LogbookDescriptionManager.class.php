<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookDescription;
use VWM\Apps\Logbook\Entity\LogbookCustomDescription;
use \VWM\Apps\Logbook\Entity\LogbookInspectionType;

class LogbookDescriptionManager
{

    const LOGBOOK_DESCRIPTION_ORIGIN = 'inspection_type';
    const LOGBOOK_CUSTOM_DESCRIPTION_ORIGIN = 'custom_description';
    const SERVICE = 'logbookDescription';
    
    /**
     * 
     * delete Logbook description By Inspection Type id
     * 
     * @param int $inspectionTypeId
     */
    public function deleteDescriptionsByInspectionTypeId($inspectionTypeId)
    {
        $logbookDescriptionList = $this->getDescriptionListByInspectionTypeId($inspectionTypeId);
        foreach($logbookDescriptionList as $logbookDescription){
            $logbookDescription->delete();
        }
    }
    
    /**
     * 
     * get logbook description list
     * 
     * @param int $inspectionTypeId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookDescription[]
     */
    public function getDescriptionListByInspectionTypeId($inspectionTypeId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $logbookDescriptionList = array();
        $query = "SELECT * FROM ".LogbookDescription::TABLE_NAME." ".
                 "WHERE inspection_type_id={$db->sqltext($inspectionTypeId)} ".
                 "AND origin = '".self::LOGBOOK_DESCRIPTION_ORIGIN."'";
        $db ->query($query);
        
        $rows = $db->fetch_all_array();
        foreach($rows as $row){
            $logbookDescription = new LogbookDescription();
            $logbookDescription->initByArray($row);
            $logbookDescriptionList[] = $logbookDescription;
            
        }
        
        return $logbookDescriptionList;
    }
    
    /**
     * 
     * get logbook description list in json
     * 
     * @param int $inspectionTypeId
     * 
     * @return json
     */
    public function getDescriptionListByInspectionTypeIdInJson($inspectionTypeId)
    {
        $logbookDescriptionList = $this->getDescriptionListByInspectionTypeId($inspectionTypeId);
        $logbookDescriptionListJson = array();
        foreach ($logbookDescriptionList as $logbookDescription) {
            $logbookDescriptionListJson[] = $logbookDescription->getAttributes();
        }
        $logbookDescriptionListJson = json_encode($logbookDescriptionListJson);
        return $logbookDescriptionListJson;
    }
    
    /**
     * 
     * get logbook description list
     * 
     * @param int $facilityId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookCustomDescription[]
     */
    public function getCustomDescriptionListByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $logbookCustomDescriptionList = array();
        $query = "SELECT lcd.* FROM ".LogbookDescription::TABLE_NAME." lcd ".
                 "INNER JOIN ".LogbookInspectionType::TABLE_NAME." lit ".
                 "ON lit.id = lcd.inspection_type_id ".
                 "WHERE lcd.facility_id={$db->sqltext($facilityId)} ".
                 "AND lcd.origin = '".self::LOGBOOK_CUSTOM_DESCRIPTION_ORIGIN."'";
        $db ->query($query);
        $rows = $db->fetch_all_array();
        foreach($rows as $row){
            $logbookCustomDescription = new LogbookCustomDescription();
            $logbookCustomDescription->initByArray($row);
            $logbookCustomDescriptionList[] = $logbookCustomDescription;
        }
        
        return $logbookCustomDescriptionList;
    }
    
    /**
     * 
     * get All logbook description list in json
     * 
     * @param int $inspectionTypeId
     * 
     * @return json
     */
    public function getAllDescriptionListByInspectionTypeIdInJson($inspectionTypeId)
    {
        $logbookDescriptionList = $this->getDescriptionListByInspectionTypeId($inspectionTypeId);
        $logbookCustomDescriptionList = $this->getCustomDescriptionListByInspectionTypeId($inspectionTypeId);
        
        $logbookDescriptionListJson = array();
        foreach ($logbookDescriptionList as $logbookDescription) {
            $logbookDescriptionListJson[] = $logbookDescription->getAttributes();
        }
        
        foreach ($logbookCustomDescriptionList as $logbookCustomDescription) {
            $logbookDescriptionListJson[] = $logbookCustomDescription->getAttributes();
        }
        
        $logbookDescriptionListJson = json_encode($logbookDescriptionListJson);
        return $logbookDescriptionListJson;
    }
    /**
     * 
     * get logbook description list
     * 
     * @param int $inspectionTypeId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookDescription[]
     */
    public function getCustomDescriptionListByInspectionTypeId($inspectionTypeId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $logbookCustomDescriptionList = array();
        $query = "SELECT * FROM ".LogbookDescription::TABLE_NAME." ".
                 "WHERE inspection_type_id={$db->sqltext($inspectionTypeId)} ".
                 "AND origin = '".self::LOGBOOK_CUSTOM_DESCRIPTION_ORIGIN."'";
        $db ->query($query);
        
        $rows = $db->fetch_all_array();
        foreach($rows as $row){
            $logbookCustomDescription = new LogbookCustomDescription();
            $logbookCustomDescription->initByArray($row);
            $logbookCustomDescriptionList[] = $logbookCustomDescription;
            
        }
        
        return $logbookCustomDescriptionList;
    }

}
?>
