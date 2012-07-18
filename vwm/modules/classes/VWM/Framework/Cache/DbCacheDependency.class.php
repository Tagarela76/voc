<?php

namespace VWM\Framework\Cache;


class DbCacheDependency {

	private $_data;

	/**
	 * @var \db
	 */
	private $db;

	/**
	 * @var string the SQL statement whose result is used to determine if the dependency has been changed.
	 * Note, the SQL statement should return back a single value.
	 */
	private $sql;


	public function __construct(\db $db, $sql = null) {
		$this->db = $db;
		$this->sql = $sql;
	}


	/**
	 * Evaluates the dependency by generating and saving the data related with dependency.
	 * This method is invoked by cache before writing data into it.
	 */
	public function evaluateDependency() {
		$this->_data = $this->generateDependentData();
	}


	public function hasChanged() {
		$this->db->select_db(DB_NAME);
		return $this->generateDependentData() != $this->_data;
	}


	/**
	 * Generates the data needed to determine if dependency has been changed.
	 * @return bool|stdClass
	 * @throws Exception
	 */
	protected function generateDependentData() {
		if ($this->sql !== null) {
			$this->db->query($this->sql);
			if($this->db->num_rows() == 0) {
				return false;
			} else {
				return $this->db->fetch(0);
			}
		} else {
			throw new \Exception('DbCacheDependency.sql cannot be empty.');
		}
	}


}
