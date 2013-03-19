<?php

namespace VWM\Apps\Process;

use VWM\Framework\Model;

abstract class Resource extends Model
{
	/**
	 * resource id
	 * @var int
	 */
	protected $id;

	/**
	 * description 
	 * @var string
	 */
	protected $description;

	/**
	 * rate
	 * @var float
	 */
	protected $rate;

	/**
	 * qty
	 * @var float
	 */
	protected $qty;

	/**
	 * unittype_id
	 * @var float
	 */
	protected $unittype_id;

	/**
	 * resource type id
	 * @var int (TIME = 1, VOLUME = 2, COUNT=3)
	 */
	protected $resource_type_id;

	/**
	 * labor_cost
	 * @var float
	 */
	protected $labor_cost = 0;

	/**
	 * material_cost
	 * @var float
	 */
	protected $material_cost = 0;

	/**
	 * total_cost
	 * @var float
	 */
	protected $total_cost = NULL;

	/**
	 * rate_unittype_id
	 * @var float
	 */
	protected $rate_unittype_id;

	/**
	 * rate_qty
	 * @var int
	 */
	protected $rate_qty = 1;

	/**
	 * step id
	 * @var int
	 */
	protected $step_id;

	const TABLE_NAME = 'resource_template';
	
	const TIME = 1;
	const VOLUME = 2;
	const GOM = 3;
	
	//Realy class id 
	const TIME_UNIT_CLASS = 7;
	const GOM_UNIT_CLASS = 8;
	const VOLUME_UNIT_CLASS = 1;
    
    //default values
    const DEFAULT_TIME_TYPE_ID = 38;
    const DEFAULT_GOM_TYPE_ID = 40;
    const DEFAULT_VULUME_TYPE_ID = 1;
    const LINER_FEET_TYPE_ID = 41;
    const SQUARE_FEET_TYPE_ID = 44;
    

	public function __construct(\db $db, $Id = null)
	{
		$this->db = $db;
		$this->modelName = "Resource";
		if (isset($Id)) {
			$this->setId($Id);
			$this->load();
		}
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getRate()
	{
		return $this->rate;
	}

	public function setRate($rate)
	{
		$rate = $this->validateCount($rate);
		$this->rate = $rate;
	}

	public function getQty()
	{
		return $this->qty;
	}

	public function setQty($qty)
	{
		$qty = $this->validateCount($qty);
		$this->qty = $qty;
	}

	public function getUnittypeId()
	{
		return $this->unittype_id;
	}

	public function setUnittypeId($unittype_id)
	{
		$this->unittype_id = $unittype_id;
	}

	public function getResourceTypeId()
	{
		return $this->resource_type_id;
	}

	public function setResourceTypeId($resource_type_id)
	{
		$this->resource_type_id = $resource_type_id;
	}

	public function getLaborCost()
	{
		return $this->labor_cost;
	}

	public function setLaborCost($labor_cost)
	{
		$this->labor_cost = $labor_cost;
	}

	public function getMaterialCost()
	{
		return $this->material_cost;
	}

	public function setMaterialCost($material_cost)
	{
		$this->material_cost = $material_cost;
	}

	public function getTotalCost()
	{
		if ($this->total_cost === null) {
			$this->calculateTotalCost();
		}
		return $this->total_cost;
	}

	public function setTotalCost($total_cost)
	{
		$this->total_cost = $this->validateCount($total_cost);
	}

	public function getRateUnittypeId()
	{
		return $this->rate_unittype_id;
	}

	public function setRateUnittypeId($rate_unittype_id)
	{
		$this->rate_unittype_id = $rate_unittype_id;
	}

	public function getRateQty()
	{
		return $this->rate_qty;
	}

	public function setRateQty($rate_qty)
	{
		$rate_qty = $this->validateCount($rate_qty);
		$this->rate_qty = $rate_qty;
	}

	public function getStepId()
	{
		return $this->step_id;
	}

	public function setStepId($step_id)
	{
		$this->step_id = $step_id;
	}

	/**
	 * function for calculate total cost 
	 */
	public function calculateTotalCost()
	{
		if ($this->getMaterialCost() == 0 && $this->getLaborCost() == 0) {
			$this->countCost();
		}
		$totalCost = $this->getMaterialCost() + $this->getLaborCost();
		$this->setTotalCost($totalCost);
	}

	/**
	 * function for calculate labor or material cost for certain unit type
	 */
	public function countCost()
	{
        //array of additional unitType Description for GOM
        $additionalUnitTypes = array('LS', 'LF', 'SF');
		$unitTypeConvector = new \UnitTypeConverter($this->db);
		$unitType = new \Unittype($this->db);
		$from = $unitType->getNameByID($this->unittype_id);
		$to = $unitType->getNameByID($this->rate_unittype_id);
		$value = $this->qty;

		switch ($this->resource_type_id) {
			case self::TIME:
                //set default value if null
                if(is_null($this->rate_unittype_id)){
                    $this->setRateUnittypeId(self::DEFAULT_TIME_TYPE_ID);
                    $to = $unitType->getNameByID($this->rate_unittype_id);
                }
				$qty = $unitTypeConvector->convertTimeFromTo($from, $to, $value);
				$rate = $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty * $rate) / $rateQty;
				$this->setLaborCost($laborCost);
				break;
			case self::VOLUME:
                //set default value if null
                if (is_null($this->rate_unittype_id)) {
                    $this->setRateUnittypeId(self::DEFAULT_VULUME_TYPE_ID);
                    $to = $unitType->getNameByID($this->rate_unittype_id);
                }
				if ($to == 'LS' || $from == 'LS') {
					$qty = $value;
				} else {
					$qty = $unitTypeConvector->convertFromTo($value, $from, $to);
				}
				$rate = $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty * $rate) / $rateQty;
				$this->setMaterialCost($laborCost);
				break;
			case self::GOM:
                
                //set default value if null
                if (is_null($this->rate_unittype_id)) {
                    $this->setRateUnittypeId(self::DEFAULT_GOM_TYPE_ID);
                    $to = $unitType->getNameByID($this->rate_unittype_id);
                }
				if ( in_array($to, $additionalUnitTypes) || in_array($from, $additionalUnitTypes)) {
					$qty = $value;
				} else {
					$qty = $unitTypeConvector->convertCountFromTo($from, $to, $value);
				}
				$rate = $this->getRate();
				$rateQty = $this->getRateQty();
				$laborCost = ($qty * $rate) / $rateQty;
				$this->setMaterialCost($laborCost);
				break;
			default :
				throw new \Exception('unccorect resource type');
				break;
		}
	}

	/**
	 * function for correct setter validate
	 * @return $value
	 */
	private function validateCount($value)
	{
		$value = ereg_replace(',', '.', $value);
		return $value;
	}
	
	/**
	 * function for getting all available resource types
	 * @return string[] 
	 */
	public function getResourceTypes(){
		return array(
			self::TIME => 'TIME',
			self::VOLUME => 'VOLUME',
			self::GOM => 'GOM'
		); 
	}
	
	/**
	 * function for getting resource unit types
	 * @param int $resourceType
	 * @param int $deparmentId
	 * 
	 * @return \VWM\Apps\UnitType\Entity\UnitType[]
	 * @throws \Exception 
	 */
	public function getResourceUnitTypeByResourceType($resourceType,$departmentId = null)
	{
		$db = \VOCApp::getInstance()->getService('db');
		
		$utManager = new \VWM\Apps\UnitType\Manager\UnitTypeManager($db);
		$unitClass = new \VWM\Apps\UnitType\Entity\UnitClass($db);
		
		switch ($resourceType) {
			case self::TIME:
				$unitClass->setId(self::TIME_UNIT_CLASS);
				break;
			case self::GOM:
				$unitClass->setId(self::GOM_UNIT_CLASS);
				break;
			case self::VOLUME:
				$unitClass->setId(self::VOLUME_UNIT_CLASS);
				break;
			default :
				throw new \Exception('unknown resource type');
				break;
		}
		$unitClass->load();
		
		if(is_null($deparmentId)){
			$unitTypes = null;
		}else{
			$department = new \VWM\Hierarchy\Department($db, $departmentId);
			$unitTypes = $department->getUnitTypeList();
		}
		
		$unitTypeList = $utManager->getUnitTypeListByUnitClass($unitClass->getDescription(), $unitTypes);
        //add new resources for GOM
        if($resourceType==self::GOM){
            //add LF Type to GOM Resource
            $unitType = new \VWM\Apps\UnitType\Entity\UnitType($db);
            $unitType->setUnitTypeId(self::LINER_FEET_TYPE_ID);
            $unitType->load();
            $unitTypeList[] = $unitType;
            
            //add LF Type to GOM Resource
            $unitType = new \VWM\Apps\UnitType\Entity\UnitType($db);
            $unitType->setUnitTypeId(self::SQUARE_FEET_TYPE_ID);
            $unitType->load();
            $unitTypeList[] = $unitType;
        }
		return $unitTypeList;
		
	}

}

?>
