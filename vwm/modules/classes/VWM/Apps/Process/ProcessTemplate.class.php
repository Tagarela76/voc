<?php
namespace VWM\Apps\Process;

use VWM\Framework\Model;


class ProcessTemplate extends Process {
	
	
	const Template = 0;
	
	
	
	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		$this->setProcessType(self::Template);
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}
	
	protected function _insert() {
		
		$sql = "INSERT INTO ". self::TABLE_NAME ." (" .
				"facility_id, name, process_type, last_update_time" .
				") VALUES(" .
				"{$this->db->sqltext($this->getFacilityId())}," .
				"'{$this->db->sqltext($this->getName())}'," .
				"'{$this->db->sqltext($this->getProcessType())}'," .
				"'{$this->db->sqltext($this->getLastUpdateTime())}'" .
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
				"last_update_time='{$lastUpdateTime}' " .
				"process_type='{$this->getProcessType()}' " .
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
