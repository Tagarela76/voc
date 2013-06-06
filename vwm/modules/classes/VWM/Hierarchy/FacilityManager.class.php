<?php

namespace VWM\Hierarchy;

use VWM\Hierarchy\Facility;

class FacilityManager
{
    /**
     * 
     * get facility list
     * 
     * @param int $companyId
     * 
     * @return VWM\Hierarchy\Facility[]
     */
    public function getFacilityListByCompanyId($companyId = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $facilityList = array();

        $companyId = $db->sqltext($companyId);
        $sql = "SELECT facility_id  " .
                "FROM " . Facility::TABLE_NAME;
        
        if (!is_null($companyId) && $companyId!='') {
            
            $sql.= " WHERE company_id = " . $companyId;
            
        }
        $sql.= " ORDER BY name";
        $db->query($sql);
        
        $facilityIds = null;
        if ($db->num_rows()) {
            $facilityIds = $db->fetch_all_array();
        }

        foreach ($facilityIds as $facilityId) {
            $facility = new Facility($db, $facilityId['facility_id']);
            $facilityList[] = $facility;
        }

        return $facilityList;
    }

}
?>
