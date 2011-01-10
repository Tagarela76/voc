<?php

interface iReportCreator {
	/**
	 * build xml for selected report
	 */
	function buildXML($fileName);
}

class ReportCreator {
	
	protected $db;
	
	protected $categoryType;
	protected $categoryID;
	
    function ReportCreator() {
    }
    
    protected function getPeriodByFrequency($frequency, $dateBegin) { // tmp function //denis 20 May 2009
		
		//dateEnd is ignored //denis 20 May 2009
		
		if ($frequency == 'monthly') {
			$month = substr($dateBegin,0,2);
			$year = substr($dateBegin,6,4);
			settype($month, "integer");
			settype($year, "integer"); 												
			$dateBegin = date("d-m-Y", mktime(0, 0, 0, $month, 1, $year ));
			$dateEnd = date("d-m-Y", mktime(0, 0, 0, $month + 1, 0, $year));
			$label =  sprintf("%02s",$month) . "/" . sprintf("%02s",$year);										
		} else { // then annual
			$month = substr($dateBegin,0,2);
			$year = substr($dateBegin,6,4);
			settype($month, "integer");
			settype($year, "integer"); 												
			$dateBegin = date("d-m-Y", mktime(0, 0, 0, 1, 1, $year ));
			$dateEnd = date("d-m-Y", mktime(0, 0, 0, 12, 31, $year));
			$label = sprintf("%02s",$year);						
		}
		
		$period['dateBegin'] = $dateBegin;
		$period['dateEnd'] = $dateEnd;
		$period['label'] = $label;
		
		return $period; 
	}
}
?>