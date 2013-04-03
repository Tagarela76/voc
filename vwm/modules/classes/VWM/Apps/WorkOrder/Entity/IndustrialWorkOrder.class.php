<?php

namespace VWM\Apps\WorkOrder\Entity;

class IndustrialWorkOrder extends WorkOrder
{
    public function __construct(\db $db, $id = null)
    {
        $this->db = $db;
        $this->modelName = 'IndustrialWorkOrder';
        if (isset($id)) {
            $this->setId($id);
            $this->_load();
        }
    }

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }

    private function _load()
    {
        if (is_null($this->getId())) {
            return false;
        }
        $sql = "SELECT * " .
                "FROM " . self::TABLE_NAME . " " .
                "WHERE id={$this->db->sqltext($this->getId())} " .
                "LIMIT 1";
        $this->db->query($sql);

        if ($this->db->num_rows() == 0) {
            return false;
        }
        $rows = $this->db->fetch(0);

        $this->initByArray($rows);
    }

    /**
     * Insert WO
     * @return int
     */
    protected function insert()
    {
        $creation_time = $this->getCreationTime();
        if ($creation_time == '') {
            $creation_time = 'NULL';
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " SET " .
                "number = '{$this->db->sqltext($this->getNumber())}', " .
                "description='{$this->db->sqltext($this->getDescription())}', " .
                "customer_name='{$this->db->sqltext($this->getCustomerName())}', " .
                "facility_id = {$this->db->sqltext($this->getFacilityId())}, " .
                "status = '{$this->db->sqltext($this->getStatus())}', " .
                "creation_time='{$creation_time}' ";

        if ($this->getProcessTemplateId() != null) {
            $query.=", process_template_id = '{$this->db->sqltext($this->getProcessTemplateID())}'";
        }

        $this->db->query($query);
        $id = $this->db->getLastInsertedID();
        $this->setId($id);
        return $id;
    }

    /**
     * Update WO
     * @return int
     */
    protected function update()
    {
        $creation_time = $this->getCreationTime();
        if ($creation_time == '') {
            $creation_time = 'NULL';
        }
        $query = "UPDATE " . self::TABLE_NAME . " " .
                "set number='{$this->db->sqltext($this->getNumber())}', " .
                "description='{$this->db->sqltext($this->getDescription())}', " .
                "customer_name='{$this->db->sqltext($this->getCustomerName())}', " .
                "facility_id='{$this->db->sqltext($this->getFacilityId())}', " .
                "status='{$this->db->sqltext($this->getStatus())}', " .
                "creation_time={$creation_time} " .
                "WHERE id= " . $this->db->sqltext($this->getId());
                
        $this->db->query($query);

        return $this->getId();
    }
}
?>