<?php
	
	class MixOptimized {
		
		private $db;
		
		public $valid = self::MIX_IS_VALID;
		public $hoverMessage;
		
		public $mix_id;
		public $equipment_id;
		public $department_id;
		public $description;
		public $voc;
		public $voclx;
		public $vocwx;
		public $creation_time;
		public $rule_id;
		public $exempt_rule;
		public $apmethod_id;
		public $waste_percent;
		
		public $products = null;
		
		private $department;
		private $facility;
		private $equipment;
		
		private $waste; 
		
		const MIX_IS_VALID = 'valid';
		const MIX_IS_INVALID = 'invalid';
		const MIX_IS_EXPIRED = 'expired';
		const MIX_IS_PREEXPIRED = 'preexpired';
		
		
		public function __construct($db, $mix_id = null) {
			$this->db = $db;
			if (isset($mix_id)) {
				$this->mix_id = $mix_id;	
				$this->_load();			
			}
		}
					
		
		public function setDepartment(Department $department) {
			$this->department = $department;
		}
		public function setFacility(Facility $facility) {
			$this->facility = $facility;
		}
		public function setEquipment(Equipment $equipment) {
			$this->equipment = $equipment;
		}
		public function getDepartment() {
			return $this->department;
		}
		public function getFacility() {
			return $this->facility;
		}
		public function getEquipment() {
			return $this->equipment;
		}
		
		public function getWaste() {
			return $this->waste;
		}
		
		public function getCreationTime() {
			//TODO: dates
			return date('m-d-Y', strtotime($this->creation_time));
		}
		
		
		public function save() {
			
		}
		
		
		private function _load() {
			if (!isset($this->mix_id)) return false;
			
			$mixID = mysql_escape_string($this->mix_id);
			
			$query = 'SELECT * FROM '.TB_USAGE.' WHERE mix_id = '.$mixID.'';
			$this->db->query($query);
			
			if ($this->db->num_rows() == 0) return false;
			
			$mixData = $this->db->fetch(0);
			foreach ($mixData as $property =>$value) {
				if (property_exists($this,$property)) {
					$this->$property = $mixData->$property;
				}
			}

			$this->loadProducts();
		}
		
		
		
		public function loadProducts() {
									
			if (!isset($this->mix_id)) return false;
			
			$this->products = array();
			
			$mixID = mysql_escape_string($this->mix_id);
			
			$query = 'SELECT * FROM '.TB_MIXGROUP.' WHERE mix_id = '.$mixID.'';
			$this->db->query($query);
			
			if ($this->db->num_rows() == 0) return false;
			
			$productsData = $this->db->fetch_all();
			foreach ($productsData as $productData) {
				
				$mixProduct = new MixProduct($this->db);
				foreach ($productData as $property =>$value) {
					if (property_exists($mixProduct,$property)) {
						$mixProduct->$property = $productData->$property;
					}
				}
				//	TODO: add userfriendly records to product properties
				$mixProduct->initializeByID($mixProduct->product_id);
				var_dump($mixProduct);
				array_push($this->products, $mixProduct);								
			}
			
			return $this->products;
		}
		
		
		
		public function isAlreadyExist() {
			return (!$this->mix_id) ? false : true; 
		}
		
		
		
		public function iniWaste() {
			if (!isset($this->mix_id)) return false;
			
			$unittype = new Unittype($this->db);
			$unitTypeConverter = new UnitTypeConverter();			

			$wasteFromDB = $this->selectWaste($mixID);

			if (!$wasteFromDB) {
				//	default values
				$this->waste = array (
					'mixID'			=> $mixID,
					'value'			=> 0.00,
					'unittypeClass'	=> 'percent'								
				);
			} else {
				$this->waste = array (
					'mixID'			=> $wasteFromDB->mix_id,
					'value'			=> $wasteFromDB->value
				);

				$this->waste['unittypeClass'] = ($wasteFromDB->method == 'percent') ? $wasteFromDB->method : $unittype->getUnittypeClass($wasteFromDB->unittype_id);
				$this->waste['unittypeID'] = (is_null($wasteFromDB->unittype_id)) ? '' : $wasteFromDB->unittype_id;
				$this->waste['unitTypeList'] = (is_null($wasteFromDB->unittype_id)) ? false : $unittype->getUnittypeListDefault($this->waste['unittypeClass']);
			}

			//	get mix products
			if (is_null($this->products)) {
				$this->loadProducts();
			}

			//	sum total quantity
			$quantitySum = 0;
			foreach ($this->products as $product) {				
				$densityObj = new Density($this->db, $product->getDensityUnitID());
				$densityType = array (
					'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
					'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
				);
				$unitTypeDetails = $unittype->getUnittypeDetails($product->unit_type);
				$quantitySum += $unitTypeConverter->convertToDefault($product->quantity, $unitTypeDetails['description'], $product->getDensity(), $densityType);
			}

			$this->waste['calculated'] = $this->calculateWaste($this->waste['unittypeID'], $this->waste['value'], $quantitySum);	//	waste in weight unit type same as VOC
				
			return $this->waste;
		}
		
		
		
		public function recalcAndSaveWastePersent() {
			if (!isset($this->mix_id)) return false;
			$mixID = mysql_escape_string($this->mix_id);
			
			$errors = $this->calculateCurrentUsage();
			$this->waste_percent = round($this->waste_percent,2);
			
			$query= "UPDATE ".TB_USAGE." SET waste_percent='$this->waste_percent.' WHERE mix_id='".$mixID."'";
			$this->db->exec($query);
			
			return $this->waste_percent;
		}
	
		
		public function calculateCurrentUsage() {
			
			if ($this->products === null) return false; 
			
			$errors = array(
				'isDensityToVolumeError'=>false,
				'isDensityToWeightError'=>false,
				'isWasteCalculatedError'=>false,
				'isWastePercentAbove100'=>false			
			);

			$isThereProductWithoutDensity = false;
			
			$company = new Company($this->db);
			$unittype = new Unittype($this->db);
			
			$wasteUnitDetails = $unittype->getUnittypeDetails($this->waste['unittypeID']);
			$companyID = $company->getCompanyIDbyDepartmentID($this->department_id);
			$companyDetails = $company->getCompanyDetails($companyID);
			
			//	default unit type = company's voc unit
			$defaultType = $unittype->getDescriptionByID($companyDetails['voc_unittype_id']);
				
			$unitTypeConverter = new UnitTypeConverter($defaultType);

			foreach ($this->products as $key=>$product) {
					
				$voclx = $product->getVoclx();
				$vocwx = $product->getVocwx();
				$percentVolatileWeight = $product->getPercentVolatileWeigh();
				$percentVolatileVolume = $product->getPercentVolatileVolume();
				$errors['isVocwxOrPercentWarning'][$key]='false';
					
				$density = $product->getDensity();
				$densityObj = new Density($this->db, $product->getDensityUnitID());
					
				//	check density
				if (empty($density) || $density == '0.00') {
					$density = false;
					$isThereProductWithoutDensity = true;
				}							
					
				$quantity = $product->quantity;
				$unitTypeId = $product->unit_type;
					
				$unitTypeDetails = $unittype->getUnittypeDetails($unitTypeId);								
					
				// get Density Type
				$densityType = array (
    				'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
    				'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
				);
					
				//quantity array in lbs
				$quantitiWeightSum += $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "lb", $density, $densityType);//	in weight
				//quantity array in gallon
				$quantitiVolumeSum += $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon", $density, $densityType);//	in volume ;

					
				//check, is vocwx or percentVolatileWeight
				$isVocwx = true;
				$isPercentVolatileWeight = true;
				
				if (empty($vocwx) || $vocwx == '0.00') {
					$isVocwx = false;	
				}
				
				if (empty($percentVolatileWeight) || $percentVolatileWeight == '0.00') {
					$isPercentVolatileWeight = false;	
				}
				
				if ($isPercentVolatileWeight||$isVocwx) {
					$errors['isVocwxOrPercentWarning'][$key] = false;
					switch ($unittype->isWeightOrVolume($unitTypeId)) {
						
						case 'weight':
							if ($isPercentVolatileWeight) {
								$percentAndWeight = array(
									'weight' => $unitTypeConverter->convertToDefault($quantity, $unitTypeDetails["description"]),
									'percent'=> $percentVolatileWeight
								);
								$ArrayWeight[] = $percentAndWeight;
							} else {
								if ($density) {
									$galQty = $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon", $density, $densityType);//	in volume
									$vocwxAndVolume = array(
										'volume' => $galQty,
										'vocwx' => $vocwx
									);
									$ArrayVolume[] = $vocwxAndVolume;
								} else {
									$errors['isDensityToVolumeError'] = true;
								}
							}
							break;
							
						case 'volume':
							if ($isVocwx) {
								$galQty = $unitTypeConverter->convertFromTo($quantity, $unitTypeDetails["description"], "us gallon");//	in volume
								$vocwxAndVolume = array(
									'volume'=>$galQty,
									'vocwx'=>$vocwx
								);
								$ArrayVolume[] = $vocwxAndVolume;
							} else {
								if ($density) {
									$percentAndWeight = array(
										'weight'=>$unitTypeConverter->convertToDefault($quantity, $unitTypeDetails["description"], $density, $densityType),
										'percent'=> $percentVolatileWeight
									);
									$ArrayWeight[] = $percentAndWeight;
								} else {
									$errors['isDensityToWeightError'] = true;
								}
							}
							break;
					}

				} else {
					$errors['isVocwxOrPercentWarning'][$key]='true';	
				}				
			}
			
			$wasteResult = $this->calculateWastePercent($this->waste['unittypeID'], $this->waste['value'],$unitTypeConverter,$quantitiWeightSum,$quantitiVolumeSum);
			
			$calculator = new Calculator();

			$this->voc = $calculator->calculateVocNew($ArrayVolume,$ArrayWeight,$defaultType,$wasteResult);		

			$this->waste_percent = $wasteResult['wastePercent'];
			//$this->currentUsage = $this->voc;
			//$this->wastePercent = $wasteResult['wastePercent'];
			
			$errors['isWastePercentAbove100'] = $wasteResult['isWastePercentAbove100'];
			
			return $errors;
		}
		
		
		private function selectWaste() {
			if (!isset($this->mix_id)) return false;
			$mixID = mysql_escape_string($this->mix_id);

			$query = "SELECT * FROM waste WHERE mix_id = ".$mixID;
			$this->db->query($query);
			
			if ($this->db->num_rows() > 0) {				
				return $this->db->fetch(0);
			} else
				return false;
		}
		
		
		private function calculateWaste($unittypeID, $value, $quantitySum = 0, $mixDensity = false) {				
			$waste = 0;

			$unitTypeConverter = new UnitTypeConverter();
			$unittype = new Unittype($this->db);

			if (empty($unittypeID)) {
				//	percent
				$waste = ($value * $quantitySum) / 100;

			} else {
				//	weight					
				$unitTypeDetails = $unittype->getUnittypeDetails($unittypeID);
				$waste = $unitTypeConverter->convertToDefault($value, $unitTypeDetails["description"],$mixDensity);
			}
			return round($waste, 2);
		}	
		
		
		
		private function calculateWastePercent($unittypeID, $value, UnitTypeConverter $unitTypeConverter, $quantityWeightSum = 0, $quantityVolumeSum = 0) {
			$result = array(
						'wastePercent' => 0,
						'isWasteError' => false,
						'isWastePercentAbove100' => false
					);
			$unittype = new Unittype($this->db);
			$wasteUnitDetails = $unittype->getUnittypeDetails($this->waste['unittypeID']);

			if (empty($unittypeID)) {
				//	percent
				$result['wastePercent']= $value;
			} else {
				switch ($unittype->isWeightOrVolume($this->waste['unittypeID'])) {
					
					case 'volume':
						$weistVolume = $unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], 'us gallon');
						$result['wastePercent'] = $weistVolume/$quantityVolumeSum*100;							
						break;
						
					case 'weight':						
						$weistWeight = $unitTypeConverter->convertFromTo($value, $wasteUnitDetails["description"], "lb");
						$result['wastePercent'] = $weistWeight/$quantityWeightSum*100;						
						break;
						
					case false:
						$result['isWasteError'] = true;
						break;
				}
			}
			
			if ($result['wastePercent']>100) {
				$result['wastePercent'] = 0;
				$result['isWastePercentAbove100'] = true;
			}
			
			return $result;
		}
	}