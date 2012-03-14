<?php


class GOMInventory extends InventoryNew {
	
	private $accessory_id;
	private $accessory_name;
	
	public function __construct(db $db, Array $array = null) {
		parent::__construct($db, $array);
	}
	
	
	public function save() {
		
	}
	
	public function set_accessory_id($value) {
		try {
			$this->accessory_id = $value;
		} catch(Exception $e) {
			throw new Exception("Id cannot be empty!" . $e->getMessage());
		}
	}
	
	
	public function set_accessory_name($value) {
		try {
			$this->accessory_name = $value;
		} catch(Exception $e) {
			throw new Exception("Accessory Name cannot be empty!" . $e->getMessage());
		}
	}
	
	public function get_accessory_id() {
		return $this->accessory_id;
	}
	
	
	public function get_accessory_name() {
		return $this->accessory_name;
	}
	
	
	public function calculateUsage() {
		$sql = "SELECT sum(`usage`) totalUsage
			FROM accessory_usage 
			WHERE date BETWEEN ".  mysql_escape_string($this->period_start_date->getTimestamp())." 
				AND ".  mysql_escape_string($this->period_end_date->getTimestamp())."
			AND accessory_id = ".mysql_escape_string($this->accessory_id);
		$this->db->query($sql);
		
		$result = $this->db->fetch(0);
		$this->usage = ($result->totalUsage === null) ? 0 : $result->totalUsage;
		return $this->usage;
	}
}

?>
