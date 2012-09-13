<?php

use VWM\Framework\Model;

class WorkOrder extends Model {

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

	function __construct(db $db, $workOrderId = null) {
		$this->db = $db;
		$this->modelName = 'WorkOrder';

		if (isset($workOrderId)) {
			$this->id = $workOrderId;
			$this->_load();
		}
	}

	/**
	 * add work order
	 * @return int 
	 */
	public function addWorkOrder() {

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
	public function updateWorkOrder() {

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
			$work_order_id = $this->addWorkOrder();
		} else {
			$work_order_id = $this->updateWorkOrder();
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

}

?>