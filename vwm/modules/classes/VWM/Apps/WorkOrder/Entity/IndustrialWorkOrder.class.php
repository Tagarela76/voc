<?php

namespace VWM\Apps\WorkOrder\Entity;

class IndustrialWorkOrder extends WorkOrder {

	const TABLE_NAME = 'work_order';
    public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'IndustrialWorkOrder';
		if(isset($id)) {
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

    private function _load() {

		if (is_null($this->getId())) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM ".TB_WORK_ORDER." ".
				"WHERE id={$this->db->sqltext($this->getId())} " .
				"LIMIT 1";
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

    public function save() {

		if($this->getId() ) {
			return $this->update();
		} else {
			return $this->insert();
		}
	}

    /**
     * Insert WO
     * @return int
     */
    protected function insert() {
		$query = "INSERT INTO " . self::TABLE_NAME . " SET " .
				"number = '{$this->db->sqltext($this->getNumber())}', " .
				"description='{$this->db->sqltext($this->getDescription())}', " .
				"customer_name='{$this->db->sqltext($this->getCustomerName())}', " .
				"facility_id = {$this->db->sqltext($this->getFacilityId())}, " .
				"status = '{$this->db->sqltext($this->getStatus())}' ".
				"creation_time=NOW()";

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
	protected function update() {

		$query = "UPDATE " . self::TABLE_NAME . "
					set number='" . $this->db->sqltext($this->getNumber()) . "',
						description='" . $this->db->sqltext($this->getDescription()) . "',
						customer_name='" . $this->db->sqltext($this->getCustomerName()) . "',
						facility_id='" . $this->db->sqltext($this->getFacilityId()) . "',
						status='" . $this->db->sqltext($this->getStatus()) . "'
					WHERE id= " . $this->db->sqltext($this->getId());
		$this->db->query($query);

		return $this->getId();
	}
}

?>