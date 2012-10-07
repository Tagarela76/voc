<?php

namespace VWM\Entity\Product;


/**
 * Represents product in Crib scope 
 */
class CribContext {
	
	protected $current_qty;	
	protected $product_id;
	protected $crib_id;
	
	protected $binContext;		
	
	public function getCurrentQty() {
		return $this->current_qty;
	}
	
	public function getProductId() {
		return $this->product_id;
	}
	
	public function getCribId() {
		return $this->crib_id;
	}	
}

?>
