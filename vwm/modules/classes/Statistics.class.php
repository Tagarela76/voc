<?php

class Statistics {

	private $db;	
	private $sessionID;
	public $iAmFine = true;	
	const POLL_COUNT = 50;

    function Statistics($db) {
    	$this->db = $db;
    	$this->sessionID = session_id();
    	
    	if ($this->sessionID === "") {
    		$this->iAmFine = false;
    	}    	    	
    }
    
    
    
    public function save() {        		
    	if ($this->areStatsSet()) {
    		//	update
    		$this->updateStats();
    	} else {
    		//	insert
    		$this->insertStats();
    	} 
    }
    
    
    
    
    public function validate() {
    	return $this->iAmFine;
    }
    
    
    
    
    public function show($fromTimestamp, $toTimestamp) {
    	//$this->db->select_db(DB_NAME);
    	
    	if ($toTimestamp < $fromTimestamp || $fromTimestamp > $toTimestamp || $fromTimestamp == $toTimestamp) {
    		return array('failure' => true);
    	}
    	
    	$step = $this->calculateStep($fromTimestamp, $toTimestamp);
    	$currentPosition = $fromTimestamp;    	    	    	
    	
    	$output = array('failure' => false, 'data'=>array());    	
    	
    	while ($currentPosition <= $toTimestamp) {
    		$next = $currentPosition + $step;
    		$query = "SELECT count(session_id) result FROM user_stats WHERE start_time BETWEEN ".$currentPosition." AND " .$next. "";
    		$this->db->query($query);
    		
    		$value = $this->db->fetch(0)->result;
    		    		
    		$output['data'][$currentPosition."000"] = $value;

    		$currentPosition += $step;
    	}
    	
    	return $output;
    }
    
    
    
    
    private function areStatsSet() {
    	//$this->db->select_db(DB_NAME);
    	
    	$query = "SELECT session_id FROM user_stats WHERE session_id = '".$this->sessionID."'";
    	$this->db->query($query);
    	
    	return $this->db->num_rows() ? true : false;     	
    }
    
    
    
    
    private function updateStats() {
    	//$this->db->select_db(DB_NAME);
    	
    	$query = "UPDATE user_stats SET last_update_time = ".time()." WHERE session_id = '".$this->sessionID."'";
    	$this->db->query($query);
    	
    	return true;     	
    }
    
    
    
    
    private function insertStats() {
    	//$this->db->select_db(DB_NAME);
    	
    	$query = "INSERT INTO user_stats (session_id, start_time, last_update_time, ip) VALUES ('".$this->sessionID."', ".time().", ".time().", '".$_SERVER['REMOTE_ADDR']."')";
    	$this->db->query($query);
    	
    	return true;     	
    }
    
    
    
    
    private function calculateStep($fromTimestamp, $toTimestamp) {
    	$length = $toTimestamp - $fromTimestamp;    	
		return floor($length/self::POLL_COUNT);
    }
}
?>