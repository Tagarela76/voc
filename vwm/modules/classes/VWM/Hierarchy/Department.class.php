<?php

namespace VWM\Hierarchy;

use VWM\Framework\Model;
use VWM\Apps\Gauge\Entity\Gauge;
use VWM\Apps\Gauge\Entity\SpentTimeGauge;
use VWM\Apps\Gauge\Entity\QtyProductGauge;
use VWM\Apps\Gauge\Entity\NoxGauge;
use VWM\Apps\Gauge\Entity\VocGauge;


class Department extends Model {

	protected $department_id;
	protected $name;
	protected $facility_id;
	protected $creater_id;
	protected $voc_limit;
	protected $voc_annual_limit;
	protected $share_wo;

	/**
	 *
	 * @var Facility
	 */
	protected $facility;

	/**
	 * @var \PfpTypes[]
	 */
	protected $pfpTypes;
	
	/*
	 * name of unit_class for getUnitTypeList function
	 * USAWght for default
	 * @var string 
	 */
	protected $unitTypeClass = 'USAWght';
	
	public $searchCriteria = array();

	const TABLE_NAME = 'department';
	const TB_UNITTYPE = 'unittype';
	const TB_DEFAULT = '`default`';
	const TB_TYPE = 'type';
	const TB_UNITCLASS = 'unit_class';
	const CATEGORY = 'department';

	public function __construct(\db $db, $departmentId = null) {
		$this->db = $db;
		if (isset($departmentId)) {
			$this->setDepartmentId($departmentId);
			$this->load();
		}
	}

	public function load() {
		if (is_null($this->getDepartmentId())) {
			return false;
		}

		$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE department_id =" .
				$this->db->sqltext($this->getDepartmentId());
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
	}

	public function getDepartmentId() {
		return $this->department_id;
	}

	public function setDepartmentId($department_id) {
		$this->department_id = $department_id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getFacilityId() {
		return $this->facility_id;
	}

	public function setFacilityId($facility_id) {
		$this->facility_id = $facility_id;
	}

	public function getCreaterId() {
		return $this->creater_id;
	}

	public function setCreaterId($creator_id) {
		$this->creater_id = $creator_id;
	}

	public function getVocLimit() {
		return $this->voc_limit;
	}

	public function setVocLimit($voc_limit) {
		$this->voc_limit = $voc_limit;
	}

	public function getVocAnnualLimit() {
		return $this->voc_annual_limit;
	}

	public function setVocAnnualLimit($voc_annual_limit) {
		$this->voc_annual_limit = $voc_annual_limit;
	}

	public function getShareWo() {
		return $this->share_wo;
	}

	public function setShareWo($shareWo) {
		$this->share_wo = $shareWo;
	}
	
	public function getUnitTypeClass() {
		return $this->unitTypeClass;
	}

	public function setUnitTypeClass($unitTypeClass) {
		$this->unitTypeClass = $unitTypeClass;
	}

	
	public function getGauge($gaugeType) {
		switch ($gaugeType) {
			case Gauge::QUANTITY_GAUGE:
				$gauge = new QtyProductGauge($this->db);
				break;
			case Gauge::TIME_GAUGE:
				$gauge = new SpentTimeGauge($this->db);
				break;
			case Gauge::VOC_GAUGE:
				break;
			default:
				break;
		}

		$gauge->setDepartmentId($this->department_id);
		$gauge->setFacilityId($this->facility_id);
		$gauge->load();
		return $gauge;
	}

	public function getAllAvailableGauges() {
		$sql = "SELECT gauge_type FROM " . QtyProductGauge::TABLE_NAME . " WHERE `limit`<>0 AND department_id=" . $this->db->sqltext($this->getDepartmentId());
		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		$gauges = array();
		foreach ($rows as $row) {
			switch ($row["gauge_type"]) {
				case Gauge::QUANTITY_GAUGE:
					$gauge = new QtyProductGauge($this->db);
					break;
				case Gauge::TIME_GAUGE:
					$gauge = new SpentTimeGauge($this->db);
					break;
				case Gauge::VOC_GAUGE:
					$gauge = new VocGauge($this->db);
					break;
				case Gauge::NOX_GAUGE:
					$gauge = new NoxGauge($this->db);
					break;
				default:
					break;
			}
			$gauge->setDepartmentId($this->department_id);
			$gauge->setFacilityId($this->facility_id);
			$gauge->load();

			$gauges[] = $gauge;
		}
		return $gauges;
	}

	public function countRepairOrderInDepartment() {

		$sql = "SELECT count(*) repairOrderCount FROM " . TB_WORK_ORDER . " w " .
				"JOIN " . TB_WO2DEPARTMENT . " dw " .
				"ON w.id=dw.wo_id " .
				"WHERE department_id=" . $this->db->sqltext($this->department_id);
		if (count($this->searchCriteria) > 0) {
			$searchSql = array();
			$sql .= " AND ( ";
			foreach ($this->searchCriteria as $repairOrder) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($repairOrder) . "%')";
			}
			$sql .= implode(' OR ', $searchSql);
			$sql .= ") ";
		}
		$this->db->query($sql);
		if ($this->db->num_rows() > 0) {
			return (int) $this->db->fetch(0)->repairOrderCount;
		} else {
			return false;
		}
	}

	public function getRepairOrdersList(Pagination $pagination = null) {

		$repairOrders = array();
		
		$sql =  "SELECT w.* FROM " . TB_WORK_ORDER . " w ".
				"JOIN ". TB_WO2DEPARTMENT." dw ".
				"ON w.id=dw.wo_id ".
				"WHERE department_id=".$this->db->sqltext($this->department_id);
	
		if(count($this->searchCriteria) > 0) {
			$searchSql = array();
			$sql .= " AND ( ";
			foreach ($this->searchCriteria as $repairOrder) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($repairOrder) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($repairOrder) . "%')";
			}
			$sql .= implode(' OR ', $searchSql);
			$sql .= ") ";
		}

		$sql .= " ORDER BY dw.id DESC";

		if (isset($pagination)) {
			$sql .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		$this->db->query($sql);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}

		foreach ($rows as $row) {
			$repairOrder = new \RepairOrder($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($repairOrder, $key)) {
					$repairOrder->$key = $value;
				}
			}
			$repairOrders[] = $repairOrder;
		}
		return $repairOrders;
	}

	protected function _insert() {

		$lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";

		$query = "INSERT INTO " . self::TABLE_NAME . " (" .
				"name, facility_id, creater_id, voc_limit, voc_annual_limit, share_wo, last_update_time " .
				") VALUES ( " .
				"'{$this->db->sqltext($this->getName())}', " .
				"{$this->db->sqltext($this->getFacilityId())}, " .
				"'{$this->db->sqltext($this->getCreaterId())}', " .
				"'{$this->db->sqltext($this->getVocLimit())}', " .
				"'{$this->db->sqltext($this->getVocAnnualLimit())}', " .
				"'{$this->db->sqltext($this->getShareWo())}', " .
				"{$lastUpdateTime} " .
				")";
		$response = $this->db->exec($query);
		if ($response) {
			$this->setDepartmentId($this->db->getLastInsertedID());
			return $this->getDepartmentId();
		} else {
			return false;
		}
	}

	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime()) ? "'{$this->getLastUpdateTime()}'" : "NULL";

		$query = "UPDATE " . self::TABLE_NAME . " SET " .
				"name='" . $this->db->sqltext($this->getName()) . "', " .
				"facility_id=" . $this->db->sqltext($this->getFacilityId()) . ", " .
				"creater_id=" . $this->db->sqltext($this->getCreaterId()) . ", " .
				"voc_limit=" . $this->db->sqltext($this->getVocLimit()) . ", " .
				"voc_annual_limit=" . $this->db->sqltext($this->getVocAnnualLimit()) . ", " .
				"last_update_time=" . $lastUpdateTime . ", " .
				"share_wo=" . $this->db->sqltext($this->getShareWo()) .
				" WHERE department_id=" . $this->db->sqltext($this->getDepartmentId());


		$response = $this->db->exec($query);
		if ($response) {
			return $this->department_id;
		} else {
			return false;
		}
	}

	public function save() {
		$this->setLastUpdateTime(date(MYSQL_DATETIME_FORMAT));

		if ($this->department_id) {
			return $this->_update();
		} else {
			return $this->_insert();
		}
	}

	public function getMixList(\Pagination $pagination = null, $filter = ' TRUE ', $sort=' ORDER BY m.mix_id DESC ') {
		$woDescriptionField = 'woDescription';
		$query = "SELECT m.*, wo.id, wo.customer_name, wo.description {$woDescriptionField}, wo.vin  " .
				"FROM " . TB_USAGE . " m " .
				" LEFT JOIN ".TB_WORK_ORDER." wo ON m.wo_id = wo.id " .
				"LEFT JOIN " . TB_WO2DEPARTMENT . " j ON m.wo_id=j.wo_id " .
				"WHERE (m.department_id ={$this->db->sqltext($this->getDepartmentId())} " .
				"OR j.department_id={$this->db->sqltext($this->getDepartmentId())}) " .
				" AND {$filter} ";

		if (count($this->searchCriteria) > 0) {
			$searchSql = array();
			$query .= " AND ( ";
			foreach ($this->searchCriteria as $mixCriteria) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($mixCriteria) . "%') " .
						"OR m.description LIKE ('%" . $this->db->sqltext($mixCriteria) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($mixCriteria) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($mixCriteria) . "%')";
			}
			$query .= implode(' OR ', $searchSql);
			$query .= ") ";
		}

		$query.= " GROUP BY m.mix_id";

		$query .= $sort;

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		if (!$this->db->query($query)) {
			throw new \Exception('SQL query failed.');
		}
		
		$rows = $this->db->fetch_all_array();
		
		$mixes = array();
		foreach($rows as $row) {
			$mix = new \MixOptimized($this->db);			
			foreach ($row as $key => $value) {				
				if (property_exists($mix, $key)) {
					$mix->$key = $value;
				}
			}
			
			if($mix->wo_id !== null) {
				$repairOrder = new \RepairOrder($this->db);
				//	overrite mix description just because both mix and work order
				//	have field description
				$row['description'] = $row['woDescription'];				
				$repairOrder->initByArray($row);
				$mix->setRepairOrder($repairOrder);
			}
			$mixes[] = $mix;
		}

		return $mixes;
	}



	/**
	 * Get department's facility
	 * @return \VWM\Hierarchy\Facility
	 * @throws \Exception
	 */
	public function getFacility() {
		if($this->facility === null) {
			if(!$this->getFacilityId()) {
				throw new \Exception('Facility Id is not set');
			}
				
			$this->facility = new Facility($this->db, $this->getFacilityId());	
		}	

		return $this->facility;
	}

	public function getCountMix() {
		$query = "SELECT count(*) mixCount FROM " . TB_USAGE . " m " .
				"LEFT JOIN " . TB_WO2DEPARTMENT . " j ON m.wo_id=j.wo_id " .
				"WHERE m.department_id =" . $this->db->sqltext($this->department_id) . " " .
				"OR j.department_id=" . $this->db->sqltext($this->department_id);

		if (count($this->searchCriteria) > 0) {
			$searchSql = array();
			$query .= " AND ( ";
			foreach ($this->searchCriteria as $mixCriteria) {
				$searchSql[] = " number LIKE ('%" . $this->db->sqltext($mixCriteria) . "%') " .
						"OR description LIKE ('%" . $this->db->sqltext($mixCriteria) . "%') " .
						"OR customer_name LIKE ('%" . $this->db->sqltext($mixCriteria) . "%') " .
						"OR vin LIKE ('%" . $this->db->sqltext($mixCriteria) . "%')";
			}
			$query .= implode(' OR ', $searchSql);
			$query .= ") ";
		}


		if (!$this->db->query($query)) {
			throw new Exception('SQL query failed.');
		}

		if ($this->db->num_rows() > 0) {
			return (int) $this->db->fetch(0)->mixCount;
		} else {
			return false;
		}
	}


	public function getPfpTypes() {
		if($this->pfpTypes === null) {
			$sql = "SELECT pfp_t.* " .
					"FROM ".TB_PFP_TYPES." pfp_t " .
					"JOIN ".\PfpTypes::TB_PFP_2_DEPARTMENT." pfp_t2d " .
					"ON pfp_t.id = pfp_t2d.pfp_type_id " .
					"WHERE pfp_t2d.department_id = {$this->db->sqltext($this->getDepartmentId())}";
			$this->db->query($sql);

			if($this->db->num_rows() == 0) {
				$this->pfpTypes = array();
				return $this->pfpTypes;
			}

			$rows = $this->db->fetch_all_array();
			foreach ($rows as $row) {
				$pfpType = new \PfpTypes($this->db);
				//TODO: switch to init by array
				$pfpType->id = $row['id'];
				$pfpType->name = $row['name'];
				$pfpType->facility_id = $row['facility_id'];
				$this->pfpTypes[] = $pfpType;
			}
		}

		return $this->pfpTypes;
	}
	
	public function getUnitTypeList() {
		
		$unitTypeCollection = new \VWM\Apps\UnitType\UnitTypeCollection();
		$unitTypesName = array();
		$unitTypes = array();
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				 "ut.unittype_desc, ut.system, uc.name " .
				 "FROM " . self::TB_UNITTYPE ." ut ". 
				 "INNER JOIN " . self::TB_TYPE ." t ".
				 "ON ut.type_id = t.type_id ".
				 "INNER JOIN " . self::TB_DEFAULT ." def ".
				 "ON ut.unittype_id = def.id_of_subject ".
				 "INNER JOIN " . self::TB_UNITCLASS ." uc ".
				 "ON ut.unit_class_id = uc.id ".
				 "WHERE def.object = '" .self::CATEGORY."' ".
				 "AND def.id_of_object = {$this->db->sqltext($this->getDepartmentId())} ".
			     "AND def.subject = 'unittype' ".
				 "ORDER BY ut.unittype_id";
		
		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				
				$unittype = new \VWM\Apps\UnitType\Entity\UnitType();
				$unittype->initByArray($data);
				$unittypes[] = $unittype;
				
			}
		} else {
			$facility = $this->getFacility();
			return $facility->getUnitTypeList();
		}
		foreach($unittypes as $unitType){
				$type = array(
				'unittype_id' => $unitType->getUnitTypeId(),
				'type_id' => $unitType->getTypeId(),
				'name' => $unitType->getName()
				);
				$unitTypes[] = $type;
				if(!in_array($unitType->getName(), $unitTypesName)){
					$unitTypesName[] = $unitType->getName();
				}
		}
		$unitTypeCollection->setUnitTypeClases($unittypes);
		$unitTypeCollection->setUnitTypes($unitTypes);
		$unitTypeCollection->setUnitTypeNames($unitTypesName);
		
		return $unitTypeCollection;
		
	}
	
	public function getDefaultAPMethod(){
		
		$query ="SELECT apm.apmethod_id, apm.apmethod_desc"; 
		$query.=" FROM ".TB_DEFAULT." def, ".TB_APMETHOD." apm WHERE def.id_of_object={$this->db->sqltext($this->getDepartmentId())}";
		$query.= " AND apm.apmethod_id=def.id_of_subject";
		$query.=" AND def.subject='apmethod'";
		$query.=" AND def.object='" .self::CATEGORY."'";
		
		$this->db->query($query);
		if ($this->db->num_rows()) {
			for ($j=0; $j < $this->db->num_rows(); $j++) {
				$data=$this->db->fetch($j);				
				$apmethod=array (
					'apmethod_id'			=>	$data->apmethod_id,
					'description'			=>	$data->apmethod_desc
				);	
				$apmethods[]=$apmethod;				
			}
		}else{
			$facility = $this->getFacility();
			$apmethods = $facility->getDefaultAPMethod();
		} 
		
		return $apmethods;
	}
	
	
	public function getOldUnitTypeList() {
		
		$unitTypeCollection = new \VWM\Apps\UnitType\UnitTypeCollection();
		$unitTypesName = array();
		$unitTypes = array();
		$query = "SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, " .
				 "ut.unittype_desc, ut.system, uc.name " .
				 "FROM " . self::TB_UNITTYPE ." ut ". 
				 "INNER JOIN " . self::TB_TYPE ." t ".
				 "ON ut.type_id = t.type_id ".
				 "INNER JOIN " . self::TB_DEFAULT ." def ".
				 "ON ut.unittype_id = def.id_of_subject ".
				 "INNER JOIN " . self::TB_UNITCLASS ." uc ".
				 "ON ut.unit_class_id = uc.id ".
				 "WHERE def.object = '" .self::CATEGORY."' ".
				 "AND def.id_of_object = {$this->db->sqltext($this->getDepartmentId())} ".
			     "AND def.subject = 'unittype' ".
				 "ORDER BY ut.unittype_id";
		
		$this->db->query($query);

		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				
				$unittype = array(
					'unittype_id' => $data->unittype_id,
					'description' => $data->name,
					'type_id' => $data->type_id,
					'type' => $data->type_desc,
					'unittype_desc' => $data->unittype_desc,
					'system' => $data->system,
					'name' => $data->name
				);
				$unittypes[] = $unittype;
				
			}
		} else {
			$facility = $this->getFacility();
			$facility->setUnitTypeClass($this->getUnitTypeClass());
			$unittypes = $facility->getOldUnitTypeList();
		}
		
		
		return $unittypes;
	}
}

?>
