<?php
class InventoryManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getProductUsageGetAll(DateTime $beginDate, DateTime $endDate, $category, $categoryID, $productID = null) {
            
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
		
		
		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name,p.product_id, pi.inventory_id, pi.in_stock, pi.amount, pi.inventory_limit, pi.in_stock_unit_type ";
				
		$query .= " FROM {$tables} " .
				  " LEFT JOIN product2inventory pi ON p.product_id = pi.product_id AND pi.facility_id = {$categoryID} " .
				  " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."'";

		if ($productID){
			$query .= "AND p.product_id = {$productID} ";
		}
		$query .= " GROUP BY mg.product_id " .
				  " ORDER BY p.product_id ";

		//echo $query;
		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		

		$productUsageData = array();
			foreach($arr as $b) {
				$productinv = new ProductInventory($this->db, $b);
				$productUsageData[] = $productinv;                        
			}

		return $productUsageData;
	}
	
	public function getProductsSupplierList($categoryID, $productID = null) {
        if ($productID){ 
			$categoryDependedSql = "";
			$tables = TB_USAGE." m, ".TB_MIXGROUP." mg";

			$tables .= ", ".TB_DEPARTMENT." d ";
			$categoryDependedSql = " m.department_id = d.department_id AND d.facility_id = {$categoryID} ";
			$tables .= ", ".TB_PRODUCT." p, " . TB_SUPPLIER . " s";


			$query	=    "SELECT DISTINCT p.supplier_id, s.original_id, s.supplier, di.discount ";

			$query .=	" FROM {$tables} " .
						" LEFT JOIN discounts2inventory di ".
						" ON di.supplier_id =  s.original_id AND di.facility_id = {$categoryID} ".
						" WHERE {$categoryDependedSql} " ;

			$query .=   " AND p.product_id  = {$productID} ";

			$query .=	" AND p.product_id = mg.product_id " .
						" AND m.mix_id = mg.mix_id ".
						" AND p.supplier_id  = s.supplier_id ";

			$this->db->query($query);

			$arr = $this->db->fetch_all_array();


			$SupData = array();
				foreach($arr as $b) { 
					if ( $b['supplier_id'] <> $b['original_id'] ){
						$query = "SELECT supplier FROM " . TB_SUPPLIER . " WHERE original_id=supplier_id AND original_id='" .$b['original_id']. "' ORDER BY supplier ASC";
						$this->db->query($query);
						$suppliername = $this->db->fetch_all_array();
						$b['supplier'] = $suppliername[0]['supplier'];
						$SupData[] = $b;
					}else{
						$SupData[] = $b;
					}

				}

			return $SupData;		
		
		
		}else{		
			$query	=	"SELECT DISTINCT di.*, s.supplier ";

			$query .=	" FROM discounts2inventory di, " . TB_SUPPLIER . " s   " .
						" WHERE di.facility_id =  {$categoryID} AND di.supplier_id = s.original_id AND s.supplier_id = s.original_id";	
			$this->db->query($query);

			$SupData = $this->db->fetch_all_array();
			return $SupData;						
		}

	

	}	
	
	public function getSupplierOrders($facilityID) {
        $time = new DateTime('first day of this month');

        $query = "SELECT io.*, pi.amount ";
				
		$query .=	" FROM inventory_order io , product2inventory pi " .

					" WHERE io.order_facility_id = {$facilityID} AND pi.product_id = io.order_product_id AND pi.facility_id = {$facilityID} AND io.order_created_date >= {$time->getTimestamp()}";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		

		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
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
	
	public function getSupplierOrderDetails($facilityID,$orderID) {
        $time = new DateTime('first day of this month');

        $query = "SELECT io.*, pi.amount ";
				
		$query .=	" FROM inventory_order io , product2inventory pi " .

					" WHERE io.order_id = {$orderID} AND pi.product_id = io.order_product_id AND pi.facility_id = {$facilityID} AND io.order_created_date >= {$time->getTimestamp()}";

		$this->db->query($query);
	
		$arr = $this->db->fetch_all_array();
		
		//echo $query;
		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
	}	
	
	public function updateSupplierOrder( $form ) {

										
		if ($form['order_id'] == null){
			//$query = "INSERT INTO inventory_order VALUES () ";
				

		}else{
            $query = "UPDATE inventory_order SET order_status = '{$form['status']}' WHERE order_id = {$form['order_id']}";			

		}

		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function getSupplierDiscounts($facilityID, $supplierID ) {
            
        $query = "SELECT di.*, s.supplier ";
				
		$query .= " FROM supplier s " .
				  " LEFT JOIN discounts2inventory di ".
				  " ON di.supplier_id =  {$supplierID} AND di.facility_id = {$facilityID} ".
				  " WHERE s.supplier_id =  {$supplierID} ";


		//echo $query;
		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}
	
	public function updateSupplierDiscounts( $form ) {

										
		if ($form['discount_id'] == null){
			$query = "INSERT INTO discounts2inventory VALUES (NULL,". $form['facilityID'] .",". $form['supplier_id'] .",". $form['discount'] .") ";
				

		}else{
            $query = "UPDATE discounts2inventory SET discount = '{$form['discount']}' WHERE discount_id = {$form['discount_id']}";			

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
			email_all = '".mysql_escape_string($form['email_all'])."',
			email_manager = '".mysql_escape_string($form['email_manager'])."'
			WHERE email_id = {$form['email_id']} ";			

		}

		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	
	public function getInventoryByID($inventoryID) {
            
        $query = "SELECT * FROM product2inventory WHERE inventory_id = {$inventoryID} ";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}	

	public function inventoryInstockDegreece( $productsOldVal,$mix ) {
				
		
		foreach ($productsOldVal as $product){
			

			$oldInventory = $this->getInventoryInfoForProduct($product->product_id, $mix->facility_id, $mix->mix_id);

			$productUsageData = new ProductInventory($this->db, $oldInventory);
			$productUsageData->product_id = ($productUsageData->product_id == null) ? $product->product_id : $productUsageData->product_id;
			$productUsageData->in_stock_unit_type = ($productUsageData->in_stock_unit_type == null) ? $product->unit_type : $productUsageData->in_stock_unit_type;
			
			if ($productUsageData->id != null){
				$productUsageData->in_stock = $productUsageData->in_stock + $product->quantity_lbs;
			}			
			
			
			
			$productUsageData->save();

		}		
		
		$productObjArray = $mix->products;

		foreach ($productObjArray as $productObj){

			$inventory = $this->getInventoryInfoForProduct($productObj->product_id, $mix->facility_id, $mix->mix_id);

			$productUsageData = new ProductInventory($this->db, $inventory);
			
			$productUsageData->in_stock = $productUsageData->in_stock - $inventory['sum'];
			
			$productUsageData->save();

		}		

	}	
	
	
	public function getInventoryInfoForProduct( $productID, $facilityID, $mixID ) {

		$query = "SELECT sum(mg.quantity_lbs) as sum, mg.quantity_lbs, p.product_nr, pi.* 
				FROM mix m, mixgroup mg, department d , product p 
				LEFT JOIN product2inventory pi ON p.product_id = pi.product_id 
				WHERE m.department_id = d.department_id
				AND p.product_id = ".$productID."
				AND pi.facility_id = ".$facilityID."
				AND d.facility_id = ".$facilityID."
				AND p.product_id = mg.product_id 
				AND m.mix_id = ".$mixID." 
				AND m.mix_id = mg.mix_id";
		
		//echo $query;
		//echo "<BR>";

		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$inventory = $arr[0];
		$inventory['product_id'] = ($inventory['product_id'] == null) ? $productID : $inventory['product_id'];
		return $inventory;
	}	
	
	public function checkInventoryOrderByPrductId($product_id,$facility_id){
		
        $query = "SELECT * FROM inventory_order WHERE order_product_id = {$product_id} AND order_facility_id = {$facility_id} ";
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();

		$data = array();
			foreach($arr as $b) {
					$data = $b;
			}
		return $data;		
	}
	
	public function runInventoryOrderingSystem( $mix ) {
		$productObjArray = $mix->products;
		// TODO reduce the amount of product in stock with type convert!!
		foreach ($productObjArray as $productObj){

			$inventory = $this->getInventoryInfoForProduct($productObj->product_id, $mix->facility_id, $mix->mix_id);
			$productUsageData = new ProductInventory($this->db, $inventory);

			if ($productUsageData->id == null){
				$result = $productUsageData->save();

			}elseif ($productUsageData->in_stock - $productObj->quantity  <= $productUsageData->limit){
				
				$checkOrder = $this->checkInventoryOrderByPrductId($productUsageData->product_id, $mix->facility_id);
				
				if (!$checkOrder){
					
					$newOrder = new OrderInventory($this->db);
					//TODO get price
					$price = 10;
					$newOrder->order_product_id = $productUsageData->product_id;
					$newOrder->order_facility_id = $mix->facility_id;
					$newOrder->order_name = 'Order for product "'.$productUsageData->product_nr.'"';
					$newOrder->order_total = $productUsageData->amount * $price;
										
					$supplierDetails = $this->getProductsSupplierList($mix->facility_id,$productUsageData->product_id);
					
					$this->checkSupplierEmail($supplierDetails[0]['email']);

					$newOrder->save();
							
					
				}else{
					$supplierDetails = $this->getProductsSupplierList($mix->facility_id,$productUsageData->product_id);

					$supplierDetails[0]['email'] = '2reckiy@gmail.com';
					$this->checkSupplierEmail($supplierDetails[0]['email']);
					
					
				}
				
			}else{
				
			}			
		}
		
	}
	
	
	private function checkSupplierEmail($email){
		$user = new User($this->db);
		$userDetails = $user->getUserDetails($_SESSION['user_id']);
		if (isset($email) && $email != ''){
			$this->sendEmailToAll($email,$userDetails['email']);
		}else{
			$this->sendEmailToManager($userDetails['email']);
		}
	}	

	private function sendEmailToAll($supplierEmail, $userEmail){
	
	}	
	
	private function sendEmailToManager($userEmail){
		
	}	

	
		
}