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
    
    /**
     * date format for creation time
     * @var string
     */
    protected $dateFormat;
    /**
     *
     * @var string 
     */
    protected $creation_time = '';
    

	const TB_PROCESS_INSTANCE = 'process_instance';
    const TABLE_NAME = 'work_order';

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

    public function getCreationTime()
    {
        $dateFormat = $this->getDateFormat();
        $facilityId = $this->getFacilityId();
        if (!isset($dateFormat) && isset($facilityId)) {
			$this->iniDateFormat();
		} else if (!isset($dateFormat) and !isset($facilityId)) {
			throw new Exception("Date format does not exists! And department_id is not set!");
		}

		$date = date($dateFormat, $this->creation_time);
		return $date;
    }

    public function setCreationTime($creation_time)
    {
        $dateFormat = $this->getDateFormat();
        
        if (!isset($dateFormat)) {
            $this->iniDateFormat();
            $dateFormat = $this->getDateFormat();
            if (!isset($dateFormat)) {
                return;
            }
        } else if (!isset($creation_time)) {
            throw new \Exception("\$value is not set!");
        }

        /*
         * If value is already timestamp  - just set value
         */

        if (strlen($creation_time) == 10 && is_numeric($creation_time)) {
            $this->creation_time = $creation_time;
        } else {
            $dateFormat = $this->getDateFormat();
            $date = \VWM\Framework\Utils\DateTime::createFromFormat($dateFormat, $creation_time);
            if (!$date) {
                return;
            }
            $timestamp = $date->getTimestamp();
            $this->creation_time = $timestamp;
        }
        
    }

    public function getDateFormat()
    {
            return $this->dateFormat;
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
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
    
    /**
     * init date Format
     * @param type $departmentID
     * @throws Exception
     */
    private function iniDateFormat($facilityId = null) {

		$fId = $facilityId ? $facilityId : $this->facility_id;

		if (!$fId or $fId == NULL or !isset($fId)) {
			throw new \Exception("Cannot get date format for work order, because facility id is not set!");
		}

		$chain = new \TypeChain(null, 'Date', $this->db, $fId, 'facility');

		$this->dateFormat = $chain->getFromTypeController('getFormat');
	}
    
}
?>
