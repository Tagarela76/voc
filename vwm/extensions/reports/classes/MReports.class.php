<?php

class MReports {

    function MReports() {
    	$this->isSetCurrentList = false;
    	define ('TB_REPORT', 'report');
    	define ('TB_REPORT2COMPANY', 'report2company');
    }
    
    /**
     * function makeXml($params) - build xml for class XMLBuilder
     * @param array $params - $db, $reportRequest, $fileName
     */
    public function makeXml($params) {
    	extract($params);
    	$reportType = $reportRequest->getReportType(); // if we get there it mean that we already cheack that report type exist for company
    	//all ReportCreators files have names in format "R".$reportType.".class.php"
    	$reportClassName = "R".$reportType;
    	if (class_exists($reportClassName)) {
    		$reportCreator = new $reportClassName($db,$reportRequest);
    		$reportCreator->buildXML($fileName);
    	} else {
    		throw new Exception('Unknown report type!');
    	}
    }
    
    /**
     * function prepareSendReport($params) - prepare params for smarty 
     * @param array $params - $db, $reportType, $companyID, $request
     * @return array params prepared for smarty
     */
    public function prepareSendReport($params) {
    	extract($params);
    	//at first we should check if exist this reportType for company:
    	$reportsList = $this->getAvailableReportsList($db,$companyID);
    	if (isset($reportsList[$reportType])) {
    		//ok! company has this report
    		$result["reportName"] = $reportsList[$reportType];
    	} else {
    		throw new Exception ('deny'); 
    	}
    	
	    // getting rule list				
	    $rule = new Rule($db);				
	    $rulesList = $rule->getRuleListFromMix($request['category'], $request['id']);
	    $result["rules"] = $rulesList;
	    $result["subReport"] = $reportType;
	    $result["tpl"] = $this->getInputTPLfileName($reportType);
	    
	    //getting month list for select tag
	    $today = date('d-m-Y');
	    $month = substr($today,3,2);
	    $year = substr($today,-4);
	    $y = array($year-2,$year-1,$year);					
	    for ($i=2;$i>=0;$i--){
		    if ( $i != 2 ) {
			    $month = 12;
		    }
		    for ($m=$month;$m>=1;$m--) {
			    $monthList['text'] = $m."/".substr($y[$i],-2);					
			    $monthList['value'] = $m."/01/".$y[$i];
			    $monthesList[] = $monthList;							
		    }
	    }	
	    $result["monthes"] = $monthesList;															
	    
	    //getting supplier list for projectCoat report				
	    if ($reportType == "projectCoat") {
		    $supplierObj = new Supplier($db);
		    $supplierList = $supplierObj->getSupplierList();					
		    $result["supplierList"] = $supplierList;
	    }
	    
	    return $result;
    }
    
    /**
     * function prepareSendSubReport($params) - filter get vars and create report createReportForm 
     * @param array $params - $db, $xnyo, $request, $companyID
     */
    public function prepareSendSubReport($params) {
    	extract($params);
    	$reportType = $request['reportType'];
    	
    	//at first we should check if exist this reportType for company:
    	$reportsList = $this->getAvailableReportsList($db,$companyID);
    	if (isset($reportsList[$reportType])) {
    		//ok! company has this report
    		$result["reportName"] = $reportsList[$reportType];
    	} else {
    		throw new Exception ('deny'); 
    	}
    	
    	$standartInputTPL = 'reports/design/standartInput.tpl';// file name of standart input tpl
    	$currentTpl = $this->getInputTPLfileName($reportType);
    	if ($currentTpl == $standartInputTPL) {
    		$categoryType = $request['categoryLevel'];
	    	$id = $request['id'];				
	    	$format = $request['format'];	
	    	
	    	$xnyo->filter_get_var("date_begin","text");
	    	$xnyo->filter_get_var("date_end","text");
	    	$dateBegin = $_GET['date_begin'];
	    	$dateEnd = $_GET['date_end'];
	    	
	    	if ($format == "csv") {
		    	$xnyo->filter_get_var("commaSeparator","text");
		    	$xnyo->filter_get_var("textDelimiter","text");
		    	$extraVar['commaSeparator'] = $_REQUEST['commaSeparator'];
		    	$extraVar['textDelimiter'] = $_REQUEST['textDelimiter'];
		    	if (strstr($extraVar['commaSeparator'],"\\")) {
			    	$extraVar['commaSeparator'] = substr(strstr($extraVar['commaSeparator'],"\\"),1); 
		    	}
		    	if (strstr($extraVar['textDelimiter'],"\\")) {								
			    	$extraVar['textDelimiter'] = str_replace("\\","",$extraVar['textDelimiter']); 
		    	}
	    	}
	    	$frequency = null;
	    	$reportRequest = new ReportRequest($reportType, $categoryType, $id, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $_SESSION['user_id']);
    	} else {
    		//for reports with specific data in input report request gets in class
    		$reportClassName = "R".$reportType;
	    	if (class_exists($reportClassName)) {
	    		$reportCreator = new $reportClassName($db);
	    		$reportRequest = $reportCreator->getReportRequestByGetVars($xnyo);
	    	}
    	}
    	$result = new Report($reportRequest,$db);
    	return $result;
    }
    
    /**
     * function prepareDoAdmin($params)
     * @param array params - $db, $save (if $save==true $setCheckboxes )
     * @return array for smarty
     */
    public function prepareDoAdmin($params) 
    {
    	extract($params);
    	    	
    	$modSystem = new ModuleSystem($db);
    	$reportList=$this->getAllReports($db);
		$coms=$modSystem->getCompaniesWhereIsModule('reports');		
		$defaultReportsList=array();
				
		
		foreach($coms as $com)
		{			
			$defaultReportsList[$com->company_id]=$this->getCheckReportsInCompany($db,$com->company_id);
		}	
		if ($save==true)
    	{
    		foreach($coms as $com)
    		{
    			foreach ($reportList  as $rep)
    			{
    				$company_id=$com->company_id;
    				$report_id=$rep->report_id;
    				$status=0;
					foreach ($setCheckboxes as $value)
					{
						if ($value==="chbox_".$company_id."_".$report_id)
							$status=1;										
					}	
    				if ($defaultReportsList[$company_id][$report_id]!==$status)
    				{    					
    					$this->saveIntoReport2Company($db, $company_id, $report_id, $status);
    					$defaultReportsList[$company_id][$report_id]=$status;		
    				}
    			}
    		}				
    	}    	
		
    	$result = array(
    		'companyList'		=> $coms,
    		'reportList'		=> $reportList,
    		'defaultReportsList'=> $defaultReportsList,
    		'tpl'   			=> 'reports/design/reportsAdmin.tpl'
    	);
    	return $result;
    }
    
    public function getXML2PDFfileName($reportType) {
    	if (file_exists("extensions/reports/xml2pdf/".$reportType."2pdf.php")) {
    		return "extensions/reports/xml2pdf/".$reportType."2pdf.php";
    	} else {
    		throw new Exception('Unknown report type!');
    	}
    }
    
    public function getInputTPLfileName($reportType) {
    	if (file_exists('extensions/reports/design/'.$reportType.'Input.tpl')) {
    		return 'reports/design/'.$reportType.'Input.tpl';
    	} else {
    		return 'reports/design/standartInput.tpl';
    	}
    }
    
    public function getReportName($db, $reportType) {
    	$query = "SELECT name FROM ".TB_REPORT." WHERE type = '$reportType' LIMIT 1";
    	$this->db->query($query);
    	return $this->db->fetch(0)->name;
    }
    
    /**
     * function getAvailableReportsList($db,$companyID) - select from db information about choosen reports for company
     * @param $db
     * @param $companyID
     * @return $currentReportList - list of choosen
     */
    public function getAvailableReportsList($db, $companyID) {
    	$query = "SELECT r.type, r.name FROM ".TB_REPORT2COMPANY." rc, ".TB_REPORT." r WHERE rc.company_id = '$companyID' AND rc.report_id = r.report_id AND rc.on_off = 1 ";
    	$db->query($query);
    	$data = $db->fetch_all();
    	$currentReportList = array();
    	foreach ($data as $record) {
    		$currentReportList[$record->type] = $record->name;
    	}
    	return $currentReportList;
    }
    
    private function getCheckReportsInCompany($db, $companyID)
    {
    	$query = "SELECT rc.on_off ,rc.report_id FROM ".TB_REPORT2COMPANY." rc WHERE rc.company_id = $companyID ";
    	$db->query($query);
    	$repArray=array();
    	foreach($db->fetch_all() as $report)
    	{
    		$repArray[$report->report_id]=$report->on_off;
    	}	
    	return $repArray;
    }
    
    private function getAllReports($db)
    {
    	$query = "SELECT * FROM ".TB_REPORT;
    	$db->query($query);
    	$data=$db->fetch_all();
    	return $data;
    }   
    
    private function saveIntoReport2Company($db, $company_id, $report_id, $on_off)
    {
    	$query = "SELECT * FROM ".TB_REPORT2COMPANY." WHERE company_id=".$company_id." AND report_id=".$report_id;
    	$db->query($query);  
    		
    	if($db->num_rows()>0) 
    	{   	
    		$query = "UPDATE ".TB_REPORT2COMPANY." SET on_off=".$on_off." WHERE company_id=".$company_id." AND report_id=".$report_id;
    	}
    	else
    	{       		
    		$query = "INSERT INTO ".TB_REPORT2COMPANY."(report_id, company_id, on_off) VALUES (".$report_id.",".$company_id.",".$on_off.")";
    	}
    	$db->query($query);
    }
    
}
?>