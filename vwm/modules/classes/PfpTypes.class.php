<?php

use \VWM\Hierarchy\Department;

class PfpTypes
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var int
     */
    public $facility_id;
    public $pfps = array();

    /**
     * db connection
     * @var db
     */
    private $db;
    /*
     * pfp for department
     */
    public $departments;
    public $searchCriteria = array();

    function __construct(db $db, $pfpTypeId = null)
    {
        $this->db = $db;

        if (isset($pfpTypeId)) {
            $this->id = $pfpTypeId;
            $this->_load();
        }
    }

    const TB_PFP_2_DEPARTMENT = 'pfp_type2department';

    /**
     * add pfp type
     * @return int
     */
    public function save()
    {
        if ($this->id && !is_null($this->id)) {
            $this->_update();
        } else {
            $this->_insert();
        }
    }

    private function _saveDepartmentPFP()
    {

        if (count($this->departments) == 0) {
            return false;
        }
        //Delete all from table
        $query = "DELETE FROM " . self::TB_PFP_2_DEPARTMENT .
                " WHERE  pfp_type_id={$this->db->sqltext($this->id)}";
        $this->db->query($query);
        //Insert
        $query = "INSERT INTO " . self::TB_PFP_2_DEPARTMENT . "(pfp_type_id, department_id)
				  VALUES (" .
                "{$this->db->sqltext($this->id)}, " .
                "{$this->db->sqltext($this->departments[0]->getDepartmentId())})";

        for ($i = 1; $i < count($this->departments); $i++) {
            $query .= ",(
				'" . $this->db->sqltext($this->id) . "'
                , " . $this->db->sqltext($this->departments[$i]->getDepartmentId()) . "
				)";
        }

        $this->db->query($query);
    }

    private function _insert()
    {

        $query = "INSERT INTO " . TB_PFP_TYPES . "(name, facility_id)
				VALUES (
				'" . $this->db->sqltext($this->name) . "'
                , " . $this->db->sqltext($this->facility_id) . "
				)";

        $this->db->query($query);
        $pfpTypeId = $this->db->getLastInsertedID();
        $this->id = $pfpTypeId;
        $this->_saveDepartmentPFP();
        return $this->id;
    }

    private function _update()
    {
        $query = "UPDATE " . TB_PFP_TYPES . " SET " .
                "name='{$this->db->sqltext($this->name)}'" .
                " WHERE id=" . $this->id;
        $this->db->query($query);
        $this->_saveDepartmentPFP();
        return $this->id;
    }

    /**
     *
     * delete pfp type
     */
    public function delete()
    {

        $sql = "DELETE FROM " . TB_PFP_TYPES . "
				 WHERE id=" . $this->db->sqltext($this->id);
        $this->db->query($sql);
    }

    /**
     *
     * Overvrite get property if property is not exists or private.
     * @param string $name - property name. method call method get_%property_name%, if method does not exists - return property value;
     */
    public function __get($name)
    {


        if (method_exists($this, "get_" . $name)) {
            $methodName = "get_" . $name;
            $res = $this->$methodName();
            return $res;
        } else {
            return $this->$name;
        }
    }

    public function getDepartments()
    {
        if ($this->departments === null) {

            $sql = "SELECT *" .
                    " FROM " . self::TB_PFP_2_DEPARTMENT . " p " .
                    "JOIN " . TB_DEPARTMENT . " d ON " .
                    "p.department_id=d.department_id " .
                    "WHERE pfp_type_id = {$this->db->sqltext($this->id)}";


            $this->db->query($sql);
            if ($this->db->num_rows() == 0) {
                $this->departments = array();
                return $this->departments;
            }

            $rows = $this->db->fetch_all_array();
            foreach ($rows as $row) {
                $department = new Department($this->db);
                $department->initByArray($row);
                $this->departments[] = $department;
            }
        }

        return $this->departments;
    }

    public function setDepartments($departments)
    {
        $this->departments = $departments;
    }

    /**
     * Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
     * @param string $name - name of property
     * @param mixed $value - value to set
     */
    public function __set($name, $value)
    {

        /* Call setter only if setter exists */
        if (method_exists($this, "set_" . $name)) {
            $methodName = "set_" . $name;
            $this->$methodName($value);
        }
        /*
         * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
         * it will craete dynamic property, like usually does PHP
         */ else if (!property_exists($this, $name)) {
            $this->$name = $value;
        }
        /*
         * property exists and private or protected, do not touch. Keep OOP
         */ else {
            //Do nothing
        }
    }

    private function _load()
    {

        if (!isset($this->id)) {
            return false;
        }
        $sql = "SELECT *
				FROM " . TB_PFP_TYPES . "
				 WHERE id=" . $this->db->sqltext($this->id);
        $this->db->query($sql);

        if ($this->db->num_rows() == 0) {
            return false;
        }
        $rows = $this->db->fetch(0);

        foreach ($rows as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get list of PFP's of current type
     * @param Pagination $pagination
     * @return boolean|PFP[]
     */
    public function getPfpProducts(Pagination $pagination = null)
    {
        if ($this->pfps) {
            return $this->pfps;
        }

        $query = "SELECT * FROM " . TB_PFP . " pfp " .
                "JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                "WHERE pfp2t.pfp_type_id = {$this->db->sqltext($this->id)}";

        if (count($this->searchCriteria) > 0) {
            $searchSql = array();
            $query .= " AND ( ";
            foreach ($this->searchCriteria as $pfp) {
                $searchSql[] = " pfp.description LIKE ('%" . $this->db->sqltext($pfp) . "%')";
            }
            $query .= implode(' OR ', $searchSql);
            $query .= ") ";
        }

        $query .= " ORDER BY pfp.description";
        if (isset($pagination)) {
            $query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
        }

        $this->db->query($query);
        $rows = $this->db->fetch_all_array();

        if ($this->db->num_rows() == 0) {
            return false;
        }
        $pfpProducts = array();
        $pfpManager = new PFPManager($this->db);
        foreach ($rows as $row) {
            $pfp = $pfpManager->getPfp($row["id"]);
            $pfpProducts[] = $pfp;
        }
        $this->pfps = $pfpProducts;
        return $pfpProducts;
    }

    /**
     * get pfp Products Count
     * 
     * @return int
     */
    public function getPfpProductsCount()
    {
        $query = "SELECT count(*) count FROM " . TB_PFP . " pfp " .
                "JOIN " . TB_PFP2PFP_TYPES . " pfp2t ON pfp.id = pfp2t.pfp_id " .
                "WHERE pfp2t.pfp_type_id = {$this->db->sqltext($this->id)}";

        if (count($this->searchCriteria) > 0) {
            $searchSql = array();
            $query .= " AND ( ";
            foreach ($this->searchCriteria as $pfp) {
                $searchSql[] = " pfp.description LIKE ('%" . $this->db->sqltext($pfp) . "%')";
            }
            $query .= implode(' OR ', $searchSql);
            $query .= ") ";
        }

        $this->db->query($query);
        $count = $this->db->fetch(0);
        return $count->count;
    }

}
?>