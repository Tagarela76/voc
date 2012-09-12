<?php

use VWM\Framework\Model;

class NoxBurner extends Model {
	
	private $burner_id;
	private $manufacturer_id;
	private $serial;
	private $model;
	private $btu;
	private $department_id;
	private $input;
	private $output;



	public function __construct(db $db, Array $array = null) {
		$this->db = $db;
		$this->modelName = "NoxBurner";
		
		if (isset($array)) {
			$this->initByArray($array);
		}
	}

	

	public function save() {
		if ($this->burner_id != NULL) {
			$query = "UPDATE burner SET " .
						"manufacturer_id = {$this->db->sqltext($this->manufacturer_id)}, " .
						"department_id = {$this->db->sqltext($this->department_id)}, " .
						"model = '{$this->db->sqltext($this->model)}', " .
						"serial = '{$this->db->sqltext($this->serial)}', " .
						"input = {$this->db->sqltext($this->input)}, " .
						"output = {$this->db->sqltext($this->output)}, " .
						"btu = {$this->db->sqltext($this->btu)} " .
						"WHERE burner_id = {$this->db->sqltext($this->burner_id)}";
		} else {
			$query = "INSERT INTO burner VALUES (NULL,'"
					. $this->db->sqltext($this->department_id) . "','"
					. $this->db->sqltext($this->manufacturer_id) . "','"
					. $this->db->sqltext($this->model) . "','"
					. $this->db->sqltext($this->serial) . "','"
					. $this->db->sqltext($this->input) . "','"
					. $this->db->sqltext($this->output) . "','"
					. $this->db->sqltext($this->btu) . "')";
		}

		$this->db->query($query);

		if (mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	

	public function get_burner_id() {
		return $this->burner_id;
	}

	public function get_department_id() {
		return $this->department_id;
	}

	public function get_manufacturer_id() {
		return $this->manufacturer_id;
	}

	public function get_serial() {
		return $this->serial;
	}

	public function get_model() {
		return $this->model;
	}

	public function get_btu() {
		return $this->btu;
	}

	public function get_input() {
		return $this->input;
	}

	public function get_output() {
		return $this->output;
	}
	

	public function set_burner_id($value) {
		try {
			$this->burner_id = $value;
		} catch (Exception $e) {
			throw new Exception("burner Id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_manufacturer_id($value) {
		try {
			$this->manufacturer_id = $value;
		} catch (Exception $e) {
			throw new Exception("manufacturer id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_department_id($value) {
		try {
			$this->department_id = $value;
		} catch (Exception $e) {
			throw new Exception("Department id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_serial($value) {
		try {
			$this->serial = $value;
		} catch (Exception $e) {
			throw new Exception("serial id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_model($value) {
		try {
			$this->model = $value;
		} catch (Exception $e) {
			throw new Exception("model cannot be empty!" . $e->getMessage());
		}
	}

	public function set_btu($value) {
		try {
			$this->btu = $value;
		} catch (Exception $e) {
			throw new Exception("btu type id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_input($value) {
		try {
			$this->input = $value;
		} catch (Exception $e) {
			throw new Exception("Input type id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_output($value) {
		try {
			$this->output = $value;
		} catch (Exception $e) {
			throw new Exception("Output id cannot be empty!" . $e->getMessage());
		}
	}
	
	public function isUniqueSerial() {
		$sql = "SELECT burner_id FROM burner WHERE serial = '{$this->db->sqltext($this->serial)}'";
		$this->db->query($sql);
		return ($this->db->num_rows() == 0) ? true : false;
	}
}

?>
