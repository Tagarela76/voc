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

//    	//$this->db->select_db(DB_NAME);

    	$this->productTo = $input['maxNumber'];

    	$path = $input['inputFile'];

    	$this->insertedCnt = 0;
		$this->updatedCnt = 0;

		$actionLog = "--------------------------------\n";
		$actionLog .= "(" . date("m.d.Y H:i:s") . ") Starting uploading of ". $input['realFileName'] . "...\n";

		$validation = new validateCSV($this->db);
		$validation->validate($input);


		$errorLog = $validation->errorComments;

		//partition
		$cntCorrect = count($validation->productsCorrect);
		$cntError = count($validation->productsError);
		$total = $cntError+$cntCorrect;
		$percent = round($cntError*100/($cntCorrect+$cntError),2);
		$this->productsCorrect = $validation->productsCorrect;
		$this->productsError = $validation->productsError;


		//creating_backup
    	$sqlFile = "db_backup/".DB_NAME."_".date('Y_m_d_H_i_s').".sql";
    	$creatBackup = "mysqldump -h ".DB_HOST." -u ".DB_USER." --password=".DB_PASS." ".DB_NAME." > ".$sqlFile;
		//exec($creatBackup);

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

		$this->validationResult = "	Percent of errors is ".$percent."%.";
		$actionLog .= "(" . date("m.d.Y H:i:s") . ") Uploading of ". $input['realFileName'] . " is successfuly finished.\n";
		$actionLog .= "	Number of inserted products is " . $this->insertedCnt."\n";
		$actionLog .= "	Number of updated products is " . $this->updatedCnt."\n";
		$actionLogFile = fopen(DIR_PATH_LOGS."actions.log","a");
		fwrite($actionLogFile,$actionLog);
		fclose($actionLogFile);
		$errorLog .= "(" . date("m.d.Y H:i:s") . ") Validation of ". $input['realFileName'] . " complete!\n";
		$errorLog .= "	Percent of errors is ".$percent."%. Threshold is ".$input['threshold']."%.\n";

		$this->actions = str_replace("\n","<br>",$actionLog);
		$this->actions = str_replace("	","&nbsp;&nbsp;",$this->actions);

		//$this->validationResult = "	Percent of errors is ".$percent."%. Threshold is ".$input['threshold']."%. <b>Correct input file, please.</b>";
		$errorLog .= "(" . date("m.d.Y H:i:s") . ") Validation of ". $input['realFileName'] . " failed!\n";
		$errorLog .= "	Percent of errors is ".$percent."%.";

		$validationLogFile = fopen(DIR_PATH_LOGS."validation.log","a");
		fwrite($validationLogFile,$errorLog);
		fclose($validationLogFile);
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
			$this->db->query("UPDATE supplier SET original_id = {$r->supplier_id} WHERE supplier_id = {$r->supplier_id}");
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


		$query = "INSERT INTO product (product_nr, name, voclx, vocwx, density, density_unit_id, coating_id, " .
					"specific_gravity, specific_gravity_unit_id, boiling_range_from, " .
					"boiling_range_to, flash_point, supplier_id, percent_volatile_weight, percent_volatile_volume, closed, discontinued, product_pricing, price_unit_type) " .
			 "VALUES ('".$product['productID']."', '" .
				 $product['productName']."', " .
				 $product['voclx'].", " .
				 $product['vocwx'].", " .
				 $product['density'].", " .
				 "1, ".									// The default density is measured in lbs/gal
				 $coating_id.", " .
				 $product['gavity'].", " .
				 "1, ".									// The default density is measured in lbl/gal
				 $product['boilingRangeFrom'].", " .
				 $product['boilingRangeTo'].", " .
				 $product['flashPoint'].", " .
				 $supplier_id.", " .
				 "".$product['percentVolatileWeight'].", " .
				 "".$product['percentVolatileVolume'].", " .
				 "'".$product['closed']."', ".
				 $product['discontinued'] .", ".
				 $product['productPricing'] .", ".
				 $product['unitType'] ." ".
				 ")"; 
		$this->db->query($query);

		if (mysql_errno()==0) {
			//$productID = mysql_insert_id();//OLD
			$productID = $this->db->getLastInsertedID();
			$actionLog .= "	Adding product " . $product['productID'] . "\n";

			//	set product to company link
			if (!empty($this->companyID)) {
				$this->productObj->assignProduct2Company($productID, $this->companyID);
			}

			//	waste class processing
			if (!empty($product['waste'])) {
				$wasteClasses = explode(',',$product['waste']);
				foreach ($wasteClasses as $wasteClass) {
					$wasteClass = trim($wasteClass);
					$querySel = "SELECT id FROM waste_class WHERE name = '".$wasteClass."'";
					$this->db->query($querySel);
					if ($this->db->num_rows() == 0) {
						$query = "INSERT INTO waste_class (name) VALUES ('".$wasteClass."')";
						$this->db->exec($query);

						$this->db->query($querySel);
						$wasteClassID = $this->db->fetch(0)->id;
					} else {
						$wasteClassID = $this->db->fetch(0)->id;
					}

					$this->productObj->assignProduct2WasteClass($productID, $wasteClassID);
				}
			}

			// set product to type
			foreach ($product['industryType'] as $industryType){
				$this->productObj->assignProduct2Type($productID, $industryType['industryType'], $industryType['industrySubType']);
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
			$this->db->query("UPDATE supplier SET original_id = {$r->supplier_id} WHERE supplier_id = {$r->supplier_id}");
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
					"density_unit_id=1, ".				// The default density is measured in lbs/gal
					"coating_id=".$coating_id.", " .
					"specific_gravity=".$product['gavity'].", " .
					"boiling_range_from=".$product['boilingRangeFrom'].", " .
					"boiling_range_to=".$product['boilingRangeTo'].", " .
					"flash_point=".$product['flashPoint'].", " .
					"supplier_id=".$supplier_id.", " .
					"percent_volatile_weight = ".$product['percentVolatileWeight'].", " .
					"percent_volatile_volume = ".$product['percentVolatileVolume'].", " .
					"closed='".$product['closed']."', ".
				    "discontinued =" . $product['discontinued'] . ", " .
					"product_pricing =" . $product['productPricing'] . ", " .
					"price_unit_type =" . $product['unitType'] . "" .
				" WHERE product_id = ".$productID;   
		$this->db->query($queryUpd); 

		//	set product to company link
		if (!empty($this->companyID)) {
			$this->productObj->assignProduct2Company($productID, $this->companyID);
		}

		//	waste class processing
		if (!empty($product['waste'])) {
			$wasteClasses = explode(',',$product['waste']);
			foreach ($wasteClasses as $wasteClass) {
				$wasteClass = trim($wasteClass);
				$querySel = "SELECT id FROM waste_class WHERE name = '".$wasteClass."'";
				$this->db->query($querySel);
				if ($this->db->num_rows() == 0) {
					$query = "INSERT INTO waste_class (name) VALUES ('".$wasteClass."')";
					$this->db->exec($query);

					$this->db->query($querySel);
					$wasteClassID = $this->db->fetch(0)->id;
				} else {
					$wasteClassID = $this->db->fetch(0)->id;
				}

				$this->productObj->assignProduct2WasteClass($productID, $wasteClassID);
			}
		}

		//updating industy type and subtype
		$this->productObj->unassignProductFromType($productID);
		foreach ($product['industryType'] as $industryType){
			$this->productObj->assignProduct2Type($productID, $industryType['industryType'], $industryType['industrySubType']);
		}

		//	set product to chemical class link
		$this->hazardousObj->setProduct2ChemicalClasses($productID, $chemicalClasses);

		//component part
		//delete old data
		$actionLog .= "			Deleting old components from product ".$product['productID']."\n";
		$this->db->query("DELETE FROM components_group WHERE product_id = " . $productID);

		//add new components
		for ($i=0;$i<count($product['component']);$i++){
			$actionLog .= $this->addComponentToProduct($product['component'][$i],$productID,$product['productID']);	//	$product['productID'] - name
		}
		return $actionLog;
	}


	private function addComponentToProduct($component,$productID,$product) {
		//component
		$query = "SELECT component_id " .
			"FROM component " .
			"WHERE cas = '" .$component['caseNumber'] . "' " .
			"AND description = '" . $component['description'] . "'";
		$this->db->query($query);
		$r=$this->db->fetch(0);

		if (empty($r->component_id)){//adding component
			$actionLog .= "				Adding component '".$component['caseNumber']."','".$component['description'] ."'\n";
			$this->db->query("INSERT INTO component (einecs_elincs, substance_symbol, cas, description) VALUES (" .
					"'".$component['einecsElincs']."', " .
					"'".$component['substanceSymbol']."', " .
					"'".$component['caseNumber']."', " .
					"'".$component['description']."')");

			$query = "SELECT component_id " .
				"FROM component " .
				"WHERE cas = '" .$component['caseNumber'] . "' " .
				"AND description = '" . $component['description'] . "'";
			$this->db->query($query);
			$r=$this->db->fetch(0);

			//	substance rules
			if ( !empty($component['substanceR'])) {
				$substanceRs = $this->processSubstanceR($component['substanceR']);
				foreach ($substanceRs as $substanceR) {
					$query = "INSERT INTO component2rule (component_id, rule_id) VALUES (".$r->component_id.", ".$substanceR.")";
					$this->db->exec($query);
				}
			}
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

		$tmpArray = array("mmhg","temp","weightFrom","weightTo");
		foreach ($tmpArray as $key) {
			if ($component[$key] == "") {
				$component[$key] = "NULL";
			} else {
				$component[$key] = str_replace(",", ".", $component[$key]);
			}
		}
		$component['weightFrom'] = str_replace("%", "", $component['weightFrom']);
		$component['weightTo'] = str_replace("%", "", $component['weightTo']);
		$component['temp'] = trim($component['temp']);

		if ($component['vocpm'] == "") {
			$component['vocpm'] = "VOC";
		}

		//component group insertion

		$query="INSERT INTO components_group (component_id, product_id, substrate_id, rule_id, mm_hg, temp, weight_from, weight_to, type) ".
			"VALUES (" . $componentID . ", " .
				$productID . ", " .
				$substrateID . ", " .
				$ruleID . ", " .
				$component['mmhg'] . ", " .
				$component['temp'] . ", " .
				$component['weightFrom'] . ", " .
				$component['weightTo'] . ", '" .
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




	/*
	 * return array
  0 =>
    array
      'id' => string '1' (length=1)
      'rules' =>
        array
          0 => string '61' (length=2)
          1 => string '58' (length=2)
          2 => string '135' (length=3)
          3 => string '140' (length=3)
          4 => string '115' (length=3)
          5 => string '128' (length=3)
	 */
	private function processChemicalClass($product) {

//		//$this->db->select_db(DB_NAME);
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




	private function processSubstanceR($substanceR) {
		$rules = explode(',',$substanceR);
		foreach ($rules as $rule) {
			$rule = trim(strtoupper($rule));
			$querySel = "SELECT rule_id FROM rule WHERE ".$this->ruleObj->ruleNrMap[$this->ruleObj->getRegion()]." = '".$rule."'";
			$this->db->query($querySel);
			$dataRule = $this->db->fetch(0);
			if (empty($dataRule->rule_id)) {
				$actionLog .= "	Adding Rule = '".$rule."'\n";
				$queryIns = "INSERT INTO ".TB_RULE." (".$this->ruleObj->ruleNrMap[$this->ruleObj->getRegion()].") VALUES ('".$rule."')";
				$this->db->query($queryIns);

				$this->db->query($querySel);
				$dataRule = $this->db->fetch(0);
			}
			$rulesID[] = $dataRule->rule_id;
		}
		return $rulesID;
	}

}
?>