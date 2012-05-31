<?php

class bulkUploader4GOM {

	/**
	 * @var db
	 */
	private $db;
	private $productFrom = 0;
	private $productTo;
	private $GOM;
	private $vendor;
	public $productsError;
	public $productsCorrect;
	public $insertedCnt;
	public $updatedCnt;
	public $validationResult;
	public $actions;

	function bulkUploader4GOM(db $db, $input, validateCSV $validate) {
		$GOM_array = $validate->productsCorrect;
		$this->db = $db;
		
		$pfpArray = $validate->productsCorrect;
		$this->db = $db;

		$this->GOM = new Accessory($this->db);
		$this->vendor = new Vendor($this->db);

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

		foreach ($GOM_array as $item) {
			$GOM_details = $this->GOM->getAccessoryDetailsByCode($item['gom_code']);
			if (empty($GOM_details)) {
				$actionLog .= $this->insertData($item);
				$this->insertedCnt++;
			} else {
				if (!empty($input['update'])) {
					$actionLog .= "	GOM ".$GOM_details['name']." already exists. Update items: YES.\n";
					$actionLog .= $this->updateData($item,$GOM_details);
					$this->updatedCnt++;
				} else {
					$actionLog .= "	GOM ".$GOM_details['name']." already exists. Update items: NO.\n";
				}
			}
		}
		//var_dump($actionLog);

		$actionLog .= "--------------------------------\n";
		$actionLog .= "(" . date("m.d.Y H:i:s") . ") Uploading of " . $input['realFileName'] . " is successfuly finished.\n";
		$actionLog .= "	Number of inserted GOM is " . $this->insertedCnt . "\n";
		$actionLog .= "	Number of updated GOM is " . $this->updatedCnt . "\n";
		$actionLogFile = fopen(DIR_PATH_LOGS . "actions.log", "a");
		fwrite($actionLogFile, $actionLog);
		fclose($actionLogFile);

		$this->actions = str_replace("\n", "<br>", $actionLog);
		$this->actions = str_replace("	", "&nbsp;&nbsp;", $this->actions);
	}

	//--------------private functions-------------------------------------

	private function insertData($data) {
		$cJobber = new Jobber($this->db);
		$jobber_id = $cJobber->getJobberByName(trim($data['jobber']));
		$cVendor = new Vendor($this->db);
		$vendor_details = $cVendor->getVendorDetailsByCode($data['vendor']);
		if ($jobber_id != 0) {
			if (is_array($vendor_details) && !empty($vendor_details)) {
				$vendor_id = $vendor_details['vendor_id'];
			} else {
				$actionLog .= "\t Adding Vendor ".$data['name']."\n";
				$vendor_id = $cVendor->addVendor($data['vendor'], $data['name']);
			}
			$actionLog .= "\t Adding GOM " . $data['description'] . "\n";
			$cAccessory = new Accessory($this->db);
			$cAccessory->setAccessoryName(mysql_real_escape_string($data['description']));
			$cAccessory->vendor_id = $vendor_id;
			$cAccessory->code = mysql_real_escape_string($data['gom_code']);
			$cAccessory->insertAccessory($jobber_id);
			$cUnittype = new Unittype($this->db);
			$unittype = $cUnittype->getUnittypeByName($data['unit']);
			if (is_array($unittype) && !empty($unittype)) {
				$unittype_id = $unittype['unittype_id'];
			} else {
				$unittype_id = $cUnittype->insertOtherUnitType($data['unit'], $data['unit']);
			}
			$accessory = $cAccessory->getAccessoryDetailsByCode($data['gom_code']);
			$query_insert = "INSERT INTO price4accessory (jobber_id, accessory_id, unittype_id, quantity, unit_quantity, price) VALUES ("
						.mysql_real_escape_string(intval($jobber_id)).", "
						.mysql_real_escape_string(intval($accessory['id'])).", "
						.mysql_real_escape_string(intval($unittype_id)).", "
						.mysql_real_escape_string(intval($data['quantity'])).", "
						.mysql_real_escape_string(intval($data['unit_quantity'])).", "
						.mysql_real_escape_string(floatval($data['sales'])).")";
			$this->db->query($query_insert);
		} else {
			$actionLog .= "\t Can not adding GOM because no this jobber in jobber-list \n";
		}
		
		return $actionLog;
	}

	private function updateData($data, $GOM_details) {
		$cJobber = new Jobber($this->db);
		$jobber_id = $cJobber->getJobberByName(trim($data['jobber']));
		$cVendor = new Vendor($this->db);
		$vendor_details = $cVendor->getVendorDetailsByCode($data['vendor']);
		if ($jobber_id != 0) {
			if (is_array($vendor_details) && !empty($vendor_details)) {
				$vendor_id = $vendor_details['vendor_id'];
			} else {
				$actionLog .= "\t Adding Vendor ".$data['name']."\n";
				$vendor_id = $cVendor->addVendor($data['vendor'], $data['name']);
			}
			$actionLog .= "\t Update GOM " . $data['description'] . "\n";
			$cAccessory = new Accessory($this->db);
			$cAccessory->accessory_id = $GOM_details['id'];
			$cAccessory->setAccessoryName(mysql_real_escape_string($data['description']));
			$cAccessory->vendor_id = $vendor_id;
			$cAccessory->code = mysql_real_escape_string($data['gom_code']);
			$cAccessory->updateAccessory($jobber_id);
			$cUnittype = new Unittype($this->db);
			$unittype = $cUnittype->getUnittypeByName($data['unit']);
			if (is_array($unittype) && !empty($unittype)) {
				$unittype_id = $unittype['unittype_id'];
			} else {
				$unittype_id = $cUnittype->insertOtherUnitType($data['unit'], $data['unit']);
			}
			$accessory = $cAccessory->getAccessoryDetailsByCode($data['gom_code']);
			$query_delete = "DELETE FROM price4accessory WHERE accessory_id = ".mysql_real_escape_string($accessory['id']).
							" AND jobber_id = ".mysql_real_escape_string($jobber_id);
			$this->db->query($query_delete);
			$query_insert = "INSERT INTO price4accessory (jobber_id, accessory_id, unittype_id, quantity, unit_quantity, price) VALUES ("
						.mysql_real_escape_string(intval($jobber_id)).", "
						.mysql_real_escape_string(intval($accessory['id'])).", "
						.mysql_real_escape_string(intval($unittype_id)).", "
						.mysql_real_escape_string(intval($data['quantity'])).", "
						.mysql_real_escape_string(intval($data['unit_quantity'])).", "
						.mysql_real_escape_string(floatval($data['sales'])).")";
			$this->db->query($query_insert);
		} else {
			$actionLog .= "\t Can not update GOM because no this jobber in jobber-list \n";
		}
		
		return $actionLog;
	}
}

?>