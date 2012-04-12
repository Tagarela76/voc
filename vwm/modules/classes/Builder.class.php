<?php

class Builder {

	private $db;

    function Builder($db) {
    	$this->db = $db;
    }
    
    public function buildMixRecord($mixRecordID) {
    	//$this->db->select_db(DB_NAME);
    	$query = "SELECT * FROM ".TB_MIXGROUP." WHERE mixgroup_id=".$mixRecordID;
    	$this->db->query($query);
    	
    	if ($this->db->num_rows() > 0) {
    		$mixRecordData = $this->db->fetch_array(0);
    		
    		//	Initialize product
    		$product = new Product($this->db);
    		$product->initializeByID($mixRecordData['product_id']);
    		
    		//	Initialize properties
    		$recordProperties = new RecordProperties();
    		$recordProperties->setQuantity($mixRecordData['quantity']);
    		$recordProperties->setUnitType($mixRecordData['unit_type']);
    		
    		//	Initialize MixRecord
    		$mixRecord = new MixRecord($product, $recordProperties);
    		
    		return $mixRecord;
    	} else {
//    		echo "FALSE!!!!!<br>";
    		return false;
    	}
    }
}
?>