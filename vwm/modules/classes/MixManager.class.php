<?php

class MixManager {

	/**
	 * @var db
	 */
	private $db;
	public $departmentID;

	/**
	 * @var array Search strings
	 */
	public $searchCriteria = array();

	public function __construct(db $db, $departmentID = null) {
		$this->db = $db;
		if (isset($departmentID)) {
			$this->departmentID = $departmentID;
		}
	}

	/**
	 * Count mixes in department. Useful for pagination
	 * @param string $filter
	 * @return bool|int false on failure
	 */
	public function countMixes( $filter=' TRUE ') {
		if(!$this->departmentID) {
			return false;
		}
		$query = "SELECT count(mix_id) mixCount FROM ".TB_USAGE." WHERE department_id = {$this->db->sqltext($this->departmentID)} " .
			"AND $filter ";

		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$query .= "AND ( ";
			foreach ($this->searchCriteria as $mixDescription) {
				$searchSql[] = " (description LIKE '%{$this->db->sqltext($mixDescription)}%') ";
			}
			$query .= implode(' OR ', $searchSql);
			$query .= ") ";
		}

		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			return (int)$this->db->fetch(0)->mixCount;
		} else {
			return false;
		}
	}

	/**
	 * Count mixes in facility
	 * @param int $facilityID
	 * @param string $filter
	 * @return bool|int false on failure
	 */
	public function countMixesInFacility($facilityID, $filter = ' TRUE ') {
		$query = "SELECT count(m.mix_id) mixCount " .
			" FROM ".TB_USAGE." m " .
			" JOIN ".TB_DEPARTMENT." d ON m.department_id = d.department_id " .
			" WHERE d.facility_id = {$this->db->sqltext($facilityID)} " .
			" AND {$filter} ";

		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$query .= "AND ( ";
			foreach ($this->searchCriteria as $mixDescription) {
				$searchSql[] = " (m.description LIKE '%{$this->db->sqltext($mixDescription)}%') ";
			}
			$query .= implode(' OR ', $searchSql);
			$query .= ") ";
		}

		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			return (int)$this->db->fetch(0)->mixCount;
		} else {
			return false;
		}
	}


	/**
	 * Get mix list
	 * @param Pagination $pagination
	 * @param string $filter
	 * @return array|bool
	 */
	public function getMixList(Pagination $pagination = null, $filter = ' TRUE ', $sort=' ORDER BY mix_id DESC ' ) {
		if(!$this->departmentID) {
			return false;
		}

		$query = "SELECT * FROM ".TB_USAGE." " .
			"WHERE department_id = {$this->db->sqltext($this->departmentID)} " .
			"AND $filter ";

		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$query .= "AND ( ";
			foreach ($this->searchCriteria as $mixDescription) {
				$searchSql[] = " (description LIKE '%{$this->db->sqltext($mixDescription)}%') ";
			}
			$query .= implode(' OR ', $searchSql);
			$query .= ") ";
		}

		$query .= $sort;

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		return $this->_processListQuery($query);
	}


	/**
	 * Get mix list for facility
	 * @param int $facilityID facility scope
	 * @param Pagination $pagination
	 * @param string $filter
	 * @return array|bool false on failure
	 */
	public function getMixListInFacility($facilityID, Pagination $pagination = null, $filter = ' TRUE ', $sort=' ORDER BY m.mix_id DESC ') {
		$woDescriptionField = 'woDescription';
		$query = "SELECT m.*, wo.id, wo.customer_name, wo.description {$woDescriptionField}, wo.vin " .
			" FROM ".TB_USAGE." m " .
			" JOIN ".TB_DEPARTMENT." d ON m.department_id = d.department_id " .
			" LEFT JOIN ".TB_WORK_ORDER." wo ON m.wo_id = wo.id " .
			" WHERE d.facility_id = {$this->db->sqltext($facilityID)} " .
			" AND {$filter} ";

		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$query .= "AND ( ";
			foreach ($this->searchCriteria as $mixDescription) {
				$searchSql[] = " (m.description LIKE '%{$this->db->sqltext($mixDescription)}%') ";
			}
			$query .= implode(' OR ', $searchSql);
			$query .= ") ";
		}

		$query .= $sort;

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		return $this->_processListQuery($query);
	}

	/**
	 * Process SQL query for mix listing
	 * @param string $query
	 * @return array|bool array of MixOptimized or false on empty result
	 * @throws Exception
	 */
	private function _processListQuery($query) {
		if (!$this->db->query($query)) {
			throw new Exception('SQL query failed.');
		}

		$rowCount = $this->db->num_rows();
		if($rowCount == 0) {
			return false;
		}

		$rows = $this->db->fetch_all_array();
		$mixes = array();
		foreach($rows as $row) {
			$mix = new MixOptimized($this->db);			
			foreach ($row as $key => $value) {				
				if (property_exists($mix, $key)) {
					$mix->$key = $value;
				}
			}
			
			if($mix->wo_id !== null) {
				$workOrder = new WorkOrder($this->db);
				//	overrite mix description just because both mix and work order
				//	have field description
				$row['description'] = $row['woDescription'];				
				$workOrder->initByArray($row);
				$mix->setWorkOrder($workOrder);
			}
			$mixes[] = $mix;
		}

		return $mixes;
	}


	public function fillProductsUnitTypes($mixesProducts) {

		$ids = array();

		foreach ($mixesProducts as $product) {
			$ids[] = $product->unit_type;
		}

		$unittype = new Unittype($this->db);

		$unitTypeDetails = $unittype->getUnittypesDetails($ids);

		foreach ($mixesProducts as $product) {
			$product->unittypeDetails = $unitTypeDetails[$product->unit_type];
		}
	}

	public function deleteMixList($mixIDarr) {

	}

}