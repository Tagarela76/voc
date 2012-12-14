<?php

class ProductLibraryType {

	/**
	 *
	 * @var int 
	 */
	public $id;

	/**
	 *
	 * @var string 
	 */
	public $name;

	/**
	 * db connection
	 * @var db 
	 */
	private $db;

	const PAINT_SHOP_PRODUCTS = "Paint Shop Products";
	const BODY_SHOP_PRODUCTS = "Body Shop Products";
	const DETAILING_SHOP_PRODUCTS = "Detailing Products";
    const POWDER_COATING = "POWDER COATING";

	function __construct(db $db, $productLibraryTypeId = null) {
		$this->db = $db;

		if (isset($productLibraryTypeId)) {
			$this->id = $productLibraryTypeId;
			$this->_load();
		}
	}

	/**
	 * Insert/update product library type
	 * @return int 
	 */
	public function save() {

		if (!isset($this->id)) {
			$productLibraryTypeId = $this->addProductLibraryType();
		} else {
			$productLibraryTypeId = $this->updateProductLibraryType();
		}
		return $productLibraryTypeId;
	}

	/**
	 * insert new product library type
	 * @return int 
	 */
	public function addProductLibraryType() {

		$query = "INSERT INTO " . TB_PRODUCT_LIBRARY_TYPE . "(name) 
				VALUES ( 
				'" . $this->db->sqltext($this->name) . "'
				)";
		$this->db->query($query);
		$productLibraryTypeId = $this->db->getLastInsertedID();
		$this->id = $productLibraryTypeId;
		return $productLibraryTypeId;
	}

	/**
	 * update product library type
	 * @return int 
	 */
	public function updateProductLibraryType() {

		$query = "UPDATE " . TB_PRODUCT_LIBRARY_TYPE . "
					set name='" . $this->db->sqltext($this->name) . "'	
					WHERE id= " . $this->db->sqltext($this->id);
		$this->db->query($query);

		return $this->id;
	}

	/**
	 *
	 * delete an product library type
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_PRODUCT_LIBRARY_TYPE . "
				 WHERE id=" . $this->db->sqltext($this->id);
		$this->db->query($sql);
	}

	/**
	 *
	 * Overvrite get property if property is not exists or private.
	 * @param string $name - property name. method call method get_%property_name%, if method does not exists - return property value;
	 */
	public function __get($name) {

		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else {
			return $this->$name;
		}
	}

	/**
	 * Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	 * @param string $name - name of property
	 * @param mixed $value - value to set
	 */
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/*
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
		 * it will craete dynamic property, like usually does PHP
		 */ else if (!property_exists($this, $name)) {
			$this->$name = $value;
		}
		/*
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}

	/**
	 * load one product library type
	 * @return boolean 
	 */
	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM " . TB_PRODUCT_LIBRARY_TYPE . "
				 WHERE id='" . $this->db->sqltext($this->id) .
				"' LIMIT 1";
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
	 * get all product library types
	 * @return boolean|\ProductLibraryType 
	 */
	public function getProductLibraryTypes() {

		$query = "SELECT * FROM " . TB_PRODUCT_LIBRARY_TYPE;
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$productLibraryTypes = array();
		foreach ($rows as $row) {
			$productLibraryType = new ProductLibraryType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($productLibraryType, $key)) {
					$productLibraryType->$key = $value;
				}
			}
			$productLibraryTypes[] = $productLibraryType;
		}
		return $productLibraryTypes;
	}

	public function mapping($libraryType) {

		switch (trim($libraryType)) {
			case "paintShop" :
				$productLibraryType = "1";
				break;

			case "bodyShop":
				$productLibraryType = "2";
				break;

			case "detailingShop":
				$productLibraryType = "3";
				break;

			case "fuelAndOils":
				$productLibraryType = "4";
				break;
            
            case "powderCoating":
                $productLibraryType = "5";
		}
		return $productLibraryType;
	}

}

?>