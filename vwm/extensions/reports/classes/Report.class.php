<?php

class Report {
	private $reportRequest;	
	
    function Report($reportRequest, $db) {
    	$this->reportRequest = $reportRequest; 
    //	$debug = new Debug();
    	
   // 	$debug->printMicrotime(__LINE__,__FILE__);
    	//ini_set("max_execution_time","180");  	  	    	
    	//ini_set("memory_limit","70M");
   // 	$debug->printMicrotime(__LINE__,__FILE__);
    	//	XML Building
    	$userID = $this->reportRequest->getUserID();
    //	$debug->printMicrotime(__LINE__,__FILE__);
    	$xmlFileName = 'tmp/reportByUser'.$userID.'.xml';
    	$xml = new XMLBuilder($db);
   // 	$debug->printMicrotime(__LINE__,__FILE__);
    	$xml->BuildXML($reportRequest, $xmlFileName);
    	//var_dump($xml);
    	//exit;
  //  	$debug->printMicrotime(__LINE__,__FILE__);
    	
    	//	Report Building
    	$reportType	= $this->reportRequest->getReportType();
    	$format		= $this->reportRequest->getFormat();
    	$extraVar 	= $this->reportRequest->getExtraVar();
   // 	  $debug->printMicrotime(__LINE__,__FILE__);
		$this->buildReport($xmlFileName, $reportType, $format, $extraVar);
	//	$debug->printMicrotime(__LINE__,__FILE__);
    }
    
    private function buildReport($xmlFileName, $reportType, $format,$extraVar) {
    	switch ($format) {
    		case "pdf":
    			$pdf = new PDFBuilder($xmlFileName, $reportType, $extraVar);
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