<?php
class RPotentialFacilityExpenses extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;

    function RPotentialFacilityExpenses($db, $reportRequest) {

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
				$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, pp .jobber_id, pp .unittype, pp .price, p.product_pricing as price_by_manufacturer, p.price_unit_type as unit_type_by_manufacturer
				FROM mix m, mixgroup mg, department d, product p
				LEFT JOIN price4product pp ON(pp.product_id=p.product_id)
				WHERE d.facility_id in (".$facilityString.")
				AND mg.product_id = p.product_id
				AND (pp.jobber_id != 0 OR pp.jobber_id IS NULL)
				AND d.department_id = m.department_id
				AND mg.mix_id = m.mix_id";
				break;
			case "facility":
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);

				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Facility",
					'notes' => ""
				); 
				
				$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, pp .jobber_id, pp .unittype, pp .price, p.product_pricing as price_by_manufacturer, p.price_unit_type as unit_type_by_manufacturer
				FROM mix m, mixgroup mg, department d, product p
				LEFT JOIN price4product pp ON(pp.product_id=p.product_id)
				WHERE d.facility_id = ".$this->categoryID."
				AND mg.product_id = p.product_id
				AND (pp.jobber_id != 0 OR pp.jobber_id IS NULL)
				AND d.department_id = m.department_id
				AND mg.mix_id = m.mix_id";

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
			
				$query="SELECT m.mix_id,m.description, mg.product_id,p.product_nr, mg.quantity_lbs, d.name, pp .jobber_id, pp .unittype, pp .price, p.product_pricing as price_by_manufacturer, p.price_unit_type as unit_type_by_manufacturer
				FROM mix m, mixgroup mg, department d, product p
				LEFT JOIN price4product pp ON(pp.product_id=p.product_id)
				WHERE d.department_id = ".$this->categoryID."
				AND mg.product_id = p.product_id
				AND (pp.jobber_id != 0 OR pp.jobber_id IS NULL)
				AND d.department_id = m.department_id
				AND mg.mix_id = m.mix_id";
				break;
	
		}

		$mix_arr= $this->group($query, $this->dateBegin, $this->dateEnd);   
		$DatePeriod = "From ".$this->dateBegin." To ".$this->dateEnd;

		$this->createXML($mix_arr, $orgInfo, $DatePeriod, $fileName);	
	
	}
	
	public function createXML($expenses_arr, $orgInfo, $DatePeriod, $fileName) { 
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
			$doc->createTextNode("Potential Facility Expenses Report") 
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
	
		foreach ($expenses_arr['data'] as $expensesByMonth) {
			$monthTag = $doc->createElement( "month" );		
			$tableTag->appendChild( $monthTag );
			
			$monthNameTag = $doc->createAttribute( "name" );
			$monthNameTag->appendChild(
				$doc->createTextNode((string)$expensesByMonth['month'])
			);
			$monthTag->appendChild( $monthNameTag );
			
			//get product list by mix
			foreach ($expensesByMonth['data'] as $mixKey => $productsInformation) { 
				
				$monthMixTag = $doc->createElement( "mixName" );
				$monthTag->appendChild( $monthMixTag );
				$monthNameAttribute = $doc->createAttribute( "name" );
				$monthNameAttribute->appendChild(
					$doc->createTextNode((string)$mixKey)
				);
				$monthMixTag->appendChild( $monthNameAttribute );
                                $totalExpensesByMix = 0;
				// get product detailed information
				$productCount = 0;				
				foreach ($productsInformation as $productInformation) {
					if ($productInformation['depName'] != 'none'){
						
						$productsTag = $doc->createElement( "products" );
						$monthMixTag->appendChild( $productsTag );
						
						$producDepNametTag = $doc->createElement( "depName" );
						$producDepNametTag->appendChild(
							$doc->createTextNode( html_entity_decode ($productInformation['depName']))
						);
						$productsTag->appendChild( $producDepNametTag );
						
						$producExpensestTag = $doc->createElement( "expenses" );
						$producExpensestTag->appendChild(
						$doc->createTextNode($productInformation['potentialExpenses'])
						);
						$productsTag->appendChild( $producExpensestTag );
						
						$producProductNameTag = $doc->createElement( "productName" );
						$producProductNameTag->appendChild(
						$doc->createTextNode($productInformation['productName'])
						);
						$productsTag->appendChild( $producProductNameTag );
                        $totalExpensesByMix += $productInformation['potentialExpenses'];
						$productCount++;
					}
				}
				$totalExpensesByMixTag = $doc->createElement( "totalExpensesByMix" );
				$totalExpensesByMixTag->appendChild(
				$doc->createTextNode($totalExpensesByMix)
				);
				$monthMixTag->appendChild( $totalExpensesByMixTag );
				
				$productCountTag = $doc->createAttribute( "productCount" );
				$productCountTag->appendChild(
				$doc->createTextNode($productCount)
				);
				$monthMixTag->appendChild( $productCountTag );
			//	var_dump($mixKey, $expensesByRule); die();
			}
			$totalTag = $doc->createElement( "total" );
			$totalTag->appendChild(
				$doc->createTextNode($expensesByMonth['total'])
			);
			$monthTag->appendChild( $totalTag );			
		
		}
		$fullTotalTag = $doc->createElement( "fullTotal" );
		$fullTotalTag->appendChild(
			$doc->createTextNode($expenses_arr['total'])
		);
		$tableTag->appendChild( $fullTotalTag );		

		$doc->save($fileName);

	}	
	
	private function group($query, $dateBegin, $dateEnd) { 
		
		$emptyData [0] = array (
						'depName' => 'none',
						'potentialExpenses' => 'none',
						'mixName' => 'none',
						'products' => 'none',
		);
	

		/*
		 * $tmpYear, $tmpMonth, $tmpDay - values of year, month and day of current time period for temporary query
		 * it need for generating $tmpDate and $tmpDateEnd
		 * $endYear, $endMonth - values of year and month of the end date for query
		 */
		$dateBeginObj = DateTime::createFromFormat($this->dateFormat, $dateBegin);
		$dateEndObj = DateTime::createFromFormat($this->dateFormat, $dateEnd);				

		$beginYear = $dateBeginObj->format('Y');			
		$beginMonth = $dateBeginObj->format('m');
		$beginDay = 1;
		
		$endYear = $dateEndObj->format('Y');
		$endMonth = $dateEndObj->format('m');

		$total = 0;
		$results = array();
		$fullTotalMix = 0;

		$inventoryManager = new InventoryManager($this->db);
		
		while ( ( ((int)$beginYear == (int)$endYear) && ((int)$beginMonth <= (int)$endMonth) )  || ( (int)$beginYear<(int)$endYear) )	{
			
			if (((int)$beginMonth == (int)$endMonth)&&((int)$beginYear == (int)$endYear)) {
				//$tmpDateEnd = $dateEnd;
				$tmpDateEndObj = $dateEndObj;
			} else {
				if ( $beginMonth ==12 ) {
					$beginYear +=1;
					$beginMonth = 1;
				} else {
					$beginMonth += 1; 
				}
				//$tmpDateEnd = $tmpYear."-".$tmpMonth."-".$tmpDay;
				$tmpDateEndObj = new DateTime(date('Y-m-d',mktime(0, 0, 0, $beginMonth, $beginDay, $beginYear)));
			}
			$results = array();

			$tmpQuery = $query." AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$tmpDateEndObj->getTimestamp()." ";
			//AND io.order_completed_date >= ".$dateBeginObj->getTimestamp()."  NEED???????
			$tmpQuery .= " ORDER BY m.creation_time";

			$this->db->query($tmpQuery);

			$res = array();
			$mixArray = array();
			$productList = array();
			
			$potentialExpenses = array();

			if ($this->db->num_rows()) {

				$resultData = $this->db->fetch_all();
				foreach ($resultData as $data) {  

					$data->usage = $data->quantity_lbs;

					// if supllier doesn't set price we get manufacturer price
					if ( is_null($data->price) || $data->price == '0.00') {
						$data->price = $data->price_by_manufacturer;
					} 
					// if supllier doesn't set unit type we get manufacturer unit type
					if ( is_null($data->unittype) ) {
						$data->unittype = $data->unit_type_by_manufacturer;
					}

					$unittype2price = $inventoryManager->convertUnitTypeFromTo($data); 

					if ($unittype2price){
						$price = $data->usage * ( $data->price / $unittype2price['usage'] ); // qty (always in lbsp * price for one product unit / price for one lbsp
					} else {

					}

					$potentialExpenses[] = number_format($price, 2, '.', '');			

					$res = array(
						'depName' => $data->name,
						'potentialExpenses' => number_format($price, 2, '.', ''),
						'mixName' => $data->description,
						'productName' => $data->product_nr,
					);

					$results[] = $res; 
				}  
			} else {
				$res = $emptyData;
				$results[] = $res;
			} 
			foreach ($results as $result) {
				if (!in_array($result['mixName'], $mixArray)) {
					$mixArray[] = $result['mixName'];
				}
			}

			foreach ($mixArray as $mix) {
				foreach ($results as $result) {
					if ($result['mixName'] == $mix) {
						$productList[$mix][] = $result;
					}
				}
			}

			foreach($potentialExpenses as $sum){
				$total += $sum;
			}
			$total = number_format($total, 2, '.', '');
			
			$resultByMonth [] = array(
				//'month' => date("M", strtotime($tmpDate)),
				'month' => $dateBeginObj->format('F Y'),
				'data' => $productList,
				'total' => $total
			);
		
			$fullTotalMix += $total;
			$total = 0;
 
			$dateBeginObj = $tmpDateEndObj; 
		
			if ($dateBeginObj == $dateEndObj) {
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
