<?php

namespace VWM\Entity\Product;

use VWM\Framework\Model;

/**
 * Abstract generalization for all products
 */
abstract class GeneralProduct extends Model {
	
	protected $id;
	protected $name;
	protected $product_nr;
	protected $product_instock;
	protected $product_limit;
	protected $product_amount;//TODO: what is product amount? How much we should order?
	protected $product_stocktype;//TODO: what is this?
	protected $product_pricing;
	protected $price_unit_type;
	
	/**	 
	 * @var \VWM\Entity\Product\FacilityContext
	 */
	protected $facilityContext;
	
	/**	 
	 * @var \VWM\Entity\Product\CribContext
	 */
	protected $cribContext;
	
	/**
	 *
	 * @var array key => value, where key is bin_id
	 */
	protected $binContext = array();

	public function getId() {	
		return $this->id;
	}
	
	public function setId($id) {		
		$this->id = $id;
	}
	
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}
	
	public function getProductNr() {
		return $this->product_nr;
	}

	public function setProductNr($product_nr) {
		$this->product_nr = $product_nr;
	}

	public function getProductInstock() {
		return $this->product_instock;
	}

	public function setProductInstock($product_instock) {
		$this->product_instock = $product_instock;
	}

	public function getProductLimit() {
		return $this->product_limit;
	}

	public function setProductLimit($product_limit) {
		$this->product_limit = $product_limit;
	}

	public function getProductAmount() {
		return $this->product_amount;
	}

	public function setProductAmount($product_amount) {
		$this->product_amount = $product_amount;
	}

	public function getProductStocktype() {
		return $this->product_stocktype;
	}

	public function setProductStocktype($product_stocktype) {
		$this->product_stocktype = $product_stocktype;
	}

	public function getProductPricing() {
		return $this->product_pricing;
	}

	public function setProductPricing($product_pricing) {
		$this->product_pricing = $product_pricing;
	}

	public function getPriceUnitType() {
		return $this->price_unit_type;
	}

	public function setPriceUnitType($price_unit_type) {
		$this->price_unit_type = $price_unit_type;
	}

	/**	 
	 * @return VWM\Entity\Product\FacilityContext
	 * TODO: finish me
	 */
	public function getFacilityContext($facilityId) {		
		return $this->facilityContext;
	}	
	
	/**	 
	 * @return VWM\Entity\Product\CribContext
	 * TODO: finish me
	 */
	public function getCribContext($cribId) {
		return $this->cribContext;
	}

	/**	 
	 * @return VWM\Entity\Product\BinContext|boolean
	 */
	public function getBinContext($binId) {
		if(!$this->binContext[$binId]) {
			$binContext = new BinContext($this->db);
			if(!$binContext->load($this->getId(), $binId)) {
				return false;
			}
		
			$this->binContext[$binId] = $binContext;	
		}		
		
		return $this->binContext[$binId];
	}


}

?>
