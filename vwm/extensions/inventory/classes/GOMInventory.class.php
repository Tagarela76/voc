<?php


class GOMInventory extends InventoryNew {
	
	private $accessory_id;
	private $accessory_name;
	
	public function __construct(db $db, Array $array = null) {
		parent::__construct($db, $array);
	}
	
	
	public function save() {
		if($this->id) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
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
	
	
	public function loadByAccessoryID() {
		$sql = "SELECT * FROM product2inventory WHERE accessory_id = ".mysql_escape_string($this->accessory_id);
		$this->db->query($sql);
		
		if ($this->db->num_rows() == 0) {
			return false;
		}
		
		//	get only the first row
		$row = $this->db->fetch(0);
		
		$this->id = $row->inventory_id;
		$this->accessory_id = $row->accessory_id;
		$this->facility_id = $row->facility_id;
		$this->in_stock = $row->in_stock;
		$this->limit = $row->inventory_limit;
		$this->amount = $row->amount;
		
		return true;
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
	
	private function _insert() {
		$sql = "INSERT INTO `product2inventory` (`accessory_id`, `facility_id`, `in_stock`, `inventory_limit`, `amount`) VALUES (" .
				mysql_escape_string($this->accessory_id).", ".  
				mysql_escape_string($this->facility_id).", ".  
				mysql_escape_string($this->in_stock).", ".  
				mysql_escape_string($this->limit).", ".  
				mysql_escape_string($this->amount).")";		
		$this->db->exec($sql);	
		return true;
	}
	
	private function _update() {		
		$sql = "UPDATE `product2inventory` " .
				"SET `in_stock` = ".mysql_escape_string($this->in_stock).", " .
				"`inventory_limit` = ".mysql_escape_string($this->limit).", " .
				"`amount` = ".mysql_escape_string($this->amount)." " .
				"WHERE inventory_id = ".mysql_escape_string($this->id);
		$this->db->exec($sql);		
		
		return true;
	}
}

?>
