<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;

class Pfp extends Model {

	protected $id;
	protected $description;
	protected $company_id;
	protected $creator_id;
	protected $last_update_time;
	protected $isProprietary;
	protected $products;

	const TABLE_NAME = 'preformulated_products';

	public function __construct(\db $db) {
		$this->db = $db;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getCompanyId() {
		return $this->company_id;
	}

	public function setCompanyId($company_id) {
		$this->company_id = $company_id;
	}

	public function getCreatorId() {
		return $this->creator_id;
	}

	public function setCreatorId($creator_id) {
		$this->creator_id = $creator_id;
	}

	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}

	public function getProducts() {
		return $this->products;
	}

	public function setProducts($products) {
		$this->products = $products;
	}

	public function getIsProprietary() {
		return $this->isProprietary;
	}

	public function setIsProprietary($isProprietary) {
		$this->isProprietary = $isProprietary;
	}

		

	protected function _insert() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'" : "NULL";
				
		$sql = "INSERT INTO " . self::TABLE_NAME . " " .
				"(description, last_update_time) VALUES (".
				"'{$this->db->sqltext($this->getDescription())}', {$lastUpdateTime})";
		$response = $this->db->exec($sql);
		if ($response) {
			$this->setId($this->db->getLastInsertedID());
			return $this->getId();
		} else {
			return false;
		}
	}

	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'" : "NULL";

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"description = '{$this->db->sqltext($this->getDescription())}', " .
				"last_update_time = {$lastUpdateTime} " .
				"WHERE id = {$this->db->sqltext($this->getId())}";
		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}
	
	
	public function load() {
		if (is_null($this->getId())) {
			return false;
		}

		$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id =" .
				$this->db->sqltext($this->getId());
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
	}

	
}

?>
