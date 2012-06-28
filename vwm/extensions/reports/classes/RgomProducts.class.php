<?php

class RgomProducts extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;
	
	/**	 
	 * @var Department
	 */
	private $department;
	
	/**
	 *
	 * @var Facility
	 */
	private $facility;

	function __construct(db $db, ReportRequest $reportRequest) {		
		$this->db = $db;
		$this->categoryType = $reportRequest->getCategoryType();
		$this->categoryID = $reportRequest->getCategoryID();
		$this->dateBegin = $reportRequest->getDateBegin();
		$this->dateEnd = $reportRequest->getDateEnd(); 	 	
		$this->dateFormat = $reportRequest->getDateFormat();
		
		$this->department = new Department($this->db);
		$this->facility = new Facility($this->db);
	}
	
	
	
	public function buildXML($fileName) {	
		$dateBeginObj = DateTime::createFromFormat($this->dateFormat, $this->dateBegin);
		$dateEndObj = DateTime::createFromFormat($this->dateFormat, $this->dateEnd);    	
		
		$datePeriod = "From ".$this->dateBegin." To ".$this->dateEnd;
		
		switch ($this->categoryType) {
		
			case "company":
				$company = new Company($this->db);						
				$companyDetails = $company->getCompanyDetails($this->categoryID);
								
				$facilityList = $this->facility->getFacilityListByCompany($this->categoryID);				
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);
				
				$orgInfo = array(
					'details' => $companyDetails,
					'category' => "Company",
					'notes' => ""					
				); 			
				
				$query ="SELECT accessory.name, accessory_usage.accessory_id, accessory_usage.department_id, accessory_usage.date, accessory_usage.usage, price4product.price  " .
					"FROM accessory_usage, department d, accessory, price4product " .
					"WHERE accessory_usage.department_id = d.department_id " .					
					"AND accessory_usage.accessory_id = accessory.id " .
					"AND price4product.product_id = accessory.id " .
					"AND d.facility_id IN (" . $facilityString . ") " .
					"AND accessory_usage.date >= ".$dateBeginObj->getTimestamp()." AND accessory_usage.date <= ".$dateEndObj->getTimestamp()." " .
					" ";		
				break;
				
			case "facility":				
				$facilityDetails = $this->facility->getFacilityDetails($this->categoryID);				;
				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Facility",
					'notes' => ""
				); 
				$query ="SELECT accessory.name, accessory_usage.accessory_id, accessory_usage.department_id, accessory_usage.date, accessory_usage.usage, price4product.price " .
					"FROM accessory_usage, department d, accessory, price4product  " .
					"WHERE accessory_usage.department_id = d.department_id " .
					"AND accessory_usage.accessory_id = accessory.id " .
					"AND price4product.product_id = accessory.id " .	
					"AND d.facility_id = " . $this->categoryID . " " .
					"AND accessory_usage.date >= ".$dateBeginObj->getTimestamp()." AND accessory_usage.date <= ".$dateEndObj->getTimestamp()." " .
					" ";		
																
				break;
				
			case "department":
				
				$departmentDetails = $this->department->getDepartmentDetails($this->categoryID);								
				$facilityDetails = $this->facility->getFacilityDetails($departmentDetails['facility_id']);
						
				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Department",
					'name' => $departmentDetails['name'],
					'notes' => ""
				); 				
				$query ="SELECT accessory.name, accessory_usage.accessory_id, accessory_usage.department_id, accessory_usage.date, accessory_usage.usage, price4product.price  " .
					"FROM accessory_usage, accessory, price4product " .
					"WHERE accessory_usage.department_id = " . $this->categoryID . " " .
					"AND accessory_usage.accessory_id = accessory.id " .
					"AND price4product.product_id = accessory.id " .
					"AND accessory_usage.date >= ".$dateBeginObj->getTimestamp()." AND accessory_usage.date <= ".$dateEndObj->getTimestamp()." " .
					" ";															
				break;
		} 
		$gomProducts = $this->group($query);	//var_dump($gomProducts); die('hj');																					
		$this->createXML($gomProducts, $orgInfo, $datePeriod, $fileName);					
	}
	
	
	private function group($query) {			
		$this->db->query($query);						
		if ($this->db->num_rows()) {
			// do smth
		}
		
		$body = new DOMElement('body'); 
		$departments = array();
		$departmentNames = array();	
		$gomProducts = $this->db->fetch_all_array();
		foreach ($gomProducts as $key => $gomProduct) {
			if(!$departmentNames[$gomProduct['department_id']]) {
				$departmentDetails = $this->department->getDepartmentDetails($gomProduct['department_id']);
				$departmentNames[$gomProduct['department_id']] = $departmentDetails['name'];
			}
			$departments[ $departmentNames[$gomProduct['department_id']] ] [] = $gomProduct;			
		}		
		return $departments;
	}
	
		
	
	public function createXML($gomProducts, $orgInfo, $datePeriod, $fileName) {
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
			$doc->createTextNode("GOM products") 
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
			$doc->createTextNode($datePeriod)
		);
		$page->appendChild($timePeriodTag);
		
		$tableTag = $doc->createElement( "table" );		
		$page->appendChild( $tableTag );		

		$totalSuper = array(
			'gom'			=> 0,
			'price'			=>0
		);
		//by department
		foreach ($gomProducts as $depID => $gomProductsByDepartment) {
			$totalByDepartment = array(
				'gom'			=> 0,
				'price'			=>0
			);
						
			$depTag = $doc->createElement( "department" );		
			$tableTag->appendChild( $depTag );
			
			$depIDTag = $doc->createAttribute( "id" );
			$depIDTag->appendChild(
				$doc->createTextNode((string)$depID)
			);
			$depTag->appendChild( $depIDTag );
			
			foreach ($gomProducts[$depID] as $key => $gomProduct) {
				$gomProductsTag = $doc->createElement('gomProducts');
				$depTag->appendChild($gomProductsTag);
				
				$gomProductsNameTag = $doc->createElement('gomProductsName');
				$gomProductsNameTag->appendChild(
						$doc->createTextNode(html_entity_decode($gomProduct['name']))
						);
				$gomProductsTag->appendChild($gomProductsNameTag);
				
				$gomProductsPriceTag = $doc->createElement('gomProductsPrice');
				$gomProductsPriceTag->appendChild(
						$doc->createTextNode(html_entity_decode($gomProduct['price']))
						);
				$gomProductsTag->appendChild($gomProductsPriceTag);
				$totalByDepartment['price'] += (float)$gomProduct['price'];

				$date = date(DEFAULT_DATE_FORMAT.' H:i:s', $gomProduct['date']);
				$dateTag = $doc->createElement('gomProductsDate');
				$dateTag->appendChild(
						$doc->createTextNode($date)
						);
				$gomProductsTag->appendChild($dateTag);
				
				$gomTag = $doc->createElement('usage');
				$gomTag->appendChild(
						$doc->createTextNode(html_entity_decode($gomProduct['usage']))
						);
				$gomProductsTag->appendChild($gomTag);
				$totalByDepartment['usage'] += (float)$gomProduct['usage'];
			}
			
			//	DEPARTMENT TOTALS
			$totalDepTag = $doc->createElement('totalForDepartment');
			
			$totalGomDepTag = $doc->createElement('totalGomDep');
			$totalGomDepTag->appendChild(
					$doc->createTextNode($totalByDepartment['gom'])
					);
			$totalDepTag->appendChild($totalNoxDepTag);
			$totalSuper['gom'] += $totalByDepartment['usage'];
			$totalSuper['price'] += $totalByDepartment['price'];
			$depTag->appendChild($totalDepTag);
		}
		
		/* SUPER TOTALS */
		$totalSuperTag = $doc->createElement('totalSuper');
			
		$totalGomSuperTag = $doc->createElement('totalGomSuper');
		$totalGomSuperTag->appendChild(
				$doc->createTextNode($totalSuper['gom'])
				);
		$totalPriceSuperTag = $doc->createElement('totalPriceSuper');
		$totalPriceSuperTag->appendChild(
				$doc->createTextNode($totalSuper['price'])
				);

		$totalSuperTag->appendChild($totalGomSuperTag);
		$totalSuperTag->appendChild($totalPriceSuperTag);
		
		$tableTag->appendChild($totalSuperTag);

		$doc->save($fileName);		
	}	
	
	
}
?>