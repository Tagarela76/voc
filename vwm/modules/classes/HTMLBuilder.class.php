<?php

class HTMLBuilder {

    function HTMLBuilder($xmlFileName, $reportType) {
    	$xslFileName = $this->getXSLTemplate($reportType);    	
		$xslDoc = new DOMDocument();
		$xslDoc->load($xslFileName);
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->load($xmlFileName);
		
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xslDoc);
		
		echo $proc->transformToXML($xmlDoc);
	}
    
    private function getXSLTemplate($reportType) {
    	$xslFileName = "modules/resources/xslTemplates/";    	
    	switch ($reportType) {
    		case "chemClass":
    			$xslFileName .= "chemicalClassReport.xsl";
    			break;
    		
    		case "vocLogs":
    			$xslFileName .= "vocLogsPerRule.xsl";
    			break;
    			
    		case "productQuants":
    			$xslFileName .= "productQuantitiesReport.xsl";
    			break;
    			
    		case "mixQuantRule":
    			$xslFileName .= "mixQuantRuleReport.xsl";
    			break;
    			
    		case "toxicCompounds":
    			$xslFileName .= "toxicCompoundsReport.xsl";
    			break;
    			
    		case "exemptCoat":
    			$xslFileName .= "exemptCoatingReport.xsl";
    			break;
    			
    		case "projectCoat":   		
    			$xslFileName .= "projectCoatingReport.xsl";
    			break;
    			
    		default:
    			echo $reportType."<br>";
    	}
    	
    	return $xslFileName;
    }
}
?>