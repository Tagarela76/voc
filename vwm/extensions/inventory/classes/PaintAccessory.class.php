<?php
interface iPaintAccessory {
	public function setID($id);	    	       
    public function setUnitAmount($unitAmount);
    public function setUnitCount($unitCount);
    public function setUnitQuantity($unitQuantity);
    public function setTotalQuantity($totalQuantity);
    
    public function getID();   
    public function getUnitAmount();
    public function getUnitCount();
    public function getUnitQuantity();
    public function getTotalQuantity();	
}

class PaintAccessory extends Accessory implements iPaintAccessory {
	
	private $id;
	private $unitAmount;
	private $unitCount;
	private $unitQuantity;
	private $totalQty;
	
	
    function PaintAccessory($db, $id = null) {
    	$this->db = $db;
    	if (isset($id)) {
    		$this->setID($id);
    		$this->_load();
    	}    	
    }
    
    public function setID($id) {
    	$id=mysql_escape_string($id); 	
    	$this->id = $id;
    }   
    public function setUnitAmount($unitAmount) {    	
    	$this->unitAmount = (empty($unitAmount)) ? 0.0 : $unitAmount;
    }
    public function setUnitCount($unitCount) {
    	$this->unitCount = $unitCount;	
    }
    public function setUnitQuantity($unitQuantity) {
    	$this->unitQuantity = (empty($unitQuantity)) ? 0.0 : $unitQuantity;
    }
    public function setTotalQuantity($totalQuantity) {
    	$this->totalQuantity = $totalQuantity;
    }
    
    public function getID() {
    	return $this->id; 	
    }
    public function getUnitAmount() {
    	return $this->unitAmount;
    }
    public function getUnitCount() {
    	return $this->unitCount;	
    }
    public function getUnitQuantity() {
    	return $this->unitQuantity;
    }
    public function getTotalQuantity() {
    	return $this->totalQuantity;
    }
    
    
    private function _load() {
    	$query = "SELECT * FROM ".TB_ACCESSORY2INVENTORY." WHERE id = ".$this->getID()."";
    	$this->db->query($query);
    	
    	if ($this->db->num_rows() == 0) {
    		return false;
    	}
    	
		$this->_xnyo2properties($this->db->fetch(0));
		
		// set product-related properties
		$productDetails = $this->getAccessoryDetails($this->accessoryID);
		//$this->setSupplier($productDetails['supplier_id']);
		//$this->setProductNR($productDetails['product_nr']);
		$this->setAccessoryName($productDetails['name']);						 
    }
    
    
     private function _xnyo2properties($dataRow) {    	
    	$this->setAccessoryID($dataRow->accessory_id);
		$this->inventoryID = $dataRow->inventory_id;
		
		$this->setUnitAmount($dataRow->unit_amount);
		$this->setUnitCount($dataRow->unit_count);
		$this->setUnitQuantity($dataRow->unit_qty);
		$this->setTotalQuantity($dataRow->total_qty);		
    }
}
?>