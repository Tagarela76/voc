<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Step extends Model
{
	/**
	 * step id
	 * @var int
	 */
	protected $id;

	/**
	 * step number
	 * @var int
	 */
	protected $number = 0;

	/**
	 * process id
	 * @var int
	 */
	protected $process_id;

	/**
	 * option
	 * $var boolean
	 */
	protected $optional = 1;

	/**
	 * description
	 * @var string
	 */
	protected $description;

	/**
	 * total spent time
	 * @var int
	 */
	protected $total_spent_time = 0;

	/**
	 * resources
	 * @var array of \VWM\Apps\Process\Resource[]
	 */
	protected $resources = array();

	//const TABLE_NAME = 'step_template';
	//const RESOURCE_TABLE = 'resource_template';
	const TIME = 1;
	const VOLUME = 2;
	const COUNT = 3;

	public function __construct(\db $db, $id = null)
	{
		$this->db = $db;
		$this->modelName = "Step";
		if (isset($id)) {
			$this->setId($id);
			$this->load();
		}
	}

	public function getOptional()
	{
		return $this->optional;
	}

	public function setOptional($optional)
	{
		$this->optional = $optional;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * get and set resources for initialization
	 * @return Resource[] 
	 */
	public function getResources()
	{
		return $this->resources;
	}

	public function setResources($resources)
	{
		$this->resources = $resources;
	}

	public function getNumber()
	{
		return $this->number;
	}

	public function setNumber($number)
	{
		$this->number = $number;
	}

	public function getProcessId()
	{
		return $this->process_id;
	}

	public function setProcessId($process_id)
	{
		$this->process_id = $process_id;
	}
	
	public function load($table)
	{
		if (is_null($this->getId())) {
			return false;
		}
		$sql = "SELECT * " .
				"FROM " . $table . " " .
				"WHERE id = {$this->db->sqltext($this->getId())} " .
				"LIMIT 1";

		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
        return $this->getId();
	}

}

?>
