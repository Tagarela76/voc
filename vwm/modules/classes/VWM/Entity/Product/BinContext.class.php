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

	public function getCurrentQty() {
		return $this->current_qty;
	}

	public function getProductId() {
		return $this->product_id;
	}

	public function getBinId() {
		return $this->bin_id;
	}

		
	public function increaseQty($byQty = 1) {
		$this->current_qty = $this->current_qty + $byQty;
	}
	
	public function decreaseQty($byQty = 1) {
		$this->current_qty = $this->current_qty - $byQty;		
	}
	
	/**
	 * Loads object from database
	 * @param int $productId
	 * @param int $binId
	 * @return bool true on success, false on failure
	 */
	public function load($productId, $binId) {
		$sql = "SELECT * FROM ".BinContext::TABLE_NAME." " .
				"WHERE product_id = {$productId} " .
				"AND bin_id = {$binId}";
		$this->db->query($sql);			
		if($this->db->num_rows() == 0) {
			return false;
		}
				
		$row = $this->db->fetch_array(0);
		$this->initByArray($row);
		return true;
	}
}

?>
