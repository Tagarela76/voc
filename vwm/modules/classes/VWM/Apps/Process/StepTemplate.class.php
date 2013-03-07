<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class StepTemplate extends Step
{
	const TABLE_NAME = 'step_template';
	const RESOURCE_TABLE = 'resource_template';
	const TIME = 1;
	const VOLUME = 2;
	const COUNT = 3;

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }
    
	public function load()
	{
		return parent::load(self::TABLE_NAME);
	}

	/**
	 * get and set resources for initialization
	 * @return type array
	 */
	public function getTotalSpentTime()
	{
		$this->calculateTotalSpentTime();
		return $this->total_spent_time;
	}

	private function calculateTotalSpentTime()
	{
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

	/**
	 * function for getting step resources
	 * @return boolean|\VWM\Apps\Process\ResourceTemplate
	 */
	public function getResources()
	{
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

	protected function _insert()
	{
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

	protected function _update()
	{
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

	/**
	 * function for creating step instance by step template
	 * @param type $processInstanceId
	 * @return \VWM\Apps\Process\StepInstance
	 */
	public function createInstanceStep($processInstanceId = null)
	{
		$step = new StepInstance($this->db);
		$step->setNumber($this->getNumber());
		$step->setProcessId($processInstanceId);
		$step->setDescription($this->getDescription());
		$step->setOptional($this->getOptional());

		return $step;
	}
    
}
?>
