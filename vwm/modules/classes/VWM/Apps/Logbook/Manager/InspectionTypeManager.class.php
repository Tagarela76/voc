<?php

namespace VWM\Apps\Logbook\Manager;

use \VWM\Apps\Logbook\Entity\LogbookInspectionType;

class InspectionTypeManager
{
    const TB_INSPECTION_TYPE = 'inspection_type';
    const TB_DESCRIPTION_DESCRIPTION = 'inspection_description';
    const TB_INSPECTION_TYPE2FACILITY = 'inspection_type2facility';
    const TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE = 'inspection_type2logbook_setup_template';

    /**
     * 
     * getting inspection Type structure in json string
     * 
     * @param int $facilityId
     * 
     * @return string
     */
    public function getInspectionTypeListInJson($facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeInJson = array();
        
        $ltManager = new LogbookSetupTemplateManager();
        $itManager = new InspectionTypeManager();
        $logbookTemplateList = $ltManager->getLogbookTemplateListByFacilityIds($facilityId);
        $logbookTemplateIds = array();
        foreach($logbookTemplateList as $logbookTemplate){
            $logbookTemplateIds[] = $logbookTemplate->getId();
        }
        $logbookTemplateIds= implode(',', $logbookTemplateIds);
        $inspectionTypeList = $itManager->getInspectionTypeList($logbookTemplateIds);
        
        foreach ($inspectionTypeList as $inspectionType) {
            $inspectionTypeInJson[] = $inspectionType->getInspectionTypeRaw();
        }
        $inspectionTypeInJson = implode(',', $inspectionTypeInJson);
        $inspectionTypeInJson = '[' . $inspectionTypeInJson . ']';
        return $inspectionTypeInJson;
    }
    /**
     * 
     * getting inspection description structure in json string
     * 
     * @return string
     */
    public function getLogbookDescriptionListInJson()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionDescriptionInJson = array();
        $query = "SELECT description_settings FROM " . self::TB_DESCRIPTION_DESCRIPTION;
        $db->query($query);
        $result = $db->fetch_all_array();

        foreach ($result as $r) {
            $inspectionDescriptionInJson[] = $r['description_settings'];
        }
        $inspectionDescriptionInJson = implode(',', $inspectionDescriptionInJson);
        $inspectionDescriptionInJson = '[' . $inspectionDescriptionInJson . ']';

        return $inspectionDescriptionInJson;
    }

    /**
     * 
     * function for getting subtypes list
     * 
     * @param string $typeDescription
     * 
     * @return boolean
     */
    public function getInspectionSubTypesByTypeDescription($typeDescription = null)
    {
        $json = $this->getInspectionTypeListInJson();
        $typeList = json_decode($json);
        if (!isset($typeDescription) || $typeDescription == '') {
            return false;
        }
        foreach ($typeList as $type) {
            if ($type->typeName == $typeDescription) {
                return $type->subtypes;
            }
        }
        return false;
    }

    /**
     * 
     * function for getting addition type list
     * 
     * @param string $typeDescription
     * 
     * @return boolean
     */
    public function getInspectionAdditionTypesByTypeDescription($typeDescription = null)
    {
        $json = $this->getInspectionTypeListInJson();
        $typeList = json_decode($json);
        if (!isset($typeDescription) || $typeDescription == '') {
            return $typeList[0]->additionFieldList;
        }
        foreach ($typeList as $type) {
            if ($type->typeName == $typeDescription) {
                return $type->additionFieldList;
            }
        }
        return false;
    }

    /**
     * 
     * getting inspection sub type by type and subtype description
     * 
     * @param string $typeDescription
     * @param string $subTypeDescription
     * 
     * @return boolean|std
     */
    public function getInspectionSubTypeByTypeAndSubTypeDescription($typeDescription = null, $subTypeDescription = null)
    {
        $subtypes = $this->getInspectionSubTypesByTypeDescription($typeDescription);
        foreach ($subtypes as $subtype) {
            if ($subtype->name == $subTypeDescription) {
                return $subtype;
            }
        }
        return false;
    }

    /**
     * 
     * getting Inspection Type by Type Name
     * 
     * @param string $typeName
     * 
     * @return boolean| std
     */
    public function getInspectionTypeByTypeName($typeName = null)
    {
        if (is_null($typeName)) {
            throw new Exception('type Name can\'t be NULL');
        }
        $inspectionTypeList = $this->getInspectionTypeListInJson();
        $inspectionTypeList = json_decode($inspectionTypeList);
        foreach ($inspectionTypeList as $inspectionType) {
            if ($inspectionType->typeName == $typeName) {

                return $inspectionType;
            }
        }
        return false;
    }

    /**
     * 
     * @param string $descriptionName
     * 
     * @return string|boolean
     * @throws Exception
     */
    public function getLogbookDescriptionByDescriptionName($descriptionName = null)
    {
        if (is_null($descriptionName)) {
            throw new Exception('description name can\'t be NULL');
        }
        $logbookDescriptionList = $this->getLogbookDescriptionListInJson();
        $logbookDescriptionList = json_decode($logbookDescriptionList);
        foreach ($logbookDescriptionList as $logbookDescription) {
            if ($logbookDescription->name == $descriptionName) {
                return $logbookDescription;
            }
        }
        return false;
    }
    
    /**
     * 
     * get inspection type list
     * 
     * @param int $logbookTemplateId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookInspectionType[]
     */
    public function getInspectionTypeList($logbookTemplateId = null, $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeList = array();
        
        //get inspection type ids by facility id
        if (!is_null($logbookTemplateId)) {
            $inspectionTypeIds = array();
            $sql = "SELECT inspection_type_id " .
                    "FROM " . self::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE . " " .
                    "WHERE logbook_setup_template_id IN ({$db->sqltext($logbookTemplateId)})";
            $db->query($sql);
            $result = $db->fetch_all_array();
            foreach ($result as $r) {
                $inspectionTypeIds[] = $r['inspection_type_id'];
            }
            $inspectionTypeIds = implode(',', $inspectionTypeIds);
        }
        
        //get inspection Types id
        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME;
        
        if (!is_null($logbookTemplateId)) {
            $query.= " WHERE id IN ({$db->sqltext($inspectionTypeIds)})";
        }
        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }
        $db->query($query);
        $result = $db->fetch_all_array();

        foreach ($result as $r) {
            $inspectionType = new LogbookInspectionType();
            $inspectionType->initByArray($r);
            $inspectionTypeList[] = $inspectionType;
        }

        return $inspectionTypeList;
    }
    
    /**
     * 
     * get count of inspection type list
     * 
     * @param int $facilityId
     * 
     * @return int
     */
    public function getCountInspectionTypeByTemplateId($templateId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeList = array();
        //get inspection type ids by facility id
        if (!is_null($templateId)) {
            $inspectionTypeIds = array();
            $sql = "SELECT inspection_type_id " .
                    "FROM " . self::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE . " " .
                    "WHERE logbook_setup_template_id IN ({$db->sqltext($templateId)})";
            $db->query($sql);
            $result = $db->fetch_all_array();
            foreach ($result as $r) {
                $inspectionTypeIds[] = $r['inspection_type_id'];
            }
            $inspectionTypeIds = implode(',', $inspectionTypeIds);
        }
        //get inspection Types id
        $query = "SELECT count(*) count FROM " . LogbookInspectionType::TABLE_NAME;
        if (!is_null($templateId)) {
            $query.= " WHERE id IN ({$db->sqltext($inspectionTypeIds)})";
        }
        $db->query($query);
        $result = $db->fetch(0);

       return $result->count;
    }
    
    /**
     * 
     * Assign Inspection Type to Inspection Template
     * 
     * @param int $inspectionType
     * @param int $facilityId
     */
    public function assignInspectionTypeToInspectionTemplate($inspectionTypeId, $logbookSetupTemplateId){
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO ".self::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE." (	inspection_type_id, logbook_setup_template_id) VALUES " .
				"({$db->sqltext($inspectionTypeId)}, {$db->sqltext($logbookSetupTemplateId)})";
        $db->query($query);
    }

    /**
     * 
     * Remove Inspection Type to Facility
     * 
     * @param int $inspectionType
     * @param int $facilityId
     */
     public function unAssignInspectionTypeFromInspectionTemplate($inspectionTypeId, $logbookSetupTemplateId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "DELETE FROM " . self::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE . " " .
                "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} ";
        if (!is_null($logbookSetupTemplateId)) {
            $query.="AND logbook_setup_template_id = {$db->sqltext($logbookSetupTemplateId)}";
        }
		$db->query($query);
    }
    
    /**
     * 
     * @param int $inspectionTypeId
     * @return string
     */
    public function getFacilityIdsByInspectionTypeId($inspectionTypeId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "SELECT lt2f.facility_id " .
                "FROM " . LogbookSetupTemplateManager::TB_LOGBOOK_SETUP_TEMPLATE2FACILITY . " lt2f " .
                "LEFT JOIN ".LogbookSetupTemplateManager::TB_INSPECTION_TYPE2LOGBOOK_SETUP_TEMPLATE." it2lt ".
                "ON it2lt.logbook_setup_template_id = lt2f.logbook_setup_template_id ".
                "LEFT JOIN ".LogbookInspectionType::TABLE_NAME ." it ".
                "ON it2lt.inspection_type_id = it.id ".
                "WHERE it.id	= {$db->sqltext($inspectionTypeId)}";
        $db->query($query);
        if ($db->num_rows() == 0) {
            return false;
        }
        
        $result = $db->fetch_all_array();
        $facilityIds = array();
        foreach($result as $r){
            if (!in_array($r['facility_id'], $facilityIds)) {
                $facilityIds[] = $r['facility_id'];
            }
        }
        return $facilityIds;
    }
    
    

}
?>
