<?php

class LogbookAccidentPlan extends LogbookAction implements iLogbookAction {
	
	public $link;
	public $tmp_name;
	
	function LogbookAccidentPlan($db, $id = null) {    	
		$this->db = $db;
		$this->type = self::ACTION_ACCIDENT_PLAN;
		if (!is_null($id)) {
			$this->id = mysql_real_escape_string($id);
			$this->_load();
		}	    		    		    	
	}
	
	public function save() {
		move_uploaded_file($this->tmp_name, $this->link);
		if (!is_null($this->id)) {
			$query = "UPDATE logbook SET date = '$this->date', link = '$this->link' WHERE id = '$this->id'";
		} else {
			$query = "INSERT INTO logbook (id, facility_id, date, link, type) " .
				"VALUES (NULL, '$this->facility_id', '$this->date', '$this->link', '$this->type')";
		}
		$this->db->query($query);
	}
}
?>