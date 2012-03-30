<?php

interface iInventory {
	public function setID($id);
    public function setName($name);
    public function setDescription($description);
    public function setType($type = Inventory::PAINT_MATERIAL);
    public function setFacilityID($facilityID);
    
    public function getID();
    public function getName();
    public function getDescription();
    public function getType();
    public function getFacilityID();
    
    public function getProducts();
    public function addProduct($product);
            
    public function save();
    public function deleteProducts();
    
    public function delete();
    public function validateProduct();        
    
    public function getSupplier();
    public function getProductNR();
    public function getProductDescription();
}


 
class Inventory implements iInventory {
	
	//	inventory's properties
	public $id;
	public $name;
	public $description;
	public $type;	//	material or accessory
	private $products = array();
	
	//	link to
	private $facilityID;
	private $departmentID;

	//	hello xnyo
	private $db;
	
	//	my beautiful tracking system
	private $trashRecord;
	private $parentTrashRecord;
	
	//	others
	public $url;
	
	const PAINT_MATERIAL = 'material';
	const PAINT_ACCESSORY = 'accessory';

	/**
	 * 
	 * constructor
	 * @param unknown_type $db
	 * @param unknown_type $id
	 * @param boolean $loadProducts - we do not need products, just inventory listing? - false 
	 */
    function Inventory($db, $id = null, $loadProducts = true) {
    	$this->db = $db;
    	if (isset($id)) {
    		$this->setID($id);
    		$this->_load($loadProducts);    		
    	}
    }
    
    public function getSupplier() {
    	if(empty($this->products)) {
    		$this->_load(true);
    	}
    	if(!empty($this->products) and count($this->products) > 0) {

    		$result = array();
    		$count = count($this->products);
    		$limit = 3;
    		$str = "";
    		
    		for($i=0; $i<$count and $i < $limit; $i++) {
    			$result[$this->products[$i]->getSupplier()] = true;
    			
    		}
    		foreach($result as $key => $val) {
    			$str .= $key . " , ";
    		}
    		
    		$str = substr_replace($str,"",strlen($str)-3);
    		return $str;
    	}
    	else {
    		return false;
    	}
    }
    public function getProductNR() {
    	if(empty($this->products)) {
    		$this->_load(true);
    	}
    	if(!empty($this->products) and count($this->products) > 0) {

    		$result = array();
    		$count = count($this->products);
    		$limit = 3;
    		$str = "";
    		
    		for($i=0; $i<$count and $i < $limit; $i++) {
    			$result[$this->products[$i]->getProductNR()] = true;
    			
    		}
    		foreach($result as $key => $val) {
    			$str .= $key . " , ";
    		}
    		
    		$str = substr_replace($str,"",strlen($str)-3);
    		return $str;
    	}
    	else {
    		return false;
    	}
    }
    public function getProductDescription() {
    	if(empty($this->products)) {
    		$this->_load(true);
    	}
    	if(!empty($this->products) and count($this->products) > 0) {

    		$result = array();
    		$count = count($this->products);
    		$limit = 3;
    		$str = "";
    		
    		for($i=0; $i<$count and $i < $limit; $i++) {
    			$result[$this->products[$i]->getName()] = true;
    			
    		}
    		foreach($result as $key => $val) {
    			$str .= $key . " , ";
    		}
    		
    		$str = substr_replace($str,"",strlen($str)-3);
    		return $str;
    	}
    	else {
    		return false;
    	}
    }
    
    public function setID($id) {
    	
    	$this->id = mysql_escape_string($id);	
    }
    public function setName($name) {
    	$this->name = mysql_escape_string($name);
    }
    public function setDescription($description) {
    	$this->description = mysql_escape_string($description);
    }
    public function setType($type = self::PAINT_MATERIAL) {
    	$this->type = mysql_escape_string($type);
    }
    public function setFacilityID($facilityID) {
    	$this->facilityID = mysql_escape_string($facilityID);
    }
    
    public function getID() {
    	return $this->id;
    }
    public function getName() {
    	return $this->name;
    }
    public function getDescription() {
    	return $this->description;
    }
    public function getType() {
    	return $this->type;
    }
    public function getFacilityID() {
    	return $this->facilityID;
    }
    public function getProducts() {
    	return $this->products;
    }
    
    //	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection	
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;		
	}
	public function setParentTrashRecord(iTrash $trashRecord) {
		$this->parentTrashRecord = $trashRecord;
	}		
    
    
    
    public function save() {
    	$query = "SELECT * FROM ".TB_INVENTORY." WHERE id = ".$this->getID()."";
    	$this->db->query($query);    	
    	if ($this->db->num_rows() > 0) {    		
    		//	save to trash
			$this->save2trash('U');
			    		    	
    		//	update
    		$this->_update();
    		switch ($this->getType()) {
    			case self::PAINT_MATERIAL:    			
    				$this->_replacePaintMaterials();    				    				
    				break;
    			case self::PAINT_ACCESSORY:
    				$this->_replacePaintAccessories();
    				break;
    		}    		
    	} else {    		
    		//	insert    		
    		$this->_insert();
    		switch ($this->getType()) {
    			case self::PAINT_MATERIAL:    				
    				$this->_replacePaintMaterials();    				    				
    				break;
    			case self::PAINT_ACCESSORY:
    				$this->_replacePaintAccessories();
    				break;
    		}    		
    		//	save to trash
			$this->save2trash('C');
    	}    
    }
    
    
    
    
    public function addProduct($product) {
    	$this->products[] = $product;    	
    }
    
    
    
    
    public function deleteProducts() {
    	$this->products = array();    	
    }
    
    
    
    
    public function delete() {
    	//	save to trash
		$this->save2trash('D');
		
		if (null !== ($inUseList = $this->isInUseList())) {						
			$equipment = new Equipment($this->db);
			$equipment->setParentTrashRecord($this->trashRecord);
			foreach ($inUseList as $dependedEquipment) {
				$equipment->setTrashRecord(new Trash($this->db));
				$equipment->deleteEquipment($dependedEquipment["id"]);
			}				
		}
    	    	
    	$this->_delete();
    }
    
    
    
    //	sum of dep usage should be < total inventory
    public function validateProduct() {
    	$result['summary'] = true;
    	foreach ($this->getProducts() as $product) {
    		$used = 0;
    		foreach ($product->getUseLocation() as $useLocation) {
    			$used += $useLocation['totalQty'];
    		}
    		 $productResult = ($used <= $product->getTotalQty() ) ? true : ($product->getTotalQty() - $used);    
    		if ($productResult !== true) {
    			$result['summary'] = false;
    		}
    		$result['products'][] = $productResult;		
    	}
    	return $result;
    }
    
    
    
    
    public function isInUseList() {	
		$query = "SELECT * FROM ".TB_EQUIPMENT." WHERE inventory_id = ".$this->getID();
		$this->db->query($query);
		
		$equipments = $this->db->fetch_all();
		foreach ($equipments as $equipment) {			
		    $linkedEquipment = array(
		    	'id' 	=> $equipment->equipment_id,
		    	'desc' 	=> $equipment->equip_desc
		    );				    
		    $linkedEquipments[] = $linkedEquipment;
		}					
		return $linkedEquipments;		
	}
    
    
    
    
    private function _xnyo2properties($dataRow) {
    	$this->setID($dataRow->id);
		$this->setName($dataRow->name);
		$this->setDescription($dataRow->description);
		$this->setType($dataRow->type);
		$this->setFacilityID($dataRow->facility_id);
    }
    
    
    
    
    private function _getCustomInventoryID($vanillaInventoryID, $supplierID) {
		if ($supplierID == 0) {
			return $vanillaInventoryID;
		} else {
			return $this->getSupplierCode($supplierID)." ".$vanillaInventoryID;
		}
	}
	
	
	
	
	private function _load($loadProducts) {
		$query = "SELECT * FROM ".TB_INVENTORY." WHERE id = ".$this->getID()."";
		$this->db->query($query);
		
		//	sql should not return empty result
		if ($this->db->num_rows() == 0) {			
			return false;
		}
		
		$this->_xnyo2properties($this->db->fetch(0));
		
		if ($loadProducts) {
			//	assign products
			if ($this->getType() == self::PAINT_MATERIAL) {
				$query = "SELECT id FROM ".TB_MATERIAL2INVENTORY." WHERE inventory_id = ".$this->getID()."";
				$this->db->query($query);
					
				if ($this->db->num_rows() > 0) {
					$dataRows = $this->db->fetch_all();
					foreach ($dataRows as $dataRow) {
						$this->addProduct(new PaintMaterial($this->db, $dataRow->id));
					}
				}
					
			} elseif ($this->getType() == self::PAINT_ACCESSORY) {
				$query = "SELECT id FROM ".TB_ACCESSORY2INVENTORY." WHERE inventory_id = ".$this->getID()."";
				$this->db->query($query);
					
				if ($this->db->num_rows() > 0) {
					$dataRows = $this->db->fetch_all();
					foreach ($dataRows as $dataRow) {
						$this->addProduct(new PaintAccessory($this->db, $dataRow->id));
					}
				}
			}
		}		
		
		return true;		
	}
	
	
	
	private function _replacePaintMaterials() {
		$query = "SELECT id FROM ".TB_MATERIAL2INVENTORY." WHERE inventory_id = ".$this->getID();		
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$data = $this->db->fetch_all();
			foreach ($data as $material2inventory) {
				$query = "DELETE FROM ".TB_USE_LOCATION2MATERIAL." WHERE material2inventory_id = ".$material2inventory->id;
				$this->db->query($query);					
			}
			
			$query = "DELETE FROM ".TB_MATERIAL2INVENTORY." WHERE inventory_id = ".$this->getID();
			$this->db->query($query);								
		}								
		
		if (count($this->products) == 0) {
			return true;
		}
		
		foreach($this->products as $material) {			
			//	storage location NULL or 'value'			
			$sqlStorageLocation = ($material->getStorageLocation() === "") ? "NULL" : "'".$material->getStorageLocation()."'";						
			$query = "INSERT INTO ".TB_MATERIAL2INVENTORY." (product_id, inventory_id, os_use, cs_use, storage_location, total_qty) VALUES (" .
					"".mysql_real_escape_string($material->getProductID()).", " .
					"".mysql_real_escape_string($this->getID()).", " .
					"".mysql_real_escape_string($material->getOS_use()).", " .
					"".mysql_real_escape_string($material->getCS_use()).", " .
					"".mysql_real_escape_string($sqlStorageLocation).", " .
					"".mysql_real_escape_string($material->getTotalQty()).")";
			$this->db->query($query);
//			$material->setID(mysql_insert_id());
		$this->db->query("SELECT LAST_INSERT_ID() id");
		$ID = $this->db->fetch(0)->id;	
		$material->setID($ID);
			
			if (is_array($material->getUseLocation())) {
				foreach ($material->getUseLocation() as $department) {
					$query = "INSERT INTO ".TB_USE_LOCATION2MATERIAL." (department_id, material2inventory_id, total_qty) VALUES (" .
							"".mysql_real_escape_string($department['departmentID']).", " .
							"".mysql_real_escape_string($material->getID())."," .
							"".mysql_real_escape_string($department['totalQty'])." )";
					$this->db->query($query);
				}	
			}			
		}
	}
	
	
	
	private function _insert() {
		$query = "INSERT INTO ".TB_INVENTORY." (name, description, type, facility_id) VALUES (" .
				"'".mysql_real_escape_string($this->getName())."', " .
				"'".mysql_real_escape_string($this->getDescription())."', " .
				"'".mysql_real_escape_string($this->getType())."', " .
				"".mysql_real_escape_string($this->getFacilityID()).")";
		$this->db->query($query);
//		$this->setID(mysql_insert_id());
		$this->db->query("SELECT LAST_INSERT_ID() id");
		$ID = $this->db->fetch(0)->id;	
		$this->setID($ID);		
	}
	
	
	
	
	private function _update() {
		$query = "UPDATE ".TB_INVENTORY." SET " .
					"name = '".mysql_real_escape_string($this->getName())."', " .
					"description = '".mysql_real_escape_string($this->getDescription())."' " .
				"WHERE id = ".$this->getID()."";				
		$this->db->query($query);		
	}
	
	
	
	
	private function _replacePaintAccessories() {
		$query = "DELETE FROM ".TB_ACCESSORY2INVENTORY." WHERE inventory_id = ".$this->getID();
		$this->db->query($query);				
		
		if (count($this->products) == 0) {
			return true;
		}
		
		foreach($this->products as $accessory) {	
			//	UnitCount NULL or 'value'			
			$sqlUnitCount = ($accessory->getUnitCount() === "") ? "NULL" : "'".$accessory->getUnitCount()."'";			
											
			$query = "INSERT INTO ".TB_ACCESSORY2INVENTORY." (accessory_id, inventory_id, unit_amount, unit_count, unit_qty, total_qty) VALUES (" .
					"".mysql_real_escape_string($accessory->getAccessoryID()).", " .
					"".mysql_real_escape_string($this->getID()).", " .
					"".mysql_real_escape_string($accessory->getUnitAmount()).", " .
					"".mysql_real_escape_string($sqlUnitCount).", " .
					"".mysql_real_escape_string($accessory->getUnitQuantity()).", " .
					"".mysql_real_escape_string($accessory->getTotalQuantity()).")";			
			$this->db->query($query);
//			$accessory->setID(mysql_insert_id());
			$this->db->query("SELECT LAST_INSERT_ID() id");
			$ID = $this->db->fetch(0)->id;	
			$accessory->setID($ID);						
		}
	}

	
	
	
	private function _delete() {
		$query = "DELETE FROM ".TB_INVENTORY." WHERE id = ".$this->getID()."";		
		$this->db->query($query);
	}
	
	
	
	
	//	Tracking System
	private function save2trash($CRUD) {
		
		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_INVENTORY, $this->getID(), $CRUD, $this->parentTrashRecord);
		
		//	DEPRECATED July 16, 2010		
//		if (isset($this->trashRecord)) {	
//			$query = "SELECT * FROM ".TB_INVENTORY." WHERE id = ".$this->getID()."";
//			$this->db->query($query);
//			$dataRows = $this->db->fetch_all();
//
//			foreach ($dataRows as $dataRow) {
//				$parentID = (isset($this->parentTrashRecord)) ? $this->parentTrashRecord->getID() : null;
//				
//				$inventoryRecords = TrackingSystem::properties2array($dataRow);		
//				$this->trashRecord->setTable(TB_INVENTORY);		
//				$this->trashRecord->setData(json_encode($inventoryRecords[0]));
//				$this->trashRecord->setUserID($_SESSION['user_id']);
//				$this->trashRecord->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//				$this->trashRecord->setDate(time());	//	current time
//				$this->trashRecord->setReferrer($parentID);
//				$this->trashRecord->save();		
//			}			
//
//			if ($CRUD != 'D') {
//				//	load and save dependencies
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
}
?>