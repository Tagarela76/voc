<?php

class Storage {
	
	private $db;
	
	public $storage_id;
	public $facility_id;
	public $name;
	public $capacity_volume;
	public $volume_unittype;
	public $capacity_weight;
	public $weight_unittype;
	public $density;
	public $density_unit_id;
	public $max_period;
	public $suitability; //waste_stream_id
	public $use_date;
	public $active; //0 if removed and 1 - active
	public $delete_date;
	public $document_id;
	public $link;
	
	public $current_usage;
	public $days_left;
	public $density_type;

    function Storage($db, $storage_id = null, $date = null, $mix = null) {
    	$this->db = $db;
    	if (!is_null($storage_id)) {
    		$this->storage_id = $storage_id;
    		$this->_load($date, $mix);
    	}
    }
    
    public function getCurrentList($facilityID, Pagination $pagination = null, $status = 'active' , $sort=' ORDER BY s.name ') {
    	$to = ROW_COUNT; //how many storages per page;

		if ($status != 'active') {
			$query = "SELECT * FROM `".TB_STORAGE."` s WHERE active = 0 AND facility_id = '$facilityID' $sort  ";
		} else {
			$query = "SELECT * , ( " .
					"SELECT m.creation_time " .
					"FROM ".TB_USAGE." m, ".TB_WASTE." w " .
					"WHERE w.storage_id = s.storage_id " .
					"AND m.mix_id = w.mix_id " .
					"AND m.creation_time >= IFNULL(( " .
					"SELECT date " .
					"FROM ".TB_STORAGE_EMPTY." " .
					"WHERE storage_id = s.storage_id " .
					"ORDER BY date DESC " .
					"LIMIT 1 ),0) " .
					"ORDER BY creation_time ASC " .
					"LIMIT 1) AS use_date " .
					"FROM  `".TB_STORAGE."` s " .
					"WHERE s.active =1 " .
					"AND s.facility_id =  '$facilityID'  $sort" ;
					 
		}
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}			
    	$this->db->query($query);
    	if ($this->db->num_rows() == 0) return false;
    	$data = $this->db->fetch_all();
    	$storageList = array();
    	
    	foreach ($data as $record) {
    		$storage = new Storage($this->db);
    		foreach ($record as $property =>$value) {
	    		if (property_exists($storage,$property)) {
		    		$storage->$property = $record->$property;
	    		}
    		}
    		if ($status == 'active') {
    			//take current usage and correct date_use
    			$query = "SELECT sum(w.value) AS summ, w.unittype_id FROM ".TB_WASTE." w, ".TB_USAGE." m WHERE w.storage_id = '$record->storage_id' AND m.mix_id = w.mix_id " .
    					"AND m.department_id IN (SELECT department_id FROM department WHERE facility_id = '$facilityID') " .
    					"AND (m.creation_time BETWEEN DATE_FORMAT('" .$record->use_date. "','%Y-%m-%d') AND DATE_FORMAT('" . date("Y-m-d"). "','%Y-%m-%d')) " .
    					"GROUP BY w.unittype_id ";
    			$this->db->query($query);
    			$usageData = $this->db->fetch_all();
    			$value = 0;
    			$unittypeConverter = new UnitTypeConverter();
    			$unittype = new Unittype($this->db);
//    			$density = $storage->capacity_weight/$storage->capacity_volume;
//    			$densityUnitID = 1;
//		    	$densityType = array (
//			    	'numerator' => $unittype->getDescriptionByID($storage->weight_unittype),
//					'denominator' => $unittype->getDescriptionByID($storage->volume_unittype)
//		    	);
				$densityObj = new Density($this->db,$storage->density_unit_id);
				$densityUnittypeDetails = array (
					'numerator' => $unittype->getUnittypeDetails($densityObj->getNumerator()),
					'denominator' => $unittype->getUnittypeDetails($densityObj->getDenominator())
				);
				$densityType = array (
					'numerator' => $densityUnittypeDetails['numerator']['description'],
					'denominator' => $densityUnittypeDetails['denominator']['description']
				);
				$storage->density_type = $densityUnittypeDetails['numerator']['name'].'/'.$densityUnittypeDetails['denominator']['name'];
		    	$unitTypeDescription = $unittype->getDescriptionByID($storage->volume_unittype);
    			foreach ($usageData as $usageRecord) {
    				$value += $unittypeConverter->convertFromTo($usageRecord->summ,$unittype->getDescriptionByID($usageRecord->unittype_id),$unitTypeDescription,$storage->density,$densityType);
    			}
    			$storage->current_usage = round($value,2);
    		}
    		if (!is_null($storage->use_date)) {
	    		$time = time() - strtotime($storage->use_date);
	    		$storage->days_left = floor($time/ (3600 * 24));
    		} else {
    			$storage->days_left = 0;
    		}
    		$storageList []= $storage;	
    	}
    	return $storageList;
    }
    
    public function compareDateWasteAndDeletedStorage($storageID,$mixDateCreated)
    {    	
    	$query ="SELECT IF ((IFNULL((SELECT date FROM ".TB_STORAGE_DELETED." WHERE storage_id = $storageID),STR_TO_DATE('9999-12-12', '%Y-%m-%d'))<STR_TO_DATE('$mixDateCreated', '%m-%d-%Y')),1,0) as compare"; 
    	$this->db->query($query);
    	$data = $this->db->fetch_all();    	   	
    	return $data[0]->compare;
    }
    
    public function getStorageNameByID($storageID)
    {
    	$query ="SELECT name FROM `".TB_STORAGE."` WHERE storage_id = $storageID LIMIT 1";
    	$this->db->query($query);
    	$data=$this->db->fetch_all();
    	return $data[0]->name;
    } 
    
    public function getCurrentStoragesGroupedByWaste($facilityID, $date = null, $mixID = null) {
    	//$query = "SELECT * FROM ".TB_STORAGE." s WHERE facility_id = '$facilityID' AND active = 1 ";
    	//$query.= "ORDER BY s.suitability";
    	
    	$query ="SELECT s.* FROM `".TB_STORAGE."` s WHERE facility_id = '$facilityID' AND '$date'< IFNULL((SELECT date FROM ".TB_STORAGE_DELETED." sd WHERE sd.storage_id=s.storage_id),9999-12-12) ORDER BY s.suitability";
    	
    	$this->db->query($query);
    	$data = $this->db->fetch_all();
    	
    	$storageList = array();
    	foreach($data as $record) {
    		$storage = new Storage($this->db);
    		foreach ($record as $property =>$value) {
		    	if (property_exists($storage,$property)) {
			    	$storage->$property = $record->$property;
		    	}
	    	} 
	    	$query = "SELECT sum(w.value) AS summ, w.unittype_id FROM ".TB_WASTE." w, ".TB_USAGE." m WHERE w.storage_id = '$record->storage_id' AND m.mix_id = w.mix_id " .
	    		((is_null($mixID))?" ":" AND m.mix_id != '$mixID' "). //if $curMix != null we load storage without waste of that mix in it...
		    	"AND m.department_id IN (SELECT department_id FROM department WHERE facility_id = '$record->facility_id') " .
				"AND (m.creation_time BETWEEN IFNULL((SELECT date FROM `storage_empty` WHERE storage_id = '$record->storage_id' AND date <= '$date' ORDER BY date DESC LIMIT 1),0) " .
				"AND IFNULL((SELECT date FROM `storage_empty` WHERE storage_id = '$record->storage_id' AND date > '$date' ORDER BY date ASC LIMIT 1),'".date("Y-m-d")."')) " .
				"GROUP BY w.unittype_id ";
	    	$this->db->query($query);
	    	$usageData = $this->db->fetch_all();
	    	$value = 0;
	    	$unittypeConverter = new UnitTypeConverter();
	    	$unittype = new Unittype($this->db);
//	    	$density = $storage->capacity_weight/$storage->capacity_volume;
//	    	$densityUnitID = 1;
//	    	$densityType = array (
//			    	'numerator' => $unittype->getDescriptionByID($storage->weight_unittype),
//					'denominator' => $unittype->getDescriptionByID($storage->volume_unittype)
//		    	);
	    	$densityObj = new Density($this->db,$storage->density_unit_id);
	    	$densityUnittypeDetails = array (
		    	'numerator' => $unittype->getUnittypeDetails($densityObj->getNumerator()),
				'denominator' => $unittype->getUnittypeDetails($densityObj->getDenominator())
	    	);
	    	$densityType = array (
		    	'numerator' => $densityUnittypeDetails['numerator']['description'],
				'denominator' => $densityUnittypeDetails['denominator']['description']
	    	);
	    	$storage->density_type = $densityUnittypeDetails['numerator']['name'].'/'.$densityUnittypeDetails['denominator']['name'];
	    	$unitTypeDescription = $unittype->getDescriptionByID($storage->volume_unittype);
	    	foreach ($usageData as $usageRecord) {
		    	$value += $unittypeConverter->convertFromTo($usageRecord->summ,$unittype->getDescriptionByID($usageRecord->unittype_id),$unitTypeDescription,$storafe->density,$densityType);
	    	}
	    	if ($value > $storage->capacity_volume) {
	    		$storage->current_usage = round($storage->capacity_volume,2);
	    	} else {
	    		$storage->current_usage = round($value,2); 
	    	}
	    	
	    	$storage->volume_unittype = $unittype->getNameByID($storage->volume_unittype);
    		$storageList[$record->suitability] []= $storage;
    	}
    	return $storageList;
    } 
    
    public function countStorages($facilityID, $status = 'active') {
	    $query = "SELECT count(storage_id) recordCount FROM `".TB_STORAGE."` WHERE facility_id = '".$facilityID."' AND active = '".(($status == 'active')?1:0)."' ";
	    $this->db->query($query);
	    return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->recordCount : false;	    	
    }
    
    public function save() {
    	if (is_null($this->storage_id)) {
    		$query = "INSERT INTO `".TB_STORAGE."` (facility_id, name, capacity_volume, capacity_weight, max_period, suitability, use_date, volume_unittype, weight_unittype," .
    				" density, density_unit_id".((is_null($this->document_id))?"":", document_id ").") VALUES " .
    				" ('$this->facility_id', '$this->name', '$this->capacity_volume', '$this->capacity_weight', '$this->max_period', '$this->suitability', '', " .
    				" '$this->volume_unittype', '$this->weight_unittype', '$this->density', '$this->density_unit_id'".((is_null($this->document_id))?"":", '$this->document_id' ").")";
    	} else {
    		$query = "UPDATE `".TB_STORAGE."` SET name = '$this->name', capacity_volume = '$this->capacity_volume', capacity_weight = '$this->capacity_weight', " .
    				" max_period = '$this->max_period', suitability = '$this->suitability', volume_unittype = '$this->volume_unittype', " .
    				" weight_unittype = '$this->weight_unittype',  density = '$this->density', density_unit_id = '$this->density_unit_id' ".
    				((is_null($this->document_id))?"":", document_id = '$this->document_id' ").
					"  WHERE storage_id = '$this->storage_id' ";
    	}
    	$this->db->query($query);
    }
    
    public function delete($id = null,  $dateYMD = null) {
    	if ($id != null) {
    		$this->storage_id = $id;
    	}    	
    	$query = "UPDATE `".TB_STORAGE."` SET active = 0 WHERE storage_id = $this->storage_id LIMIT 1";
    	$this->db->query($query);
    	
    	$query ="INSERT INTO ".TB_STORAGE_DELETED." (storage_id,date) VALUES";
    	$query .= " ('$this->storage_id', DATE_FORMAT('" . ((!is_null($dateYMD))?$dateYMD:date("Y-m-d")). "','%Y-%m-%d'))";
    	$this->db->query($query);
    }
    
    public function restore($id = null) {
    	if ($id != null) {
    		$this->storage_id = $id;
    	}
    	    	
    	$query = "UPDATE `".TB_STORAGE."` SET active = 1 WHERE storage_id = $this->storage_id LIMIT 1";
    	$this->db->query($query);
    	
    	$query ="DELETE FROM ".TB_STORAGE_DELETED." WHERE storage_id= $this->storage_id LIMIT 1";    	
    	$this->db->query($query);
    }
    
    public function emptyStorages($idArray, $dateYMD = null) {
    	$query = "INSERT INTO ".TB_STORAGE_EMPTY." (storage_id, date) VALUES " ;
    	foreach ($idArray as $id) {
    		$query .= " ('$id', DATE_FORMAT('" . ((!is_null($dateYMD))?$dateYMD:date("Y-m-d")). "','%Y-%m-%d')),";
    	}
    	$query = substr($query,0,-1);
    	$this->db->query($query);
    }
    
    public function validateName($name, $facilityID, $storage_id = null) {
    	$query = "SELECT * FROM `".TB_STORAGE."` WHERE name = '$name' AND facility_id = '$facilityID' ";
    	if (!is_null($storage_id)) {
			$query .= "AND storage_id != '$storage_id'";
    	}
    	$query .= " LIMIT 1 ";
    	$this->db->query($query);
    	if ($this->db->num_rows() == 0) {
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function validateOverflow($wastes,  $date = null, $mixID = null) {
    	$unittype = new Unittype($this->db);
    	$unittypeConverter = new UnitTypeConverter();
    	$arrayStoragesOverflow = array();
    	$storagesValues = array();
    	
    	$densityObj = new Density($this->db,$storage->density_unit_id);
    	$densityUnittypeDetails = array (
	    	'numerator' => $unittype->getUnittypeDetails($densityObj->getNumerator()),
			'denominator' => $unittype->getUnittypeDetails($densityObj->getDenominator())
    	);
    	$densityType = array (
	    	'numerator' => $densityUnittypeDetails['numerator']['description'],
			'denominator' => $densityUnittypeDetails['denominator']['description']
    	);
    	$storage->density_type = $densityUnittypeDetails['numerator']['name'].'/'.$densityUnittypeDetails['denominator']['name'];
    	
    	
    	
    	foreach ($wastes as $waste) {
    		
    		if ($waste->storageId == -1)
    				continue;
    		
    		$storage = new Storage($this->db,$waste->storageId,$date,$mixID);
    		$unitTypeDescription = $unittype->getDescriptionByID($storage->volume_unittype);
    		
    		if(!$waste->pollutionsDisabled) {
    			
    			
    			
    			foreach($waste->pollutions as $pollution) {
    				
    				
    				
    				
	    			
	    			$val = $unittypeConverter->convertFromTo($pollution->quantity,$unittype->getDescriptionByID($pollution->unittypeId),$unitTypeDescription,$storage->density,$densityType);
	    			//echo "<br/> Value: from: " . $unittype->getDescriptionByID($pollution->unittypeId) . " to: " . $unitTypeDescription . " , quantity: " . $pollution->quantity . " = $val<br/>";
	    			
	    			$storagesValues[$waste->storageId]['value'] += $val;
	    			
	    			
	    		}
    		}
    		else {
    			
    			$val = $unittypeConverter->convertFromTo($waste->quantity,$unittype->getDescriptionByID($waste->unittypeId),$unitTypeDescription,$storage->density,$densityType);
	    		//echo "<br/> Value: from: " . $unittype->getDescriptionByID($waste->unittypeId) . " to: " . $unitTypeDescription . " , quantity: " . $waste->quantity . " = $val<br/>";
	    		$storagesValues[$waste->storageId]['value'] += $val;
	    		
	    		
    		}
    		
    		//echo "<br/> storageValue: {$storagesValues[$waste->storageId]['value']} + storageCurrentUsage: {$storage->current_usage} > {$storage->capacity_volume} ";
    		if($storagesValues[$waste->storageId]['value'] + $storage->current_usage > $storage->capacity_volume  ) {
	    		//echo "<br/> storage " . $waste->storageId . " is overflow<br/>";
	    		$arrayStoragesOverflow[$waste->storageId]['isOverflow'] = true;
	    		//echo " <b>YES</b><br/>";
	    	} else {
	    		
	    		//echo " no<br/>";
	    	}
    	}
    	
    	//echo "curent usage: " . $storage->current_usage . " capacity volume: " . $storage->capacity_volume;
    		

    	return (empty($arrayStoragesOverflow))?false:$arrayStoragesOverflow;
    }
    
    private function _load($date = null,$curMix = null) {
    	if ($this->storage_id === null) {
    		return false;
    	}
    	if (is_null($date)) {
    		$date = date("Y-m-d"); //if $date != null => we load storage in past time...
    	}
    	$query = "SELECT * FROM `".TB_STORAGE."` WHERE storage_id = '$this->storage_id' LIMIT 1";
    	$query = "SELECT * , ( " .
	    	"SELECT m.creation_time FROM ".TB_USAGE." m, ".TB_WASTE." w WHERE w.storage_id = s.storage_id AND m.mix_id = w.mix_id AND m.creation_time >= IFNULL(( " .
			"SELECT date FROM ".TB_STORAGE_EMPTY." WHERE storage_id = s.storage_id ORDER BY date DESC LIMIT 1 ),0) " .
			"ORDER BY creation_time ASC LIMIT 1) AS use_date " .
			"FROM  `".TB_STORAGE."` s " .
			"WHERE  storage_id = '$this->storage_id' LIMIT 1 ";
    	$this->db->query($query);
    	if ($this->db->num_rows() == 0) return false;
    	
    	$record = $this->db->fetch(0);
    	foreach ($record as $property =>$value) {
	    	if (property_exists($this,$property)) {
		    	$this->$property = $record->$property;
	    	}
    	} 
    	$query = "SELECT sum(w.value) AS summ, w.unittype_id FROM ".TB_WASTE." w, ".TB_USAGE." m WHERE w.storage_id = '$record->storage_id' AND m.mix_id = w.mix_id " .
    		((is_null($curMix))?" ":" AND m.mix_id != '$curMix' "). //if $curMix != null we load storage without waste of that mix in it...
	    	"AND m.department_id IN (SELECT department_id FROM department WHERE facility_id = '$record->facility_id') " .
			"AND (m.creation_time BETWEEN IFNULL((SELECT date FROM `storage_empty` WHERE storage_id = '$record->storage_id' AND date <= '$date' ORDER BY date DESC LIMIT 1),0) " .
			"AND IFNULL((SELECT date FROM `storage_empty` WHERE storage_id = '$record->storage_id' AND date > '$date' ORDER BY date ASC LIMIT 1),'".date("Y-m-d")."')) " .
			"GROUP BY w.unittype_id ";
    	$this->db->query($query);
    	$usageData = $this->db->fetch_all();
    	if ($this->active == 0) {
    		$query = "SELECT date FROM ".TB_STORAGE_DELETED." WHERE storage_id = '$this->storage_id' LIMIT 1 ";
    		$this->db->query($query);
    		$this->delete_date = $this->db->fetch(0)->date;
    	}
    	$value = 0;
    	$unittypeConverter = new UnitTypeConverter();
    	$unittype = new Unittype($this->db);
//    	$density = $this->capacity_weight/$this->capacity_volume;
//    	$densityUnitID = 1;
//    	$densityType = array (
//			    	'numerator' => $unittype->getDescriptionByID($this->weight_unittype),
//					'denominator' => $unittype->getDescriptionByID($this->volume_unittype)
//		    	);
    	$densityObj = new Density($this->db,$this->density_unit_id);
    	$densityUnittypeDetails = array (
	    	'numerator' => $unittype->getUnittypeDetails($densityObj->getNumerator()),
			'denominator' => $unittype->getUnittypeDetails($densityObj->getDenominator())
    	);
    	$densityType = array (
	    	'numerator' => $densityUnittypeDetails['numerator']['description'],
			'denominator' => $densityUnittypeDetails['denominator']['description']
    	);
    	$this->density_type = $densityUnittypeDetails['numerator']['name'].'/'.$densityUnittypeDetails['denominator']['name'];
    	
    	$unitTypeDescription = $unittype->getDescriptionByID($this->volume_unittype);
    	foreach ($usageData as $usageRecord) {
	    	$value += $unittypeConverter->convertFromTo($usageRecord->summ,$unittype->getDescriptionByID($usageRecord->unittype_id),$unitTypeDescription,$this->density,$densityType);
    	}
    	
    	$this->current_usage = round($value, 2); 
    	if (!is_null($this->use_date)) {
    		$time = time() - strtotime($this->use_date);
    		$this->days_left = floor($time/ (3600 * 24));  	
    	} else {
    		$this->days_left = 0;
    	}
    }
}
?>