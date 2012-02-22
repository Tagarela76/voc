<?php
class RWastePrice extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;

    function RWastePrice($db, $reportRequest) {

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
/*				$query = "SELECT m.mix_id, m.creation_time, re.value,re.unittype_id,re.method ".
					"FROM mix m, department d, recycle re ".
					"WHERE d.facility_id in (".$facilityString.") ".
					"AND d.department_id = m.department_id ".
					"AND re.mix_id = m.mix_id ";
*/				
				$query="SELECT m.mix_id,m.description, mg.product_id, p.product_nr, mg.quantity_lbs, m.creation_time, m.waste_percent, m.recycle_percent, io.*
						FROM mix m, mixgroup mg, department d, inventory_order io, product p
						WHERE d.facility_id in (".$facilityString.")
						AND d.department_id = m.department_id
						AND mg.mix_id = m.mix_id
						AND mg.product_id = io.order_product_id
						AND p.product_id = io.order_product_id
						AND io.order_facility_id in (".$facilityString.")
						AND io.order_status = 3
						AND io.order_completed_date <= m.creation_time";				
				break;
			case "facility":
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);

				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Facility",
					'notes' => ""
				); 
				
/*				$query="SELECT m.mix_id, m.creation_time, re.value,re.unittype_id,re.method ".
					"FROM mix m, department d, recycle re ".
					"WHERE d.facility_id = ".$this->categoryID." ".
					"AND d.department_id = m.department_id ".
					"AND re.mix_id = m.mix_id ";
*/				
				$query="SELECT m.mix_id,m.description, mg.product_id, p.product_nr, mg.quantity_lbs, m.creation_time, m.waste_percent, m.recycle_percent, io.*
				FROM mix m, mixgroup mg, department d, inventory_order io, product p
				WHERE d.facility_id = ".$this->categoryID."
				AND d.department_id = m.department_id
				AND mg.mix_id = m.mix_id
				AND mg.product_id = io.order_product_id
				AND p.product_id = io.order_product_id
				AND io.order_facility_id = ".$this->categoryID."
				AND io.order_status = 3
				AND io.order_completed_date <= m.creation_time";			
	
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
				
/*				$query="SELECT m.mix_id, m.creation_time, re.value,re.unittype_id,re.method ".
					"FROM mix m, recycle re, rule r ".
					"WHERE m.department_id = ".$this->categoryID." ".
					"AND re.mix_id = m.mix_id ".
					"AND m.rule_id = r.rule_id ";
*/				
				$query="SELECT m.mix_id,m.description, mg.product_id, p.product_nr, mg.quantity_lbs, m.creation_time, m.waste_percent, m.recycle_percent, io.*
						FROM mix m, mixgroup mg, department d, inventory_order io, product p
						WHERE d.department_id = ".$this->categoryID."
							
						AND d.department_id = m.department_id
						AND mg.mix_id = m.mix_id
						AND mg.product_id = io.order_product_id
						AND p.product_id = io.order_product_id
						AND io.order_facility_id = d.facility_id
						AND io.order_status = 3
						AND io.order_completed_date <= m.creation_time";				
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
			$doc->createTextNode("Product Waste Price Log") 
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
				if ($vocByRule['mixName'] != 'none'){
					for ($i=1;$i<=count($vocByRule['mixName']);$i++){

						$infoTag = $doc->createElement( "info" );
						$monthTag->appendChild( $infoTag );

							$ruleTag = $doc->createAttribute( "mixName" );
							$ruleTag->appendChild(
								$doc->createTextNode( html_entity_decode ($vocByRule['mixName'][$i]))
							);



							for ($j=0;$j<count($vocByRule['productName'][$i]);$j++){

								$infoTag->appendChild( $ruleTag );
								$vocTag = $doc->createAttribute( "productName".$j );
								$vocTag->appendChild(
									$doc->createTextNode($vocByRule['productName'][$i][$j])
								);
								$infoTag->appendChild( $vocTag );

							}




						$infoTag->appendChild( $ruleTag );
						$mixTag = $doc->createAttribute( "mixPrice" );
						$mixTag->appendChild(
						$doc->createTextNode($vocByRule['mixPrice'][$i])
						);
						$infoTag->appendChild( $mixTag );

						$infoTag->appendChild( $ruleTag );
						$mixTag = $doc->createAttribute( "wastePrice" );
						$mixTag->appendChild(
						$doc->createTextNode($vocByRule['wastePrice'][$i])
						);
						$infoTag->appendChild( $mixTag );					

					}
				}
			}
			
			$totalTag = $doc->createElement( "totalMix" );
			$totalTag->appendChild(
				$doc->createTextNode($vocByMonth['totalMix'])
			);
			$monthTag->appendChild( $totalTag );
			
			$totalTag = $doc->createElement( "totalWaste" );
			$totalTag->appendChild(
				$doc->createTextNode($vocByMonth['totalWaste'])
			);
			$monthTag->appendChild( $totalTag );			
		}
		$fullTotalTag = $doc->createElement( "fullTotalMix" );
		$fullTotalTag->appendChild(
			$doc->createTextNode($voc_arr['totalMix'])
		);
		$tableTag->appendChild( $fullTotalTag );		
		
		$fullTotalTag = $doc->createElement( "fullTotalWaste" );
		$fullTotalTag->appendChild(
			$doc->createTextNode($voc_arr['totalWaste'])
		);
		$tableTag->appendChild( $fullTotalTag );	

		$doc->save($fileName);
		
	}	
	
		private function group($query, $dateBegin, $dateEnd) {
			
		$emptyData [0] = array (
						'mixName' => 'none',
						'productName' => 'none',
						'quanLbs' => 'none',
						'mixPrice' => 'none',
						'wastePrice' => 'none'
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

		$totalMix = 0;
		$totalWaste = 0;
		$results = array();
		
		$fullTotalMix = 0;
		$fullTotalWaste = 0;

		$inventoryManager = new InventoryManager($this->db);

		
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

			
				$tmpQuery = $query." AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$tmpDateEndObj->getTimestamp()." ";
				$tmpQuery .= " AND io.order_completed_date >= ".$dateBeginObj->getTimestamp()." ORDER BY m.creation_time";

				$this->db->query($tmpQuery);

				$res = array();
				$mixName = array();
				if ($this->db->num_rows()) {
					$num = $this->db->num_rows();
					for ($j=0; $j<$num; $j++) {
						$this->db->query($tmpQuery);
						
						$data = $this->db->fetch($j);	

						$data->usage = $data->quantity_lbs;
						$data->in_stock_unit_type = $data->order_unittype;

						$count=(count($mixName))? count($mixName) : 0;
						if ($mixName[$count] != $data->description){
							$tmpName = array();
							
							$mixName[$count+1] = $data->description;
							$productName[$count+1][] = $data->product_nr;
							$quanLbs[$count+1][] = $data->quantity_lbs;
							
							$unittype2price = $inventoryManager->unitTypeConverter($data);
							if ($unittype2price){
								$mixPrice[$count+1] = $unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100;
								$waste = $mixPrice[$count+1] * $data->waste_percent / 100;
								$recycle = ( $mixPrice[$count+1] - $waste ) * $data->recycle_percent / 100;
								$wastePrice[$count+1] = $waste + $recycle;
								
								$totalMix += $mixPrice[count($mixPrice)];
								$totalWaste = $wastePrice[count($wastePrice)];								
							}else{
								//TODO can't convert
							}
							
							$tmpName[] = $data->product_nr;
						}elseif (!in_array($data->product_nr, $tmpName)){

							$productName[$count][] = $data->product_nr;
							$quanLbs[$count][] = $data->quantity_lbs;
							
							$tmpName[] = $data->product_nr;
							$unittype2price = $inventoryManager->unitTypeConverter($data);
							if ($unittype2price){
								$mixPrice[$count] += $unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100;
								$waste = $mixPrice[$count] * $data->waste_percent / 100;
								$recycle = ( $mixPrice[$count] - $waste ) * $data->recycle_percent / 100;
								$wastePrice[$count] = $waste + $recycle;	
								
								$totalMix += $mixPrice[count($mixPrice)];
								$totalWaste = $wastePrice[count($wastePrice)];								
							}else{
								//TODO can't convert
							}
						}

					}//end for
					$res = array(
						'mixName' => $mixName,
						'productName' => $productName,
						'quanLbs' => $quanLbs,
						'mixPrice' => $mixPrice,
						'wastePrice' => $wastePrice
					);
					$results[] = $res;
					$WasARule = true;
					
				}


			if ($WasARule == false) {
				$results [] = $emptyData[0];
			}	

			$resultByMonth [] = array(
				//'month' => date("M", strtotime($tmpDate)),
				'month' => $dateBeginObj->format('F Y'),
				'totalMix' => $totalMix,
				'totalWaste' => $totalWaste,
				'data' => $results
			);
			$fullTotalMix += $totalMix;
			$fullTotalWaste += $totalWaste;
			$totalMix = 0;
			$totalWaste = 0;
//var_dump($res);
			$dateBeginObj = $tmpDateEndObj;
			if ($dateBeginObj == DateTime::createFromFormat($this->dateFormat, $dateEnd)) {
				break;
			}
		}
		$totalResults = array(
			'totalMix' => $fullTotalMix,
			'totalWaste' => $fullTotalWaste,
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
