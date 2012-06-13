<?php

interface iAccessory {

	public function getAllAccessory($companyID);

	public function getAccessoryDetails();

	public function getAccessoryID();

	public function getAccessoryName();

	public function setAccessoryID($ID);

	public function setAccessoryName($name);

	public function setTrashRecord(iTrash $trashRecord);

	public function searchAccessory($accessory, $companyID);

	public function insertAccessory($jobberID);

	public function updateAccessory($jobberID);

	public function deleteAccessory();

	public function save2trash($CRUD, $accessoryID);
}

/**
 * Model for Accessory Domain ( Another name GOM - goods of manufacturer)
 */
class Accessory implements iAccessory {

	/**
	 * XNYO database
	 * @var db
	 */
	protected $db;

	/**
	 * VOC Tracking System record
	 * @var iTrash
	 */
	protected $trashRecord;

	/**
	 * Accessory ID
	 * @var int
	 */
	protected $accessoryID;

	/**
	 * Accessory Name
	 * @var string
	 */
	protected $accessoryName;

	public function __construct(db $db) {
		$this->db = $db;
	}

	// GETTERS
	public function getAccessoryID() {
		return $this->accessoryID;
	}

	public function getAccessoryName() {
		return $this->accessoryName;
	}


	/**
	 * Count total number of GOM. You can count accessories by Jobber
	 * @param mix $jobberID integer or array of jobberID's
	 * @return int - GOM count assigned to jobber (or all)
	 */
	public function queryTotalCount($jobberID = null) {
		$query = "SELECT COUNT(*) cnt FROM " . TB_ACCESSORY;

		if (is_array($jobberID)) {
			$expression = "(" . $this->db->sqltext($jobberID[0]['jobber_id']);
			foreach ($jobberID as $id) {
				$expression .= "," . $this->db->sqltext($id['jobber_id']);
			}
			$expression .= ")";

			$sql = " a.jobber_id IN {$expression} ";
		} else {
			$sql = " a.jobber_id = {$this->db->sqltext($jobberID)} ";
		}

		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}

	/**
	 * Get Accessory List
	 * @param int $jobberID
	 * @param string $sort
	 * @param Pagination $pagination
	 * @return mixed array of accessories or false if list is empty
	 */
	public function getAllAccessory($jobberID = null, $sort = ' ORDER BY a.name ', $pagination = null) {

		$table = '';
		$sqlSelect = '';
		if ($jobberID) {
			$sqlSelect = " j.name as jname ,  v.name as vname, ";
			$table = " jobber j,";
			$sql = "";

			if (is_array($jobberID)) {
				$expression = "(" . $this->db->sqltext($jobberID[0]['jobber_id']);
				foreach ($jobberID as $id) {
					$expression .= "," . $this->db->sqltext($id['jobber_id']);
				}
				$expression .= ")";

				$sql = " a.jobber_id IN {$expression} ";
			} else {
				$sql = " a.jobber_id = {$this->db->sqltext($jobberID)} ";
			}

			$queryWithJobber = " WHERE {$sql} AND j.jobber_id = a.jobber_id ";
		}

		//	TODO: correct join with orders
		$query = "SELECT a.id, a.name, {$sqlSelect} io.order_completed_date, io.order_status FROM
			{$table} " . TB_ACCESSORY . " a
			LEFT JOIN inventory_order io ON a.id = io.order_product_id ";
		$query .= " LEFT JOIN vendor v ON a.vendor_id = v.vendor_id ";
		$query .= $queryWithJobber;
		$query .= " GROUP BY a.id {$this->db->sqltext($sort)}";
		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		$this->db->query($query);

		if ($this->db->num_rows()) {
			return $this->db->fetch_all_array();
		} else {
			return false;
		}
	}


	/**
	 * Get Accessory Details
	 * @return mixed array or null
	 */
	public function getAccessoryDetails() {

		$query = "	SELECT a.*, io.order_completed_date, io.order_status FROM " . TB_ACCESSORY . " a
					LEFT JOIN inventory_order io ON a.id = io.order_product_id
					WHERE a.id=" . $this->db->sqltext($this->accessoryID);
		$this->db->query($query);

		$accessory = $this->db->fetch_array(0);


		return $accessory;
	}


	/**
	 * Get Accessory Details by code
	 * @param string $code
	 * @return type
	 */
	public function getAccessoryDetailsByCode($code) {
		$query = "	SELECT * FROM " . TB_ACCESSORY . " WHERE code LIKE '" . $this->db->sqltext($code) . "'";
		$this->db->query($query);
		$accessory = $this->db->fetch_array(0);
		if (is_array($accessory) && !empty($accessory)) {
			return $accessory;
		} else {
			return array();
		}
	}

	// SETTERS
	public function setAccessoryID($ID) {
		$ID = mysql_real_escape_string($ID);
		$this->accessoryID = $ID;
	}

	public function setAccessoryName($name) {
		$name = mysql_real_escape_string($name);
		$this->accessoryName = $name;
	}

	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;
	}

	/**
	 * Get list of autocomplete options
	 * @param string $occurrence
	 * @param int $jobberID
	 * @return mixed array of options or false on empty list
	 *		option keys are
	 *			"productNR"
	 *			"occurrence"
	 */
	public function accessoryAutocomplete($occurrence, $jobberID = 0) {

		if ($jobberID === 0) {
			$query = "SELECT name, LOCATE('" . $this->db->sqltext($occurrence) . "', name) occurrence " .
					"FROM " . TB_ACCESSORY . " a WHERE LOCATE('" . $this->db->sqltext($occurrence) . "', name)>0 LIMIT " . AUTOCOMPLETE_LIMIT;
		} else {

			$query = "SELECT name, LOCATE('" . $this->db->sqltext($occurrence) . "', name) occurrence " .
					"FROM " . TB_ACCESSORY . " a WHERE ";
			if (is_array($jobberID)) {
				$expression = "(" . $this->db->sqltext($jobberID[0]['jobber_id']);
				foreach ($jobberID as $id) {
					$expression .= "," . $this->db->sqltext($id['jobber_id']);
				}
				$expression .= ")";

				$query .= " a.jobber_id IN  {$expression} ";
			} else {
				$query .= " a.jobber_id = {$this->db->sqltext($jobberID)} ";
			}
			" AND LOCATE('" . $this->db->sqltext($occurrence) . "', name)>0 LIMIT " . AUTOCOMPLETE_LIMIT;
		}

		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$productsData = $this->db->fetch_all();
			for ($i = 0; $i < count($productsData); $i++) {
				if ($productsData[$i]->occurrence) {
					$product = array(
						"productNR" => $productsData[$i]->name,
						"occurrence" => $productsData[$i]->occurrence
					);
					$results[] = $product;
				} elseif ($productsData[$i]->occurrence2) {
					$product = array(
						"productNR" => $productsData[$i]->name,
						"occurrence" => $productsData[$i]->occurrence2
					);
					$results[] = $product;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}


	/**
	 * Search accessory
	 * @param mixed $accessory - string or array of strings for Accessory name
	 * @param int $companyID - search in context of this company
	 * @param Pagination $pagination
	 * @return mixed array of accessories or null on empty list
	 */
	public function searchAccessory($accessory, $companyID = null, $pagination = null) {

		$query = "SELECT * FROM " . TB_ACCESSORY;
		if ($companyID) {
			$query .= " WHERE company_id = " . $this->db->sqltext($companyID) . " AND (";
		} else {
			$query .= " WHERE (";
		}
		if (!is_array($accessory)) {
			$accessory = array($accessory);
		}

		$sqlParts = array();
		foreach ($accessory as $accessory_item) {
			$sqlParts[] = "name LIKE '%" . $this->db->sqltext($accessory_item) . "%'";
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql . ")";

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$searched = $this->db->fetch_all_array();
		}
		return (isset($searched)) ? $searched : null;
	}


	/**
	 * Insert new Accessory into database
	 * @param int $jobberID
	 */
	public function insertAccessory($jobberID) {
		$query = "INSERT INTO " . TB_ACCESSORY . " (name, jobber_id, vendor_id, code)" .
				"VALUES ('" . $this->db->sqltext($this->accessoryName) . "', " . $this->db->sqltext($jobberID) . ", " .
				$this->db->sqltext($this->vendor_id) . ", '" . $this->db->sqltext($this->code) . "')";
		$this->db->query($query);

		$query = "SELECT * FROM " . TB_ACCESSORY . " a WHERE a.name='" . $this->db->sqltext($this->accessoryName) . "'";
		$this->db->query($query);

		$row = $this->db->fetch_array(0);

		//	save to trash_bin
		$this->save2trash('C', $row['id']);
	}


	/**
	 * Update existing accessory
	 * @param int $jobberID
	 */
	public function updateAccessory($jobberID) {
		//	save to trash_bin
		$this->save2trash('U', $this->accessoryID);

		$query = " UPDATE " . TB_ACCESSORY . " " .
				" SET name='" . $this->db->sqltext($this->accessoryName) . "', jobber_id='" .
				$this->db->sqltext($jobberID) . "', vendor_id=" . $this->db->sqltext($this->vendor_id) . ", code = '" .
				$this->db->sqltext($this->code) . "'" .
				" WHERE id=" . $this->db->sqltext($this->accessoryID);

		$this->db->query($query);
	}


	/**
	 * Delete accessory from database
	 */
	public function deleteAccessory() {
		//	save to trash_bin
		$this->save2trash('D', $this->accessoryID);

		$query = "DELETE FROM " . TB_ACCESSORY . " " .
				"WHERE id=" . $this->db->sqltext($this->accessoryID);
		$this->db->query($query);
	}

	/**
	 * Save action to tracking system
	 * @param string $CRUD "C"reate, "U"pdate or "D"elete
	 * @param int $accessoryID
	 */
	public function save2trash($CRUD, $accessoryID) {
		//	protect from SQL injections
		$accessoryID = $this->db->sqltext($accessoryID);

		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_ACCESSORY, $accessoryID, $CRUD, $this->parentTrashRecord);
	}


	/**
	 * Get list of accessory usages
	 * @param int $accessoryID
	 * @param int $departmentID
	 * @return mixed array of AccessoryUsage or false on empty list
	 */
	public function getAccessoryUsages($accessoryID, $departmentID = null) {
		$sql = "SELECT * FROM `accessory_usage` WHERE accessory_id = " . $this->db->sqltext($accessoryID) . " ";
		if ($departmentID) {
			$sql .= " AND department_id = " . $this->db->sqltext($departmentID) . " ";
			$sql .= " ORDER BY date DESC ";
		}
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}

		$usages = array();
		$rows = $this->db->fetch_all();
		foreach ($rows as $row) {
			$accessoryUsage = new AccessoryUsage($this->db);
			$accessoryUsage->id = $row->id;
			$accessoryUsage->accessory_id = $row->accessory_id;
			$accessoryUsage->date = DateTime::createFromFormat('U', $row->date);
			$accessoryUsage->usage = $row->usage;

			$usages[] = $accessoryUsage;
		}

		return $usages;
	}


	/**
	 * Alias of Accessory::queryTotalCount()
	 * @param int $jobberID
	 * @return int count
	 */
	public function getCountGoms($jobberID) {
		return $this->queryTotalCount($jobberID);
	}


	/**
	 * Get List of accessories with prices
	 * @param int $jobberID accessories in context of jobber
	 * @param int $priceID
	 * @param Pagination $pagination
	 * @param Sort $sortStr
	 * @return mixed array of accessories or false on empty list
	 */
	public function getGomPriceList($jobberID, $priceID = null, Pagination $pagination = null, Sort $sortStr = null) {

		$query = "SELECT a.id, a.name as product_nr, pp.* " .
				"FROM price4product pp  , " . TB_ACCESSORY . " a " .
				"WHERE a.jobber_id = {$this->db->sqltext($jobberID)} AND pp.product_id = a.id ";
		if ($priceID) {
			$query .= " AND pp.price_id = " . $this->db->sqltext($priceID) . "";
		}

		$query .= " GROUP BY a.id ";

		if ($sortStr) {
			$query .= $this->db->sqltext($sortStr);
		} else {
			$query .= " ORDER BY a.id ASC ";
		}
		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();
		$productPrice = array();
		foreach ($arr as $b) {

			$productPrice[] = $b;
		}

		return $productPrice;
	}


	/**
	 * Alias of Accessory::getAllAccessory()
	 * @param int $jobberID
	 * @return int count
	 */
	public function getGomList($jobberID) {
		return $this->getAllAccessory($jobberID);
		/*settype($jobberID, "integer");

		$query = "SELECT * " .
				"FROM " . TB_ACCESSORY . " a " .
				"WHERE a.jobber_id = " . (int) $jobberID . " ORDER BY  a.id ASC";

		$this->db->query($query);
		$numRows = $this->db->num_rows();
		if ($numRows) {
			for ($i = 0; $i < $numRows; $i++) {
				$productData = $this->db->fetch($i);
				$product = array(
					'product_id' => $productData->id,
					'name' => $productData->name,
				);
				$products[] = $product;
			}

			return $products;
		} else {

			return false;
		}*/
	}



	/**
	 * Get list of companies who use GOM by ID
	 * @param int $accessoryID
	 * @return boolean
	 */
	public function getCompanyListWhichGOMUse($accessoryID) {

		$query = "SELECT a.id, c.name " .
				"FROM accessory a, company c, facility f,product2inventory pi " .
				"WHERE f.facility_id = pi.facility_id AND f.company_id = c.company_id AND pi.accessory_id = a.id " .
				"AND a.id = " . $this->db->sqltext($accessoryID) . " ";

		$query .= " ORDER BY c.name ASC";
//echo $query;
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$companyList = $this->db->fetch_all_array();
		return $companyList;
	}

	public function getGomSeparateDiscount($facilityID, $jobberID, $accessoryID = null) {

		$query = "	SELECT di . discount_id, di.discount , c.company_id, c.name AS cname, f.name AS fname, f.facility_id, a.*
					FROM  company c, facility f, accessory a
					LEFT JOIN discounts2inventory di ON di.facility_id = {$facilityID} AND di.product_id = a.id AND di.jobber_id = {$jobberID} ";



		$query .= " WHERE f.facility_id = {$facilityID} " .
				" AND f.company_id = c.company_id AND a.jobber_id = {$jobberID} ";
		if ($accessoryID) {
			$query .= " AND a.id = {$accessoryID} ";
		}
		$query .= " GROUP BY a.id ";

//echo $query;

		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();


		$GomData = array();
		foreach ($arr as $b) {

			$GomData[] = $b;
		}

		return $GomData;
	}

	public function getDiscount4Accessory($facilityID, $jobberID, $accessoryID = null) {

		$query = "	SELECT di . discount_id, di.discount , a.id as product_id, c.company_id, c.name, f.name AS fname, f.facility_id, a.name as product_nr
					FROM  company c, facility f, accessory a
					LEFT JOIN discounts2inventory di ON di.facility_id = {$facilityID} AND di.product_id = a.id AND di.jobber_id = {$jobberID} ";



		$query .= " WHERE f.facility_id = {$facilityID} " .
				" AND f.company_id = c.company_id AND a.jobber_id = {$jobberID}";
		if ($accessoryID) {
			$query .= " AND a.id = {$accessoryID} ";
		}


//echo $query;

		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();


		$SupData = array();
		foreach ($arr as $b) {

			$SupData[] = $b;
		}

		return $SupData;
	}

	public function getAccessoryDiscountList4Facility($facilityID, $jobberID, $accessoryID = null) {


		$tables = " " . TB_ACCESSORY . " a,  product2inventory pi "; //m.department_id = d.department_id AND

		$query = "SELECT a.name AS product_nr , di.discount, di.discount_id, pi.accessory_id , pi.in_stock_unit_type";

		$query .= " FROM {$tables} " .
				" LEFT JOIN discounts2inventory di " .
				" ON di.product_id = pi.accessory_id AND di.facility_id = {$facilityID} ";
		if ($jobberID) {
			$query .= " AND di.jobber_id = {$jobberID} ";
		}
		$query .= " WHERE pi.facility_id = {$facilityID} AND a.id = pi.accessory_id ";

		if ($accessoryID) {
			$query .= " AND a.id  = {$accessoryID} ";
		}

//echo $query;
		$this->db->query($query);

		$arr = $this->db->fetch_all_array();

		return $arr;
	}

}

?>