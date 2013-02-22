<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;

class PfpProduct extends Model {

	protected $id;
	protected $ratio;
	protected $ratio_to;
	protected $ratio_from_original;
	protected $ratio_to_original;
	protected $product_id;
	protected $preformulated_products_id;
	protected $isPrimary;

	public function __construct() {
		;
	}

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }
    
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getRatio() {
		return $this->ratio;
	}

	public function setRatio($ratio) {
		$this->ratio = $ratio;
	}

	public function getRatioTo() {
		return $this->ratio_to;
	}

	public function setRatioTo($ratio_to) {
		$this->ratio_to = $ratio_to;
	}

	public function getRatioFromOriginal() {
		return $this->ratio_from_original;
	}

	public function setRatioFromOriginal($ratio_from_original) {
		$this->ratio_from_original = $ratio_from_original;
	}

	public function getRatioToOriginal() {
		return $this->ratio_to_original;
	}

	public function setRatioToOriginal($ratio_to_original) {
		$this->ratio_to_original = $ratio_to_original;
	}

	public function getProductId() {
		return $this->product_id;
	}

	public function setProductId($product_id) {
		$this->product_id = $product_id;
	}

	public function getPreformulatedProductsId() {
		return $this->preformulated_products_id;
	}

	public function setPreformulatedProductsId($preformulated_products_id) {
		$this->preformulated_products_id = $preformulated_products_id;
	}

	public function getIsPrimary() {
		return $this->isPrimary;
	}

	public function setIsPrimary($isPrimary) {
		$this->isPrimary = $isPrimary;
	}


}

?>
