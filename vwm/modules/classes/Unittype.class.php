<?php

class Unittype
{

    /**
     *
     * @var db
     */
    private $db;

    const UNIT_GAL_ID = 1;
    const UNIT_LBS_ID = 2;
    const UNIT_KG_ID = 3;
    const UNIT_L_ID = 4;
    const UNIT_T_ID = 5;
    const UNIT_M_ID = 6;
    const UNIT_OZ_ID = 7;
    const UNIT_IMP_GAL_ID = 8;
    const UNIT_ML_ID = 9;
    const UNIT_MG_ID = 10;
    const UNIT_G_ID = 11;
    const UNIT_GRAIN_ID = 12;
    const UNIT_DRY_GAL_ID = 13;
    const UNIT_IMP_FL_OZ_ID = 14;
    const UNIT_FL_OZ_ID = 15;
    const UNIT_PT_ID = 16;
    const UNIT_QT_ID = 17;

    // TODO: complete list


    function __construct(db $db)
    {
        $this->db = $db;
    }

    public function getUnittypeList()
    {
        //$this->db->select_db(DB_NAME);
        //$query = $this->db->query("SELECT * FROM ".TB_UNITTYPE.",".TB_TYPE." WHERE type.type_id = unittype.type_id ORDER BY name");

        $query = "SELECT * FROM " . TB_UNITCLASS . " uc, " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                " WHERE ut.unit_class_id=uc.id AND t.type_id=ut.type_id" .
                " ORDER BY uc.id";
        $this->db->query($query);

        if ($this->db->num_rows() > 0) {
            $rows = $this->db->fetch_all();
            foreach ($rows as $row) {
                $unittype = array(
                    'unittype_id' => $row->unittype_id,
                    'name' => $row->name,
                    'type_id' => $row->type_id,
                    'type' => $row->type_desc
                );

                $unittypes[] = $unittype;
            }
        }
        return $unittypes;
    }

    public function getUnittypeDetails($unittypeID, $vanilla = false)
    {

        $sql = "SELECT * " .
                "FROM " . TB_UNITTYPE . " u " .
                "JOIN " . TB_TYPE . " t ON t.type_id = u.type_id " .
                "WHERE  u.unittype_id = {$this->db->sqltext($unittypeID)}";
        $this->db->query($sql);
        if ($this->db->num_rows() == 0) {
            //exit;
            //throw new Exception('Unittype::getUnittypeDetails() - query failed, no unittype with received ID '.$unittypeID);
        }

        $data = $this->db->fetch(0);
        $unittypeDetails = array(
            'unittype_id' => $data->unittype_id,
            'name' => $data->name,
            'description' => $data->unittype_desc,
            'formula' => $data->formula,
            'type_id' => $data->type_id,
            'type' => $data->type_desc
        );

        return $unittypeDetails;
    }

    /**
     * Returns unit type details for array of unit type ids. Returns associative array, key is unittype_id
     *
     * @param int array <b>$unittypeIDAray</b>
     */
    public function getUnittypesDetails($unittypeIDAray)
    {

        $query = "SELECT *,unittype_desc as 'description' FROM " . TB_UNITTYPE . "," . TB_TYPE . " WHERE type.type_id = unittype.type_id AND unittype_id IN ( ";

        foreach ($unittypeIDAray as $id) {
            $query .= " $id,";
        }

        $query = substr_replace($query, ")", strlen($query) - 1);

        $this->db->query($query);


        $tmp = $this->db->fetch_all_array();



        foreach ($tmp as $unit) {

            $unittypeDetails[$unit['unittype_id']] = $unit;
        }

        return $unittypeDetails;
    }

    public function setUnittypeDetails($unittypeDetails)
    {

        //$this->db->select_db(DB_NAME);

        $query = "UPDATE " . TB_UNITTYPE . " SET ";

        $query.="name='" . $unittypeDetails['name'] . "', ";
        $query.="unittype_desc='" . $unittypeDetails['description'] . "', ";
        $query.="formula='" . $unittypeDetails['formula'] . "', ";
        $query.="type_id='" . $unittypeDetails['type'] . "'";

        $query.=" WHERE unittype_id=" . $unittypeDetails['unittype_id'];

        $this->db->query($query);
    }

    public function addNewUnittype($unittypeData)
    {
        //$this->db->select_db(DB_NAME);

        $query = "INSERT INTO " . TB_UNITTYPE . " (name, unittype_desc, formula, type_id) VALUES (";

        $query.="'" . $unittypeData["name"] . "', ";
        $query.="'" . $unittypeData["description"] . "', ";
        $query.="'" . $unittypeData["formula"] . "', ";
        $query.="'" . $unittypeData["type"] . "'";
        $query.=')';

        $this->db->query($query);
    }

    public function deleteUnittype($unittypeID)
    {
        //$this->db->select_db(DB_NAME);
        $this->db->query("DELETE FROM " . TB_UNITTYPE . " WHERE unittype_id=" . $unittypeID);
    }

    public function getUnittypeListDefault($sysType = "USAWght")
    {
        //$this->db->select_db(DB_NAME);
        switch ($sysType) {
            case 'USALiquid':
                $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = 'USA' " .
                        "AND t.type_desc in ('Volume Liquid','Volume') " .
                        "ORDER BY ut.unittype_id";
                break;
            case 'USADry':
                $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = 'USA' " .
                        "AND t.type_desc in ('Volume Dry','Volume') " .
                        "ORDER BY ut.unittype_id";
                break;
            case 'USAWght':
                $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = 'USA' " .
                        "AND t.type_desc = 'Weight' " .
                        "ORDER BY ut.unittype_id";
                break;
            case 'MetricVlm':
                $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = 'metric' " .
                        "AND t.type_desc = 'Volume' " .
                        "ORDER BY ut.unittype_id";
                break;
            case 'MetricWght':
                $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = 'metric' " .
                        "AND t.type_desc = 'Weight' " .
                        "ORDER BY ut.unittype_id";
                break;
            case 'AllOther':
                $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                        "WHERE ut.type_id = t.type_id " .
                        "AND ut.system = 'metric' " .
                        "AND t.type_desc = 'Other' " .
                        "ORDER BY ut.unittype_id";
                break;
        }

        $this->db->query($query);

        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);
                $unittype = array(
                    'unittype_id' => $data->unittype_id,
                    'description' => $data->name,
                    'type_id' => $data->type_id,
                    'type' => $data->type_desc,
                    'unittype_desc' => $data->unittype_desc,
                    'system' => $data->system
                );
                $unittypes[] = $unittype;
            }
        }
        return $unittypes;
    }

    public function getUnittypeClass($unittypeID)
    {

        //$this->db->select_db(DB_NAME);
        $query = "SELECT * FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                "WHERE ut.type_id = t.type_id " .
                "AND ut.unittype_id = " . $unittypeID;

        $this->db->query($query);

        if ($this->db->num_rows()) {
            $data = $this->db->fetch(0);
            $unittype = array(
                'unittype_id' => $data->unittype_id,
                'description' => $data->name,
                'type_id' => $data->type_id,
                'type' => $data->type_desc,
                'system' => $data->system
            );
        }

        if ($unittype['system'] == 'USA' && ($unittype['type'] == 'Volume Liquid' || $unittype['type'] == 'Volume')) {
            return 'USALiquid';
        } elseif ($unittype['system'] == 'USA' && ($unittype['type'] == 'Volume Dry' || $unittype['type'] == 'Volume')) {
            return 'USADry';
        } elseif ($unittype['system'] == 'USA' && $unittype['type'] == 'Weight') {
            return 'USAWght';
        } elseif ($unittype['system'] == 'metric' && $unittype['type'] == 'Volume') {
            return 'MetricVlm';
        } elseif ($unittype['system'] == 'metric' && $unittype['type'] == 'Weight') {
            return 'MetricWght';
        } elseif ($unittype['system'] == 'time' && $unittype['type'] == 'Time') {
            return 'Time';
        }
    }

    public function getClassesOfUnits()
    {

        $this->db->query("SELECT * " .
                "FROM " . TB_UNITTYPE . "," . TB_TYPE . " " .
                "WHERE type.type_id = unittype.type_id AND unittype.system IS NOT NULL AND type.type_id <> '3' " .
                "ORDER BY type.type_id ASC");

        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);

                $unittype[$i] = array(
                    'unittype_id' => $data->unittype_id,
                    'name' => $data->name,
                    'unittype_desc' => $data->unittype_desc,
                    'type_id' => $data->type_id,
                    'type_desc' => $data->type_desc,
                    'system' => $data->system,
                    'unit_class_id' => $data->unit_class_id
                );
            }
        }

        return $unittype;
    }

    public function getDefaultUnitTypelist($companyID)
    {

        $query = "SELECT * FROM " . TB_DEFAULT . " d WHERE d.id_of_object=" . (int) $companyID;
        $query.=" AND subject='unittype'";
        $this->db->query($query);

        if ($this->db->num_rows()) {
            for ($j = 0; $j < $this->db->num_rows(); $j++) {
                $data = $this->db->fetch($j);
                $unittype[$j] = $data->id_of_subject;
            }
        }

        return $unittype;
    }

    /**
     * Recursive method
     * get default unit type list by category 
     * 
     * @param int $id
     * @param string $category
     * 
     * @return int[]
     * @throws Exception
     */
    public function getDefaultCategoryUnitTypeList($id, $category)
    {
        $unittype = array();

        $db = VOCApp::getInstance()->getService('db');
        $query = "SELECT * FROM " . TB_DEFAULT . " " .
                "WHERE id_of_object={$db->sqltext((int) $id)} " .
                "AND subject='unittype'" . " " .
                "AND object = '{$db->sqltext($category)}'";

        $db->query($query);
        
        if ($db->num_rows()) {
            $rows = $db->fetch_all_array();
            foreach ($rows as $row) {
                $unittype[] = $row['id_of_subject'];
            }
            return $unittype;
        } else {
            switch ($category) {
                case 'department':
                    $department = new VWM\Hierarchy\Department($db, $id);
                    $unittype = $this->getDefaultCategoryUnitTypeList($department->getFacilityId(), 'facility');
                    break;
                case 'facility':
                    $facility = new \VWM\Hierarchy\Facility($db, $id);
                    $unittype = $this->getDefaultCategoryUnitTypeList($facility->getCompanyId(), 'company');
                    break;
                case 'company':
                    break;
                default :
                    throw new Exception('Invalid category');
                    break;
            }
        }

        return $unittype;
    }

    public function setDefaultUnitTypelist($unitTypeID, $categoryName, $companyID)
    {

        $this->deleteDefaultUnitType($companyID);

        $query = "SELECT " . TB_UNITTYPE . ".unittype_id FROM " . TB_UNITTYPE . " WHERE " . TB_UNITTYPE . ".system <> 'NULL' AND " . TB_UNITTYPE . ".unittype_id IN " .
                "(SELECT DISTINCT unit_type FROM " . TB_MIXGROUP . " WHERE " . TB_MIXGROUP . ".mix_id IN " .
                "(SELECT mix_id FROM " . TB_USAGE . " WHERE " . TB_USAGE . ".department_id IN " .
                "(SELECT department_id FROM " . TB_DEPARTMENT . " WHERE " . TB_DEPARTMENT . ".facility_id IN " .
                "(SELECT facility_id FROM " . TB_FACILITY . " WHERE " . TB_FACILITY . ".company_id='" . $companyID . "'))))";


        $this->db->query($query);

        // select unit types for which has already created products
        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);
                $unittype[$i] = $data->unittype_id;
            }
        }

        // insert unit types that exist but are not marked
        $i = 0;
        $j = 0;
        $flag = 0;
        while ($unittype[$j]) {
            while ($unitTypeID[$i]) {
                if ($unittype[$j] == $unitTypeID[$i]) {
                    $flag = 1;
                }
                $i++;
            }
            if ($flag == 0) {
                $this->insertDefaultUnitType('unittype', $unittype[$j], $categoryName, $companyID);
            }
            $i = 0;
            $j++;
            $flag = 0;
        }

        // insert marked unit types
        $i = 0;
        while ($unitTypeID[$i]) {
            $this->insertDefaultUnitType('unittype', $unitTypeID[$i], $categoryName, $companyID);
            $i++;
        }
    }

    private function deleteDefaultUnitType($companyID)
    {

        $query = "DELETE FROM " . TB_DEFAULT . " WHERE `id_of_object` = '" . $companyID . "'";
        $query.= " AND subject='unittype'";

        $this->db->query($query);
    }

    private function insertDefaultUnitType($unittypeName, $unittypeID, $companyName, $categotyID)
    {
        //$this->db->select_db(DB_NAME);
        $query = "INSERT INTO " . TB_DEFAULT . " (subject, id_of_subject, object, id_of_object) " .
                "VALUES ('" . $unittypeName . "', " . (int) $unittypeID . ", '" . $companyName . "', " . (int) $categotyID . ")";
        $this->db->query($query);
    }

    public function getUnitTypeExist($companyID)
    {
        //80% of U.S. customers use the system USAWeight, so make it default
        $query = "SELECT * " .
                "FROM " . TB_UNITTYPE . ", " . TB_UNITCLASS . " " .
                "WHERE " . TB_UNITTYPE . ".unit_class_id=" . TB_UNITCLASS . ".id " . "AND "
                . TB_UNITTYPE . ".unittype_id IN " .
                "(SELECT d.id_of_subject " .
                "FROM " . TB_DEFAULT . " d " .
                "WHERE d.id_of_object='" . $companyID . "' AND d.subject='unittype') " .
                "AND " . TB_UNITTYPE . ".system IS NOT NULL AND  " . TB_UNITTYPE . ".type_id <> '3'" .
                " ORDER BY " . TB_UNITCLASS . ".id"; //Order by priority unittype
        $this->db->query($query);


        if ($this->db->num_rows() > 0) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);
                $unittype = array(
                    'unittype_id' => $data->unittype_id,
                    'type_id' => $data->type_id,
                    'name' => $data->name
                );
                $unittypes[] = $unittype;
            }
        } else {
            //set default unittype list
            $unittypes = $this->getUnittypeList();
        }

        return $unittypes;
    }

    public function getAllClassesOfUnitTypes()
    {
        $query = "SELECT * FROM `unit_class`";
        $this->db->query($query);

        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);
                $unitClasslist = array(
                    'id' => $data->id,
                    'name' => $data->name,
                    'description' => $data->description
                );
                $unitClasses[] = $unitClasslist;
            }
        }

        return $unitClasses;
    }

    public function getUnittypeListDefaultByCompanyId($companyID, $typeClass)
    {
        //$this->db->select_db(DB_NAME);

        $query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, ut.unittype_desc, ut.system FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t, " . TB_DEFAULT . " def, " . TB_UNITCLASS . " uc " .
                "WHERE def.subject = 'unittype' " .
                "AND ut.unittype_id = def.id_of_subject " .
                "AND ut.type_id = t.type_id " .
                "AND def.object = 'company' " .
                "AND def.id_of_object = '" . $companyID . "' " .
                "AND ut.unit_class_id = uc.id " .
                "AND uc.name = '" . $typeClass . "' " .
                "ORDER BY ut.unittype_id";


        $this->db->query($query);

        if ($this->db->num_rows()) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);
                $unittype = array(
                    'unittype_id' => $data->unittype_id,
                    'description' => $data->name,
                    'type_id' => $data->type_id,
                    'type' => $data->type_desc,
                    'unittype_desc' => $data->unittype_desc,
                    'system' => $data->system
                );
                $unittypes[] = $unittype;
            }
        } else {
            $unittypes = $this->getUnittypeListDefault($typeClass);
        }

        return $unittypes;
    }

    public function getNameByID($id)
    {
        $query = "SELECT name FROM " . TB_UNITTYPE . " WHERE unittype_id = " . $id . "";
        $this->db->query($query);

        return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->name : false;
    }

    public function getDescriptionByID($id)
    {
        $query = "SELECT unittype_desc FROM " . TB_UNITTYPE . " WHERE unittype_id = " . $id . "";
        $this->db->query($query);

        return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->unittype_desc : false;
    }

    public function isWeightOrVolume($unittypeID)
    {
        $query = "SELECT t.type_desc FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                "WHERE ut.unittype_id = '" . $unittypeID . "' " .
                "AND ut.type_id = t.type_id ";
        $this->db->query($query);
        if ($this->db->num_rows()) {
            $data = $this->db->fetch(0);
            switch ($data->type_desc) {
                case 'Weight':
                    return 'weight';
                    break;
                case 'Volume':
                case 'Volume Liquid':
                case 'Volume Dry':
                    return 'volume';
                    break;
                case 'Distance':
                    return false;
                    break;
                case 'Energy':
                    return 'energy';
                    break;
                case 'Other':
                    return 'other';
                    break;
            }
        }
    }

    public function getUnittypListFromClassOfUnittypeID($unittypeID)
    {

        switch ($this->isWeightOrVolume($unittypeID)) {
            case 'weight': {
                    $query = "SELECT ut.unittype_id, ut.unittype_desc FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                            "WHERE ut.type_id = t.type_id " .
                            "AND t.type_desc = 'Weight' " .
                            "ORDER BY ut.unittype_id";
                    break;
                }
            case 'volume': {
                    $query = "SELECT ut.unittype_id, ut.unittype_desc FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                            "WHERE ut.type_id = t.type_id " .
                            "AND t.type_desc in ('Volume','Volume Liquid','Volume Dry'" .
                            "ORDER BY ut.unittype_id";
                    break;
                }
            case 'energy': {
                    $query = "SELECT ut.unittype_id, ut.unittype_desc FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t " .
                            "WHERE ut.type_id = t.type_id " .
                            "AND t.type_desc = 'Energy'" .
                            "ORDER BY ut.unittype_id";
                    break;
                }
            default:
                return false;
        }
        $this->db->query($query);
        if ($this->db->num_rows()) {
            $unittypes = $this->db->fetch_all_array();
            return $unittypes;
        }
        else
            return false;
    }

    public function getUnittypeListByCategory($category, $companyID = null)
    {
        $types = $this->getAllTypesByCategory($category);
        $query = "SELECT ut.unittype_id, ut.name, ut.unittype_desc FROM " . TB_UNITTYPE . " ut, " . TB_TYPE . " t ";
        if ($companyID != null) {
            $query .= ", " . TB_DEFAULT . " def ";
        }
        $query .= " WHERE (";
        foreach ($types as $typeName) {
            $query .= "t.type_desc = '$typeName' OR ";
        }
        $query = substr($query, 0, -3);
        $query .= ") AND ut.type_id = t.type_id ";
        if ($category != 'energy') {
            $query .= " AND ut.system IS NOT NULL ";
        }
        if ($companyID != null) {
            $query .= " AND def.id_of_subject = ut.unittype_id AND def.id_of_object = '$companyID' AND def.subject = 'unittype' AND def.object = 'company'";
        }
        $this->db->query($query);
        if ($this->db->num_rows() > 0) {
            //$unittypeList = $this->db->fetch_all_array();
            $data = $this->db->fetch_all();
            $unittypeList = array();
            foreach ($data as $unittype) {
                $unittypeList [] = array(
                    'id' => $unittype->unittype_id,
                    'name' => $unittype->name,
                    'description' => $unittype->unittype_desc
                );
            }
        } elseif ($companyID != null) {
            $unittypeList = $this->getUnittypeListByCategory($category);
        }
        return $unittypeList;
    }

    public function getAllTypesByCategory($category)
    {
        switch ($category) {
            case 'weight':
                return array('Weight');
                break;
            case 'volume':
                return array('Volume', 'Volume Liquid', 'Volume Dry');
                break;
            case 'energy':
                return array('Energy');
                break;
            case 'other':
                return array('Other');
                break;
        }
        return false;
    }

    public function insertOtherUnitType($unittypeName, $unittypeDesc)
    {
        //$this->db->select_db(DB_NAME);
        $query = "INSERT INTO " . TB_UNITTYPE . " (`unittype_id`, `name`, `unittype_desc`, `formula`, `type_id`, `system`, `unit_class_id`) " .
                "VALUES (NULL, '" . mysql_escape_string($unittypeName) . "', '" . mysql_escape_string($unittypeDesc) . "', NULL, 7, 'metric', 6)";
        $this->db->query($query);
        $id = $this->db->getLastInsertedID();
        if (mysql_error() == '') {
            return $id;
        } else {
            throw new Exception(mysql_error());
        }
        //echo $query;
    }

    /**
     * get unit type id my name
     * @param string $unittype
     * @return array 
     */
    public function getUnittypeByName($unittype)
    {
        $query = "SELECT * FROM " . TB_UNITTYPE . " WHERE name LIKE '" . $this->db->sqltext($unittype) . "'";
        $this->db->query($query);

        if ($this->db->num_rows() == 0) {
            return array();
        } else {
            return $this->db->fetch_array(0);
        }
    }

    public function getTypeNameById($typeId)
    {
        $query = "SELECT type_desc FROM " . TB_TYPE . " WHERE type_id=" . $this->db->sqltext($typeId);
        $this->db->query($query);
        return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->type_desc : false;
    }

    public function getUnitTypeIdByName($typeName)
    {
        $query = "SELECT unittype_id FROM " . TB_UNITTYPE . " " .
                "WHERE name = '{$this->db->sqltext($typeName)}'";
        $this->db->query($query);
        $id = $this->db->fetch(0);
        return $id->unittype_id;
    }

    private function deleteDefaultCategoryUnitType($category, $companyID)
    {

        $query = "DELETE FROM " . TB_DEFAULT . " WHERE `id_of_object` = '" . $companyID . "' " .
                "AND object='" . $category . "' " .
                "AND subject='unittype'";

        $this->db->query($query);
    }

    public function setDefaultCategoryUnitTypelist($unitTypeID, $categoryName, $categoryID)
    {

        $this->deleteDefaultCategoryUnitType($categoryName, $categoryID);

        $query = "SELECT " . TB_UNITTYPE . ".unittype_id FROM " . TB_UNITTYPE . " WHERE " . TB_UNITTYPE . ".system <> 'NULL' AND " . TB_UNITTYPE . ".unittype_id IN " .
                "(SELECT DISTINCT unit_type FROM " . TB_MIXGROUP . " WHERE " . TB_MIXGROUP . ".mix_id IN " .
                "(SELECT mix_id FROM " . TB_USAGE . " WHERE " . TB_USAGE . ".department_id IN " .
                "(SELECT department_id FROM " . TB_DEPARTMENT . " WHERE " . TB_DEPARTMENT . ".facility_id IN " .
                "(SELECT facility_id FROM " . TB_FACILITY . " WHERE " . TB_FACILITY . ".company_id='" . $categoryID . "'))))";


        $this->db->query($query);
        //delete this check for some time
        $deleteCheck = 1;

        // select unit types for which has already created products
        if ($this->db->num_rows() && $deleteCheck != 1) {
            for ($i = 0; $i < $this->db->num_rows(); $i++) {
                $data = $this->db->fetch($i);
                $unittype[$i] = $data->unittype_id;
            }
        }

        // insert unit types that exist but are not marked
        $i = 0;
        $j = 0;
        $flag = 0;
        while ($unittype[$j]) {
            while ($unitTypeID[$i]) {
                if ($unittype[$j] == $unitTypeID[$i]) {
                    $flag = 1;
                }
                $i++;
            }
            if ($flag == 0) {
                $this->insertDefaultUnitType('unittype', $unittype[$j], $categoryName, $categoryID);
            }
            $i = 0;
            $j++;
            $flag = 0;
        }

        // insert marked unit types
        $i = 0;

        while ($unitTypeID[$i]) {
            $this->insertDefaultUnitType('unittype', $unitTypeID[$i], $categoryName, $categoryID);
            $i++;
        }
    }

}
?>