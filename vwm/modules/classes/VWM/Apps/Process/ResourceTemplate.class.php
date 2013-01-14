<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class ResourceTemplate extends Resource {

	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}

	protected function _insert() {
		if ($this->total_cost===NULL){
			$this->calculateTotalCost();
		}
		
		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"description, qty, unittype_id, resource_type_id, labor_cost, " .
				"material_cost, total_cost, rate, rate_unittype_id, rate_qty, " .
				"step_template_id, last_update_time" .
				") VALUES(" .
				"'{$this->db->sqltext($this->getDescription())}'," .
				"{$this->db->sqltext($this->getQty())}," .
				"{$this->db->sqltext($this->getUnittypeId())}," .
				"{$this->db->sqltext($this->getResourceTypeId())}," .
				"{$this->db->sqltext($this->getLaborCost())}," .
				"{$this->db->sqltext($this->getMaterialCost())}," .
				"{$this->db->sqltext($this->getTotalCost())}," .
				"{$this->db->sqltext($this->getRate())}," .
				"{$this->db->sqltext($this->getRateUnittypeId())}," .
				"{$this->db->sqltext($this->getRateQty())}," .
				"{$this->db->sqltext($this->getStepTemplateId())}," .
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
		if ($this->total_cost===NULL){
			$this->calculateTotalCost();
		}
		$lastUpdateTime = $this->getLastUpdateTime();

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"description='{$this->db->sqltext($this->getDescription())}', " .
				"qty={$this->db->sqltext($this->getQty())}, " .
				"unittype_id={$this->db->sqltext($this->getUnittypeId())}, " .
				"resource_type_id={$this->db->sqltext($this->getResourceTypeId())}, " .
				"labor_cost={$this->db->sqltext($this->getLaborCost())}, " .
				"material_cost={$this->db->sqltext($this->getMaterialCost())}, " .
				"total_cost={$this->db->sqltext($this->getTotalCost())}, " .
				"rate={$this->db->sqltext($this->getRate())}, " .
				"rate_unittype_id={$this->db->sqltext($this->getRateUnittypeId())}, " .
				"rate_qty={$this->db->sqltext($this->getRateQty())}, " .
				"step_template_id={$this->db->sqltext($this->getStepTemplateId())}, " .
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
