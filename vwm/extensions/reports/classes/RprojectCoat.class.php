<?php

class RprojectCoat extends ReportCreator implements iReportCreator {

	private $frequency;
	private $rule;
	private $monthYear;
	private $data;
	
	function RprojectCoat($db, $reportRequest = null) {
		$this->db = $db;
		if (!is_null($reportRequest)) {
			$this->categoryType = $reportRequest->getCategoryType();
			$this->categoryID = $reportRequest->getCategoryID();
			$this->frequency = $reportRequest->getFrequency();
			$extraVar = $reportRequest->getExtraVar();
			$this->rule = $extraVar['rule'];
			$this->monthYear = $extraVar['monthYear'];
			$this->data = $extraVar['data'];    	
		}
	}
	
	public function getReportRequestByGetVars($companyID) {
		//at first lets get data already filtered
		$categoryType = $_REQUEST['categoryLevel'];
		$id = $_REQUEST['id'];
		$reportType = $_REQUEST['reportType'];				
		$format = $_REQUEST['format'];
				
		//and get them too
		$frequency = $_REQUEST['frequency'];
		$extraVar['monthYear'] = $_REQUEST['monthYearSelect'];
		$extraVar['rule'] = $_REQUEST['logs'];						
		
		$data['clientName'] = (($_REQUEST['clientName'] == "[Client Name]") ? "" : $_REQUEST['clientName']);
		$data['clientSpecification'] = (($_REQUEST['clientSpecification'] == "[Client Specification]") ? "" : $_REQUEST['clientSpecification']);				
		$data['supplier1'] = $_REQUEST['supplier1'];
		$data['supplier2'] = $_REQUEST['supplier2'];
		$data['supplier3'] = $_REQUEST['supplier3'];
		$data['reason1'] = (($_REQUEST['reason1'] == "[Reason for Non-Availability Name#1]") ? "" : $_REQUEST['reason1']);
		$data['reason2'] = (($_REQUEST['reason2'] == "[Reason for Non-Availability Name#2]") ? "" : $_REQUEST['reason2']);
		$data['reason3'] = (($_REQUEST['reason3'] == "[Reason for Non-Availability Name#3]") ? "" : $_REQUEST['reason3']);
		$data['summary'] = (($_REQUEST['summary'] == "[Summary for compliant coating problem/failure]") ? "" : $_REQUEST['summary']);
		$extraVar['data'] = $data;
		
		//lets set extra vars in case its csv format
		if ($format == "csv") {
			$extraVar['commaSeparator'] = $_REQUEST['commaSeparator'];
			$extraVar['textDelimiter'] = $_REQUEST['textDelimiter'];
			if (strstr($extraVar['commaSeparator'],"\\")) {
				$extraVar['commaSeparator'] = substr(strstr($extraVar['commaSeparator'],"\\"),1); 
			}
			if (strstr($extraVar['textDelimiter'],"\\")) {								
				$extraVar['textDelimiter'] = str_replace("\\","",$extraVar['textDelimiter']); 
			}
		}
		
		$dateBegin = new TypeChain($_GET['date_begin'],'date',$this->db,$companyID,'company');
	    $dateEnd = new TypeChain($_GET['date_end'],'date',$this->db,$companyID,'company');
		
		//finally: lets get	reportRequest object!
		$reportRequest = new ReportRequest($reportType, $categoryType, $id, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $_SESSION['user_id']);							
		return $reportRequest;
	}	
	
	public function buildXML($fileName) {
		switch ($this->categoryType) {
			case "company":
				$company = new Company($this->db);						
				$companyDetails = $company -> getCompanyDetails($this->categoryID);
				$orgName = $companyDetails['name']; 
				break;
			case "facility":
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);
				$orgName = $facilityDetails['name']; 
				break;
			case "department":
				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($this->categoryID);
				
				$facility = new Facility($this->db);
				$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
				$orgName = $facilityDetails['name'];
				break;
		}
		
		$orgName = strtoupper($orgName);
		
		$rule = new Rule($this->db);
		$rule_nr_byRegion = $rule->ruleNrMap[$rule->getRegion()];
		
		$this->db->query("SELECT $rule_nr_byRegion as rule_nr FROM rule WHERE rule_id = ".$this->rule);						
		$data=$this->db->fetch(0);
		$rule = $data->rule_nr;		
		
		$period = $this->getPeriodByFrequency($this->frequency, $this->monthYear);														
		
		$reportData = $this->data;				
		for ($i=1;$i<=3;$i++) {													
			if (!empty($reportData['supplier'.$i])) {
				$this->db->query("SELECT * FROM supplier WHERE supplier_id = ".$reportData['supplier'.$i]);						
				$data=$this->db->fetch(0);																																			
				$supplier[$i] = array (
					'supplier'		=>	$data->supplier,
					'contactPerson'	=>	$data->contact_person,
					'phone'			=>	$data->phone																								
				);																										
			}
			
		}			
		
		$this->createXML($rule,$period,$reportData,$supplier,$orgName,$fileName);
	}
	
	public function createXML($rule,$period,$reportData,$supplier,$orgName, $fileName){
		$doc = new DOMDocument();
		$doc->formatOutput = true;     							  							  							  						
		
		$page = $doc->createElement( "page" );		
		$doc->appendChild( $page );
		
		$pageOrientation = $doc->createAttribute("orientation");
		$pageOrientation->appendChild(
			$doc->createTextNode("p")
		);
		$page->appendChild($pageOrientation);
		
		$pageTopMargin = $doc->createAttribute("topmargin");
		$pageTopMargin->appendChild(
			$doc->createTextNode("10")
		);
		$page->appendChild($pageTopMargin);
		
		$pageLeftMargin = $doc->createAttribute("leftmargin");
		$pageLeftMargin->appendChild(
			$doc->createTextNode("10")
		);
		$page->appendChild($pageLeftMargin);
		
		$pageRightMargin = $doc->createAttribute("rightmargin");
		$pageRightMargin->appendChild(
			$doc->createTextNode("10")
		);
		$page->appendChild($pageRightMargin);  							  							  										  					
		
		$meta = $doc->createElement( "meta" );
		$page->appendChild( $meta );
		
		$metaName = $doc->createAttribute("name");
		$metaName->appendChild(
			$doc->createTextNode("basefont")
		);
		$meta->appendChild($metaName);
		
		$metaValue = $doc->createAttribute("value");
		$metaValue->appendChild(
			$doc->createTextNode("times")
		);
		$meta->appendChild($metaValue);
		
		$title = $doc->createElement( "title" );
		$title->appendChild(
			$doc->createTextNode( html_entity_decode ($orgName)." PROJECT COATING REPORT")
		);
		$page->appendChild( $title );
		
		$ruleTag = $doc->createElement( "rule" );
		$ruleTag->appendChild(
			$doc->createTextNode( html_entity_decode ($rule))
		);
		$page->appendChild( $ruleTag );
		
		$monthYearTag = $doc->createElement( "monthYear" );
		$monthYearTag->appendChild(
			$doc->createTextNode( html_entity_decode ($period['label']))
		);
		$page->appendChild( $monthYearTag );			
		
		$categoriesTag = $doc->createElement( "categories" );
		$categoriesTag->appendChild(
			$doc->createTextNode("(1K/2K)")
		);
		$page->appendChild( $categoriesTag );
		
		$clientNameTag = $doc->createElement( "clientName" );
		$clientNameTag->appendChild(
			$doc->createTextNode( html_entity_decode ($reportData['clientName']))
		);
		$page->appendChild( $clientNameTag );
		
		$clientSpecTag = $doc->createElement( "clientSpecification" );
		$clientSpecTag->appendChild(
			$doc->createTextNode( html_entity_decode ($reportData['clientSpecification']))
		);
		$page->appendChild( $clientSpecTag );				
		
		$tableTag = $doc->createElement( "table" );		
		$page->appendChild( $tableTag );		
		$supplierNameTag = $doc->createElement( "supplierName" );		
		$tableTag->appendChild( $supplierNameTag );
		
		for ($i=1;$i<=3;$i++) {
			$nameTag = $doc->createElement( "name" );
			$nameTag->appendChild(
				$doc->createTextNode( html_entity_decode ($supplier[$i]['supplier']))
			);
			$supplierNameTag->appendChild( $nameTag );
		}
		
		$supplierContactTag = $doc->createElement( "supplierContact" );		
		$tableTag->appendChild( $supplierContactTag );		
		for ($i=1;$i<=3;$i++) {
			$contactTag = $doc->createElement( "contact" );
			$contactTag->appendChild(
				$doc->createTextNode( html_entity_decode ($supplier[$i]['contactPerson']))
			);
			$supplierContactTag->appendChild( $contactTag );
		}
		
		$supplierPhoneTag = $doc->createElement( "supplierPhone" );		
		$tableTag->appendChild( $supplierPhoneTag );		
		for ($i=1;$i<=3;$i++) {
			$phoneTag = $doc->createElement( "phone" );
			$phoneTag->appendChild(
				$doc->createTextNode( html_entity_decode ($supplier[$i]['phone']))
			);
			$supplierPhoneTag->appendChild( $phoneTag );
		}
		
		$supplierReasonTag = $doc->createElement( "supplierReason" );		
		$tableTag->appendChild( $supplierReasonTag );		
		for ($i=1;$i<=3;$i++) {
			$reasonTag = $doc->createElement( "reason" );
			$reasonTag->appendChild(
				$doc->createTextNode( html_entity_decode ($reportData['reason'.$i]))
			);
			$supplierReasonTag->appendChild( $reasonTag );
		}		
		
		$summaryTag = $doc->createElement( "summary" );
		$summaryTag->appendChild(
			$doc->createTextNode( html_entity_decode ($reportData['summary']))
		);
		$page->appendChild( $summaryTag );

		$doc->save($fileName);
	}
	
	private function group() {
		
	}
}
?>