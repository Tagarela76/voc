<?php

namespace VWM\Entity\Crib;

use VWM\Framework\Model;

class Crib extends Model {
		
	protected $id;
	protected $serial_number;
	protected $facility_id;			

	/**
	 * Crib's bins
	 * @var \VWM\Entity\Crib\Bin[]
	 */
	protected $bins;
	
	const TABLE_NAME = 'crib';
	
	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'Crib';
		
		if($id !== null) {
			$this->setId($id);
			if(!$this->_load()) {
				throw new Exception('404');
			}
		}
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

	/**
	 * Crib's bins
	 * @return \VWM\Entity\Crib\Bin[]
	 */
	public function getBins() {
		
		if (!is_array($this->bins)) {
			$sql = "SELECT * FROM " . Bin::TABLE_NAME . " " .
					"WHERE crib_id = {$this->db->sqltext($this->getId())}";
			$this->db->query($sql);

			if ($this->db->num_rows() == 0) {
				$this->setBins(array());
				return $this->bins;
			}

			$rows = $this->db->fetch_all();
			$bins = array();
			foreach ($rows as $row) {
				$bin = new Bin($this->db);
				$bin->initByArray($row);
				$bins[] = $bin;
			}

			$this->setBins($bins);
		}
		
		return $this->bins;
	}

	public function setBins($bins) {
		$this->bins = $bins;
	}
				
	protected function _insert() {		
		$sql = "INSERT INTO ".self::TABLE_NAME." (serial_number, facility_id, " .
				"last_update_time " .
				") VALUES (" .
				"'{$this->db->sqltext($this->getSerialNumber())}', " .
				"{$this->db->sqltext($this->getFacilityId())}, " .
				"'{$this->db->sqltext($this->getLastUpdateTime())}' " .
				")";
		if(!$this->db->exec($sql)) {
			return false;
		}
		
		$this->setId($this->db->getLastInsertedID());
		
		return $this->getId();
	} 
	
	
	protected function _update() {
		$sql = "UPDATE ".self::TABLE_NAME." SET " .
				"serial_number = '{$this->db->sqltext($this->getSerialNumber())}', " .
				"facility_id = {$this->db->sqltext($this->getFacilityId())}, " .
				"last_update_time = '{$this->db->sqltext($this->getLastUpdateTime())}' " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		if(!$this->db->exec($sql)) {
			return false;
		}				
		
		return $this->getId();
	}
	
	
	private function _load() {
		if(!$this->getId()) {
			throw new Exception("Crib Id should be set to call this method");			
		}
		
		$sql = "SELECT * FROM ".self::TABLE_NAME." " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if($this->db->num_rows() == 0) {
			return false;
		}
		
		$row = $this->db->fetch_array(0);
		$this->initByArray($row);
		return true;
	}
}

?>
