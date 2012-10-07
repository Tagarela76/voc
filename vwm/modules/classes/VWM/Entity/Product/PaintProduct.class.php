<?php

namespace VWM\Entity\Product;

/**
 * This is old \Product class 
 */
class PaintProduct extends GeneralProduct {
	
	protected $product_id;	// TODO: rename to id
	protected $product_nr;
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
	


	const TABLE_NAME = 'product';

	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'PaintProduct';
		//TODO:
	}
	
	public function getId() {
		return $this->product_id;
	}
	
	public function setId($id) {
		$this->id = $id;
		$this->product_id = $id;
	}

	public function getProductNr() {
		return $this->product_nr;
	}

	public function setProductNr($product_nr) {
		$this->product_nr = $product_nr;
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

	protected function _insert() {
		
	}
	
	protected function _update() {
		
	}

}

?>
