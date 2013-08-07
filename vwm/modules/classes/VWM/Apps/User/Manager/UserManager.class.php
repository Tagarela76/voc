<?php

namespace VWM\Apps\User\Manager;

use VWM\Apps\User\Entity\User;

class UserManager
{
    /**
     * 
     * get User List by Facility Id
     * 
     * @param int $facilityId
     * 
     * @return \VWM\Apps\User\Entity\User[]
     */
    public function getUserListByFacilityId($facilityId)
    {
        $db = \VOCApp::getInstance()->getService('db');
        $userList = array();
        
        $sql = "SELECT * FROM ".TB_USER." ".
                "WHERE facility_id={$db->sqltext($facilityId)}";
        $db->query($sql);
        $rows = $db->fetch_all_array();
        
        foreach($rows as $row){
            $user = new User();
            $user->initByArray($row);
            $userList[] = $user;
        }
        
        return $userList;
    }
}
?>
