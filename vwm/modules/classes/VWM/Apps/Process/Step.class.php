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
	protected $number = 0;

		/*
	 * step id
	 * @var int
	 */

	protected $process_id;

	/*
	 * option
	 * $var boolean
	 */
	protected $optional = 1;
	
	/*
	 * description
	 * @var string
	 */
	protected $description;
	
	/*
	 * resources
	 * @var array of objects
	 */
	protected $init_resources = array();
	
	const TABLE_NAME = 'step_template';
	const RESOURCE_TABLE = 'resource_template';
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
	
	public function getOptional() {
		return $this->optional;
	}

	public function setOptional($optional) {
		$this->optional = $optional;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

		public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	/**
	 *get and set resources for initialization
	 * @return type array
	 */
	public function getInitResources() {
		return $this->init_resources;
	}

	public function setInitResources($init_resources) {
		$this->init_resources = $init_resources;
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

	
	
	
}

?>
