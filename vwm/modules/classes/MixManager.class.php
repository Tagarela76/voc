<?php

	class MixManager {
		
		public $db;
		public $departmentID;
		
		public function __construct($db, $departmentID = null) {
			$this->db = $db;
			if (isset($departmentID)) {
				$this->departmentID = $departmentID;
			}
		}
		
		/**
		 * getMixList
		 * 
		 * @param Pagination $pagination
		 * @param unknown_type $filter 
		 * @param array $mixArr array of mixes to get. <b>default null</b>
		 */
		public function getMixList(Pagination $pagination = null, $filter = ' TRUE ', $mixArr = null) {
			
			if (!isset($this->departmentID)) return false;
			$departmentID = mysql_escape_string($this->departmentID);
			
			if(!is_null($mixArr) and count($mixArr) > 0) {
				$sql_param = " AND mix_id IN (";
				$count = count($mixArr);
				for($i = 0; $i < $count; $i++) {
					$sql_param .= $mixArr[$i];
					if($i < $count-1) {
						$sql_param .= ", "; 
					}
				}
				$sql_param .= ") ";
			} else {
				$sql_param = "";
			}
			
			

			$query = "SELECT * FROM ".TB_USAGE." WHERE department_id = ".$departmentID." AND ".$filter." $sql_param ORDER BY mix_id DESC";
			
			

			if (isset($pagination) and is_null($mixArr)) {
				$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
			}
			
			
			
			$this->db->query($query);

			if ($this->db->num_rows() == 0) return false;
			
			//	prepare all stuff
			$mixesData = $this->db->fetch_all();
			$mixValidator = new MixValidatorOptimized();
			$department = new Department($this->db);
			$department->initializeByID($this->departmentID);
			$facility = new Facility($this->db);
			$facility->initializeByID($department->getFacilityID());
			$mixHover = new Hover();
			$equipments = array();
			
			foreach ($mixesData as $mixData) {
				
				$mix = new MixOptimized($this->db);				
				foreach ($mixData as $property =>$value) {
					if (property_exists($mix,$property)) {
						$mix->$property = $mixData->$property;
					}
				}

				$mix->url = "?action=viewDetails&category=mix&id=" . $mixData->mix_id . "&departmentID=" . $departmentID;
				
				$mix->setDepartment($department);
				$mix->setFacility($facility);
				
				if (!$equipments[$mix->equipment_id]) {
					//	we didnot created this equipment yet
					$equipment = new Equipment($this->db);
					$equipment->initializeByID($mix->equipment_id);
					$equipments[$mix->equipment_id] = $equipment;
				}
				$mix->setEquipment($equipments[$mix->equipment_id]);
				
				// validate them
				$validatorResponse = $mixValidator->isValidMix($mix);
				
				if ($validatorResponse->isValid()) {
					$mix->valid = MixOptimized::MIX_IS_VALID;
					$mix->hoverMessage = $mixHover->mixValid();
				} else {
					if ($validatorResponse->isPreExpired()) {
						$mix->valid = MixOptimized::MIX_IS_PREEXPIRED;
						$mix->hoverMessage = $mixHover->mixPreExpired();						
					}						
					if ($validatorResponse->isSomeLimitExceeded() or $validatorResponse->isExpired()){
						$mix->valid = MixOptimized::MIX_IS_INVALID;
						$mix->hoverMessage = $mixHover->mixInvalid();						
					}
				}
										
				$usageList[] = $mix;
			}
			
			return $usageList;
		}
		
		
		public function fillProductsUnitTypes($mixesProducts) {
			
			$ids = array();
			
			foreach($mixesProducts as $product) {
				$ids[] = $product->unit_type;
			}
			
			$unittype = new Unittype($this->db);
			
			$unitTypeDetails = $unittype->getUnittypesDetails($ids);
			
			foreach($mixesProducts as $product) {
				$product->unittypeDetails = $unitTypeDetails[$product->unit_type];
			}
			
			
		}
		
		public function countMixes() {
			if (!isset($this->departmentID)) return false;
			$departmentID=mysql_escape_string($this->departmentID);

			$query = "SELECT count(mix_id) mixCount FROM ".TB_USAGE." WHERE department_id = ".$departmentID;
			$this->db->query($query);

			if ($this->db->num_rows() > 0) {
				return $this->db->fetch(0)->mixCount;
			} else {
				return false;
			}				
		}
		
		public function deleteMixList($mixIDarr) {
			
		}
	}