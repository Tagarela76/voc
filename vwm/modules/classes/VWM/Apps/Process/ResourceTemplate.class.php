<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class ResourceTemplate extends Resource {
	/*
	 * step id
	 * @var int
	 */

	protected $id;

	/*
	 * description 
	 * @var string
	 */
	protected $description;

	/*
	 * rate
	 * @var float
	 */
	protected $rate;

	/*
	 * qty
	 * @var float
	 */
	protected $qty;

	/*
	 * unittype_id
	 * @var float
	 */
	protected $unittype_id;

	/*
	 * resource type id
	 * @var int (TIME = 1, VOLUME = 2, COUNT=3)
	 */
	protected $resource_type_id;

	/*
	 * labor_cost
	 * @var float
	 */
	protected $labor_cost = 0;

	/*
	 * material_cost
	 * @var float
	 */
	protected $material_cost = 0;

	/*
	 * total_cost
	 * @var float
	 */
	protected $total_cost = NULL;

	/*
	 * rate_unittype_id
	 * @var float
	 */
	protected $rate_unittype_id;

	/*
	 * rate_qty
	 * @var int
	 */
	protected $rate_qty = 1;

	/*
	 * step id
	 * @var int
	 */
	protected $step_id;

	/*
	 * step_template_id
	 * @var int
	 */

	const TABLE_NAME = 'resource_template';
	const TIME = 1;
	const VOLUME = 2;
	const GOM = 3;

	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getRate() {
		return $this->rate;
	}

	public function setRate($rate) {
		$rate = $this->validateCount($rate);
		$this->rate = $rate;
	}

	public function getQty() {
		return $this->qty;
	}

	public function setQty($qty) {
		$qty = $this->validateCount($qty);
		$this->qty = $qty;
	}

	public function getUnittypeId() {
		return $this->unittype_id;
	}

	public function setUnittypeId($unittype_id) {
		$this->unittype_id = $unittype_id;
	}

	public function getResourceTypeId() {
		return $this->resource_type_id;
	}

	public function setResourceTypeId($resource_type_id) {
		$this->resource_type_id = $resource_type_id;
	}

	public function getLaborCost() {
		return $this->labor_cost;
	}

	public function setLaborCost($labor_cost) {
		$this->labor_cost = $labor_cost;
	}

	public function getMaterialCost() {
		return $this->material_cost;
	}

	public function setMaterialCost($material_cost) {
		$this->material_cost = $material_cost;
	}

	public function getTotalCost() {
		if ($this->total_cost === NULL) {
			$this->calculateTotalCost();
		}
		return $this->total_cost;
	}

	public function setTotalCost($total_cost) {
		$this->total_cost = $this->validateCount($total_cost);
	}

	public function getRateUnittypeId() {
		return $this->rate_unittype_id;
	}

	public function setRateUnittypeId($rate_unittype_id) {
		$this->rate_unittype_id = $rate_unittype_id;
	}

	public function getRateQty() {
		return $this->rate_qty;
	}

	public function setRateQty($rate_qty) {
		$rate_qty = $this->validateCount($rate_qty);
		$this->rate_qty = $rate_qty;
	}

	public function getStepId() {
		return $this->step_id;
	}

	public function setStepId($step_id) {
		$this->step_id = $step_id;
	}

	public function calculateTotalCost() {

		if ($this->material_cost == 0 && $this->labor_cost == 0) {
			$this->countCost();
		}
		$total_cost = $this->material_cost + $this->labor_cost;
		$this->setTotalCost($total_cost);
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
	
	/**
	 * function for calculate labor or material cost for certain unit type
	 */
	public function countCost() {
		$unitTypeConvector = new \UnitTypeConverter($this->db);
		$unitType = new \Unittype($this->db);
		$from = $unitType->getNameByID($this->unittype_id);
		$to = $unitType->getNameByID($this->rate_unittype_id);
		$value = $this->qty;

		switch ($this->resource_type_id) {
			case self::TIME:
				$qty = $unitTypeConvector->convertTimeFromTo($from, $to, $value);
				$rate = $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty * $rate) / $rateQty;
				$this->setLaborCost($laborCost);
				break;
			case self::VOLUME:
				if ($to == 'LS' || $from == 'LS') {
					$qty = $value;
				} else {
					$qty = $unitTypeConvector->convertFromTo($value, $from, $to);
				}
				$rate = $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty * $rate) / $rateQty;
				$this->setMaterialCost($laborCost);
				break;
			case self::GOM:
				if ($to == 'LS' || $from == 'LS') {
					$qty = $value;
				} else {
					$qty = $unitTypeConvector->convertCountFromTo($from, $to, $value);
				}
				$rate = $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty * $rate) / $rateQty;
				$this->setMaterialCost($laborCost);
				break;
			default :
				throw new \Exception('unccorect resource type');
				break;
		}
	}

	protected function _insert() {

		if ($this->total_cost === NULL) {
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

	protected function _update() {
		if ($this->total_cost === NULL) {
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

	private function validateCount($value) {
		$value = ereg_replace(',', '.', $value);
		return $value;
	}
	
	public function createInstanceResource($stepId = null){
		
		if(is_null($stepId)){
			$stepId = $this->getStepId();
		}
		
		if(is_null($stepId)){
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
		
		$resourceInstanceId = $resource->save();
		
		//save resources
		if ($resourceInstanceId) {
			return $resource;
		} else {
			return false;
		}
	}

}

?>
