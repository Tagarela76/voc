<?php

class RproductQuants extends ReportCreator implements iReportCreator {

	private $dateBegin;
	private $dateEnd;
	private $dateFormat;

    function RproductQuants($db, ReportRequest $reportRequest) {
    	$this->db = $db;
		$this->categoryType = $reportRequest->getCategoryType();
		$this->categoryID = $reportRequest->getCategoryID();
		$this->dateBegin = $reportRequest->getDateBegin();
		$this->dateEnd = $reportRequest->getDateEnd();
		$this->dateFormat = $reportRequest->getDateFormat();
    }
    
    public function buildXML($fileName) {
	    //$agencyStr = "1, 30"; //CAOSHA and SCAQMD
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
			    
			    $query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
				    //"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
				    "FROM mix m, mixgroup mg, department d, facility f, product p " .
					"WHERE m.mix_id = mg.mix_id and " .
					"m.department_id = d.department_id and " .
					"mg.product_id = p.product_id and " .
					"d.facility_id = f.facility_id " .
					//" AND p.product_id = cg.product_id " .
					//"AND cg.component_id = ab.component_id " .												
					//"AND ab.agency_id IN (" . $agencyStr . ") " .				
					"AND f.facility_id IN (" . $facilityString . ") " .
					//"AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($this->dateBegin)). "','%Y-%m-%d') " .
					"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					//"AND DATE_FORMAT('" . date("Y-m-d", strtotime($this->dateEnd)). "','%Y-%m-%d') " .													
					"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";

			    //getting company name
			    $company = new Company($this->db);
			    $companyDetails = $company -> getCompanyDetails($this->categoryID);
			    $orgDetails['company'] = $companyDetails; 
			    break;
			    
		    case "facility":
			    $query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
				    //"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
				    "FROM mix m, mixgroup mg, department d, facility f, product p " .
					"WHERE m.mix_id = mg.mix_id and " .
					"m.department_id = d.department_id and " .
					"mg.product_id = p.product_id and " .
					"d.facility_id = f.facility_id " .
					//"AND p.product_id = cg.product_id " .
					//"AND cg.component_id = ab.component_id " .												
					//"AND ab.agency_id IN (" . $agencyStr . ") " .		
					"AND f.facility_id = " . $this->categoryID . " " .
					//"AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($this->dateBegin)). "','%Y-%m-%d') " .
					"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					//"AND DATE_FORMAT('" . date("Y-m-d", strtotime($this->dateEnd)). "','%Y-%m-%d') " .						
					"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";

			    //getting company name
			    $facility = new Facility($this->db);    				
			    $facilityDetails = $facility->getFacilityDetails($this->categoryID);
			    
			    $company = new Company($this->db);
			    $companyDetails = $company -> getCompanyDetails($facilityDetails['company_id']);						
			    $orgDetails['company'] = $companyDetails;
			    $orgDetails['facility'] = $facilityDetails;
			    break;
			    
		    case "department":
			    $query = "SELECT p.product_id, p.product_nr, p.name product_name, p.vocwx, p.voclx " .
				    //"FROM mix m, mixgroup mg, department d, facility f, product p, components_group cg, agency_belong ab " .
				    "FROM mix m, mixgroup mg, department d, facility f, product p " .
					"WHERE m.mix_id = mg.mix_id and " .
					"m.department_id = d.department_id and " .
					"mg.product_id = p.product_id and " .
					"d.facility_id = f.facility_id " .
					//"AND p.product_id = cg.product_id " .
					//"AND cg.component_id = ab.component_id " .													
					//"AND ab.agency_id IN (" . $agencyStr . ") " .
					"AND d.department_id = " . $this->categoryID . " " .
					//"AND m.creation_time BETWEEN DATE_FORMAT('" . date("Y-m-d", strtotime($this->dateBegin)). "','%Y-%m-%d') " .
					"AND m.creation_time >= ".$dateBeginObj->getTimestamp()." AND m.creation_time <= ".$dateEndObj->getTimestamp()." " .
					//"AND DATE_FORMAT('" . date("Y-m-d", strtotime($this->dateEnd)). "','%Y-%m-%d') " .			
					"group by p.product_id, p.product_nr, p.name, p.vocwx, p.voclx";

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
	    $this->createXML($in['mps'],$in['results'],$orgDetails,$this->dateBegin,$this->dateEnd,$fileName);			
    }
    
    public function createXML($inventories,$results,$orgDetails,$dateBegin,$dateEnd,$fileName) {
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
			$doc->createTextNode("SDS Submittal")
		);
		$page->appendChild( $title );

		//period added by den 03/June/2009
		
		$periodTag = $doc->createElement( "period" );
		$periodTag->appendChild(
			//$doc->createTextNode("From ".date("Y-m-d", strtotime($dateBegin))." To ".date("Y-m-d", strtotime($dateEnd)))
			$doc->createTextNode("From ".$this->dateBegin." To ".$this->dateEnd)
		);
		$page->appendChild( $periodTag );
		
		//company inf added by den 03/June/2009
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
		
		foreach ($inventories as $inventory) {
			$r = $doc->createElement( "mpsGroup" );
			
			$equipmentNameTag = $doc->createAttribute("name");
			$equipmentNameTag->appendChild(
				$doc->createTextNode( html_entity_decode ($inventory))
			);
			$r->appendChild($equipmentNameTag);
			
			$page->appendChild( $r );
			$count = 1;
			foreach ($results as $result) {
				if (substr($result['product_nr'],0,2) == $inventory) {
					$group = $doc->createElement( "product" );
					
					$idTag = $doc->createElement( "ID" );
					$idTag->appendChild(
						$doc->createTextNode( html_entity_decode (substr($result['product_nr'],0,2) ." - " . sprintf("%03s",$count)))
					);
					$group->appendChild( $idTag );
					
					$productCodeTag = $doc->createElement( "productCode" );
					$productCodeTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['product_nr']))
					);
					$group->appendChild( $productCodeTag );
					
					$colorTag = $doc->createElement( "color" );
					$colorTag->appendChild(
						$doc->createTextNode( html_entity_decode ($result['product_name']))
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
    
    private function group($query) {
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
}
?>