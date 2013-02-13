<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;

abstract class WorkOrder extends Model {

	/**
	 *
	 * @var int 
	 */
	protected $id;

	/**
	 *
	 * @var string 
	 */
	protected $number;

	/**
	 *
	 * @var string 
	 */
	protected $description;

	/**
	 *
	 * @var string 
	 */
	protected $customer_name;

	/**
	 *
	 * @var string 
	 */
	protected $facility_id;

	/**
	 *
	 * @var string 
	 */
	protected $status;
	/*
	 * @var int
	 */
	protected $process_template_id = null;
	
	public $url;

	const TB_PROCESS_INSTANCE = 'process_instance';
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getNumber() {
		return $this->number;
	}

	public function setNumber($number) {
		$this->number = $number;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getCustomer_name() {
		return $this->customer_name;
	}

	public function setCustomer_name($customer_name) {
		$this->customer_name = $customer_name;
	}

	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}

	public function getProcessTemplateID() {
		return $this->process_template_id;
	}

	public function setProcessTemplateID($processId) {
		$this->process_template_id = $processId;
	}


	/**
	 *
	 * delete WO
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_WORK_ORDER . "
				 WHERE id=" . $this->db->sqltext($this->getId());
		$this->db->query($sql);
	}

	public function getMixes() {

		$query = "SELECT * FROM " . TB_USAGE .
				" WHERE wo_id={$this->db->sqltext($this->id)}";
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$mixes = array();
		foreach ($rows as $row) {
			$mix = new \MixOptimized($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($mix, $key)) {
					$mix->$key = $value;
				}
			}
			$mixes[] = $mix;
		}
		return $mixes;
	}

	public function getProcess() {
		$process = new \VWM\Apps\Process\ProcessTemplate($this->db, $this->getProcessTemplateID());
		return $process;
	}
	
	public function getProcessInstance(){
		$sql = "SELECT id FROM ".self::TB_PROCESS_INSTANCE." ".
			   "WHERE work_order_id = {$this->db->sqltext($this->getId())} LIMIT 1";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$result = $this->db->fetch(0);
		$processInstance = new \VWM\Apps\Process\ProcessInstance($this->db, $result->id);
		
		return $processInstance;
		
	}


}

?>
