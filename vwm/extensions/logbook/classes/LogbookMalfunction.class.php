<?php

class LogbookMalfunction extends LogbookAction implements iLogbookAction {
	
	public $department_id;
	public $equipment_id;
	public $operator;
	public $reason;
	
	function LogbookMalfunction($db, $id = null) {    	
		$this->db = $db;
		$this->type = self::ACTION_MALFUNCTION;
		if (!is_null($id)) {
			$this->id = mysql_real_escape_string($id);
			$this->_load();
		}	    		    		    	
	}
	
	public function save() {
		if (!is_null($this->id)) {
			$query = "UPDATE logbook SET date = '$this->date', department_id = '$this->department_id', equipment_id = '$this->equipment_id', " .
				"description = '$this->description', operator = '$this->operator', reason = '$this->reason' WHERE id = '$this->id'";
		} else {
			$query = "INSERT INTO logbook (id, facility_id, date, department_id, equipment_id, description, operator, reason, type) " .
				"VALUES (NULL, '$this->facility_id', '$this->date', '$this->department_id', '$this->equipment_id', '$this->description'," .
				" '$this->operator', '$this->reason', '$this->type')";
		}
		$this->db->query($query);
	}
}
?>