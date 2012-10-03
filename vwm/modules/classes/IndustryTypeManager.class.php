<?php

use VWM\Framework\Model;

class IndustryTypeManager extends Model {

	function __construct(db $db) {
		
		$this->db = $db;
		$this->modelName = 'industryTypeManager';
	}
	
	/**
	 * get all industry types
	 * @param Pagination $pagination
	 * @return boolean|\IndustryType
	 */
	public function getIndustryTypes(Pagination $pagination = NULL) {

		$industryTypes = array();
		$sql = "SELECT * ".
				"FROM " . TB_INDUSTRY_TYPE . 
				" WHERE parent IS NULL";
		if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}  
		$this->db->query($sql);
		
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch_all_array();

		foreach ($rows as $row) {
			$industryType = new IndustryType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($industryType, $key)) {
					$industryType->$key = $value;
				}
			}
			$industryTypes[] = $industryType;
		}
		return $industryTypes;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function getIndustryTypesCount() {

		$sql = "SELECT * ".
				"FROM " . TB_INDUSTRY_TYPE . 
				" WHERE parent IS NULL";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->num_rows();
		}
	}
	
	/**
	 * get all sub industry types
	 * @param Pagination $pagination
	 * @return boolean|\IndustryType
	 */
	public function getSubIndustryTypes(Pagination $pagination = NULL) {

		$industryTypes = array();
		$sql = "SELECT * ".
				"FROM " . TB_INDUSTRY_TYPE . 
				" WHERE parent IS NOT NULL
				 ORDER BY parent";
		if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}  
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch_all_array();

		foreach ($rows as $row) {
			$industryType = new IndustryType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($industryType, $key)) {
					$industryType->$key = $value;
				}
			}
			$industryTypes[] = $industryType;
		}
		return $industryTypes;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function getSubIndustryTypesCount() {

		$sql = "SELECT * ".
				"FROM " . TB_INDUSTRY_TYPE . 
				" WHERE parent IS NOT NULL
				 ORDER BY parent";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->num_rows();
		}
	}
	
	/**
	 * search industry type
	 * @param string $querySearch
	 * @param Pagination $pagination
	 * @return \IndustryType
	 */
	public function searchType($querySearch, Pagination $pagination = NULL) {
		$industryTypes = array();
		
		$sql = "SELECT * " .
			     " FROM " . TB_INDUSTRY_TYPE . 
				 " WHERE parent IS null AND UCASE(type) LIKE UCASE('%".$querySearch."%')"; 
		if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}  
		$this->db->query($sql);
		
		$rows = $this->db->fetch_all_array();

		foreach ($rows as $row) {
			$industryType = new IndustryType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($industryType, $key)) {
					$industryType->$key = $value;
				}
			}
			$industryTypes[] = $industryType;
		}
		return $industryTypes;
	}
	
	/**
	 * 
	 * @param string $querySearch
	 * @return boolean
	 */
	public function searchTypeResultsCount($querySearch) {

		$sql = "SELECT * " .
			     " FROM " . TB_INDUSTRY_TYPE . 
				 " WHERE parent IS null AND UCASE(type) LIKE UCASE('%".$querySearch."%')";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		} else {
			return $this->db->num_rows();
		}
	}
	
	/**
	 * search sub industry type
	 * @param type $querySearch
	 * @param Pagination $pagination
	 * @return type
	 */
	public function searchSubType($querySearch, Pagination $pagination = NULL) {
		$industryTypes = array();
		
		$sql = "SELECT * " .
				 " FROM " . TB_INDUSTRY_TYPE . 
				 " WHERE parent IS NOT null AND UCASE(type) LIKE UCASE('%".$querySearch."%')";
		if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}  
		$this->db->query($sql);
		
		$subTypesArray = $this->db->fetch_all_array();
		
		$allTypes = $this->getAllTypes();
		for ($i=0; $i<count($subTypesArray); $i++){
			for ($j=0; $j<count($allTypes); $j++){
				if ($subTypesArray[$i]['parent'] == $allTypes[$j]['id']){
					$subTypesArray[$i]['parentType'] = $allTypes[$j]['type'];
				}
			}
		}
		
		return $subTypesArray;
	}
	
	/**
	 * 
	 * @param type int
	 * @return boolean|\IndustryType
	 */
	public function getIndustryTypesByProductId($productId) {
		
		$sql = "SELECT * " .
				"FROM " . TB_INDUSTRY_TYPE . " it " .
				"LEFT JOIN " . TB_PRODUCT2INDUSTRY_TYPE . " p2it ON(p2it.industry_type_id=it.id) " .
				"WHERE p2it.product_id={$this->db->sqltext($productId)}";
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$industryTypes = array();
		foreach ($rows as $row) {
			$industryType = new IndustryType($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($industryType, $key)) {
					$industryType->$key = $value;
				}
			}
			$industryTypes[] = $industryType;
		}
		return $industryTypes;		
	}


	
}

?>