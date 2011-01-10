<?php

	interface iCarbonFootprint {
		/**
		 * 
		 * get Monthly carbon emissions 
		 * @param string $mm
		 * @param string $yyyy
		 * @return array[CarbonEmissions] or false
		 */
		function getMonthlyEmissions($mm, $yyyy);
		/**
		 * 
		 * get Quarterly carbon emissions 
		 * @param string $quarter (1,2,3,4)
		 * @param string $yyyy
		 * @return array[CarbonEmissions] or false
		 */
		function getQuarterlyEmissions($quarter, $yyyy);
		/**
		 * 
		 * get Semi Annual carbon emissions 
		 * @param string $period (1,2)
		 * @param string $yyyy
		 * @return array[CarbonEmissions] or false
		 */
		function getSemiAnnualyEmissions($period, $yyyy);
		/**
		 * 
		 * get Annual carbon emissions 		 
		 * @param string $yyyy
		 * @return array[CarbonEmissions] or false
		 */
		function getAnnualyEmissions($yyyy);
		/**
		 * 
		 * get carbon footprint limits
		 * @return array
		 * 			float $limits['monthly']['value']
		 * 			bool $limits['monthly']['show']
		 *			float $limits['annual']['value']
		 * 			bool $limits['annual']['show']
		 */
		function getLimits();
		function setLimit($period, $value, $show);
		/**
		 * 
		 * @return array of EmissionFactor
		 */
		function getAllDirectEmissionFactors(Pagination $pagination = null);
	}
	
	
	class CarbonFootprint implements iCarbonFootprint {
	
		//	hello xnyo
		protected $db;								
		protected $facilityID;
		
		private $limits;		
		
		const MONTHLY = 'monthly';
		const ANNUAL = 'annual';
		
		const CONST_FACTOR = 0.001;		
		
    	public function CarbonFootprint($db, $facilityID = null) {    	
	    	$this->db = $db;	    	
	    	if (!is_null($facilityID)) {
	    		$this->facilityID = mysql_real_escape_string($facilityID);
	    		
	    		//	setup carbon footprint limits	
	    		$this->_selectLimits();	    		
	    	}
    	}

    	
    	
		public function getMonthlyEmissions($mm, $yyyy) {			
			$mm = mysql_real_escape_string($mm);
			$yyyy = mysql_real_escape_string($yyyy);
			
			$query = "SELECT * " .
					"FROM ".TB_CARBON_EMISSIONS." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND month = ".$mm." " .
					"AND year = ".$yyyy;
			
			return $this->_formCarbonEmissions($query);
		}

		
		public function getQuarterlyEmissions($quarter, $yyyy) {			
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
			
			//	protect from injections
			$yyyy = mysql_real_escape_string($yyyy);
			
			//	we should group result and hide description, estimation adjustment
			//	because we need totals!
			$query = "SELECT emission_factor_id, sum(quantity) quantity, unittype_id, sum(tco2) tco2 " .
					"FROM ".TB_CARBON_EMISSIONS." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND month IN (".implode(', ',$mm).") " .
					"AND year = ".$yyyy." " .
					"GROUP BY emission_factor_id, unittype_id";

			return $this->_formCarbonEmissions($query, $isGrouped = true);
						
		}

		
		public function getSemiAnnualyEmissions($period, $yyyy) {			
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
			
			//	protect from injections
			$yyyy = mysql_real_escape_string($yyyy);
			
			//	we should group result and hide description, estimation adjustment
			//	because we need totals!
			$query = "SELECT emission_factor_id, sum(quantity) quantity, unittype_id, sum(tco2) tco2 " .
					"FROM ".TB_CARBON_EMISSIONS." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND month IN (".implode(', ',$mm).") " .
					"AND year = ".$yyyy." " . 
					"GROUP BY emission_factor_id, unittype_id";
			
			return $this->_formCarbonEmissions($query, $isGrouped = true);
		}

				
		public function getAnnualyEmissions($yyyy) {
			//	protect from injections			
			$yyyy = mysql_real_escape_string($yyyy);
			
			//	we should group result and hide description, estimation adjustment
			//	because we need totals!
			$query = "SELECT emission_factor_id, sum(quantity) quantity, unittype_id, sum(tco2) tco2 " .
					"FROM ".TB_CARBON_EMISSIONS." " .
					"WHERE facility_id = ".$this->facilityID." " .
					"AND year = ".$yyyy." " .
					"GROUP BY emission_factor_id, unittype_id";
			
			return $this->_formCarbonEmissions($query, $isGrouped = true);
		}	
		
		
		public function getLimits() {
			return $this->limits;
		}
		
		
		private function _selectLimits() {
			//	no facility is bad..
			if (!isset($this->facilityID)) return false;
			
			$query = "SELECT * " . 
					"FROM ".TB_CARBON_FOOTPRINT." " .
					"WHERE facility_id = ".$this->facilityID;
			$this->db->query($query); 
			
			//	empty result is also bad..
			if ($this->db->num_rows() == 0) return false;
			
			//	fetching result
			$carbonFootprintLimitsRecord = $this->db->fetch(0);
			
			//	output array
			$limits = array (
				'monthly' => array('value'=>$carbonFootprintLimitsRecord->monthly_value, 
									'show'=>($carbonFootprintLimitsRecord->monthly_show == 0) ? false : true),
				'annual' => array('value'=>$carbonFootprintLimitsRecord->annual_value, 
									'show'=>($carbonFootprintLimitsRecord->annual_show == 0)  ? false : true)
			);
			
			//	save result at property ..
			$this->limits = $limits;
			
			//	..and return 
			return $limits;
		}
		
		
		
		public function setLimit($period, $value, $show) {
			//	no facility is bad..
			if (!isset($this->facilityID)) return false;

			//	period should have only 2 values
			if ($period != self::MONTHLY && $period != self::ANNUAL) return false;	//	incorrect input
			
			//	MySQL boolean is 1(true) or 0(false)
			$showLimit = ($show) ? 1 : 0;
			
			//	protect from SQL injections
			$period = mysql_real_escape_string($period);
			$value = mysql_real_escape_string($value);
			
			if ($this->limits) {
				// update	
				$query = "UPDATE ".TB_CARBON_FOOTPRINT." SET " .
							"".$period."_value = ".$value.", " .
							"".$period."_show = ".$showLimit." " .
							"WHERE facility_id = ".$this->facilityID;	
			} else {
				//	insert
				$query = "INSERT INTO ".TB_CARBON_FOOTPRINT." ( facility_id, ".$period."_value, ".$period."_show) VALUES " .
							"(".$this->facilityID.", ".$value.", ".$showLimit.")";   
			}

			//	execute $query (update or insert)
			$this->db->exec($query);
			$this->limits[$period] = array('value' => $value, 'show' => $show);
			//	e-e-e!
			return true;
			
		}
		
		public function getUnittipeOfEmissionFactor($EmissionFactor_id)
    	{
    		$query = "SELECT unittype_id FROM ".TB_EMISSION_FACTOR. " WHERE id=".$EmissionFactor_id;
    		$this->db->query($query);
    		if ($this->db->num_rows()) 
				$data=$this->db->fetch(0);
			return $data->unittype_id;			
    	}
		
		
		public function getAllDirectEmissionFactors(Pagination $pagination = null) {
			$query = "SELECT * FROM ".TB_EMISSION_FACTOR;
			
			if (isset($pagination)) {
				$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
			}
		
			$this->db->query($query);
			
			//	no result is sucks..
			if ($this->db->num_rows() == 0) return false;
			
			//	fetch result
			$dataRecords = $this->db->fetch_all();
			
			//	ini output array
			$emissionFactors = array();
			
			//	loop throw result and prepare output
			foreach ($dataRecords as $dataRecord) {
				
				//	electricity is very very special
				if ($dataRecord->id == EmissionFactor::ELECTRICITY_FACTOR_ID) {
					//	skip electricity
					continue;
				}
				
				//	we do not send emission factor ID to conructor, because I want to reduce number of SQL queries
				$emissionFactor = new EmissionFactor($this->db);
				
				//	auto setup for properties
				foreach($dataRecord as $property=>$value) {
					if (property_exists($emissionFactor, $property)) {
			 			$emissionFactor->$property = $value;
			 		}
				}
				
				//	add $emissionFactor to output
				$emissionFactors[] = $emissionFactor;
			}
			
			return (count($emissionFactors) == 0) ? false : $emissionFactors;
		}
		
		public function getMonthlyIndirectEmission($month, $year) {
			$query = "SELECT * FROM ".TB_CARBON_EMISSIONS." WHERE emission_factor_id = '".EmissionFactor::ELECTRICITY_FACTOR_ID."' " .
				"AND facility_id = '$this->facilityID' AND month = '$month' AND year = '$year' LIMIT 0,1";
			return $this->_formIndirectEmission($query,false);	
		}
		
		public function getQuarterlyIndirectEmission($quarter, $year) {
			$query = "SELECT quantity, tco2 FROM ".TB_CARBON_EMISSIONS." WHERE emission_factor_id = '".EmissionFactor::ELECTRICITY_FACTOR_ID."' " .
				"AND facility_id = '$this->facilityID' AND month IN ('".($quarter*3-2)."', '".($quarter*3-1)."', '".($quarter*3)."') AND year = '$year'";
			return $this->_formIndirectEmission($query,true);
		}
		
		public function getSemiAnnualyIndirectEmission($period, $year) {
			$query = "SELECT quantity, tco2 FROM ".TB_CARBON_EMISSIONS." WHERE emission_factor_id = '".EmissionFactor::ELECTRICITY_FACTOR_ID."' " .
							"AND facility_id = '$this->facilityID' AND month IN (".(($period == 1)?"'1', '2', '3', '4', '5', '6'":"'7', '8', '9', '10', '11', '12'").") " .
							"AND year = '$year'";
			return $this->_formIndirectEmission($query,true);
		}
		
		public function getAnnualyIndirectEmission($year) {
			$query = "SELECT quantity, tco2 FROM ".TB_CARBON_EMISSIONS." WHERE emission_factor_id = '".EmissionFactor::ELECTRICITY_FACTOR_ID."' " .
							"AND facility_id = '$this->facilityID' AND year = '$year'";
			return $this->_formIndirectEmission($query,true);
		}
		
		public function getMonthlyCurrentUsage() {
			if (!isset($this->facilityID)) {
				return false;
			}
			$year = substr(date("Y-m-d",time()),0,4);
			$month = substr(date("m-d-Y",time()),0,2);
			$query = "SELECT sum(tco2) tco2 FROM ".TB_CARBON_EMISSIONS." WHERE month = '$month' AND year = '$year' AND facility_id = '$this->facilityID' GROUP BY year LIMIT 1";
			$this->db->query($query);
			if ($this->db->num_rows() == 0) {
				return 0;
			}
			$data = $this->db->fetch(0);
			return $data->tco2;			
		}
		
		public function getAnnualCurrentUsage() {
			if (!isset($this->facilityID)) {
				return false;
			}
			$year = substr(date("Y-m-d",time()),0,4);
			$query = "SELECT sum(tco2) tco2 FROM ".TB_CARBON_EMISSIONS." WHERE year = '$year' AND facility_id = '$this->facilityID' GROUP BY year LIMIT 1";
			$this->db->query($query);
			if ($this->db->num_rows() == 0) {
				return 0;
			}
			$data = $this->db->fetch(0);
			return $data->tco2;			
		}
		
		public function getEmissionFactorIdArrayExist($month, $year) {
			if (!isset($this->facilityID)) {
				return false;
			}
			$query = "SELECT emission_factor_id FROM ".TB_CARBON_EMISSIONS." WHERE facility_id = '$this->facilityID' AND year = '$year' AND month = '$month' ";
			$this->db->query($query);
			$data = $this->db->fetch_all();
			$idArray = array();
			foreach($data as $id) {
				$idArray []= $id->emission_factor_id;
			}
			return $idArray;
		}
		
		
		
		public function queryTotalEmissionCount() {
			$query = "SELECT COUNT(*) cnt FROM ".TB_EMISSION_FACTOR."";			
			$this->db->query($query);
			return $this->db->fetch(0)->cnt;
		}
		
		
		
		private function _formIndirectEmission($query, $isNeedToGroup = false) {
			if (!isset($this->facilityID)) {
				return false;
			}
			$carbonEmission = new CarbonEmissions($this->db);
			$carbonEmission->emission_factor_id = EmissionFactor::ELECTRICITY_FACTOR_ID;
			$carbonEmission->emissionFactor = new EmissionFactor($this->db, $carbonEmission->emission_factor_id);
			$this->db->query($query);
			if ($this->db->num_rows() == 0) {
				return false;
			}
			if ($isNeedToGroup) {
				$data = $this->db->fetch_all();
				$quantity = 0;
				$tco2 = 0;
				foreach($data as $emission) {
					$quantity += $emission->quantity;
					$tco2 += $emission->tco2;
				}
				$carbonEmission->quantity = $quantity;
				$carbonEmission->tco2 = $tco2;
			} else {
				$data = $this->db->fetch(0);
				foreach($data as $property => $value) {
					if (property_exists($carbonEmission,$property)) {
						$carbonEmission->$property = $value;
					}
				}
			}
			return $carbonEmission;
		}
		

		private function _formCarbonEmissions($query, $isGrouping = false) {
			
			//	no facility is bad..
			if (!isset($this->facilityID)) return false;
			
			//	execute query
			$this->db->query($query);
			
			//	empty result is also bad.. 
			if($this->db->num_rows() == 0) return false;
			
			//	fetch result to variable
			$dataRecords = $this->db->fetch_all();
			
			//	ini output array
			$carbonEmissions = array();
			
			//	when grouping we should convert all stuff to one unittype
			//	so these objects will be usefull
			if ($isGrouping) {				
				$unittypeConverter = new UnitTypeConverter();
				$unittype = new Unittype($this->db);
			}			
						
			
			foreach ($dataRecords as $dataRecord) {
				//	is this emission factor unique in this set, hm? probably, yes
				$isUnique = true;
				if ($dataRecord->emission_factor_id == EmissionFactor::ELECTRICITY_FACTOR_ID) {
					continue;
				}
				//	check for unique record
				if ($isGrouping) {
					
					//	loop throw already added to output array elements 					
					foreach ($carbonEmissions as $key=>$objectsAlreadyFound) {
						
						//	compare
						if ($objectsAlreadyFound->emission_factor_id == $dataRecord->emission_factor_id ) {
							
							//	opa! such emission factor already in our array. add
							
							//	convert to emission factor default unittype
							$qty = $unittypeConverter->convertFromTo($dataRecord->quantity,	//	what
																		$unittype->getDescriptionByID($dataRecord->unittype_id),	//	from
																		$unittype->getDescriptionByID($carbonEmissions[$key]->emissionFactor->unittype_id)	//	to
																	) ;
																	
							//	add result to quantity
							$carbonEmissions[$key]->quantity += $qty;
							
							//	add tonnes of CO2
							$carbonEmissions[$key]->tco2 += $dataRecord->tco2;
							
							//	yeap. this emission factor is not unique
							$isUnique = false;							
						}
					}
					
					//	skip iteration?
					if (!$isUnique) {
						//	we've added this record to one of the previous carbonEmissios
						//	so let's skip iteration
						continue;
					}
				}				

				//	this guy is unique. set let's setup him as new one
				$carbonEmission = new CarbonEmissions($this->db);
				
				//	set emission factor
				$carbonEmission->emissionFactor = new EmissionFactor($this->db, $dataRecord->emission_factor_id);
				
				//	when grouping we should convert all stuff to one unittype
				if ($isGrouping) {					
					$dataRecord->quantity = $unittypeConverter->convertFromTo($dataRecord->quantity,	//	what
																				$unittype->getDescriptionByID($dataRecord->unittype_id),	//	from
																				$unittype->getDescriptionByID($carbonEmission->emissionFactor->unittype_id)	//	to
																			) ;
				}

				//	auto properties setter at $carbonEmission
				foreach($dataRecord as $property=>$value) {
					if (property_exists($carbonEmission, $property)) {
			 			$carbonEmission->$property = $value;
			 		}
				}		
										
				//	add emission to output
				$carbonEmissions[] = $carbonEmission;
			}
			
			//	that's all.
			return (count($carbonEmissions) == 0) ? false : $carbonEmissions;
		}
    	
	}
    	