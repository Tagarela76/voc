<?php

namespace VWM\Import\Process;

class ProcessUploaderMapper extends \VWM\Import\Mapper {

	private $process_name = array('Process Name');
	private $step_number = array('Step');
	private $optional = array('Optional Steps', 'Optional', 'Steps');
	private $step_description = array('Description of Step');
	private $resource_description = array('Process of Work');
	private $process_type = array('Process Type', 'Process', 'Type', 'Process ', 'Process  Type', 'Process   Type', ' Type');
	private $unit_type = array('Unit Type');
	private $rate = array('Rate');
	private $qty = array('Qty');
	private $cost = array('Cost');
	private $rate_unit_type = array('Rate Unit Type');

	function __construct() {
		
	}

	public function getProcessName() {
		return $this->process_name;
	}

	public function getStepNumber() {
		return $this->step_number;
	}

	public function getOptional() {
		return $this->optional;
	}

	public function getStepDescription() {
		return $this->step_description;
	}

	public function getResourceDescription() {
		return $this->resource_description;
	}

	public function getProcessType() {
		return $this->process_type;
	}

	public function getUnitType() {
		return $this->unit_type;
	}

	public function getRate() {
		return $this->rate;
	}

	public function getQty() {
		return $this->qty;
	}

	public function getCost() {
		return $this->cost;
	}

	public function getRateUnitType() {
		return $this->rate_unit_type;
	}

	public function getMap() {
		return array(
			"processName" => $this->getProcessName(),
			"stepNumber" => $this->getStepNumber(),
			"optional" => $this->getOptional(),
			"stepDescription" => $this->getStepDescription(),
			"resourceDescription" => $this->getResourceDescription(),
			"processType" => $this->getProcessType(),
			"unitType" => $this->getUnitType(),
			"rate" => $this->getRate(),
			"qty" => $this->getQty(),
			"cost" => $this->getCost(),
			"rateUnitType" => $this->getRateUnitType(),
		);
	}

}

?>
