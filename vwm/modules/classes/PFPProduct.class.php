<?php
/**
 * PreFormulatedProducts Product (Extends MixProduct + ratio)
 */ 

class PFPProduct extends MixProduct
{
	
	private $isPrimary;
	private $ratio;
	private $id;
	
	function PFPProduct($db,$id=null) {
		$this->db = $db;
		
		if(isset($id)) {
			$this->id = $id;
			$this->_load($id);
		}
	}
	
	public function setId($value) {
		$this->id = $value;
	}
	
	public function getId($value) {
		return $this->id;
	}
	
	private function _load($id) {
		$id = mysql_escape_string($id);
		$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE id = $id LIMIT 1";
		$this->db->query($getProductsQuery);
		$arr = $this->db->fetch_array(0);
		
		
		$this->ratio = $arr['ratio'];
		$this->setIsPrimary($arr['isPrimary']);
		
		$this->initializeByID($arr['product_id']);
	}
	
	public function getRatio() {
		return $this->ratio;
	}
	
	public function setRatio($value) {
		$this->ratio = $value;
	}
	
	public function setIsPrimary($value) {
		if($value == "1") {
			$value = true;
		} else if($value == "0") {
			$value = false;
		}
		$this->isPrimary = $value;
	}
	
	public function isPrimary() {
		return $this->isPrimary;
	}
	
	public function toJson() {
		$res = array("ratio" => $this->ratio, "isPrimary" => $this->isPrimary, "product" => $this);
		$enc = json_encode($this);
		$tmp = json_decode($enc);
		$tmp->ratio = $this->ratio;
		$tmp->isPrimary = $this->isPrimary;
		$tmp->id = $this->id;
		$res = json_encode($tmp);
		return $res;
	}
}
?>