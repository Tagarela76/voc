<?php

class WasteStreams {
	
	private $db;
	public $wasteType;

	public $productSumm;
	public $defUnittypeID;
	public $wasteData;
	public $error;

    function WasteStreams($db) {
    	$this->db = $db;
    	$this->wasteType = array(
    		'weight' => true,
    		'volume' => true
    	);
    	$this->productSumm = array(
    		'weight' => 0,// in lbs id = 2
    		'volume' => 0// in gal id = 1
    	);
    	$this->defUnittypeID = array(
    		'weight' => 2,
    		'volume' => 1
    	);
    }
    
    
    public function getWasteStreamsFullList() {
    	$query = "SELECT * FROM ".TB_WASTE_STREAMS;    	
    	$this->db->query($query);
    	if ($this->db->num_rows()>0) {
    		$wasteStreams = $this->db->fetch_all();
    		$list = array();
    		foreach($wasteStreams as $wasteStream) {
    			$list []= array(
    				'id' => $wasteStream->id,
    				'name' => $wasteStream->name,
    				'pollutions' => ((self::getPolutionList($wasteStream->id))?'true':'false')
    			);
    		}
    		return $list;
    	} else {
    		return false;
    	}
    }
    
    public function getNameById($id)
    {
    	$query = "SELECT name FROM ".TB_WASTE_STREAMS." WHERE id = ".$id;
    	$this->db->query($query);
    	if ($this->db->num_rows()>0) {
    		return $this->db->fetch(0)->name;    		
    	}
    	else
    		return false;
    }
    
    public function getPolutionList($wasteStreamID) {
    	$query = "SELECT * FROM ".TB_POLLUTION." WHERE waste_stream_id='".$wasteStreamID."'";
    	$this->db->query($query);
    	if ($this->db->num_rows()>0) {
    		$pollutions = $this->db->fetch_all();
    		$list = array();
    		foreach($pollutions as $pollution) {
    			$list []= array(
    				'id' => $pollution->id,
    				'name' => $pollution->name
    			);
    		}
    		return $list;
    	} else {
    		return false;
    	}
    }
    
    private function addWasteToMix($mixId, $wasteData, $type = "waste", $wasteStream_id = null) {
	    $query_insert = "INSERT INTO ".TB_WASTE." (`id` , `mix_id` , `method` , `unittype_id` ,".
		    " `value` , `waste_stream_id` , `pollution_id`" .
		    ", `storage_id`" .		// storage_id (Storage.class.php)
		    " ) VALUES ( NULL, ";  
	    $query_update = "UPDATE ".TB_WASTE." SET ";
	    $query_select = "SELECT id FROM ".TB_WASTE." WHERE mix_id = $mixId ";	
	    
	    if ($type == "waste") {
	    	$ws_id = "'".$wasteData['id']."'";
	    	$p_id_select = "IS NULL";
	    	$p_id = "NULL";
	    } else {
	    	$ws_id = "'".$wasteStream_id."'";
	    	$p_id_select = " = '".$wasteData['id']."'";
	    	$p_id = " '".$wasteData['id']."'";
	    }	
	    $method = ($wasteData['unittypeID'])?'weight':'percent';
	    $unittype_id = ($wasteData['unittypeID'])?"'".$wasteData['unittypeID']."'":"NULL";
	    $value = $wasteData['value'];
	    $storage = $wasteData['storage_id'];
	    $query = $query_select." AND waste_stream_id = $ws_id AND pollution_id $p_id_select ";
	    $this->db->query($query);
	    if($this->db->num_rows()>0) {
		    $w_id = $this->db->fetch(0)->id; //id in the Waste table with needed waste stream
		    $query = $query_update;
		    $query .= "method = '$method', unittype_id = $unittype_id, value = '$value' " .
		    		" , storage_id = $storage ".  	// storage_id (Storage.class.php)
			    	" WHERE id = $w_id";
	    } else {
		    $query = $query_insert;
		    $query .= "'$mixId', '$method', $unittype_id, '$value', $ws_id, $p_id " .
		    		", $storage " .		// storage_id (Storage.class.php)
		    		" ) ";
	    }
	    $this->db->query($query);
    }
    

    
    public function addWasteStreamsToMix($mixId,$wasteArray) {
		$query = "SELECT id, waste_stream_id, pollution_id FROM ".TB_WASTE." WHERE mix_id = $mixId ";
    	$this->db->query($query);
    	$data = $this->db->fetch_all();
    	$idArray = array();
    	foreach($data as $key => $wsData) {
    		$idArray [$wsData->waste_stream_id] [$wsData->pollution_id] = $key;
    	}
    	$newId = array();    	
    	foreach ($wasteArray as $wasteStream) {
    	//$wasteStream can be array of details or array of pollutions with details
    		if (isset($wasteStream['count'])) {
    			//$wasteStream - array of pollutions
    			$ws_id = $wasteStream['id']; // id of Waste Stream
    			$storage_id = $wasteStream['storage_id'];
    			foreach($wasteStream as $key => $pollution) {
    				if ($key !== 'count' && $key !== 'id' && $key !== 'storage_id') {
    					$pollution['storage_id'] = $storage_id;
    					$this->addWasteToMix($mixId,$pollution,'pollution',$ws_id);
    					 if (isset($idArray[$wasteStream['id']][$pollution['id']])) {
    						$newId []= $idArray[$wasteStream['id']][$pollution['id']];
    					}
    				}
    			}
    		} else {
    			//$wasteStream - array of details about stream(without pollutions)
    			$this->addWasteToMix($mixId,$wasteStream);
    			if (isset($idArray[$wasteStream['id']][null])) {
		       		$newId []= $idArray[$wasteStream['id']][null];
	       		}
    		}
    	}
    	foreach($data as $key => $value) {
			if (!in_array($key,$newId)) {
				$query = " DELETE FROM `waste` WHERE `waste`.`id` = $value->id ";
				$this->db->query($query);
			}
		}
    }
    
    public function checkWasteType($product) {
    	$unittype = new Unittype($this->db);
    	$unittypeID = $product['unittype'];
    	$unittypeType = $unittype->isWeightOrVolume($unittypeID);
    	$productObj = new Product($this->db);
    	$productObj->initializeByID($product['product_id']);
    	$density = $productObj->getDensity();
    	if (empty($density) || $density == '0.00') {
    		$isDensity = false;
    	} else {
    		$isDensity = true;
    	}
    	$densityUnitID = $productObj->getDensityUnitID();
		$densityObj = new Density($this->db,$densityUnitID);
    	$densityType = array (
	    	'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
    	);
    	$unitTypeDetails = $unittype->getUnittypeDetails($product['unittype']);
    	if ($unittypeType == 'weight') {
    		$this->wasteType['volume'] = ($this->wasteType['volume'])?($isDensity):false;
    	} else {
	    	$this->wasteType['weight'] = ($this->wasteType['weight'])?$isDensity:false;
    	}
    	$unitTypeConverter = new UnitTypeConverter();
    	if ($this->wasteType['weight']) {
	    	$this->productSumm['weight'] += $unitTypeConverter->convertFromTo($product['quantity'], $unitTypeDetails["description"], $unittype->getDescriptionByID($this->defUnittypeID['weight']), $density, $densityType);
    	} 
		if ($this->wasteType['volume']) {
	    	$this->productSumm['volume'] += $unitTypeConverter->convertFromTo($product['quantity'], $unitTypeDetails["description"], $unittype->getDescriptionByID($this->defUnittypeID['volume']), $density, $densityType);
    	}
    }
    
    public function calculateTotalWaste($wasteArray, $unittypeType = null, $productSumm = null) {
    	$value = 0;
    	$this->wasteData = $wasteArray;
    	$unittype = new Unittype($this->db);
    	if ($unittypeType == null) {
    		if ($this->wasteType['weight'] === false && $this->wasteType['volume'] === false) {
    			$this->error = "Error! Products summary quantity can not be calculated in one type(weight or volume). So waste for them cannot be calculated! Please set density for products or set all products in one type. Voc calculated with waste = 0.";
    			return $result = array('error' => 'wasteCalc');
    		} else {
    			$totalW = 0;
    			$totalV = 0;
    			foreach ($wasteArray as $wasteStream) {
    				if (isset($wasteStream['count'])) {
    					foreach ($wasteStream as $key => $pollution) {
    						if ($key !== 'id' && $key !== 'count' && $key !== 'storage_id') {
	    						$type = $unittype->isWeightOrVolume($pollution['unittypeID']);
	    						if ($type == 'weight') {
	    							$totalW++;
	    						} 
	    						if ($type == 'volume'){
	    							$totalV++;
	    						}
    						}
    					}
    				} else {
    					if (isset($wasteStream['unittypeID'])) {    						
		    				$type = $unittype->isWeightOrVolume($wasteStream['unittypeID']);
		    				if ($type == 'weight') {
			    				$totalW++;
		    				} 
							if ($type == 'volume'){
			    				$totalV++;
		    				}
    					}
    				}     				
    			}
    			
    			
    			
    			if ($totalW != 0 && $totalV != 0) {
    				$this->error = "Error! Please set waste in one type(weight or volume)! Voc was calculated with waste = 0.";
    				return $result = array('error' => 'wasteCalc');
    			} elseif ($totalW != 0 && $totalV == 0 || $totalV != 0 && $totalW == 0) {
    				if ($this->wasteType['weight'] && $totalW != 0) {
    					$unittypeType = 'weight';
    				} elseif ($this->wasteType['volume'] && $totalV != 0) {
    					$unittypeType = 'volume';
    				}  else {
    					$this->error = "Error! Products summary quantity can not be calculated in the same type as waste(weight or volume). So waste for them cannot be calculated! Please set density for products or set all products in same type as waste. Voc calculated with waste = 0";
    					return $result = array('error' => 'wasteCalc');
    				}
    			} 
//    			$unittypeType = ($totalW !=0 && $totalV == 0)?(($this->wasteType['weight'])?'weight':'volume'):(($this->wasteType['volume'])?'volume':'weight');
//    			$unittypeType = ($this->wasteType['weight'])?'weight':'volume';
    		}
    	}
    	if ($productSumm == null) {
    		$productSumm = $this->productSumm[$unittypeType];
    	}
    	$newWasteArr = array();
    	$error = false;
    	foreach ($wasteArray as $wasteStream) {
    	//$wasteStream can be array of details or array of pollutions with details
    		if (isset($wasteStream['count'])) {
    			//$wasteStream - array of pollutions
    			$newPollutions = array();
    			foreach($wasteStream as $key => $pollution) {
    				if ($key !== 'count' && $key !== 'id' && $key !== 'storage_id') {
						$pollution['value'] = str_replace(',','.',$pollution['value']);
    					$quantity = $this->calculateWasteStream($pollution,$unittypeType,$productSumm,'pollution');
    					$value += $quantity['value'];  
    					$pollution ['validation']=  $quantity['error'];	
    					if ($pollution['validation'] != 'success') {
    						$pollution['value'] = 0;
    						$error = true;
    					}
    					if ($quantity['value'] == 0 && ($pollution['value'] == '' || is_null($pollution['value']))) {
							$pollution['value'] = 0;
						}
    					$newPollutions []= $pollution;
    				}			
    			}
    			$newPollutions['storage_id'] = $wasteStream['storage_id'];
    			$newPollutions['count'] = $wasteStream['count'];
    			$newPollutions['id'] = $wasteStream['id'];
    			$newWasteArr []= $newPollutions;
    		} else {
    			//$wasteStream - array of details about stream(without pollutions)
				$wasteStream['value'] = str_replace(',','.',$wasteStream['value']);
    			$quantity = $this->calculateWasteStream($wasteStream,$unittypeType,$productSumm,'wasteStream');
				$value += $quantity['value'];
				$wasteStream ['validation'] = $quantity['error'];
				if ($wasteStream['validation'] != 'success') {
					$wasteStream ['value'] = 0;
					$error = true;
				}
				if ($quantity['value'] == 0 && ($wasteStream['value'] == '' || is_null($wasteStream['value']))) {
					$wasteStream['value'] = 0;
				}
				$newWasteArr []= $wasteStream;
    		}
    	}
		$this->error = $error;
    	$this->wasteData = $newWasteArr;
    	$unittype = new Unittype($this->db);
    	$totalWasteData = array(
    		'value' => $value,
    		'unittypeClass' => $unittype->getUnittypeClass($this->defUnittypeID[$unittypeType]),
    		'unittypeID' => $this->defUnittypeID[$unittypeType]
    	);

    	return $totalWasteData;
    }
    
    public function calculateWasteStream($wasteData, $unittypeType, $productSumm, $type = 'wasteStream') {
		$unittype = new Unittype($this->db);
		$error = 'success';
		if (!is_numeric($wasteData['value']) || $wasteData['value'] < 0) {
			if (!(/*$type=='wasteStream' &&*/ is_null($wasteData['value']) || $wasteData['value'] == '')) {
				$error = "Error! Wrong waste value(&#147;".$wasteData['value']."&#148;)! Please enter valid waste value! Waste value must be a positive number. VOC was calculated with waste = 0.";
			}
			$value = 0;
		}
	  /*  $density = $this->getDensity($wasteData['id'],$type);
	    $densityUnitID = $this->getDensityUnitID($wasteData['id'],$type);
	    $densityObj = new Density($this->db,$densityUnitID);
	    $densityType = array (
		    'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
	    );*/
	    $unitTypeConverter = new UnitTypeConverter();
	    //TODO WHAT SHOULD WE DO WITH PERCENTS?!!
	    if ($wasteData['unittypeID'] && !isset($value)) {
	    	$wasteType = $unittype->isWeightOrVolume($wasteData['unittypeID']);
	    	if ($wasteType != $unittypeType) {
//	    		if ($this->wasteType[$wasteType]) {
//	    			$unitTypeDetails = $unittype->getUnittypeDetails($wasteData['unittypeID']);
//		    		$valueInType = $unitTypeConverter->convertFromTo($wasteData['value'], $unitTypeDetails["description"], $unittype->getDescriptionByID($this->defUnittypeID[$wasteType])/*, $density, $densityType*/);
//	    			$wasteData['value'] = ($valueInType/$this->productSumm[$wasteType])*$this->productSumm[$unittypeType];
//	    			$wasteData['unittypeID'] = $this->defUnittypeID[$unittypeType];
//	    		} else {
	    			$error = "Error! Can not calculate waste for mix. Please enter valid waste value in $unittypeType unittype. VOC was calculated with waste = 0.";
	    			$wasteData['unittypeID'] = $this->defUnittypeID[$unittypeType];
	    			$wasteData['value'] = 0;
//	    		}
	    	}
		    $unitTypeDetails = $unittype->getUnittypeDetails($wasteData['unittypeID']);
		    $value = $unitTypeConverter->convertFromTo($wasteData['value'], $unitTypeDetails["description"], $unittype->getDescriptionByID($this->defUnittypeID[$unittypeType])/*, $density, $densityType*/);
	    }
//	     else {
//		    //percent!!!
//		    $value = $wasteData['value']*$productSumm/100;
//	    }
	    return array (
	    		'value' => $value,
	    		'error' => $error
	    	);
    }
    
    public function getWasteStreamsFromMix($mixId) {
    	$query = "SELECT * FROM ".TB_WASTE." WHERE mix_id = '$mixId'";
    	$this->db->query($query);
    	$data = $this->db->fetch_all();
    	$result = array();
    	$wsArray = array();
    	$unittype = new Unittype($this->db);
    	foreach($data as $waste) {
    		if(isset($wsArray[$waste->waste_stream_id])) {
    			$wsArray[$waste->waste_stream_id]['count'] +=1;
    			$wsArray[$waste->waste_stream_id]['storage_id'] = $waste->storage_id; //barrels were stored waste.(Storage.class.php)
    			$wsArray[$waste->waste_stream_id]['pollutions'] []= array(
    					"id"			=> $waste->pollution_id,						
		    			"value"			=> $waste->value,
						"unittypeClass"	=> ($waste->method == 'weight')?$unittype->getUnittypeClass($waste->unittype_id):'%',
						"unittypeID"	=> $waste->unittype_id
    			); 
    		} else {
    			if ($waste->pollution_id !== null) {
    				$wsArray[$waste->waste_stream_id]['count'] = 1;
    				$wsArray[$waste->waste_stream_id]['storage_id'] = $waste->storage_id; //barrels were stored waste.(Storage.class.php)
    				$wsArray[$waste->waste_stream_id]['pollutions'] []= array(
    					"id" 			=> $waste->pollution_id,
    					"value" 		=> $waste->value,
    					"unittypeClass" => ($waste->method == 'weight')?$unittype->getUnittypeClass($waste->unittype_id):'%',
    					"unittypeID" 	=> $waste->unittype_id
    				);
    			} else {
    				$wsArray[$waste->waste_stream_id] = array(
    					"id" 			=> $waste->waste_stream_id,
    					"value" 		=> $waste->value,
    					"unittypeClass" => ($waste->method == 'weight')?$unittype->getUnittypeClass($waste->unittype_id):'%',
    					"unittypeID" 	=> $waste->unittype_id,
    					'storage_id'	=> $waste->storage_id    	//barrels were stored waste.(Storage.class.php)				
    				);
    			}
    		}

    	}

    	foreach($wsArray as $waste_id => $waste) {
    		if(isset($waste['count'])) {
    			$toResult = array(
    				'count' => $waste['count'],
    				'id' => $waste_id,
    				'storage_id' => $waste['storage_id']
    			);
    			$i = 0;
    			foreach($waste['pollutions'] as $pollution) {
    				$toResult[$i] = $pollution;
    				$i++;
    			}
    		} else {
    			$toResult = $waste;
    		}
    		$result []= $toResult;
    	}
    	return $result;

    }
    

    public function getWasteStreamsToPollutionsList() {
    	$wsList = $this->getWasteStreamsFullList();
    	$resultList = array();
    	foreach($wsList as $ws) {
    		$resultList[$ws['id']]['name'] = $ws['name'];
    		if($ws['pollutions'] == 'false') {
    			$resultList[$ws['id']][0] = 'false';
    		} else {
    			$pollutionsList = $this->getPolutionList($ws['id']);
    			foreach($pollutionsList as $pollution) {
    				$resultList[$ws['id']][$pollution['id']] = $pollution['name'];
    			}
    		}
    	}
    	return $resultList;
    }
    
    public function calculateTypeAndSummForMix($mixID) {
    	$mix = new Mix($this->db);
    	$this->wasteType = array(
    		'weight' => true,
    		'volume' => true
    	);
    	$this->productsSumm = array(
    		'weight' => 0,// in lbs id = 2
    		'volume' => 0// in gal id = 1
    	);
    	$products = $mix->getMixProducts($mixID);
    	foreach ($products as $product) {
    		$this->checkWasteType($product);
    	}
    }
    
    public function getDensity($idOfObject, $objectType = 'wasteStream') {
    	$query = "SELECT density FROM ".(($objectType == 'wasteStream')?TB_WASTE_STREAMS:TB_POLLUTION)." WHERE id = '$idOfObject' ";
    	$this->db->query($query);
    	$data = $this->db->fetch(0);
    	return $data->density;
    }
    
    public function getDensityUnitId($idOfObject, $objectType = 'wasteStream') {
    	$query = "SELECT density_unit_id FROM ".(($objectType == 'wasteStream')?TB_WASTE_STREAMS:TB_POLLUTION)." WHERE id = '$idOfObject' ";
    	$this->db->query($query);
    	$data = $this->db->fetch(0);
    	return $data->density_unit_id;

    }
}
?>