<?php

class bulkUploader {
	private $db;
	
	private $productFrom = 0;
	private $productTo;
	private $inventoryID = 0;
	private $productObj;
	private $hazardousObj;
	private $ruleObj;
	private $companyID;
	//private $compType = 'VOC';
	
	public $productsError;
	public $productsCorrect;
	public $insertedCnt;
	public $updatedCnt;
	public $validationResult;	
	public $actions;
	
    function bulkUploader($db,$input) {
    	
    	$this->db=$db;    	    	
    	    	        
    	$this->productObj = new Product($db);
    	$this->hazardousObj = new Hazardous($db);
    	$this->ruleObj = new Rule($db);
    	$this->companyID = (empty($input['companyID'])) ? null : $input['companyID'];
    	
    	//$this->db->select_db(DB_NAME);
    	
    	$this->productTo = $input['maxNumber'];
    	
    	$path = $input['inputFile'];
    	
    	$this->insertedCnt = 0;
		$this->updatedCnt = 0;

		$actionLog = "--------------------------------\n";
		$actionLog .= "(" . date("m.d.Y H:i:s") . ") Starting uploading of ". $input['realFileName'] . "...\n";
		
		$validation = new validateCSV();
		$validation->validate($input);
		 
		$errorLog = $validation->errorComments;
		
		//partition
		$cntCorrect = count($validation->productsCorrect);
		$cntError = count($validation->productsError);		
		$total = $cntError+$cntCorrect;
		$percent = round($cntError*100/($cntCorrect+$cntError),2);
		$this->productsCorrect = $validation->productsCorrect;
		$this->productsError = $validation->productsError;		 	
		
		if ($percent<=$input['threshold']) {
			
			//creating_backup
    		$sqlFile = "db_backup/".DB_NAME."_".date('Y_m_d_H_i_s').".sql";
    		$creatBackup = "mysqldump -h ".DB_HOST." -u ".DB_USER." --password=".DB_PASS." ".DB_NAME." > ".$sqlFile;		
			exec($creatBackup);
		
			$products = $validation->productsCorrect;
			for($i=0;$i<count($products);$i++) {
				
				$this->db->query("SELECT product_id FROM product WHERE product_nr='" . $products[$i]['productID'] . "'");
				$r=$this->db->fetch(0);				
				if (empty($r) && $i >= $this->productFrom && $i <= $this->productTo) {					
					$actionLog .= $this->insertData($products[$i]);
					$this->insertedCnt++;						
				} elseif (isset($r->product_id)) { //product exist then update it
					if (!empty($input['update'])) {
						$actionLog .= "	Product ".$products[$i]['productID']." already exists. Update items: YES.\n";
						$productID = $r->product_id;								
						$actionLog .= $this->updateData($productID, $products[$i]);			
						$this->updatedCnt++;
					} else {
						$actionLog .= "	Product ".$products[$i]['productID']." already exists. Update items: NO.\n";
					}				
					
				} //if isset($r->product_id)
			}
			$this->validationResult = "	Percent of errors is ".$percent."%. Threshold is ".$input['threshold']."%.";
			$actionLog .= "(" . date("m.d.Y H:i:s") . ") Uploading of ". $input['realFileName'] . " is successfuly finished.\n";
			$actionLog .= "	Number of inserted products is " . $this->insertedCnt."\n";
			$actionLog .= "	Number of updated products is " . $this->updatedCnt."\n";
			$actionLogFile = fopen("../voc_logs/actions.log","a");
			fwrite($actionLogFile,$actionLog);
			fclose($actionLogFile);			
			$errorLog .= "(" . date("m.d.Y H:i:s") . ") Validation of ". $input['realFileName'] . " complete!\n";
			$errorLog .= "	Percent of errors is ".$percent."%. Threshold is ".$input['threshold']."%.\n";
			
			$this->actions = str_replace("\n","<br>",$actionLog);
			$this->actions = str_replace("	","&nbsp;&nbsp;",$this->actions);
		} else {
			$this->validationResult = "	Percent of errors is ".$percent."%. Threshold is ".$input['threshold']."%. <b>Correct input file, please.</b>";
			$errorLog .= "(" . date("m.d.Y H:i:s") . ") Validation of ". $input['realFileName'] . " failed!\n";
			$errorLog .= "	Percent of errors is ".$percent."%. Threshold is ".$input['threshold']."%. Correct input file, please.\n";			
		}		
		$validationLogFile = fopen("../voc_logs/validation.log","a");
		fwrite($validationLogFile,$errorLog);
		fclose($validationLogFile);	
		echo $errorLog;
		
    }
    
    //--------------private functions-------------------------------------
    
    private function insertData($product){
		
		//supplier
		$this->db->query("SELECT supplier_id FROM supplier WHERE supplier = '" . $product['MFG'] . "'");
		$r=$this->db->fetch(0);		
		if (empty($r->supplier_id)) {
			$actionLog .= "	Adding supplier ".$product['MFG']."\n";
			$this->db->query("INSERT INTO supplier (supplier) VALUES ('".$product['MFG']."')");
									
			$this->db->query("SELECT supplier_id FROM supplier WHERE supplier = '".$product['MFG']."'");
			$r=$this->db->fetch(0);
		}
		$supplier_id = $r->supplier_id;		

		//coating_id
		$this->db->query("SELECT coat_id FROM coat WHERE coat_desc = '".$product['coating']."'");
		$r=$this->db->fetch(0);
		if (empty($r->coat_id)) {
			$actionLog .= "	Adding coating ".$product['coating']."\n";
			$this->db->query("INSERT INTO coat (coat_desc) VALUES ('".$product['coating']."')");		
		
			$this->db->query("SELECT coat_id FROM coat WHERE coat_desc = '".$product['coating']."'");
			$r=$this->db->fetch(0);
		}
		$coating_id = $r->coat_id;

		//	hazardous			
		$chemicalClasses = $this->processChemicalClass($product);		
		/*$querySel = "SELECT hazardous_class_id " .
			 "FROM hazardous_class " .
			 "WHERE class = '".$product['hazardousClass']."' " .
			 "AND irr = '".$product['hazardousIRR']."' " .
			 "AND ohh = '".$product['hazardousOHH']."' " .
			 "AND sens = '".$product['hazardousSENS']."' " .
			 "AND oxy_1 = '".$product['hazardousOXY']."'";
		$this->db->query($querySel);
		$r=$this->db->fetch(0);
		if (empty($r->hazardous_class_id)) {
			$actionLog .= "	Adding Hazardous class = '".$product['hazardousClass']."', " .
			 	      "irr = '".$product['hazardousIRR']."', " .
			 	      "ohh = '".$product['hazardousOHH']."', " .
			 	      "sens = '".$product['hazardousSENS']."', " .
				      "oxy_1 = '".$product['hazardousOXY']."'\n";
			$queryIns = "INSERT INTO hazardous_class (class, irr, ohh, sens, oxy_1) " .
				 "VALUES ('".$product['hazardousClass']."', " .
				 "'".$product['hazardousIRR']."', " .
				 "'".$product['hazardousOHH']."', " .
				 "'".$product['hazardousSENS']."', " .
				 "'".$product['hazardousOXY']."')";
			$this->db->query($queryIns);			

			$this->db->query($querySel);
			$r=$this->db->fetch(0);
		}
		$hazardous_class_id = $r->hazardous_class_id;*/

		$tmpArray = array("vocwx","voclx","density","gavity","boiling_range_from","boiling_range_to");
		foreach ($tmpArray as $key) {
			if ($product[$key] == "") {
				$product[$key] = "NULL";
			} else {
				$product[$key] = str_replace(",", ".", $product[$key]);
			}
		}

		$tmpArray = array("specialtyCoating","aerosol");
		foreach ($tmpArray as $key) {
			if (empty($product[$key])) {
				$product[$key] = "no";
			}
		}
		// query with old hazardous (chemical system) 
		/*$query = "INSERT INTO product (product_nr, name, inventory_id, voclx, vocwx, density, coating_id, " .
					"specialty_coating, aerosol, specific_gravity, boiling_range_from, " . 
					"boiling_range_to, hazardous_class_id, supplier_id) " .
			 "VALUES ('".$product['productID']."', '" .
				 $product['productName']."', " .
				 $this->inventoryID.", " .
				 $product['voclx'].", " .
				 $product['vocwx'].", " .
				 $product['density'].", " .
				 $coating_id.", '" .
				 $product['specialtyCoating']."', '" .
				 $product['aerosol']."', " .
				 $product['gavity'].", " .
				 $product['boilingRangeFrom'].", " .
				 $product['boilingRangeTo'].", " .
				 $hazardous_class_id.", " .
				 $supplier_id.")";*/
				 
		$query = "INSERT INTO product (product_nr, name, inventory_id, voclx, vocwx, density, density_unit_id, coating_id, " .
					"specialty_coating, aerosol, specific_gravity, boiling_range_from, " . 
					"boiling_range_to, supplier_id) " .
			 "VALUES ('".$product['productID']."', '" .
				 $product['productName']."', " .
				 $this->inventoryID.", " .
				 $product['voclx'].", " .
				 $product['vocwx'].", " .
				 $product['density'].", " .
				 "1, ".									// The default density is measured in lbs/gal
				 $coating_id.", '" .
				 $product['specialtyCoating']."', '" .
				 $product['aerosol']."', " .
				 $product['gavity'].", " .
				 $product['boilingRangeFrom'].", " .
				 $product['boilingRangeTo'].", " .				 
				 $supplier_id.")";		 
		$this->db->query($query);

		if (mysql_errno()==0) {
			$productID = $this->db->getLastInsertedID();//mysql_insert_id(); OLD
			$actionLog .= "	Adding product " . $product['productID'] . "\n";
			
			//	set product to company link
			if (!empty($this->companyID)) {
				$this->productObj->assignProduct2Company($productID, $this->companyID);	
			}
			
			//	set product to chemical class link
			$this->hazardousObj->setProduct2ChemicalClasses($productID, $chemicalClasses);			
			
			//	component part
			for ($i=0;$i<count($product['component']);$i++){
				$actionLog .= $this->addComponentToProduct($product['component'][$i],$productID,$product['productID']);		
			}					
		} else { 
			$productID = 0;
			$actionLog .= "	Error while adding product " . $product['productID'] . "\n";	
		}		

		return $actionLog;
	}
	
		
	private function updateData($productID, $product) {
		
		//supplier
		$this->db->query("SELECT supplier_id FROM supplier WHERE supplier = '" . $product['MFG'] . "'");
		$r=$this->db->fetch(0);
		if (empty($r->supplier_id)) {
			$actionLog .= "		Adding supplier ".$product['MFG']."\n";
			$this->db->query("INSERT INTO supplier (supplier) VALUES ('".$product['MFG']."')");			
					
			$this->db->query("SELECT supplier_id FROM supplier WHERE supplier = '".$product['MFG']."'");
			$r=$this->db->fetch(0);
		}
		$supplier_id = $r->supplier_id;

		//coating_id
		$this->db->query("SELECT coat_id FROM coat WHERE coat_desc = '".$product['coating']."'");
		$r=$this->db->fetch(0);
		if (empty($r->coat_id)) {
			$actionLog .= "		Adding coating ".$product['coating']."\n";
			$this->db->query("INSERT INTO coat (coat_desc) VALUES ('".$product['coating']."')");			
				
			$this->db->query("SELECT coat_id FROM coat WHERE coat_desc = '".$product['coating']."'");
			$r=$this->db->fetch(0);
		}
		$coating_id = $r->coat_id;

		//hazrdous class	
		$chemicalClasses = $this->processChemicalClass($product);				
		/*$tmpArray = array("hazardousIRR","hazardousOHH","hazardousSENS","hazardousOXY");
		foreach ($tmpArray as $key) {
			if (empty($product[$key])) {
				$product[$key] = "no";
			} else {
				$product[$key] = "yes";
			}
		}
		$querySel = "SELECT hazardous_class_id " .
			    "FROM hazardous_class " .
			    "WHERE class = '".$product['hazardousClass']."' " .
			    "AND irr = '".$product['hazardousIRR']."' " .
			    "AND ohh = '".$product['hazardousOHH']."' " .
			    "AND sens = '".$product['hazardousSENS']."' " .
			    "AND oxy_1 = '".$product['hazardousOXY']."'";
		$this->db->query($querySel);					
		$row_haz=$this->db->fetch(0);
		if (empty($row_haz)) {
			$actionLog .= "		Adding Hazardous class = '".$product['hazardousClass']."', " .
			 	      "irr = '".$product['hazardousIRR']."', " .
			 	      "ohh = '".$product['hazardousOHH']."', " .
			 	      "sens = '".$product['hazardousSENS']."', " .
				      "oxy_1 = '".$product['hazardousOXY']."'\n";
			$queryIns = "INSERT INTO hazardous_class (class, irr, ohh, sens, oxy_1) " .
				 	"VALUES ('".$product['hazardousClass']."', " .
					 "'".$product['hazardousIRR']."', " .
					 "'".$product['hazardousOHH']."', " .
					 "'".$product['hazardousSENS']."', " .
					 "'".$product['hazardousOXY']."')";
			$this->db->query($queryIns);			
			$row_haz->hazardous_class_id = mysql_insert_id();
		}*/



		$tmpArray = array("vocwx","voclx","density","gavity","boiling_range_from","boiling_range_to");
		foreach ($tmpArray as $key) {
			if ($product[$key] == "") {
				$product[$key] = "NULL";
			} else {
				$product[$key] = str_replace(",", ".", $product[$key]);
			}
		}

		$tmpArray = array("specialtyCoating","aerosol");
		foreach ($tmpArray as $key) {
			if (empty($product[$key])) {
				$product[$key] = "no";
			}
		}

		$actionLog .= "		Updating product ".$product['productID']."\n";
		$queryUpd = "UPDATE product " .
				"SET name='".$product['productName']."', " .
					"voclx=".$product['voclx'].", " .
					"vocwx=".$product['vocwx'].", " .
					"density=".$product['density'].", " .
					//"density_unit_id=1, ".				// The default density is measured in lbs/gal
					"coating_id=".$coating_id.", " .
					"specific_gravity=".$product['gavity'].", " .
					"specialty_coating='".$product['specialtyCoating']."', " .
					"aerosol='".$product['aerosol']."', " .
					"boiling_range_from=".$product['boilingRangeFrom'].", " .
					"boiling_range_to=".$product['boilingRangeTo'].", " .
					//"hazardous_class_id='".$row_haz->hazardous_class_id."', " .
					"supplier_id=".$supplier_id." " .
				"WHERE product_id = ".$productID;
		$this->db->query($queryUpd);
		
		//	set product to company link
		if (!empty($this->companyID)) {
			$this->productObj->assignProduct2Company($productID, $this->companyID);	
		}
			
		//	set product to chemical class link
		$this->hazardousObj->setProduct2ChemicalClasses($productID, $chemicalClasses);		

		//component part
		//delete old data
		$actionLog .= "			Deleting old components from product ".$product['productID']."\n";
		$this->db->query("DELETE FROM components_group WHERE product_id = " . $productID);		

		//add new components
		for ($i=0;$i<count($product['component']);$i++){
			$actionLog .= $this->addComponentToProduct($product['component'][$i],$productID,$product['productID']);
		}	
		return $actionLog;	
	}
	
	
	private function addComponentToProduct($component,$productID,$product) {
		//component
		$query = "SELECT component_id " .
			"FROM component " .
			"WHERE cas = '" .$component['caseNumber'] . "'";
		$this->db->query($query);
		$r=$this->db->fetch(0);
			
		if (empty($r->component_id)){//adding component
			$actionLog .= "				Adding component '".$component['caseNumber']."','".$component['description'] ."'\n";
			$this->db->query("INSERT INTO component (cas, description) VALUES ('".$component['caseNumber']."', " .
					"'".$component['description']."')");			

			$query = "SELECT component_id " .
				"FROM component " .
				"WHERE cas = '" .$component['caseNumber'] . "' " .
				"AND description = '" . $component['description'] . "'";
			$this->db->query($query);
			$r=$this->db->fetch(0);
		}
		$componentID = $r->component_id;

		//substrate
		if ( !empty($component['substrate']) ){
			$this->db->query("SELECT substrate_id FROM substrate WHERE substrate_desc = '".$component['substrate']."'");
			$r=$this->db->fetch(0);
			if (empty($r->substrate_id)) {
				$actionLog .= "				Adding substrate '" . $component['substrate']."'\n";	
				$this->db->query("INSERT INTO substrate (substrate_desc) VALUES ('".$component['substrate']."')");				
	
				$this->db->query("SELECT substrate_id FROM substrate WHERE substrate_desc = '".$component['substrate']."'");
				$r=$this->db->fetch(0); 
			}
			$substrateID = $r->substrate_id;
		} else {
			$substrateID = "NULL";
		}

		//rule
		if ( !empty($component['rule']) ){			
			$this->db->query("SELECT rule_id FROM rule WHERE ".$this->ruleObj->ruleNrMap[$this->ruleObj->getRegion()]." = '".$component['rule']."'");
			$r=$this->db->fetch(0);
			if ( empty($r->rule_id) ) {
				$r->rule_id = "NULL";
			}
			$ruleID = $r->rule_id;
		} else {
			$ruleID = "NULL";
		}

		$tmpArray = array("mmhg","temp","weight");
		foreach ($tmpArray as $key) {
			if ($component[$key] == "") {
				$component[$key] = "NULL";
			} else {
				$component[$key] = str_replace(",", ".", $component[$key]);
			}
		}
		$component['weight'] = str_replace("%", "", $component['weight']);
		
		if ($component['vocpm'] == "") {
			$component['vocpm'] = "VOC";
		}			

		//component group insertion

		$query="INSERT INTO components_group (component_id, product_id, substrate_id, rule_id, mm_hg, temp, weight, type) ".
			"VALUES (" . $componentID . ", " .
				$productID . ", " .
				$substrateID . ", " .
				$ruleID . ", " .
				$component['mmhg'] . ", " .
				$component['temp'] . ", " .
				$component['weight'] . ", '" .
				$component['vocpm'] . "')";
		$this->db->query($query);

		if (mysql_errno()==0) {
			//$productID = mysql_insert_id();
			$actionLog .= "			Adding component " .$component['caseNumber'] . " to product " .$product."\n";			
		} else { 
			//$productID = 0;
			$actionLog .= "			Error while adding component " . $component['caseNumber'] . " to product " . $product . "\n";
		}

		return $actionLog;
	}
	
	private function processChemicalClass($product) {
		//$this->db->select_db(DB_NAME);
		$hazardousClasses = array();
		
		$tmpArray 	= array("hazardousIRR","hazardousOHH","hazardousSENS","hazardousOXY");
		$realNames 	= array("IRR","OHH","SENS","OXY-1");
		foreach ($tmpArray as $index=>$key) {
			if (!empty($product[$key])) {			
				$querySel = "SELECT id FROM chemical_class WHERE name = '".$realNames[$index]."'";
				$this->db->query($querySel);
				$data = $this->db->fetch(0);
				if (!empty($data->id)) {
					$hazardousClasses[] = $data->id;			
				}			
			}
		}			
		
		$chemicalClass = strtoupper($product['hazardousClass']);
		$querySel = "SELECT id FROM chemical_class WHERE name = '".$chemicalClass."'";
		$this->db->query($querySel);
		$data = $this->db->fetch(0);		
		if (empty($data->id)) {
			$actionLog .= "	Adding Hazardous class = '".$chemicalClass."'\n";
			$queryIns = "INSERT INTO chemical_class (name) VALUES ('".$chemicalClass."')";
			$this->db->query($queryIns);			

			$this->db->query($querySel);
			$data = $this->db->fetch(0);
		}
		$hazardousClasses[] = $data->id;
				
		return $hazardousClasses; 
	}
			
}
?>