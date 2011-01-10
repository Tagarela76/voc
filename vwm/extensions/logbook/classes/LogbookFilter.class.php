<?php

class LogbookFilter extends LogbookAction implements iLogbookAction {
	
	public $department_id;
	public $equipment_id;
	public $installed;
	public $removed;
	public $filter_type;
	public $filter_size;
	public $action; //removed or installed

	function LogbookFilter($db, $id = null) {    	
		$this->db = $db;
		$this->type = self::ACTION_FILTER;
		if (!is_null($id)) {
			$this->id = mysql_real_escape_string($id);
			$this->_load();
			$this->installed = ($this->action == 'installed')?true:false;
			$this->removed = ($this->action == 'removed')?true:false;
		}	    		    		    	
	}
	
	public function save() {
		if (!is_null($this->id)) {
			$query = "UPDATE logbook SET date = '$this->date', department_id = '$this->department_id', equipment_id = '$this->equipment_id', " .
				"action = '".(($this->installed)?'installed':'removed')."', filter_type = '$this->filter_type', filter_size = '$this->filter_size' WHERE id = '$this->id'";
		} else {
			$query = "INSERT INTO logbook (id, facility_id, date, department_id, equipment_id, action, filter_type, filter_size, type) " .
				"VALUES (NULL, '$this->facility_id', '$this->date', '$this->department_id', '$this->equipment_id', '".(($this->installed)?'installed':'removed')."', " .
				" '$this->filter_type', '$this->filter_size', '$this->type')";
		}
		$this->db->query($query);
	}
}
?>