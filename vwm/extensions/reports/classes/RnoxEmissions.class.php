<?php

class RnoxEmissions extends ReportCreator implements iReportCreator {
	
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
				
				$query ="SELECT * " .
					"FROM nox, department d " .
					"WHERE nox.department_id = d.department_id " .					
					"AND d.facility_id IN (" . $facilityString . ") " .
					"AND nox.end_time >= ".$dateBeginObj->getTimestamp()." AND nox.end_time <= ".$dateEndObj->getTimestamp()." " .
					" ";		
				break;
				
			case "facility":				
				$facilityDetails = $this->facility->getFacilityDetails($this->categoryID);				;
				$orgInfo = array(
					'details' => $facilityDetails,
					'category' => "Facility",
					'notes' => ""
				); 
				$query ="SELECT * " .
					"FROM nox, department d " .
					"WHERE nox.department_id = d.department_id " .
					"AND d.facility_id = " . $this->categoryID . " " .
					"AND nox.end_time >= ".$dateBeginObj->getTimestamp()." AND nox.end_time <= ".$dateEndObj->getTimestamp()." " .
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
				$query ="SELECT * " .
					"FROM nox " .
					"WHERE nox.department_id = " . $this->categoryID . " " .
					"AND nox.end_time >= ".$dateBeginObj->getTimestamp()." AND nox.end_time <= ".$dateEndObj->getTimestamp()." " .
					" ";															
				break;
		}
		$noxEmissions = $this->group($query);																					
		$this->createXML($noxEmissions, $orgInfo, $datePeriod, $fileName);					
	}
	
	
	private function group($query) {			
		$this->db->query($query);						
		if ($this->db->num_rows()) {
			// do smth
		}
		
		$body = new DOMElement('body');
		$noxManager = new NoxEmissionManager($this->db);
		$departments = array();
		$departmentNames = array();
		$burners = array();		
		$noxEmissions = $this->db->fetch_all_array();
		foreach ($noxEmissions as $key => $noxEmission) {
			if(!$departmentNames[$noxEmission['department_id']]) {
				$departmentDetails = $this->department->getDepartmentDetails($noxEmission['department_id']);
				$departmentNames[$noxEmission['department_id']] = $departmentDetails['name'];
			}
			
			if(!$burners[$noxEmission['burner_id']]) {
				$burners[$noxEmission['burner_id']] = $noxManager->getBurnerDetail($noxEmission['burner_id']);
			}
			
			$noxEmission['burnerDescription'] = $burners[$noxEmission['burner_id']] ['model']." >> ".$burners[$noxEmission['burner_id']] ['serial'];
			$departments[ $departmentNames[$noxEmission['department_id']] ] [] = $noxEmission;			
		}			
		return $departments;
	}
	
		
	
	public function createXML($noxEmissions, $orgInfo, $datePeriod, $fileName) {
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
			$doc->createTextNode("20")
		);
		$page->appendChild($pageLeftMargin);
		
		$pageRightMargin = $doc->createAttribute("rightmargin");
		$pageRightMargin->appendChild(
			$doc->createTextNode("20")
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
			$doc->createTextNode("NOx Emissions") 
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
			'gasUnitUsed'	=> 0,
			'nox'			=> 0
		);
		//by department
		foreach ($noxEmissions as $depID => $noxEmissionsByDepartment) {
			$totalByDepartment = array(
				'gasUnitUsed'	=> 0,
				'nox'			=> 0
			);
						
			$depTag = $doc->createElement( "department" );		
			$tableTag->appendChild( $depTag );
			
			$depIDTag = $doc->createAttribute( "id" );
			$depIDTag->appendChild(
				$doc->createTextNode((string)$depID)
			);
			$depTag->appendChild( $depIDTag );
			
			foreach ($noxEmissions[$depID] as $key => $noxEmission) {
				$emissionTag = $doc->createElement('emission');
				$depTag->appendChild($emissionTag);
				
				$descriptionTag = $doc->createElement('description');
				$descriptionTag->appendChild(
						$doc->createTextNode(html_entity_decode($noxEmission['description']))
						);
				$emissionTag->appendChild($descriptionTag);
				
				$burnerTag = $doc->createElement('burner');
				$burnerTag->appendChild(
						$doc->createTextNode(html_entity_decode($noxEmission['burnerDescription']))
						);
				$emissionTag->appendChild($burnerTag);						
				
				$gasUnitUsedTag = $doc->createElement('gasUnitUsed');
				$gasUnitUsedTag->appendChild(
						$doc->createTextNode(html_entity_decode($noxEmission['gas_unit_used']))
						);
				$emissionTag->appendChild($gasUnitUsedTag);
				$totalByDepartment['gasUnitUsed'] += (float)$noxEmission['gas_unit_used'];
				
				$startTime = date(DEFAULT_DATE_FORMAT.' H:i:s', $noxEmission['start_time']);
				$startTimeTag = $doc->createElement('startTime');
				$startTimeTag->appendChild(
						$doc->createTextNode($startTime)
						);
				$emissionTag->appendChild($startTimeTag);
				
				$endTime = date(DEFAULT_DATE_FORMAT.' H:i:s', $noxEmission['end_time']);
				$endTimeTag = $doc->createElement('endTime');
				$endTimeTag->appendChild(
						$doc->createTextNode($endTime)
						);
				$emissionTag->appendChild($endTimeTag);
				
				$noteTag = $doc->createElement('note');
				$noteTag->appendChild(
						$doc->createTextNode(html_entity_decode($noxEmission['note']))
						);
				$emissionTag->appendChild($noteTag);
				
				$noxTag = $doc->createElement('nox');
				$noxTag->appendChild(
						$doc->createTextNode(html_entity_decode($noxEmission['nox']))
						);
				$emissionTag->appendChild($noxTag);
				$totalByDepartment['nox'] += (float)$noxEmission['nox'];
			}
			
			//	DEPARTMENT TOTALS
			$totalDepTag = $doc->createElement('totalForDepartment');
			
			$totalGasUnitUsedDepTag = $doc->createElement('totalGasUnitUsedDep');
			$totalGasUnitUsedDepTag->appendChild(
					$doc->createTextNode($totalByDepartment['gasUnitUsed'])
					);
			$totalDepTag->appendChild($totalGasUnitUsedDepTag);
			$totalSuper['gasUnitUsed'] += $totalByDepartment['gasUnitUsed'];
			
			$totalNoxDepTag = $doc->createElement('totalNoxDep');
			$totalNoxDepTag->appendChild(
					$doc->createTextNode($totalByDepartment['nox'])
					);
			$totalDepTag->appendChild($totalNoxDepTag);
			$totalSuper['nox'] += $totalByDepartment['nox'];
			
			$depTag->appendChild($totalDepTag);
		}
		
		/* SUPER TOTALS */
		$totalSuperTag = $doc->createElement('totalSuper');
		
		$totalGasUnitUsedSuperTag = $doc->createElement('totalGasUnitUsedSuper');
		$totalGasUnitUsedSuperTag->appendChild(
				$doc->createTextNode($totalSuper['gasUnitUsed'])
				);
		$totalSuperTag->appendChild($totalGasUnitUsedSuperTag);		
			
		$totalNoxSuperTag = $doc->createElement('totalNoxSuper');
		$totalNoxSuperTag->appendChild(
				$doc->createTextNode($totalSuper['nox'])
				);
		$totalSuperTag->appendChild($totalNoxSuperTag);
		
		$tableTag->appendChild($totalSuperTag);

		$doc->save($fileName);		
	}	
	
	
}
?>