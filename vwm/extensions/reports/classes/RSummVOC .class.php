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
		
		//var_dump($this->categoryType); exit;
		
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
		
		$voc_arr= $this->group($query, $ruleQuery, $this->dateBegin, $this->dateEnd);
		
		var_dump($voc_arr);
		exit;
		
		//var_dump($voc_arr['data']); exit;
		$DatePeriod = "From ".date("Y-m-d",strtotime($this->dateBegin))." To ".date("Y-m-d",strtotime($this->dateEnd));
		//var_dump($voc_arr['data'][0]); exit;
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
			$doc->createTextNode("Monthly Summary Report of total VOC usage") 
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
		//var_dump($dateBegin);
		
		//$dateBeginTimeStump = strtorime();
		$cdateBegin = DateTime::createFromFormat("Y-m-d", $dateBegin);
		
		$cdateEnd = DateTime::createFromFormat("Y-m-d", $dateEnd);
		
		
		$total = 0;
		$tmpResults = array();
		$results = array();
		$fullTotal = 0;
		$interval = new DateInterval('P1M'); // Interval - 1 month
		$dateFormat = "Y-m-d H:i";
		$datetime1 = DateTime::createFromFormat($dateFormat, "2009-01-10 00:00");
		$datetime2 = DateTime::createFromFormat($dateFormat, "2010-04-05 00:00");
		
		var_dump($datetime1);
		var_dump($datetime2);
		//$interval = $datetime1->diff($datetime2);
		echo "Start test";
		/*for($i=0; $i < 5; $i++) {
			var_dump($datetime1);
			$iv = $datetime1->diff($datetime2);
			var_dump($iv);
			$datetime1->add($interval);
		}*/
		
		$i = 0;
		
		$diff = $datetime1->diff($datetime2);
		
		$firstIter = true; // if first iteration - we're get date difference between begin to end of current month of begin. else - we calc by monthly.

		while(($diff->m >= 0 or $diff->y >= 0) or $i < 20){
			
			//echo "date from:";
			//var_Dump($dateFrom);
			
			$i++;
			if($i > 10) {break;}
			
			//$dateFrom 	= null; //Reset values
			//$dateTo		= null;
			//$dateEndOfMonth = null;
			
			if($firstIter)
			{
				echo "Last day of current month: ";
				$lastday = $datetime1->format("t"); //date('t',strtotime('3/1/2009'));
				echo $lastday . "<br/>";
				
				$dateEndOfMonth = DateTime::createFromFormat($dateFormat, $datetime1->format("Y-m-$lastday H:i"));
				
				echo "dateEndOfMonth: ". $dateEndOfMonth->format($dateFormat);
				
				$dateTo = $dateEndOfMonth;
				//$dateFrom = $datetime1;
				
				$firstIter = false;
				
				
			}
			else {
			
				if($diff->m == 0 and $diff->y == 0) {
					
					echo "<br/>diff->m == 0 and diff->y == 0";
					
					//Calculate days..
					$dateFrom = DateTime::createFromFormat($dateFormat,$datetime1->format("Y-m-d H:i")); //This is copy of object.. i didnt found method copy or something.. =(
					
					echo "<br/> Add {$diff->d} days";
					$dateTo = $dateFrom->add(new DateInterval("P{$diff->d}D"));
					//echo "<br/> Get some between {$datetime1->format($dateFormat)} and {$dateTo->format($dateFormat)}";
					
					//$datetime1->add(new DateInterval("P{$diff->d}D"));
					
					echo "<br/> Get some between {$datetime1->format($dateFormat)} and {$dateTo->format($dateFormat)}";
					break;
					
				} else {
					
					
					echo "<br/><b>datetime1:</b>{$datetime1->format($dateFormat)}";
					
					$dateFrom = DateTime::createFromFormat($dateFormat,$datetime1->format($dateFormat)); //This is copy of object.. i didnt found method copy or something.. =(
					//echo "<br/>dateFrom add 1month: {$dateFrom->format($dateFormat)}";
					
					//echo "<br/>datetime1 before add 1month: {$datetime1->format($dateFormat)}";
					
					//Add Month
					
					
					$dateTo = $dateFrom->add($interval);
					
					/*$curM = $dateFrom->format("m");
					
					$nextMonth = $curM == 12 ? '01' : sprintf('%02d', $curM + 1);// $curM + 1;
					
					echo "<br/>curMonth: $curM, nextMonth: $nextMonth";
					
					$day = $dateFrom->format("d");
					
					$year = intval($dateFrom->format("Y"));
					
					
					$year = $curM == 12 ? $year+1 : $year;
					
					$dateTo = DateTime::createFromFormat($dateFormat,"$year-$nextMonth-$day 00:00");//$dateFrom->format("Y-m-d H:i");
					
					$dateTo->setDate($year,$month,$day); */
					
					//echo "<br/>dateTo add 1month: {$dateTo->format($dateFormat)}";
					
					//echo "<br/> Get some between {$dateFrom->format($dateFormat)} and {$dateTo->format($dateFormat)}";
					
					//$datetime1->add($interval);
					
				}
			}
			
			echo "<br/> Get some between {$datetime1->format($dateFormat)} and {$dateTo->format($dateFormat)}";
			
			
			$datetime1 = DateTime::createFromFormat($dateFormat, $dateTo->format($dateFormat));
			//$datetime1->add(new DateInterval("P1D"));
			//$datetime1 = $dateTo;
			//$datetime1->add($interval);
			//echo "date from:";
			//var_Dump($dateFrom);
			
			$diff = $datetime1->diff($datetime2);
			
			var_dump($diff->y);	
			
		}
		echo "End Test";
		
		//var_Dump($interval);
		
		$cdateBegin = DateTime::createFromFormat("Y-m-d", $dateBegin);
		
		$cdateEnd = DateTime::createFromFormat("Y-m-d", $dateEnd);
		
		$diff = $cdateBegin->diff($cdateEnd);
		
		while ($diff->m >= 0)	{

			
			if (((int)$tmpMonth == (int)$endMonth)&&((int)$tmpYear == (int)$endYear)) {
				$tmpDateEnd = $dateEnd;
			} else {
				if ( $tmpMonth==12 ) {
					$tmpYear += 1;
					$tmpMonth = 1;
				} else {
					$tmpMonth += 1; 
				}
				$tmpDateEnd = $tmpYear."-".$tmpMonth."-".$tmpDay;
			}
			echo "<h1>tmpDateEnd $tmpDateEnd</h1>";
			$results = array();
			$WasARule = false;
			for ($i = 0; $i<count($rule); $i++) {
				/*$tmpQuery = $query." AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDate)). "','%Y-%m-%d') " .
					"AND DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDateEnd)). "','%Y-%m-%d') "; */

				$tmpQuery = $query." AND m.creation_time BETWEEN $dateBeginTimeStump " .
					"AND $dateEndTimeStump ";
				
				$tmpQuery .= "AND m.rule_id = ".$rule[$i]." ";	

				
				
				
				$this->db->query($tmpQuery);

				$result = array();
				if ($this->db->num_rows()) {
					$VOCresult = 0;

					for ($j=0; $j<$this->db->num_rows(); $j++) {
						$data = $this->db->fetch($j);	
						var_dump($data);
						$VOCresult += $data->voc;			
					}
					$total += $VOCresult; 
					
					//echo "TOTAL:";
					//var_dump($total);

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
			$WasAnExemptRule = false;
			for ($i = 0; $i<count($exempt); $i++) {
				/*$tmpQuery = $query."AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDate)). "','%Y-%m-%d') " .
					"AND DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDateEnd)). "','%Y-%m-%d') "; */

				$tmpQuery = $query." AND m.creation_time BETWEEN $dateBeginTimeStump " .
					"AND $dateEndTimeStump";
				
				$tmpQuery .= " AND m.exempt_rule = '".$exempt[$i]."' ";	

				//var_dump($tmpQuery);
				
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
						'exempt' => $exempt[$i],

						'voc' => $VOCresult
					);		
					$WasAnExemptRule = true;
					$results [] = $result;
				}
			}
			if ($WasAnExemptRule == false) {
				$results [] = $emptyData[1];
			}	
				//result:
				$resultByMonth [] = array(
					'month' => date("M, Y", strtotime($tmpDate)),
					'total' => $total,
					'data' => $results
				);
				echo "<h1>Total: $total</h1>";
				$fullTotal += $total;
				$total = 0;

			$tmpDate = $tmpDateEnd;
			if ($tmpDate == $dateEnd) {
				break;
			}
		}	
		$totalResults = array(
			'total' => $fullTotal,
			'data' => $resultByMonth
		); 
		
		

		return $totalResults;			
	}
}
?>