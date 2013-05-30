<?php

use \VWM\Apps\Logbook\Entity\LogbookInspectionType;

class InspectionTypeManager
{

    const TB_INSPECTION_TYPE = 'inspection_type';
    const TB_DESCRIPTION_DESCRIPTION = 'inspection_description';

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
        $query = "SELECT settings FROM " . self::TB_INSPECTION_TYPE;
        
        if (!is_null($facilityId)) {
            $query.= " WHERE facility_id = {$db->sqltext($facilityId)}";
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
            return $typeList[0]->subtypes;
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
    public function getInspectionTypeList($facilityId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeList = array();
        $query = "SELECT * FROM " .LogbookInspectionType::TABLE_NAME;
        
        if (!is_null($facilityId)) {
            $query.= " WHERE facility_id IN ({$db->sqltext($facilityId)})";
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

}
?>
