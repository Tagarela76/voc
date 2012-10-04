<?php

use VWM\Framework\Model;

class IndustryType extends Model {

	/**
	 *
	 * @var int 
	 */
	public $id;
	
	/**
	 *
	 * @var string 
	 */
	public $type;
	
	/**
	 *
	 * @var int 
	 */
	public $parent = NULL;
	
	/**
	 *
	 * @var string 
	 */
	public $url;
	
	/**
	 *
	 * @var array
	 */
	private $subIndustryTypes = array();

	function __construct(db $db, $id = NULL) {
		
		$this->db = $db;
		$this->modelName = 'industryType';

		if (isset($id)) {
			$this->id = $id;
			$this->_load();
		}
		$this->loadSubIndustryTypes();
	}
	
	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM ".TB_INDUSTRY_TYPE." ".
				"WHERE id={$this->db->sqltext($this->id)} " . 
				"LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch(0);

		foreach ($rows as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * Add or Update industry type
	 * @return int
	 */
	public function save() {

		if (!isset($this->id)) {
			$id = $this->add();
		} else {
			$id = $this->update();
		}
		return $id;
	}
	
	/**
	 * add new industry type
	 * if parent is not null we add sub industry type
	 * @return int
	 */
	public function add() {

		$parent = (isset($this->parent)) ? $this->parent : "NULL";
		$query = "INSERT INTO " . TB_INDUSTRY_TYPE . " (type, parent) VALUES ( " .
				"'{$this->db->sqltext($this->type)}', " .
				"{$this->db->sqltext($parent)}" . 	
				")";

		$this->db->query($query); 
		$id = $this->db->getLastInsertedID();
		$this->id = $id;
		return $id;
	}
	
	/**
	 * update industry type
	 * @return int
	 */
	public function update() {

		$parent = (isset($this->parent)) ? $this->parent : "NULL";
		$query = "UPDATE " . TB_INDUSTRY_TYPE .
				 " set type='{$this->db->sqltext($this->type)}'," .
				 " parent={$this->db->sqltext($parent)}" .	
				 " WHERE id= " . $this->db->sqltext($this->id);
		$this->db->query($query);

		return $this->id;
	}
	
	/**
	 *  Delete industry type
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_INDUSTRY_TYPE . 
			   " WHERE id=" . $this->db->sqltext($this->id);
		$this->db->query($sql);
	}
	
	/**
	 * get products id
	 * @return array 
	 */
	public function getProducts() {
		$query = "SELECT product_id " . 
				 " FROM " . TB_PRODUCT2INDUSTRY_TYPE . 
				 " WHERE industry_type_id={$this->db->sqltext($this->id)} " .
				 " ORDER BY product_id ASC";
		$this->db->query($query);
		$products = $this->db->fetch_all_array();
		return $products;
	}
	
	// TODO : I must change this function but i don't know how - ask Denis
	public function getTypesWithSubTypes() { 
		$query = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE parent is NULL";
		$this->db->query($query);
		$types = $this->db->fetch_all();
		$i = 0;
		foreach ($types as $item){
			$query = "SELECT * FROM ".TB_INDUSTRY_TYPE." WHERE parent = ".$item->id;
			$this->db->query($query);
			$subTypes = $this->db->fetch_all();
			$result[$item->type]['id'] = $item->id;
			foreach ($subTypes as $subitem){
				$result[$item->type]['subTypes'][$subitem->id] = $subitem->type;
			}
		}
		
		return $result;
	}
	
	
	/**
	 * 
	 * @return array
	 */
	public function getCompanies() {
		$query = "SELECT * " .
				"FROM " . TB_COMPANY . " c " .
				"LEFT JOIN " . TB_COMPANY2INDUSTRY_TYPE . " c2it ON(c2it.company_id=c.company_id) " .
				"WHERE c2it.industry_type_id={$this->db->sqltext($this->id)}";
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();
		return $rows;
	}
	
	/**
	 * add company to industry type
	 * @param type int
	 */
	public function setCompanyToIndustryType($companyId) {
		
		$query = "INSERT INTO " . TB_COMPANY2INDUSTRY_TYPE . " (company_id, industry_type_id) VALUES ( " .
				"{$this->db->sqltext($companyId)}, " .
				"{$this->db->sqltext($this->id)}" . 	
				")";

		$this->db->query($query);
	}
	
	/**
	 * delete all dependences
	 */
	public function unSetCompanyFromIndustryType() {
		$sql = "DELETE FROM " . TB_COMPANY2INDUSTRY_TYPE .
			   " WHERE industry_type_id={$this->db->sqltext($this->id)}";
		$this->db->query($sql);
	}
	
	public function setProductToIndustryType($productId) {
		
		$query = "INSERT INTO " . TB_PRODUCT2INDUSTRY_TYPE . " (product_id, industry_type_id) VALUES ( " .
				"{$this->db->sqltext($productId)}, " .
				"{$this->db->sqltext($this->id)}" . 	
				")";

		$this->db->query($query);
	}
	
	/**
	 * load sub types
	 * @return boolean
	 */
	private function loadSubIndustryTypes(){
		$query = "SELECT * " .
				 " FROM " . TB_INDUSTRY_TYPE . 
				 " WHERE parent = {$this->db->sqltext($this->id)}";
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$subIndustryTypes = array();
		foreach ($rows as $row) {
			$subIndustryType = new IndustryType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($subIndustryType, $key)) {
					$subIndustryType->$key = $value;
				}
			}
			$subIndustryTypes[] = $subIndustryType;
		}
		$this->subIndustryTypes = $subIndustryTypes;
	}

	public function getSubIndustryTypes() {
		return $this->subIndustryTypes;		
	}
}

?>