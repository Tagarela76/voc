<?php
class RReclaimedCredit extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;

    function RReclaimedCredit($db, $reportRequest) {
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

				
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);
				//$companyDetails['facility_id'] = $facilityString;
				$orgInfo = array(
					'details' => $companyDetails,
					'category' => "Company",
					'notes' => ""
					
				); 				
				$query = "SELECT m.mix_id, m.creation_time, re.value,re.unittype_id,re.method ".
					"FROM mix m, department d, recycle re ".
					"WHERE d.facility_id in (".$facilityString.") ".
					"AND d.department_id = m.department_id ".
					"AND re.mix_id = m.mix_id ";
				break;
			case "facility":
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);

				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Facility",
					'notes' => ""
				); 
				
				$query="SELECT m.mix_id, m.creation_time, re.value,re.unittype_id,re.method ".
					"FROM mix m, department d, recycle re ".
					"WHERE d.facility_id = ".$this->categoryID." ".
					"AND d.department_id = m.department_id ".
					"AND re.mix_id = m.mix_id ";
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
				
				$query="SELECT m.mix_id, m.creation_time, re.value,re.unittype_id,re.method ".
					"FROM mix m, recycle re, rule r ".
					"WHERE m.department_id = ".$this->categoryID." ".
					"AND re.mix_id = m.mix_id ".
					"AND m.rule_id = r.rule_id ";
				break;
		}

		$voc_arr= $this->group($query, $this->dateBegin, $this->dateEnd);
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
			$doc->createTextNode("1")
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
			$doc->createTextNode("Reclaimed Credit Log ") 
		);
		$page->appendChild( $title );
		
		$subTitle = $doc->createElement( "subTitle" );
		$subTitle->appendChild(
			$doc->createTextNode(" including by month ")
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
		
		//if ($orgInfo['category'] != "Company") {
			$facilityIdTag = $doc->createElement( "facilityID" );
			$facilityIdTag->appendChild(
				$doc->createTextNode( html_entity_decode ($orgInfo['details']['facility_id']))
			);
			$page->appendChild($facilityIdTag);
		//}
		
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
				
					$ruleTag = $doc->createAttribute( "recycle" );
					$ruleTag->appendChild(
						$doc->createTextNode( html_entity_decode ($vocByRule['recycle']))
					);
				
				$infoTag->appendChild( $ruleTag );
				$vocTag = $doc->createAttribute( "date" );
				$vocTag->appendChild(
					$doc->createTextNode($vocByRule['date'])
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
	
		private function group($query, $dateBegin, $dateEnd) {
			
		$emptyData [0] = array (
					//	'rule' => "none",
						'date' => "none",
						'recycle' => "none"
		);

		/*
		 * $tmpYear, $tmpMonth, $tmpDay - values of year, month and day of current time period for temporary query
		 * it need for generating $tmpDate and $tmpDateEnd
		 * $endYear, $endMonth - values of year and month of the end date for query
		 */
		$dateBeginObj = DateTime::createFromFormat($this->dateFormat, $dateBegin);
		$dateEndObj = DateTime::createFromFormat($this->dateFormat, $dateEnd);				

		$tmpYear = $dateBeginObj->format('Y');			
		$tmpMonth = $dateBeginObj->format('m');
		$tmpDay = 1;
		
		$endYear = $dateEndObj->format('Y');
		$endMonth = $dateEndObj->format('m');

		$total = 0;
		$results = array();
		$Recycleresult = array();
		$fullTotal = 0;
		//$unittype = new Unittype($this->db);


		
		while ( ( ((int)$tmpYear == (int)$endYear) && ((int)$tmpMonth <= (int)$endMonth) )  || ( (int)$tmpYear<(int)$endYear) )	{
			
			if (((int)$tmpMonth == (int)$endMonth)&&((int)$tmpYear == (int)$endYear)) {
				//$tmpDateEnd = $dateEnd;
				$tmpDateEndObj = $dateEndObj;
			} else {
				if ( $tmpMonth==12 ) {
					$tmpYear +=1;
					$tmpMonth = 1;
				} else {
					$tmpMonth += 1; 
				}
				//$tmpDateEnd = $tmpYear."-".$tmpMonth."-".$tmpDay;
				$tmpDateEndObj = new DateTime(date('Y-m-d',mktime(0, 0, 0, $tmpMonth, $tmpDay, $tmpYear)));
			}
			$results = array();
	
			
				$tmpQuery = $query."AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$tmpDateEndObj->getTimestamp()." ";
				$tmpQuery .= "AND re.value <> '0.00' ";

				$this->db->query($tmpQuery);

				$result = array();
				if ($this->db->num_rows()) {
					$num = $this->db->num_rows();
					for ($j=0; $j<$num; $j++) {
						$this->db->query($tmpQuery);
						
						$data = $this->db->fetch($j);	
						$createDate[$j] = date($this->dateFormat ,$data->creation_time);
						//$Recycleresult[$j] = $data->value;	
						
						/*Convert recycle value to lbs*/							
						//$Recycleresult[$j] = $this->Convert($data->mix_id,$data->value, $data->unittype_id,$companyDetails,$unittype);
						$Recycleresult[$j] = $this->FromAllToLbs($data->mix_id);
						
					$result = array(
						//'rule' => $rule_nr[$j],
						'date' => $createDate[$j],
						'recycle' => $Recycleresult[$j]
					);	
					$results[] = $result;
					$total += $Recycleresult[$j];
					
					}
					$WasARule = true;
				}
			
			if ($WasARule == false) {
				$results [] = $emptyData[0];
			}	

			$resultByMonth [] = array(
				//'month' => date("M", strtotime($tmpDate)),
				'month' => $dateBeginObj->format('F Y'),
				'total' => $total,
				'data' => $results
			);
			$fullTotal += $total;
			$total = 0;

			$dateBeginObj = $tmpDateEndObj;
			if ($dateBeginObj == DateTime::createFromFormat($this->dateFormat, $dateEnd)) {
				break;
			}
		}
		$totalResults = array(
			'total' => $fullTotal,
			'data' => $resultByMonth
		); 

		return $totalResults;			
	}

/*		private function Convert($mixID,$value, $unittype_id,$companyDetails,Unittype $unittype) {	
			
		//$unittype = new Unittype($this->db);
		$defaultType = $unittype->getDescriptionByID($companyDetails['voc_unittype_id']);	
		$unitTypeConverter = new UnitTypeConverter($defaultType);

			if (empty($unittype_id)) {
				//	percent
				
				echo 'percent';
			}	
						$recycleUnitDetails = $unittype->getUnittypeDetails($unittype_id);
						if ($unittype->isWeightOrVolume($unittype_id) == 'volume') {
								$recycleVolume = $unitTypeConverter->convertFromTo($value, $recycleUnitDetails["description"], 'us gallon');
								//$result['recyclePercent'] = $recycleVolume/$quantityVolumeSum*100;
								return $recycleVolume;
						}if ($unittype->isWeightOrVolume($unittype_id) == 'weight') {
								$recycleWeight = $unitTypeConverter->convertFromTo($value, $recycleUnitDetails["description"], "lb");
								return $recycleWeight;
								//$result['recyclePercent'] = $recycleWeight/$quantityWeightSum*100;
						}
	
		}*/
		
	private function FromAllToLbs($mixID) {	
	$result = 0;		
	$query ="SELECT mg.quantity_lbs " .
			"FROM mixgroup mg " .
			"WHERE mg.mix_id = ".$mixID."";
	
			$this->db->query($query);
			$tmpresult = $this->db->fetch($j);
			foreach ($tmpresult as $val) {
				
				$result += $val->quantity_lbs;
				
			}		

	return $result;
}	
	
}	
?>
