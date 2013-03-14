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
	protected $product_pricing;

	
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

	public function getProductPricing() {
		return $this->product_pricing;
	}

	public function setProductPricing($product_pricing) {
		$this->product_pricing = $product_pricing;
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
    
    /**
     * getting product id by name
     * 
     * @param string $productName
     * 
     * @return int
     */
    public function getProductIdByName($productName) {

		$productName = mysql_escape_string($productName);

        $query = "SELECT * FROM " . TB_PRODUCT . " where product_nr='" . $productName . "'";
		$this->db->query($query);
		$data = $this->db->fetch(0);
		$productId = $data->product_id;
        
        $this->setId($productId);
		return $productId;
	}


}

?>
