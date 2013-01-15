<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class Step extends Model {
	/*
	 * step id
	 * @var int
	 */

	protected $id;

	/*
	 * step number
	 * @var int
	 */
	protected $number;

		/*
	 * step id
	 * @var int
	 */

	protected $process_id;

	/*
	 * step number
	 * @var int
	 */
	
	protected $total_spent_time=0;
	
	const TABLE_NAME = 'step';
	const RESOURCE_TABLE = 'resource';
	const TIME = 1;
	const VOLUME = 2;
	const COUNT = 3;
	
	public function __construct(\db $db, $Id = null) {
		$this->db = $db;
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}
	
	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getNumber() {
		return $this->number;
	}

	public function setNumber($number) {
		$this->number = $number;
	}
	public function getProcessId() {
		return $this->process_id;
	}

	public function setProcessId($process_id) {
		$this->process_id = $process_id;
	}

	public function getTotalSpentTime() {
		$this->calculateTotalSpentTime();
		return $this->total_spent_time;
	}
	
	public function load() {
		if (is_null($this->getId())) {
			return false;
		}
		$sql = "SELECT * " .
				"FROM " . self::TABLE_NAME . " " .
				"WHERE id = {$this->db->sqltext($this->getId())} " .
				"LIMIT 1";

		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
	}
	
	private function calculateTotalSpentTime(){
		$resources = $this->getResources();
		$totalSpentTime = 0;
		foreach($resources as $resource){
			
			if($resource->getResourceTypeId()==self::TIME){
				$unitTypeConvector = new \UnitTypeConverter($this->db);
				$unitType = new \Unittype($this->db);
				$unittypeName = $unitType->getNameByID($resource->getUnittypeId());
				$qty = $unitTypeConvector->convertDefaultTime($resource->getQty(), $unittypeName);
				$totalSpentTime+=$qty;
			}
		}
		$this->total_spent_time = $totalSpentTime;
	}
	
	public function getResources(){
		$sql = "SELECT * FROM " . self::RESOURCE_TABLE .
				" WHERE step_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$resourcesDetails = $this->db->fetch_all_array();
		$resources = array();
		foreach ($resourcesDetails as $resourceDetails) {
			$resource = new Resource($this->db);
			$resource->initByArray($resourceDetails);
			$resources[] = $resource;
		}
		return $resources;
	}
	
	protected function _insert() {

		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"number, process_id, last_update_time" .
				") VALUES(" .
				"{$this->db->sqltext($this->getNumber())}," .
				"'{$this->db->sqltext($this->getProcessId())}'," .
				"'{$this->db->sqltext($this->getLastUpdateTime())}'" .
				")";
		$response = $this->db->exec($sql);
		if ($response) {
			$this->setId($this->db->getLastInsertedID());
			return $this->getId();
		} else {
			return false;
		}
	}

	protected function _update() {
		$lastUpdateTime = $this->getLastUpdateTime();

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"number={$this->db->sqltext($this->getNumber())}, " .
				"process_id='{$this->db->sqltext($this->getProcessId())}', " .
				"last_update_time='{$lastUpdateTime}' " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}

	
}

?>
