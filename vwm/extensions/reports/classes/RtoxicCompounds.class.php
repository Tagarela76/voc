<?php
class RtoxicCompounds extends ReportCreator implements iReportCreator {
	
	private $dateBegin;
	private $dateEnd;
	
	private $dateFormat;

    function RtoxicCompounds($db, $reportRequest) {
    	$this->db = $db;
		$this->categoryType = $reportRequest->getCategoryType();
		$this->categoryID = $reportRequest->getCategoryID();

		$this->dateBegin = $reportRequest->getDateBegin();
		$this->dateEnd = $reportRequest->getDateEnd(); 	
		$this->dateFormat = $reportRequest->getDateFormat();
    }
    
    public function buildXML($fileName) {
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

			    $query = "SELECT e.equip_desc, c.description, c.cas, cg.type, c.sara313, c.caab2588, mg.unit_type, MIN( mg.quantity ) low, MAX( mg.quantity ) high, AVG( mg.quantity ) avg, SUM( mg.quantity ) total " .
				    "FROM product p, components_group cg, component c, mixgroup mg, mix m, department d, equipment e " .
					"WHERE cg.product_id = p.product_id " .
					"AND p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .			    	
					"AND m.department_id = d.department_id " .
					"AND cg.component_id = c.component_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND d.facility_id IN (" . $facilityString . ") " .
			    	"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					"GROUP BY m.equipment_id, c.description, c.cas";

			    //getting company name
			    $company = new Company($this->db);
			    $companyDetails = $company -> getCompanyDetails($this->categoryID);
			    $orgDetails['company'] = $companyDetails;
			    break;
			    
		    case "facility":
			    $query = "SELECT e.equip_desc, c.description, c.cas, cg.type, c.sara313, c.caab2588, mg.unit_type, MIN( mg.quantity ) low, MAX( mg.quantity ) high, AVG( mg.quantity ) avg, SUM( mg.quantity ) total " .
				    "FROM product p, components_group cg, component c, mixgroup mg, mix m, department d, equipment e " .
					"WHERE cg.product_id = p.product_id " .
					"AND p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .
					"AND m.department_id = d.department_id " .
					"AND cg.component_id = c.component_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND d.facility_id = " . $this->categoryID . " " .
			    	"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					"GROUP BY m.equipment_id, c.description, c.cas";

			    $facility = new Facility($this->db);    				
			    $facilityDetails = $facility->getFacilityDetails($this->categoryID);
			    
			    $company = new Company($this->db);
			    $companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
			    $orgDetails['company'] = $companyDetails;
			    $orgDetails['facility'] = $facilityDetails;																
			    break;
			    
		    case "department":
			    $query = "SELECT e.equip_desc, c.description, c.cas, cg.type, c.sara313, c.caab2588, mg.unit_type, MIN( mg.quantity ) low, MAX( mg.quantity ) high, AVG( mg.quantity ) avg, SUM( mg.quantity ) total " .
				    "FROM product p, components_group cg, component c, mixgroup mg, mix m, equipment e " .
					"WHERE cg.product_id = p.product_id " .
					"AND p.product_id = mg.product_id " .
					"AND mg.mix_id = m.mix_id " .								 
					"AND cg.component_id = c.component_id " .
					"AND m.equipment_id = e.equipment_id " .
					"AND m.department_id = " . $this->categoryID . " " .
			    	"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					"GROUP BY m.equipment_id, c.description, c.cas";						

			    $department = new Department($this->db);
			    $departmentDetails = $department -> getDepartmentDetails($this->categoryID);
			    
			    $facility = new Facility($this->db);
			    $facilityDetails = $facility -> getFacilityDetails($departmentDetails['facility_id']);
			    
			    $company = new Company($this->db);
			    $companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);
			    
			    $orgDetails['company'] = $companyDetails;
			    $orgDetails['facility'] = $facilityDetails;
			    $orgDetails['department'] = $departmentDetails;						
			    break;
	    }
	    $in = $this->group($query);
	    $this->createXML($orgDetails,$in['equipments'],$in['output'],$fileName);
    }
    
    public function createXML($orgDetails,$equipments,$results, $fileName) {
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
		$equipmentsTag = $doc->createElement( "equipments" );
		
		foreach ($equipments as $equipment) {
			$equipmentTag = $doc->createElement( "equipment" );
			
			$equipName = $doc->createAttribute("description");
			$equipName->appendChild(
				$doc->createTextNode( html_entity_decode ($equipment))
			);
			$equipmentTag->appendChild($equipName);					
			
			foreach ($results as $result) {
				if($result['equipDesc'] == $equipment) {
					
					$compoundTag = $doc->createElement( "compound" );					
					$equipmentTag->appendChild( $compoundTag );
					
					$compoundNameTag = $doc->createElement( "compoundName" );
					$compoundNameTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['description']))
					);
					$compoundTag->appendChild( $compoundNameTag );
					
					$casTag = $doc->createElement( "cas" );
					$casTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['cas']))
					);
					$compoundTag->appendChild( $casTag );
					
					$lowTag = $doc->createElement( "low" );
					$lowTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['low']))
					);
					$compoundTag->appendChild( $lowTag );
					
					$highTag = $doc->createElement( "high" );
					$highTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['high']))
					);
					$compoundTag->appendChild( $highTag );
					
					$avgTag = $doc->createElement( "avg" );
					$avgTag->appendChild(
						$doc->createTextNode( html_entity_decode (round($result['avg'],2)))
					);
					$compoundTag->appendChild( $avgTag );
					
					$totalTag = $doc->createElement( "total" );
					$totalTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['total']))
					);
					$compoundTag->appendChild( $totalTag );
					
					$vocpmTag = $doc->createElement( "vocpm" );
					$vocpmTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['type']))
					);
					$compoundTag->appendChild( $vocpmTag );
					
					$avgHourTag = $doc->createElement( "avgHour" );
					$avgHourTag->appendChild(
						$doc->createTextNode( html_entity_decode ("0.000"))
					);
					$compoundTag->appendChild( $avgHourTag );
					
					$caab2588Tag = $doc->createElement( "caab2588" );
					if (!empty($result['caab2588'])) {
						$caab2588Tag->appendChild(
							$doc->createTextNode( html_entity_decode ($result['caab2588']))
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
							$doc->createTextNode( html_entity_decode ($result['sara313']))
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
    
    private function group($query) {
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
}
?>