<?php

namespace VWM\Apps\Logbook\Manager;

use VWM\Framework\Model;
use VWM\Apps\Logbook\Entity\LogbookDescription;

class LogbookDescriptionManager
{

    const LOGBOOK_DESCRIPTION_ORIGIN = 'inspection_type';
    
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

}
?>
