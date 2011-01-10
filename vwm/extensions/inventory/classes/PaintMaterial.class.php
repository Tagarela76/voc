<?php

interface iPaintMaterial {
	public function setID($id);
    public function setOS_use($OS_use);
    public function setCS_use($CS_use);
    public function setStorageLocation($storageLocation);
    public function setTotalQty($totalQty);
    public function setLastInventory($lastInventory);
    public function setToDateLeft($toDateLeft);
    
    public function getID();
    public function getOS_use();
    public function getCS_use();
    public function getStorageLocation();
    public function getTotalQty();
    public function getLastInventory();
    public function getToDateLeft();
          
    public function addUseLocation($locationOfUse);
    public function clearUseLocation();
    public function getUseLocation();           
}



class PaintMaterial extends Product implements iPaintMaterial {
	
	//	ProductProperties
	//private $supplier;
	//private $nr;$productNR
	//private $name;
	private $OS_use;
	private $CS_use;
	private $storageLocation;
	private $useLocation = array();
	public $lastInventory = array();
	private $totalQty;
	private $toDateLeft;
	
	private $id;	
	private $inventoryID;	
	

    function PaintMaterial($db, $id = null) {
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
    public function setOS_use($OS_use) {
    	$this->OS_use = (empty($OS_use)) ? 0.0 : $OS_use;
    }
    public function setCS_use($CS_use) {
    	$this->CS_use = (empty($CS_use)) ? 0.0 : $CS_use;
    }
    public function setStorageLocation($storageLocation) {
    	$this->storageLocation = $storageLocation;
    }
    public function setTotalQty($totalQty) {
    	$this->totalQty = (empty($totalQty)) ? 0.0 : $totalQty;
    }
    public function setLastInventory($lastInventory) {
    	$this->lastInventory = $lastInventory;
    }
    public function setToDateLeft($toDateLeft) {
    	$this->toDateLeft = $toDateLeft;
    }
    
    public function getID() {
    	return $this->id;
    }
    public function getOS_use() {
    	return $this->OS_use;
    }
    public function getCS_use() {
    	return $this->CS_use;
    }
    public function getStorageLocation() {
    	return $this->storageLocation;
    }
    public function getTotalQty() {
    	return $this->totalQty;
    }
    public function getLastInventory() {
    	return $this->lastInventory;
    }
    public function getToDateLeft() {
    	return $this->toDateLeft;
    }
      
    
    
    //	$locationOfUse[$i]['departmentID']
    //	$locationOfUse[$i]['name']
    //	$locationOfUse[$i]['totalQty']
    //	$locationOfUse[$i]['used']    
    public function addUseLocation($locationOfUse) {
    	$this->useLocation[] = $locationOfUse;
    }    
    public function clearUseLocation() {
    	return $this->useLocation = array();
    }
    public function getUseLocation() {
    	return $this->useLocation;
    }
    
    
    
    
    public function delete() {
    	
    	$query = "DELETE FROM ".TB_MATERIAL2INVENTORY." WHERE id = ".(int)$this->getID()."";
    	
    	$this->db->exec($query);
    }
    
    
    
    
    private function _load() {
    	$query = "SELECT * FROM ".TB_MATERIAL2INVENTORY." WHERE id = ".$this->getID()."";
    	$this->db->query($query);
    	
    	if ($this->db->num_rows() == 0) {
    		return false;
    	}
    	
		$this->_xnyo2properties($this->db->fetch(0));
		
		// set product-related properties
		$productDetails = $this->getProductDetails($this->productID);
		$this->setSupplier($productDetails['supplier_id']);
		$this->setProductNR($productDetails['product_nr']);
		$this->setName($productDetails['name']);
		$this->setDensity($productDetails['density']);
		$this->setDensityUnitID($productDetails['densityUnitID']);		
		
		$this->_setUseLocation();
		
		$this->lastInventory = $this->_findLastInventory();
		
		$totalUsed = $this->_totalUsed();
		$this->toDateLeft = $this->totalQty - $totalUsed; 
    }
    
    
    
    
    private function _xnyo2properties($dataRow) { 
    	   	   	
    	$this->setProductID($dataRow->product_id);
		$this->inventoryID = $dataRow->inventory_id;
		
		$this->setOS_use($dataRow->os_use);
		$this->setCS_use($dataRow->cs_use);
		$this->setStorageLocation($dataRow->storage_location);
		$this->setTotalQty($dataRow->total_qty);		
    }
    
    
    
    
    private function _setUseLocation() {
    	$query = "SELECT ul2m.department_id, ul2m.total_qty, d.name " .
				"FROM ".TB_USE_LOCATION2MATERIAL." ul2m, department d " .
				"WHERE ul2m.department_id = d.department_id AND " .
				"ul2m.material2inventory_id = ".$this->getID()."";
		$this->db->query($query);		
		if ($this->db->num_rows() > 0) {
			$dataRows = $this->db->fetch_all();
			foreach ($dataRows as $dataRow) {				
				$locationOfUse = array (
					'departmentID'	=> $dataRow->department_id,
					'name'			=> $dataRow->name,
					'totalQty' 		=> $dataRow->total_qty,
					'used'			=> round($this->_totalUsed($dataRow->department_id), 2),
					'lastInventory'	=> $this->_findLastInventory($dataRow->department_id) 
				);								
				$this->addUseLocation($locationOfUse);
			}
		}
    }
    
    
    
    
    private function _findLastInventory($departmentID = null) {
    	if (!is_null($departmentID)) {
    		$query = "SELECT m.creation_time inventory, mg.quantity, mg.unit_type, u.name " .
				"FROM mix m, mixgroup mg, equipment e, ".TB_INVENTORY." i, unittype u " .
				"WHERE mg.mix_id = m.mix_id AND " .
				"m.equipment_id = e.equipment_id AND " .
				"e.inventory_id = i.id AND " .
				"u.unittype_id = mg.unit_type AND " .
				"mg.product_id = ".$this->productID." AND " .
				"i.id = ".$this->inventoryID." AND " .
				"m.department_id = ".$departmentID." " .
				"ORDER BY m.creation_time DESC " .
				"LIMIT 1";
    	} else {
    		$query = "SELECT m.creation_time inventory, mg.quantity, mg.unit_type, u.name " .
				"FROM mix m, mixgroup mg, equipment e, ".TB_INVENTORY." i, unittype u " .
				"WHERE mg.mix_id = m.mix_id AND " .
				"m.equipment_id = e.equipment_id AND " .
				"e.inventory_id = i.id AND " .
				"u.unittype_id = mg.unit_type AND " .
				"mg.product_id = ".$this->productID." AND " .
				"i.id = ".$this->inventoryID." " .
				"ORDER BY m.creation_time DESC " .
				"LIMIT 1";	
    	}    	
		$this->db->query($query);
			
		if ($this->db->num_rows() > 0) {
			$dataRow = $this->db->fetch(0);					
			return array (
				'inventory'		=> $dataRow->inventory,
				'quantity'		=> $dataRow->quantity,
				'unittype' 		=> $dataRow->unit_type,
				'unittypeName'	=> $dataRow->name
			);													
		}				
    }
    
    
    
    
    private function _totalUsed($departmentID = null) {
    	
    	if (!is_null($departmentID)) {
    		$query = "SELECT mg.unit_type, SUM(mg.quantity) quantity " .
				"FROM mix m, mixgroup mg, equipment e, ".TB_INVENTORY." i " .
				"WHERE mg.mix_id = m.mix_id AND " .
				"m.equipment_id = e.equipment_id AND " .
				"e.inventory_id = i.id AND " .
				"mg.product_id = ".$this->productID." AND " .
				"i.id = ".$this->inventoryID." AND " .
				"m.department_id = ".$departmentID." " .
				"GROUP BY unit_type";
    	} else {
    		$query = "SELECT mg.unit_type, SUM(mg.quantity) quantity " .
				"FROM mix m, mixgroup mg, equipment e, ".TB_INVENTORY." i " .
				"WHERE mg.mix_id = m.mix_id AND " .
				"m.equipment_id = e.equipment_id AND " .
				"e.inventory_id = i.id AND " .
				"mg.product_id = ".$this->productID." AND " .
				"i.id = ".$this->inventoryID." " .
				"GROUP BY unit_type";	
    	}    	
		$this->db->query($query);		
		
		if ($this->db->num_rows() == 0) {
			return 0;
		}
		$usedData = $this->db->fetch_all();
				
		$unittype = new Unittype($this->db);
		$unitTypeConverter = new UnitTypeConverter();
		$density = $this->getDensity();
		$densityID = $this->getDensityUnitID();
		
		// get Density Type
	    $cDensity = new Density($this->db, $densityID);
    	$numerator = $unittype->getUnittypeDetails($cDensity->getNumerator());
    	$denominator = $unittype->getUnittypeDetails($cDensity->getDenominator());
    	$densityType = array (
    		'numerator' => $numerator['description'],
    		'denominator' => $denominator['description']
    	);
    	
		//	check density
		if (empty($density) || $density == '0.00') {
			$density = false;
			$densityType = false;
		}
				
		$sum = 0;
		foreach ($usedData as $usage) {						
			$unitTypeDetails = $unittype->getUnittypeDetails($usage->unit_type);
			$sum += $unitTypeConverter->convertToDefault($usage->quantity, $unitTypeDetails["description"], $density, $densityType);						
		}  	
										
		return $sum;		
    }
    
}
?>