<?php
/**
 * PreFormulatedProducts Product (Extends MixProduct + ratio)
 */

class PFPProduct extends MixProduct
{

	private $isPrimary;
	private $isRange;
	private $range_ratio;
	private $ratio;
	private $id;

	public function __construct(\db $db, $id = null) {
		$this->db = $db;

		if(isset($id)) {
			$this->id = $id;
			$this->_load();
		}
	}

	public function setId($value) {
		$this->id = $value;
	}

	public function getId() {
		return $this->id;
	}

	private function _load() {
		$id = mysql_escape_string($this->getId());
		if(!$id) {
			throw new Exception("You should set id first");
		}
		$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE id = $id LIMIT 1";
		$this->db->query($getProductsQuery);
		$arr = $this->db->fetch_array(0);

		if (!empty($arr['ratio_from_original']) && !empty($arr['ratio_to_original'])) {
			$this->isRange = true;
			$this->range_ratio = trim($arr['ratio_from_original']).'-'.trim($arr['ratio_to_original']);
		} else {
			$this->isRange = false;
		}

		$this->ratio = $arr['ratio'];
		$this->setIsPrimary($arr['isPrimary']);

		$this->initializeByID($arr['product_id']);
	}

	public function getRatio() {
		return $this->ratio;
	}

	public function getRangeRatio() {
		return $this->range_ratio;
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

	public function isRange() {
		return $this->isRange;
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

	public function setIsRange($isRange) {
		$this->isRange = $isRange;
	}

	public function setRangeRatio($range_ratio) {
		$this->range_ratio = $range_ratio;
	}
}
?>