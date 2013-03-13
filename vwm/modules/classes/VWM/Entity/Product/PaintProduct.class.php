<?php

namespace VWM\Entity\Product;

/**
 * This is old \Product class
 */
class PaintProduct extends GeneralProduct {

	protected $product_id;	// TODO: rename to id
	protected $voclx;
	protected $vocwx;
	protected $density;
	protected $density_unit_id;
	protected $coating_id;
	protected $paint_chemical; // TODO: remove
	protected $specialty_coating;// TODO: replace all yes/no values to boolean in the DB
	protected $aerosol; // TODO: replace all yes/no values to boolean in the DB
	protected $specific_gravity;
	protected $specific_gravity_unit_id;
	protected $boiling_range_from;
	protected $boiling_range_to;
	protected $flash_point; // TODO: why varchar?
	protected $supplier_id;
	protected $percent_volatile_weight;
	protected $percent_volatile_volume;
	protected $closed; // TODO: replace all yes/no values to boolean in the DB
	protected $discontinued;
	protected $product_instock;
	protected $product_limit;
	protected $product_amount;//TODO: what is product amount? How much we should order?
	protected $product_stocktype;//TODO: what is this?
	protected $product_pricing;
	protected $price_unit_type;


	const TABLE_NAME = 'product';

	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'PaintProduct';
		if($id !== null) {
			$this->setId($id);
			if(!$this->load()) {
				throw new \Exception('404');
			}
		}
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
		return $this->product_id;
	}

	public function setId($id) {
		$this->id = $id;
		$this->product_id = $id;
	}

	public function getVoclx() {
		return $this->voclx;
	}

	public function setVoclx($voclx) {
		$this->voclx = $voclx;
	}

	public function getVocwx() {
		return $this->vocwx;
	}

	public function setVocwx($vocwx) {
		$this->vocwx = $vocwx;
	}

	public function getDensity() {
		return $this->density;
	}

	public function setDensity($density) {
		$this->density = $density;
	}

	public function getDensityUnitId() {
		return $this->density_unit_id;
	}

	public function setDensityUnitId($density_unit_id) {
		$this->density_unit_id = $density_unit_id;
	}

	public function getCoatingId() {
		return $this->coating_id;
	}

	public function setCoatingId($coating_id) {
		$this->coating_id = $coating_id;
	}

	public function getPaintChemical() {
		return $this->paint_chemical;
	}

	public function setPaintChemical($paint_chemical) {
		$this->paint_chemical = $paint_chemical;
	}

	public function getSpecialtyCoating() {
		return $this->specialty_coating;
	}

	public function setSpecialtyCoating($specialty_coating) {
		$this->specialty_coating = $specialty_coating;
	}

	public function getAerosol() {
		return $this->aerosol;
	}

	public function setAerosol($aerosol) {
		$this->aerosol = $aerosol;
	}

	public function getSpecificGravity() {
		return $this->specific_gravity;
	}

	public function setSpecificGravity($specific_gravity) {
		$this->specific_gravity = $specific_gravity;
	}

	public function getSpecificGravityUnitId() {
		return $this->specific_gravity_unit_id;
	}

	public function setSpecificGravityUnitId($specific_gravity_unit_id) {
		$this->specific_gravity_unit_id = $specific_gravity_unit_id;
	}

	public function getBoilingRangeFrom() {
		return $this->boiling_range_from;
	}

	public function setBoilingRangeFrom($boiling_range_from) {
		$this->boiling_range_from = $boiling_range_from;
	}

	public function getBoilingRangeTo() {
		return $this->boiling_range_to;
	}

	public function setBoilingRangeTo($boiling_range_to) {
		$this->boiling_range_to = $boiling_range_to;
	}

	public function getFlashPoint() {
		return $this->flash_point;
	}

	public function setFlashPoint($flash_point) {
		$this->flash_point = $flash_point;
	}

	public function getSupplierId() {
		return $this->supplier_id;
	}

	public function setSupplierId($supplier_id) {
		$this->supplier_id = $supplier_id;
	}

	public function getPercentVolatileWeight() {
		return $this->percent_volatile_weight;
	}

	public function setPercentVolatileWeight($percent_volatile_weight) {
		$this->percent_volatile_weight = $percent_volatile_weight;
	}

	public function getPercentVolatileVolume() {
		return $this->percent_volatile_volume;
	}

	public function setPercentVolatileVolume($percent_volatile_volume) {
		$this->percent_volatile_volume = $percent_volatile_volume;
	}

	public function getClosed() {
		return $this->closed;
	}

	public function setClosed($closed) {
		$this->closed = $closed;
	}

	public function getDiscontinued() {
		return $this->discontinued;
	}

	public function setDiscontinued($discontinued) {
		$this->discontinued = $discontinued;
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

	public function getPriceUnitType() {
		return $this->price_unit_type;
	}

	public function setPriceUnitType($price_unit_type) {
		$this->price_unit_type = $price_unit_type;
	}

	protected function _insert() {

		$sql = "INSERT INTO ". TB_PRODUCT." (name, product_instock, " .
				"product_limit, product_amount, product_stocktype, " .
				"product_pricing, price_unit_type, product_nr, voclx, vocwx, " .
				"density, density_unit_id, coating_id, paint_chemical, " .
				"specialty_coating, aerosol, specific_gravity, " .
				"specific_gravity_unit_id, boiling_range_from, " .
				"boiling_range_to, flash_point, supplier_id, " .
				"percent_volatile_weight, percent_volatile_volume, " .
				"closed, discontinued) VALUES ( "  .
				"'{$this->db->sqltext($this->getName())}', " .
				"{$this->db->sqltext($this->getProductInstock())}, " .
				"{$this->db->sqltext($this->getProductLimit())}, " .
				"{$this->db->sqltext($this->getProductAmount())}, " .
				"{$this->db->sqltext($this->getProductStocktype())}, " .
				"'{$this->db->sqltext($this->getProductPricing())}', " .
				"{$this->db->sqltext($this->getPriceUnitType())}, " .
				"'{$this->db->sqltext($this->getProductNr())}', " .
				"{$this->db->sqltext($this->getVoclx())}, " .
				"{$this->db->sqltext($this->getVocwx())}, " .
				"{$this->db->sqltext($this->getDensity())}, " .
				"{$this->db->sqltext($this->getDensityUnitId())}, " .
				"{$this->db->sqltext($this->getCoatingId())}, " .
				"'{$this->db->sqltext($this->getPaintChemical())}', " .
				"'{$this->db->sqltext($this->getSpecialtyCoating())}', " .
				"'{$this->db->sqltext($this->getAerosol())}', " .
				"{$this->db->sqltext($this->getSpecificGravity())}, " .
				"{$this->db->sqltext($this->getSpecificGravityUnitId())}, " .
				"{$this->db->sqltext($this->getBoilingRangeFrom())}, " .
				"{$this->db->sqltext($this->getBoilingRangeTo())}, " .
				"'{$this->db->sqltext($this->getFlashPoint())}', " .
				"{$this->db->sqltext($this->getSupplierId())}, " .
				"{$this->db->sqltext($this->getPercentVolatileWeight())}, " .
				"{$this->db->sqltext($this->getPercentVolatileVolume())}, " .
				"'{$this->db->sqltext($this->getClosed())}', " .
				"{$this->db->sqltext($this->getDiscontinued())} " .
				")";

		if(!$this->db->exec($sql)) {
			return false;
		}

		$this->setId($this->db->getLastInsertedID());

		return $this->getId();
	}

	protected function _update() {

		$sql = "UPDATE ".TB_PRODUCT." SET " .
				"name = '{$this->db->sqltext($this->getName())}', " .
				"product_instock = {$this->db->sqltext($this->getProductInstock())}, " .
				"product_limit = {$this->db->sqltext($this->getProductLimit())}, " .
				"product_amount = {$this->db->sqltext($this->getProductAmount())}, " .
				"product_stocktype = {$this->db->sqltext($this->getProductStocktype())}, " .
				"product_pricing = '{$this->db->sqltext($this->getProductPricing())}', " .
				"price_unit_type = {$this->db->sqltext($this->getPriceUnitType())}, " .
				"product_nr = '{$this->db->sqltext($this->getProductNr())}', " .
				"voclx = {$this->db->sqltext($this->getVoclx())}, " .
				"vocwx = {$this->db->sqltext($this->getVocwx())}, " .
				"density = {$this->db->sqltext($this->getDensity())}, " .
				"density_unit_id = {$this->db->sqltext($this->getDensityUnitId())}, " .
				"coating_id = {$this->db->sqltext($this->getCoatingId())}, " .
				"paint_chemical = '{$this->db->sqltext($this->getPaintChemical())}', " .
				"specialty_coating = '{$this->db->sqltext($this->getSpecialtyCoating())}', " .
				"aerosol = '{$this->db->sqltext($this->getAerosol())}', " .
				"specific_gravity = {$this->db->sqltext($this->getSpecificGravity())}, " .
				"specific_gravity_unit_id = {$this->db->sqltext($this->getSpecificGravityUnitId())}, " .
				"boiling_range_from = {$this->db->sqltext($this->getBoilingRangeFrom())}, " .
				"boiling_range_to = {$this->db->sqltext($this->getBoilingRangeTo())}, " .
				"flash_point = '{$this->db->sqltext($this->getFlashPoint())}', " .
				"supplier_id = {$this->db->sqltext($this->getSupplierId())}, " .
				"percent_volatile_weight = {$this->db->sqltext($this->getPercentVolatileWeight())}, " .
				"percent_volatile_volume = {$this->db->sqltext($this->getPercentVolatileVolume())}, " .
				"closed = '{$this->db->sqltext($this->getClosed())}', " .
				"discontinued = {$this->db->sqltext($this->getDiscontinued())} " .
				"WHERE product_id = {$this->db->sqltext($this->getId())}";
		if(!$this->db->exec($sql)) {
			return false;
		}

		return $this->getId();
	}

	public function load() {
		if(!$this->getId()) {
            var_dump($this->getId());die();
			throw new \Exception('Paint Product ID should be set before calling this method');
		}

		$sql = "SELECT * FROM ".self::TABLE_NAME." " .
				"WHERE product_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		}

		$row = $this->db->fetch_array(0);
		$this->initByArray($row);

		return true;
	}
    
    /**
     * function for getting product id by product_nr
     * 
     * @param string $productNr
     * 
     * @return boolean|int
     */
    public function getProductIdByProductNr($productNr = null)
    {
        $db = \VOCApp::getInstance()->getService('db');
        if (is_null($productNr)) {
            return false;
        }
        $sql = "SELECT product_id FROM " . self::TABLE_NAME . " " .
                "WHERE product_nr = '{$db->sqltext($productNr)}'";
        $db->query($sql);
       
        if ($db->num_rows() == 0) {
            return false;
        }
        $row = $db->fetch(0);
        return $row->product_id;
    }

}

?>
