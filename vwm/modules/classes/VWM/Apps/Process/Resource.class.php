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

	
}

?>
