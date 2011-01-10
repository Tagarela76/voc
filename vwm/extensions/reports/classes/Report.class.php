<?php

class Report {
	private $reportRequest;	
	
    function Report($reportRequest, $db) {
    	$this->reportRequest = $reportRequest; 
    	
    	ini_set("max_execution_time","180");  	  	    	
    	ini_set("memory_limit","70M");
    	
    	//	XML Building
    	$userID = $this->reportRequest->getUserID();
    	$xmlFileName = 'tmp/reportByUser'.$userID.'.xml';
    	$xml = new XMLBuilder($db);
    	$xml -> BuildXML($reportRequest, $xmlFileName);
    	
    	//	Report Building
    	$reportType	= $this->reportRequest->getReportType();
    	$format		= $this->reportRequest->getFormat();
    	$extraVar 	= $this->reportRequest->getExtraVar();
    	   
		$this->buildReport($xmlFileName, $reportType, $format, $extraVar);
    }
    
    private function buildReport($xmlFileName, $reportType, $format,$extraVar) {
    	switch ($format) {
    		case "pdf":
    			$pdf = new PDFBuilder($xmlFileName, $reportType);
    			break;
    			
    		case "html":    		
    			$html = new HTMLBuilder($xmlFileName, $reportType);
    			break;
    			
    		case "csv":
    			$csv = new CSVBuilder($xmlFileName, $reportType,$extraVar);    	
    			break;
    			
    		case "excel":    		
    			$xls = new XLSBuilder($xmlFileName, $reportType);
    			break;
    	}
    }    
    
    
    public function the42() {
    	echo $this->reportRequest->getReportType()."<br>";
    	echo $this->reportRequest->getCategoryType()."<br>";
    	echo $this->reportRequest->getCategoryID()."<br>";
    	echo $this->reportRequest->getFrequency()."<br>";
    	echo $this->reportRequest->getFormat()."<br>";
    	echo $this->reportRequest->getDateBegin()."<br>";
    	echo $this->reportRequest->getDateEnd()."<br>";
    	echo $this->reportRequest->getExtraVar()."<br>";
    }
}
?>