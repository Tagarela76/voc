<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class StepInstance extends Step {
	/*
	 * step id
	 * @var int
	 */
	
	protected $process_id;

	/*
	 * step number
	 * @var int
	 */
	protected $total_spent_time = 0;

	/*
	 * resources
	 * @var array of objects
	 */
	protected $init_resources = array();

	const TABLE_NAME = 'step_instance';
	const RESOURCE_TABLE = 'resource_instance';
	const TIME = 1;
	const VOLUME = 2;
	const COUNT = 3;

	/**
	 * get and set resources for initialization
	 * @return type array
	 */
	public function getInitResources() {
		return $this->init_resources;
	}

	public function setInitResources($init_resources) {
		$this->init_resources = $init_resources;
	}

	public function getProcessId() {
		return $this->process_id;
	}

	public function setProcessId($process_id) {
		$this->process_id = $process_id;
	}

	public function getTotalSpentTime() {
		$this->calculateTotalSpentTime();
		return $this->total_spent_time;
	}

	
	public function load() {
		if (is_null($this->getId())) {
			return false;
		}
		$sql = "SELECT * " .
				"FROM " . self::TABLE_NAME . " " .
				"WHERE id = {$this->db->sqltext($this->getId())} " .
				"LIMIT 1";

		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
	}
	
	private function calculateTotalSpentTime() {
		$resources = $this->getResources();
		$totalSpentTime = 0;
		
		foreach ($resources as $resource) {

			if ($resource->getResourceTypeId() == self::TIME) {
				$unitTypeConvector = new \UnitTypeConverter($this->db);
				$unitType = new \Unittype($this->db);
				$unittypeName = $unitType->getNameByID($resource->getUnittypeId());
				$qty = $unitTypeConvector->convertToDefaultTime($resource->getQty(), $unittypeName);
				$totalSpentTime+=$qty;
			}
			
		}
		$this->total_spent_time = $totalSpentTime;
	}

	public function getResources() {
		$sql = "SELECT * FROM " . self::RESOURCE_TABLE .
				" WHERE step_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$resourcesDetails = $this->db->fetch_all_array();
		$resources = array();
		foreach ($resourcesDetails as $resourceDetails) {
			$resource = new ResourceTemplate($this->db);
			$resource->initByArray($resourceDetails);
			$resources[] = $resource;
		}
		return $resources;
	}

	protected function _insert() {

		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"number, process_id, last_update_time, description, optional" .
				") VALUES(" .
				"{$this->db->sqltext($this->getNumber())}," .
				"'{$this->db->sqltext($this->getProcessId())}'," .
				"'{$this->db->sqltext($this->getLastUpdateTime())}'," .
				"'{$this->db->sqltext($this->getDescription())}'," .
				"'{$this->db->sqltext($this->getOptional())}'" .
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
				"process_id='{$this->db->sqltext($this->getProcessId())}', " .
				"optional='{$this->db->sqltext($this->getOptional())}', " .
				"description='{$this->db->sqltext($this->getDescription())}', " .
				"last_update_time='{$lastUpdateTime}' " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}
	
	public function deleteCurrentStepInstance($stepId = null){
		if (is_null($stepId)) {
			$stepId = $this->getId();
		}

		if (is_null($stepId)) {
			return false;
		}
		
		$sql = "DELETE s,r FROM ".self::TABLE_NAME." s ".
				"LEFT JOIN ".self::RESOURCE_TABLE." r ". 
				"ON s.id = r.step_id ".
				"WHERE s.id = {$this->db->sqltext($stepId)}";
		$response = $this->db->exec($sql);
		if ($response) {
			return true;
		} else {
			return false;
		}
	}

}

?>
