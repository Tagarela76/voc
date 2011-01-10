<?php

class MWasteStreams {
	
	public $resultParams; // we collect wasteSteans array in $this->resultParams['waste'] !!!
	
	private $isForm;
	
    function MWasteStreams() {
    }
    
    /**
     * function getNewObject($db) - return new WasteStreams object
     * @param $db
     * @return WasteStreams object
     */
    public function getNewObject($db) {
    	return new WasteStreams($db);
    }
    
    /**
     * function prepare4mixAdd($params) - load waste streams from form to wasteStreams array($this->resultParams['waste']) and prepare params for smarty
     * @param array $params - $db,( $xnyo - dont needed due no filters for post) $isForm = true/false, $facilityID [, $id - only for edit mix] [, $companyID - now its no needed...]
     * @return array for smarty, $this->resultParams['waste']
     */
    public function prepare4mixAdd($params) {
    	extract($params);
    	$ws = new WasteStreams($db);
    	$storageObj= new Storage($db);
    	$isDeletedStorageError='false';
    	if (isset($id)) {
    		$this->resultParams['loadedWaste'] = $ws->getWasteStreamsFromMix($id);
    	}
    	if ($isForm) {
    		$this->isForm=true;
    		$date = $_POST['creationTime'];
    		$wsCount = $_POST['wasteStreamCount'];    		    		
    		$storageValidation = array();
    		$deletedStorageValidation = array();    				
    		for ($i=0;$i<$wsCount;$i++) {
    			$pollutionCount = $_POST['pollutionCount_'.$i];
    			$storage = $_POST['selectStorage_'.$i];
    			
    			$compareResult = $storageObj->compareDateWasteAndDeletedStorage($storage,$date); 
    			  			
    			if ($compareResult==1)
    			{
    				$storageName = $storageObj->getStorageNameByID($storage);
    				$deletedStorageValidation[$i] ="Warning: Storage ($storageName) was remote before $date";
    				$isDeletedStorageError='true';
    			}
    			else
    			{
    				$deletedStorageValidation[$i] = "false";
    			}
    			     			
    			if ($pollutionCount == 0) {
	    			$wasteData = array (
	    				"id"			=> $_POST["wasteStreamSelect_$i"],						
		    			"value"			=> $_POST["quantityWithoutPollutions_$i"],
						"unittypeClass"	=> $_POST["selectWasteUnittypeClassWithoutPollutions_$i"],
						"unittypeID"	=> (isset($_POST["selectWasteUnittypeWithoutPollutions_$i"])) ? $_POST["selectWasteUnittypeWithoutPollutions_$i"] : false, //it's no 'false' in any case cause we don't use percent any more in waste streams!
						'storage_id'	=> $storage
	    			);	
	    			//validation for storages!!!	we'll prepare array with storage_id indexes that collected data with values+unittypes witch we'll add to storages	
	    			if (!isset($storageValidation[$storage][$wasteData['unittypeID']])) {
	    				$storageValidation[$storage][$wasteData['unittypeID']] = $wasteData['value'];
	    			} else {
	    				$storageValidation[$storage][$wasteData['unittypeID']] += $wasteData['value'];
	    			}			
	    			$unittype = new Unittype($db);
	    		//	$wasteData["unitTypeList"] = ($wasteData["unittypeID"]) ? (($unittype->getUnittypeListDefaultByCompanyId($companyID,$wasteData["unittypeClass"]))?$unittype->getUnittypeListDefaultByCompanyId($companyID,$wasteData["unittypeClass"]):$unittype->getUnittypeListDefault($wasteData['unittypeClass'])) : false;															
	    			$this->resultParams['waste'][$i] = $wasteData;   				
    			} else {
    				$this->resultParams['waste'][$i] = array();
    				$this->resultParams['waste'][$i]['count'] = $pollutionCount;
    				$this->resultParams['waste'][$i]['storage_id'] = $storage;
    				$this->resultParams['waste'][$i]['id'] = $_POST["wasteStreamSelect_$i"];
    				$value = 0;
	    			for ($j=0;$j<$pollutionCount;$j++) {
		    			$wasteData = array (
		    				"id"			=> $_POST["selectPollution_$i".'_'.$j],						
			    			"value"			=> $_POST["quantity_$i".'_'.$j],
							"unittypeClass"	=> $_POST["selectWasteUnittypeClass_$i".'_'.$j],
							"unittypeID"	=> (isset($_POST["selectWasteUnittype_$i".'_'.$j])) ? $_POST["selectWasteUnittype_$i".'_'.$j] : false
		    			);			
		    			$unittype = new Unittype($db);
		    		//	$wasteData["unitTypeList"] = ($wasteData["unittypeID"]) ? $unittype->getUnittypeListDefault($wasteData["unittypeClass"]) : false;															
		    			$this->resultParams['waste'][$i][$j] = $wasteData;
		    			$value += $wasteData['value'];
		    			//validation for storages!!! we'll prepare array with storage_id indexes that collected data with values+unittypes witch we'll add to storages
		    			if (!isset($storageValidation[$storage][$wasteData['unittypeID']])) {
		    				$storageValidation[$storage][$wasteData['unittypeID']] = $wasteData['value'];
		    			} else {
		    				$storageValidation[$storage][$wasteData['unittypeID']] += $wasteData['value'];
		    			}
	    			}
    			}       				
    		}    		   			
    	    $storage = new Storage($db);
    		$storageOverflow = $storage->validateOverflow($storageValidation, $date, $id);	
    		if ($storageOverflow !== false) {
	    		$result['storageError'] = "Error! Choosen storages are overflow!";
	    	}
	    	$result['storageOverflow'] = json_encode($storageOverflow);
	    	   	    
    	} else {
    		$this->isForm=false;
    		if (isset($id)) {
    			$mix = new Mix($db);
    			$mixDetails = $mix->getMixDetails($id);
    			$date = $mixDetails['creationTime'];
    		} else {
    			$date = date('m-d-Y');
    		}
    		$result['storageOverflow'] = json_encode(false);
    	}
    	//now we should convert $date from mm-dd-yyyy into yyyy-mm-dd
    	$date = substr($date,-4,4)."-".substr($date,0,2)."-".substr($date,3,2);

		if ($deletedStorageValidation==null)
		{
			$result['deletedStorageValidation']='false';
		}
		else
		{
			$result['deletedStorageValidation']=json_encode($deletedStorageValidation);
		}
		$result['isDeletedStorageError']=$isDeletedStorageError;
		
    	$result['wasteStreamsList'] = json_encode($ws->getWasteStreamsFullList());
    	$result['wasteStreamsWithPollutions'] = json_encode($ws->getWasteStreamsToPollutionsList());
    	$storage = new Storage($db);
    	$result['storages'] = json_encode($storage->getCurrentStoragesGroupedByWaste($facilityID,$date,$id));
    	return $result;
    }
    
    /**
     * function calculateWaste($params) - check waste steams and calculate total waste for voc calculations
     * @param array $params - $db, $products
     */
     public function calculateWaste($params) {
     	extract($params);
     	$ws = new WasteStreams($db);
     	foreach ($products as $product) {
     		$ws->checkWasteType($product);
     	}
     	if ($this->isForm && !isset($this->resultParams['waste']))
     	{
     		$wasteTotal =0;
     	}
     	else
     	{
	     	if (!isset($this->resultParams['waste'])) {
	     		$this->resultParams['waste'] = $this->resultParams['loadedWaste'];
	     	}
	     	$wasteTotal = $ws->calculateTotalWaste($this->resultParams['waste']);
     	}
     	$this->resultParams['waste'] = $ws->wasteData; // in waste streams array we take array with validation from WS class
     	return array(
     		'wasteData' => $wasteTotal,
     		'wasteArr' => $this->resultParams['waste'],
     		'ws_error' => $ws->error
     	);
     }
     
     /**
      * function prepareSaveWasteStreams($params) - save waste for new mix
      * @param array $params - $db, $id(mix id)
      */
     public function prepareSaveWasteStreams ($params) {
     	extract($params);
     	$ws = new WasteStreams($db);
     	$ws->addWasteStreamsToMix($id,$this->resultParams['waste']);
     	//TODO: storage!!! //done in WasteStreams.class.php addWasteStreamsToMix()
     }
     
     /**
      * function prepareViewMix($params) - if it needed calculate total waste to calculate and save waste percent in view mix
      * @param $params - $db, $id(mix id)
      */
     public function prepareViewMix ($params) {
	     extract($params);
	     $ws = new WasteStreams($db);
	     $wasteArray = $ws->getWasteStreamsFromMix($id);
	     $ws->calculateTypeAndSummForMix($id);
	     $wasteData = $ws->calculateTotalWaste($wasteArray);
	     return array('wasteData' => $wasteData);
     }
     
      /**
     * function prepareViewStorage($params)
     * @param array params - $db, $storage_id, $isDocs = true/false
     * @return array for smarty
     */
     public function prepareViewStorage ($params) {
     	extract($params);
     	$storageObj=new Storage($db,$storage_id);
     	$unittypeObj = new Unittype($db); 
     	$wasteStreamObj = new WasteStreams($db); 
     	    	   	
     	$result = array( 
     		'data' => $storageObj,
     		//'weightUnittype' => $unittypeObj->getNameByID($storageObj->weight_unittype),
     		'volumeUnittype' => $unittypeObj->getNameByID($storageObj->volume_unittype),
     		'deleteORrestore' => ($storageObj->active == 1)?"delete":"restore",
     		'suitability'=>$wasteStreamObj->getNameById($storageObj->suitability)     		
     	);
     	return $result;
     }
     
     /**
      * function prepareBrowseStorage ($params) - prepare data to browse list of storages
      * @param array $params - $db, $facilityID, $status = active/removed, $page, $isDocs = true/false, $sort
      */
     public function prepareBrowseStorage ($params) {
     	extract($params);
     	$unittypeObj = new Unittype($db);
     	$wasteStreamObj = new WasteStreams($db);
     	$storage = new Storage($db);
     	$count = $storage->countStorages($facilityID, $status);
     	
     	$pagination = new Pagination($storage->countStorages($facilityID, $status));
     	$pagination->url = "?action=browseCategory&category=facility&id=".$facilityID."&bookmark=wastestorage&tab=".(($status == 'active')?"active":"removed");
     	//$pageCount = (int)ceil($count/ROW_COUNT);
     	//if (!$page || $page<1 || $page > $pageCount) {
    	//	$page = 1;
    	//}
    	//$from = ROW_COUNT*($page - 1);
//    	if ($pageCount > 20) {
//    		$first = ($page <= 10)?1:$page - 10;
//    		$last = ($page > $pageCount - 10)?$pageCount:$page + 10;
//    	} else {
//    		$first = 1;
//    		$last = $pageCount;
//    	}
    	$storageList = $storage->getCurrentList($facilityID,$pagination,$status,$sort);
    	if ($isDocs) {
    		$idArray = array();
    		foreach($storageList as $storageObj) {
    			if (!in_array($storageObj->document_id,$idArray)) {
    				$idArray []= $storageObj->document_id;
    			}
    		}
    	}
    	//$url = "?action=browseCategory&category=facility&id=$facilityID&bookmark=wastestorage&tab=".(($status == 'active')?"active":"removed");
     	return array(
			'data' => $storageList,
			//'pageCount' => $pageCount,
			//'currentPage' => $page,
			//'first' => $first,
			//'last' => $last,
			//'currentURL' => $url,
			'pagination'	=> $pagination,
			'unittypeObj'=>	$unittypeObj,
			'wasteStreamObj'=>$wasteStreamObj,
			'idArray' => $idArray //its needed for docs	    
		);
     }
     
      /**
     * function prepareAddStorage($params)
     * @param array params - $db, $facilityID, $companyID, $save, $action, $capacity_volume, [$capacity_weight], $max_period,
     * 						$name,$selectSuitability, $volume_unittype, [$weight_unittype], density, density_unit_id $isDocs = true/false
     * 						if (edit){$storage_id} if (isDocs){$document_id}
     * @return array for smarty
     */
     public function prepareAddStorage ($params) {
     	extract($params);     	
     	$storageObj=new Storage($db,$storage_id); 
     	$unittypeObj = new Unittype($db); 
     	$wastStreamObj= new WasteStreams($db);     	
     	
     	if ($action=='edit') 
     	{    	
     		$result['action']='edit';
     		$suitability=$wastStreamObj->getNameById($storageObj->suitability);     		     		  		  		
     	}     		
     	else
     	{
     		$result['action']='addItem';
     	}
     	
     	if ($save===true)
     	{     		
     		$storageObj->capacity_volume=$capacity_volume;
     		$storageObj->capacity_weight=$capacity_weight;
     		$storageObj->density = $density;
     		$storageObj->density_unit_id = $density_unit_id;
     		$storageObj->max_period=$max_period;
     		$storageObj->name=trim($name);
     		if (!is_null($selectSuitability)) {
     			$storageObj->suitability=$selectSuitability;
     		}
     		$storageObj->facility_id=$facilityID;
     		$storageObj->volume_unittype=$volume_unittype;
     		$storageObj->weight_unittype=$weight_unittype;     		
     		
     		if ($isDocs) {
     			$storageObj->document_id=$document_id;
     		}
     		
     		$validation['summary'] = 'success';
     		
     		if (trim($name) == null)
     		{
    			$validation['summary'] = 'failed';
    			$validation['name'] = 'Error! Please enter name!';
    		} elseif (!($storageObj->validateName($storageObj->name, $storageObj->facility_id, $storage_id))) {
    			$validation['summary'] = 'failed';
    			$validation['name'] = 'Error! This name is already used! Please enter unique name!';
    		}
     		
     		if (trim($capacity_volume) == null || $capacity_volume <= 0 || !is_numeric($capacity_volume))
     		{
    			$validation['summary'] = 'failed';
    			$validation['capacity_volume'] = 'Error! Please enter valid quantity!';
    		}
    		
//    		if (trim($capacity_weight) == null || $capacity_weight <= 0 || !is_numeric($capacity_weight))
//     		{
//    			$validation['summary'] = 'failed';
//    			$validation['capacity_weight'] = 'Error! Please enter valid quantity!';
//    		}
    		
    		if (trim($max_period) == null) {
    			$max_period = 90;
    			$storageObj->max_period=$max_period;
     		} elseif ( $max_period <= 0 || !is_numeric($max_period))
     		{
    			$validation['summary'] = 'failed';
    			$validation['max_period'] = 'Error! Please enter valid value of days!';
    		}    		
     	}      	
     	
     	if ($action=='addItem')     		
     			$suitability=$wastStreamObj->getWasteStreamsFullList();    	
     	
     	if ($save && $validation['summary'] == 'success') 
     	{
    		$storageObj->save();
    		return true;
     	}	
		//density 
     	$cDensity = new Density($db);
     	$cUnitType = new Unittype($db);
     	$densityDetails = $cDensity->getAllDensity($cUnitType);	
     	$densityDefault = (!is_null($storageObj->density_unit_id))?$storageObj->density_unit_id:$densityDetails[0]['id'];
     	
     	$result = array(      		
     		'volumeUnittypes'=>$unittypeObj->getUnittypeListByCategory('volume',$companyID),
     		'weightUnittypes'=>$unittypeObj->getUnittypeListByCategory('weight',$companyID),
     		'data'=> $storageObj,  
     		'densityDetails'=>$densityDetails,
     		'densityDefault'=>$densityDefault,  
     		'validation'=>$validation,
     		'suitability'=>$suitability		    		
     	);     	
     	
     	return $result;
     }
     
     /**
     * function prepareDeleteStorage($params) - prepare params for delete/restore/empty storage
     * @param array $params - $db, $facilityID, $confirmed = false/true, $idArray[, $date = "yyyy-mm-dd"], $method = empty/delete/restore
     * @return array for smarty/true
     */
     public function prepareDeleteStorage($params) {
    	extract($params);
    	if (!$confirmed) {
    		$arrayForDelete = array();
    		$error = null;
    		$date = date('Y-m-d',strtotime($date));
    		$curDate = date('Y-m-d');
    		if ($date > $curDate && $method != 'restore') {
    			$error = 'date';
    		} 
    		foreach($idArray as $id) {
	    		$storage = new Storage ($db, $id);
	    		$arrayForDelete []= array(
		    		'id' => $storage->storage_id,
					'name' => $storage->name
	    		);
    		}
    		
    		return array('itemForDelete' => $arrayForDelete, 'error' => $error);
    	} else {
    		$storage = new Storage($db);
    		switch ($method)
    		{
    			case 'empty':
    				$storage->emptyStorages($idArray,$date);
    			break;
    			case 'delete':
    				foreach($idArray as $id) 
    				{
						$storage->delete($id,$date);
	    			}
    			break;
    			case 'restore':
    				foreach($idArray as $id) 
    				{
						$storage->restore($id);
	    			}
    			break;
    		}
    		    		
    		return true;
    	}     	
     }
}
?>