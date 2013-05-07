<?php

class InspectionTypeManager
{
    const TB_INSPECTION_TYPE = 'inspection_type';
    const TB_DESCRIPTION_DESCRIPTION = 'inspection_description';

    /**
     * 
     * getting inspection Type structure in json string
     * 
     * @return string
     */
    public function getInspectionTypeListInJson()
    {
        $db = \VOCApp::getInstance()->getService('db');
        $inspectionTypeInJson = array();
        $query = "SELECT settings FROM " . self::TB_INSPECTION_TYPE;
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

    public function getInspectionSubTypeByTypeDescription($typeDescription = null)
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

}
?>
