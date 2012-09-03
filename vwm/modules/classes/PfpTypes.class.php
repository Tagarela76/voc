<?php

class PfpTypes {

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
	 *
	 * @var int 
	 */
	public $facility_id;
    
	/**
	 * db connection
	 * @var db 
	 */
	private $db;


    const PFP_TYPES_LIMIT = 10;
    
	function __construct(db $db, $pfpTypeId = null) {
		$this->db = $db;

		if (isset($pfpTypeId)) {
			$this->id = $pfpTypeId;
			$this->_load();
		}
	}

	/**
	 * add pfp type
	 * @return int 
	 */
	public function save() {

        // Is unique ?
        $query = "SELECT * FROM " . TB_PFP_TYPES .
				 " WHERE name ='{$this->db->sqltext($this->name)}'
                  AND facility_id = {$this->db->sqltext($this->facility_id)}";
		$this->db->query($query);
        if ($this->db->num_rows() > 0) {
			return false;
		}
        // we should limit pfp type's count (10 types)
        $facility = new Facility($this->db);
        if ($facility->getPfpTypesCount($this->db->sqltext($this->facility_id)) > self::PFP_TYPES_LIMIT) {
            return false;
        }
		$query = "INSERT INTO " . TB_PFP_TYPES . "(name, facility_id) 
				VALUES ( 
				'" . $this->db->sqltext($this->name) . "'
                , " . $this->db->sqltext($this->facility_id) . "
				)";
		$this->db->query($query); 
		$pfpTypeId = $this->db->getLastInsertedID();
		$this->id = $pfpTypeId;
		return $this->id;
	}

	/**
	 *
	 * delete pfp type
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_PFP_TYPES . "
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

	private function _load() { 

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM " . TB_PFP_TYPES . "
				 WHERE id=" . $this->db->sqltext($this->id);
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
	
		
	public function getPfpProducts(Pagination $pagination = null) {
		
		$query = "SELECT * FROM " . TB_PFP .
				 " WHERE type_id ={$this->db->sqltext($this->id)}";
                 
        if (isset($pagination)) {
			$query .= " ORDER BY description LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}    
        
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$pfpProducts = array();
		$pfpManager = new PFPManager($this->db);
		foreach ($rows as $row) {
			$pfp = $pfpManager->getPfp($row["id"]);
			$pfpProducts[] = $pfp;
		}
		return $pfpProducts;
		
	}

}

?>