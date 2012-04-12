<?php

class CType {
	protected $db;
	
	protected $mainFormat;
	protected $format;
	
	private $errors;
	
	protected $outputFormat;//description of customers format

    function CType($db = null) {
    	if (!is_null($db)) {
	    	$this->db = $db;
    	}
    	
    }
    
        
    public function getErrorsForConvertedValue($value) {
    	return $this->errors[$value];
    }
    
    public function getOutputFormat() {
    	return $this->outputFormat;
    }
}
?>