<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Resource extends Model {
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
	 * @var int
	 */
	protected $resource_type_id;
	
	/*
	 * labor_cost
	 * @var float
	 */
	protected $labor_cost=0;
	
	/*
	 * material_cost
	 * @var float
	 */
	protected $material_cost=0;
	
	/*
	 * total_cost
	 * @var float
	 */
	protected $total_cost=NULL;
	
	/*
	 * rate_unittype_id
	 * @var float
	 */
	protected $rate_unittype_id;
	
	/*
	 * rate_qty
	 * @var int
	 */
	protected $rate_qty=1;
	
	/*
	 * step id
	 * @var int
	 */
	protected $step_id;
	
	/*
	 * step_template_id
	 * @var int
	 */
	protected $step_template_id;
	
	
	const TABLE_NAME = 'resource';
	
	//const ResourceId = new array('TIME', 'WEIGHT', 'COUNT');
	
	const TIME = 1;
	const VOLUME = 2;
	const COUNT = 3;
	
	
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
		$this->rate = $rate;
	}

	public function getQty() {
		return $this->qty;
	}

	public function setQty($qty) {
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
		if($this->total_cost===NULL){
			$this->calculateTotalCost();
		} 
		return $this->total_cost;
	}

	private function setTotalCost($total_cost) {
		$this->total_cost = $total_cost;
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
		$this->rate_qty = $rate_qty;
	}

	public function getStepId() {
		return $this->step_id;
	}

	public function setStepId($step_id) {
		$this->step_id = $step_id;
	}

	public function getStepTemplateId() {
		return $this->step_template_id;
	}

	public function setStepTemplateId($step_template_id) {
		$this->step_template_id = $step_template_id;
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

	public function calculateTotalCost(){
		if($this->material_cost==0 && $this->labor_cost==0){
			$this->countCost();
		}
		$total_cost = $this->material_cost+$this->labor_cost;
		$this->setTotalCost($total_cost);
	}
	
	
	/**
	 * function for calculate labor or material cost for certain unit type
	 */
	public function countCost(){
		$unitTypeConvector = new \UnitTypeConverter($this->db);
		$unitType = new \Unittype($this->db);
		$from = $unitType->getNameByID($this->unittype_id);
		$to = $unitType->getNameByID($this->rate_unittype_id);
		$value =  $this->qty;
		
		switch ($this->resource_type_id) {
			case self::TIME:
				$qty = $unitTypeConvector->convertTimeFromTo($from, $to, $value);
				$rate =  $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty*$rate)/$rateQty;
				$this->setLaborCost($laborCost);
				break;
			case self::VOLUME:
				$qty = $unitTypeConvector->convertFromTo($value, $from, $to);
				$rate =  $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty*$rate)/$rateQty;
				$this->setMaterialCost($laborCost);
				break;
			case self::COUNT:
				$qty = $unitTypeConvector->convertCountFromTo($from, $to, $value);
				$rate =  $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty*$rate)/$rateQty;
				$this->setLaborCost($laborCost);
				break;
			default :
				break;
		}
				
	}
	
}

?>
