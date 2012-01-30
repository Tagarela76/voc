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
				  " LEFT JOIN product2inventory pi ON p.product_id = pi.product_id " .
				  " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."'";

		if ($productID){
			$query .= "AND p.product_id = {$productID} ";
		}
		$query .= " GROUP BY mg.product_id " .
				  " ORDER BY p.product_id ";
		//"AND m.creation_time BETWEEN '".$beginDate->formatInput()."' AND '".$endDate->formatInput()."' " .
//               /echo $query;
		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		if ($this->db->num_rows() == 1){
			$productUsageData = new ProductInventory($this->db, $arr[0]);
		}else{
		$productUsageData = array();
			foreach($arr as $b) {
				$productinv = new ProductInventory($this->db, $b);
				$productUsageData[] = $productinv;                        
			}
		}
		return $productUsageData;
	}
	
	public function getProductsSupplierList($category, $categoryID, $productID = null) {
            
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

			$tables .= ", ".TB_PRODUCT." p, " . TB_SUPPLIER . " s";
		
		
		$query = "SELECT DISTINCT p.supplier_id, s.original_id, s.supplier, di.discount ";
				
		$query .=	" FROM {$tables} " .
					" LEFT JOIN discounts2inventory di ".
					" ON di.supplier_id =  s.original_id AND di.facility_id = {$categoryID} ".
					" WHERE {$categoryDependedSql} " .
					" AND p.product_id = mg.product_id " .
					" AND m.mix_id = mg.mix_id ".
					" AND p.supplier_id  = s.supplier_id ";


		//echo $query;
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
	}	
	
	public function getSupplierDiscounts($facilityID, $supplierID ) {
            
        $query = "SELECT di.*, s.supplier ";
				
		$query .= " FROM supplier s " .
				  " LEFT JOIN discounts2inventory di ".
				  " ON di.supplier_id =  {$supplierID} AND di.facility_id = {$facilityID} ".
				  " WHERE s.supplier_id =  {$supplierID} ";


		//"AND m.creation_time BETWEEN '".$beginDate->formatInput()."' AND '".$endDate->formatInput()."' " .
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
            $query = "UPDATE discounts2inventory SET 
			discount = '".mysql_escape_string($form['discount'])."'
			WHERE discount_id = {$form['discount_id']}";			

		}



		//"AND m.creation_time BETWEEN '".$beginDate->formatInput()."' AND '".$endDate->formatInput()."' " .

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
				// TODO reduce the amount of product in stock with type convert!! 
		foreach ($productsOldVal as $product){
			

			$oldInventory = $this->getInventoryInfoForProduct($product->product_id, $mix->facility_id, $mix->mix_id);

			$productUsageData = new ProductInventory($this->db, $oldInventory);
			
			$productUsageData->in_stock = $productUsageData->in_stock + $product->quantity_lbs;
			
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

		$query = "SELECT sum(mg.quantity_lbs) as sum, mg.quantity_lbs, pi.* 
				FROM mix m, mixgroup mg, department d , product p 
				LEFT JOIN product2inventory pi ON p.product_id = pi.product_id 
				WHERE m.department_id = d.department_id
				AND p.product_id = ".$productID."
				AND d.facility_id = ".$facilityID."
				AND p.product_id = mg.product_id 
				AND m.mix_id = ".$mixID." 
				AND m.mix_id = mg.mix_id";
		
		//echo $query;
		//echo "<BR>";

		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$inventory = $arr[0];		
		return $inventory;
	}	
	
	
	public function runInventoryOrderingSystem( $mix ) {
		$productObjArray = $mix->products;

		foreach ($productObjArray as $productObj){
			$inventory = $this->getInventoryInfoForProduct($productObj->product_id, $mix->facility_id, $mix->mix_id);
			$productUsageData = new ProductInventory($this->db, $inventory);
			if ($productUsageData->in_stock <= $productUsageData->limit){
				//var_dump('inventory',$inventory,$productUsageData);		die();
			}
		
		
		}
		
	}

	
		
}