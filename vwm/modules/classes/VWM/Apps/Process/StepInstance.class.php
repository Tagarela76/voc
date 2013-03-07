<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

class StepInstance extends Step
{
	const TABLE_NAME = 'step_instance';
	const RESOURCE_TABLE = 'resource_instance';
	const TIME = 1;
	const VOLUME = 2;
	const COUNT = 3;

	protected $resources = array();

    /**
     * TODO: implement this method
     *
     * @return array property => value
     */
    public function getAttributes()
    {
        return array();
    }

	public function getTotalSpentTime()
	{
		$this->calculateTotalSpentTime();
		return $this->total_spent_time;
	}

	public function load()
	{
		parent::load(self::TABLE_NAME);
	}

	/**
	 * calculate total spent time
	 */
	private function calculateTotalSpentTime()
	{
		$resources = $this->getResources();
		$totalSpentTime = 0;

		foreach ($resources as $resource) {
			if ($resource->getResourceTypeId() == self::TIME) {
				$unitTypeConvector = new \UnitTypeConverter($this->db);
				$unitType = new \Unittype($this->db);
				$unittypeName = $unitType->getNameByID($resource->getUnittypeId());
				$qty = $unitTypeConvector->convertToDefaultTime($resource->getQty(), $unittypeName);
				$totalSpentTime+=$qty;
			}
		}
		$this->total_spent_time = $totalSpentTime;
	}

	/**
	 * function for getting step resources
	 * @return boolean|\VWM\Apps\Process\ResourceTemplate[]
	 */
	public function getResources($stepId = null)
	{
		if (!empty($this->resources)) {
			return $this->resources;
		}
		if (is_null($stepId)) {
			$stepId = $this->getId();
		}
		$sql = "SELECT * FROM " . self::RESOURCE_TABLE .
				" WHERE step_id = {$this->db->sqltext($stepId)}";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$resourcesDetails = $this->db->fetch_all_array();
		$resources = array();
		foreach ($resourcesDetails as $resourceDetails) {
			$resource = new ResourceTemplate($this->db);
			$resource->initByArray($resourceDetails);
			$resources[] = $resource;
		}
		$this->resources = $resources;
		return $resources;
	}



	protected function _insert()
	{
        $this->db->beginTransaction();
        
		$sql = "INSERT INTO " . self::TABLE_NAME . " (" .
				"number, process_id, last_update_time, description, optional" .
				") VALUES(" .
				"{$this->db->sqltext($this->getNumber())}," .
				"'{$this->db->sqltext($this->getProcessId())}'," .
				"NOW()," .
				"'{$this->db->sqltext($this->getDescription())}'," .
				"'{$this->db->sqltext($this->getOptional())}'" .
				")";

		$response = $this->db->exec($sql);

		if ($response) {
			$this->setId($this->db->getLastInsertedID());
            $resources = $this->getResources();
            
        //save Resources
        if (!empty($resources)) {
                foreach ($resources as $resource) {
                    $resource->setStepId($this->getId());
                    $resourceId = $resource->save();
                    if (!$resourceId) {
                        $this->db->rollbackTransaction();
                        return false;
                    }
                }
            }

            $this->db->commitTransaction();
            return $this->getId();
        } else {
            $this->db->rollbackTransaction();
            return false;
        }
	}

	protected function _update()
	{
		$this->db->beginTransaction();
		//delete step Resources
		$resources = $this->getResources();
		if(!empty($resources)){
			$sql = "DELETE FROM ". self::RESOURCE_TABLE ." ".
					"WHERE step_id = {$this->db->sqltext($this->getId())}";
			$response = $this->db->exec($sql);
			foreach ($resources as $resource){
				$resourceId = $resource->save();
				if(!$resourceId){
					$this->db->rollbackTransaction();
					return false;
				}
			}
		}

		$sql = "UPDATE " . self::TABLE_NAME . " SET " .
				"number={$this->db->sqltext($this->getNumber())}, " .
				"process_id='{$this->db->sqltext($this->getProcessId())}', " .
				"optional='{$this->db->sqltext($this->getOptional())}', " .
				"description='{$this->db->sqltext($this->getDescription())}', " .
				"last_update_time=NOW() " .
				"WHERE id={$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			$this->db->commitTransaction();
			return $this->getId();
		} else {
			$this->db->rollbackTransaction();
			return false;
		}
	}

	/**
	 * delete current step with its resources
	 * @param int $stepId
	 * @return boolean
	 */
	public function delete($stepId = null)
	{
		if (is_null($stepId)) {
			$stepId = $this->getId();
		}

		if (is_null($stepId)) {
			return false;
		}

		$sql = "DELETE s,r FROM " . self::TABLE_NAME . " s " .
				"LEFT JOIN " . self::RESOURCE_TABLE . " r " .
				"ON s.id = r.step_id " .
				"WHERE s.id = {$this->db->sqltext($stepId)}";
		$response = $this->db->exec($sql);
		if ($response) {
			return true;
		} else {
			return false;
		}
	}
    
    

}

?>
