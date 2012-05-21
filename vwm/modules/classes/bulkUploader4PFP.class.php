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
			$description = '/ ';

			for ($i = 0; $i < count($products); $i++) {
				
				$this->db->query("SELECT product_id FROM product WHERE product_nr='" . $products[$i][self::PRODUCTNR_INDEX] . "'");
				$r = $this->db->fetch(0);

				if (empty($r)) {
					$actionLog .= " Product " . $products[$i][self::PRODUCTNR_INDEX] . " doesn't exist \n";
				} elseif (isset($r->product_id)) { //product exist			
					if ($products[$i][self::PRODUCTRATIO_INDEX] >= 1) {
						$productIDS[] = $r->product_id;
						$productRATIOS[] = $products[$i][self::PRODUCTRATIO_INDEX];

						$description .= $products[$i][self::PRODUCTNR_INDEX] . " / ";
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
						$actionLog .= $this->insertData($productIDS, $productRATIOS, $this->companyID, $description);
						$this->insertedCnt++;
					} elseif (isset($r->description)) { //pfp exist	
						if (!empty($input['update'])) {
							$actionLog .= "	PFP " . $r->description . " already exists. Update items: YES.\n";
							$actionLog .= $this->updateData($productIDS, $productRATIOS, $this->companyID, $r->description);
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

	private function insertData($productIDS, $productRATIOS, $companyID, $description) {
		if (!isset($description)) {
			$description = microtime();
		}
		$actionLog .= "	Adding pfp " . $description . "\n";

		$sql = "INSERT INTO preformulated_products  (description,company_id,creater_id) VALUES ('" . $description . "',{$companyID},NULL)";
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
			$sql = "INSERT INTO pfp2product (ratio,product_id,preformulated_products_id,isPrimary) VALUES ('" . $productRATIOS[$i] . "','" . $productIDS[$i] . "',{$pfp_id},{$primary})";
			$this->db->query($sql);

			$primary = 0;
		}
		return $actionLog;
	}

	private function updateData($productIDS, $productRATIOS, $companyID, $description) {

		$this->db->query("SELECT id FROM preformulated_products WHERE description = '" . $description . "'");
		$r = $this->db->fetch(0);
		$pfp_id = $r->id;

		$actionLog .= "	Updating pfp " . $description . "\n";
		if ($companyID != 0) {
			$sql = "UPDATE preformulated_products SET company_id= " . $companyID . " WHERE id=" . $pfp_id . "";
			$this->db->query($sql);

			$sql = "INSERT INTO pfp2company (pfp_id ,company_id) VALUES (" . $pfp_id . ", " . $companyID . ")";
			$this->db->query($sql);
		}

		$actionLog .= "Deleting old prducts from PFP " . $description . "\n";
		$this->db->query("DELETE FROM pfp2product WHERE preformulated_products_id={$pfp_id}");

		$primary = 1;
		for ($i = 0; $i < count($productIDS); $i++) {
			$actionLog .= "	Updating product in TB_ pfp2product " . $productIDS[$i] . "\n";
			$sql = "INSERT INTO pfp2product (ratio,product_id,preformulated_products_id,isPrimary) VALUES ('" . $productRATIOS[$i] . "','" . $productIDS[$i] . "',{$pfp_id},{$primary})";
			$this->db->query($sql);

			$primary = 0;
		}
		return $actionLog;
	}
	
	
	public static function isRangeRatio($ratioField) {
		return preg_match("/^\d+\-\d+\%$/", $ratioField);
	}
	
	public static function splitRangeRatio($ratioField) {
		$ratioField = str_replace(' ', '', $ratioField);
		$ratioField = str_replace('%', '', $ratioField);
		return split('-', $ratioField);
	}

}

?>