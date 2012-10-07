<?php

namespace VWM\Entity\Product;

use VWM\Framework\Model;
use VWM\GeneralInterfaces\ITrackable;

/**
 * Represents product in Bin scope 
 * Product Invetory tracks here
 */
class BinContext extends Model implements ITrackable {
	
	protected $current_qty;	
	protected $product_id;
	protected $bin_id;	
	
	const TABLE_NAME = 'product2bin';
	
	public function __construct(\db $db) {
		$this->db = $db;		
	}

	public function increaseQty($byQty = 1) {
		$this->current_qty = $this->current_qty + $byQty;
	}
	
	public function decreaseQty($byQty = 1) {
		$this->current_qty = $this->current_qty - $byQty;		
	}
}

?>
