<?php

class RchemClass extends ReportCreator implements iReportCreator {
	
	function RchemClass($db, $reportRequest) {
		$this->db = $db;
		$this->categoryType = $reportRequest->getCategoryType();
		$this->categoryID = $reportRequest->getCategoryID(); 	
	}
	
	public function buildXML($fileName) {
		switch ($this->categoryType) {
		
			case "company":
				//get data
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);	    				    									

				//	new query with new hazardous (chemical) system							
				$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum(mg.quantity) quantity, mg.unit_type, cc.name hazard_class ".
					"FROM mixgroup mg, mix m, department d, product p, chemical_class cc, product2chemical_class p2cc " .
					"WHERE mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND p.product_id = mg.product_id " .
					"AND p.product_id = p2cc.product_id " .
					"AND p2cc.chemical_class_id = cc.id " .							
					"AND d.facility_id IN (" . $facilityString . ") " .
					"group by cc.name, p.product_nr, p.name, mg.unit_type";
				
				$in = $this->group($query,$this->categoryType,$this->categoryID);										
				
				//getting company name
				$company = new Company($this->db);
				$companyDetails = $company->getCompanyDetails($this->categoryID);
				$orgDetails['company'] = $companyDetails; 
				//Creating XML file  		
				$this -> createXML($in['hazardClass'],$in['outputs'],$orgDetails,$fileName);						    			    											
				
				break;
				
			case "facility":
				//	new query with new hazardous (chemical) system
				$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum(mg.quantity) quantity, mg.unit_type, d.name department_name, cc.name hazard_class ".
					"FROM mixgroup mg, mix m, department d, product p, chemical_class cc, product2chemical_class p2cc " .
					"WHERE mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND p.product_id = mg.product_id " .
					"AND p.product_id = p2cc.product_id " .
					"AND p2cc.chemical_class_id = cc.id " .
					"AND d.facility_id = " . $this->categoryID . " " .
					"group by cc.name, p.product_nr, p.name, mg.unit_type, d.name";
				
				$in = $this->group($query,$this->categoryType,$this->categoryID);						
				
				//getting company name
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);
				
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
				$orgDetails['company'] = $companyDetails;
				$orgDetails['facility'] = $facilityDetails;
				
				//Creating XML file  
				$this -> createXML($in['hazardClass'],$in['outputs'],$orgDetails,$fileName);																			    			    																		
				break;
				
			case "department":
				//	new query with new hazardous (chemical) system
				$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum( mg.quantity ) quantity, mg.unit_type, m.description, cc.name hazard_class " .
					"FROM mixgroup mg, mix m, department d, product p, chemical_class cc, product2chemical_class p2cc " .
					"WHERE mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND p.product_id = mg.product_id " .
					"AND p.product_id = p2cc.product_id " .
					"AND p2cc.chemical_class_id = cc.id " .
					"AND d.department_id =" .$this->categoryID. " " .
					"GROUP BY cc.name, p.product_nr, p.name, mg.unit_type, m.description";							
				$this->db->query($query);    				        						    			
				
				if ($this->db->num_rows()) {
					for ($i=0; $i<$this->db->num_rows(); $i++) {
						$data=$this->db->fetch($i);																
						$result=array (
							'productID'	=>	$data->product_id,
							'commonName'	=>	$data->product_nr,
							'chemicalName'  =>	$data->product_name,
							'amount' => $data->quantity,
							'unitType' => $data->unit_type,
							'hazardClass' => $data->hazard_class									
						);							
						$results[]=$result;							
					}
				}							
				//group by product			
				//------------------
				$unittype = new Unittype($this->db);
				$unitTypeConverter = new UnitTypeConverter("us gallon");						
				//----------------													
				$outputs[0] = $results[0];
				$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
				$amount = $unitTypeConverter->convertToDefault($results[0]['amount'], $unitypeDetails['description']);
				$outputs[0]['amount'] = $amount;
				$hazardClass[0] = $results[0]['hazardClass'];					
				$k=0;
				$j=0;
				for($i=1; $i < count($results); $i++) {
					if ($outputs[$k]['commonName'] == $results[$i]['commonName']) {
						//convert HERE!
						//$unitTypeConverter = new UnitTypeConverter("us gallon");
						$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);								
						$amount = $unitTypeConverter->convertToDefault($results[$i]['amount'], $unitypeDetails['description']);
						$outputs[$k]['amount'] += $amount;
						//$outputs[$k]['locationOfUse'] .= ", ".$results[$i]['locationOfUse'];							
					} else {
						$k++;
						$outputs[$k] = $results[$i];							
					}
					//distinct hazard class
					if ($hazardClass[$j] != $results[$i]['hazardClass']) {
						$j++;
						$hazardClass[$j] = $results[$i]['hazardClass'];
					}								
				}
				//------------------------------------
				for($i=0; $i < count($outputs); $i++) {
					$query = "SELECT c.coat_desc, s.supplier " .
						"FROM product p,coat c, supplier s " .
						"WHERE p.coating_id = c.coat_id and " .
						"p.supplier_id = s.supplier_id and " .
						"p.product_nr = '" . $outputs[$i]['commonName'] ."'";
					
					$this->db->query($query);
					
					if ($this->db->num_rows() > 0) {
						$productData = $this->db->fetch(0);
						
						$coating =	$productData->coat_desc;
						$supplier =	$productData->supplier;
					}
					$outputs[$i]['commonName'] = $coating . " " . $outputs[$i]['commonName'] . " - " . $supplier;
					$productString .= $outputs[$i]['productID'] . ",";
				}								
				$productString = substr($productString,0,-1);
				
				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($this->categoryID);
				
				$facility = new Facility($this->db);
				$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
				
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
				
				$orgDetails['company'] = $companyDetails;
				$orgDetails['facility'] = $facilityDetails;
				$orgDetails['department'] = $departmentDetails;
				
				
				$query = "SELECT pg.product_id, pg.osuse, pg.csuse, pg.location_storage, pg.location_use " .
					"FROM productgroup pg, inventory i " .
					"WHERE pg.inventory_id = i.inventory_id " .
					"AND product_id IN (" . $productString . ") " .
					"AND i.facility_id = " . $departmentDetails['facility_id'] . " " .
					"ORDER BY product_id";
				
				$this->db->query($query);
				if ($this->db->num_rows()) {
					for ($i=0; $i<$this->db->num_rows(); $i++) {
						$data=$this->db->fetch($i);																
						$pg=array (
							'productID'	=>	$data->product_id,
							'osuse'	=>	$data->osuse,
							'csuse'  =>	$data->csuse,
							'locationStorage' => $data->location_storage,
							'locationUse' => $data->location_use																		
						);							
						$pgData[]=$pg;							
					}
				}
				
				for ($i=0; $i<count($outputs); $i++) {
					$outputs[$i]['osuse'] = 0;
					$outputs[$i]['csuse'] = 0;
					$outputs[$i]['locationStorage'] = "";
					$outputs[$i]['locationUse'] = "";
					foreach ($pgData as $pg) {
						if ($pg['productID'] == $outputs[$i]['productID']) {
							$outputs[$i]['osuse'] += $pg['osuse'];
							$outputs[$i]['csuse'] += $pg['csuse'];
							if ($pg['locationStorage'] != "" && !strstr($outputs[$i]['locationStorage'],$pg['locationStorage'])) {
								$outputs[$i]['locationStorage'] .= $pg['locationStorage'] . ", ";
							} 									 
							if ($pg['locationUse'] != "" && !strstr($outputs[$i]['locationUse'],$pg['locationUse'])) {
								$outputs[$i]['locationUse'] .= $pg['locationUse'] . ", ";
							}																
						}
					}
					if ($outputs[$i]['locationStorage'] != "") {
						$outputs[$i]['locationStorage'] = substr($outputs[$i]['locationStorage'],0,-2);
					}
					if ($outputs[$i]['locationUse'] != "") {
						$outputs[$i]['locationUse'] = substr($outputs[$i]['locationUse'],0,-2);
					}							
				}										
				//-------------------------------------																													
				
				//Creating XML file  			
				$this -> createXML($hazardClass,$outputs,$orgDetails,$fileName);				    			    																													
				break;
		}
	}
	
	public function createXML($hazardClass,$outputs,$orgDetails,$fileName) {
		$doc = new DOMDocument();
		$doc->formatOutput = true;     							  							  							  						
		
		$page = $doc->createElement( "page" );		
		$doc->appendChild( $page );
		
		$pageOrientation = $doc->createAttribute("orientation");
		$pageOrientation->appendChild(
			$doc->createTextNode("l")
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
			$doc->createTextNode("CHEMICAL CLASSIFICATION SUMMARY FORM")
		);
		$page->appendChild( $title );
		
		$company = $doc->createElement("company");  						
		$page->appendChild( $company );
		
		$companyName = $doc->createElement( "companyName" );
		$companyName->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["company"]["name"]))
		);
		$company -> appendChild( $companyName );
		
		$companyAddress = $doc->createElement("companyAddress" );
		$companyAddress->appendChild(
			$doc->createTextNode( html_entity_decode ($orgDetails["company"]["address"].", ".$orgDetails["company"]["city"].", ".$orgDetails["company"]["zip"]))
		);
		$company->appendChild( $companyAddress );
		
		if (isset($orgDetails["facility"])) {
			$facility = $doc->createElement("facility");  						
			$page->appendChild( $facility );
			
			$facilityName = $doc->createElement( "facilityName" );
			$facilityName->appendChild(
				$doc->createTextNode( html_entity_decode ($orgDetails["facility"]["name"]))
			);
			$facility -> appendChild( $facilityName );
			
			$facilityAddress = $doc->createElement("facilityAddress" );
			$facilityAddress->appendChild(
				$doc->createTextNode( html_entity_decode ($orgDetails["facility"]["address"].", ".$orgDetails["facility"]["city"].", ".$orgDetails["facility"]["zip"]))
			);
			$facility->appendChild( $facilityAddress );
		}
		
		if (isset($orgDetails["department"])) {
			$department = $doc->createElement("department");  						
			$page->appendChild( $department );
			
			$departmentName = $doc->createElement( "departmentName" );
			$departmentName->appendChild(
				$doc->createTextNode( html_entity_decode ($orgDetails["department"]["name"]))
			);
			$department -> appendChild( $departmentName );						
		}		
		
		$r = $doc->createElement( "items" );
		$page->appendChild( $r );
		foreach ($hazardClass as $hc) {
			$totalIS = 0;
			$totalES = 0;
			$totalOS = 0;
			$totalCS = 0;
			
			$hazard = $doc->createElement( "hazardClass" );  							
			
			$hazardName = $doc->createAttribute("class");
			$hazardName->appendChild(
				$doc->createTextNode( html_entity_decode ($hc))
			);
			$hazard->appendChild($hazardName);		
			
			foreach( $outputs as $output ) {
				if ($hc == $output['hazardClass']) {  									  																			
					$item = $doc->createElement( "item" );
					
					$commonName = $doc->createElement( "commonName" );
					$commonName->appendChild(
						$doc->createTextNode( html_entity_decode ( $output['commonName'] ))
					);
					$item->appendChild( $commonName );
					
					$chemicalName = $doc->createElement( "chemicalName" );
					$chemicalName->appendChild(
						$doc->createTextNode( html_entity_decode ( $output['chemicalName'] ))
					);
					$item->appendChild($chemicalName );
					
					$amount = $doc->createElement( "amount" );
					$amount->appendChild(
						$doc->createTextNode(  html_entity_decode ( round($output['amount'],2)." gal"))
					);
					$item->appendChild($amount );
					if (strtoupper($output['locationStorage']) == 'EXTERIOR STORAGE') {
						$totalES += $output['amount'];
					} else {
						$totalIS += $output['amount'];
					}					
					
					$osUse = $doc->createElement( "osUse" );
					$osUse->appendChild(
						$doc->createTextNode( html_entity_decode (round($output['osuse'],2)." gal"))
					);
					$item->appendChild($osUse);
					$totalOS += $output['osuse'];
					
					$csUse = $doc->createElement( "csUse" );
					$csUse->appendChild(
						$doc->createTextNode( html_entity_decode (round($output['csuse'],2)." gal"))
					);
					$item->appendChild($csUse);
					$totalCS += $output['csuse'];
					
					$locationOfStorage = $doc->createElement( "locationOfStorage" );
					if ($output['locationStorage'] != "") {
						$locationOfStorage->appendChild(
							$doc->createTextNode( html_entity_decode ($output['locationStorage']))
						);	
					} else {
						$locationOfStorage->appendChild(
							$doc->createTextNode("N/A")
						);	
					}					
					$item->appendChild($locationOfStorage);
					
					$location = $doc->createElement( "locationOfUse" );
					if ($output['locationUse'] != "") {
						$location->appendChild(
							$doc->createTextNode(  html_entity_decode ( $output['locationUse'] ))
						);
					} else {
						$location->appendChild(
							$doc->createTextNode("N/A")
						);
					}
					$item->appendChild($location );
					
					$hazard->appendChild( $item );
					
					$r->appendChild( $hazard ); 							
					
				}
			}
			
			$totalTag = $doc->createElement( "total" );
			
				$ISattr = $doc->createAttribute("IS");
				$ISattr->appendChild(
					$doc->createTextNode(round($totalIS,2)." gal")
				);	
				$totalTag->appendChild($ISattr);
				
				$ESattr = $doc->createAttribute("ES");
				$ESattr->appendChild(
					$doc->createTextNode(round($totalES,2)." gal")
				);	
				$totalTag->appendChild($ESattr);
				
				$OSattr = $doc->createAttribute("OS");
				$OSattr->appendChild(
					$doc->createTextNode(round($totalOS,2)." gal")
				);	
				$totalTag->appendChild($OSattr);
				
				$CSattr = $doc->createAttribute("CS");
				$CSattr->appendChild(
					$doc->createTextNode(round($totalCS,2)." gal")
				);	
				$totalTag->appendChild($CSattr);
				
			$hazard->appendChild($totalTag); 				  			
		}		  				
		$doc->save($fileName);			
	}
	
	private function group($query,$categoryType,$categoryID){
		$this->db->query($query);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																
				$result=array (
					'productID'		=>	$data->product_id,
					'commonName'	=>	$data->product_nr,
					'chemicalName'  =>	$data->product_name,
					'amount' 		=> $data->quantity,
					'unitType' 		=> $data->unit_type,
					'hazardClass' 	=> $data->hazard_class									
				);							
				$results[]=$result;							
			}
		}
		//group by product			
		//------------------
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter("us gallon");						
		//----------------													
		$outputs[0] = $results[0];
		$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
		$amount = $unitTypeConverter->convertToDefault($results[0]['amount'], $unitypeDetails['description']);
		$outputs[0]['amount'] = $amount;
		$hazardClass[0] = $results[0]['hazardClass'];					
		$k=0;
		$j=0;
		for($i=1; $i < count($results); $i++) {
			if ($outputs[$k]['commonName'] == $results[$i]['commonName']) {
				//convert HERE!
				//$unitTypeConverter = new UnitTypeConverter("us gallon");
				$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);								
				$amount = $unitTypeConverter->convertToDefault($results[$i]['amount'], $unitypeDetails['description']);
				$outputs[$k]['amount'] += $amount;							
			} else {
				$k++;
				$outputs[$k] = $results[$i];							
			}
			//distinct hazard class
			if ($hazardClass[$j] != $results[$i]['hazardClass']) {
				$j++;
				$hazardClass[$j] = $results[$i]['hazardClass'];
			}								
		}
		//------------------------------------
		for($i=0; $i < count($outputs); $i++) {
			$query = "SELECT c.coat_desc, s.supplier " .
				"FROM product p,coat c, supplier s " .
				"WHERE p.coating_id = c.coat_id and " .
				"p.supplier_id = s.supplier_id and " .
				"p.product_nr = '" . $outputs[$i]['commonName'] ."'";
			
			$this->db->query($query);
			
			if ($this->db->num_rows() > 0) {
				$productData = $this->db->fetch(0);
				
				$coating =	$productData->coat_desc;
				$supplier =	$productData->supplier;
			}
			$outputs[$i]['commonName'] = $coating . " " . $outputs[$i]['commonName'] . " - " . $supplier;							
			$productString .= $outputs[$i]['productID'] . ","; 
			
		}					
		$productString = substr($productString,0,-1);
		switch ($categoryType) {					
			case "company":			
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);
				
				$query = "SELECT pg.product_id, pg.osuse, pg.csuse, pg.location_storage, pg.location_use " .
					"FROM productgroup pg, inventory i " .
					"WHERE pg.inventory_id = i.inventory_id " .
					"AND product_id IN (" . $productString . ") " .
					"AND i.facility_id IN (" . $facilityString . ") " .
					"ORDER BY product_id";
				break;
			case "facility":
				$query = "SELECT pg.product_id, pg.osuse, pg.csuse, pg.location_storage, pg.location_use " .
					"FROM productgroup pg, inventory i " .
					"WHERE pg.inventory_id = i.inventory_id " .
					"AND product_id IN (" . $productString . ") " .
					"AND i.facility_id = " . $categoryID . " " .
					"ORDER BY product_id";
				break;
		}		
		
		$this->db->query($query);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																
				$pg=array (
					'productID'	=>	$data->product_id,
					'osuse'	=>	$data->osuse,
					'csuse'  =>	$data->csuse,
					'locationStorage' => $data->location_storage,
					'locationUse' => $data->location_use																		
				);							
				$pgData[]=$pg;							
			}
		}
		
		for ($i=0; $i<count($outputs); $i++) {
			$outputs[$i]['osuse'] = 0;
			$outputs[$i]['csuse'] = 0;
			$outputs[$i]['locationStorage'] = "";
			$outputs[$i]['locationUse'] = "";
			foreach ($pgData as $pg) {
				if ($pg['productID'] == $outputs[$i]['productID']) {
					$outputs[$i]['osuse'] += $pg['osuse'];
					$outputs[$i]['csuse'] += $pg['csuse'];
					if ($pg['locationStorage'] != "" && !strstr($outputs[$i]['locationStorage'],$pg['locationStorage'])) {
						$outputs[$i]['locationStorage'] .= $pg['locationStorage'] . ", ";
					} 									 
					if ($pg['locationUse'] != "" && !strstr($outputs[$i]['locationUse'],$pg['locationUse'])) {
						$outputs[$i]['locationUse'] .= $pg['locationUse'] . ", ";
					}																	
				}
			}
			if ($outputs[$i]['locationStorage'] != "") {
				$outputs[$i]['locationStorage'] = substr($outputs[$i]['locationStorage'],0,-2);
			}
			if ($outputs[$i]['locationUse'] != "") {
				$outputs[$i]['locationUse'] = substr($outputs[$i]['locationUse'],0,-2);
			}							
		}
		
		$out['hazardClass'] = $hazardClass;
		$out['outputs'] = $outputs;
		return $out; 
	}
}
?>