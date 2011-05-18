<?php
/**
 * PFP - pre formulated product
 */
class PFP
{
	private $db;
	private $id;
	private $description;
	
	public $products;
	
	function PFP($PFPProductsArray) {
		$this->products = $PFPProductsArray;
		
	}
	
	public function getProductsCount() {
		return isset($this->products) ? count($this->products) : "0";
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setID($value){
		$this->id = $value;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getRatio() {
		
		$count = $this->getProductsCount();
		for($i=0; $i<$count; $i++) {
			$res .= $this->products[$i]->getRatio();
			if($i < $count -1) {
				$res .= ":";
			}
		}
		return $res;
	}
	
	public function setDescription($val) {
		$this->description = $val;
	}
	
	public function save() {
		
	}
	
	public function toJson() {
		
	}
}
?>