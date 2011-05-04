<?php

class RmixQuantRule extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;

	function RmixQuantRule($db, $reportRequest) {
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
		
		$dateBeginObj = DateTime::createFromFormat($this->dateFormat, $this->dateBegin);
		$dateEndObj = DateTime::createFromFormat($this->dateFormat, $this->dateEnd);    	
		
		switch ($this->categoryType) {
		
			case "company":
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);
				
				$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.$rule_nr_byRegion as rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type, mg.quantity_lbs " .
					"FROM product p, mixgroup mg, mix m, department d, rule r, supplier s " .
					"WHERE p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND m.rule_id = r.rule_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND d.facility_id IN  (" . $facilityString . ") " .
					"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					"GROUP BY p.product_nr, p.name, m.rule_id, r.$rule_nr_byRegion";

				//getting company name
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($this->categoryID);
				$orgDetails['company'] = $companyDetails;
				
				$in = $this->group($query,$this->categoryType,$this->categoryID);
				$this -> createXML($in['products'],$in['results'],$orgDetails,$fileName);			
				break;
				
			case "facility":
				$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.$rule_nr_byRegion as rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type, mg.quantity_lbs " .
					"FROM product p, mixgroup mg, mix m, department d, rule r, supplier s " .
					"WHERE p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND m.rule_id = r.rule_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND d.facility_id = " . $this->categoryID . " " .
					"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					"GROUP BY p.product_nr, p.name, m.rule_id, r.$rule_nr_byRegion";										

				//getting company name
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);
				
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
				$orgDetails['company'] = $companyDetails;
				$orgDetails['facility'] = $facilityDetails;	
				
				$in = $this->group($query,$this->categoryType,$this->categoryID);

				$this -> createXML($in['products'],$in['results'],$orgDetails,$fileName);																
				break;
				
			case "department":
				$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.$rule_nr_byRegion as rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type, mg.quantity_lbs " .
					"FROM product p, mixgroup mg, mix m, rule r, supplier s " .
					"WHERE p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .								
					"AND m.rule_id = r.rule_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND m.department_id = " . $this->categoryID . " " .
					"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					"GROUP BY p.product_nr, p.name, m.rule_id, r.$rule_nr_byRegion";											
				$this->db->query($query);						

				if ($this->db->num_rows()) {
					for ($i=0; $i<$this->db->num_rows(); $i++) {
						$data=$this->db->fetch($i);																								
						$result = array (
							'supplier'		=>	$data->supplier,
							'product_id'	=>	$data->product_id,
							'product_nr'	=>	$data->product_nr,
							'product_name'	=>	$data->product_name,
							'rule_nr'		=>	$data->rule_nr,
							'qtyRule'		=>	$data->qtyRule,
							'used'			=>	$data->used,							
							'unitType'		=>	$data->unit_type,	
							'quantity_lbs'	=>	$data->quantity_lbs
						);
						$results[] = $result;																						
					}
				}
				//conversion
				//$unittype = new Unittype($this->db);
				//$unitTypeConverter = new UnitTypeConverter("us gallon");						
				
				$products[0]['supplier'] = $results[0]['supplier'];
				$products[0]['product_id'] = $results[0]['product_id'];
				$products[0]['product_nr'] = $results[0]['product_nr'];
				$products[0]['product_name'] = $results[0]['product_name'];
				//$products[0]['used'] = $results[0]['used'];
				
				//$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
				
				//$qtyRule = $unitTypeConverter->convertToDefault($results[0]['qtyRule'], $unitypeDetails['description']);
				//$results[0]['qtyRule'] = $qtyRule;
				
				//$used = $unitTypeConverter->convertToDefault($results[0]['used'], $unitypeDetails['description']);
				//$products[0]['used'] = $used;
				$products[0]['used'] = $results[0]['quantity_lbs'];
				
				//	TODO: add inventory support later 
				$products[0]['notUsed'] = '--';
				//end of conversion
				
				$k=0;											
				for($i=1; $i < count($results); $i++) {
					//$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
					//$qtyRule = $unitTypeConverter->convertToDefault($results[$i]['qtyRule'], $unitypeDetails['description']);
					//$results[$i]['qtyRule'] = $qtyRule;							
					if ($results[$i]['product_nr'] != $products[$k]['product_nr']) {
						$k++;
						$products[$k]['supplier'] = $results[$i]['supplier'];
						$products[$k]['product_id'] = $results[$i]['product_id'];
						$products[$k]['product_nr'] = $results[$i]['product_nr'];
						$products[$k]['product_name'] = $results[$i]['product_name'];
						
						//$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
						//$used = $unitTypeConverter->convertToDefault($results[$i]['used'], $unitypeDetails['description']);
						//$products[$k]['used'] = $used;					
						$products[$k]['used'] = $results[$i]['quantity_lbs'];

						//	TODO: add inventory support later 
						$products[$k]['notUsed'] = '--';												
					}
				}
				
				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($this->categoryID);
				
				$facility = new Facility($this->db);
				$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
				
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
				
				$orgDetails['company'] = $companyDetails;
				$orgDetails['facility'] = $facilityDetails;
				$orgDetails['department'] = $departmentDetails;
				
				//getting product quantities in inventory
				//	TODO: add inventory suppport later
			/*	foreach ($products as $value) {
					$productString .= $value['product_id']. ","; 
				}		
				$productString = substr($productString,0,-1);												
				
				$query = "SELECT pg.product_id, sum( pg.quantity ) quantity " .
					"FROM inventory i, productgroup pg " .
					"WHERE i.inventory_id = pg.inventory_id " .
					"AND i.facility_id = " . $departmentDetails['facility_id'] . " " .
					"AND pg.product_id IN (" . $productString . ")" .
					"GROUP BY pg.product_id";
				$this->db->query($query);												
				if ($this->db->num_rows()) {
					for ($i=0; $i<$this->db->num_rows(); $i++) {
						$data=$this->db->fetch($i);																								
						$inventoryQty = array (									
							'product_id'	=>	$data->product_id,
							'quantity'	=>	$data->quantity																							
						);
						$inventoryQties[] = $inventoryQty;																						
					}
				}											
				//group data
				for ($i=0; $i<count($products); $i++) {						
					$diff = 0;
					foreach ($inventoryQties as $inventoryQty) {
						if ($inventoryQty['product_id'] == $products[$i]['product_id']) {
							$diff = $inventoryQty['quantity'] - $products[$i]['used'];
						}	
					}
					if ($diff > 0) {
						$products[$i]['notUsed'] = $diff;
					} else {
						$products[$i]['notUsed'] = 0;
					}														
				}*/
		
				$this -> createXML($products,$results,$orgDetails,$fileName);					
				break;
		}
	}
	
	
	
	public function buildXMLDeprecated($fileName) {
		$rule = new Rule($this->db);
		$rule_nr_byRegion = $rule->ruleNrMap[$rule->getRegion()];
		switch ($this->categoryType) {
		
			case "company":
				$facility = new Facility($this->db);
				$facilityList = $facility->getFacilityListByCompany($this->categoryID);						
				foreach ($facilityList as $value) {
					$facilityString .= $value['id']. ","; 
				}		
				$facilityString = substr($facilityString,0,-1);
				
				$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.$rule_nr_byRegion as rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type " .
					"FROM product p, components_group cg, mixgroup mg, mix m, department d, rule r, supplier s " .
					"WHERE cg.product_id = p.product_id " .
					"AND p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND cg.rule_id = r.rule_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND d.facility_id IN  (" . $facilityString . ") " .
					"GROUP BY p.product_nr, p.name, cg.rule_id, r.$rule_nr_byRegion";

				//getting company name
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($this->categoryID);
				$orgDetails['company'] = $companyDetails;
				
				$in = $this->group($query,$this->categoryType,$this->categoryID);
				$this -> createXML($in['products'],$in['results'],$orgDetails,$fileName);			
				break;
				
			case "facility":
				$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.$rule_nr_byRegion as rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type " .
					"FROM product p, components_group cg, mixgroup mg, mix m, department d, rule r, supplier s " .
					"WHERE cg.product_id = p.product_id " .
					"AND p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND cg.rule_id = r.rule_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND d.facility_id = " . $this->categoryID . " " .
					"GROUP BY p.product_nr, p.name, cg.rule_id, r.$rule_nr_byRegion";										

				//getting company name
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($this->categoryID);
				
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
				$orgDetails['company'] = $companyDetails;
				$orgDetails['facility'] = $facilityDetails;	
				
				$in = $this->group($query,$this->categoryType,$this->categoryID);
				$this -> createXML($in['products'],$in['results'],$orgDetails,$fileName);																
				break;
				
			case "department":
				$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.$rule_nr_byRegion as rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type, mg.quantity_lbs " .
					"FROM product p, components_group cg, mixgroup mg, mix m, rule r, supplier s " .
					"WHERE cg.product_id = p.product_id " .
					"AND p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .								
					"AND cg.rule_id = r.rule_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND m.department_id = " . $this->categoryID . " " .
					"GROUP BY p.product_nr, p.name, cg.rule_id, r.$rule_nr_byRegion";											
				$this->db->query($query);						
				
				if ($this->db->num_rows()) {
					for ($i=0; $i<$this->db->num_rows(); $i++) {
						$data=$this->db->fetch($i);																								
						$result = array (
							'supplier'		=>	$data->supplier,
							'product_id'	=>	$data->product_id,
							'product_nr'	=>	$data->product_nr,
							'product_name'	=>	$data->product_name,
							'rule_nr'		=>	$data->rule_nr,
							'qtyRule'		=>	$data->qtyRule,
							'used'			=>	$data->used,							
							'unitType'		=>	$data->unit_type	
						);
						$results[] = $result;																						
					}
				}
				//conversion
				$unittype = new Unittype($this->db);
				$unitTypeConverter = new UnitTypeConverter("us gallon");						
				
				$products[0]['supplier'] = $results[0]['supplier'];
				$products[0]['product_id'] = $results[0]['product_id'];
				$products[0]['product_nr'] = $results[0]['product_nr'];
				$products[0]['product_name'] = $results[0]['product_name'];				
				//$products[0]['used'] = $results[0]['used'];
				$products[0]['used'] = $results[0]['quantity_lbs'];
				
				//	TODO: add inventory support later 
				$products[$i]['notUsed'] = 0;
				
				//$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
				
				//$qtyRule = $unitTypeConverter->convertToDefault($results[0]['qtyRule'], $unitypeDetails['description']);
				//$results[0]['qtyRule'] = $qtyRule;
				
				//$used = $unitTypeConverter->convertToDefault($results[0]['used'], $unitypeDetails['description']);
				//$products[0]['used'] = $used;
				//end of conversion
				
				$k=0;											
				for($i=1; $i < count($results); $i++) {
					//$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
					//$qtyRule = $unitTypeConverter->convertToDefault($results[$i]['qtyRule'], $unitypeDetails['description']);
					//$results[$i]['qtyRule'] = $qtyRule;							
					if ($results[$i]['product_nr'] != $products[$k]['product_nr']) {
						$k++;
						$products[$k]['supplier'] = $results[$i]['supplier'];
						$products[$k]['product_id'] = $results[$i]['product_id'];
						$products[$k]['product_nr'] = $results[$i]['product_nr'];
						$products[$k]['product_name'] = $results[$i]['product_name'];
						
						//$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
						//$used = $unitTypeConverter->convertToDefault($results[$i]['used'], $unitypeDetails['description']);
						//$products[$k]['used'] = $used;																	
						$products[$k]['used'] = $results[$i]['quantity_lbs'];

						//	TODO: add inventory support later 
						$products[$i]['notUsed'] = 0;
					}
				}
				
				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($this->categoryID);
				
				$facility = new Facility($this->db);
				$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
				
				$company = new Company($this->db);
				$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
				
				$orgDetails['company'] = $companyDetails;
				$orgDetails['facility'] = $facilityDetails;
				$orgDetails['department'] = $departmentDetails;
				
				//getting product quantities in inventory
				//	TODO: add inventory suppport later
		/*		foreach ($products as $value) {
					$productString .= $value['product_id']. ","; 
				}		
				$productString = substr($productString,0,-1);												
				
				$query = "SELECT pg.product_id, sum( pg.quantity ) quantity " .
					"FROM inventory i, productgroup pg " .
					"WHERE i.inventory_id = pg.inventory_id " .
					"AND i.facility_id = " . $departmentDetails['facility_id'] . " " .
					"AND pg.product_id IN (" . $productString . ")" .
					"GROUP BY pg.product_id";
				$this->db->query($query);												
				if ($this->db->num_rows()) {
					for ($i=0; $i<$this->db->num_rows(); $i++) {
						$data=$this->db->fetch($i);																								
						$inventoryQty = array (									
							'product_id'	=>	$data->product_id,
							'quantity'	=>	$data->quantity																							
						);
						$inventoryQties[] = $inventoryQty;																						
					}
				}											
				//group data
				for ($i=0; $i<count($products); $i++) {						
					$diff = 0;
					foreach ($inventoryQties as $inventoryQty) {
						if ($inventoryQty['product_id'] == $products[$i]['product_id']) {
							$diff = $inventoryQty['quantity'] - $products[$i]['used'];
						}	
					}
					if ($diff > 0) {
						$products[$i]['notUsed'] = $diff;
					} else {
						$products[$i]['notUsed'] = 0;
					}														
				}*/
				
				$this -> createXML($products,$results,$orgDetails,$fileName);					
				break;
		}
	}
	
	public function createXML($products,$results,$orgDetails,$fileName) {
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
			$doc->createTextNode("Product Usage by Rule Summary")
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
		
		$productsTag = $doc->createElement( "products" );					
		foreach ($products as $product) {
			$productTag = $doc->createElement( "product" );		
			
			$supplierTag = $doc->createElement( "supplier" );
			$supplierTag->appendChild(
				$doc->createTextNode( html_entity_decode ($product['supplier']))
			);
			$productTag->appendChild( $supplierTag );
			
			$productCodeTag = $doc->createElement( "productCode" );
			$productCodeTag->appendChild(
				$doc->createTextNode( html_entity_decode ($product['product_nr']))
			);
			$productTag->appendChild( $productCodeTag );
			
			$productNameTag = $doc->createElement( "productName" );
			$productNameTag->appendChild(
				$doc->createTextNode( html_entity_decode ($product['product_name']))
			);
			$productTag->appendChild( $productNameTag );
			
			$rulesTag = $doc->createElement( "rules" );
			foreach ($results as $result) {
				if($result['product_nr'] == $product['product_nr']) {
					
					$ruleTag = $doc->createElement( "rule" );
					
					$nameTag = $doc->createElement( "name" );
					$nameTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['rule_nr']))
					);
					$ruleTag->appendChild( $nameTag );

					$rulesTag->appendChild( $ruleTag );
				} 				
			}			
			$productTag->appendChild( $rulesTag );
			
			$usedTag = $doc->createElement( "used" );
			$usedTag->appendChild(
				$doc->createTextNode( html_entity_decode ($product['used']))
			);
			$productTag->appendChild( $usedTag );
			
			$notUsedTag = $doc->createElement( "notUsed" );
			$notUsedTag->appendChild(
				$doc->createTextNode( html_entity_decode ($product['notUsed']))
			);
			$productTag->appendChild( $notUsedTag );
			
			$productsTag ->appendChild( $productTag );
			
		}
		$page->appendChild( $productsTag );
		$doc->save($fileName);			
	}
	
	private function group($query,$categoryType,$categoryID) {
		$this->db->query($query);															
		
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																								
				$result = array (
					'supplier'		=>	$data->supplier,
					'product_id'	=>	$data->product_id,
					'product_nr'	=>	$data->product_nr,
					'product_name'	=>	$data->product_name,
					'rule_nr'		=>	$data->rule_nr,
					'qtyRule'		=>	$data->qtyRule,
					'used'			=>	$data->used,							
					'unitType'		=>	$data->unit_type,	
					'quantity_lbs'	=>	$data->quantity_lbs
				);
				$results[] = $result;																						
			}
		}
		//conversion
		//$unittype = new Unittype($this->db);
		//$unitTypeConverter = new UnitTypeConverter("us gallon");						
		
		$products[0]['supplier'] = $results[0]['supplier'];
		$products[0]['product_id'] = $results[0]['product_id'];
		$products[0]['product_nr'] = $results[0]['product_nr'];
		$products[0]['product_name'] = $results[0]['product_name'];
		//$products[0]['used'] = $results[0]['used'];
		$products[0]['used'] = $results[0]['quantity_lbs'];
				
		//	TODO: add inventory support later 
		$products[0]['notUsed'] = '--';
		
		//$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
		
		//$qtyRule = $unitTypeConverter->convertToDefault($results[0]['qtyRule'], $unitypeDetails['description']);
		//$results[0]['qtyRule'] = $qtyRule;
		
		//$used = $unitTypeConverter->convertToDefault($results[0]['used'], $unitypeDetails['description']);
		//$products[0]['used'] = $used;
		//end of conversion
		
		$k=0;											
		for($i=1; $i < count($results); $i++) {
			//$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
			//$qtyRule = $unitTypeConverter->convertToDefault($results[$i]['qtyRule'], $unitypeDetails['description']);
			//$results[$i]['qtyRule'] = $qtyRule;							
			if ($results[$i]['product_nr'] != $products[$k]['product_nr']) {
				$k++;
				$products[$k]['supplier'] = $results[$i]['supplier'];
				$products[$k]['product_id'] = $results[$i]['product_id'];
				$products[$k]['product_nr'] = $results[$i]['product_nr'];
				$products[$k]['product_name'] = $results[$i]['product_name'];
				
				//$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
				//$used = $unitTypeConverter->convertToDefault($results[$i]['used'], $unitypeDetails['description']);
				//$products[$k]['used'] = $used;																	
				$products[$k]['used'] = $results[$i]['quantity_lbs'];
			}
		}
		
		//getting product quantities in inventory
		//TODO: LATER
		/*foreach ($products as $value) {
			$productString .= $value['product_id']. ","; 
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
				
				$query = "SELECT pg.product_id, sum( pg.quantity ) quantity " .
					"FROM inventory i, productgroup pg " .
					"WHERE i.inventory_id = pg.inventory_id " .
					"AND i.facility_id IN (" . $facilityString . ") " .
					"AND pg.product_id IN (" . $productString . ")" .
					"GROUP BY pg.product_id";
				break;
			case "facility":
				$query = "SELECT pg.product_id, sum( pg.quantity ) quantity " .
					"FROM inventory i, productgroup pg " .
					"WHERE i.inventory_id = pg.inventory_id " .
					"AND i.facility_id = " . $categoryID . " " .
					"AND pg.product_id IN (" . $productString . ")" .
					"GROUP BY pg.product_id";
				break;
		}
		
		$this->db->query($query);						
		
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																								
				$inventoryQty = array (									
					'product_id'	=>	$data->product_id,
					'quantity'	=>	$data->quantity																							
				);
				$inventoryQties[] = $inventoryQty;																						
			}
		}											
		//group data
		for ($i=0; $i<count($products); $i++) {						
			$diff = 0;
			foreach ($inventoryQties as $inventoryQty) {
				if ($inventoryQty['product_id'] == $products[$i]['product_id']) {
					$diff = $inventoryQty['quantity'] - $products[$i]['used'];
				}	
			}
			if ($diff > 0) {
				$products[$i]['notUsed'] = $diff;
			} else {
				$products[$i]['notUsed'] = 0;
			}							 
		}*/
		
		$out['products'] = $products;
		$out['results'] = $results;
		
		return $out;
	}
}
?>