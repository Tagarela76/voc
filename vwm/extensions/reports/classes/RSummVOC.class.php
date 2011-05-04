<?php

class RSummVOC extends ReportCreator implements iReportCreator {

	private $dateBegin;
	private $dateEnd;
	
	function RSummVOC($db, $reportRequest) {
		$this->db = $db;
		$this->categoryType = $reportRequest->getCategoryType();
		$this->categoryID = $reportRequest->getCategoryID();
		$this->dateBegin = $reportRequest->getDateBegin();
		$this->dateEnd = $reportRequest->getDateEnd();	
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
				
				$query = "SELECT m.mix_id, m.voc, r.$rule_nr_byRegion as rule_nr ".
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
				
				$query="SELECT m.mix_id, m.voc, r.$rule_nr_byRegion as rule_nr ".
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
				$query="SELECT m.mix_id, m.voc, r.$rule_nr_byRegion as rule_nr ".
					"FROM mix m, rule r ".
					"WHERE m.department_id = ".$this->categoryID." ".
					"AND m.rule_id = r.rule_id ";
				break;
		}

		$ruleQuery = "SELECT r.rule_id, r.$rule_nr_byRegion as rule_nr ".
			"FROM rule r";		
		
		
		
		$voc_arr= $this->group($query, $ruleQuery, $this->dateBegin, $this->dateEnd);
		$DatePeriod = "From ".$this->dateBegin." To ".$this->dateEnd;
		
		$this->createXML($voc_arr, $orgInfo, $DatePeriod, $fileName);	
	}
	
	public function createXML($voc_arr, $orgInfo, $DatePeriod, $fileName) {
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
			$doc->createTextNode("Monthly Summary Report of Total VOC Usage") 
		);
		$page->appendChild( $title );
		
		$subTitle = $doc->createElement( "subTitle" );
		$subTitle->appendChild(
			$doc->createTextNode(" including by rule numbers and exemptions ")
		);
		$page->appendChild( $subTitle );
		
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
			$doc->createTextNode($orgInfo['details']['county'])
		);
		$page->appendChild( $countyTag );
		
		$phoneTag = $doc->createElement( "phone" );
		$phoneTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['phone']))
		);
		$page->appendChild( $phoneTag );
		
		$faxTag = $doc->createElement( "fax" );
		$faxTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['details']['fax']))
		);
		$page->appendChild( $faxTag );
		
		if ($orgInfo['category'] != "Company") {
			$facilityIdTag = $doc->createElement( "facilityID" );
			$facilityIdTag->appendChild(
				$doc->createTextNode( html_entity_decode ($orgInfo['details']['facility_id']))
			);
			$page->appendChild($facilityIdTag);
		}
		
		$notesTag = $doc->createElement( "notes" );
		$notesTag->appendChild(
			$doc->createTextNode( html_entity_decode ($orgInfo['notes']))
		);
		$page->appendChild($notesTag);
		
		$timePeriodTag = $doc->createElement( "period" );
		$timePeriodTag->appendChild(
			$doc->createTextNode($DatePeriod)
		);
		$page->appendChild($timePeriodTag);
		
		$tableTag = $doc->createElement( "table" );		
		$page->appendChild( $tableTag );		
		
		//by month
		foreach ($voc_arr['data'] as $vocByMonth) {
			$monthTag = $doc->createElement( "month" );		
			$tableTag->appendChild( $monthTag );
			
			$monthNameTag = $doc->createAttribute( "name" );
			$monthNameTag->appendChild(
				$doc->createTextNode((string)$vocByMonth['month'])
			);
			$monthTag->appendChild( $monthNameTag );
			
			//by rule or exempt rule
			foreach ($vocByMonth['data'] as $vocByRule) {
				$infoTag = $doc->createElement( "info" );
				$monthTag->appendChild( $infoTag );
				if (isset($vocByRule['rule'])) {
					$ruleTag = $doc->createAttribute( "rule" );
					$ruleTag->appendChild(
						$doc->createTextNode( html_entity_decode ($vocByRule['rule']))
					);
				} else {
					$ruleTag = $doc->createAttribute( "exempt" );
					$ruleTag->appendChild(
						$doc->createTextNode( html_entity_decode ($vocByRule['exempt']))
					);						
				}
				$infoTag->appendChild( $ruleTag );
				$vocTag = $doc->createAttribute( "voc" );
				$vocTag->appendChild(
					$doc->createTextNode($vocByRule['voc'])
				);
				$infoTag->appendChild( $vocTag );
			}
			$totalTag = $doc->createElement( "total" );
			$totalTag->appendChild(
				$doc->createTextNode($vocByMonth['total'])
			);
			$monthTag->appendChild( $totalTag );
		}
		$fullTotalTag = $doc->createElement( "fullTotal" );
		$fullTotalTag->appendChild(
			$doc->createTextNode($voc_arr['total'])
		);
		$tableTag->appendChild( $fullTotalTag );		
		$doc->save($fileName);
	}
	
	private function group($query, $ruleQuery, $dateBegin, $dateEnd) {
		$emptyData [0] = array (
			'rule' => "none",
			'voc' => "none"
		);
		$emptyData [1] = array (
			'exempt' => "none",
			'voc' => "none"
		);
		$this->db->query($ruleQuery);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);	
				$rule[$i] = $data->rule_id;
				$rule_nr[$i] = $data->rule_nr;
			}	
		}	
		$exemptQuery = "SELECT exempt_rule ".
			"FROM mix ".
			"WHERE exempt_rule <> 'NULL' ".
			"GROUP BY `exempt_rule` ";
		$this->db->query($exemptQuery);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);	
				$exempt[$i] = $data->exempt_rule;
			}	
		}	
		/*
		 * $tmpYear, $tmpMonth, $tmpDay - values of year, month and day of current time period for temporary query
		 * it need for generating $tmpDate and $tmpDateEnd
		 * $endYear, $endMonth - values of year and month of the end date for query
		 */
		$tmpYear = date("Y", strtotime($dateBegin));
		$tmpMonth = date("m", strtotime($dateBegin));
		$tmpDay = 1;
		$tmpDate = date("Y-m-d", strtotime($dateBegin));
		$endYear = date("Y", strtotime($dateEnd));
		$endMonth = date("m", strtotime($dateEnd));
		$total = 0;
		$tmpResults = array();
		$results = array();
		$fullTotal = 0;
		
		//var_dump($tmpYear, $tmpMonth, $tmpDate, $endYear, $endMonth);
		//exit;
		
		while ((((int)$tmpYear == (int)$endYear)&&((int)$tmpMonth <= (int)$endMonth))||
				( (int)$tmpYear<(int)$endYear))	{
			if (((int)$tmpMonth == (int)$endMonth)&&((int)$tmpYear == (int)$endYear)) {
				$tmpDateEnd = $dateEnd;
			} else {
				if ( $tmpMonth==12 ) {
					$tmpYear +=1;
					$tmpMonth = 1;
				} else {
					$tmpMonth += 1; 
				}
				$tmpDateEnd = $tmpYear."-".$tmpMonth."-".$tmpDay;
			}
			$results = array();
			$WasARule = false;
			//var_dump($ruleCount,$rule);
			$ruleCount = count($rule);
			for ($i = 0; $i<$ruleCount; $i++) {
				
				//$tmpTimestamp = mktime(0, 0, 0, int month, int day, int year, int [is_dst] );
				
				$tmpQuery = $query."AND m.creation_time BETWEEN " . strtotime($tmpDate). " " ."AND " . strtotime($tmpDateEnd). " ";	
				$tmpQuery .= "AND m.rule_id = ".$rule[$i]." ";	

				$this->db->query($tmpQuery);
				
				

				$result = array();
				if ($this->db->num_rows()) {
					$VOCresult = 0;

					for ($j=0; $j<$this->db->num_rows(); $j++) {
						$data = $this->db->fetch($j);	
						
						$alreadyRuleCalculatedVoc[$data->mix_id] = 'true';
						$VOCresult += $data->voc;			
					}
					$total += $VOCresult; 
					
					

					$result = array(
						'rule' => $rule_nr[$i],

						'voc' => $VOCresult
					);	
					$results [] = $result;	
					$WasARule = true;
				} 
			}
			if ($WasARule == false) {
				$results [] = $emptyData[0];
			}
			
			//var_dump($exempt);
			
			$WasAnExemptRule = false;
			for ($i = 0; $i<count($exempt); $i++) {
				
				$tmpQuery = $query."AND m.creation_time BETWEEN " . strtotime($tmpDate). " " . "AND " . strtotime($tmpDateEnd). " ";	
				$tmpQuery .= "AND m.exempt_rule = '".$exempt[$i]."' ";	

				
				
				$this->db->query($tmpQuery);

				$result = array();
				if ($this->db->num_rows()) {
					
					//var_dump($tmpQuery);	
					$VOCresult = 0;
					$VOCresultForExcemptRuleRowOnlyForReadAndNoCalculate = 0;

					for ($j=0; $j<$this->db->num_rows(); $j++) {
						$data = $this->db->fetch($j);

						if(!isset($alreadyRuleCalculatedVoc[$data->mix_id])) {
							$VOCresult += $data->voc;
							echo "<br/>Not calculated, += total VOC";
						} else {
							echo "<br/>This mix is already calculated, skip..";
						}		
						
						$VOCresultForExcemptRuleRowOnlyForReadAndNoCalculate += $data->voc;
					}
					
					
					$total += $VOCresult; 
					
					//var_dump($exempt[$i]);

					$result = array(
						'exempt' => $exempt[$i],

						'voc' => $VOCresultForExcemptRuleRowOnlyForReadAndNoCalculate ? $VOCresultForExcemptRuleRowOnlyForReadAndNoCalculate : 'none'
					);		
					$WasAnExemptRule = true;
					$results [] = $result;
				}
			}
			if ($WasAnExemptRule == false) {
				$results [] = $emptyData[1];
			}	
			
			var_dump($results);
				//result:
				$resultByMonth [] = array(
					'month' => date("M", strtotime($tmpDate)),
					'total' => $total,
					'data' => $results
				);
				$fullTotal += $total;
				$total = 0;
				//var_dump($resultByMonth);
				
			$tmpDate = $tmpDateEnd;
			if ($tmpDate == $dateEnd) {
				break;
			}
		}	
		$totalResults = array(
			'total' => $fullTotal,
			'data' => $resultByMonth
		); 
//exit;
		return $totalResults;			
	}
}
?>