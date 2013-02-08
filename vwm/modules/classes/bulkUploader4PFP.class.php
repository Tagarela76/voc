<?php

class bulkUploader4PFP {

	/**
	 * @var db
	 */
	private $db;
	private $productFrom = 0;
	private $productTo;
	private $inventoryID = 0;
	private $productObj;
	private $hazardousObj;
	private $ruleObj;
	private $companyID;
	private $is_proprietary = 0;
	//private $compType = 'VOC';

	public $productsError;
	public $productsCorrect;
	public $insertedCnt;
	public $updatedCnt;
	public $validationResult;
	public $actions;
	

	const PRODUCTNR_INDEX = 2;
	const PRODUCTRATIO_INDEX = 4;
	const PRODUCTUNITTYPE_INDEX = 5;
	const PRODUCTNAME_INDEX = 3;
	const INTELLECTUAL_PROPRIETARY = 6;

	public function getIsProprietary() {
		return $this->is_proprietary;
	}

	public function setIsProprietary($is_proprietary) {
		$this->is_proprietary = $is_proprietary;
	}
	
	function bulkUploader4PFP(db $db, $input, validateCSV $validate) {
		
		$pfpArray = $validate->productsCorrect;
		$this->db = $db;

		$this->productObj = new Product($db);
		$this->hazardousObj = new Hazardous($db);
		$this->ruleObj = new Rule($db);
		$this->companyID = (empty($input['companyID'])) ? 0 : $input['companyID'];
		

//    	//$this->db->select_db(DB_NAME);

		$this->productTo = $input['maxNumber'];

		$path = $input['inputFile'];

		$this->insertedCnt = 0;
		$this->updatedCnt = 0;

		$actionLog = "--------------------------------\n";
		$actionLog .= "(" . date("m.d.Y H:i:s") . ") Starting uploading of " . $input['realFileName'] . "...\n";

		//creating_backup
		$sqlFile = "db_backup/" . DB_NAME . "_" . date('Y_m_d_H_i_s') . ".sql";
		$creatBackup = "mysqldump -h " . DB_HOST . " -u " . DB_USER . " --password=" . DB_PASS . " " . DB_NAME . " > " . $sqlFile;
		//exec($creatBackup);
		
		foreach ($pfpArray as $products) {
		
			$productIDS = array();
			$productRATIOS = array();
			$productRATIOSTo = array();
			$productRATIOSFromOriginal = array();
			$productRATIOSToOriginal = array();
		//	$description = '/ ';
			// set pfp intellectual proprietary
			$this->setIsProprietary($this->convertPfpIProprietary($products[0][self::INTELLECTUAL_PROPRIETARY]));
			
			
			for ($i = 0; $i < count($products); $i++) {

				$this->db->query("SELECT product_id FROM product WHERE product_nr='" . $products[$i][self::PRODUCTNR_INDEX] . "'");
				$r = $this->db->fetch(0);

				if (empty($r)) {
					$actionLog .= " Product " . $products[$i][self::PRODUCTNR_INDEX] . " doesn't exist \n";
				} elseif (isset($r->product_id)) { //product exist
					if ($products[$i][self::PRODUCTRATIO_INDEX] >= 1) {
						$productIDS[] = $r->product_id;
						$productRATIOS[] = $products[$i][self::PRODUCTRATIO_INDEX];
						$productRATIOSTo[] = (isset($products[$i]['ratioRangeTo']))
								? $products[$i]['ratioRangeTo']
								: false;
						$productRATIOSFromOriginal[] = (isset($products[$i]['ratioRangeFromOriginal']))
								? $products[$i]['ratioRangeFromOriginal']
								: false;
						$productRATIOSToOriginal[] = (isset($products[$i]['ratioRangeToOriginal']))
								? $products[$i]['ratioRangeToOriginal']
								: false;

						if ($i == 0) {
							$description .= $products[$i][self::PRODUCTNAME_INDEX] . " / " . $products[$i][self::PRODUCTNR_INDEX] . " / ";
						} else {
							$description .= $products[$i][self::PRODUCTNR_INDEX] . " / ";
						}
						
					} else {
						$actionLog .= " Product " . $products[$i][self::PRODUCTNR_INDEX] . " has ratio less than 1 \n";
					}
				}
			}//end for

			if (count($products) == count($productIDS)) { // all products exists
				if ($description != '') {
					$this->db->query("SELECT description FROM preformulated_products WHERE description = '" . $description . "'");
					$r = $this->db->fetch(0);
					if (empty($r)) {
						$actionLog .= $this->insertData($productIDS, $productRATIOS, $productRATIOSTo, $productRATIOSFromOriginal, $productRATIOSToOriginal, $this->companyID, $description);
						$this->insertedCnt++;
					} elseif (isset($r->description)) { //pfp exist
						if (!empty($input['update'])) {
							$actionLog .= "	PFP " . $r->description . " already exists. Update items: YES.\n";
							$actionLog .= $this->updateData($productIDS, $productRATIOS, $productRATIOSTo, $productRATIOSFromOriginal, $productRATIOSToOriginal, $this->companyID, $r->description);
							$this->updatedCnt++;
						} else {
							$actionLog .= "	PFP " . $r->description . " already exists. Update items: NO.\n";
						}
					}
				} else {
					$actionLog .= " PFP with products hasn't description. \n";
				}
				$description = '/ ';
			}
		}//end foreach

		$actionLog .= "--------------------------------\n";
		$actionLog .= "(" . date("m.d.Y H:i:s") . ") Uploading of " . $input['realFileName'] . " is successfuly finished.\n";
		$actionLog .= "	Number of inserted pfps is " . $this->insertedCnt . "\n";
		$actionLog .= "	Number of updated pfps is " . $this->updatedCnt . "\n";
		$actionLogFile = fopen(DIR_PATH_LOGS . "actions.log", "a");
		fwrite($actionLogFile, $actionLog);
		fclose($actionLogFile);

		$this->actions = str_replace("\n", "<br>", $actionLog);
		$this->actions = str_replace("	", "&nbsp;&nbsp;", $this->actions);
	}

	
	//--------------private functions-------------------------------------

	

		private function insertData($productIDS, $productRATIOS, $productRATIOSTo, $productRATIOSFromOriginal, $productRATIOSToOriginal, $companyID, $description) {
		if (!isset($description)) {
			$description = microtime();
		}
		$actionLog .= "	Adding pfp " . $description . "\n";

		$sql = "INSERT INTO preformulated_products  ".
				"(description, ".
				"company_id, ".
				"creater_id, ".
				"last_update_time, ". 
				"is_proprietary) ".
				"VALUES ".
				"('{$this->db->sqltext($description)}', ".
				"{$this->db->sqltext($companyID)}, ".
				"NULL, ".
				" NOW(), ".
				"{$this->db->sqltext($this->getIsProprietary())})";
				
		$this->db->query($sql);

		$this->db->query("SELECT id FROM preformulated_products WHERE description = '" . $description . "'");
		$r = $this->db->fetch(0);
		$pfp_id = $r->id;
		if ($companyID != 0) {
			$sql = "INSERT INTO pfp2company (pfp_id ,company_id) VALUES (" . $pfp_id . ", " . $companyID . ")";
			$this->db->query($sql);
		}
		$primary = 1;
		for ($i = 0; $i < count($productIDS); $i++) {
			$actionLog .= "	Adding product to TB_ pfp2product " . $productIDS[$i] . "\n";

			$ratio_to = ($productRATIOSTo[$i]) ? $productRATIOSTo[$i] : " NULL ";
			$ratio_from_original = ($productRATIOSFromOriginal[$i]) ? $productRATIOSFromOriginal[$i] : " NULL ";
			$ratio_to_original = ($productRATIOSToOriginal[$i]) ? $productRATIOSToOriginal[$i] : " NULL ";

			$sql = "INSERT INTO pfp2product (ratio,	ratio_to, ratio_from_original, ratio_to_original, product_id, preformulated_products_id,isPrimary) VALUES " .
					"(" . $productRATIOS[$i] . " " .
					", " .$ratio_to.
					", " .$ratio_from_original.
					", " .$ratio_to_original.
					", {$productIDS[$i]}, {$pfp_id}, {$primary})";
			$this->db->query($sql);

			$primary = 0;
		}
		return $actionLog;
	}

	private function updateData($productIDS, $productRATIOS, $productRATIOSTo, $productRATIOSFromOriginal, $productRATIOSToOriginal, $companyID, $description) {

		$this->db->query("SELECT id FROM preformulated_products WHERE description = '" . $description . "'");
		if ($this->db->num_rows() == 0) {
			throw new Exception("Cannot update PFP ".$description);
		}
		$r = $this->db->fetch(0);
		$pfp_id = $r->id;

		$actionLog .= "	Updating pfp " . $description . "\n";
		
			$sql = "UPDATE preformulated_products SET ".
					"company_id = {$this->db->sqltext($companyID)}, ".
					"is_proprietary = {$this->db->sqltext($this->getIsProprietary())}, ".
					"last_update_time = NOW() ".
					"WHERE id = {$this->db->sqltext($pfp_id)}";
					
			$this->db->query($sql);
		if ($companyID != 0) {
			$sql = "INSERT INTO pfp2company (pfp_id ,company_id) VALUES (" . $pfp_id . ", " . $companyID . ")";
			$this->db->query($sql);
		}

		$actionLog .= "Deleting old prducts from PFP " . $description . "\n";
		$this->db->query("DELETE FROM pfp2product WHERE preformulated_products_id={$pfp_id}");

		$primary = 1;
		for ($i = 0; $i < count($productIDS); $i++) {
			$actionLog .= "	Updating product in TB_ pfp2product " . $productIDS[$i] . "\n";

			$ratio_to = ($productRATIOSTo[$i]) ? $productRATIOSTo[$i] : " NULL ";
			$ratio_from_original = ($productRATIOSFromOriginal[$i]) ? $productRATIOSFromOriginal[$i] : " NULL ";
			$ratio_to_original = ($productRATIOSToOriginal[$i]) ? $productRATIOSToOriginal[$i] : " NULL ";

			$sql = "INSERT INTO pfp2product (ratio,	ratio_to, ratio_from_original, ratio_to_original, product_id, preformulated_products_id,isPrimary) VALUES " .
					"(" . $productRATIOS[$i] . " " .
					", " .$ratio_to.
					", " .$ratio_from_original.
					", " .$ratio_to_original.
					", {$productIDS[$i]}, {$pfp_id}, {$primary})";
			$this->db->query($sql);

			$primary = 0;
		}
		return $actionLog;
	}


	public static function isRangeRatio($ratioField) {
		$ratioField = str_replace(' ', '', $ratioField);
		return preg_match("/^\d+\-\d+\%$/", $ratioField);
	}


	public static function splitRangeRatio($ratioField) {
		$ratioField = str_replace(' ', '', $ratioField);
		$ratioField = str_replace('%', '', $ratioField);
		return split('-', $ratioField);
	}


	public static function isRtuOrRtsRatio($ratioField) {
		$possibleValues = array('RTU', 'RTS');
		return in_array(trim(strtoupper($ratioField)), $possibleValues);

	}
	
	public static function isProprietary($isProprietary) {
		$possibleValues = array('1', 'IP', '', '0');
		return in_array(trim(strtoupper($isProprietary)), $possibleValues);

	}
	
	/**
	 * function for converting pfps intellectual proprietary to boolean type
	 * @string isProprietary
	 * return bool 
	 */
	private function convertPfpIProprietary($isProprietary=0){
		//correct values
		
		if($isProprietary == '1' || $isProprietary == '0'){
			$this->setIsProprietary($isProprietary);
			return $isProprietary;
		}
		elseif($isProprietary == 'IP'){
			$this->setIsProprietary(1);
			return 1;
		}
		elseif(trim($isProprietary == '')){
			$this->setIsProprietary(0);
			return 0;
		}else{
			return false;
		}
		
	}
	

}

?>