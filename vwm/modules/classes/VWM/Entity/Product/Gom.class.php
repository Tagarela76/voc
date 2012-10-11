<?php

namespace VWM\Entity\Product;

class Gom extends GeneralProduct {
	
	protected $supplier_id;
	protected $code;
	
	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'Gom';
		if($id !== null) {
			$this->setId($id);
			if(!$this->_load()) {
				throw new \Exception('404');
			}
		}
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getSupplierId() {
		return $this->vendor_id;
	}
	
	public function setSupplierId($vendorId) {
		$this->vendor_id = $vendorId;
	}
	
	public function getJobberId() {
		return $this->jobber_id;
	}
	
	public function setJobberId($jobberId) {
		$this->jobber_id = $jobberId;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	
	protected function _load() {
		if(!$this->getId()) {
			throw new \Exception('GOM ID should be set before calling this method');
		}
		
		$sql = "SELECT * FROM ".TB_GOM." " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		$row = $this->db->fetch_array(0);
		$this->initByArray($row);
		
		return true;
	}
	
		protected function _insert() {
		
		$sql = "INSERT INTO ". TB_GOM." (name, product_nr, jobber_id, " .
				"supplier_id, code, product_instock, " .
				"product_limit, product_amount, product_stocktype, " .
				"product_pricing, price_unit_type) VALUES ( "  .
				"'{$this->db->sqltext($this->getName())}', " .
				"'{$this->db->sqltext($this->getProductNr())}', " .		
				"{$this->db->sqltext($this->getJobberId())}, " .
				"{$this->db->sqltext($this->getSupplierId())}, " .
				"'{$this->db->sqltext($this->getCode())}', " .
				"{$this->db->sqltext($this->getProductInstock())}, " .
				"{$this->db->sqltext($this->getProductLimit())}, " .
				"{$this->db->sqltext($this->getProductAmount())}, " .
				"{$this->db->sqltext($this->getProductStocktype())}, " .		
				"{$this->db->sqltext($this->getProductPricing())}, " .
				"{$this->db->sqltext($this->getPriceUnitType())} " .
				")";
			
		if(!$this->db->exec($sql)) {
			return false;
		}
		
		$this->setId($this->db->getLastInsertedID());
		
		return $this->getId();
	}
	
	protected function _update() {
		
		$sql = "UPDATE ". TB_GOM ." SET " .
				"name = '{$this->db->sqltext($this->getName())}', " .
				"product_nr = '{$this->db->sqltext($this->getProductNr())}', " .		
				"jobber_id = {$this->db->sqltext($this->getJobberId())}, " .
				"supplier_id = {$this->db->sqltext($this->getSupplierId())}, " .
				"code = '{$this->db->sqltext($this->getCode())}', " .		
				"product_instock = {$this->db->sqltext($this->getProductInstock())}, " .
				"product_limit = {$this->db->sqltext($this->getProductLimit())}, " .
				"product_amount = {$this->db->sqltext($this->getProductAmount())}, " .
				"product_stocktype = {$this->db->sqltext($this->getProductStocktype())}, " .
				"product_pricing = {$this->db->sqltext($this->getProductPricing())}, " .
				"price_unit_type = {$this->db->sqltext($this->getPriceUnitType())} " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		if(!$this->db->exec($sql)) {
			return false;
		}				
		
		return $this->getId();
	}
}

?>
