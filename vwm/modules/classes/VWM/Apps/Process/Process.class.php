<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class Process extends Model {
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
	
	/*
	 * process steps
	 * $var array of objects
	 */
	protected $process_steps = array();

	const TABLE_NAME = 'process';
	const STEP_TABLE = 'step';
	const RESOURCE_TABLE = 'resource';

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
	 *get and set steps for initialization
	 * @return type array
	 */
	
	public function getProcessSteps() {
		return $this->process_steps;
	}

	public function setProcessSteps($process_steps) {
		$this->process_steps = $process_steps;
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

	protected function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'" : "NULL";
	
		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"facility_id, name, last_update_time, work_order_id" .
				") VALUES(" .
				"{$this->db->sqltext($this->getFacilityId())}," .
				"'{$this->db->sqltext($this->getName())}'," .
				"{$lastUpdateTime}, " .
				"'{$this->db->sqltext($this->getWorkOrderId())}'" .
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
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'" : "NULL";

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"facility_id={$this->db->sqltext($this->getFacilityId())}, " .
				"name='{$this->db->sqltext($this->getName())}', " .
				"work_order_id='{$this->db->sqltext($this->getWorkOrderId())}', " .
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}
	
	public function getProcessIdByNameAndFacilityId($facilityId = NULL, $name = NULL) {
		if (is_null($facilityId)) {
			$facilityId = $this->getFacilityId();
		}
		if (is_null($name)) {
			$name = $this->getName();
		}
		
		if (is_null($facilityId)) {
			return false;
		}

		$sql = "SELECT id FROM " . self::TABLE_NAME . " " .
				"WHERE facility_id = {$this->db->sqltext($facilityId)} " .
				"AND name = '{$this->db->sqltext($name)}' LIMIT 1";

		$this->db->query($sql);
		
		
		$row = $this->db->fetch(0);
		
		if(is_null($row->id)){
			return false;
		}else{
			return $row->id;
		}
	}
	
	/**
	 * function for deleting process step
	 * @param int $processId 
	 */
	public function deleteProcessSteps($processId = NULL) {

		if (is_null($processId)) {
			$processId = $this->getId();
		}

		if (is_null($processId)) {
			return false;
		}
		
		$sql = "DELETE FROM s,r ".
				"USING ".self::STEP_TABLE." s ".
				"LEFT JOIN ".self::RESOURCE_TABLE." r ". 
				"ON s.id = r.step_id ".
				"WHERE s.process_id = {$this->db->sqltext($processId)}";
		$response = $this->db->exec($sql);
		if ($response) {
			return true;
		} else {
			return false;
		}
	}
	
}

?>
