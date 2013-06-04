<?php

namespace VWM\Apps\Logbook\Manager;

use \VWM\Apps\Logbook\Entity\LogbookInspectionType;

class InspectionTypeManager
{
    const TB_INSPECTION_TYPE = 'inspection_type';
    const TB_DESCRIPTION_DESCRIPTION = 'inspection_description';
    const TB_INSPECTION_TYPE2FACILITY = 'inspection_type2facility';

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
        
        //get inspection type ids by facility id
        if (!is_null($facilityId)) {
            $inspectionTypeIds = array();
            $sql = "SELECT inspection_type_id " .
                    "FROM " . self::TB_INSPECTION_TYPE2FACILITY . " " .
                    "WHERE facility_id IN ({$db->sqltext($facilityId)})";
            $db->query($sql);
            
            $result = $db->fetch_all_array();
            foreach ($result as $r) {
                $inspectionTypeIds[] = $r['inspection_type_id'];
            }
            $inspectionTypeIds = implode(',', $inspectionTypeIds);
        }
         //get inspection Types id
        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME;
        
        if (!is_null($facilityId)) {
            $query.= " WHERE id IN ({$db->sqltext($inspectionTypeIds)})";
        }
        
        $db->query($query);
        $result = $db->fetch_all_array();
        foreach ($result as $r) {
            $inspectionTypeInJson[] = $r['settings'];
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
     * @param int $facilityId
     * 
     * @return \VWM\Apps\Logbook\Entity\LogbookInspectionTyp[]
     */
    public function getInspectionTypeList($facilityId = null, $pagination = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeList = array();

        //get inspection type ids by facility id
        if (!is_null($facilityId)) {
            $inspectionTypeIds = array();
            $sql = "SELECT inspection_type_id " .
                    "FROM " . self::TB_INSPECTION_TYPE2FACILITY . " " .
                    "WHERE facility_id IN ({$db->sqltext($facilityId)})";
            $db->query($sql);
            $result = $db->fetch_all_array();
            foreach ($result as $r) {
                $inspectionTypeIds[] = $r['inspection_type_id'];
            }
            $inspectionTypeIds = implode(',', $inspectionTypeIds);
        }
        //get inspection Types id
        $query = "SELECT * FROM " . LogbookInspectionType::TABLE_NAME;
        
        if (!is_null($facilityId)) {
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
    public function getCountInspectionTypeByFacilityId($facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeList = array();

        //get inspection type ids by facility id
        if (!is_null($facilityId)) {
            $inspectionTypeIds = array();
            $sql = "SELECT inspection_type_id " .
                    "FROM " . self::TB_INSPECTION_TYPE2FACILITY . " " .
                    "WHERE facility_id IN ({$db->sqltext($facilityId)})";
            $db->query($sql);
            $result = $db->fetch_all_array();
            foreach ($result as $r) {
                $inspectionTypeIds[] = $r['inspection_type_id'];
            }
            $inspectionTypeIds = implode(',', $inspectionTypeIds);
        }
        //get inspection Types id
        $query = "SELECT count(*) count FROM " . LogbookInspectionType::TABLE_NAME;
        
        if (!is_null($facilityId)) {
            $query.= " WHERE id IN ({$db->sqltext($inspectionTypeIds)})";
        }
        $db->query($query);
        $result = $db->fetch(0);

       return $result->count;
    }
    
    /**
     * 
     * Assign Inspection Type to Facility
     * 
     * @param int $inspectionType
     * @param int $facilityId
     */
    public function assignInspectionTypeToFacility($inspectionTypeId, $facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "INSERT INTO ".self::TB_INSPECTION_TYPE2FACILITY." (	inspection_type_id, facility_id) VALUES " .
				"({$db->sqltext($inspectionTypeId)}, {$db->sqltext($facilityId)})";
        $db->query($query);
    }

    /**
     * 
     * Remove Inspection Type to Facility
     * 
     * @param int $inspectionType
     * @param int $facilityId
     */
    public function unAssignInspectionTypeToFacility($inspectionTypeId, $facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $query = "DELETE FROM " . self::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id = {$db->sqltext($inspectionTypeId)} ";
        if (!is_null($facilityId)) {
            $query.="AND facility_id = {$db->sqltext($facilityId)}";
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
        $query = "SELECT facility_id " .
                "FROM " . self::TB_INSPECTION_TYPE2FACILITY . " " .
                "WHERE inspection_type_id	= {$db->sqltext($inspectionTypeId)}";
        $db->query($query);
        if ($db->num_rows() == 0) {
            return false;
        }
        
        $result = $db->fetch_all_array();
        $facilityIds = array();
        foreach($result as $r){
            $facilityIds[] = $r['facility_id'];
        }
        //$facilityIds = implode(',', $facilityIds);
        
        return $facilityIds;
    }

}
?>
