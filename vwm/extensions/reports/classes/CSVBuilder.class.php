<?php

class CSVBuilder {
	
	var $xls = FALSE;
	var $csvFileName = 'tmp_report.csv';

    function CSVBuilder($xmlFileName, $reportType, $extraVar, $xls) {
    	if ($xls) {
    		$this->xls = $xls;
    	}    	
    	
    	$xslFileName = $this->getXSLTemplate($reportType);
    	
		$xslDoc = new DOMDocument();
		$xslDoc->load($xslFileName);

		$xmlDoc = new DOMDocument();
		$xmlDoc->load($xmlFileName);
	
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);		
		$proc->setParameter('', 's', $extraVar['commaSeparator']);
		$proc->setParameter('', 'd', $extraVar['textDelimiter']);
				
		if ($this->xls) {			
			$handle = fopen($this->csvFileName,'w');
			if (!fwrite($handle,$proc->transformToXML($xmlDoc))) {
				echo "Can't write in ($filename)";
        		exit;
			};					
			fclose($handle);					
		} else {			
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename="report.csv"');		
			echo $proc->transformToXML($xmlDoc);	
		}		
    	
    }
    
    public function getCsvFileName() {
    	return $this->csvFileName;
    }
    
    private function getXSLTemplate($reportType) {
    	$xslFileName = "modules/resources/xslTemplates/";
    	
    	switch ($reportType) {
    		case "chemClass":
    			$xslFileName .= "csvChemicalClassReport.xsl";
    			break;
    			
    		case "vocLogs":
    			$xslFileName .= "csvVocLogsPerRule.xsl";
    			break;
    			
    		case "productQuants":
    			$xslFileName .= "csvProductQuantitiesReport.xsl";
    			break;
    			
    		case "mixQuantRule":
    			$xslFileName .= "csvMixQuantRuleReport.xsl";
    			break;
    			
    		case "toxicCompounds":
    			$xslFileName .= "csvToxicCompoundsReport.xsl";
    			break;
    			
    		case "exemptCoat":
    			$xslFileName .= "csvExemptCoatingReport.xsl";
    			break;
    			
    		case "projectCoat":
    			$xslFileName .= "csvProjectCoatingReport.xsl";
    			break;
    	}
    	
    	return $xslFileName;
    }
}
?>