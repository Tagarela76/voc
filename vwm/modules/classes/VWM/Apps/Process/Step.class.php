<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Step extends Model {
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
	protected $process_template_id;
	
	
	const TABLE_NAME = 'step';
	
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

	public function getProcessTemplateId() {
		return $this->process_template_id;
	}

	public function setProcessTemplateId($process_template_id) {
		$this->process_template_id = $process_template_id;
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

	
}

?>
