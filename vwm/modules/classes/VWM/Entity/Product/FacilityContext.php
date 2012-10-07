<?php

namespace VWM\Entity\Product;


/**
 * Represents product in facility scope 
 */
class FacilityContext {
	
	protected $current_qty;	
	protected $product_id;
	protected $facility_id;
	
	protected $binContext;	
	protected $cribContext;
	
	public function getCurrentQty() {
		return $this->current_qty;
	}
	
	public function getProductId() {
		return $this->product_id;
	}
	
	public function getFacilityId() {
		return $this->facility_id;
	}	
}

?>
