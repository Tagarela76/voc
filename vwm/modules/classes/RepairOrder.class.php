<?php

use VWM\Framework\Model;

class RepairOrder extends Model {

	/**
	 *
	 * @var int 
	 */
	public $id;
	
	/**
	 *
	 * @var string 
	 */
	public $number;
	
	/**
	 *
	 * @var string 
	 */
	public $description;
	
	/**
	 *
	 * @var string 
	 */
	public $customer_name;

	/**
	 *
	 * @var string 
	 */
	public $facility_id;

	/**
	 *
	 * @var string 
	 */
	public $status;
	
	/**
	 *
	 * @var string 
	 */
	public $vin;
	
	public $url;
	
	public $process_id = NULL;

	const TB_MIX = 'mix';
	const TB_STEP = 'step';
	const TB_PROCESS_INSTANCE = 'process_instance';

	public function __construct(db $db, $repairOrderId = null) {
		$this->db = $db;
		$this->modelName = 'repairOrder';

		if (isset($repairOrderId)) {
			$this->id = $repairOrderId;
			$this->_load();
		}
	}


	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getProcessId() {
		return $this->process_id;
	}

	public function setProcessId($process_id) {
		$this->process_id = $process_id;
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

	public function getCustomerName() {
		return $this->customer_name;
	}

	public function setCustomerName($customer_name) {
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

	public function getVin() {
		return $this->vin;
	}

	public function setVin($vin) {
		$this->vin = $vin;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	
	/**
	 * add work order
	 * @return int 
	 */
	public function addRepairOrder() {

		$query = "INSERT INTO " . TB_WORK_ORDER . "(number, description, customer_name, facility_id, status, vin) 
				VALUES ( 
				'" . $this->db->sqltext($this->number) . "'
				, '" . $this->db->sqltext($this->description) . "'
				, '" . $this->db->sqltext($this->customer_name) . "'
				, '" . $this->db->sqltext($this->facility_id) . "'	
				, '" . $this->db->sqltext($this->status) . "'	
				, '" . $this->db->sqltext($this->vin) . "'		
				)";
		$this->db->query($query); 
		$work_order_id = $this->db->getLastInsertedID();
		$this->id = $work_order_id;
		return $work_order_id;
	}

	/**
	 * update work order
	 * @return int 
	 */
	public function updateRepairOrder() {

		$query = "UPDATE " . TB_WORK_ORDER . "
					set number='" . $this->db->sqltext($this->number) . "',
						description='" . $this->db->sqltext($this->description) . "',
						customer_name='" . $this->db->sqltext($this->customer_name) . "',	
						facility_id='" . $this->db->sqltext($this->facility_id) . "',
						status='" . $this->db->sqltext($this->status) . "', 	
						vin='" . $this->db->sqltext($this->vin) . "'		
					WHERE id= " . $this->db->sqltext($this->id);
		$this->db->query($query);

		return $this->id;
	}

	/**
	 *
	 * delete work order
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_WORK_ORDER . "
				 WHERE id=" . $this->db->sqltext($this->id);
		$this->db->query($sql);
	}

	/**
	 * insert or update work order
	 * @return int 
	 */
	public function save() {

		if (!isset($this->id)) {
			$work_order_id = $this->addRepairOrder();
		} else {
			$work_order_id = $this->updateRepairOrder();
		}
		return $work_order_id;
	}

	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM ".TB_WORK_ORDER." ".
				"WHERE id={$this->db->sqltext($this->id)} " . 
				"LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch(0);

		foreach ($rows as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
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
			$mix = new MixOptimized($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($mix, $key)) {
					$mix->$key = $value;
				}
			}
			$mixes[] = $mix;
		}
		return $mixes;
	}
	
	public function searchAutocomplete($occurrence, $facilityId) {

		$query = "SELECT number, description, customer_name, vin, LOCATE('{$this->db->sqltext($occurrence)}', number) occurrence1, LOCATE('{$this->db->sqltext($occurrence)}', description) occurrence2,
				LOCATE('{$this->db->sqltext($occurrence)}', customer_name) occurrence3, LOCATE('{$this->db->sqltext($occurrence)}', vin) occurrence4  " .
				"FROM " . TB_WORK_ORDER . 
				" WHERE facility_id={$this->db->sqltext($facilityId)} AND LOCATE('{$this->db->sqltext($occurrence)}', number)>0 OR 
				 LOCATE('{$this->db->sqltext($occurrence)}', description)>0 OR 
				 LOCATE('{$this->db->sqltext($occurrence)}', customer_name)>0 OR 
				 LOCATE('{$this->db->sqltext($occurrence)}', vin)>0 
				 LIMIT ".AUTOCOMPLETE_LIMIT;
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$repairOrders = $this->db->fetch_all_array();

			foreach ($repairOrders as $repairOrder) {
				if($repairOrder['occurrence1'] != 0) {
					$result = array (
						"repairOrder"		=>	$repairOrder['number'],
						"occurrence"	=>	$repairOrder['occurrence1']
					);
					$results[] = $result;
				} elseif ($repairOrder['occurrence2'] != 0) {
					$result = array (
						"repairOrder"		=>	$repairOrder['description'],
						"occurrence"	=>	$repairOrder['occurrence2']
					);
					$results[] = $result;
				} elseif ($repairOrder['occurrence3'] != 0) {
					$result = array (
						"repairOrder"		=>	$repairOrder['customer_name'],
						"occurrence"	=>	$repairOrder['occurrence3']
					);
					$results[] = $result;
				} elseif ($repairOrder['occurrence4'] != 0) {
					$result = array (
						"repairOrder"		=>	$repairOrder['vin'],
						"occurrence"	=>	$repairOrder['occurrence4']
					);
					$results[] = $result;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}
	
	public function getRepairOrderProcessName(){
		if(is_null($this->getProcessId())){
			return false;
		}
		
		$process = new \VWM\Apps\Process\ProcessTemplate($this->db, $this->process_id);
		return $process->getName();
		
	}
	
	/**
	 *function for getting step ids for Repair Order  which have been already used
	 */
	/*public function getUsedStepIds(){
		
		if (is_null($this->getProcessId())){
			return false;
		}
		
		$ids = array();
		$query = "SELECT s.id FROM ".  self::TB_STEP." s ".
				"LEFT JOIN ".self::TB_MIX." m ".
				"ON s.id=m.step_id WHERE ".
				"s.process_id = {$this->db->sqltext($this->getProcessId())} AND ".
				"m.wo_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($query);
		$result = $this->db->fetch_all_array();
		
		foreach ($result as $r){
			$ids[] = $r['id'];
		}
		return $ids;
		
	}*/
	
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
