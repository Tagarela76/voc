<?php

namespace VWM\Apps\UnitType\Entity;

use VWM\Framework\Model;

class Type extends Model {

	protected $type_id;
	protected $type_desc;

	const TABLE_NAME = '`type`';

	public function __construct(\db $db) {
		$this->db = $db;
	}

	public function getTypeId() {
		return $this->type_id;
	}

	public function setTypeId($type_id) {
		$this->type_id = $type_id;
	}

	public function getTypeDesc() {
		return $this->type_desc;
	}

	public function setTypeDesc($type_desc) {
		$this->type_desc = $type_desc;
	}

	public function save() {
		throw new \Exception('You cannot add/edit this entity');
	}

	public function load() {
		if(!$this->getTypeId()) {
			throw new \Exception("You cannot get type without type id");
		}

		$sql = "SELECT * " .
				"FROM ".self::TABLE_NAME. " t " .
				"WHERE t.type_id = {$this->db->sqltext($this->getTypeId())}";
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
