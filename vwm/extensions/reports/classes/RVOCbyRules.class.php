<?php

class RVOCbyRules extends ReportCreator implements iReportCreator {

	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;
	
	function RVOCbyRules($db, ReportRequest $reportRequest) {
		$this->db = $db;
		$this->categoryType = $reportRequest->getCategoryType();
		$this->categoryID = $reportRequest->getCategoryID();
		$this->dateBegin = $reportRequest->getDateBegin();
		$this->dateEnd = $reportRequest->getDateEnd(); 	
		$this->dateFormat = $reportRequest->getDateFormat();
	}
	
	public function buildXML($fileName) {
		$rule = new Rule($this->db);
		$rule_nr_byRegion = $rule->ruleNrMap[$rule->getRegion()];
		switch ($this->categoryType) {
			case "company":
				$company = new Company($this->db);						
				$companyDetails = $company -> getCompanyDetails($this->categoryID);
				$orgInfo = array(
					'details' => $companyDetails,
					'category' => "Company",
					'notes' => ""							
				); 
				
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);
				
				$query = "SELECT m.voc, r.$rule_nr_byRegion as rule_nr ".
					"FROM mix m, department d, rule r ".
					"WHERE d.facility_id in (".$facilityString.") ".
					"AND d.department_id = m.department_id ".
					"AND m.rule_id = r.rule_id ";
				break;
			case "facility":
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);
				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Facility",
					'notes' => ""
				); 
				
				$query="SELECT m.voc, r.$rule_nr_byRegion as rule_nr ".
					"FROM mix m, department d, rule r ".
					"WHERE d.facility_id = ".$this->categoryID." ".
					"AND d.department_id = m.department_id ".
					"AND m.rule_id = r.rule_id ";
				break;
			case "department":
				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($this->categoryID);
				
				$facility = new Facility($this->db);
				$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Department",
					'name' => $departmentDetails['name'],
					'notes' => ""
				); 
				$query="SELECT m.voc, r.$rule_nr_byRegion as rule_nr ".
					"FROM mix m, rule r ".
					"WHERE m.department_id = ".$this->categoryID." ".
					"AND m.rule_id = r.rule_id ";
				break;
		}

		$ruleQuery = "SELECT r.rule_id, r.$rule_nr_byRegion as rule_nr ".
			"FROM rule r";
		
		$voc_arr = $this->group($query, $ruleQuery, $this->dateBegin, $this->dateEnd);
		$DatePeriod = "From ".$this->dateBegin." To ".$this->dateEnd;
		
		$this->createXML($voc_arr, $orgInfo, $DatePeriod, $fileName);	
	}
	
	public function createXML($voc_arr, $orgInfo, $DatePeriod, $fileName){
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
			$doc->createTextNode("Summary for Each Rule Number ") 
		);
		$page->appendChild( $title );
		
		
		$categoryTag = $doc->createElement( "category" );
		$categoryTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['category']))
		);
		$page->appendChild($categoryTag);
		
		$nameTag = $doc->createElement( "name" );
		$nameTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['name']))
		);
		$page->appendChild( $nameTag );
		if ($orgInfo['category'] == "Department") {
			$nameDepartmentTag = $doc->createElement( "departmentName" );
			$nameDepartmentTag->appendChild(
				$doc->createTextNode( html_entity_decode ($orgInfo['name']))
			);
			$page->appendChild( $nameDepartmentTag );
		}
		
		$adressTag = $doc->createElement( "address" );
		$adressTag->appendChild( 
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['address']))
		);
		$page->appendChild( $adressTag );
		
		$cityStateZipTag = $doc->createElement( "cityStateZip" );
		$cityStateZipTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['city'].", ".$orgInfo['details']['state'].
				", ".$orgInfo['details']['zip']))
		);
		$page->appendChild( $cityStateZipTag );
		
		$countyTag = $doc->createElement( "county" );
		$countyTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['county']))
		);
		$page->appendChild( $countyTag );
		
		$phoneTag = $doc->createElement( "phone" );
		$phoneTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['phone']))
		);
		$page->appendChild( $phoneTag );
		
		$faxTag = $doc->createElement( "fax" );
		$faxTag->appendChild(
			$doc->CreateTextNode( html_entity_decode ($orgInfo['details']['fax']))
		);
		$page->appendChild( $faxTag );
		
		if ($orgInfo['category'] != "Company") {
			$facilityIdTag = $doc->createElement( "facilityID" );
			$facilityIdTag->appendChild(
				$doc->CreateTextNode( html_entity_decode ($orgInfo['details']['facility_id']))
			);
			$page->appendChild($facilityIdTag);
		}
		
		$notesTag = $doc->createElement( "notes" );
		$notesTag->appendChild(
			$doc->CreateTextNode( html_entity_decode ($orgInfo['notes']))
		);
		$page->appendChild($notesTag);
				
		$timePeriodTag = $doc->createElement( "period");
		$timePeriodTag->appendChild(
			$doc->createTextNode($DatePeriod)
		);
		$page->appendChild($timePeriodTag);
		
		$tableTag = $doc->createElement( "table" );		
		$page->appendChild( $tableTag );		

		
		//by rule
		foreach ($voc_arr as $vocByRule) {
			$ruleTag = $doc->createElement( "rule" );		
			$tableTag->appendChild( $ruleTag );
			
			$ruleNameTag = $doc->createAttribute( "name" );
			$ruleNameTag->appendChild(
				$doc->createTextNode( html_entity_decode ($vocByRule['rule']))
			);
			$ruleTag->appendChild( $ruleNameTag );
			
			//by year!
			foreach($vocByRule['data'] as $vocByYear) {
				$yearTag = $doc->createElement( "year" );
				$ruleTag->appendChild( $yearTag );
				$yearName = $doc->createAttribute( "name" );
				$yearName->appendChild(
					$doc->createTextNode( html_entity_decode ($vocByYear['year']))
				);
				$yearTag->appendChild( $yearName );
				//final:month&voc
				foreach ($vocByYear['data'] as $vocByMonth) {
					$infoTag = $doc->createElement( "info" );
					$yearTag->appendChild( $infoTag );
					$monthTag = $doc->createAttribute( "month" );
					$monthTag->appendChild(
						$doc->createTextNode( html_entity_decode ($vocByMonth['month']))
					);
					$infoTag->appendChild( $monthTag );
					$vocTag = $doc->createAttribute( "voc" );
					$vocTag->appendChild(
						$doc->createTextNode($vocByMonth['voc'])
					);
					$infoTag->appendChild( $vocTag );
				}
				$totalTag = $doc->createElement( "total" );
				$totalTag->appendChild(
					$doc->createTextNode($vocByYear['total'])
				);
				$yearTag->appendChild( $totalTag );
			}
		}
		
		$doc->save($fileName);
	}
	
	private function group($query, $ruleQuery, $dateBegin, $dateEnd) {
		$this->db->query($ruleQuery);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);	
				$rule[$i] = $data->rule_id;
				$rule_nr[$i] = $data->rule_nr;
			}	
		}
		$ruleCount = count($rule);	
		for ($i = 0; $i<$ruleCount; $i++) {
			/*
			 * $tmpYear, $tmpMonth, $tmpDay - values of year, month and day of current time period for temporary query
			 * it need for generating $tmpDate and $tmpDateEnd
			 * $endYear, $endMonth - values of year and month of the end date for query
			 */
			$totalByRule = 0;

			$dateBeginObj = DateTime::createFromFormat($this->dateFormat, $dateBegin);
			$dateEndObj = DateTime::createFromFormat($this->dateFormat, $dateEnd);
						
			$tmpYear = $dateBeginObj->format('Y');			
			$tmpMonth = $dateBeginObj->format('m');
			$tmpDay = 1;
			
			//$endYear = substr(date("Y-m-d", strtotime($dateEnd)), 0, 4);
			$endYear = $dateEndObj->format('Y');
			$endMonth = $dateEndObj->format('m');
			$total = 0;
			$tmpResults = array();
			$results = array();
			
			while ((((int)$tmpYear == (int)$endYear)&&((int)$tmpMonth <= (int)$endMonth))||
					( (int)$tmpYear<(int)$endYear))	{
				if (((int)$tmpMonth == (int)$endMonth)&&((int)$tmpYear == (int)$endYear)) {
					$tmpDateEndObj = $dateEndObj;
				} else {
					if ( $tmpMonth==12 ) {
						$tmpYear +=1;
						$tmpMonth = 1;
					} else {
						$tmpMonth += 1; 
					}
					$tmpDateEndObj = new DateTime(date('Y-m-d',mktime(0, 0, 0, $tmpMonth, $tmpDay, $tmpYear)));
				}

				//$tmpQuery = $query."AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDate)). "','%Y-%m-%d') " .
				//	"AND DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDateEnd)). "','%Y-%m-%d') ";
				$tmpQuery = $query."AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$tmpDateEndObj->getTimestamp()." ";
				$tmpQuery .= "AND m.rule_id = ".$rule[$i]." ";	

				$this->db->query($tmpQuery);

				$result = array();
				if ($this->db->num_rows()) {
					$VOCresult = 0;

					for ($j=0; $j<$this->db->num_rows(); $j++) {
						$data = $this->db->fetch($j);	
						$VOCresult += $data->voc;			
					}
					$total += $VOCresult; 

					$result = array(
						//'month' => substr(date("Y-M-d", strtotime($tmpDate)), 5, 3),
						'month' => $dateBeginObj->format('M'),
						'voc' => $VOCresult
					);		
				} else {
					$result = array(
					    //'month' => substr(date("Y-M-d", strtotime($tmpDate)), 5, 3),
					    'month' => $dateBeginObj->format('M'),
						'voc' => 0
					);
				}
				//if ((int)substr(date("Y-m-d", strtotime($tmpDate)), 0, 4) == (int)$tmpYear) {
				if ((int)$dateBeginObj->format('Y') == (int)$tmpYear) {
						$tmpResults[] = $result;
					} else {
						$tmpResults[] = $result;
						$results1 = array(
							//'year' => substr(date("Y-m-d", strtotime($tmpDate)), 0, 4),
							'year' => $dateBeginObj->format('Y'),
							'total' => $total,
							'data' => $tmpResults
						);
						$results [] = $results1;
						$tmpResults = array();
						$totalByRule += $total;
						$total = 0;
					}
				//	TODO:			
				$dateBeginObj = $tmpDateEndObj;
				if ($dateBeginObj == DateTime::createFromFormat($this->dateFormat, $dateEnd)) {
					break;
				}
			}				
			if (count($tmpResults)!=0) {
				$results[]= array(
					//'year' => substr(date("Y-m-d", strtotime($tmpDate)), 0, 4),
					'year' => $dateBeginObj->format('Y'),
					'total' => $total,
					'data' => $tmpResults
				);
				$totalByRule += $total;	
				$total=0;					
			}
			if ($totalByRule != 0) {
				$resultsByRules[] = array(
					'rule' => $rule_nr[$i],
					'data' => $results 
				);
			}
		}

		return $resultsByRules;		
	}
}
?>