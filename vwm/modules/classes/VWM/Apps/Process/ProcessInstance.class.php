<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class ProcessInstance extends Process {

	
const INSTANCE = 1;
	
	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		$this->setProcessType(self::INSTANCE);
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}

	protected function _insert() {

		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"facility_id, name, last_update_time, process_type, work_order_id" .
				") VALUES(" .
				"{$this->db->sqltext($this->getFacilityId())}," .
				"'{$this->db->sqltext($this->getName())}'," .
				"'{$this->db->sqltext($this->getLastUpdateTime())}', " .
				"'{$this->db->sqltext($this->getProcessType())}', " .
				"'{$this->db->sqltext($this->getWorkOrderId())}'" .
				")";
		$response = $this->db->exec($sql);
		if ($response) {
			$this->setId($this->db->getLastInsertedID());
			return $this->getId();
		} else {
			return false;
		}
	}

	protected function _update() {
		$lastUpdateTime = $this->getLastUpdateTime();

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"facility_id={$this->db->sqltext($this->getFacilityId())}, " .
				"name='{$this->db->sqltext($this->getName())}', " .
				"work_order_id='{$this->db->sqltext($this->getWorkOrderId())}', " .
				"process_type='{$this->db->sqltext($this->getProcessType())}', " .
				"last_update_time='{$lastUpdateTime}' " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}

}

?>
