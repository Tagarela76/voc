<?php

class Date {

	private $timeStamp;
		
    function Date($timeStamp) {
    	$this->timeStamp = $timeStamp;
    }
    
    public function setTimeStamp($timeStamp) {
    	$this->timeStamp = $timeStamp;
    }
    
    public function getTimeStamp() {
    	return $this->timeStamp;
    }
    
    public function isBiggerThan($date) {
    	return $this->timeStamp > $date->timeStamp;
    }
    
}
?>