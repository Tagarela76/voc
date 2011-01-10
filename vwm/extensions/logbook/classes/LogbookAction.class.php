<?php

interface iLogbookAction {
	/**
	 * load logbook item if its id was set in constructor
	 */
	function _load();
	/**
	 * save new or edited logbook item
	 */
	function save();
	/**
	 * delete logbook item that was initialized or by $id
	 * @param $id = null
	 */
	function delete($id = null);
}

class LogbookAction {
	
	const ACTION_INSPECTION = "Inspection";
	const ACTION_SAMPLING = "Sampling";
	const ACTION_ACCIDENT_PLAN = "AccidentPlan";
	const ACTION_MALFUNCTION = "Malfunction";
	const ACTION_FILTER = "Filter";  
	
	public $date;
	public $facility_id;
	public $id;
	public $type;
	
	protected $db;

    function LogbookAction() {
    }
    
    public function _load() {
    	if ($this->id === null) {
    		return false;
    	}
    	$query = "SELECT * FROM logbook WHERE id = '$this->id' LIMIT 1";
    		$this->db->query($query);
    		if ($this->db->num_rows() == 0) return false;
    		
    		$logbookRecord = $this->db->fetch(0);
    		foreach ($logbookRecord as $property =>$value) {
	    		if (property_exists($this,$property)) {
		    		$this->$property = $logbookRecord->$property;
	    		}
    		}
    }
    
    public function delete($id = null) {
    	if ($id != null) {
    		$this->id = $id;
    	}
    	$query = "DELETE FROM logbook WHERE id = $this->id LIMIT 1";
    	$this->db->query($query);
    }
}
?>