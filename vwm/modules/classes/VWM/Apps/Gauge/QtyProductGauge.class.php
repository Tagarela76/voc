<?php
namespace VWM\Apps\Gauge;
use VWM\Framework\Model;

class QtyProductGauge extends Model {
	
	protected $id;

	protected $limit=0;
    
    protected $unit_type=1;
    
    protected $period=0;
    
    protected $facility_id;
    
    protected $last_update_time;
    
    const PERIOD_MONTHLY = 0;
	const PERIOD_ANNUM= 1;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
    }

    public function getUnit_type() {
        return $this->unit_type;
    }

    public function setUnit_type($unit_type) {
        $this->unit_type = $unit_type;
    }

    public function getPeriod() {
        return $this->period;
    }

    public function setPeriod($period) {
        $this->period = $period;
    }

    public function getLast_update_time() {
        return $this->last_update_time;
    }

    public function setLast_update_time($last_update_time) {
        $this->last_update_time = $last_update_time;
    }
    
    public function getFacility_id() {
        return $this->facility_id;
    }

    public function setFacility_id($facility_id) {
        $this->facility_id = $facility_id;
    }

    function __construct(\db $db, $facilityId = null) {
		$this->db = $db;
		$this->modelName = 'QtyProductGauge';
		if (isset($facilityId)) {
			$this->setFacility_id($facilityId);
			$this->_load();
		}		
	}
	
	private function _load() {

		if (is_null($this->getFacility_id())) {
			return false;
		}
		$sql = "SELECT * ".
				"FROM " . TB_QTY_PRODUCT_GAUGE . " ".
				"WHERE facility_id={$this->db->sqltext($this->getFacility_id())} " . 
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
	
	public function save() {		
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->getId() ) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}
	
    /**
     * Insert new settings
     * @return int| boolean
     */
	protected function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: "NULL";
				
		$sql = "INSERT INTO ".TB_QTY_PRODUCT_GAUGE." (" .
				"`limit`, unit_type, period, facility_id, last_update_time" .
				") VALUES ( ".
				"{$this->db->sqltext($this->getLimit())}, " .
				"{$this->db->sqltext($this->getUnit_type())}, " .
				"{$this->db->sqltext($this->getPeriod())}, " .
				"{$this->db->sqltext($this->getFacility_id())}, " .
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
				
		$sql = "UPDATE ".TB_QTY_PRODUCT_GAUGE." SET " .
				"`limit`={$this->db->sqltext($this->getLimit())}, " .
				"unit_type='{$this->db->sqltext($this->getUnit_type())}', " .
				"period={$this->db->sqltext($this->getPeriod())}, " .
                "facility_id={$this->db->sqltext($this->getFacility_id())}, " .        
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

		$sql = "DELETE FROM " . TB_QTY_PRODUCT_GAUGE . "
				 WHERE facility_id={$this->db->sqltext($this->getFacility_id())}";
		$this->db->query($sql);
	}
    
    public function getPeriodOptions() {
		return array(
			'Monthly' => self::PERIOD_MONTHLY,
			'Annum' => self::PERIOD_ANNUM,
		);
	}


	public function getPeriodName() {
		$options = $this->getPeriodOptions();
		foreach ($options as $key => $option) {
			if($option == $this->getPeriod()) {
				return $key;
			}
		}
	}
}

?>
