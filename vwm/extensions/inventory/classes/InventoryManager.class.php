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
		$query .= " GROUP BY mg.product_id, m.creation_time " .
				  " ORDER BY p.product_id ";
		//"AND m.creation_time BETWEEN '".$beginDate->formatInput()."' AND '".$endDate->formatInput()."' " .
               //echo $query;
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

			$tables .= ", ".TB_PRODUCT." p, supplier s";
		
		
		$query = "SELECT DISTINCT p.supplier_id, s.original_id, s.supplier ";
				
		$query .= " FROM {$tables} " .
				  
				  " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id ".
					"AND p.supplier_id  = s.supplier_id ";


		//"AND m.creation_time BETWEEN '".$beginDate->formatInput()."' AND '".$endDate->formatInput()."' " .
              // echo $query;
		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		/*if ($this->db->num_rows() == 1){
			$productUsageData = new ProductInventory($this->db, $arr[0]);
		}else{
		$productUsageData = array();
			foreach($arr as $b) {
				$productinv = new ProductInventory($this->db, $b);
				$productUsageData[] = $productinv;                        
			}
		}*/
		return $arr;
	}	
	
	
		
}