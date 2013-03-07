<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

/**
 * class ProcessInstance class of work order process
 *
 * @access public
 *
 */
class ProcessInstance extends Process
{

	const TABLE_NAME = 'process_instance';
	const STEP_TABLE = 'step_instance';
	const RESOURCE_TABLE = 'resource_instance';
    /**
     * @var VWM\Apps\Process\StepInstance[]
     */
    protected $stepsCreatingByUser = array();
    
    public function setStepsCreatingByUser($stepsCreatingByUser)
    {
        $this->stepsCreatingByUser = $stepsCreatingByUser;
    }

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

	protected function _insert()
	{
		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"facility_id, name, last_update_time, work_order_id" .
				") VALUES(" .
				"{$this->db->sqltext($this->getFacilityId())}," .
				"'{$this->db->sqltext($this->getName())}'," .
				"NOW(), " .
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
		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"facility_id={$this->db->sqltext($this->getFacilityId())}, " .
				"name='{$this->db->sqltext($this->getName())}', " .
				"work_order_id='{$this->db->sqltext($this->getWorkOrderId())}', " .
				"last_update_time=NOW() " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			return $this->getId();
		} else {
			return false;
		}
	}

	/**
	 * get current process steps
	 * @return boolean|\VWM\Apps\Process\StepInstance false on failure
	 */
	public function getSteps()
	{
		$processSteps = $this->getProcessSteps();
		if (!empty($processSteps)) {
			return $this->getProcessSteps();
		}
		$sql = "SELECT * FROM " . self::STEP_TABLE .
				" WHERE process_id = {$this->db->sqltext($this->getId())}";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$stepsDetails = $this->db->fetch_all_array();
		$steps = array();
		foreach ($stepsDetails as $stepDetails) {
			$step = new StepInstance($this->db);
			$step->initByArray($stepDetails);
			$steps[] = $step;
		}

		$this->setProcessSteps($steps);
		return $steps;
	}

	/**
	 * function for deleting process, process steps and process resources
	 *
	 * @param int $processId
	 *
	 * @return boolean 
	 */
	public function deleteCurrentProcess($processId)
	{
		if (is_null($processId)) {
			$processId = $this->getId();
		}
		if (is_null($processId)) {
			return false;
		}
		$sql = "DELETE s,p,r FROM " . self::TABLE_NAME . " p " .
				"LEFT JOIN " . self::STEP_TABLE . " s " .
				"ON p.id = s.process_id " .
				"LEFT JOIN " . self::RESOURCE_TABLE . " r " .
				"ON s.id = r.step_id " .
				"WHERE p.id = {$this->db->sqltext($processId)}";
		$response = $this->db->exec($sql);
		if ($response) {
			return true;
		} else {
			return false;
		}
	}
    
    /**
     * get step wich user create by himself.
     * 
     * @param int $lastTemplateStepNumber
     * 
     * @return NULL | VWM\Apps\Process\StepInstance[]
     */
    public function getStepsCreatingByUser($lastTemplateStepNumber = null)
    {
        if (!empty($this->stepsCreatingByUser)) {
            return $this->stepsCreatingByUser;
        }

        $stepsCreatingByUser = array();

        if (!is_null($lastTemplateStepNumber)) {
            $sql = "SELECT id FROM " . self::STEP_TABLE . " " .
                    "WHERE process_id={$this->db->sqltext($this->getId())} " .
                    "AND number > {$this->db->sqltext($lastTemplateStepNumber)}";
            $this->db->exec($sql);

            if ($this->db->num_rows() == 0) {
                return null;
            }

            $stepIds = $this->db->fetch_all();
            foreach ($stepIds as $stepId) {
                $stepInstance = new StepInstance($this->db);
                $stepInstance->setId($stepId->id);
                $stepInstance->load();
                $stepsCreatingByUser[] = $stepInstance;
            }
            $this->setStepsCreatingByUser($stepsCreatingByUser);
            return $stepsCreatingByUser;
        } else {
            //return all steps
            return $this->getSteps();
        }
    }

}
?>
