<?php
/**
 * PreFormulateProducts Product (Extend MixProduct + ratio)
 */ 

class PFPProduct extends MixProduct
{
	
	
	private $ratio;
	
	function PFP($db) {
		$this->db = $db;
	}
	
	public function getRatio() {
		return $this->ratio;
	}
	
	public function setRatio($value) {
		$this->ratio = $value;
	}
	
	public function save() {
		
	}
}
?>