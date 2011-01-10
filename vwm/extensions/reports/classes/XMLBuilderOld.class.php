<?php

class XMLBuilder {
	private $db;
	
	function XMLBuilder($db) {		
		$this->db = $db;
	}
	
	public function BuildXML($reportRequest, $xmlFileName) {
		
		$reportType = $reportRequest->getReportType();
		$categoryType = $reportRequest->getCategoryType();
		$categoryID = $reportRequest->getCategoryID();
		$frequency = $reportRequest->getFrequency();
		$dateBegin = $reportRequest->getDateBegin();
		$dateEnd = $reportRequest->getDateEnd();
		$extraVar = $reportRequest->getExtraVar();
		
		$fileName = $xmlFileName;

		switch ($reportType) {
		
			case "toxicCompounds":
				switch ($categoryType) {
					case "company":
						//$this->db->select_db(DB_NAME);
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);
						
						$query = "SELECT e.equip_desc, c.description, c.cas, cg.type, c.sara313, c.caab2588, mg.unit_type, MIN( mg.quantity ) low, MAX( mg.quantity ) high, AVG( mg.quantity ) avg, SUM( mg.quantity ) total " .
							"FROM product p, components_group cg, component c, mixgroup mg, mix m, department d, equipment e " .
							"WHERE cg.product_id = p.product_id " .
							"AND p.product_id = mg.product_id " .
							"AND mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND cg.component_id = c.component_id " .
							"AND m.equipment_id = e.equipment_id " .
							"AND d.facility_id IN (" . $facilityString . ") " .
							"GROUP BY m.equipment_id, c.description, c.cas";
						
						$in = $this->groupToxicCompounds($query);
						
						//getting company name
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($categoryID);
						$orgDetails['company'] = $companyDetails;
						
						$this->toxicCompoundsCreateXML($orgDetails,$in['equipments'],$in['output'],$fileName);
						break;
						
					case "facility":
						//$this->db->select_db(DB_NAME);
						$query = "SELECT e.equip_desc, c.description, c.cas, cg.type, c.sara313, c.caab2588, mg.unit_type, MIN( mg.quantity ) low, MAX( mg.quantity ) high, AVG( mg.quantity ) avg, SUM( mg.quantity ) total " .
							"FROM product p, components_group cg, component c, mixgroup mg, mix m, department d, equipment e " .
							"WHERE cg.product_id = p.product_id " .
							"AND p.product_id = mg.product_id " .
							"AND mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND cg.component_id = c.component_id " .
							"AND m.equipment_id = e.equipment_id " .
							"AND d.facility_id = " . $categoryID . " " .
							"GROUP BY m.equipment_id, c.description, c.cas";
						
						$in = $this->groupToxicCompounds($query);
						
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;																
						
						$this->toxicCompoundsCreateXML($orgDetails,$in['equipments'],$in['output'],$fileName);
						break;
						
					case "department":
						//$this->db->select_db(DB_NAME);
						$query = "SELECT e.equip_desc, c.description, c.cas, cg.type, c.sara313, c.caab2588, mg.unit_type, MIN( mg.quantity ) low, MAX( mg.quantity ) high, AVG( mg.quantity ) avg, SUM( mg.quantity ) total " .
							"FROM product p, components_group cg, component c, mixgroup mg, mix m, equipment e " .
							"WHERE cg.product_id = p.product_id " .
							"AND p.product_id = mg.product_id " .
							"AND mg.mix_id = m.mix_id " .								 
							"AND cg.component_id = c.component_id " .
							"AND m.equipment_id = e.equipment_id " .
							"AND m.department_id = " . $categoryID . " " .
							"GROUP BY m.equipment_id, c.description, c.cas";						
						
						$in = $this->groupToxicCompounds($query);
						
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
						$facility = new Facility($this->db);
						$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;
						$orgDetails['department'] = $departmentDetails;
						
						$this->toxicCompoundsCreateXML($orgDetails,$in['equipments'],$in['output'],$fileName);							
						break;
				}
				break;
				
			case "productQuants":	//Product List Report
			
				$agencyStr = "1, 30"; //CAOSHA and SCAQMD
				switch ($categoryType) {
				
					case "company":
						//$this->db->select_db(DB_NAME);
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);
						
						//old query without dates
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .							
							"AND ab.agency_id IN (" . $agencyStr . ") " .				
							"AND f.facility_id IN (" . $facilityString . ") " .							
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";*/
						//	old query with error date region	
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab, equipment e " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .
							"AND m.equipment_id = e.equipment_id " .							
							"AND ab.agency_id IN (" . $agencyStr . ") " .				
							"AND f.facility_id IN (" . $facilityString . ") " .
							"AND m.creation_time > DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
							"AND e.expire < " . strtotime($dateEnd) . " " .						
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";*/
							
						$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .												
							"AND ab.agency_id IN (" . $agencyStr . ") " .				
							"AND f.facility_id IN (" . $facilityString . ") " .
							"AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
								"AND DATE_FORMAT('" . date("Y-m-d", strtotime($dateEnd)). "','%Y-%m-%d') " .													
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";
								
						$in = $this->groupProductQuants($query);												
						
						//getting company name
						$company = new Company($this->db);
						//$companyDetails = $company -> getCompanyDetails($categoryID);
						$companyDetails = $company -> getCompanyDetails($categoryID);
						//$orgDetails["type"] = "company";
						$orgDetails['company'] = $companyDetails; 
						
						$this->productQuantsCreateXML($in['mps'],$in['results'],$orgDetails,$dateBegin,$dateEnd,$fileName);
						
						break;
						
					case "facility":
						//$this->db->select_db(DB_NAME);											
						//	old query with error date region	
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab, equipment e " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .
							"AND m.equipment_id = e.equipment_id " .							
							"AND ab.agency_id IN (" . $agencyStr . ") " .		
							"AND f.facility_id = " . $categoryID . " " .
							"AND m.creation_time > DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
							"AND e.expire < " . strtotime($dateEnd) . " " .						
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";*/
							
						$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .												
							"AND ab.agency_id IN (" . $agencyStr . ") " .		
							"AND f.facility_id = " . $categoryID . " " .
							"AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
								"AND DATE_FORMAT('" . date("Y-m-d", strtotime($dateEnd)). "','%Y-%m-%d') " .						
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";
						
						$in = $this->groupProductQuants($query);
																		
						//getting company name
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;
						
						$this->productQuantsCreateXML($in['mps'],$in['results'],$orgDetails,$dateBegin,$dateEnd,$fileName);
						break;
						
					case "department":
						//$this->db->select_db(DB_NAME);						
						//	old query with error date region		
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab, equipment e " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .
							"AND m.equipment_id = e.equipment_id " .							
							"AND ab.agency_id IN (" . $agencyStr . ") " .
							"AND d.department_id = " . $categoryID . " " .
							"AND m.creation_time > DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
							"AND e.expire < " . strtotime($dateEnd) . " " .						
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";*/					
						
						$query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
							"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
							"WHERE m.mix_id = mg.mix_id and " .
							"m.department_id = d.department_id and " .
							"mg.product_id = p.product_id and " .
							"d.facility_id = f.facility_id and " .
							"p.product_id = cg.product_id " .
							"AND cg.component_id = ab.component_id " .													
							"AND ab.agency_id IN (" . $agencyStr . ") " .
							"AND d.department_id = " . $categoryID . " " .
							"AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($dateBegin)). "','%Y-%m-%d') " .
								"AND DATE_FORMAT('" . date("Y-m-d", strtotime($dateEnd)). "','%Y-%m-%d') " .			
							"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";
						$in = $this->groupProductQuants($query);						
						
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
						$facility = new Facility($this->db);
						$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;
						$orgDetails['department'] = $departmentDetails;
						
						$this->productQuantsCreateXML($in['mps'],$in['results'],$orgDetails,$dateBegin,$dateEnd,$fileName);
						break;
				}						
				break;
			case "vocLogs":
			
				$reportData = $extraVar['data'];
				//	get rule name
				$ruleObj = new Rule($this->db);
				$ruleDetails = $ruleObj->getRuleDetails($extraVar['rule'], true);
				$rule = $ruleDetails['rule_nr'];	
				
				switch ($categoryType) {
				
					case "company":
						//get data					
						//$this->db->select_db(DB_NAME);
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);																										

						$query = "SELECT e.equipment_id, e.equip_desc, e.permit, f.epa " .
							"FROM mix m, department d, equipment e, facility f " .
							"WHERE m.department_id = d.department_id " .
							"AND m.equipment_id = e.equipment_id " .
							"AND d.facility_id = f.facility_id " .							
							"AND m.rule_id = " . $extraVar['rule'] . " " .
							"AND d.facility_id in (" . $facilityString . ")  " .
							"GROUP BY e.equip_desc, e.permit, f.epa";
												
						$in = $this->groupVocLogs($query, $dateBegin, $dateEnd, $extraVar['rule']);					 												
					
						$company = new Company($this->db);
						$orgDetails = $company->getCompanyDetails($categoryID);
						$orgDetails["type"] = "company";
						
						//xml generation
						$this->vocLogsCreateXML($orgDetails, $rule, $in['equipments'], $in['days'], $fileName, $reportData);
						
						break;
						
					case "facility":
						//$this->db->select_db(DB_NAME);												
						
						$query = "SELECT e.equipment_id, e.equip_desc, e.permit, e.expire, f.epa " .
							"FROM mix m, department d,  equipment e, facility f " .
							"WHERE m.department_id = d.department_id " .
							"AND m.equipment_id = e.equipment_id " .
							"AND d.facility_id = f.facility_id " .							
							"AND m.rule_id = " . $extraVar['rule'] . " " .
							"AND d.facility_id = " . $categoryID . " " .
							"GROUP BY e.equip_desc, e.permit, f.epa";
	
						$in = $this->groupVocLogs($query, $dateBegin, $dateEnd, $extraVar['rule']);

						$facility = new Facility($this->db);
						$orgDetails = $facility->getFacilityDetails($categoryID);
						$orgDetails["type"] = "facility";																											
						
						//xml generation
						$this->vocLogsCreateXML($orgDetails, $rule, $in['equipments'], $in['days'], $fileName, $reportData);					
						break;
						
					case "department":
						//$this->db->select_db(DB_NAME);																			
													
						$query = "SELECT e.equipment_id, e.equip_desc, e.permit, f.epa " .
							"FROM mix m, equipment e, department d, facility f " .
							"WHERE m.equipment_id = e.equipment_id " .
							"AND m.department_id = d.department_id " .
							"AND d.facility_id = f.facility_id " .													 
							"AND m.rule_id = " . $extraVar['rule'] . " " .
							"AND m.department_id = " . $categoryID . " " .
							"GROUP BY e.equip_desc, e.permit, f.epa";
							
						$in = $this->groupVocLogs($query, $dateBegin, $dateEnd, $extraVar['rule']);
					
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						$orgDetails["dep"] = $departmentDetails;
						$facility = new Facility($this->db);
						$orgDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						$orgDetails["type"] = "facility";												
						
						//xml generation
						$this->vocLogsCreateXML($orgDetails,$rule,$in['equipments'],$in['days'], $fileName,$reportData);				
						
						break;
				}
				
				break;		
				
			case "mixQuantRule":				
				switch ($categoryType) {
				
					case "company":
						//$this->db->select_db(DB_NAME);
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);
						
						$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type " .
							"FROM product p, components_group cg, mixgroup mg, mix m, department d, rule r, supplier s " .
							"WHERE cg.product_id = p.product_id " .
							"AND p.product_id = mg.product_id " .
							"AND mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND cg.rule_id = r.rule_id " .
							"AND p.supplier_id = s.supplier_id " .
							"AND d.facility_id IN  (" . $facilityString . ") " .
							"GROUP BY p.product_nr, p.name, cg.rule_id, r.rule_nr";
						
						$in = $this->groupMixQuantRule($query,$categoryType,$categoryID);
						
						//getting company name
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($categoryID);
						$orgDetails['company'] = $companyDetails;
						
						$this -> mixQuantRuleCreateXML($in['products'],$in['results'],$orgDetails,$fileName);			
						break;
						
					case "facility":
						//$this->db->select_db(DB_NAME);															
						
						$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type " .
							"FROM product p, components_group cg, mixgroup mg, mix m, department d, rule r, supplier s " .
							"WHERE cg.product_id = p.product_id " .
							"AND p.product_id = mg.product_id " .
							"AND mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND cg.rule_id = r.rule_id " .
							"AND p.supplier_id = s.supplier_id " .
							"AND d.facility_id = " . $categoryID . " " .
							"GROUP BY p.product_nr, p.name, cg.rule_id, r.rule_nr";										
						
						$in = $this->groupMixQuantRule($query,$categoryType,$categoryID);
						
						//getting company name
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;																
						
						$this -> mixQuantRuleCreateXML($in['products'],$in['results'],$orgDetails,$fileName);
						break;
						
					case "department":
						//$this->db->select_db(DB_NAME);															
						
						$query ="SELECT s.supplier, p.product_id, p.product_nr, p.name product_name, r.rule_nr, sum(mg.quantity) qtyRule, sum(mg.quantity) used, mg.unit_type " .
							"FROM product p, components_group cg, mixgroup mg, mix m, rule r, supplier s " .
							"WHERE cg.product_id = p.product_id " .
							"AND p.product_id = mg.product_id " .
							"AND mg.mix_id = m.mix_id " .								
							"AND cg.rule_id = r.rule_id " .
							"AND p.supplier_id = s.supplier_id " .
							"AND m.department_id = " . $categoryID . " " .
							"GROUP BY p.product_nr, p.name, cg.rule_id, r.rule_nr";											
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
						$products[0]['used'] = $results[0]['used'];
						
						$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
						
						$qtyRule = $unitTypeConverter->convertToDefault($results[0]['qtyRule'], $unitypeDetails['description']);
						$results[0]['qtyRule'] = $qtyRule;
						
						$used = $unitTypeConverter->convertToDefault($results[0]['used'], $unitypeDetails['description']);
						$products[0]['used'] = $used;
						//end of conversion
						
						$k=0;											
						for($i=1; $i < count($results); $i++) {
							$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
							$qtyRule = $unitTypeConverter->convertToDefault($results[$i]['qtyRule'], $unitypeDetails['description']);
							$results[$i]['qtyRule'] = $qtyRule;							
							if ($results[$i]['product_nr'] != $products[$k]['product_nr']) {
								$k++;
								$products[$k]['supplier'] = $results[$i]['supplier'];
								$products[$k]['product_id'] = $results[$i]['product_id'];
								$products[$k]['product_nr'] = $results[$i]['product_nr'];
								$products[$k]['product_name'] = $results[$i]['product_name'];
								
								$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
								$used = $unitTypeConverter->convertToDefault($results[$i]['used'], $unitypeDetails['description']);
								$products[$k]['used'] = $used;																	
							}
						}
						
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
						$facility = new Facility($this->db);
						$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;
						$orgDetails['department'] = $departmentDetails;
						
						//getting product quantities in inventory
						foreach ($products as $value) {
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
						}
						
						$this -> mixQuantRuleCreateXML($products,$results,$orgDetails,$fileName);					
						break;
				}
				break;	
				
			case "chemClass":
				
				switch ($categoryType) {
				
					case "company":
						//get data
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);	    				    									
						
						//$this->db->select_db(DB_NAME);
						//	query with old hazardous (chemical) system
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum(mg.quantity) quantity, mg.unit_type, hc.class hazard_class ".
							"FROM mixgroup mg, mix m, department d, product p, hazardous_class hc " .
							"WHERE mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id AND p.product_id = mg.product_id " .
							"AND hc.hazardous_class_id = p.hazardous_class_id " .							
							"AND d.facility_id IN (" . $facilityString . ") " .
							"group by hc.class, p.product_nr, p.name, mg.unit_type";*/
							
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

						$in = $this->groupChemClass($query,$categoryType,$categoryID);										
						
						//getting company name
						$company = new Company($this->db);
						$companyDetails = $company->getCompanyDetails($categoryID);
						$orgDetails['company'] = $companyDetails; 
						//Creating XML file  		
						$this -> chemicalClassCreateXML($in['hazardClass'],$in['outputs'],$orgDetails,$fileName);						    			    											
						
						break;
						
					case "facility":
						//get data
						//$this->db->select_db(DB_NAME);
						//	query with old hazardous (chemical) system
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum(mg.quantity) quantity, mg.unit_type, d.name department_name, hc.class hazard_class ".
							"FROM mixgroup mg, mix m, department d, product p, hazardous_class hc " .
							"WHERE mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id AND p.product_id = mg.product_id " .
							"AND hc.hazardous_class_id = p.hazardous_class_id " .
							"AND d.facility_id =" . $categoryID . " " .
							"group by hc.class, p.product_nr, p.name, mg.unit_type, d.name";*/
							
						//	new query with new hazardous (chemical) system
						$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum(mg.quantity) quantity, mg.unit_type, d.name department_name, cc.name hazard_class ".
							"FROM mixgroup mg, mix m, department d, product p, chemical_class cc, product2chemical_class p2cc " .
							"WHERE mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND p.product_id = mg.product_id " .
							"AND p.product_id = p2cc.product_id " .
							"AND p2cc.chemical_class_id = cc.id " .
							"AND d.facility_id = " . $categoryID . " " .
							"group by cc.name, p.product_nr, p.name, mg.unit_type, d.name";
						
						$in = $this->groupChemClass($query,$categoryType,$categoryID);						
						
						//getting company name
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						
						$company = new Company($this->db);
						$companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
						$orgDetails['company'] = $companyDetails;
						$orgDetails['facility'] = $facilityDetails;
						
						//Creating XML file  
						$this -> chemicalClassCreateXML($in['hazardClass'],$in['outputs'],$orgDetails,$fileName);																			    			    																		
						break;
						
					case "department":
						//get data
						//$this->db->select_db(DB_NAME);
						//	query with old hazardous (chemical) system
						/*$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum( mg.quantity ) quantity, mg.unit_type, m.description, hc.class hazard_class " .
							"FROM mixgroup mg, mix m, department d, product p, hazardous_class hc " .
							"WHERE mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND p.product_id = mg.product_id " .
							"AND hc.hazardous_class_id = p.hazardous_class_id " .
							"AND d.department_id =" .$categoryID. " " .
							"GROUP BY hc.class, p.product_nr, p.name, mg.unit_type, m.description";*/
							
						//	new query with new hazardous (chemical) system
						$query = "SELECT p.product_id, p.product_nr, p.name product_name, sum( mg.quantity ) quantity, mg.unit_type, m.description, cc.name hazard_class " .
							"FROM mixgroup mg, mix m, department d, product p, chemical_class cc, product2chemical_class p2cc " .
							"WHERE mg.mix_id = m.mix_id " .
							"AND m.department_id = d.department_id " .
							"AND p.product_id = mg.product_id " .
							"AND p.product_id = p2cc.product_id " .
							"AND p2cc.chemical_class_id = cc.id " .
							"AND d.department_id =" .$categoryID. " " .
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
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
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
						$this -> chemicalClassCreateXML($hazardClass,$outputs,$orgDetails,$fileName);				    			    																													
						break;
				}
				break;
				
			case "exemptCoat":
				switch ($categoryType) {
				
					case "company":
						//$this->db->select_db(DB_NAME);
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);	
						
						// Dates are not used. It's wrong! Fix it. //denis 20 May 2009						
						$period = $this->getPeriodByFrequency($frequency, $extraVar['monthYear']);
						
						//comments may be usefull //denis 19 May 2009
						$query = "SELECT d.facility_id, e.equipment_id, e.permit, p.product_nr, s.supplier, p.name productName, sum(mg.quantity) quantity, mg.unit_type, p.voclx " .
							"FROM mix m, department d, mixgroup mg, product p, supplier s, equipment e " .//,components_group cg " .
							"WHERE d.department_id = m.department_id " .
							"AND m.mix_id = mg.mix_id " .
							"AND mg.product_id = p.product_id " .
							"AND p.supplier_id = s.supplier_id " .
							"AND m.equipment_id = e.equipment_id " .
							//"AND p.product_id = cg.product_id " .
							//"AND cg.rule_id = r.rule_id " .
							"AND (p.name LIKE '%2K%' OR p.name LIKE '%1K%') " .
							//"AND r.rule_nr = '1145' " .
							"AND d.facility_id in (".$facilityString.") " .
							"GROUP BY d.facility_id, e.equipment_id, p.product_nr, s.supplier, p.name, mg.unit_type";
						
						$in = $this->groupExemptCoat($query,$categoryType,$categoryID);
						
						$this->exemptCoatCreateXML($in,$period,$fileName); 						 
						break;
						
					case "facility":				
						//$this->db->select_db(DB_NAME);
						
						// Dates are not used. It's wrong! Fix it. //denis 20 May 2009						
						//$period = $this->getPeriodByFrequency($frequency, $dateBegin);
						$period = $this->getPeriodByFrequency($frequency, $extraVar['monthYear']);
						
						$query = "SELECT d.facility_id, e.equipment_id, e.permit, p.product_nr, s.supplier, p.name productName, sum(mg.quantity) quantity, mg.unit_type, p.voclx " .
							"FROM mix m, department d, mixgroup mg, product p, supplier s, equipment e " .//,components_group cg " .
							"WHERE d.department_id = m.department_id " .
							"AND m.mix_id = mg.mix_id " .
							"AND mg.product_id = p.product_id " .
							"AND p.supplier_id = s.supplier_id " .
							"AND m.equipment_id = e.equipment_id " .
							//"AND p.product_id = cg.product_id " .
							//"AND cg.rule_id = r.rule_id " .
							"AND (p.name LIKE '%2K%' OR p.name LIKE '%1K%') " .
							//"AND r.rule_nr = '1145' " .
							"AND d.facility_id = ".$categoryID." " .
							"GROUP BY d.facility_id, e.equipment_id, p.product_nr, s.supplier, p.name, mg.unit_type";
						
						$in = $this->groupExemptCoat($query,$categoryType,$categoryID);
						
						$this->exemptCoatCreateXML($in,$period,$fileName);
						break;
						
					case "department":				
						//$this->db->select_db(DB_NAME);
						
						// Dates are not used. It's wrong! Fix it. //denis 20 May 2009						
						//$period = $this->getPeriodByFrequency($frequency, $dateBegin);
						$period = $this->getPeriodByFrequency($frequency, $extraVar['monthYear']);						
						
						$query = "SELECT d.facility_id, e.equipment_id, e.permit, p.product_nr, s.supplier, p.name productName, sum(mg.quantity) quantity, mg.unit_type, p.voclx " .
							"FROM mix m, department d, mixgroup mg, product p, supplier s, equipment e " .//,components_group cg " .
							"WHERE d.department_id = m.department_id " .
							"AND m.mix_id = mg.mix_id " .
							"AND mg.product_id = p.product_id " .
							"AND p.supplier_id = s.supplier_id " .
							"AND m.equipment_id = e.equipment_id " .
							//"AND p.product_id = cg.product_id " .
							//"AND cg.rule_id = r.rule_id " .
							"AND (p.name LIKE '%2K%' OR p.name LIKE '%1K%') " .
							//"AND r.rule_nr = '1145' " .
							"AND d.department_id = ".$categoryID." " .
							"GROUP BY d.facility_id, e.equipment_id, p.product_nr, s.supplier, p.name, mg.unit_type";
						
						$in = $this->groupExemptCoat($query,$categoryType,$categoryID);
						
						$this->exemptCoatCreateXML($in,$period,$fileName);
						break;
				}
				break;
				
			case "projectCoat":
				//$this->db->select_db(DB_NAME);
				
				switch ($categoryType) {
					case "company":
						$company = new Company($this->db);						
						$companyDetails = $company -> getCompanyDetails($categoryID);
						$orgName = $companyDetails['name']; 
						break;
					case "facility":
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						$orgName = $facilityDetails['name']; 
						break;
					case "department":
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
						$facility = new Facility($this->db);
						$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						$orgName = $facilityDetails['name'];
						break;
				}
				
				$orgName = strtoupper($orgName);
				
				$this->db->query("SELECT rule_nr FROM rule WHERE rule_id = ".$extraVar['rule']);						
				$data=$this->db->fetch(0);
				$rule = $data->rule_nr;		
				
				$period = $this->getPeriodByFrequency($frequency, $extraVar['monthYear']);														
				
				$reportData = $extraVar['data'];				
				for ($i=1;$i<=3;$i++) {													
					if (!empty($reportData['supplier'.$i])) {
						$this->db->query("SELECT * FROM supplier WHERE supplier_id = ".$reportData['supplier'.$i]);						
						$data=$this->db->fetch(0);																																			
						$supplier[$i] = array (
							'supplier'		=>	$data->supplier,
							'contactPerson'	=>	$data->contact_person,
							'phone'			=>	$data->phone																								
						);																										
					}
					
				}			
				
				$this->projectCoatCreateXML($rule,$period,$reportData,$supplier,$orgName,$fileName);			
				break;
				
			case "VOCbyRules":
				//$this->db->select_db(DB_NAME);
				//$dateBegin = '2009-04-20';
				//$dateEnd = '2010-04-20';
				switch ($categoryType) {
					case "company":
						$company = new Company($this->db);						
						$companyDetails = $company -> getCompanyDetails($categoryID);
						$orgInfo = array(
							'details' => $companyDetails,
							'category' => "Company",
							'notes' => ""							
						); 
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);

						$query = "SELECT m.voc, r.rule_nr ".
							"FROM mix m, department d, rule r ".
							"WHERE d.facility_id in (".$facilityString.") ".
							"AND d.department_id = m.department_id ".
							"AND m.rule_id = r.rule_id ";
						break;
					case "facility":
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						$orgInfo = array(
							'details' => $facilityDetails,
							'category' => "Facility",
							'notes' => ""
						); 

						$query="SELECT m.voc, r.rule_nr ".
							"FROM mix m, department d, rule r ".
							"WHERE d.facility_id = ".$categoryID." ".
							"AND d.department_id = m.department_id ".
							"AND m.rule_id = r.rule_id ";
						break;
					case "department":
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
						$facility = new Facility($this->db);
						$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						$orgInfo = array(
							'details' => $facilityDetails,
							'category' => "Department",
							'name' => $departmentDetails['name'],
							'notes' => ""
						); 
						$query="SELECT m.voc, r.rule_nr ".
							"FROM mix m, rule r ".
							"WHERE m.department_id = ".$categoryID." ".
							"AND m.rule_id = r.rule_id ";
						break;
				}
//				$ruleQuery = $this->GetRuleQuery($categoryType, $categoryID);
				$ruleQuery = "SELECT r.rule_id, r.rule_nr ".
					"FROM rule r";
					
				$voc_arr = $this->groupVOCbyRules($query, $ruleQuery, $dateBegin, $dateEnd);
				$DatePeriod = "From ".date("Y-m-d",strtotime($dateBegin))." To ".date("Y-m-d",strtotime($dateEnd));
			
				$this->VOCbyRulesCreateXML($voc_arr, $orgInfo, $DatePeriod, $fileName);			
				break;
				
			case "SummVOC":
				//$this->db->select_db(DB_NAME);
			//	$dateBegin = '2009-04-20';
			//	$dateEnd = '2010-04-20';
				switch ($categoryType) {
					case "company":
						$company = new Company($this->db);						
						$companyDetails = $company -> getCompanyDetails($categoryID);
						$orgInfo = array(
							'details' => $companyDetails,
							'category' => "Company",
							'notes' => ""							
						); 
						
						$facility = new Facility($this->db);
						$facilityList = $facility->getFacilityListByCompany($categoryID);						
						foreach ($facilityList as $value) {
							$facilityString .= $value['id']. ","; 
						}		
						$facilityString = substr($facilityString,0,-1);

						$query = "SELECT m.voc, r.rule_nr ".
							"FROM mix m, department d, rule r ".
							"WHERE d.facility_id in (".$facilityString.") ".
							"AND d.department_id = m.department_id ".
							"AND m.rule_id = r.rule_id ";
						break;
					case "facility":
						$facility = new Facility($this->db);    				
						$facilityDetails = $facility->getFacilityDetails($categoryID);
						$orgInfo = array(
							'details' => $facilityDetails,
							'category' => "Facility",
							'notes' => ""
						); 
						
						$query="SELECT m.voc, r.rule_nr ".
							"FROM mix m, department d, rule r ".
							"WHERE d.facility_id = ".$categoryID." ".
							"AND d.department_id = m.department_id ".
							"AND m.rule_id = r.rule_id ";
						break;
					case "department":
						$department = new Department($this->db);
						$departmentDetails = $department -> getDepartmentDetails($categoryID);
						
						$facility = new Facility($this->db);
						$facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
						$orgInfo = array(
							'details' => $facilityDetails,
							'category' => "Department",
							'name' => $departmentDetails['name'],
							'notes' => ""
						); 
						$query="SELECT m.voc, r.rule_nr ".
							"FROM mix m, rule r ".
							"WHERE m.department_id = ".$categoryID." ".
							"AND m.rule_id = r.rule_id ";
						break;
				}
//				$ruleQuery = $this->GetRuleQuery($categoryType, $categoryID);
				$ruleQuery = "SELECT r.rule_id, r.rule_nr ".
					"FROM rule r";		
					
				$voc_arr= $this->groupSummVOC($query, $ruleQuery, $dateBegin, $dateEnd);
				$DatePeriod = "From ".date("Y-m-d",strtotime($dateBegin))." To ".date("Y-m-d",strtotime($dateEnd));

				$this->SummVOCCreateXML($voc_arr, $orgInfo, $DatePeriod, $fileName);			
				break;
		}
		return $fileName;
	}
	
	private function chemicalClassCreateXML($hazardClass,$outputs,$orgDetails,$fileName) {
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
			$doc->createTextNode($orgDetails["company"]["name"])
		);
		$company -> appendChild( $companyName );
		
		$companyAddress = $doc->createElement("companyAddress" );
		$companyAddress->appendChild(
			$doc->createTextNode($orgDetails["company"]["address"].", ".$orgDetails["company"]["city"].", ".$orgDetails["company"]["zip"])
		);
		$company->appendChild( $companyAddress );
		
		if (isset($orgDetails["facility"])) {
			$facility = $doc->createElement("facility");  						
			$page->appendChild( $facility );
			
			$facilityName = $doc->createElement( "facilityName" );
			$facilityName->appendChild(
				$doc->createTextNode($orgDetails["facility"]["name"])
			);
			$facility -> appendChild( $facilityName );
			
			$facilityAddress = $doc->createElement("facilityAddress" );
			$facilityAddress->appendChild(
				$doc->createTextNode($orgDetails["facility"]["address"].", ".$orgDetails["facility"]["city"].", ".$orgDetails["facility"]["zip"])
			);
			$facility->appendChild( $facilityAddress );
		}
		
		if (isset($orgDetails["department"])) {
			$department = $doc->createElement("department");  						
			$page->appendChild( $department );
			
			$departmentName = $doc->createElement( "departmentName" );
			$departmentName->appendChild(
				$doc->createTextNode($orgDetails["department"]["name"])
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
				$doc->createTextNode($hc)
			);
			$hazard->appendChild($hazardName);		
			
			foreach( $outputs as $output ) {
				if ($hc == $output['hazardClass']) {  									  																			
					$item = $doc->createElement( "item" );
					
					$commonName = $doc->createElement( "commonName" );
					$commonName->appendChild(
						$doc->createTextNode( $output['commonName'] )
					);
					$item->appendChild( $commonName );
					
					$chemicalName = $doc->createElement( "chemicalName" );
					$chemicalName->appendChild(
						$doc->createTextNode( $output['chemicalName'] )
					);
					$item->appendChild($chemicalName );
					
					$amount = $doc->createElement( "amount" );
					$amount->appendChild(
						$doc->createTextNode(  round($output['amount'],2)." gal")
					);
					$item->appendChild($amount );
					if (strtoupper($output['locationStorage']) == 'EXTERIOR STORAGE') {
						$totalES += $output['amount'];
					} else {
						$totalIS += $output['amount'];
					}					
					
					$osUse = $doc->createElement( "osUse" );
					$osUse->appendChild(
						$doc->createTextNode(round($output['osuse'],2)." gal")
					);
					$item->appendChild($osUse);
					$totalOS += $output['osuse'];
					
					$csUse = $doc->createElement( "csUse" );
					$csUse->appendChild(
						$doc->createTextNode(round($output['csuse'],2)." gal")
					);
					$item->appendChild($csUse);
					$totalCS += $output['csuse'];
					
					$locationOfStorage = $doc->createElement( "locationOfStorage" );
					if ($output['locationStorage'] != "") {
						$locationOfStorage->appendChild(
							$doc->createTextNode($output['locationStorage'])
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
							$doc->createTextNode(  $output['locationUse'] )
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
	
	private function vocLogsCreateXML($orgDetails, $rule, $equipments, $days, $fileName, $reportData) {

		$doc = new DOMDocument();
		$timeInterval = $doc->formatOutput = true;     							  							  							  						
		
		$pageTag = $doc->createElement( "page" );		
		$doc->appendChild( $pageTag );
		
		$pageOrientation = $doc->createAttribute("orientation");
		$pageOrientation->appendChild(
			$doc->createTextNode("l")
		);
		$pageTag->appendChild($pageOrientation);
		
		$pageTopMargin = $doc->createAttribute("topmargin");
		$pageTopMargin->appendChild(
			$doc->createTextNode("5")
		);
		$pageTag->appendChild($pageTopMargin);
		
		$pageLeftMargin = $doc->createAttribute("leftmargin");
		$pageLeftMargin->appendChild(
			$doc->createTextNode("10")
		);
		$pageTag->appendChild($pageLeftMargin);
		
		$pageRightMargin = $doc->createAttribute("rightmargin");
		$pageRightMargin->appendChild(
			$doc->createTextNode("10")
		);
		$pageTag->appendChild($pageRightMargin);  							  							  										  					
		
		$metaTag = $doc->createElement( "meta" );
		$pageTag->appendChild( $metaTag );
		
		$metaName = $doc->createAttribute("name");
		$metaName->appendChild(
			$doc->createTextNode("basefont")
		);
		$metaTag->appendChild($metaName);
		
		$metaValue = $doc->createAttribute("value");
		$metaValue->appendChild(
			$doc->createTextNode("times")
		);
		$metaTag->appendChild($metaValue);
		
		
		$titleTag = $doc->createElement( "title" );
		$titleTag->appendChild(
			$doc->createTextNode("Daily Emissions Report")
		);
		$pageTag->appendChild( $titleTag );
		
		$periodTag = $doc->createElement( "period" );
		$periodTag->appendChild(
			$doc->createTextNode("PERIOD: " . date('m.d.Y',min($days)) . " TO " . date('m.d.Y',max($days)) )
		);
		$pageTag->appendChild( $periodTag );
		
		$title2Tag = $doc->createElement( "title2" );
		$title2Tag->appendChild(
			$doc->createTextNode("Coating and Solvent Usage")
		);
		$pageTag->appendChild( $title2Tag );
		
		$orgTag = $doc->createElement($orgDetails["type"]);  						
		$pageTag->appendChild( $orgTag );
		
		$orgNameTag = $doc->createElement( $orgDetails["type"] . "Name" );
		$orgNameTag->appendChild(
			$doc->createTextNode($orgDetails["name"])
		);
		$orgTag -> appendChild( $orgNameTag );
		
		$orgAddressTag = $doc->createElement($orgDetails["type"] . "Address" );
		$orgAddressTag->appendChild(
			$doc->createTextNode($orgDetails["address"])
		);
		$orgTag->appendChild( $orgAddressTag );
		
		$orgCityTag = $doc->createElement($orgDetails["type"] . "City" );
		$orgCityTag->appendChild(
			$doc->createTextNode($orgDetails["city"].", ".$orgDetails["state"]. ", ".$orgDetails["zip"])
		);
		$orgTag->appendChild( $orgCityTag );
		
		$orgCountyTag = $doc->createElement($orgDetails["type"] . "County" );
		$orgCountyTag->appendChild(
			$doc->createTextNode($orgDetails["county"])
		);
		$orgTag->appendChild( $orgCountyTag );
		
		$orgPhoneTag = $doc->createElement($orgDetails["type"] . "Phone" );
		$orgPhoneTag->appendChild(
			$doc->createTextNode($orgDetails["phone"])
		);
		$orgTag->appendChild( $orgPhoneTag );
		
		$orgFaxTag = $doc->createElement($orgDetails["type"] . "Fax" );
		$orgFaxTag->appendChild(
			$doc->createTextNode($orgDetails["fax"])
		);
		$orgTag->appendChild( $orgFaxTag );				
		
		$ruleTag = $doc->createElement("rule" );
		$ruleTag->appendChild(
			$doc->createTextNode($rule)
		);						 
		$pageTag->appendChild( $ruleTag );
		
		$gcg = new GCG($this->db);	
		$gcgTag = $doc->createElement("gcg" );
		$gcgTag->appendChild(
			$doc->createTextNode($gcg->getByID($orgDetails["gcg_id"]))
		);						 
		$pageTag->appendChild( $gcgTag );
		
		$notesTag = $doc->createElement("notes");
		$notesTag->appendChild(
			$doc->createTextNode($reportData['notes'])
		);						 
		$pageTag->appendChild( $notesTag );
		
		$responsiblePersonTag = $doc->createElement("responsiblePerson");
		$responsiblePersonTag->appendChild(
			$doc->createTextNode($reportData['responsiblePerson'])
		);						 
		$pageTag->appendChild( $responsiblePersonTag );
		
		$titleManualTag = $doc->createElement("titleManual");
		$titleManualTag->appendChild(
			$doc->createTextNode($reportData['title'])
		);						 
		$pageTag->appendChild( $titleManualTag );
		
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter("us gallon");
		
		$mixObj = new Mix($this->db);
		
		foreach ($equipments as $equipment) {
			
			$summaryEquipmentQty = 0;
			$summaryEquipmentVoc3 = 0;
			$summaryEquipmentTotalVoc = 0;			
			
			$equipmentTag = $doc->createElement( "equipment" );  							
			
			$equipmentName = $doc->createAttribute("name");
			$equipmentName->appendChild(
				$doc->createTextNode($equipment['name'])
			);
			$equipmentTag->appendChild($equipmentName);
			
			$equipPermit = $doc->createAttribute("permitNo");
			$equipPermit->appendChild(
				$doc->createTextNode($equipment['permit'])
			);
			$equipmentTag->appendChild($equipPermit);
			
			
			$facilityIdTag = $doc->createAttribute("facilityID" );
			$facilityIdTag->appendChild(
				$doc->createTextNode($equipment['epa'])
			);
			$equipmentTag->appendChild( $facilityIdTag );			
			foreach ($days as $day) {				
				$dateTag = $doc->createElement( "date" );  							
				
				$dateDay = $doc->createAttribute("day");
				$dateDay->appendChild(
					$doc->createTextNode( date('m.d.Y',$day) )
				);
				$dateTag->appendChild($dateDay);
				$cnt = 0;
				$totalQty = 0;
				$totalVoc3 = 0;
				$totalVoc = 0;

				foreach ($equipment['mixes'] as $mix) {					
					$qtyRatio = array();
					$vocwx = array();
					$sumQty = 0;
					$voc = 0;
					$mixRatio = "";
					$coatAsApplied = 0;
					$ratioSum = 0;

					$isToday = false;
					
					$mix['creationTime'] = str_replace('-','/',$mix['creationTime']);												
					$creationTime = strtotime($mix['creationTime']);					
										
					if ($creationTime > ($day - 86400) && $creationTime < $day ) {
						$isToday = true;

						foreach ($mix['products'] as $product) {
							$cnt++;																					
							$productTag = $doc->createElement("product" );
							
							$supplierTag = $doc->createElement("supplier" );
							$supplierTag->appendChild(
								$doc->createTextNode($product["supplier"])							
							);
							$productTag->appendChild( $supplierTag );
							
							$product_nrTag = $doc->createElement("productNo" );
							$product_nrTag->appendChild(
								$doc->createTextNode($product["product_nr"])
							);
							$productTag->appendChild( $product_nrTag );
							
							$product_nameTag = $doc->createElement("coatingSingle" );
							$product_nameTag->appendChild(
								$doc->createTextNode($product["description"]." ".$product["coatDesc"])
							);
							$productTag->appendChild( $product_nameTag );
							
							$voclxTag = $doc->createElement("vocOfMaterial" );
							$voclxTag->appendChild(
								$doc->createTextNode($product["voclx"])
							);
							$productTag->appendChild( $voclxTag );
							
							$vocwxTag = $doc->createElement("voc2" );
							$vocwxTag->appendChild(
								$doc->createTextNode($product["vocwx"])
							);
							$productTag->appendChild( $vocwxTag );
							
							$quantityTag = $doc->createElement("qtyUsed" );
							
							$unitypeDetails = $unittype->getUnittypeDetails($product['unittype']);								
							$qty = $unitTypeConverter->convertToDefault($product['quantity'], $unitypeDetails['description']);
							$qty = round($qty,2);
							
							$sumQty += $qty; 							
							$qtyRatio[]= $qty*100;
							$vocwx[]= $product['vocwx'];
							
							$quantityTag->appendChild(							
								$doc->createTextNode($qty)
							);				
							$productTag->appendChild( $quantityTag );
							
							$voc = $mix['voc'];					//	move down
							$exemptRule = $mix['exemptRule'];	//	move down
							
							$dateTag->appendChild( $productTag );
						}						
					}								

					if ($cnt != 0) {

						if ($isToday) {
							$lcm = $this->lcm_nums($qtyRatio);
							for($j=0;$j < count($qtyRatio);$j++) {							
								$mixRatio .= $lcm/$qtyRatio[$j] . ":";
								$coatAsApplied += $vocwx[$j]*($lcm/$qtyRatio[$j]);							
								$ratioSum += $lcm/$qtyRatio[$j]; 																				
							}
							$mixRatio = substr($mixRatio,0,-1);												
							$coatAsApplied = $coatAsApplied/$ratioSum;
							
							$totalOnProjectTag = $doc->createElement("totalOnProject" );					
							
							$labelAttr = $doc->createAttribute("label" );					
							$labelAttr->appendChild(								
								$doc->createTextNode("Total Used on Project# ".$mix['description'])
							);
							$totalOnProjectTag->appendChild( $labelAttr );
							
							$mixRatioAttr = $doc->createAttribute("mixRatio" );																		
							$mixRatioAttr->appendChild(
								$doc->createTextNode($mixRatio)
							);															
							$totalOnProjectTag->appendChild( $mixRatioAttr );
							
							$qtyAttr = $doc->createAttribute("qty" );					
							$qtyAttr->appendChild(
								$doc->createTextNode($sumQty)
							);
							$totalOnProjectTag->appendChild( $qtyAttr );
							$totalQty += $sumQty;
							
							$voc3Attr = $doc->createAttribute("voc3" );					
							$voc3Attr->appendChild(
								$doc->createTextNode(round($coatAsApplied,2))
							);
							$totalOnProjectTag->appendChild( $voc3Attr );
							$totalVoc3 += round($coatAsApplied,2);
							
							$exemptAttr = $doc->createAttribute("exempt" );					
							$exemptAttr->appendChild(
								$doc->createTextNode($exemptRule)					
							);
							$totalOnProjectTag->appendChild( $exemptAttr );
							
							$totalVocAttr = $doc->createAttribute("totalVoc" );					
							$totalVocAttr->appendChild(
								$doc->createTextNode($voc)					
							);
							$totalOnProjectTag->appendChild( $totalVocAttr );
							$totalVoc += $voc;
							
							$dateTag->appendChild( $totalOnProjectTag );	
						}																												
					}								
				}
				if ($cnt == 0) {
					$productTag = $doc->createElement("product" );
					
					$supplierTag = $doc->createElement("supplier" );
					$supplierTag->appendChild(
						$doc->createTextNode("N/A")
					);
					$productTag->appendChild( $supplierTag );
					
					$product_nrTag = $doc->createElement("productNo" );
					$product_nrTag->appendChild(
						$doc->createTextNode("N/A")
					);
					$productTag->appendChild( $product_nrTag );
					
					$product_nameTag = $doc->createElement("coatingSingle" );
					$product_nameTag->appendChild(
						$doc->createTextNode("none")
					);
					$productTag->appendChild( $product_nameTag );
					
					$voclxTag = $doc->createElement("vocOfMaterial" );
					$voclxTag->appendChild(
						$doc->createTextNode("0.00")
					);
					$productTag->appendChild( $voclxTag );
					
					$vocwxTag = $doc->createElement("voc2" );
					$vocwxTag->appendChild(
						$doc->createTextNode("0.00")
					);
					$productTag->appendChild( $vocwxTag );								
					
					$quantityTag = $doc->createElement("qtyUsed" );
					$quantityTag->appendChild(
						$doc->createTextNode("0.00")
					);
					$productTag->appendChild( $quantityTag );										
					
					$dateTag->appendChild( $productTag );
					
					
					$totalOnProjectTag = $doc->createElement("totalOnProject" );					
					
					$labelAttr = $doc->createAttribute("label" );					
					$labelAttr->appendChild(
						$doc->createTextNode("Total Used on Project#")
					);
					$totalOnProjectTag->appendChild( $labelAttr );
					
					$mixRatioAttr = $doc->createAttribute("mixRatio" );																		
					$mixRatioAttr->appendChild(
						$doc->createTextNode(" ")
					);															
					$totalOnProjectTag->appendChild( $mixRatioAttr );
					
					$qtyAttr = $doc->createAttribute("qty" );					
					$qtyAttr->appendChild(
						$doc->createTextNode("0.00")
					);
					$totalOnProjectTag->appendChild( $qtyAttr );
					$totalQty += $sumQty;
					
					$voc3Attr = $doc->createAttribute("voc3" );					
					$voc3Attr->appendChild(
						$doc->createTextNode("0.00")
					);
					$totalOnProjectTag->appendChild( $voc3Attr );
					$totalVoc3 += $coatAsApplied;
					
					$totalVocAttr = $doc->createAttribute("totalVoc" );					
					$totalVocAttr->appendChild(
						$doc->createTextNode("0.00")					
					);
					$totalOnProjectTag->appendChild( $totalVocAttr );
					
					$dateTag->appendChild( $totalOnProjectTag );																
				}
			
				$totalLabelTag = $doc->createElement("totalLabel" );					
				$totalLabelTag->appendChild(
					$doc->createTextNode("Daily total from " . $equipment['name'])
				);
				$dateTag->appendChild( $totalLabelTag );
				
				$totalQtyTag = $doc->createElement("totalQty" );					
				$totalQtyTag->appendChild(
					$doc->createTextNode($totalQty)
				);
				$dateTag->appendChild( $totalQtyTag );
				
				$summaryEquipmentQty += $totalQty;
				
				$totalVoc3Tag = $doc->createElement("totalVoc3" );					
				$totalVoc3Tag->appendChild(
					$doc->createTextNode($totalVoc3)
				);
				$dateTag->appendChild( $totalVoc3Tag );
				
				$summaryEquipmentVoc3 += $totalVoc3;
				
				$totalTotalVocTag = $doc->createElement("totalTotalVoc" );					
				$totalTotalVocTag->appendChild(
					$doc->createTextNode($totalVoc)
				);
				$dateTag->appendChild( $totalTotalVocTag );
				
				$summaryEquipmentTotalVoc += $totalVoc;
				
				$equipmentTag -> appendChild($dateTag);
			}
			// Added 30 May 2009 den
			$summaryEquipmentTag = $doc->createElement("summaryEquipment" );
			
			$summaryEquipmentQtyTag = $doc->createElement("summaryEquipmentQty" );					
			$summaryEquipmentQtyTag->appendChild(
				$doc->createTextNode($summaryEquipmentQty)
			);				
			$summaryEquipmentTag->appendChild( $summaryEquipmentQtyTag );
			$summaryQty[]= $summaryEquipmentQty;
			
			$summaryEquipmentVoc3Tag = $doc->createElement("summaryEquipmentVoc3" );					
			$summaryEquipmentVoc3Tag->appendChild(
				$doc->createTextNode($summaryEquipmentVoc3)
			);				
			$summaryEquipmentTag->appendChild( $summaryEquipmentVoc3Tag );
			$summaryVoc3[] = $summaryEquipmentVoc3;
			
			$summaryEquipmenTotalVocTag = $doc->createElement("summaryEquipmentTotalVoc" );					
			$summaryEquipmenTotalVocTag->appendChild(
				$doc->createTextNode($summaryEquipmentTotalVoc)
			);				
			$summaryEquipmentTag->appendChild( $summaryEquipmenTotalVocTag );
			$summaryTotalVoc[] = $summaryEquipmentTotalVoc;			
			
			$equipmentTag->appendChild( $summaryEquipmentTag );
			//
			
			$pageTag -> appendChild($equipmentTag);
		}				
		
		//summary tags here //den 30 May 2009														
		$summaryTag = $doc->createElement("summary" );		
		for($i=0;$i<count($equipments);$i++) {
			
			$summaryTotalEquipmentTag = $doc->createElement("summaryTotalEquipment" );											
			
				$summaryEquipPar = $doc->createAttribute("equipment" );					
				$summaryEquipPar->appendChild(
					$doc->createTextNode($equipments[$i]['name'])
				);				
				$summaryTotalEquipmentTag->appendChild( $summaryEquipPar );
				
				$summaryQtyPar = $doc->createAttribute("qty" );					
				$summaryQtyPar->appendChild(
					$doc->createTextNode($summaryQty[$i])
				);				
				$summaryTotalEquipmentTag->appendChild( $summaryQtyPar );
			
				$summaryVoc3Par = $doc->createAttribute("voc3" );					
				$summaryVoc3Par->appendChild(
					$doc->createTextNode($summaryVoc3[$i])
				);				
				$summaryTotalEquipmentTag->appendChild( $summaryVoc3Par );
			
				$summaryTotalVocPar = $doc->createAttribute("totalVoc" );					
				$summaryTotalVocPar->appendChild(
					$doc->createTextNode($summaryTotalVoc[$i])
				);				
				$summaryTotalEquipmentTag->appendChild( $summaryTotalVocPar );				
			
			$summaryTag->appendChild( $summaryTotalEquipmentTag );
		}
		
		$summarySumTag = $doc->createElement("summarySum" );
		
			$summarySumQtyPar = $doc->createAttribute("qty" );					
			$summarySumQtyPar->appendChild(
				$doc->createTextNode(array_sum($summaryQty))
			);				
			$summarySumTag->appendChild( $summarySumQtyPar );
		
			$summarySumVoc3Par = $doc->createAttribute("voc3" );					
			$summarySumVoc3Par->appendChild(
				$doc->createTextNode(array_sum($summaryVoc3))
			);				
			$summarySumTag->appendChild( $summarySumVoc3Par );
		
			$summarySumTotalVocPar = $doc->createAttribute("totalVoc" );					
			$summarySumTotalVocPar->appendChild(
				$doc->createTextNode(array_sum($summaryTotalVoc))
			);				
			$summarySumTag->appendChild( $summarySumTotalVocPar );
		
		$summaryTag->appendChild( $summarySumTag );
		
		$pageTag->appendChild( $summaryTag );
		
		$doc->save($fileName);
	}
	
	private function productQuantsCreateXML($inventories,$results,$orgDetails,$dateBegin,$dateEnd,$fileName) {
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
			$doc->createTextNode("MSDS Submittal")
		);
		$page->appendChild( $title );
		
		/*$bookTag = $doc->createElement( "book" );
		$bookTag->appendChild(
			$doc->createTextNode("BOOK-1 MSDS Submittal")
		);
		$page->appendChild( $bookTag );*/
		
		//period added by den 03/June/2009
		
		$periodTag = $doc->createElement( "period" );
		$periodTag->appendChild(
			$doc->createTextNode("From ".date("Y-m-d", strtotime($dateBegin))." To ".date("Y-m-d", strtotime($dateEnd)))
		);
		$page->appendChild( $periodTag );
		
		//company inf added by den 03/June/2009
		$company = $doc->createElement("company");  						
		$page->appendChild( $company );
		
		$companyName = $doc->createElement( "companyName" );
		$companyName->appendChild(
			$doc->createTextNode($orgDetails["company"]["name"])
		);
		$company -> appendChild( $companyName );
		
		$companyAddress = $doc->createElement("companyAddress" );
		$companyAddress->appendChild(
			$doc->createTextNode($orgDetails["company"]["address"].", ".$orgDetails["company"]["city"].", ".$orgDetails["company"]["zip"])
		);
		$company->appendChild( $companyAddress );
		
		if (isset($orgDetails["facility"])) {
			$facility = $doc->createElement("facility");  						
			$page->appendChild( $facility );
			
			$facilityName = $doc->createElement( "facilityName" );
			$facilityName->appendChild(
				$doc->createTextNode($orgDetails["facility"]["name"])
			);
			$facility -> appendChild( $facilityName );
			
			$facilityAddress = $doc->createElement("facilityAddress" );
			$facilityAddress->appendChild(
				$doc->createTextNode($orgDetails["facility"]["address"].", ".$orgDetails["facility"]["city"].", ".$orgDetails["facility"]["zip"])
			);
			$facility->appendChild( $facilityAddress );
		}
		
		if (isset($orgDetails["department"])) {
			$department = $doc->createElement("department");  						
			$page->appendChild( $department );
			
			$departmentName = $doc->createElement( "departmentName" );
			$departmentName->appendChild(
				$doc->createTextNode($orgDetails["department"]["name"])
			);
			$department -> appendChild( $departmentName );						
		}		
		
		foreach ($inventories as $inventory) {
			$r = $doc->createElement( "mpsGroup" );
			
			$equipmentNameTag = $doc->createAttribute("name");
			$equipmentNameTag->appendChild(
				$doc->createTextNode($inventory)
			);
			$r->appendChild($equipmentNameTag);
			
			$page->appendChild( $r );
			$count = 1;
			foreach ($results as $result) {
				if (substr($result['product_nr'],0,2) == $inventory) {
					$group = $doc->createElement( "product" );
					
					$idTag = $doc->createElement( "ID" );
					$idTag->appendChild(
						$doc->createTextNode(substr($result['product_nr'],0,2) ." - " . sprintf("%03s",$count))
					);
					$group->appendChild( $idTag );
					
					$productCodeTag = $doc->createElement( "productCode" );
					$productCodeTag->appendChild(
						$doc->createTextNode($result['product_nr'])
					);
					$group->appendChild( $productCodeTag );
					
					$colorTag = $doc->createElement( "color" );
					$colorTag->appendChild(
						$doc->createTextNode($result['product_name'])
					);
					$group->appendChild( $colorTag );
					
					$materialVocTag = $doc->createElement( "materialVoc" );
					if (isset($result['vocwx'])) {
						$materialVocTag->appendChild(
							$doc->createTextNode($result['vocwx'])
						);	
					} else {
						$materialVocTag->appendChild(
							$doc->createTextNode("N/A")
						);	
					}					
					$group->appendChild( $materialVocTag );
					
					$coatingVocTag = $doc->createElement( "coatingVoc" );
					if (isset($result['voclx'])) {
						$coatingVocTag->appendChild(
							$doc->createTextNode($result['voclx'])
						);	
					} else {
						$coatingVocTag->appendChild(
							$doc->createTextNode("N/A")
						);	
					}
					$group->appendChild( $coatingVocTag );
					
					$r->appendChild( $group );
					
					$count++;		
					
				} else {
					$count = 1;
				}
			}										
		}
		$doc->save($fileName);
	}
	
	private function mixQuantRuleCreateXML($products,$results,$orgDetails,$fileName) {
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
			$doc->createTextNode($orgDetails["company"]["name"])
		);
		$company -> appendChild( $companyName );
		
		$companyAddress = $doc->createElement("companyAddress" );
		$companyAddress->appendChild(
			$doc->createTextNode($orgDetails["company"]["address"].", ".$orgDetails["company"]["city"].", ".$orgDetails["company"]["zip"])
		);
		$company->appendChild( $companyAddress );
		
		if (isset($orgDetails["facility"])) {
			$facility = $doc->createElement("facility");  						
			$page->appendChild( $facility );
			
			$facilityName = $doc->createElement( "facilityName" );
			$facilityName->appendChild(
				$doc->createTextNode($orgDetails["facility"]["name"])
			);
			$facility -> appendChild( $facilityName );
			
			$facilityAddress = $doc->createElement("facilityAddress" );
			$facilityAddress->appendChild(
				$doc->createTextNode($orgDetails["facility"]["address"].", ".$orgDetails["facility"]["city"].", ".$orgDetails["facility"]["zip"])
			);
			$facility->appendChild( $facilityAddress );
		}
		
		if (isset($orgDetails["department"])) {
			$department = $doc->createElement("department");  						
			$page->appendChild( $department );
			
			$departmentName = $doc->createElement( "departmentName" );
			$departmentName->appendChild(
				$doc->createTextNode($orgDetails["department"]["name"])
			);
			$department -> appendChild( $departmentName );						
		}
		
		$productsTag = $doc->createElement( "products" );					
		foreach ($products as $product) {
			$productTag = $doc->createElement( "product" );		
			
			$supplierTag = $doc->createElement( "supplier" );
			$supplierTag->appendChild(
				$doc->createTextNode($product['supplier'])
			);
			$productTag->appendChild( $supplierTag );
			
			$productCodeTag = $doc->createElement( "productCode" );
			$productCodeTag->appendChild(
				$doc->createTextNode($product['product_nr'])
			);
			$productTag->appendChild( $productCodeTag );
			
			$productNameTag = $doc->createElement( "productName" );
			$productNameTag->appendChild(
				$doc->createTextNode($product['product_name'])
			);
			$productTag->appendChild( $productNameTag );
			
			$rulesTag = $doc->createElement( "rules" );
			foreach ($results as $result) {
				if($result['product_nr'] == $product['product_nr']) {
					
					$ruleTag = $doc->createElement( "rule" );
					
					$nameTag = $doc->createElement( "name" );
					$nameTag->appendChild(
						$doc->createTextNode($result['rule_nr'])
					);
					$ruleTag->appendChild( $nameTag );
					
					/*$qtyRuleTag = $doc->createElement( "qtyRule" );
					$qtyRuleTag->appendChild(
						$doc->createTextNode($result['qtyRule'])
					);
					$ruleTag->appendChild( $qtyRuleTag );*/
					
					$rulesTag->appendChild( $ruleTag );
				} 				
			}			
			$productTag->appendChild( $rulesTag );
			
			$usedTag = $doc->createElement( "used" );
			$usedTag->appendChild(
				$doc->createTextNode($product['used'])
			);
			$productTag->appendChild( $usedTag );
			
			$notUsedTag = $doc->createElement( "notUsed" );
			$notUsedTag->appendChild(
				$doc->createTextNode($product['notUsed'])
			);
			$productTag->appendChild( $notUsedTag );
			
			$productsTag ->appendChild( $productTag );
			
		}
		$page->appendChild( $productsTag );
		$doc->save($fileName);									
	}
	
	private function toxicCompoundsCreateXML($orgDetails,$equipments,$results, $fileName) {
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
			$doc->createTextNode("EQUIPMENT TOXIC/CHEMICAL EMISSIONS")
		);
		$page->appendChild( $title );
		
		$company = $doc->createElement("company");  						
		$page->appendChild( $company );
		
		$companyName = $doc->createElement( "companyName" );
		$companyName->appendChild(
			$doc->createTextNode($orgDetails["company"]["name"])
		);
		$company -> appendChild( $companyName );
		
		$companyAddress = $doc->createElement("companyAddress" );
		$companyAddress->appendChild(
			$doc->createTextNode($orgDetails["company"]["address"].", ".$orgDetails["company"]["city"].", ".$orgDetails["company"]["zip"])
		);
		$company->appendChild( $companyAddress );
		
		if (isset($orgDetails["facility"])) {
			$facility = $doc->createElement("facility");  						
			$page->appendChild( $facility );
			
			$facilityName = $doc->createElement( "facilityName" );
			$facilityName->appendChild(
				$doc->createTextNode($orgDetails["facility"]["name"])
			);
			$facility -> appendChild( $facilityName );
			
			$facilityAddress = $doc->createElement("facilityAddress" );
			$facilityAddress->appendChild(
				$doc->createTextNode($orgDetails["facility"]["address"].", ".$orgDetails["facility"]["city"].", ".$orgDetails["facility"]["zip"])
			);
			$facility->appendChild( $facilityAddress );
		}
		
		if (isset($orgDetails["department"])) {
			$department = $doc->createElement("department");  						
			$page->appendChild( $department );
			
			$departmentName = $doc->createElement( "departmentName" );
			$departmentName->appendChild(
				$doc->createTextNode($orgDetails["department"]["name"])
			);
			$department -> appendChild( $departmentName );						
		}
		$equipmentsTag = $doc->createElement( "equipments" );
		
		foreach ($equipments as $equipment) {
			$equipmentTag = $doc->createElement( "equipment" );
			
			$equipName = $doc->createAttribute("description");
			$equipName->appendChild(
				$doc->createTextNode($equipment)
			);
			$equipmentTag->appendChild($equipName);					
			
			foreach ($results as $result) {
				if($result['equipDesc'] == $equipment) {
					
					$compoundTag = $doc->createElement( "compound" );					
					$equipmentTag->appendChild( $compoundTag );
					
					$compoundNameTag = $doc->createElement( "compoundName" );
					$compoundNameTag->appendChild(
						$doc->createTextNode($result['description'])
					);
					$compoundTag->appendChild( $compoundNameTag );
					
					$casTag = $doc->createElement( "cas" );
					$casTag->appendChild(
						$doc->createTextNode($result['cas'])
					);
					$compoundTag->appendChild( $casTag );
					
					$lowTag = $doc->createElement( "low" );
					$lowTag->appendChild(
						$doc->createTextNode($result['low'])
					);
					$compoundTag->appendChild( $lowTag );
					
					$highTag = $doc->createElement( "high" );
					$highTag->appendChild(
						$doc->createTextNode($result['high'])
					);
					$compoundTag->appendChild( $highTag );
					
					$avgTag = $doc->createElement( "avg" );
					$avgTag->appendChild(
						$doc->createTextNode(round($result['avg'],2))
					);
					$compoundTag->appendChild( $avgTag );
					
					$totalTag = $doc->createElement( "total" );
					$totalTag->appendChild(
						$doc->createTextNode($result['total'])
					);
					$compoundTag->appendChild( $totalTag );
					
					$vocpmTag = $doc->createElement( "vocpm" );
					$vocpmTag->appendChild(
						$doc->createTextNode($result['type'])
					);
					$compoundTag->appendChild( $vocpmTag );
					
					$avgHourTag = $doc->createElement( "avgHour" );
					$avgHourTag->appendChild(
						$doc->createTextNode("0.000")
					);
					$compoundTag->appendChild( $avgHourTag );
					
					$caab2588Tag = $doc->createElement( "caab2588" );
					if (!empty($result['caab2588'])) {
						$caab2588Tag->appendChild(
							$doc->createTextNode($result['caab2588'])
						);	
					} else {
						$caab2588Tag->appendChild(
							$doc->createTextNode("N/A")
						);	
					}					
					$compoundTag->appendChild( $caab2588Tag );
					
					$sara313Tag = $doc->createElement( "sara313" );
					if (!empty($result['sara313'])) {
						$sara313Tag->appendChild(
							$doc->createTextNode($result['sara313'])
						);	
					} else {
						$sara313Tag->appendChild(
							$doc->createTextNode("N/A")
						);	
					}
					
					$compoundTag->appendChild( $sara313Tag );
				}			
			}
			$equipmentsTag->appendChild($equipmentTag);
		}		
		
		$page->appendChild( $equipmentsTag );
		
		$doc->save($fileName);
	}
	
	
	private function exemptCoatCreateXML($results,$period,$fileName) {
		
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
			$doc->createTextNode($period['label'])
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
				$doc->createTextNode($facility['name'])
			);
			$facilityTag->appendChild( $faciltyNameTag );
			
			$faciltyLocationTag = $doc->createElement( "location" );
			$faciltyLocationTag->appendChild(
				$doc->createTextNode($facility['address'].", ".$facility['city'].", ".$facility['state'].", ".$facility['zip'])
			);
			$facilityTag->appendChild( $faciltyLocationTag );
			
			$faciltyContactTag = $doc->createElement( "contactName" );
			$faciltyContactTag->appendChild(
				$doc->createTextNode($facility['contact'])
			);
			$facilityTag->appendChild( $faciltyContactTag );
			
			$faciltyTelTag = $doc->createElement( "TelNo" );
			$faciltyTelTag->appendChild(
				$doc->createTextNode($facility['phone'])
			);
			$facilityTag->appendChild( $faciltyTelTag );						
			
			foreach ($facility['equipment'] as $equipment) {
				
				$sumAmount = 0;
				$sumVoc = 0;
				
				$equipmentTag = $doc->createElement( "equipment" );		
				$facilityTag->appendChild( $equipmentTag );
				
				$equipmentIDTag = $doc->createAttribute( "id" );
				$equipmentIDTag->appendChild(
					$doc->createTextNode($equipment['id'])
				);
				$equipmentTag->appendChild( $equipmentIDTag );
				
				$permitTag = $doc->createAttribute( "permit" );
				$permitTag->appendChild(
					$doc->createTextNode($equipment['permit'])
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
							$doc->createTextNode($product['productID'])
						);
						$productTag->appendChild( $IDTag );
						
						$categoryTag = $doc->createElement( "category" );
						$categoryTag->appendChild(
							$doc->createTextNode($product['category'])
						);
						$productTag->appendChild( $categoryTag );
						
						$amountTag = $doc->createElement( "amount" );
						$amountTag->appendChild(
							$doc->createTextNode($product['amount'])
						);
						$productTag->appendChild( $amountTag );												
						
						$vocOfCoatingTag = $doc->createElement( "vocOfCoating" );
						$vocOfCoatingTag->appendChild(
							$doc->createTextNode($product['vocOfCoating'])
						);
						$productTag->appendChild( $vocOfCoatingTag );
						
						$exemptTag = $doc->createElement( "exempt" );
						$exemptTag->appendChild(
							$doc->createTextNode("FIX ME")
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
					$doc->createTextNode("FIX ME")
				);
				$equipmentTag->appendChild( $totalExemptTag );
			}																	
		}
		//getting users phone
		$this->db->query("SELECT phone FROM user WHERE user_id = ".$_SESSION['user_id']);
		$data=$this->db->fetch(0);
		
		$printNameTag = $doc->createElement( "printName" );
		$printNameTag->appendChild(
			$doc->createTextNode($_SESSION['username'])
		);
		$page->appendChild( $printNameTag );
		
		$usersPhoneTag = $doc->createElement( "usersTelNo" );
		$usersPhoneTag->appendChild(
			$doc->createTextNode($data->phone)
		);
		$page->appendChild( $usersPhoneTag );
		
		$dateTag = $doc->createElement( "date" );
		$dateTag->appendChild(
			$doc->createTextNode(date('m-d-Y'))
		);
		$page->appendChild( $dateTag );
		
		$doc->save($fileName);
	}
	
	private function projectCoatCreateXML($rule,$period,$reportData,$supplier,$orgName, $fileName){
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
			$doc->createTextNode($orgName." PROJECT COATING REPORT")
		);
		$page->appendChild( $title );
		
		$ruleTag = $doc->createElement( "rule" );
		$ruleTag->appendChild(
			$doc->createTextNode($rule)
		);
		$page->appendChild( $ruleTag );
		
		$monthYearTag = $doc->createElement( "monthYear" );
		$monthYearTag->appendChild(
			$doc->createTextNode($period['label'])
		);
		$page->appendChild( $monthYearTag );			
		
		$categoriesTag = $doc->createElement( "categories" );
		$categoriesTag->appendChild(
			$doc->createTextNode("(1K/2K)")
		);
		$page->appendChild( $categoriesTag );
		
		$clientNameTag = $doc->createElement( "clientName" );
		$clientNameTag->appendChild(
			$doc->createTextNode($reportData['clientName'])
		);
		$page->appendChild( $clientNameTag );
		
		$clientSpecTag = $doc->createElement( "clientSpecification" );
		$clientSpecTag->appendChild(
			$doc->createTextNode($reportData['clientSpecification'])
		);
		$page->appendChild( $clientSpecTag );				
		
		$tableTag = $doc->createElement( "table" );		
		$page->appendChild( $tableTag );		
		$supplierNameTag = $doc->createElement( "supplierName" );		
		$tableTag->appendChild( $supplierNameTag );
		
		for ($i=1;$i<=3;$i++) {
			$nameTag = $doc->createElement( "name" );
			$nameTag->appendChild(
				$doc->createTextNode($supplier[$i]['supplier'])
			);
			$supplierNameTag->appendChild( $nameTag );
		}
		
		$supplierContactTag = $doc->createElement( "supplierContact" );		
		$tableTag->appendChild( $supplierContactTag );		
		for ($i=1;$i<=3;$i++) {
			$contactTag = $doc->createElement( "contact" );
			$contactTag->appendChild(
				$doc->createTextNode($supplier[$i]['contactPerson'])
			);
			$supplierContactTag->appendChild( $contactTag );
		}
		
		$supplierPhoneTag = $doc->createElement( "supplierPhone" );		
		$tableTag->appendChild( $supplierPhoneTag );		
		for ($i=1;$i<=3;$i++) {
			$phoneTag = $doc->createElement( "phone" );
			$phoneTag->appendChild(
				$doc->createTextNode($supplier[$i]['phone'])
			);
			$supplierPhoneTag->appendChild( $phoneTag );
		}
		
		$supplierReasonTag = $doc->createElement( "supplierReason" );		
		$tableTag->appendChild( $supplierReasonTag );		
		for ($i=1;$i<=3;$i++) {
			$reasonTag = $doc->createElement( "reason" );
			$reasonTag->appendChild(
				$doc->createTextNode($reportData['reason'.$i])
			);
			$supplierReasonTag->appendChild( $reasonTag );
		}		
		
		$summaryTag = $doc->createElement( "summary" );
		$summaryTag->appendChild(
			$doc->createTextNode($reportData['summary'])
		);
		$page->appendChild( $summaryTag );
		
		
		
		$doc->save($fileName);
	}
	
	private function VOCbyRulesCreateXML($voc_arr, $orgInfo, $DatePeriod, $fileName){
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
			$doc->createTextNode("Summary for each rule number") 
		);
		$page->appendChild( $title );
		
		
		$categoryTag = $doc->createElement( "category" );
		$categoryTag->appendChild(
			$doc->createTextNode($orgInfo['category'])
		);
		$page->appendChild($categoryTag);
		
		$nameTag = $doc->createElement( "name" );
		$nameTag->appendChild(
			$doc->createTextNode($orgInfo['details']['name'])
		);
		$page->appendChild( $nameTag );
		if ($orgInfo['category'] == "Department") {
			$nameDepartmentTag = $doc->createElement( "departmentName" );
			$nameDepartmentTag->appendChild(
				$doc->createTextNode($orgInfo['name'])
			);
			$page->appendChild( $nameDepartmentTag );
		}
		
		$adressTag = $doc->createElement( "address" );
		$adressTag->appendChild( 
			$doc->createTextNode($orgInfo['details']['address'])
		);
		$page->appendChild( $adressTag );
		
		$cityStateZipTag = $doc->createElement( "cityStateZip" );
		$cityStateZipTag->appendChild(
			$doc->createTextNode($orgInfo['details']['city'].", ".$orgInfo['details']['state'].
				", ".$orgInfo['details']['zip'])
		);
		$page->appendChild( $cityStateZipTag );
		
		$countyTag = $doc->createElement( "county" );
		$countyTag->appendChild(
			$doc->createTextNode($orgInfo['details']['county'])
		);
		$page->appendChild( $countyTag );
		
		$phoneTag = $doc->createElement( "phone" );
		$phoneTag->appendChild(
			$doc->createTextNode($orgInfo['details']['phone'])
		);
		$page->appendChild( $phoneTag );
		
		$faxTag = $doc->createElement( "fax" );
		$faxTag->appendChild(
			$doc->CreateTextNode($orgInfo['details']['fax'])
		);
		$page->appendChild( $faxTag );
		
		if ($orgInfo['category'] != "Company") {
			$facilityIdTag = $doc->createElement( "facilityID" );
			$facilityIdTag->appendChild(
				$doc->CreateTextNode($orgInfo['details']['facility_id'])
			);
			$page->appendChild($facilityIdTag);
		}
		
		$notesTag = $doc->createElement( "notes" );
		$notesTag->appendChild(
			$doc->CreateTextNode($orgInfo['notes'])
		);
		$page->appendChild($notesTag);
				
		$timePeriodTag = $doc->createElement( "period");
		$timePeriodTag->appendChild(
			$doc->createTextNode($DatePeriod)
		);
		$page->appendChild($timePeriodTag);
		
		$tableTag = $doc->createElement( "table" );		
		$page->appendChild( $tableTag );		

		
		//by rule
		foreach ($voc_arr as $vocByRule) {
			$ruleTag = $doc->createElement( "rule" );		
			$tableTag->appendChild( $ruleTag );
			
			$ruleNameTag = $doc->createAttribute( "name" );
			$ruleNameTag->appendChild(
				$doc->createTextNode($vocByRule['rule'])
			);
			$ruleTag->appendChild( $ruleNameTag );
			
			//by year!
			foreach($vocByRule['data'] as $vocByYear) {
				$yearTag = $doc->createElement( "year" );
				$ruleTag->appendChild( $yearTag );
				$yearName = $doc->createAttribute( "name" );
				$yearName->appendChild(
					$doc->createTextNode($vocByYear['year'])
				);
				$yearTag->appendChild( $yearName );
				//final:month&voc
				foreach ($vocByYear['data'] as $vocByMonth) {
					$infoTag = $doc->createElement( "info" );
					$yearTag->appendChild( $infoTag );
					$monthTag = $doc->createAttribute( "month" );
					$monthTag->appendChild(
						$doc->createTextNode($vocByMonth['month'])
					);
					$infoTag->appendChild( $monthTag );
					$vocTag = $doc->createAttribute( "voc" );
					$vocTag->appendChild(
						$doc->createTextNode($vocByMonth['voc'])
					);
					$infoTag->appendChild( $vocTag );
				}
				$totalTag = $doc->createElement( "total" );
				$totalTag->appendChild(
					$doc->createTextNode($vocByYear['total'])
				);
				$yearTag->appendChild( $totalTag );
			}
		}
		
		$doc->save($fileName);
	}
	
	private function SummVOCCreateXML($voc_arr, $orgInfo, $DatePeriod, $fileName) {
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
			$doc->createTextNode($orgInfo['category'])
		);
		$page->appendChild($categoryTag);
		
		$nameTag = $doc->createElement( "name" );
		$nameTag->appendChild(
			$doc->createTextNode($orgInfo['details']['name'])
		);
		$page->appendChild( $nameTag );
		if ($orgInfo['category'] == "Department") {
			$nameDepartmentTag = $doc->createElement( "departmentName" );
			$nameDepartmentTag->appendChild(
				$doc->createTextNode($orgInfo['name'])
			);
			$page->appendChild( $nameDepartmentTag );
		}
		
		$adressTag = $doc->createElement( "address" );
		$adressTag->appendChild( 
			$doc->createTextNode($orgInfo['details']['address'])
		);
		$page->appendChild( $adressTag );
		
		$cityStateZipTag = $doc->createElement( "cityStateZip" );
		$cityStateZipTag->appendChild(
			$doc->createTextNode($orgInfo['details']['city'].", ".$orgInfo['details']['state'].
				", ".$orgInfo['details']['zip'])
		);
		$page->appendChild( $cityStateZipTag );
		
		$countyTag = $doc->createElement( "county" );
		$countyTag->appendChild(
			$doc->createTextNode($orgInfo['details']['county'])
		);
		$page->appendChild( $countyTag );
		
		$phoneTag = $doc->createElement( "phone" );
		$phoneTag->appendChild(
			$doc->createTextNode($orgInfo['details']['phone'])
		);
		$page->appendChild( $phoneTag );
		
		$faxTag = $doc->createElement( "fax" );
		$faxTag->appendChild(
			$doc->CreateTextNode($orgInfo['details']['fax'])
		);
		$page->appendChild( $faxTag );
		
		if ($orgInfo['category'] != "Company") {
			$facilityIdTag = $doc->createElement( "facilityID" );
			$facilityIdTag->appendChild(
				$doc->CreateTextNode($orgInfo['details']['facility_id'])
			);
			$page->appendChild($facilityIdTag);
		}
		
		$notesTag = $doc->createElement( "notes" );
		$notesTag->appendChild(
			$doc->CreateTextNode($orgInfo['notes'])
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
						$doc->createTextNode($vocByRule['rule'])
					);
				} else {
					$ruleTag = $doc->createAttribute( "exempt" );
					$ruleTag->appendChild(
						$doc->createTextNode($vocByRule['exempt'])
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
	
	private function groupToxicCompounds($query) {
		$this->db->query($query);						
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																								
				$result = array (
					'equipDesc'		=>	$data->equip_desc,
					'description'	=>	$data->description,
					'cas'			=>	$data->cas,
					'type'			=>	$data->type,
					'unitType'		=>	$data->unit_type,
					'low'			=>	$data->low,
					'high'			=>	$data->high,
					'avg'			=>	$data->avg,
					'total'			=>	$data->total,
					'sara313'		=>	$data->sara313,
					'caab2588'		=>	$data->caab2588																	
				);
				$results[] = $result;																						
			}
		}														
		$equipments[0] = $results[0]['equipDesc'];																																			
		$k=0;											
		for($i=1; $i < count($results); $i++) {				
			if ($results[$i]['equipDesc'] != $equipments[$k]) {
				$k++;
				$equipments[$k] = $results[$i]['equipDesc'];																								
			}
		}
		$out['equipments'] = $equipments;
		
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter("us gallon");
		
		$output[0] = $results[0];
		$k=0;
		for($i=1; $i < count($results); $i++) {
			if ($results[$i]['cas'] == $output[$k]['cas']) {
				$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
				$low = $unitTypeConverter->convertToDefault($results[$i]['low'], $unitypeDetails['description']);
				$output[$k]['low'] += $low; 
				$high = $unitTypeConverter->convertToDefault($results[$i]['high'], $unitypeDetails['description']);
				$output[$k]['high'] += $high;
				$avg = $unitTypeConverter->convertToDefault($results[$i]['avg'], $unitypeDetails['description']);
				$output[$k]['avg'] += $avg;
				$total = $unitTypeConverter->convertToDefault($results[$i]['total'], $unitypeDetails['description']);
				$output[$k]['total'] += $total;
			} else {
				$k++;	
				$output[$k] = $results[$i];							
			}
		}
		$out['output'] = $output;
		
		return $out;
	}
	
	
	private function groupProductQuants($query) {
		$this->db->query($query);						
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);																								
				$result = array (
					'product_nr'	=>	$data->product_nr,
					'product_name'	=>	$data->product_name,
					'vocwx'			=>	$data->vocwx,
					'voclx'			=>	$data->voclx									
				);
				$results[] = $result;																						
			}
		}
		$out['results'] = $results; 
		
		$mps[0] = substr($results[0]['product_nr'],0,2);
		$k=0;											
		for($i=1; $i < count($results); $i++) {
			if (substr($results[$i]['product_nr'],0,2) != $mps[$k]) {
				$k++;
				$mps[$k] = substr($results[$i]['product_nr'],0,2);								
			}
		}
		$out['mps'] = $mps;
		
		return $out; 
	}
	
	
	private function groupVocLogs($query, $dateBegin, $dateEnd, $ruleID) {
		$mixObj = new Mix($this->db);
		
		$this->db->query($query);
	
		if ($this->db->num_rows()) {
			$equipmentsData = $this->db->fetch_all();
			foreach ($equipmentsData as $equipmentData) {
				$equipment = array (
					'id'				=>	$equipmentData->equipment_id,
					'name'				=>	$equipmentData->equip_desc,
					'permit'			=>	$equipmentData->permit,										
					'epa'				=>	$equipmentData->epa
				);
				$query = "SELECT mix_id FROM mix WHERE equipment_id = ".$equipment['id']." AND rule_id = ".$ruleID;
				$this->db->query($query);
				
				if ($this->db->num_rows()) {
					$mixesData = $this->db->fetch_all();
					foreach ($mixesData as $mixData) {						
						$equipment['mixes'][] = $mixObj->getMixDetails($mixData->mix_id);
					}
					$equipments[] = $equipment;
				}							
			}			
		}	
		
		//	create day list		
		$days[0] = strtotime($dateBegin);		
		$i=1;		
		while ($days[$i-1] < strtotime($dateEnd.' -1 day') ) {
			$days[$i] = $days[$i-1] + 86400;	//60*60*24 - seconds in one day			
			$i++;
		}	
	
		$out['equipments'] = $equipments;
		$out['days'] = $days;

		return $out;
	}
	
	
	private function groupMixQuantRule($query,$categoryType,$categoryID) {
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
		$products[0]['used'] = $results[0]['used'];
		
		$unitypeDetails = $unittype->getUnittypeDetails($results[0]['unitType']);
		
		$qtyRule = $unitTypeConverter->convertToDefault($results[0]['qtyRule'], $unitypeDetails['description']);
		$results[0]['qtyRule'] = $qtyRule;
		
		$used = $unitTypeConverter->convertToDefault($results[0]['used'], $unitypeDetails['description']);
		$products[0]['used'] = $used;
		//end of conversion
		
		$k=0;											
		for($i=1; $i < count($results); $i++) {
			$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
			$qtyRule = $unitTypeConverter->convertToDefault($results[$i]['qtyRule'], $unitypeDetails['description']);
			$results[$i]['qtyRule'] = $qtyRule;							
			if ($results[$i]['product_nr'] != $products[$k]['product_nr']) {
				$k++;
				$products[$k]['supplier'] = $results[$i]['supplier'];
				$products[$k]['product_id'] = $results[$i]['product_id'];
				$products[$k]['product_nr'] = $results[$i]['product_nr'];
				$products[$k]['product_name'] = $results[$i]['product_name'];
				
				$unitypeDetails = $unittype->getUnittypeDetails($results[$i]['unitType']);
				$used = $unitTypeConverter->convertToDefault($results[$i]['used'], $unitypeDetails['description']);
				$products[$k]['used'] = $used;																	
			}
		}
		//getting product quantities in inventory
		foreach ($products as $value) {
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
		}
		
		$out['products'] = $products;
		$out['results'] = $results;
		
		return $out;
	}
	
	
	private function groupChemClass($query,$categoryType,$categoryID){
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
	
	private function groupExemptCoat($query,$categoryType,$categoryID) {
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

	private function groupVOCbyRules($query, $ruleQuery, $dateBegin, $dateEnd) {
		$this->db->query($ruleQuery);
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);	
				$rule[$i] = $data->rule_id;
				$rule_nr[$i] = $data->rule_nr;
			}	
		}	

		for ($i = 0; $i<count($rule); $i++) {
			/*
			 * $tmpYear, $tmpMonth, $tmpDay - values of year, month and day of current time period for temporary query
			 * it need for generating $tmpDate and $tmpDateEnd
			 * $endYear, $endMonth - values of year and month of the end date for query
			 */
			$totalByRule = 0;
			$tmpYear = substr(date("Y-m-d", strtotime($dateBegin)), 0, 4);
			$tmpMonth = substr(date("Y-m-d", strtotime($dateBegin)), 5, 2);
			$tmpDay = 1;
			$tmpDate = date("Y-m-d", strtotime($dateBegin));
			$endYear = substr(date("Y-m-d", strtotime($dateEnd)), 0, 4);
			$endMonth = substr(date("Y-m-d", strtotime($dateEnd)), 5, 2);
			$total = 0;
			$tmpResults = array();
			$results = array();
			while ((((int)$tmpYear == (int)$endYear)&&((int)$tmpMonth <= (int)$endMonth))||
					( (int)$tmpYear<(int)$endYear))	{
				if (((int)$tmpMonth == (int)$endMonth)&&((int)$tmpYear == (int)$endYear)) {
					$tmpDateEnd = $dateEnd;
				} else {
					if ( $tmpMonth==12 ) {
						$tmpYear +=1;
						$tmpMonth = 1;
					} else {
						$tmpMonth += 1; 
					}
					$tmpDateEnd = $tmpYear."-".$tmpMonth."-".$tmpDay;
				}

				$tmpQuery = $query."AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDate)). "','%Y-%m-%d') " .
					"AND DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDateEnd)). "','%Y-%m-%d') ";	
				$tmpQuery .= "AND m.rule_id = ".$rule[$i]." ";	

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
						'month' => substr(date("Y-M-d", strtotime($tmpDate)), 5, 3),

						'voc' => $VOCresult
					);		
				} else {
					$result = array(
					    'month' => substr(date("Y-M-d", strtotime($tmpDate)), 5, 3),

						'voc' => 0
					);
				}
				if ((int)substr(date("Y-m-d", strtotime($tmpDate)), 0, 4) == (int)$tmpYear) {
						$tmpResults[] = $result;
					} else {
						$tmpResults[] = $result;
						$results1 = array(
							'year' => substr(date("Y-m-d", strtotime($tmpDate)), 0, 4),
							'total' => $total,
							'data' => $tmpResults
						);
						$results [] = $results1;
						$tmpResults = array();
						$totalByRule += $total;
						$total = 0;
					}		
				$tmpDate = $tmpDateEnd;
				if ($tmpDate == $dateEnd) {
					break;
				}
			}	
			if (count($tmpResults)!=0) {
				$results[]= array(
					'year' => substr(date("Y-m-d", strtotime($tmpDate)), 0, 4),
					'total' => $total,
					'data' => $tmpResults
				);
				$totalByRule += $total;	
				$total=0;					
			}
			if ($totalByRule != 0) {
				$resultsByRules[] = array(
					'rule' => $rule_nr[$i],
					'data' => $results 
				);
			}
		}
		return $resultsByRules;		
	}
	
	private function groupSummVOC($query, $ruleQuery, $dateBegin, $dateEnd) {
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
		$tmpYear = substr(date("Y-m-d", strtotime($dateBegin)), 0, 4);
		$tmpMonth = substr(date("Y-m-d", strtotime($dateBegin)), 5, 2);
		$tmpDay = 1;
		$tmpDate = date("Y-m-d", strtotime($dateBegin));
		$endYear = substr(date("Y-m-d", strtotime($dateEnd)), 0, 4);
		$endMonth = substr(date("Y-m-d", strtotime($dateEnd)), 5, 2);
		$total = 0;
		$tmpResults = array();
		$results = array();
		$fullTotal = 0;
		while ((((int)$tmpYear == (int)$endYear)&&((int)$tmpMonth <= (int)$endMonth))||
				( (int)$tmpYear<(int)$endYear))	{
			if (((int)$tmpMonth == (int)$endMonth)&&((int)$tmpYear == (int)$endYear)) {
				$tmpDateEnd = $dateEnd;
			} else {
				if ( $tmpMonth==12 ) {
					$tmpYear +=1;
					$tmpMonth = 1;
				} else {
					$tmpMonth += 1; 
				}
				$tmpDateEnd = $tmpYear."-".$tmpMonth."-".$tmpDay;
			}
			$results = array();
			$WasARule = false;
			for ($i = 0; $i<count($rule); $i++) {
				$tmpQuery = $query."AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDate)). "','%Y-%m-%d') " .
					"AND DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDateEnd)). "','%Y-%m-%d') ";	
				$tmpQuery .= "AND m.rule_id = ".$rule[$i]." ";	

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
						'rule' => $rule_nr[$i],

						'voc' => $VOCresult
					);	
					$results [] = $result;	
					$WasARule = true;
				} /*else {
					$result = array(
					    'rule' => $rule_nr[$i],

						'voc' => 0
					);
				}
				$results [] = $result;*/
			}
			if ($WasARule == false) {
				$results [] = $emptyData[0];
			}
			$WasAnExemptRule = false;
			for ($i = 0; $i<count($exempt); $i++) {
				$tmpQuery = $query."AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDate)). "','%Y-%m-%d') " .
					"AND DATE_FORMAT('" . date("Y-m-d", strtotime($tmpDateEnd)). "','%Y-%m-%d') ";	
				$tmpQuery .= "AND m.exempt_rule = ".$exempt[$i]." ";	

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
				}/* else {
					$result = array(
					    'exempt' => $exempt[$i],

						'voc' => 0
					);
				}
				$results [] = $result;*/
			}
			if ($WasAnExemptRule == false) {
				$results [] = $emptyData[1];
			}	
			//past here result=>
//			if ($total != 0) {
				$resultByMonth [] = array(
					'month' => substr(date("M  Y d", strtotime($tmpDate)), 0, 9),
					'total' => $total,
					'data' => $results
				);
				$fullTotal += $total;
				$total = 0;
//			} /*else {
//				$resultByMonth [] = $emptyData;
//			}*/
			$tmpDate = $tmpDateEnd;
			if ($tmpDate == $dateEnd) {
				break;
			}
		}	
		$totalResults = array(
			'total' => $fullTotal,
			'data' => $resultByMonth
		); 
//		var_dump($totalResults);
//		die();
		return $totalResults;			
	}
	
	function GetRuleQuery($categoryType, $categoryID) {
		$ruleListQuery = "SELECT rule_id ".
			"FROM selected_rules_list ".
			"WHERE category = '".$categoryType."' ".
			"AND category_id = ".$categoryID;
		$ruleQuery = "SELECT r.rule_id, r.rule_nr ".
			"FROM rule r";
		$InSelectedRuleList = false;
		$this->db->query($ruleListQuery);
		if ($this->db->num_rows()) {
			$InSelectedRuleList = true;
			$neededCategoryType = $categoryType;
			$neededCategoryID = $categoryID;
		} else {
			if ($categoryType =='facility') {
				$facility = new Facility($this->db);    				
				$facilityDetails = $facility->getFacilityDetails($categoryID);
				$ruleListQuery = "SELECT rule_id ".
					"FROM selected_rules_list ".
					"WHERE category = 'company' ".
					"AND category_id = ".$facilityDetails['company_id'];
				$this->db->query($ruleListQuery);
				if ($this->db->num_rows()) {
					$InSelectedRuleList = true;
					$neededCategoryType = 'company';
					$neededCategoryID = $facilityDetails['company_id'];
				}
			} else {
				$department = new Department($this->db);
				$departmentDetails = $department -> getDepartmentDetails($categoryID);
				$ruleListQuery = "SELECT rule_id ".
					"FROM selected_rules_list ".
					"WHERE category = 'facility' ".
					"AND category_id = ".$departmentDetails['facility_id'];
				$this->db->query($ruleListQuery);
				if ($this->db->num_rows()) {
					$InSelectedRuleList = true;
					$neededCategoryType = 'facility';
					$neededCategoryID = $departmentDetails['facility_id'];
				} else {							
					$facility = new Facility($this->db);    				
					$facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);
					$ruleListQuery = "SELECT rule_id ".
						"FROM selected_rules_list ".
						"WHERE category = 'company' ".
						"AND category_id = ".$facilityDetails['company_id'];
					$this->db->query($ruleListQuery);
					if ($this->db->num_rows()) {
						$InSelectedRuleList = true;
						$neededCategoryType = 'company';
						$neededCategoryID = $facilityDetails['company_id'];
					}
				}						
			}
		}
		if ($InSelectedRuleList) {
			$ruleQuery .= ", selected_rules_list sr ".
				"WHERE r.rule_id = sr.rule_id ".
				"AND sr.category = '".$neededCategoryType."' ".
				"AND category_id = ".$neededCategoryID;
		}
	return $ruleQuery;
	}
	
	function gcm($a, $b) {
		return ( $b == 0 ) ? ($a):( $this->gcm($b, $a % $b) );
	}
	
	function lcm($a, $b) {
		return ( $a / $this->gcm($a,$b) ) * $b;
	}
	
	function lcm_nums($ar) {
		if (count($ar) > 1) {
			$ar[] = $this->lcm( array_shift($ar) , array_shift($ar) );
			return $this->lcm_nums( $ar );
		} else {
			return $ar[0];
		}
	}
	
	function getPeriodByFrequency($frequency, $dateBegin) { // tmp function //denis 20 May 2009
		
		//dateEnd is ignored //denis 20 May 2009
		
		if ($frequency == 'monthly') {
			$month = substr($dateBegin,0,2);
			$year = substr($dateBegin,6,4);
			settype($month, "integer");
			settype($year, "integer"); 												
			$dateBegin = date("d-m-Y", mktime(0, 0, 0, $month, 1, $year ));
			$dateEnd = date("d-m-Y", mktime(0, 0, 0, $month + 1, 0, $year));
			$label =  sprintf("%02s",$month) . "/" . sprintf("%02s",$year);										
		} else { // then annual
			$month = substr($dateBegin,0,2);
			$year = substr($dateBegin,6,4);
			settype($month, "integer");
			settype($year, "integer"); 												
			$dateBegin = date("d-m-Y", mktime(0, 0, 0, 1, 1, $year ));
			$dateEnd = date("d-m-Y", mktime(0, 0, 0, 12, 31, $year));
			$label = sprintf("%02s",$year);						
		}
		
		$period['dateBegin'] = $dateBegin;
		$period['dateEnd'] = $dateEnd;
		$period['label'] = $label;
		
		return $period; 
	}
	
}

?>