<?php

	/**
	 * 
	 * Usage:
	 * 
	 * //	get solvents for 1 month
	 * $SM = new SolventManagement($db, $facilityID);
	 * $january2010Solvents = $SM->getMonthlyOutputs(1, 2010);
	 * 
	 * //	get solvents for 2010
	 * $january2010Solvents = $SM->getAnnualyOutputs(2010);
	 * 
	 * //	edit monthly
	 * $january2010Solvents = $SM->getMonthlyOutputs(1, 2010);
	 * $january2010Solvents->o1 = 43;
	 * if (!$january2010Solvents->save()) {
	 * 		return "Input doesn't equal output";
	 * }
	 * 
	 * //	edit mix for last month
	 * ...
	 * $SM = new SolventManagement($db, $facilityID);
	 * $last2010Solvents = $SM->getMonthlyOutputs(1, 2010);
	 * $last2010Solvents->o1 = $last2010Solvents->o1 - $oldVOC + $newVOC;	//	всю разницу фигачим в o1
	 * if (!$last2010Solvents->save()) {
	 * 		throw new Exception("Input doesn't equal output");
	 * }
	 */
	class SolventManagement {
		
		private $db;
										
		public $facilityID;
		public $mm;
		public $yyyy;
		public $o1;
		public $o2;
		public $o3;
		public $o4;
		public $o5;
		public $o6;
		public $o7;
		public $o8;
		public $o9;
		
		public $consumption;
		public $fugitiveEmission;
		public $annualActualSolventEmission;
		
		//	check insert or update
		public $isAlreadyExist = false;
		
		/**
		 * 
		 * o1+o2+o3+o4+o5+o6+o7+o8+o9
		 * @var float
		 */
		public $totalOutput;
		
		public $outputNames = array();
		
		
		
		
		function SolventManagement($db, $facilityID = null) {
			$this->db = $db;
			if (!is_null($facilityID)) {
				$this->facilityID = $facilityID;				
			}			
		}
		
		
		
		
		public function getTotalInput() {
			return $this->totalOutput;
		}
		
		
		
		
		public function getMonthlyOutputs($mm, $yyyy) {	
			
			$this->mm = $mm;
			$this->yyyy = $yyyy;
								
			$mm = (int)mysql_real_escape_string($mm);
			$yyyy = (int)mysql_real_escape_string($yyyy);
						
			//	input for current month is changing all the time
			//	so allow to edit ONLY outputs from past (previos month)
			$currentDate = getdate();
			if ($currentDate['year'] < $yyyy ) return false;	//	user asks for future year			
			if ($currentDate['year'] == $yyyy && $currentDate['mon'] <= $mm) return false;	//	user asks for current/future month			 			
			
			$this->isAlreadyExist = true;
			
			$query = "SELECT output_id, value " .
					"FROM ".TB_SOLVENT_MANAGEMENT." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND month = ".$mm." " .
					"AND year = ".$yyyy;
			
			return $this->_formOutputs($query);
		}

		
		
		
		public function getQuarterlyOutputs($quarter, $yyyy) {			
			//	no no no, we should null month & year just in case
			$this->mm = null;
			$this->yyyy = null;
			
			//	convert from quarter to monthes			
			$possibleQuarters = array(1,2,3,4);			
			foreach ($possibleQuarters as $possibleQuarter) {
				if ($quarter == $possibleQuarter) {
					$mm = array ($quarter*3 - 2, $quarter*3 - 1, $quarter*3);
					break;	//	we found it
				}
			}	
						
			//	$quarter - incorrect input 
			if (!isset($mm)) return false;

			//	input for current month is changing all the time
			//	so allow to edit ONLY outputs from past (previos month)
			$currentDate = getdate();
			$currentQuater = ceil($currentDate['mon']/3);			
			if ($currentDate['year'] < $yyyy ) return false;	//	user asks for future year			
			if ($currentDate['year'] == $yyyy && $currentQuater < $quarter) return false;	//	user asks for future quarter
			
			//	protect from injections
			$yyyy = mysql_real_escape_string($yyyy);
			
			//	we should group result
			$query = "SELECT sum(value) value, output_id " .
					"FROM ".TB_SOLVENT_MANAGEMENT." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND month IN (".implode(', ',$mm).") " .
					"AND year = ".$yyyy." " .
					"GROUP BY output_id";					

			return $this->_formOutputs($query);					
		}

		
		
		
		public function getSemiAnnualyOutputs($period, $yyyy) {		
			//	no no no, we should null month & year just in case
			$this->mm = null;
			$this->yyyy = null;
				
			//	convert semiannual period to monthes
			switch ($period) {
				case '1':
					$mm = array (1,2,3,4,5,6);
					break;
				case '2':
					$mm = array (7,8,9,10,11,12);
					break;
				default: 
					return false;	//period - incorrect input
			}									
			
			//	input for current month is changing all the time
			//	so allow to edit ONLY outputs from past (previos month)
			$currentDate = getdate();
			$currentSemiAnnual = ($currentDate['mon'] <= 6) ? 1 : 2;			
			if ($currentDate['year'] < $yyyy ) return false;	//	user asks for future year			
			if ($currentDate['year'] == $yyyy && $currentSemiAnnual < $period) return false;	//	user asks for future semiAnnual period
			
			//	protect from injections
			$yyyy = mysql_real_escape_string($yyyy);
			
			//	we should group result
			$query = "SELECT sum(value) value, output_id " .
					"FROM ".TB_SOLVENT_MANAGEMENT." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND month IN (".implode(', ',$mm).") " .
					"AND year = ".$yyyy." " .
					"GROUP BY output_id";			
			return $this->_formOutputs($query);
		}

		
		
				
		public function getAnnualyOutputs($yyyy) {
			//	no no no, we should null month & year just in case
			$this->mm = null;
			$this->yyyy = null;
						
			//	input for current month is changing all the time
			//	so allow to edit ONLY outputs from past (previos month)
			$currentDate = getdate();					
			if ($currentDate['year'] < $yyyy ) return false;	//	user asks for future year			
			
			//	protect from injections			
			$yyyy = mysql_real_escape_string($yyyy);
			
			//	we should group result
			$query = "SELECT sum(value) value, output_id " .
					"FROM ".TB_SOLVENT_MANAGEMENT." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND year = ".$yyyy." " .
					"GROUP BY output_id";
			return $this->_formOutputs($query);
		}

		
		
		
		public function iniOutputNames() {
			$query = "SELECT * FROM ".TB_SOLVENT_OUTPUT."";
			$this->db->query($query);

			if ($this->db->num_rows() == 0) return false;
			
			$outputNamesRecords = $this->db->fetch_all();
			foreach ($outputNamesRecords as $outputNamesRecord) {
				$this->outputNames[$outputNamesRecord->output_id] = $outputNamesRecord->name;
			}
			
			return $this->outputNames;
		}
						
		
		
		
		public function calculateConsumption() {
			$this->consumption = $this->totalOutput - $this->o8;	//	c = i1 - o8
			return $this->consumption;
		}
		
		public function calculateFugitiveEmission() {
			$this->fugitiveEmission = $this->o2 + $this->o3 + $this->o4 + $this->o9;	//	F = o2 + o3 + 04 + 09 = i1 - o1 - o5 - o6 - o7 - o8
			return $this->fugitiveEmission;
		}
		
		public function calculateAnnualActualSolventEmission() {
			$this->annualActualSolventEmission = $this->totalOutput - $this->o8 - $this->o7 - $this->o6 - $this->o5;	//	A = i1 - 08 - 07 - o6 - o5
			return $this->annualActualSolventEmission;
		}
		
		
		
		public function save() {		
			$fields = array('o1','o2','o3','o4','o5','o6','o7','o8','o9');				
			if ($this->isAlreadyExist) {
				//	update
				foreach($fields as $field) {
					$query = "UPDATE ".TB_SOLVENT_MANAGEMENT." SET value = '".$this->$field."' WHERE facility_id = '$this->facilityID' AND month = '$this->mm' AND year = '$this->yyyy' AND output_id = '$field' ";
					$this->db->query($query);
				}
			} else {
				//	insert
				$query = "INSERT INTO ".TB_SOLVENT_MANAGEMENT." (facility_id, month, year, output_id, value) VALUES ";
				foreach ($fields as $field) {
					$query .= " ('$this->facilityID', '$this->mm', '$this->yyyy', '$field', '".$this->$field."'),";
				}
				$query = substr($query,0,-1);
				$this->db->query($query);
			}
			return true;
		}
		
		
		private function _formOutputs($query) {												
			$this->totalOutput = 0;			
			
			$this->db->query($query);

			//	empty result return false
			if ($this->db->num_rows() == 0) {
				$this->isAlreadyExist = false;
				return false;
			}
			
			$outputs = $this->db->fetch_all();
						
			foreach ($outputs as $output) {
				$id_output = $output->output_id;
				$this->$id_output = $output->value;
				$this->totalOutput += $output->value; 	
			}

			$this->calculateConsumption();
			$this->calculateFugitiveEmission(); 
			$this->calculateAnnualActualSolventEmission();
			
			return true;						
		}
		
		public function getBeginDate($facilityID) {
	    	$query = "SELECT creation_time FROM ".TB_USAGE." WHERE department_id IN(SELECT department_id FROM ".TB_DEPARTMENT." WHERE facility_id = '$facilityID') ORDER BY creation_time ASC LIMIT 0 , 1 ";
	    	$this->db->query($query);
	    	$data = $this->db->fetch(0);
	    	$dateBegin = $data->creation_time;	
	    	if ($dateBegin == null) {
	    		$dateBegin = date("Y-m-d",time());
	    	}
	    	return $dateBegin;
	    }
	    
	    public function getLastUseDate($facilityID) {
	    	$query = "SELECT `month`, `year` FROM `".TB_SOLVENT_MANAGEMENT."` WHERE `facility_id` = '$facilityID' ORDER BY `year` DESC , `month` DESC LIMIT 1";
	    	$this->db->query($query);
	    	$currentDate = getDate();
	    	if ($this->db->num_rows() == 0 ) {
	    		$period = array ('month' => $currentDate['mon']-1, 'year' => $currentDate['year']);
	    	} else {
		    	$data = $this->db->fetch(0);
		    	$period = array('month' => $data->month, 'year' => $data->year);
	    	}
	    	if($period['year'] == $currentDate['year'] && $period['month'] >= $currentDate['mon']) {
		    	$period['month'] = $currentDate['mon']-1;
	    	}
	    	if ($period['month'] === 0) {
		    	$period['year'] = $period['year'] - 1;
		    	$period['month'] = 12;
	    	}
	    	return $period;
	    }
		
		public function getAnnualActualSolventEmissionList($facilityID) {
    		$facility = new Facility($this->db);
    		$dateBegin = $this->getBeginDate($facilityID);
    		$year = substr(date("Y-m-d",strtotime($dateBegin)),0,4);
    		$yearCur = substr(date("Y-m-d",time()),0,4);    	
    		$solvent = array();    		 
    		for($i = $yearCur; $i >= $year; $i--) {    			 
    			$this->getAnnualyOutputs($i);     			
    			$this->calculateAnnualActualSolventEmission(); 		
    			$solvent[]=$this->annualActualSolventEmission;
    		}
    		return $solvent;
    	}
		
	}