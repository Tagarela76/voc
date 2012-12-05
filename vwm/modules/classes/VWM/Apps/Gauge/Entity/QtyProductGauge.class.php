<?php
namespace VWM\Apps\Gauge\Entity;
use VWM\Framework\Model;

class QtyProductGauge extends Gauge {		

	const TABLE_NAME = 'qty_product_gauge';        

    public function __construct(\db $db, $facilityId = null) {
		$this->db = $db;
		$this->modelName = 'QtyProductGauge';
		if (isset($facilityId)) {
			$this->setFacilityId($facilityId);
			$this->_load();
		}		
	}
	
	private function _load() {

		if (is_null($this->getFacilityId())) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM " . self::TABLE_NAME . " ".
				"WHERE facility_id={$this->db->sqltext($this->getFacilityId())} " . 
				"LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);

		$this->initByArray($row);
	}
	
	
    /**
     * Insert new settings
     * @return int| boolean
     */
	protected function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "INSERT INTO ".self::TABLE_NAME." (" .
				"`limit`, unit_type, period, facility_id, last_update_time" .
				") VALUES ( ".
				"{$this->db->sqltext($this->getLimit())}, " .
				"{$this->db->sqltext($this->getUnitType())}, " .
				"{$this->db->sqltext($this->getPeriod())}, " .
				"{$this->db->sqltext($this->getFacilityId())}, " .
				"{$lastUpdateTime} " .
				")"; 
		$response = $this->db->exec($sql);
		if($response) {
			$this->setId($this->db->getLastInsertedID());	
			return $this->getId();
		} else {
			return false;
		}
		
		
	}
	
	/**
	 * Update update settings
	 * @return boolean
	 */
	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "UPDATE ".self::TABLE_NAME." SET " .
				"`limit`={$this->db->sqltext($this->getLimit())}, " .
				"unit_type='{$this->db->sqltext($this->getUnitType())}', " .
				"period={$this->db->sqltext($this->getPeriod())}, " .
                "facility_id={$this->db->sqltext($this->getFacilityId())}, " .        
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";	
		
		$response = $this->db->exec($sql);
		if($response) {			
			return $this->getId();
		} else {
			return false;
		}
	}		
	
	/**
	 * Delete settings for facility
	 */
	public function delete() {

		$sql = "DELETE FROM " . self::TABLE_NAME . "
				 WHERE facility_id={$this->db->sqltext($this->getFacilityId())}";
		$this->db->query($sql);
	}       
}

?>
