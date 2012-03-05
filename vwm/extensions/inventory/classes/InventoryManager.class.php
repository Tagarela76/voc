<?php
class InventoryManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getProductUsageGetAll(DateTime $beginDate, DateTime $endDate, $category, $categoryID, $productID = null, $sortStr = null) {

        $categoryDependedSql = "";
		$tables = TB_USAGE." m, ".TB_MIXGROUP." mg";
		switch ($category) {
			case "company":
				$tables .= ", ".TB_DEPARTMENT." d, ".TB_FACILITY." f ";
				$categoryDependedSql = " m.department_id = d.department_id "
                                                        ." AND d.facility_id = f.facility_id "
                                                        ." AND f.company_id = {$categoryID} ";
				break;
			case "facility":
				$tables .= ", ".TB_DEPARTMENT." d ";
				$categoryDependedSql = " m.department_id = d.department_id AND d.facility_id = {$categoryID} ";
				break;
			case "department":				
				$categoryDependedSql = " m.department_id = {$categoryID} ";
				break;
			default :
				throw new Exception('Unknown category for DailyEmissions');
				break;
		}

			$tables .= ", ".TB_PRODUCT." p";
		
		
		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name,p.product_id, pi.* ";
				
		$query .= " FROM {$tables} " .
				  " LEFT JOIN product2inventory pi ON p.product_id = pi.product_id AND pi.facility_id = {$categoryID} " .
				  " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN ".$beginDate->getTimestamp()." AND ".$endDate->getTimestamp()." ";

		if ($productID){
			$query .= "AND p.product_id = {$productID} ";
		}
		$query .= " GROUP BY mg.product_id ";
		if ($sortStr){
			$query .= $sortStr;
		}else{
			$query .= " ORDER BY p.product_id ";
		}
/*			
		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name,p.product_id ";
		if (!$productID){
			$query .= " ,pi.* ";
			
		}				
		$query .= " FROM {$tables} " ;
		if (!$productID){
			$query .= " LEFT JOIN product2inventory pi ON p.product_id = pi.product_id AND pi.facility_id = {$categoryID} ";
			
		}				  
		$query .= " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."'";

		if ($productID){
			$query .= "AND p.product_id = {$productID} ";
			$inventoryDetails = $this->checkInventory( $productID, $categoryID );
		}
		$query .= " GROUP BY mg.product_id " .
				  " ORDER BY p.product_id ";			
*/		

		//echo $query;
		$this->db->query($query);

		
		$arr = $this->db->fetch_all_array();

		// If no usage for the period of the last delivery
		if (!$arr){
			if ($productID){
				$inventoryDetails = $this->checkInventory( $productID, $categoryID );
				$arr[] = $inventoryDetails;
				//var_dump($arr);
			}			
		}
		
		$productUsageData = array();
			foreach($arr as $b) {
				$productinv = new ProductInventory($this->db, $b);
				$productUsageData[] = $productinv;                        
			}

		return $productUsageData;
	}
	
	public function getProductsSupplierList($facilityID, $productID = null ,$jobberID, $sortStr = null) {

			$tables = " ".TB_PRODUCT." p, " . TB_SUPPLIER . " s ,  product2inventory pi "; //m.department_id = d.department_id AND 
			
			


			$query	=    "SELECT p.supplier_id, p.product_nr , s.original_id , di.discount, di.discount_id, s.supplier, pi.product_id , pi.in_stock_unit_type";

			$query .=	" FROM {$tables} " .
						" LEFT JOIN discounts2inventory di ".
						" ON di.product_id = pi.product_id AND di.facility_id = {$facilityID} AND di.jobber_id = {$jobberID} ".
						" WHERE p.supplier_id  = s.supplier_id AND pi.facility_id = {$facilityID} " ;
			if ($productID){
				$query .=   " AND p.product_id  = {$productID} ";
			}
				$query .=   " AND p.product_id = pi.product_id ";
			
			if ($sortStr){
				$query .=   " {$sortStr} ";
			}
			
			$this->db->query($query);
//echo $query;
			$arr = $this->db->fetch_all_array();

			$SupData = array();
				foreach($arr as $b) { 
					if ( $b['supplier_id'] <> $b['original_id'] ){
						$query = "SELECT supplier FROM " . TB_SUPPLIER . " WHERE original_id=supplier_id AND original_id=" .$b['original_id']. " ";
						
						$this->db->query($query);
						$suppliername = $this->db->fetch_all_array();
						$b['supplier'] = $suppliername[0]['supplier'];
						$SupData[] = $b;
					}else{
						$SupData[] = $b;
					}

				}

			return $SupData;		
		
/*		
		}else{		
			$query	=	"SELECT DISTINCT di.*, s.supplier ";

			$query .=	" FROM discounts2inventory di, " . TB_SUPPLIER . " s   " .
						" WHERE di.facility_id =  {$facilityID} AND di.supplier_id = s.original_id AND s.supplier_id = s.original_id";	
			$this->db->query($query);
echo $query;
			$SupData = $this->db->fetch_all_array();
			return $SupData;	
*/				


	

	}	
	
	public function getSupplierOrders($facilityID = null, $productID = null, $jobberID, Pagination $pagination = null, Sort $sortStr = null) {
        $time = new DateTime('first day of this month');

        $query = "SELECT io.* ";
				
		$query .=	" FROM inventory_order io" .

					" WHERE ";
		 
		if ($facilityID != null){
			$query .=	" io.order_facility_id = {$facilityID} AND ";
		}
		
		if ($productID != null){
			if (is_array($productID)){
				$expression = "(".$productID[0]['product_id'];
				foreach($productID as $id){
					$expression .= ",".$id['product_id'];
				}
				$expression .= ")";
				
				$query .=	" io.order_product_id IN  {$expression} AND ";
			}else{
				$query .=	" io.order_product_id = {$productID} AND ";
			}
			
		}
		$query .=	" io.order_jobber_id = {$jobberID} ";
		/*else{
			$query .=	" AND io.order_created_date >= {$time->getTimestamp()} ";
		}*/
		
		if ($sortStr != null) {
			//, Pagination $pagination = null
			$query .= $sortStr;
		}else{
			$query .=" ORDER BY io.order_completed_date DESC ";
		}	
		
		if (isset($pagination)) {
			//, Pagination $pagination = null
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
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
	
	public function getCountFacilityOrders($facilityID) {
	
		$query = "SELECT COUNT(*) cnt FROM inventory_order WHERE order_facility_id = {$facilityID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}	
	
	public function getCountSupplierOrders($products,$jobberID) {
		if ($products && is_array($products)){
				$expression = "(".$products[0]['product_id'];
				foreach($products as $id){
					$expression .= ",".$id['product_id'];
				}
				$expression .= ")";

		}
		
		$query = "SELECT COUNT(*) cnt FROM inventory_order io WHERE order_jobber_id = {$jobberID} ";
		$query .=	" io.order_product_id IN  {$expression} ";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}	
	
	public function getSupplierOrdersStatusList() {
        $query = "SELECT * FROM inventory_order_status ";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		//echo $query;
		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
	}	
	
	public function getSupplierOrderDetails($orderID) {
        $time = new DateTime('first day of this month');

        $query = "SELECT io.*";
				
		$query .=	" FROM inventory_order io " .

					" WHERE io.order_id = {$orderID} ";
		//$query .=	" AND io.order_created_date >= {$time->getTimestamp()}";

		$this->db->query($query);
	//	echo $query; 
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
	
	public function getLastOrderId() {
        $query = "SELECT io.order_id ";
				
		$query .=	" FROM inventory_order io " .

					" ORDER BY io.order_created_date DESC LIMIT 1";

		$this->db->query($query);
	
		$arr = $this->db->fetch_all_array();
		
		//echo $query;
		$data = array();
			foreach($arr as $b) {

					$data = $b;
            
			}
	
		return $data;
	}	
	
	public function updateSupplierOrder( $data ) {

        $query = "UPDATE inventory_order SET order_status = {$data['status']} ";
		if (isset($data['order_completed_date'])){		
			$query .= " , order_completed_date = {$data['order_completed_date']} ";
		}
		$query .= " WHERE order_id = {$data['order_id']} ";			

		$this->db->query($query);

		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function getSupplierSeparateDiscount($facilityID, $supplierID, $productID = null, $jobberID ) {
/*            
        $query = "SELECT di.*, p.product_nr, c.company_id, c.name, f.name as fname   ";
				
		$query .=	" FROM product p,  " . TB_FACILITY . " f , " . TB_COMPANY . " c " .
					" LEFT JOIN discounts2inventory di ".
					" ON di.supplier_id =  {$supplierID} AND di.facility_id = {$facilityID} ".
					" WHERE p.product_id = di.product_id ";
		if ($productID){
			$query .=	" AND di.product_id = {$productID} ";
		}else{
			$query .=	" AND di.product_id IS NOT NULL ";
		}			
		
		$query .=	" AND f.facility_id = {$facilityID} AND f.company_id = c.company_id ";
*/
		$query = "SELECT di . discount_id, di.discount , p.product_nr, p.product_id , c.company_id, c.name, f.name AS fname, f.facility_id, s.original_id as supplier_id
		FROM  company c, supplier s, facility f, product p
		LEFT JOIN discounts2inventory di ON di.facility_id = {$facilityID} AND di.supplier_id = {$supplierID} AND di.product_id = p.product_id AND di.jobber_id = {$jobberID} ";



		$query .=	" WHERE p.supplier_id = s.supplier_id".
					" AND s.original_id = {$supplierID} ".
					" AND f.facility_id = {$facilityID} ".
					" AND f.company_id = c.company_id ";
				if ($productID){
					$query .=	" AND p.product_id = {$productID} ";

				}					
		$query .=	" GROUP BY p.product_id ";

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
	
	public function getSupplierWholeDiscount($supplierID, $facilityID = null,$jobberID, Pagination $pagination = null,Sort $sortStr = null) {
            
        $query =	"SELECT di.discount_id ,di.discount,di.product_id, s.original_id as supplier_id, c.company_id, c.name,f.facility_id, f.name AS fname ";
				
		$query .=	" FROM mix m , mixgroup mg , department d , company c , product p , supplier s,facility f ";
					
		$query .=	" LEFT JOIN discounts2inventory di ON di.facility_id = f.facility_id AND di.supplier_id = {$supplierID} AND di.jobber_id = {$jobberID} ".
					" AND di.product_id IS NULL ";
		if ($facilityID != null){
			$query .= " AND di.facility_id = {$facilityID} ";
		}		
		$query .=	" WHERE f.company_id = c.company_id ".
					" AND m.department_id = d.department_id ".
					" AND p.product_id = mg.product_id ".
					" AND m.mix_id = mg.mix_id ".
					" AND m.department_id = d.department_id ".
					" AND d.facility_id = f.facility_id ".
					" AND s.supplier_id = p.supplier_id ".
					" AND s.original_id = {$supplierID} ";
		if ($facilityID != null){
			$query .= " AND f.facility_id = {$facilityID} ";
		}	
	
		$query .=	" GROUP BY c.name ";
		if ($sortStr){
			$query .= $sortStr;
		}else{
			$query .= " ORDER BY c.company_id ASC ";
		}		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
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
	
	public function getCountSupplierDiscounts($supplierID,$jobberID) {
	
		$query = "SELECT COUNT(*) cnt FROM discounts2inventory WHERE product_id IS NULL AND supplier_id = {$supplierID} AND jobber_id = {$jobberID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}		
	
	public function getDiscountsByID($discountID) {
            
        $query = "SELECT di.*, c.name, f.name as fname, p.product_nr ";
				
		$query .= " FROM discounts2inventory di,  " . TB_FACILITY . " f , " . TB_COMPANY . " c , product p".
				  " WHERE di.discount_id = {$discountID} AND p.product_id = di.product_id ";

		
		$query .= " AND f.facility_id = di.facility_id AND f.company_id = c.company_id ";
		//echo $query;
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$SupData = $this->db->fetch_all_array();

		return $SupData;
	}	
	
	public function updateSupplierDiscounts( $form ) {

								
		if ($form['discount_id'] == null){
			if ($form['product_id'] != ''){
				$query = "INSERT INTO discounts2inventory VALUES (NULL,". $form['companyID'] .",". $form['facilityID'] .",".$form['jobberID'].",". $form['supplier_id'] .",". $form['product_id'] .",". mysql_real_escape_string($form['discount']) .") ";
			}else{
				$query = "INSERT INTO discounts2inventory VALUES (NULL,". $form['companyID'] .",". $form['facilityID'] .",".$form['jobberID'].",". $form['supplier_id'] .",NULL,". mysql_real_escape_string($form['discount']) .") ";
			}
			
		}else{
            $query = "UPDATE discounts2inventory SET discount = ".mysql_real_escape_string($form['discount'])." WHERE discount_id = {$form['discount_id']}";			

		}

		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function getSupplierSettings($facilityID) {
            
        $query = "SELECT * FROM email2inventory WHERE facility_id = {$facilityID} ";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}
	
	public function updateSupplierSettings( $form ) {

										
		if ($form['email_id'] == null){
			$query = "INSERT INTO email2inventory VALUES (NULL,". $form['facilityID'] .",'". $form['email_all'] ."','". $form['email_manager'] ."') ";
				

		}else{
            $query = "UPDATE email2inventory SET 
			email_all = '".  mysql_real_escape_string($form['email_all'])."',
			email_manager = '".mysql_real_escape_string($form['email_manager'])."'
			WHERE email_id = {$form['email_id']} ";			

		}

		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	
	public function updateSupplierEmails( $form ) {

		$query = "INSERT INTO email2supplier VALUES (NULL,". $form['supplier_id'] .",'". mysql_escape_string($form['email']) ."') ";
		$this->db->query($query);

		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	public function getManagerList($companyID) {
		$query = "SELECT * FROM email2manager WHERE company_id = {$companyID} ";
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
	public function getManagerEmail($userID) {
		$query = "SELECT email FROM user WHERE user_id = {$userID} ";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_array(0);

		return $arr;
	}	
	
	public function updateManagerEmails( $form ) {
		$query = "DELETE FROM email2manager WHERE company_id = {$form['companyID']}  ";
		$this->db->query($query);
		if (isset($form['cuser'])){
			foreach($form['cuser'] as $id){
				$query = "INSERT INTO email2manager VALUES (NULL,".  mysql_escape_string($id) .",'". mysql_escape_string($form['companyID']) ."') ";
				$this->db->query($query);
			}
		}
		if (isset($form['fuser'])){
			foreach($form['fuser'] as $id){
				$query = "INSERT INTO email2manager VALUES (NULL,".  mysql_escape_string($id) .",'". mysql_escape_string($form['companyID']) ."') ";
				$this->db->query($query);
			}
		}
		if (isset($form['duser'])){
			foreach($form['duser'] as $id){
				$query = "INSERT INTO email2manager VALUES (NULL,".  mysql_escape_string($id) .",'". mysql_escape_string($form['companyID']) ."') ";
				$this->db->query($query);
			}
		}		

		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function beforeUpdateSupplierEmails( $form ) {

		$query = "DELETE FROM email2supplier WHERE supplier_id = {$form['supplier_id']}  ";
		$this->db->query($query);	
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function getSupplierUsersEmails( $supplierID ) {

		$query = "SELECT * FROM email2supplier WHERE supplier_id = {$supplierID} ";
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
		
	public function getInventoryByID($inventoryID) {
            
        $query = "SELECT * FROM product2inventory WHERE inventory_id = {$inventoryID} ";

		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}
	
	public function getInventoryProductIdByFacility($facilityID, Pagination $pagination = null) {
            
        $query = "SELECT product_id FROM product2inventory WHERE facility_id = {$facilityID} ";
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_all_array();

		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b['product_id'];
            
			}
	
		return $SupData;
	}	
	
	public function getCountInventoryProduct($facilityID) {
	
		$query = "SELECT COUNT(*) cnt FROM product2inventory WHERE facility_id = {$facilityID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}	

	public function checkInventory( $productID, $facilityID ) {
	
		$query =	"SELECT pi.* , p.product_nr  FROM product2inventory pi , ".TB_PRODUCT." p WHERE pi.product_id = ".$productID." AND pi.facility_id = ".$facilityID." AND p.product_id = {$productID} ";				
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$data = array();
			foreach($arr as $b) {
					$data = $b;
			}
		return $data;

	}	
	
	public function checkDiscountID( $companyID, $facilityID, $supplierID, $jobberID ) {
	
		$query =	"SELECT di.discount_id FROM discounts2inventory di WHERE di.company_id = ".$companyID." AND di.jobber_id = ".$jobberID." AND di.facility_id = ".$facilityID." AND di.supplier_id = ".$supplierID." AND di.product_id IS NULL ";				
		
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}else{
			$arr = $this->db->fetch_all_array();
			return $arr;
		}


	}		
	
	
	public function getInventoryInfoForProduct( $productID, $facilityID, $mixID = null) {
							//ORDERS FOR THIS PODUCT
								$orderList = $this->getSupplierOrders($facilityID, $productID);		

								if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED){

									//$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
									$dateBegin = $orderList[0]['order_completed_date'];
								}else{
									$dateBegin = new DateTime('first day of this month');
									$dateBegin = $dateBegin->getTimestamp();
								}
							//
								$endDate = new DateTime();
						
				
	$query = "SELECT sum(mg.quantity_lbs) as sum, mg.quantity_lbs, p.product_nr, pi.* 
				FROM mix m, mixgroup mg, department d , product p 
				LEFT JOIN product2inventory pi ON p.product_id = pi.product_id 
				WHERE m.department_id = d.department_id
				AND p.product_id = ".$productID."
				AND pi.facility_id = ".$facilityID."
				AND d.facility_id = ".$facilityID."
				AND p.product_id = mg.product_id 
				AND m.creation_time BETWEEN ".$dateBegin." AND ".$endDate->getTimestamp()."
				AND m.mix_id = mg.mix_id";	
	//$query .= " AND m.mix_id = ".$mixID.""; 

		//echo $query;
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		
		$arr = $this->db->fetch_all_array();
		$inventory = $arr[0];
		$inventory['product_id'] = ($inventory['product_id'] == null) ? $productID : $inventory['product_id'];
		return $inventory;
	}	
	
	
	/**
	 * Check for active orders by product
	 * @param int $product_id
	 * @param int $facility_id
	 * @return boolean false if no active orders, true if this product has order with any not completed status
	 */
	public function isThereActiveOrdersByProductID($product_id, $facility_id){
				
        $query = "SELECT * 
				FROM inventory_order 
				WHERE order_product_id = {$product_id} 
				AND order_facility_id = {$facility_id}
				AND order_status NOT IN (".OrderInventory::COMPLETED.", ".OrderInventory::CANCELED.")";
		$this->db->query($query);
		
		if ($this->db->num_rows() == 0) {
			return false;
		} else {
			return true;
		}				
	}
	
	public function getSaleUserJobberID($userID) {
	
		$query = "SELECT jobber_id FROM users2jobber WHERE user_id = {$userID}";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$id = $this->db->fetch_array(0);

		return $id;
	}
	
	public function getSuppliersByJobberID($jobberID) {
	
		$query = "SELECT supplier_id FROM supplier2jobber WHERE jobber_id = {$jobberID}";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_all_array();

		return $arr;
	}
	
	public function getJobberDetails($jobberID) {
	
		$query = "SELECT * FROM jobber WHERE jobber_id = {$jobberID}";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_array(0);

		return $arr;
	}	
	
	public function getJobberList() {
	
		$query = "SELECT * FROM jobber";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_all_array();

		return $arr;
	}	
	
	
	public function getInitialInStockValues($prodictID) {
	
		$query = "SELECT product_instock,product_limit, product_amount,product_stocktype FROM product WHERE product_id = {$prodictID}";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$data = $this->db->fetch_array(0);

		return $data;
	}	
	public function runInventoryOrderingSystem( $mix ) {
		$productObjArray = $mix->products;
		//$text = $this->getEmailText($mix->facility_id);
				
		foreach ($productObjArray as $productObj){

			$inventory = $this->getInventoryInfoForProduct($productObj->product_id, $mix->facility_id, $mix->mix_id);

			if (!$inventory) {
				throw new Exception("No inventory found :(");
			}
			
			$initialInstock = $this->getInitialInStockValues($productObj->product_id);			
			$inventory['facility_id'] = $mix->facility_id;
			if ($initialInstock['product_stocktype'] && $initialInstock['product_stocktype']!= 0){
				$inventory['in_stock_unit_type'] = $initialInstock['product_stocktype'];
				$inventory['in_stock'] = $initialInstock['product_instock'];
				$inventory['amount'] = $initialInstock['product_amount'];
				$inventory['inventory_limit'] = $initialInstock['product_limit'];
			}else{
				$inventory['in_stock_unit_type'] = $productObj->unittypeDetails['unittype_id'];
			}
			
			$productUsageData = new ProductInventory($this->db, $inventory);
			
			// CONVERT PRODUCT UsAGE VAL TO IN STOCK UNITTYPE
			$inStock2Type = $this->unitTypeConverter($productUsageData);
			if 	($inStock2Type){
				$inventory['sum'] = $inStock2Type['usage'];
			}

			if ($productUsageData->id == null){
				
				$productUsageData->save();

			}
			
			if ($productUsageData->in_stock - $inventory['sum']  <= $productUsageData->limit){
				//$productUsageData->in_stock - $productObj->quantity  <= $productUsageData->limit
				$isThereActiveOrders = $this->isThereActiveOrdersByProductID($productUsageData->product_id, $mix->facility_id);

				if (!$isThereActiveOrders){
					//Create new Order
					$newOrder = new OrderInventory($this->db);
							

					// PRICE FOR PRODUCT
					$priceManager = new Product($this->db);
					$price = $priceManager->getProductPrice($productUsageData->product_id);
					$priceObj = new ProductPrice($this->db, $price[0]);

					$newOrder->order_price = $price[0]['price'];
					
					// Discount if isset for separate product, else for whole facility 
					$discount = 0;
					$result = $this->getSupplierSeparateDiscount($mix->facility_id, $price[0]['supman_id'], $productUsageData->product_id);
					if (!$result){
						$result2 = $this->getSupplierWholeDiscount($price[0]['supman_id'],$mix->facility_id);
						$discount = $result2[0]['discount'];
					}else{
						$discount = $result[0]['discount'];
					}
					//UNITTYPE CONVERT ORDER AMOUNT TYPE TO	PRICE TYPE
					$amount2Type = $this->unitTypeConverter($productUsageData, $priceObj);
					
					$newOrder->order_product_id = $productUsageData->product_id;
					$newOrder->order_facility_id = $mix->facility_id;
					$newOrder->order_name = 'Order for product "'.$productUsageData->product_nr.'"';
					$newOrder->order_discount = $discount;
					$newOrder->order_unittype = $priceObj->unittype;
					$newOrder->order_total = $amount2Type['amount'] * $newOrder->order_price - ( ($amount2Type['amount'] * $newOrder->order_price)*$newOrder->order_discount/100 );
					$newOrder->order_amount = $productUsageData->amount;
					
				    $newOrder->save();

					// EMAIL NOTIFICATION
					$supplierDetails = $this->getSupplierEmail($priceObj->supman_id);
					$supplierUsersEmais = $this->getSupplierUsersEmails($priceObj->supman_id);

					$ifEmail = $this->checkSupplierEmail($supplierDetails['email']);
						
					$facilityManager = new Facility($this->db);
					$facilityDetails = $facilityManager->getFacilityDetails($newOrder->order_facility_id);				
				
					if ($ifEmail){
						$text['msg'] = "New order ". $newOrder->order_name ." from Facility ".$facilityDetails['title'];
						$text['title'] = "New order ". $newOrder->order_name ." from Facility ";
						$isNewOrder = true;
						$this->sendEmailToSupplier($supplierDetails['email'],$text,$isNewOrder );
						if ($supplierUsersEmais){
							foreach($supplierUsersEmais as $userEmail){
								$this->sendEmailToSupplier($userEmail['email'],$text,$isNewOrder );
							}
						}						
					}
						$supplierManager = new Supplier($this->db);
						$supDetails = $supplierManager->getSupplierDetails($priceObj->supman_id);	

						$userDetails = $this->getManagerList($facilityDetails['company_id']);	
						
						if ($userDetails){
							$text['msg'] = "New order ". $newOrder->order_name ." to Supplier ";
							$text['msg'] .= "<br>" ." Supplier: ".$supDetails['supplier_desc']; 
							$text['msg'] .= "<br>" ." Contact: ".$supDetails['contact']; 
							$text['msg'] .= "<br>" ." Address: ".$supDetails['address']; 
							$text['msg'] .= "<br>" ." Phone: ".$supDetails['phone']; 
							$text['title'] = "New order ". $newOrder->order_name ." to Supplier ";
							foreach($userDetails as $user){
								$email = $this->getManagerEmail($user['user_id']);
								$this->sendEmailToManager($email,$text);
							}
						}
						
				}else{
					// remind for needing product and completed order
					
				}
				
			}		
		}
		
	}

	public function unitTypeConverter(ProductInventory $inventory, ProductPrice $price = null) {
		// UNITTYPE CONVERTER

		$unittype = new Unittype($this->db);
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($inventory->product_id);
		$densityObj = new Density($this->db, $productDetails['densityUnitID']);

		//	check density
		if (empty($productDetails['density']) || $productDetails['density'] == '0.00') {
			$productDetails['density'] = false;
			$isThereProductWithoutDensity = true;
		}

		// get Density Type
		$densityType = array(
			'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
		);


		$defaultType = $unittype->getUnittypeClass($inventory->in_stock_unit_type);
		$unittypeDetails = $unittype->getUnittypeDetails($inventory->in_stock_unit_type);
		
		$unitTypeConverter = new UnitTypeConverter($defaultType);
		$quantitiWeightSum = $unitTypeConverter->convertFromTo($inventory->usage, "lb", $unittypeDetails['description'], $productDetails['density'], $densityType); //	in weight

		if ($price){
			$defaultType = $unittype->getUnittypeClass($inventory->in_stock_unit_type);
			$unitTypeConverter = new UnitTypeConverter($defaultType);
			$typeName = $unittype->getUnittypeDetails($price->unittype);			
			$quantitiVolumeSum = $unitTypeConverter->convertFromTo($inventory->amount,$unittypeDetails['description'], $typeName['description'], $productDetails['density'], $densityType);//	in volume ;
		}


		if ($quantitiWeightSum != null && $quantitiWeightSum != 0 && $quantitiWeightSum != ''){
			$data = array();
			$data['usage'] = number_format($quantitiWeightSum, 2, '.', '');
			$data['unittype'] = $unittypeDetails['name'];
			if ($quantitiVolumeSum != null && $quantitiVolumeSum != 0 && $quantitiVolumeSum != ''){
				$data['amount'] = number_format($quantitiVolumeSum, 2, '.', '');
				$data['amountType'] = $typeName['name'];
			}
			return  $data;
		}else{
			return false;
		}
		
		//var_dump($inventory->product_nr,$quantitiWeightSum,$unittypeDetails['description'],$inventory->usage);

	}
	
	public function unitTypeConverterForStock(ProductInventory $inventory, ProductInventory $inStock = null) {
		// UNITTYPE CONVERTER
		$unittype = new Unittype($this->db);
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($inventory->product_id);
		$densityObj = new Density($this->db, $productDetails['densityUnitID']);

		//	check density
		if (empty($productDetails['density']) || $productDetails['density'] == '0.00') {
			$productDetails['density'] = false;
			$isThereProductWithoutDensity = true;
		}

		// get Density Type
		$densityType = array(
			'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
		);


		$defaultType = $unittype->getUnittypeClass($inventory->in_stock_unit_type);
		$unittypeDetails = $unittype->getUnittypeDetails($inventory->in_stock_unit_type);
		
		$unitTypeConverter = new UnitTypeConverter($defaultType);
		//$quantitiWeightSum = $unitTypeConverter->convertFromTo($inventory->usage, "lb", $unittypeDetails['description'], $productDetails['density'], $densityType); //	in weight

		if ($inStock){
	
			$inStockDetails = $unittype->getUnittypeDetails($inStock->in_stock_unit_type);
			
			$inStockValue = $unitTypeConverter->convertFromTo($inventory->in_stock, $inStockDetails['description'], $unittypeDetails['description'], $productDetails['density'], $densityType);
		
			
		}
	
		if ($inStockValue != null && $inStockValue != 0 && $inStockValue != ''){

				$data['in_stock'] = number_format($inStockValue, 2, '.', '');
	
			return  $data;
		}else{
			return false;
		}
		
		//var_dump($inventory->product_nr,$quantitiWeightSum,$unittypeDetails['description'],$inventory->usage);

	}	

	public function getSupplierEmail($supplierID){
		$query = "SELECT u.email FROM user u , users2jobber us WHERE us.supplier_id = {$supplierID} AND us.user_id = u.user_id ";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_all_array();
		$email = array();
			foreach($arr as $b) {
					$email = $b;
			}
		return $email;
	}
	
	public function getClientEmail($facilityID){
		$query = "SELECT f.email FROM facility f WHERE f.facility_id = {$facilityID} ";

		$this->db->query($query);
		//echo 	$query;
		if ($this->db->num_rows() == 0) {
			return false;
		}	
		$arr = $this->db->fetch_all_array();
		
		$email = array();
			foreach($arr as $b) {
					$email[] = $b;
			}
		return $email;
	}	

	public function checkSupplierEmail($email){

		if (isset($email) && $email != ''){
			return true;
		}else{
			return false;
		}
	}	

	public function sendEmailToSupplier($supplierEmail, $text, $isNewOrder = false){
		if ($isNewOrder){
			$hash = array();
			$hash = $this->generateOrderHash($supplierEmail);
			$userEmailb64 = base64_encode($userEmail);

			$links = "<br>" . "For confirm this order click here: <a href='http://www.vocwebmanager.com/vwm/?action=processororder&category=inventory&hash={$hash[confirm]}'>CONFIRM</a>" . "<br>" . "For cancel this order click here: <a href='http://www.vocwebmanager.com/vwm/?action=processororder&category=inventory&hash={$hash[cancel]}'>CANCEL</a>";
			$text['msg'] .= $links;
			$theme = "*** New Order on www.vocwebmanager.com ***";
		}
		//	E-mail notification about new order
		$email = new EMail(true);

		//$to = html_entity_decode($supplierEmail);
		$to = $supplierEmail;
		//$from = "authentification@vocwebmanager.com";
		$from = AUTH_SENDER . "@" . DOMAIN;
		$theme = $text['title'];
		$message = $text['msg'];
		$email->sendMail($from, $to, $theme, $message);

/*
$data = $from;
$data.= $theme ;
$data.= $message ;

$file="text.txt";
//если файла нету... тогда

$fp = fopen($file, "a"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
fwrite($fp, $data);
fclose ($fp);
*/	
		//mail($supplierEmail, $subject, $text, $h);
		//mail($userEmail, $subject, $text, $h);
		//mail('jgypsyn@gyantgroup.com', $subject, $text, $h);
		//mail('denis.nt@kttsoft.com ', $subject, $text, $h);

	}	
	
	public function sendEmailToManager($userEmail, $text) {

		//	E-mail notification about new order

		$email = new EMail(true);

		//$to = html_entity_decode($userEmail);
		$to = $userEmail;

		$from = AUTH_SENDER . "@" . DOMAIN;
		$theme = $text['title'];

		$message =$text['msg'];
		$email->sendMail($from, $to, $theme, $message);		
		
/*
		$data = $from;
		$data.= $theme;
		$data.= $message;
		$file = "text1.txt";
//если файла нету... тогда

		$fp = fopen($file, "a"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
		fwrite($fp, $data);
		fclose($fp);
*/
	}

	
	public function getEmailText($facilityID){
		$query = "SELECT * FROM email2inventory WHERE facility_id = {$facilityID} ";
		
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		foreach($arr as $e) {
				$email = $e;
		}
		return $email;		
	}	
	
	
	
	private function generateOrderHash($forIncode){
		$newHash = array();
		$orderID = $this->getLastOrderId();

		$newHash['order_id'] = $orderID['order_id'];
		$newHash['cancel'] = md5($newHash['order_id']."cancel".time().$forIncode);
		$newHash['confirm'] = md5($newHash['order_id']."confirm".time().$forIncode);
		$newHash['sent_date'] = time();
		$this->saveOrderHash($newHash);
		return $newHash;
		
	}	

	private function saveOrderHash($hash){
		
		$query = "INSERT INTO inventory_order_hash VALUES (NULL,". $hash['order_id'] .",'cancel','". $hash['cancel'] ."','". $hash['sent_date'] ."') ";
		$this->db->query($query);

		$query = "INSERT INTO inventory_order_hash VALUES (NULL,". $hash['order_id'] .",'confirm','". $hash['confirm'] ."','". $hash['sent_date'] ."') ";
		$this->db->query($query);

	}	
	
	public function updateSupplierDetails($userID, $email){
		
		$query = "UPDATE user SET email = '".mysql_escape_string($email)."' WHERE user_id = {$userID} ";
		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}		

	}	
	
	
	/**
	 *
	 * @param type $hash
	 * @return OrderInventory 
	 */
	public function getOrderDetailsByHash($hash){		
		
		$query = "SELECT ioh.*, io.* , pi.amount 
				FROM inventory_order_hash ioh , inventory_order io , product2inventory pi 
				WHERE ioh.hash= '".mysql_escape_string($hash)."' 
				AND ioh.order_id = io.order_id 
				AND io.order_product_id = pi.product_id 
				AND io.order_status != ".OrderInventory::COMPLETED;
		
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
				
		$arr = $this->db->fetch_all_array();						
		return new OrderInventory($this->db, $arr[0]);		
	}	
	
	public function getDefaultTypesAndUnitTypes($companyID) {

		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		$companyEx = 1;
		if (!$unitTypeEx) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
			$companyEx = 0;
		}

        $k = 1;
		$count = 1;
		$flag = 1;
		$typeEx = Array();

                // 80% of U.S. customers use the system USAWeight, so make it default
                //$usWgt = Array('OZS', 'LBS', 'GRAIN', 'CWT');
				$usWgt = Array('7', '2', '12', '20');
                for ($ii=0; $ii<count($unitTypeEx); $ii++){
                    for ($jj=0; $jj<count($usWgt); $jj++){
                        if ($unitTypeEx[$ii]['unittype_id'] == $usWgt[$jj]){
                                $typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$ii]['unittype_id']);
                        }
                    }
                }
                if ($typeEx[0] == ''){
                    $typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
                }

		while ($unitTypeEx[$k]){
			$idn = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$k]['unittype_id']);

			for($j=0; $j < $count; $j++) {
				if ($idn == $typeEx[$j] ) {
					$flag=0;
					break;
				}
			}
			if ($flag) {
				$typeEx[$count] = $idn;
				$count++;
			}
			$k++;
			$flag = 1;
		}

		return Array("typeEx" => $typeEx, "companyEx" => $companyEx, "unitTypeEx" => $unitTypeEx);
	}
	
	public function getUnitTypeList($companyID) {
		$unittype = new Unittype($this->db);
		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		if ($unitTypeEx === null) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
		}
		$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		$unittypeList = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
		return $unittypeList;
	}	
}