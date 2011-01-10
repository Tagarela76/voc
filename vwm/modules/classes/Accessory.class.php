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
	public function insertAccessory($companyID);
	public function updateAccessory();
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
    
    public function queryTotalCount($companyID) 
    {
		$query = "SELECT COUNT(*) cnt FROM ".TB_ACCESSORY." WHERE company_id=".(int)$companyID;
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
    
    public function getAllAccessory($companyID,$sort=' ORDER BY name ') {
    	$companyID=mysql_real_escape_string($companyID);
    	
    	$query = "SELECT id, name FROM ".TB_ACCESSORY." WHERE company_id=".(int)$companyID."  $sort";
    	$this->db->query($query);
    	
    	if ($this->db->num_rows()) 
    	{    		
    		return $this->db->fetch_all_array();
    	}
    	else
    		return false;
    }
    
    public function getAccessoryDetails() {
    	
    	$query = "SELECT * FROM ".TB_ACCESSORY." a WHERE a.id=".(int)$this->accessoryID;
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
    
    public function searchAccessory($accessory, $companyID, $pagination = null) {
    	$companyID=mysql_escape_string($companyID);		
		$query = "SELECT * FROM ".TB_ACCESSORY." WHERE company_id = ".$companyID." AND (";		
		
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
    
    public function insertAccessory($companyID) {
    	$companyID=mysql_real_escape_string($companyID);
    	$query = "INSERT INTO ".TB_ACCESSORY." (name, company_id)" .
    			 "VALUES ('".$this->accessoryName."', ".(int)$companyID.")";
    	$this->db->query($query);
    	
    	$query = "SELECT * FROM ".TB_ACCESSORY." a WHERE a.name='".$this->accessoryName."'";
    	$this->db->query($query);
    	 
    	$row = $this->db->fetch_array(0);
    	
    	//	save to trash_bin
		$this->save2trash('C', $row['id']);
    }
    
    public function updateAccessory() {
    	//	save to trash_bin
		$this->save2trash('U', $this->accessoryID);
    	
    	$query = "UPDATE ".TB_ACCESSORY." " .
    			 "SET name='".$this->accessoryName."' " .
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
	
	//	This comment was added at branch "test"
}

?>