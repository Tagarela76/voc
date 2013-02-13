<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Process extends Model {
	/*
	 * process id 
	 * @var int
	 */

	protected $id = NULL;
	/*
	 * process facility_id
	 * @var int
	 */
	protected $facility_id = NULL;
	/*
	 * process name
	 * @var string
	 */
	protected $name;

	/*
	 * process type
	 * 
	 */
	protected $process_type;
	/*
	 * work order id
	 * @var int
	 */
	protected $work_order_id;

	/*
	 * @var int
	 */
	protected $current_step_number;

	/*
	 * process steps
	 * $var array of objects
	 */
	protected $process_steps = array();

	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}

	public function getProcessType() {
		return $this->process_type;
	}

	public function setProcessType($process_type) {
		$this->process_type = $process_type;
	}

	/**
	 * get and set steps for initialization
	 * @return type array
	 */
	public function getProcessSteps() {
		return $this->process_steps;
	}

	public function setProcessSteps($process_steps) {
		$this->process_steps = $process_steps;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getWorkOrderId() {
		return $this->work_order_id;
	}

	public function setWorkOrderId($work_order_id) {
		$this->work_order_id = $work_order_id;
	}

	public function getCurrentStepNumber() {
		return $this->current_step_number;
	}

	public function setCurrentStepNumber($current_step_number) {
		$this->current_step_number = $current_step_number;
	}

}

?>
