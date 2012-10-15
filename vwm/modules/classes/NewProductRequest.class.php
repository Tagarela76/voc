<?php

use VWM\Framework\Model;
use VWM\Framework\Utils\DateTime;

class NewProductRequest extends Model {    
    
	protected $id;
	protected $supplier;
    protected $product_id;    
    protected $name;
    protected $description;
	protected $msds_id;
	protected $date;
	protected $last_update_time;
	protected $user_id;
	protected $status;
	
	protected $user;
	protected $msds;
	
	public $url;

	const STATUS_NEW = 0;
	const STATUS_ACCEPT = 1;
	
	public function __construct(\db $db, $id = NULL) {
		$this->db = $db;
		$this->modelName = 'NewProductRequest';
		
		if($id !== NULL) {
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

	public function getSupplier() {
		return $this->supplier;
	}

	public function setSupplier($supplier) {
		$this->supplier = $supplier;
	}

	public function getProductId() {
		return $this->product_id;
	}

	public function setProductId($product_id) {
		$this->product_id = $product_id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getMsdsId() {
		return $this->msds_id;
	}

	public function setMsdsId($msds_id) {
		$this->msds_id = $msds_id;
	}

	public function getDate() {
		return $this->date;
	}	

	public function setDate($date) {
		$this->date = $date;
	}
		
	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}

	public function getUserId() {
		return $this->user_id;
	}

	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		$this->status = $status;
	}
	
	/**
	 * TODO: finish me
	 * @return 
	 */
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
	}

	/**
	 * TODO: finish me
	 * @return 
	 */
	public function getMsds() {
		return $this->msds;
	}
	
	public function setMsds($msds) {
		$this->msds = $msds;
	}
	
	
	public function getStatusOptions() {
		return array(
			self::STATUS_NEW => 'New',
			self::STATUS_ACCEPT => 'Accept',			
		);
	}
	
	public function getStatusOptionName($id) {
		$options = $this->getStatusOptions();
		foreach ($options as $key => $name) {
			if($key == $id) {
				return $name;
			}
		}
	}

	public function save() {
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));
		
		if($this->getId()) {
			return $this->_update();
		} else {
			$now = new DateTime();
			$this->setDate($now->getTimestamp());
			return $this->_insert();
		}
	}	
	
	private function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: 'NULL';
		
		$query = "INSERT INTO ".TB_NEW_PRODUCT_REQUEST." (supplier, " .
				 "product_id, name, description, msds_id, date, user_id, " .
				 "status, last_update_time) VALUES ( " .
                "'{$this->db->sqltext($this->getSupplier())}', " .
                "'{$this->db->sqltext($this->getProductId())}', " .
                "'{$this->db->sqltext($this->getName())}', " .
                "'{$this->db->sqltext($this->getDescription())}', " .
                "{$this->db->sqltext($this->getMsdsId())}, " .
                "{$this->db->sqltext($this->getDate())}, " .
                "{$this->db->sqltext($this->getUserId())}, " .
                "{$this->db->sqltext($this->getStatus())}, " .
				"{$lastUpdateTime}) ";
				
        if($this->db->exec($query)) {
			$this->setId($this->db->getLastInsertedID());
			return $this->getId();
		} else {
			return false;
		}		
	}
	
	private function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'"
				: 'NULL';
				
		$sql = "UPDATE ".TB_NEW_PRODUCT_REQUEST." SET " .
				"supplier = '{$this->db->sqltext($this->getSupplier())}', " .
				"product_id = '{$this->db->sqltext($this->getProductId())}', " .
				"name = '{$this->db->sqltext($this->getName())}', " .
				"description = '{$this->db->sqltext($this->getDescription())}', " .
				"msds_id = {$this->db->sqltext($this->getMsdsId())}, " .
				"date = {$this->db->sqltext($this->getDate())}, " .
				"user_id = {$this->db->sqltext($this->getUserId())}, " .
				"status = {$this->db->sqltext($this->getStatus())}, " .
				"last_update_time = {$lastUpdateTime} " .
			"WHERE id = {$this->db->sqltext($this->getId())}";
		
		if($this->db->exec($sql)) {			
			return $this->getId();
		} else {
			return false;
		}	
	}

	
	private function _load() {
		if(!$this->getId()) {
			throw new \Exception('ID should be set before calling this method');
		}
				
		$sql = "SELECT * FROM ".TB_NEW_PRODUCT_REQUEST." " .
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
