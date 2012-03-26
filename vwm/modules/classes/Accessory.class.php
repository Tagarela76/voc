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


class Accessory implements iAccessory {
	
	//	hello xnyo
	protected $db;
	
	protected $trashRecord;
	
	protected $accessoryID;
	protected $accessoryName;
		
    function Accessory($db) {    	
    	$this->db = $db;
    }
    
    // GETTERS
    public function getAccessoryID() {
    	return $this->accessoryID;
    }
    
    public function getAccessoryName() {
    	return $this->accessoryName;
    }
    
    public function queryTotalCount($jobberID = null) 
    {
		$query = "SELECT COUNT(*) cnt FROM ".TB_ACCESSORY;
		
		if (is_array($jobberID)){
			$expression = "(".$jobberID[0]['jobber_id'];
			foreach($jobberID as $id){
				$expression .= ",".$id['jobber_id'];
			}
			$expression .= ")";
			
			$sql = " a.jobber_id IN {$expression} ";
		}else{
			$sql = " a.jobber_id = {$jobberID} ";
		}		
		
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
    
    public function getAllAccessory($jobberID = null,$sort=' ORDER BY a.name ', $pagination = null) {
    	//$jobberID=mysql_real_escape_string($jobberID);

		$tabble = '';
		$sqlSelect ='';
		if ($jobberID){
			$sqlSelect = " j.name as jname ,  ";
			$tabble = " jobber j,";
			
			if (is_array($jobberID)){
				$expression = "(".$jobberID[0]['jobber_id'];
				foreach($jobberID as $id){
					$expression .= ",".$id['jobber_id'];
				}
				$expression .= ")";
				
				$sql = " a.jobber_id IN {$expression} ";
			}else{
				$sql = " a.jobber_id = {$jobberID} ";
			}
			
			$queryWithJobber = " WHERE {$sql} AND j.jobber_id = a.jobber_id ";

		}
		
		//	TODO: correct join with orders
    	$query = "SELECT a.id, a.name, {$sqlSelect} io.order_completed_date, io.order_status FROM  
			{$tabble} ".TB_ACCESSORY." a		
			LEFT JOIN inventory_order io ON a.id = io.order_product_id ";
			$query .= $queryWithJobber;
			$query .= " GROUP BY a.id $sort";
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}	
//echo $query;
    	$this->db->query($query);
    	
    	if ($this->db->num_rows()) 
    	{    		
    		return $this->db->fetch_all_array();
    	}
    	else
    		return false;
    }
    
    public function getAccessoryDetails() {
    	
    	$query = "	SELECT a.*, io.order_completed_date, io.order_status FROM ".TB_ACCESSORY." a 
					LEFT JOIN inventory_order io ON a.id = io.order_product_id 			
					WHERE a.id=".(int)$this->accessoryID;
    	$this->db->query($query);
    	 
    	$accessory = $this->db->fetch_array(0);
    	
    	
    	return $accessory;
    }
    
    // SETTERS
    public function setAccessoryID($ID) {
    	$ID=mysql_real_escape_string($ID);
    	$this->accessoryID = $ID;
    }
    
    public function setAccessoryName($name) {
    	$name=mysql_real_escape_string($name);
    	$this->accessoryName = $name;	
    }
    
    //	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection	
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;		
	}
    
    //----
	public function accessoryAutocomplete($occurrence, $jobberID = 0) {

		$occurrence=mysql_escape_string($occurrence);
		settype($jobberID,"integer");

		if ($jobberID === 0){
			$query = "SELECT name, LOCATE('".$occurrence."', name) occurrence " .
				"FROM ".TB_ACCESSORY." a WHERE LOCATE('".$occurrence."', name)>0 LIMIT ".AUTOCOMPLETE_LIMIT;
		} else {
			
			$query = "SELECT name, LOCATE('".$occurrence."', name) occurrence " .
				"FROM ".TB_ACCESSORY." a WHERE ";
			if (is_array($jobberID)){
				$expression = "(".$jobberID[0]['jobber_id'];
				foreach($jobberID as $id){
					$expression .= ",".$id['jobber_id'];
				}
				$expression .= ")";
				
				$query .=	" a.jobber_id IN  {$expression} ";
			}else{
				$query .=	" a.jobber_id = {$jobberID} ";
			}			
				" AND LOCATE('".$occurrence."', name)>0 LIMIT ".AUTOCOMPLETE_LIMIT;
		}

		$this->db->query($query);
//echo $query;
		if ($this->db->num_rows() > 0) {
			$productsData = $this->db->fetch_all();
			for ($i = 0; $i < count($productsData); $i++) {
				if ($productsData[$i]->occurrence) {
					$product = array (
						"productNR"		=>	$productsData[$i]->name,
						"occurrence"	=>	$productsData[$i]->occurrence
					);
					$results[] = $product;

				} elseif ($productsData[$i]->occurrence2) {
					$product = array (
						"productNR"		=>	$productsData[$i]->name,
						"occurrence"	=>	$productsData[$i]->occurrence2
					);
					$results[] = $product;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}
	
    public function searchAccessory($accessory, $companyID = null, $pagination = null) {
    	$companyID=mysql_escape_string($companyID);		
		$query = "SELECT * FROM ".TB_ACCESSORY;
		if ($companyID){
			$query .= " WHERE company_id = ".$companyID." AND (";		
		}else{
			$query .= " WHERE (";
		}
		if (!is_array($accessory)) {
			$accessory = array($accessory);
		}
		
		$sqlParts = array();
		foreach ($accessory as $accessory_item) {
			$accessory_item=mysql_escape_string($accessory_item);
			$sqlParts[] = "name LIKE '%".$accessory_item."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.")";		
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}		
		
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
				
			$searched = $this->db->fetch_all_array();
		}
		return (isset($searched)) ? $searched : null;	
    }
    
    public function insertAccessory($jobberID) {
    	$jobberID=mysql_real_escape_string($jobberID);
    	$query = "INSERT INTO ".TB_ACCESSORY." (name, jobber_id)" .
    			 "VALUES ('".$this->accessoryName."', ".(int)$jobberID.")";
    	$this->db->query($query);
    	
    	$query = "SELECT * FROM ".TB_ACCESSORY." a WHERE a.name='".$this->accessoryName."'";
    	$this->db->query($query);
    	 
    	$row = $this->db->fetch_array(0);
    	
    	//	save to trash_bin
		$this->save2trash('C', $row['id']);
    }
    
    public function updateAccessory($jobberID) {
    	//	save to trash_bin
		$this->save2trash('U', $this->accessoryID);
    	
    	$query = "UPDATE ".TB_ACCESSORY." " .
    			 "SET name='".$this->accessoryName."', jobber_id='".$jobberID."' " .
    			 "WHERE id=".(int)$this->accessoryID;
	
    	$this->db->query($query);
    }
    
    public function deleteAccessory() {
    	//	save to trash_bin
		$this->save2trash('D', $this->accessoryID);
    	
    	$query = "DELETE FROM ".TB_ACCESSORY." " .
    			 "WHERE id=".(int)$this->accessoryID;
		$this->db->query($query);    
    }
    
    //	Tracking System
	public function save2trash($CRUD, $accessoryID) {
		//	protect from SQL injections
		$accessoryID = mysql_real_escape_string($accessoryID);		
		
		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_ACCESSORY, $accessoryID, $CRUD, $this->parentTrashRecord);
		
		//	DEPRECATED July 16, 2010
//		$accessoryID=mysql_real_escape_string($accessoryID);		
//		
//		if (isset($this->trashRecord)) {	
//			$query = "SELECT * FROM ".TB_ACCESSORY." WHERE id = ".(int)$accessoryID;
//			$this->db->query($query);
//			$dataRows = $this->db->fetch_all();
//			
//			foreach ($dataRows as $dataRow) {
//				$accessoryRecords = TrackingSystem::properties2array($dataRow);		
//				$this->trashRecord->setTable(TB_ACCESSORY);		
//				$this->trashRecord->setData(json_encode($accessoryRecords[0]));
//				$this->trashRecord->setUserID($_SESSION['user_id']);
//				$this->trashRecord->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//				$this->trashRecord->setDate(time());	//	current time
//				$this->trashRecord->save();	
//			}			
//
////			//	load and save dependencies
//			if ($CRUD != 'D') {
//				if (false !== ($dependencies = $this->trashRecord->getDependencies(TrackingSystem::HIDDEN_DEPENDENCIES))) {				
//					foreach ($dependencies as $dependency) {
//						$parentID = ($dependency->getParentObj() !== null) ? $dependency->getParentObj()->getID() : null;
//						$dependency->setUserID($_SESSION['user_id']);
//						$dependency->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//						$dependency->setDate(time());	//	current time					
//						$dependency->setReferrer($parentID);
//						$dependency->save();												
//					}
//				}		
//			}			
//		}		
	}
	
	public function getAccessoryUsages($accessoryID, $departmentID = null) {
		$sql = "SELECT * FROM `accessory_usage` WHERE accessory_id = ".mysql_escape_string($accessoryID)." ";
		if ($departmentID){
			$sql .= " AND department_id = " . (int) $departmentID . " ";
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

	public function getCountGoms($jobberID) {
	
		$query = "SELECT COUNT(*) cnt FROM accessory a WHERE a.jobber_id = {$jobberID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
	public function getGomPriceList($jobberID, $priceID = null, Pagination $pagination = null, Sort $sortStr = null) {

		$query =	"SELECT a.id, a.name as product_nr, pp.* " .
					"FROM price4product pp  , ". TB_ACCESSORY . " a ".
					"WHERE a.jobber_id = {$jobberID} AND pp.product_id = a.id ";
		if ($priceID){
			$query .= " AND pp.price_id = " . (int) $priceID . "";
		}
	
			$query .= " GROUP BY a.id ";
	
		if ($sortStr){
			$query .= $sortStr;
		}else{
			$query .= " ORDER BY a.id ASC ";
		}		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		$this->db->query($query);
//echo $query;
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();
		$productPrice = array();
			foreach($arr as $b) {

					$productPrice[] = $b;
            
			}		

		return $productPrice;
	}
	
	public function getGomList($jobberID) {
		settype($jobberID,"integer");

		$query = "SELECT * " .
				 "FROM ".TB_ACCESSORY." a " .
				 "WHERE a.jobber_id = ".(int)$jobberID. " ORDER BY  a.id ASC"; 
	
		$this->db->query($query);
		$numRows = $this->db->num_rows();
		if ($numRows) {
			for ($i=0; $i < $numRows; $i++) {
				$productData = $this->db->fetch($i);
				$product = array (
					'product_id'				=>	$productData->id,
					'name'						=>	$productData->name,
				);
				$products[] = $product;
			}

			return $products;
		} else {

			return false;
		}	
	
	}

	public function getCompanyListWhichGOMUse($accessoryID) {

		$query =	"SELECT a.id, c.name " .
					"FROM accessory a, company c, facility f,product2inventory pi ".
					"WHERE f.facility_id = pi.facility_id AND f.company_id = c.company_id AND pi.accessory_id = a.id " .
					"AND a.id = " . (int) $accessoryID . " ";

			$query .= " ORDER BY c.name ASC";
//echo $query;
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$companyList = $this->db->fetch_all_array();
		return $companyList;
	}		
	
	public function getGomSeparateDiscount($facilityID, $jobberID, $accessoryID = null ) {
		
		$query = "	SELECT di . discount_id, di.discount , c.company_id, c.name AS cname, f.name AS fname, f.facility_id, a.*
					FROM  company c, facility f, accessory a
					LEFT JOIN discounts2inventory di ON di.facility_id = {$facilityID} AND di.product_id = a.id AND di.jobber_id = {$jobberID} ";



		$query .=	" WHERE f.facility_id = {$facilityID} ".
					" AND f.company_id = c.company_id AND a.jobber_id = {$jobberID} ";
		if ($accessoryID){
			$query .=	" AND a.id = {$accessoryID} ";
		}					
		$query .=	" GROUP BY a.id ";

//echo $query;

		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}			
		$arr = $this->db->fetch_all_array();
		
		
		$GomData = array();
			foreach($arr as $b) {

					$GomData[] = $b;
            
			}
	
		return $GomData;
	}
	
	public function getDiscount4Accessory($facilityID, $jobberID, $accessoryID = null ) {

		$query = "	SELECT di . discount_id, di.discount , a.id as product_id, c.company_id, c.name, f.name AS fname, f.facility_id, a.name as product_nr
					FROM  company c, facility f, accessory a
					LEFT JOIN discounts2inventory di ON di.facility_id = {$facilityID} AND di.product_id = a.id AND di.jobber_id = {$jobberID} ";



		$query .=	" WHERE f.facility_id = {$facilityID} ".
					" AND f.company_id = c.company_id AND a.jobber_id = {$jobberID}";
		if ($accessoryID){
			$query .=	" AND a.id = {$accessoryID} ";
		}					


//echo $query;

		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}			
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
	}	
	
	public function getAccessoryDiscountList4Facility($facilityID, $jobberID, $accessoryID = null ) {
	

			$tables = " ".TB_ACCESSORY." a,  product2inventory pi "; //m.department_id = d.department_id AND 

			$query	=    "SELECT a.name AS product_nr , di.discount, di.discount_id, pi.accessory_id , pi.in_stock_unit_type";

			$query .=	" FROM {$tables} " .
						" LEFT JOIN discounts2inventory di ".
						" ON di.product_id = pi.accessory_id AND di.facility_id = {$facilityID} ";
						if ($jobberID){
							$query .= " AND di.jobber_id = {$jobberID} ";
						}		
			$query .=	" WHERE pi.facility_id = {$facilityID} AND a.id = pi.accessory_id " ;
					
			if ($accessoryID){
				$query .=   " AND a.id  = {$accessoryID} ";
			}

//echo $query;
			$this->db->query($query);

			$arr = $this->db->fetch_all_array();	
	
		return $arr;
	}	

	
}
?>