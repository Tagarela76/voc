<?php

namespace VWM\Entity\Crib;

use VWM\Framework\Model;

class Bin extends Model {
	
	protected $id;
	protected $crib_id;
	protected $number;
	protected $size;
	protected $type;
	protected $capacity;	
	protected $name;

	/**
	 * Crib to whom this bin assigned
	 * @var \VWM\Entity\Crib\Crib
	 */
	protected $crib;
	
	const TABLE_NAME = 'bin';
	
	public function __construct(\db $db, $id = null) {
		$this->db = $db;
		$this->modelName = 'Bin';
		
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

	public function getCribId() {
		return $this->crib_id;
	}

	public function setCribId($cribId) {
		$this->crib_id = $cribId;
	}

	public function getNumber() {
		return $this->number;
	}

	public function setNumber($number) {
		$this->number = $number;
	}

	public function getSize() {
		return $this->size;
	}

	public function setSize($size) {
		$this->size = $size;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getCapacity() {
		return $this->capacity;
	}

	public function setCapacity($capacity) {
		$this->capacity = $capacity;
	}		
	
	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Crib to whom this bin assigned
	 * @var \VWM\Entity\Crib\Crib|false
	 */
	public function getCrib() {
		if(!$this->crib) {
			if(!$this->getCribId()) {
				throw new \Exception("Crib Id should be set in order to call this method");
			}
			
			$sql = "SELECT * FROM ".Crib::TABLE_NAME." WHERE " .
					"id = {$this->db->sqltext($this->getCribId())}";
			$this->db->query($sql);
			if($this->db->num_rows() == 0) {
				return false;
			}
			
			$row = $this->db->fetch_array(0);
			$crib = new Crib($this->db);
			$crib->initByArray($row);
			$this->setCrib($crib);
		}
		return $this->crib;
	}

	/**	 
	 * @param \VWM\Cribs\Crib $crib
	 */
	public function setCrib(Crib $crib) {
		$this->crib = $crib;
	}

	private function _load() {
		if(!$this->getId()) {
			throw new Exception('You should set Id first to call this method');
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
	
	
	protected function _insert() {
		$sql = "INSERT INTO ".self::TABLE_NAME." (crib_id, size, " .
				"capacity, last_update_time, name ) VALUES ( "  .
				"{$this->db->sqltext($this->getCribId())}, " .
				"{$this->db->sqltext($this->getNumber())}, " .
				"{$this->db->sqltext($this->getSize())}, " .
				"{$this->db->sqltext($this->getType())}, " .
				"{$this->db->sqltext($this->getCapacity())}, " .
				"'{$this->db->sqltext($this->getLastUpdateTime())}', " .
				"'{$this->db->sqltext($this->getName())}'" .		
				")";
				
		if(!$this->db->exec($sql)) {
			return false;
		}
		
		$this->setId($this->db->getLastInsertedID());
		
		return $this->getId();
	}
	
	protected function _update() {
		$sql = "UPDATE ".self::TABLE_NAME." SET " .
				"crib_id={$this->db->sqltext($this->getCribId())}, " .
				"size={$this->db->sqltext($this->getSize())}, " .
				"capacity={$this->db->sqltext($this->getCapacity())}, " .
				"last_update_time='{$this->db->sqltext($this->getLastUpdateTime())}', " .
				"name='{$this->db->sqltext($this->getName())}' " .		
				"WHERE id = {$this->db->sqltext($this->getId())}";					
		if(!$this->db->exec($sql)) {
			return false;
		}				
		
		return $this->getId();
	}
	
	/**
	 * Method that check if exist bin with this name
	 */
	public function check() {

		$sql = "SELECT * FROM ".self::TABLE_NAME." " .
				"WHERE name = '{$this->db->sqltext($this->getName())}'";
		$this->db->query($sql);
		if($this->db->num_rows() != 0) {
			$row = $this->db->fetch_array(0);
			$this->setId($row["id"]);
		}
	}

}

?>
