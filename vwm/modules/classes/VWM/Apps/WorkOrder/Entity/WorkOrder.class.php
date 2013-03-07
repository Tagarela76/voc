<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;
use VWM\Apps\Process\ProcessTemplate;
use VWM\Apps\Process\ProcessInstance;

abstract class WorkOrder extends Model
{
	/**
	 *
	 * @var int 
	 */
	protected $id;

	/**
	 *
	 * @var string 
	 */
	protected $number;

	/**
	 *
	 * @var string 
	 */
	protected $description;

	/**
	 *
	 * @var string 
	 */
	protected $customer_name;

	/**
	 *
	 * @var string 
	 */
	protected $facility_id;

	/**
	 *
	 * @var string 
	 */
	protected $status;

	/**
	 * 
	 * @var int
	 */
	protected $process_template_id = null;

	/**
	 *
	 * @var string 
	 */
	public $url;

	/**
	 * 
	 * @var \MixOptimized[]
	 */
	protected $mixes = array();
	
	/**
	 *
	 * @var VWM\Apps\Process\ProcessTemplate; 
	 */
	protected $processTemplate = null;
	
	/**
	 *
	 * @var VWM\Apps\Process\ProcessInstance; 
	 */
	protected $processInstance = null;

	const TB_PROCESS_INSTANCE = 'process_instance';

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getNumber()
	{
		return $this->number;
	}

	public function setNumber($number)
	{
		$this->number = $number;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getCustomerName()
	{
		return $this->customer_name;
	}

	public function setCustomerName($customerName)
	{
		$this->customer_name = $customerName;
	}

	public function getFacilityId()
	{
		return $this->facility_id;
	}

	public function setFacilityId($facilityId)
	{
		$this->facility_id = $facilityId;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getProcessTemplateId()
	{
		return $this->process_template_id;
	}

	public function setProcessTemplateId($processId)
	{
		$this->process_template_id = $processId;
	}

	public function setMixes($mixes)
	{
		$this->mixes = $mixes;
	}

	public function setProcessTemplate($processTemplate)
	{
		$this->processTemplate = $processTemplate;
	}

	public function setProcessInstance($processInstance)
	{
		$this->processInstance = $processInstance;
	}

		/**
	 *delete Work Order  
	 */
	public function delete()
	{

		$sql = "DELETE FROM " . TB_WORK_ORDER . "
				 WHERE id=" . $this->db->sqltext($this->getId());
		$this->db->query($sql);
	}

	/**
	 * function for getting Work Order Mixes
	 * 
	 * @return boolean|\MixOptimized[] 
	 */
	public function getMixes()
	{
		if (!empty($this->mixes)) {
			return $this->mixes;
		}
		$query = "SELECT * FROM " . TB_USAGE .
				" WHERE wo_id={$this->db->sqltext($this->id)}";
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$mixes = array();
		foreach ($rows as $row) {
			$mix = new \MixOptimized($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($mix, $key)) {
					$mix->$key = $value;
				}
			}
			$mixes[] = $mix;
		}
		$this->setMixes($mixes);
		return $mixes;
	}

	/**
	 * function for getting Work Order Process Template
	 * 
	 * @return \VWM\Apps\Process\ProcessTemplate 
	 */
	public function getProcessTemplate()
	{
		if(!is_null($this->processTemplate)){
			return $this->processTemplate;
		}
		$processTemplate = new ProcessTemplate($this->db, $this->getProcessTemplateId());
		$this->setProcessTemplate($processTemplate);
		return $processTemplate;
	}

	/**
	 * function for getting Work Order Process Instance
	 * 
	 * @return boolean|\VWM\Apps\Process\ProcessInstance 
	 */
	public function getProcessInstance()
	{
		if(!is_null($this->processInstance)){
			return $this->processInstance;
		}
		
		$sql = "SELECT id FROM " . self::TB_PROCESS_INSTANCE . " " .
				"WHERE work_order_id = {$this->db->sqltext($this->getId())} LIMIT 1";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$result = $this->db->fetch(0);
		$processInstance = new ProcessInstance($this->db, $result->id);
		
		$this->setProcessInstance($processInstance);
		return $processInstance;
	}
    
}
?>
