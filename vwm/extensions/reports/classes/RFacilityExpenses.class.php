<?php
class RFacilityExpenses extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;

    function RFacilityExpenses($db, $reportRequest) {

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
				
				$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, io . * 
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
				
				$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, io . * 
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
			
				$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, io . * 
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
				if ($vocByRule['depName'] != 'none'){
					for ($i=1;$i<=count($vocByRule['depName']);$i++){

						$infoTag = $doc->createElement( "info" );
						$monthTag->appendChild( $infoTag );

							$ruleTag = $doc->createAttribute( "depName" );
							$ruleTag->appendChild(
								$doc->createTextNode( html_entity_decode ($vocByRule['depName'][$i]))
							);



						$infoTag->appendChild( $ruleTag );
						$vocTag = $doc->createAttribute( "expenses" );
						$vocTag->appendChild(
						$doc->createTextNode($vocByRule['expenses'][$i])
						);
						$infoTag->appendChild( $vocTag );
					}
				}
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
						'depName' => 'none',
						'expenses' => 'none'
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
		$fullTotalMix = 0;


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
				$depName = array();
				$expenses = array();
				if ($this->db->num_rows()) {
					$num = $this->db->num_rows();
					for ($j=0; $j<$num; $j++) {
						$this->db->query($tmpQuery);
						
						$data = $this->db->fetch($j);	

						$data->usage = $data->quantity_lbs;
						$data->in_stock_unit_type = $data->order_unittype;

						$count=(count($mixName))? count($mixName) : 0;
						$depCount=(count($depName))? count($depName) : 0;
						if ($depName[$depCount] != $data->name){
							$depName[$depCount+1] = $data->name;
						}
						
						if ($mixName[$count] != $data->description){
							$tmpName = array();
							
							$mixName[$count+1] = $data->description;

							
							$unittype2price = $inventoryManager->unitTypeConverter($data);
							if ($unittype2price){
								$expenses[count($depName)] += $unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100;

								//$mp[$count+1] = $unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100;
								//var_dump($data->description.'==>'.$data->product_id,$unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100);

								
							}else{
								//TODO can't convert
							}
							
							$tmpName[] = $data->product_nr;
						}elseif (!in_array($data->product_nr, $tmpName)){

							
							$tmpName[] = $data->product_nr;
							$unittype2price = $inventoryManager->unitTypeConverter($data);
							if ($unittype2price){
								$expenses[count($depName)] += $unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100;
								//$mp[$count] = $unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100;	
								//var_dump($data->description.'==>'.$data->product_id,$unittype2price['usage'] * $data->order_price - ($unittype2price['usage'] * $data->order_price)*$data->order_discount/100);
							
							}else{
								//TODO can't convert
							}
						}
						$expenses[count($depName)] = number_format($expenses[count($depName)], 2, '.', '');			

					}//end for
					
					$res = array(
						'depName' => $depName,
						'expenses' => $expenses
					);
					
					$results[] = $res;
					$WasARule = true;
					
				}


			if ($WasARule == false) {
				$results [] = $emptyData[0];
			}	
			foreach($expenses as $sum){
				$total += $sum;
			}
			$total = number_format($total, 2, '.', '');
			
			$resultByMonth [] = array(
				//'month' => date("M", strtotime($tmpDate)),
				'month' => $dateBeginObj->format('F Y'),
				'data' => $results,
				'total' => $total
			);
			
			$fullTotalMix += $total;
			$total = 0;

				
			

			$dateBeginObj = $tmpDateEndObj;
			if ($dateBeginObj == DateTime::createFromFormat($this->dateFormat, $dateEnd)) {
				break;
			}
		}
		$totalResults = array(
			'total' => $fullTotalMix,
			'data' => $resultByMonth
		); 
		
		return $totalResults;			
	}
		
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
