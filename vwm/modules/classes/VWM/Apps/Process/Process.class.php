<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Process extends Model
{

	/**
	 * process id 
	 * @var int
	 */
	protected $id = NULL;

	/**
	 * process facility_id
	 * @var int
	 */
	protected $facility_id = NULL;

	/**
	 * process name
	 * @var string
	 */
	protected $name;

	/**
	 * process type
	 * 
	 */
	protected $process_type;

	/**
	 * work order id
	 * @var int
	 */
	protected $work_order_id;

	/**
	 * @var int
	 */
	protected $current_step_number;

	/**
	 * process steps
	 * $var \VWM\Apps\Process\Step[]
	 */
	protected $process_steps = array();

	public function __construct(\db $db, $id = null)
	{
		$this->db = $db;
		if (isset($id)) {
			$this->setId($id);
			$this->load();
		}
	}

	public function getProcessType()
	{
		return $this->process_type;
	}

	public function setProcessType($processType)
	{
		$this->process_type = $processType;
	}

	/**
	 * get and set steps for initialization
	 * @return \VWM\Apps\Process\StepTemplate[] 
	 */
	public function getProcessSteps()
	{
		return $this->process_steps;
	}

	/**
	 * @param \VWM\Apps\Process\StepTemplate[] 
	 */
	public function setProcessSteps($processSteps)
	{
		$this->process_steps = $processSteps;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getFacilityId()
	{
		return $this->facility_id;
	}

	public function setFacilityId($facilityId)
	{
		$this->facility_id = $facilityId;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getWorkOrderId()
	{
		return $this->work_order_id;
	}

	public function setWorkOrderId($workOrderId)
	{
		$this->work_order_id = $workOrderId;
	}

	public function getCurrentStepNumber()
	{
		return $this->current_step_number;
	}

	public function setCurrentStepNumber($currentStepNumber)
	{
		$this->current_step_number = $currentStepNumber;
	}

}

?>
