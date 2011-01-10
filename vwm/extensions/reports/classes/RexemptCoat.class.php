<?php

class RexemptCoat extends ReportCreator implements iReportCreator {

	private $frequency;
	private $monthYear;
	
	function RexemptCoat($db, $reportRequest = null) {
		$this->db = $db;
		if (!is_null($reportRequest)) {
			$this->categoryType = $reportRequest->getCategoryType();
			$this->categoryID = $reportRequest->getCategoryID();
			$this->frequency = $reportRequest->getFrequency();
			$extraVar = $reportRequest->getExtraVar();  
			$this->monthYear = $extraVar['monthYear'];  
		}	
	}
	
	public function getReportRequestByGetVars($xnyo) {
		//at first lets get data already filtered
		$categoryType = $_REQUEST['categoryLevel'];
		$id = $_REQUEST['id'];
		$reportType = $_REQUEST['reportType'];				
		$format = $_REQUEST['format'];
		
		//now lets filter specific data
		$xnyo->filter_get_var("frequency","text");
		$xnyo->filter_get_var("monthYearSelect","text");
		$xnyo->filter_get_var("logs","text");
		
		//and get them too
		$frequency = $_REQUEST['frequency'];
		$extraVar['monthYear'] = $_REQUEST['monthYearSelect'];
		$extraVar['rule'] = $_REQUEST['logs'];	
		
		//lets set extra vars in case its csv format
		if ($format == "csv") {
			$xnyo->filter_get_var("commaSeparator","text");
			$xnyo->filter_get_var("textDelimiter","text");
			$extraVar['commaSeparator'] = $_REQUEST['commaSeparator'];
			$extraVar['textDelimiter'] = $_REQUEST['textDelimiter'];
			if (strstr($extraVar['commaSeparator'],"\\")) {
				$extraVar['commaSeparator'] = substr(strstr($extraVar['commaSeparator'],"\\"),1); 
			}
			if (strstr($extraVar['textDelimiter'],"\\")) {								
				$extraVar['textDelimiter'] = str_replace("\\","",$extraVar['textDelimiter']); 
			}
		}
		
		//finally: lets get	reportRequest object!
		$reportRequest = new ReportRequest($reportType, $categoryType, $id, $frequency, $format, $dateBegin, $dateEnd, $extraVar, $_SESSION['user_id']);							
		return $reportRequest;
	}
	
	public function buildXML($fileName) {
		switch ($this->categoryType) {
		
			case "company":
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);	
				
				// Dates are not used. It's wrong! Fix it. //denis 20 May 2009
				//comments may be usefull //denis 19 May 2009
				$query = "SELECT d.facility_id, e.equipment_id, e.permit, p.product_nr, s.supplier, p.name productName, sum(mg.quantity) quantity, mg.unit_type, p.voclx " .
					"FROM mix m, department d, mixgroup mg, product p, supplier s, equipment e " .
					"WHERE d.department_id = m.department_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND mg.product_id = p.product_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND (p.name LIKE '%2K%' OR p.name LIKE '%1K%') " .
					"AND d.facility_id in (".$facilityString.") " .
					"GROUP BY d.facility_id, e.equipment_id, p.product_nr, s.supplier, p.name, mg.unit_type";						 
				break;
				
			case "facility":				
				// Dates are not used. It's wrong! Fix it. //denis 20 May 2009	
				$query = "SELECT d.facility_id, e.equipment_id, e.permit, p.product_nr, s.supplier, p.name productName, sum(mg.quantity) quantity, mg.unit_type, p.voclx " .
					"FROM mix m, department d, mixgroup mg, product p, supplier s, equipment e " .
					"WHERE d.department_id = m.department_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND mg.product_id = p.product_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND (p.name LIKE '%2K%' OR p.name LIKE '%1K%') " .
					"AND d.facility_id = ".$this->categoryID." " .
					"GROUP BY d.facility_id, e.equipment_id, p.product_nr, s.supplier, p.name, mg.unit_type";
				break;
				
			case "department":				
				// Dates are not used. It's wrong! Fix it. //denis 20 May 2009	
				$query = "SELECT d.facility_id, e.equipment_id, e.permit, p.product_nr, s.supplier, p.name productName, sum(mg.quantity) quantity, mg.unit_type, p.voclx " .
					"FROM mix m, department d, mixgroup mg, product p, supplier s, equipment e " .
					"WHERE d.department_id = m.department_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND mg.product_id = p.product_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND (p.name LIKE '%2K%' OR p.name LIKE '%1K%') " .
					"AND d.department_id = ".$this->categoryID." " .
					"GROUP BY d.facility_id, e.equipment_id, p.product_nr, s.supplier, p.name, mg.unit_type";
				break;
		}
		$period = $this->getPeriodByFrequency($this->frequency, $this->monthYear);
		$in = $this->group($query,$this->categoryType,$this->categoryID);
		$this->createXML($in,$period,$fileName); 
	}
	
	public function createXML($results,$period,$fileName) {
		
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
			$doc->createTextNode("EXEMPT COATING OPERATIONS SUMMARY REPORT")
		);
		$page->appendChild( $title );
		
		$ruleTag = $doc->createElement( "rule" );
		$ruleTag->appendChild(
			$doc->createTextNode("1145")
		);
		$page->appendChild( $ruleTag );
		
		$monthYearTag = $doc->createElement( "monthYear" );
		$monthYearTag->appendChild(
			$doc->createTextNode( html_entity_decode ($period['label']))
		);
		$page->appendChild( $monthYearTag );
		
		$sectionTag = $doc->createElement( "section" );
		$sectionTag->appendChild(
			$doc->createTextNode("(C)")
		);
		$page->appendChild( $sectionTag );
		
		$categoriesTag = $doc->createElement( "categories" );
		$categoriesTag->appendChild(
			$doc->createTextNode("(1K/2K)")
		);
		$page->appendChild( $categoriesTag );
		
		foreach ($results['facility'] as $facility) {
			$facilityTag = $doc->createElement( "facility" );		
			$page->appendChild( $facilityTag );
			
			$faciltyNameTag = $doc->createElement( "name" );
			$faciltyNameTag->appendChild(
				$doc->createTextNode( html_entity_decode ($facility['name']))
			);
			$facilityTag->appendChild( $faciltyNameTag );
			
			$faciltyLocationTag = $doc->createElement( "location" );
			$faciltyLocationTag->appendChild(
				$doc->createTextNode( html_entity_decode ($facility['address'].", ".$facility['city'].", ".$facility['state'].", ".$facility['zip']))
			);
			$facilityTag->appendChild( $faciltyLocationTag );
			
			$faciltyContactTag = $doc->createElement( "contactName" );
			$faciltyContactTag->appendChild(
				$doc->createTextNode( html_entity_decode ($facility['contact']))
			);
			$facilityTag->appendChild( $faciltyContactTag );
			
			$faciltyTelTag = $doc->createElement( "TelNo" );
			$faciltyTelTag->appendChild(
				$doc->createTextNode( html_entity_decode ($facility['phone']))
			);
			$facilityTag->appendChild( $faciltyTelTag );						
			
			foreach ($facility['equipment'] as $equipment) {
				
				$sumAmount = 0;
				$sumVoc = 0;
				
				$equipmentTag = $doc->createElement( "equipment" );		
				$facilityTag->appendChild( $equipmentTag );
				
				$equipmentIDTag = $doc->createAttribute( "id" );
				$equipmentIDTag->appendChild(
					$doc->createTextNode( html_entity_decode ($equipment['id']))
				);
				$equipmentTag->appendChild( $equipmentIDTag );
				
				$permitTag = $doc->createAttribute( "permit" );
				$permitTag->appendChild(
					$doc->createTextNode( html_entity_decode ($equipment['permit']))
				);
				$equipmentTag->appendChild( $permitTag );
				
				$productsTag = $doc->createElement( "products" );		
				$equipmentTag->appendChild( $productsTag );
				
				foreach ($results['outputs'] as $product) {
					if ( $product['equipmentID'] == $equipment['id'] ) {
						$productTag = $doc->createElement( "product" );		
						$productsTag->appendChild( $productTag );
						
						$IDTag = $doc->createElement( "productID" );
						$IDTag->appendChild(
							$doc->createTextNode( html_entity_decode ($product['productID']))
						);
						$productTag->appendChild( $IDTag );
						
						$categoryTag = $doc->createElement( "category" );
						$categoryTag->appendChild(
							$doc->createTextNode( html_entity_decode ($product['category']))
						);
						$productTag->appendChild( $categoryTag );
						
						$amountTag = $doc->createElement( "amount" );
						$amountTag->appendChild(
							$doc->createTextNode( html_entity_decode ($product['amount']))
						);
						$productTag->appendChild( $amountTag );												
						
						$vocOfCoatingTag = $doc->createElement( "vocOfCoating" );
						$vocOfCoatingTag->appendChild(
							$doc->createTextNode( html_entity_decode ($product['vocOfCoating']))
						);
						$productTag->appendChild( $vocOfCoatingTag );
						
						$exemptTag = $doc->createElement( "exempt" );
						$exemptTag->appendChild(
							$doc->createTextNode("") //TODO: here was "FIX ME" !!!
						);
						$productTag->appendChild( $exemptTag );
						
						$sumAmount += $product['amount'];
						$sumVoc += $product['vocOfCoating'];
						
					}										
				}
				$totalAmountTag = $doc->createElement( "totalAmount" );
				$totalAmountTag->appendChild(
					$doc->createTextNode($sumAmount)
				);
				$equipmentTag->appendChild( $totalAmountTag );
				
				$totalVocTag = $doc->createElement( "totalVoc" );
				$totalVocTag->appendChild(
					$doc->createTextNode($sumVoc)
				);
				$equipmentTag->appendChild( $totalVocTag );
				
				$totalExemptTag = $doc->createElement( "totalExempt" );
				$totalExemptTag->appendChild(
					$doc->createTextNode("") //TODO: here was "FIX ME"
				);
				$equipmentTag->appendChild( $totalExemptTag );
			}																	
		}
		//getting users phone
		$this->db->query("SELECT phone FROM user WHERE user_id = ".$_SESSION['user_id']);
		$data=$this->db->fetch(0);
		
		$printNameTag = $doc->createElement( "printName" );
		$printNameTag->appendChild(
			$doc->createTextNode( html_entity_decode ($_SESSION['username']))
		);
		$page->appendChild( $printNameTag );
		
		$usersPhoneTag = $doc->createElement( "usersTelNo" );
		$usersPhoneTag->appendChild(
			$doc->createTextNode( html_entity_decode ($data->phone))
		);
		$page->appendChild( $usersPhoneTag );
		
		$dateTag = $doc->createElement( "date" );
		$dateTag->appendChild(
			$doc->createTextNode(date('m-d-Y'))
		);
		$page->appendChild( $dateTag );
		
		$doc->save($fileName);
	}
	
	private function group($query,$categoryType,$categoryID) {
		$this->db->query($query);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																
				$result=array (
					'facilityID'	=>	$data->facility_id,
					'equipmentID'	=>	$data->equipment_id,
					'permit'	=> $data->permit,
					'productID'	=>	$data->supplier ." ".$data->product_nr,
					'category'  =>	$data->productName,
					'amount' => $data->quantity,
					'unitType' => $data->unit_type,
					'vocOfCoating' => $data->voclx									
				);							
				$results[]=$result;											
			}
		}
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter("us gallon");
		$facilityObj = new Facility($this->db);	
		
		//conversion
		$outputs[0] = $results[0];
		$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
		$amount = $unitTypeConverter->convertToDefault($results[0]['amount'], $unitypeDetails['description']);
		$outputs[0]['amount'] = $amount;	
		$facilityDetails[0] = $facilityObj -> getFacilityDetails($results[0]['facilityID']);
		$facilityDetails[0]['equipment'][0]['id'] = $results[0]['equipmentID'];
		$facilityDetails[0]['equipment'][0]['permit'] = $results[0]['permit'];			
		$k=0;
		$j=0;
		$l=0;
		for($i=1; $i < count($results); $i++) {
			if ($outputs[$k]['productID'] == $results[$i]['productID']) {
				$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);								
				$amount = $unitTypeConverter->convertToDefault($results[$i]['amount'], $unitypeDetails['description']);
				$outputs[$k]['amount'] += $amount;							
			} else {
				$k++;
				$outputs[$k] = $results[$i];							
			}
			//distinct facility
			if ($facilityDetails[$j]['facility_id'] != $results[$i]['facilityID']) {
				$j++;
				$l=0;
				$facilityDetails[$j] = $facilityObj -> getFacilityDetails($results[$i]['facilityID']);
				$facilityDetails[$j]['equipment'][$l]['id'] = $results[$i]['equipmentID'];
				$facilityDetails[$j]['equipment'][$l]['permit'] = $results[$i]['permit'];
			} elseif ($facilityDetails[$j]['equipment'][$l]['id'] != $results[$i]['equipmentID']) { //distinct equipment
				$l++;
				$facilityDetails[$j]['equipment'][$l]['id'] = $results[$i]['equipmentID'];
				$facilityDetails[$j]['equipment'][$l]['permit'] = $results[$i]['permit'];
			}											
		}	
		$out['facility'] = $facilityDetails;
		$out['outputs'] = $outputs;
		return $out; 
	}
}
?>