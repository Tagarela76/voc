<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class ResourceTemplate extends Resource
{
	const TABLE_NAME = 'resource_template';
	const TIME = 1;
	const VOLUME = 2;
	const GOM = 3;

	public function __construct(\db $db, $id = null)
	{
		$this->db = $db;
		if (isset($id)) {
			$this->setId($id);
			$this->load();
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
    
	public function load()
	{
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

	protected function _insert()
	{
		if ($this->total_cost === null) {
			$this->calculateTotalCost();
		}

		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"description, qty, unittype_id, resource_type_id, labor_cost, " .
				"material_cost, total_cost, rate, rate_unittype_id, rate_qty, " .
				"step_id, last_update_time" .
				") VALUES(" .
				"'{$this->db->sqltext($this->getDescription())}'," .
				"'{$this->db->sqltext($this->getQty())}' ," .
				"{$this->db->sqltext($this->getUnittypeId())}," .
				"{$this->db->sqltext($this->getResourceTypeId())}," .
				"{$this->db->sqltext($this->getLaborCost())}," .
				"{$this->db->sqltext($this->getMaterialCost())}," .
				"{$this->db->sqltext($this->getTotalCost())}," .
				"'{$this->db->sqltext($this->getRate())}'," .
				"{$this->db->sqltext($this->getRateUnittypeId())}," .
				"'{$this->db->sqltext($this->getRateQty())}'," .
				"{$this->db->sqltext($this->getStepId())}," .
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

	protected function _update()
	{
		if ($this->total_cost === null) {
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
				"step_id={$this->db->sqltext($this->getStepId())}, " .
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
	 * function for creating resourse instance by resource template
	 * @param type $stepId
	 * @return boolean|\VWM\Apps\Process\ResourceInstance
	 */
	public function createInstanceResource($stepId = null)
	{
		if (is_null($stepId)) {
			$stepId = $this->getStepId();
		}

		if (is_null($stepId)) {
			return false;
		}

		$resource = new ResourceInstance($this->db);
		$resource->setDescription($this->getDescription());
		$resource->setQty($this->getQty());
		$resource->setUnittypeId($this->getUnittypeId());
		$resource->setResourceTypeId($this->getResourceTypeId());
		$resource->setLaborCost($this->labor_cost);
		$resource->setMaterialCost($this->material_cost);
		$resource->setRate($this->getRate());
		$resource->setRateUnittypeId($this->getRateUnittypeId());
		$resource->setRateQty($this->getRateQty());
		$resource->setStepId($stepId);

		return $resource;
	}
}

?>
