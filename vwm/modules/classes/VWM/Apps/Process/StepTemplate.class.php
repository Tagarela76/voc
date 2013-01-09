<?php
namespace VWM\Apps\Process;

use VWM\Framework\Model;


class StepTemplate extends Step {
	
	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}
	
	protected function _insert() {
		
		$sql = "INSERT INTO ". self::TABLE_NAME ." (" .
				"number, process_template_id, last_update_time" .
				") VALUES(" .
				"{$this->db->sqltext($this->getNumber())}," .
				"'{$this->db->sqltext($this->getProcessTemplateId())}'," .
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
				"number={$this->db->sqltext($this->getNumber())}, " .
				"process_template_id='{$this->db->sqltext($this->getProcessTemplateId())}', " .
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
