<?php

class ReportRequest {
	private $reportType;
	private $categoryType;
	private $categoryID;
	private $frequency;
	private $format;
	private $dateBegin;
	private $dateEnd;
	private $extraVar;
	private $userID;

    function ReportRequest($reportType, $categoryType, $categoryID, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $userID) {
    	$this->reportType	= $reportType;
    	$this->categoryType = $categoryType;
    	$this->categoryID	= $categoryID;
    	$this->frequency	= $frequency;
    	$this->format		= $format;
    	$this->dateBegin	= $dateBegin;
    	$this->dateEnd  	= $dateEnd;
    	$this->extraVar  	= $extraVar;
    	$this->userID  		= $userID;
    }
    
    public function getReportType() {
    	return $this->reportType;
    }
    
    public function getCategoryType() {
    	return $this->categoryType;
    }
    
    public function getCategoryID() {
    	return $this->categoryID;
    }
    
    public function getFrequency() {
    	return $this->frequency;
    }
    
    public function getFormat() {
    	return $this->format;
    }
    
    public function getDateBegin() {
    	return $this->dateBegin;    	
    }
    
    public function getDateEnd() {
    	return $this->dateEnd;    	
    }
    
    public function getExtraVar() {
    	return $this->extraVar;    	
    }
    
    public function getUserID() {
    	return $this->userID;    	
    }
}
?>