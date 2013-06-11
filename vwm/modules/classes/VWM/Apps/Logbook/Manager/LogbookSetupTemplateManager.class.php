<?php

namespace VWM\Apps\Logbook\Manager;

use \VWM\Apps\Logbook\Entity\LogbookSetupTemplate;
use \VWM\Hierarchy\Facility;

class LogbookSetupTemplateManager
{
    const TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE = 'inspection_type2logbook_setup_template';
    const TB_LOGBOOK_SETUP_TEMPLATE2FACILITY = 'logbook_setup_template2facility';

    /**
     * 
     * assign logbook template to facility
     * 
     * @param int $logbookTemplateId
     * @param int $facilityId
     */
    public function assignLogbookTemplateToFacility($logbookTemplateId, $facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($logbookTemplateId) || is_null($facilityId)) {
            return false;
        }
        $query = "INSERT INTO " . self::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " SET " .
                "logbook_setup_template_id = {$db->sqltext($logbookTemplateId)}, " .
                "facility_id = {$db->sqltext($facilityId)}";
        $db->query($query);
    }
    
    /**
     * 
     * unAssign Logbook Template from all facilities
     * 
     * @param int $logbookTemplateId
     */
    public function unAssignLogbookTemplateFromFacility($logbookTemplateId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "DELETE FROM " . self::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " " .
                 "WHERE logbook_setup_template_id = {$db->sqltext($logbookTemplateId)}";
                
        $db->query($query);
    }

    /**
     * 
     * get Logbook Setup Template List
     * 
     * @param int|string $facilityIds
     * 
     * @return boolean|\VWM\Apps\Logbook\Entity\LogbookSetupTemplate[]
     */
    public function getLogbookTemplateListByFacilityIds($facilityIds = null, $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');

        $logbookTemplateList = array();
        $logbookTemplateIds = array();

        //get Logbook Template Id 
        if (!is_null($facilityIds)) {
            $query = "SELECT logbook_setup_template_id FROM " . self::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " " .
                    "WHERE facility_id IN ({$db->sqltext($facilityIds)})";
            if (isset($pagination)) {
                $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
            }
            $db->query($query);
            $rows = $db->fetch_all_array();
            //delete repetitive ids
            foreach ($rows as $row) {
                if (!in_array($row['logbook_setup_template_id'], $logbookTemplateIds)) {
                    $logbookTemplateIds[] = $row['logbook_setup_template_id'];
                }
            }
        } else {
            $query = "SELECT id FROM " . LogbookSetupTemplate::TABLE_NAME;
            if (isset($pagination)) {
                $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
            }
            $db->query($query);
            $rows = $db->fetch_all_array();
            foreach ($rows as $row) {
                $logbookTemplateIds[] = $row['id'];
            }
        }

        foreach ($logbookTemplateIds as $logbookTemplateId) {
            $logbookTemplate = new LogbookSetupTemplate();
            $logbookTemplate->setId($logbookTemplateId);
            $logbookTemplate->load();
            $logbookTemplateList[] = $logbookTemplate;
        }

        return $logbookTemplateList;
    }
    
    /**
     * 
     * get count of logbook template by facility ids
     * 
     * @param string $facilityIds
     * 
     * @return int
     */
    public function getCountLogbookTemplateListByFacilityIds($facilityIds = null)
    {
        $db = \VOCApp::getInstance()->getService('db');

        $logbookTemplateList = array();
        $logbookTemplateIds = array();
        //get Logbook Template Id 
        if (!is_null($facilityIds)) {
            $query = "SELECT logbook_setup_template_id FROM " . self::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " " .
                    "WHERE facility_id IN ({$db->sqltext($facilityIds)}) GROUP BY logbook_setup_template_id";
                    
            $db->query($query);
            $rows = $db->fetch_all_array();
            return count($rows);
        } else {
            $query = "SELECT count(*) count FROM " . LogbookSetupTemplate::TABLE_NAME;
            $db->query($query);
            $rows = $db->fetch(0);
        }

        return $rows->count;
    }

    /**
     * 
     * get facilities by logbook Setup Template Id
     * 
     * @param int $logbookSetupTemplateId
     * 
     * @return boolean|\VWM\Hierarchy\Facility[]
     */
    public function getFacilityListByLogbookSetupTemplateId($logbookSetupTemplateId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($logbookSetupTemplateId)) {
            return false;
        }
        $query = "SELECT facility_id FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY .
                " WHERE logbook_setup_template_id = {$db->sqltext($logbookSetupTemplateId)}";
        $db->query($query);
        $rows = $db->fetch_all_array();
        
        $facilityList = array();
        foreach($rows as $row){
            $facility = new Facility($db, $row['facility_id']);
            $facilityList[] = $facility;
        }
        
        return $facilityList;
    }
    
    /**
     * 
     * get logbookTemplate List By Inspection Type Id
     * 
     * @param int $inspectionTypeId
     * @return \VWM\Apps\Logbook\Entity\LogbookSetupTemplate[]
     */
    public function getLogbookTemplateListByInspectionTypeId($inspectionTypeId)
    {
        $db = \VOCApp::getInstance()->getService('db');

        $logbookTemplateList = array();
        $logbookTemplateIds = array();

        //get Logbook Template Id 
        if (!is_null($inspectionTypeId)) {
            $query = "SELECT logbook_setup_template_id FROM " . self::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE . " " .
                    "WHERE inspection_type_id IN ({$db->sqltext($inspectionTypeId)})";
            $db->query($query);
            $rows = $db->fetch_all_array();
            //delete repetitive ids
            foreach ($rows as $row) {
                if (!in_array($row['logbook_setup_template_id'], $logbookTemplateIds)) {
                    $logbookTemplateIds[] = $row['logbook_setup_template_id'];
                }
            }
        } else {
            $query = "SELECT id FROM " . LogbookSetupTemplate::TABLE_NAME;
            $db->query($query);
            $rows = $db->fetch_all_array();
            foreach ($rows as $row) {
                $logbookTemplateIds[] = $row['id'];
            }
        }

        foreach ($logbookTemplateIds as $logbookTemplateId) {
            $logbookTemplate = new LogbookSetupTemplate();
            $logbookTemplate->setId($logbookTemplateId);
            $logbookTemplate->load();
            $logbookTemplateList[] = $logbookTemplate;
        }

        return $logbookTemplateList;
    }

}
?>
