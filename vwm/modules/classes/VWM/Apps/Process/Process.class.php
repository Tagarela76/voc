<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class Process extends Model {
	/*
	 * process id 
	 * @var int
	 */

	protected $id;
	/*
	 * process facility_id
	 * @var int
	 */
	protected $facility_id;
	/*
	 * process name
	 * @var string
	 */
	protected $name;
	/*
	 * process work_order_id
	 * @var int
	 */
	protected $work_order_id;
	/*
	 * process type
	 * 
	 */
	protected $process_type;

	/*
	 * @var int
	 */
	protected $current_step_number;

	const TABLE_NAME = 'process';
	const STEP_TABLE = 'step';

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

	public function getWorkOrderId() {
		return $this->work_order_id;
	}

	public function setWorkOrderId($work_order_id) {
		$this->work_order_id = $work_order_id;
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

	public function getCurrentStepNumber() {
		return $this->current_step_number;
	}

	public function setCurrentStepNumber($current_step_number) {
		$this->current_step_number = $current_step_number;
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
	 * function for getting all Steps in process
	 */

	public function getSteps() {
		$sql = "SELECT * FROM " . self::STEP_TABLE .
				" WHERE process_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$stepsDetails = $this->db->fetch_all_array();
		$steps = array();
		foreach ($stepsDetails as $stepDetails) {
			$step = new Step($this->db);
			$step->initByArray($stepDetails);
			$steps[] = $step;
		}
		return $steps;
	}

	/**
	 * function for getting Current Step in process
	 */

	public function getCurrentStep() {
		$sql = "SELECT id FROM " . self::STEP_TABLE . " " .
				"WHERE process_id = {$this->db->sqltext($this->getId())} " .
				"AND number = {$this->db->sqltext($this->getCurrentStepNumber())} " .
				"LIMIT 1";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$step = new Step($this->db, $row->id);
		return $step;
	}

}

?>
