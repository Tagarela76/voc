<?php

namespace VWM\Cribs;

use VWM\Framework\Model;

class Crib extends Model {
		
	protected $id;
	protected $serial_number;
	protected $facility_id;
	
	/**
	 * Crib's bins
	 * @var Bin[]
	 */
	protected $bins;
	
	const TABLE_NAME = 'crib';
	
	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'Crib';
	}


	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getSerialNumber() {
		return $this->serial_number;
	}

	public function setSerialNumber($serialNumber) {
		$this->serial_number = $serialNumber;
	}

	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getBins() {
		return $this->bins;
	}

	public function setBins($bins) {
		$this->bins = $bins;
	}

	public function save() {
		if($this->getId()) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
	
	private function _insert() {		
		$sql = "INSERT INTO ".self::TABLE_NAME." (serial_number, facility_id) " .
				" VALUES ( " .
				" '{$this->db->sqltext($this->getSerialNumber())}', " .
				" {$this->db->sqltext($this->getFacilityId())} ";
		if(!$this->db->exec($sql)) {
			return false;
		}
		
		$this->setId($this->db->getLastInsertedID());
		
		return $this->getId();
	} 
}

?>
