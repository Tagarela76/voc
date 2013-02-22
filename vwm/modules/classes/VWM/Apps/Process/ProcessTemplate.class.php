<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class ProcessTemplate extends Process
{
	const TABLE_NAME = 'process_template';
	const STEP_TABLE = 'step_template';
	const RESOURCE_TABLE = 'resource_template';

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }

	public function load()
	{
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

	/**
	 * function for getting all Steps in process
	 * @return boolean|\VWM\Apps\Process\StepTemplate[]
	 */
	public function getSteps()
	{
		$sql = "SELECT * FROM " . self::STEP_TABLE .
				" WHERE process_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$stepsDetails = $this->db->fetch_all_array();
		$steps = array();
		foreach ($stepsDetails as $stepDetails) {
			$step = new StepTemplate($this->db);
			$step->initByArray($stepDetails);
			$steps[] = $step;
		}

		return $steps;
	}

	protected function _insert()
	{
		$lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";

		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"facility_id, name, last_update_time, work_order_id" .
				") VALUES(" .
				"{$this->db->sqltext($this->getFacilityId())}," .
				"'{$this->db->sqltext($this->getName())}'," .
				"{$lastUpdateTime}, " .
				"'{$this->db->sqltext($this->getWorkOrderId())}'" .
				")";
		$response = $this->db->exec($sql);
		if ($response) {
			$this->setId($this->db->getLastInsertedID());
			return $this->getId();
		} else {
			return false;
		}
	}

	protected function _update()
	{
		$lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"facility_id={$this->db->sqltext($this->getFacilityId())}, " .
				"name='{$this->db->sqltext($this->getName())}', " .
				"work_order_id='{$this->db->sqltext($this->getWorkOrderId())}', " .
				"last_update_time={$lastUpdateTime} " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}

	/**
	 * function for getting Process Id by name and Facility Id
	 *
	 * @param type $facilityId
	 * @param type $name
	 *
	 * @return boolean
	 */
	public function getProcessIdByNameAndFacilityId($facilityId = null, $name = null)
	{
		if (is_null($facilityId)) {
			$facilityId = $this->getFacilityId();
		}
		if (is_null($name)) {
			$name = $this->getName();
		}

		if (is_null($facilityId) && is_null($name)) {
			return false;
		}

		$sql = "SELECT id FROM " . self::TABLE_NAME . " " .
				"WHERE facility_id = {$this->db->sqltext($facilityId)} " .
				"AND name = '{$this->db->sqltext($name)}' LIMIT 1";

		$this->db->query($sql);


		$row = $this->db->fetch(0);

		if (is_null($row->id)) {
			return false;
		} else {
			return $row->id;
		}
	}

	/**
	 * function for deleting process step
	 * @param int $processId
	 * @return boolean
	 */
	public function deleteProcessSteps($processId = null)
	{
		if (is_null($processId)) {
			$processId = $this->getId();
		}

		if (is_null($processId)) {
			return false;
		}

		$sql = "DELETE FROM s,r " .
				"USING " . self::STEP_TABLE . " s " .
				"LEFT JOIN " . self::RESOURCE_TABLE . " r " .
				"ON s.id = r.step_id " .
				"WHERE s.process_id = {$this->db->sqltext($processId)}";
		$response = $this->db->exec($sql);
		if ($response) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * function for creating process instance for Work Order
	 * @return \VWM\Apps\Process\ProcessInstance
	 */
	public function createProcessInstance()
	{
		$processInstance = new ProcessInstance($this->db);
		$processInstance->setFacilityId($this->getFacilityId());
		$processInstance->setName($this->getName());
		$processInstance->setWorkOrderId($this->getWorkOrderId());
		return $processInstance;
	}

}

?>
