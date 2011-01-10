<?php

class ReductionScheme {
	
	private $db;
	
	const DEF_UNITTYPE_ID = 2;
	const DEFAULT_ARE = 1.5;
	const DEFAULT_TE = 0.25;
	
	public $solid;
	public $are;
	public $targetEmission;
	public $unittypeID;
	public $errorsList;	
	private $factorARE;
	private $factorTargetEmission;

    function ReductionScheme($db) {
    	$this->db = $db;
    }
    
    public function getAREfactor() {
    	return $this->factorARE;
    }
    
    public function getTargetEmissionFactor() {
    	return $this->factorTargetEmission;
    }
    
    public function loadFactors($facilityID) {
    	$query = "SELECT * FROM ".TB_REDUCTION." WHERE facility_id = '$facilityID' LIMIT 0, 1 ";
    	$this->db->query($query);
    	if ($this->db->num_rows()>0) {
    		$data = $this->db->fetch(0);
    		$this->factorARE = $data->factor_are;
    		$this->factorTargetEmission = $data->factor_te;
    	} else {
    		$this->factorARE = self::DEFAULT_ARE;
    		$this->factorTargetEmission = self::DEFAULT_TE;
    	}
    }
    
    public function setFactors($facilityID,$newAREfactor = null,$newTEfactor = null) {
    	$query = "SELECT * FROM ".TB_REDUCTION." WHERE facility_id = '$facilityID' LIMIT 0, 1 ";
    	$this->db->query($query);
    	if ($this->db->num_rows()>0) {
    		$query = "UPDATE ".TB_REDUCTION." SET ";
    		if ($newAREfactor != null) {
    			$query .= " factor_are = '$newAREfactor',";
    		}
    		if ($newTEfactor != null) {
    			$query .= " factor_te = '$newTEfactor' ,";
    		}
    		$query = substr($query,0,-1). " WHERE facility_id = '$facilityID' ";
    	} else {
    		if ($newAREfactor == null) {
    			$newAREfactor = self::DEFAULT_ARE;
    		}
    		if ($newTEfactor == null) {
    			$newTEfactor = self::DEFAULT_TE;
    		}
    		$query = "INSERT INTO reduction (`id` , `facility_id` , `factor_are` , `factor_te`) " .
    				"VALUES (NULL , '$facilityID', '$newAREfactor', '$newTEfactor');";
    	}
    	$this->db->query($query);
    }
    
    public function calculateReduction($facilityID, $dateBegin = null, $dateEnd = null) {
    	$this->loadFactors($facilityID);
    	$facility = new Facility($this->db);
    	$solids = $facility->calculateSolidsMass($facilityID,$dateBegin,$dateEnd);
    	$unittypeConverter = new UnitTypeConverter();
    	$this->solid = $unittypeConverter->convertFromTo($solids['value'],self::DEF_UNITTYPE_ID,$this->unittypeID);
    	$this->errorsList = $solids['errors'];
    	$this->are = $unittypeConverter->convertFromTo($this->factorARE * $this->solid,self::DEF_UNITTYPE_ID,$this->unittypeID);
    	$this->targetEmission = $unittypeConverter->convertFromTo($this->factorTargetEmission * $this->are,self::DEF_UNITTYPE_ID,$this->unittypeID);    	
    }
    
    public function getReductionScheme($facilityID) {
    	$facility = new Facility($this->db);
    	$dateBegin = $this->getBeginDate($facilityID);
    	$year = substr(date("Y-m-d",strtotime($dateBegin)),0,4);
    	$yearCur = substr(date("Y-m-d",time()),0,4);
    	$monthDay = "-01-01";// year is fron 1st January of its year to next 1st January
    	$reduction = array();
    	for($i = $yearCur; $i >= $year; $i--) {
    		$this->calculateReduction($facilityID,$i.$monthDay,($i+1).$monthDay);
    		$reduction []= array(
    			'year' => $i,
    			'weightOfSolid' => round($this->solid,2),
    			'ARE' => round($this->are,2),
    			'targetEmission' => round($this->targetEmission,2)    			
    		); 
    	}
    	return $reduction;
    }
    
    public function getBeginDate($facilityID) {
    	$query = "SELECT creation_time FROM ".TB_USAGE." WHERE department_id IN(SELECT department_id FROM ".TB_DEPARTMENT." WHERE facility_id = '$facilityID') ORDER BY creation_time ASC LIMIT 0 , 1 ";
    	$this->db->query($query);
    	$data = $this->db->fetch(0);
    	$dateBegin = $data->creation_time;
//    	$department = new Department($this->db);
//    	$departmentList = $department->getDepartmentListByFacility($facilityID);
//    	$dateBegin = null;
//    	foreach($departmentList as $department) {
//    		$query = " SELECT creation_time FROM ".TB_USAGE." WHERE department_id ='".$department['id']."' ORDER BY creation_time ASC LIMIT 0 , 1 ";
//    		
//    		$this->db->query($query);
//    		$data = $this->db->fetch(0);
//    		if (($dateBegin > $data->creation_time && $data->creation_time != null) || $dateBegin == null) {
//    			$dateBegin = $data->creation_time;
//    		}
//    	}
    	if ($dateBegin == null) {
    		$dateBegin = date("Y-m-d",time());
    	}
    	return $dateBegin;
    }
}
?>