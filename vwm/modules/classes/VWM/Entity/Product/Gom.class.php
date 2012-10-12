<?php

namespace VWM\Entity\Product;

class Gom extends GeneralProduct {
	
	/**
	 *
	 * @var int
	 */
	protected $msds_sheet;
	
	/**
	 *
	 * @var int
	 */
	protected $add_toxic_compounds;
	
	/**
	 *
	 * @var string
	 */
	protected $package_size;


	/**
	 *
	 * @var int
	 */
	protected $supplier_id;
	
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
		return $this->supplier_id;
	}
	
	public function setSupplierId($supplierId) {
		$this->supplier_id = $supplierId;
	}
	
	public function getMsdsHheet() {
		return $this->msds_sheet;
	}

	public function setMsdsHheet($msdsSheet) {
		$this->msds_sheet = $msdsSheet;
	}

	public function getAddToxicCompounds() {
		return $this->add_toxic_compounds;
	}

	public function setAddToxicCompounds($addToxicCompounds) {
		$this->add_toxic_compounds = $addToxicCompounds;
	}
	
	public function getPackageSize() {
		return $this->package_size;
	}

	public function setPackageSize($packageSize) {
		$this->package_size = $packageSize;
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
		
		$sql = "INSERT INTO ". TB_GOM." (name, product_nr, " .
				"supplier_id, product_pricing, " .
				"add_toxic_compounds, msds_sheet, " .
				"last_update_time, package_size) VALUES ( "  .
				"'{$this->db->sqltext($this->getName())}', " .
				"'{$this->db->sqltext($this->getProductNr())}', " .		
				"{$this->db->sqltext($this->getSupplierId())}, " .		
				"{$this->db->sqltext($this->getProductPricing())}, " .
				"{$this->db->sqltext($this->getAddToxicCompounds())}, " .
				"{$this->db->sqltext($this->getMsdsHheet())}, " .
				"'{$this->db->sqltext($this->getLastUpdateTime())}', " .
				"'{$this->db->sqltext($this->getPackageSize())}' " .		
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
				"supplier_id = {$this->db->sqltext($this->getSupplierId())}, " .
				"product_pricing = {$this->db->sqltext($this->getProductPricing())}, " .
				"add_toxic_compounds = {$this->db->sqltext($this->getAddToxicCompounds())}, " .
				"msds_sheet = {$this->db->sqltext($this->getMsdsHheet())}, " .		
				"last_update_time = '{$this->db->sqltext($this->getLastUpdateTime())}', " .		
				"package_size = '{$this->db->sqltext($this->getPackageSize())}' " .			
				"WHERE id = {$this->db->sqltext($this->getId())}";
		if(!$this->db->exec($sql)) {
			return false;
		}				
		
		return $this->getId();
	}
	
	/**
	 * Method that check if exist gom with this product nr
	 */
	public function check() {

		$sql = "SELECT * FROM ".TB_GOM." " .
				"WHERE product_nr = '{$this->db->sqltext($this->getProductNr())}'";
		$this->db->query($sql);
		if($this->db->num_rows() != 0) {
			$row = $this->db->fetch_array(0);
			$this->setId($row["id"]);
		}
	}
}

?>
