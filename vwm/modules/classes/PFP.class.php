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

	public function getProducts() {
		$products = array();
		if (isset($this->products)) {
			foreach ($this->products as $item) {
				$product['product_nr'] = $item->product_nr;
				$product['name'] = $item->name;
				$products[] = $product;
			}
		}

		return $products;
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
		$res = array();
		$j = 1;
		$count = $this->getProductsCount();
		for($i=0; $i<$count; $i++) {
			if($this->products[$i]->isPrimary()){
				$res[0] = "<b>".$this->products[$i]->getRatio()."</b>";
			} else {
				$res[$j] = $this->products[$i]->getRatio();
				$j++;
			}
		}
		ksort($res);
		
		return implode(':', $res);
	}

	public function setDescription($val) {
		$this->description = $val;
	}

	public function save() {

	}

	public function toJson() {
		$arr = array("descrtiption"=>$this->description, "id" => $this->id, "ratio" => $this->getRatio(), "products" => $this->products);
		return json_encode($arr);
	}
}
?>