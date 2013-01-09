<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Process extends Model {
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

	

	const TABLE_NAME = 'process';

	
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
	
	

}

?>
