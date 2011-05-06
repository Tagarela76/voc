<?php

class MLogbook {

    function MLogbook() {
    }
    
    public function getNewObject($db) {
    	return new Logbook($db);
    }
    
    /**
     * function prepareBrowse($params) - prepare params for smarty to view logbook bookmark's page
     * @param array $params - $db, $facilityID, $toFind, $page, $q, $filter,$sort $filterData
     * @return array for smarty
     */
    public function prepareBrowse($params) {
    	extract($params);  	    	
    	$logbook = new Logbook($db, $facilityID);
    	$pagination = new Pagination($logbook->countLogbookRecords($toFind, $filter));
    	if(!$toFind) {
    		$actionList = $logbook->getActionList($pagination,$filter,$sort);
    		
    		foreach($actionList as $ac) {
    			
    		}
    		
    		$pagination->url = "?action=browseCategory&category=facility&id=".$facilityID."&bookmark=logbook".
    		(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
    		(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
    		(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
    		(isset($filterData['filterField'])?"&searchAction=filter":"");  
    		 		
    	} else {
    		$actionList = $logbook->logbookSeach($toFind,$pagination);
    		$pagination->url = "?action=browseCategory&category=facility&id=".$facilityID."&bookmark=logbook&searchAction=search&q=".$q;    	
    	}    	
    	//$count = $logbook->countLogbookRecords($toFind);
    	//$pageCount = (int)ceil($count/ROW_COUNT);
    	
    	//if (!$page || $page<1 || $page > $pageCount) {
    	//	$page = 1;
    	//}
    	//$from = ROW_COUNT*($page - 1);
//    	if(!$toFind) {
//    		$actionList = $logbook->getActionList($from);
//    		$url = "?action=browseCategory&category=facility&id=$facilityID&bookmark=logbook";
//    	} else {
//    		$actionList = $logbook->logbookSeach($toFind,$from);
//    		$url = "?action=browseCategory&category=facility&id=$facilityID&bookmark=logbook&q=$q";
//    	}
//    	if ($pageCount > 20) {
//    		$first = ($page <= 10)?1:$page - 10;
//    		$last = ($page > $pageCount - 10)?$pageCount:$page + 10;
//    	} else {
//    		$first = 1;
//    		$last = $pageCount;
//    	}
    	return array(
			'actionList' => $actionList,
    		'pagination' => $pagination
		//	'pageCount' => $pageCount,
		//	'currentPage' => $page,
		//	'first' => $first,
		//	'last' => $last,
		//	'currentURL' => $url
		);
    }
    
    /**
     * function prepareAdd($params) - prepare params to add(edit) new logbook item
     * @param array $params - $db, $facilityID, $request, $save = true/false, $logbookType, $date, $department, $equipment,
     *  $description(if needed:inspections, monitoring), $operator(if needed: inspections, monitoring, malfunction), 
     * $action(if needed:monitoring), $reason(if needed:malfunction), $installed(if needed:filter), 
     * $removed(if needed:filter), $filter_type(if needed:filter), $filter_size(if needed:filter)
     * @return array for smarty
     */
    public function prepareAdd($params) {
    	extract($params);
    	if ($logbookType === null) {
    		$logbookType = LogbookAction::ACTION_INSPECTION;
    	}
    	$validation = array('summary' => 'success');
    	
    	if ($request['action'] == 'edit') {
    		$logbook = new Logbook($db);
    		$logbookItem = $logbook->getItemById($request['id']);
    		$logbookType = $logbookItem->type;
    	} else {
    		$logClassName = Logbook::LOGBOOK_PREFIX.$logbookType;
    		if(class_exists($logClassName)) {
	    		$logbookItem = new $logClassName($db);
	    		$logbookItem->type = $logbookType;
	    		$logbookItem->facility_id = $facilityID;
    		} else return false;
    	}
    	if ($save) {
    		if ($date==null)
	    	{
	    		$validation['summary'] = 'failed';
    			$validation['date'] = 'Error! Please enter date!';
    		}
    		//echo "set date"; 
	    	$logbookItem->date = date("Y-m-d",strtotime($date));
	    	//echo $logbookItem->date . " - " . date("Y-m-d",strtotime($date)) . " - " . $date; exit;
	    	$logbookItem->facility_id = $facilityID;
	    	switch ($logbookType) {
	    		case LogbookAction::ACTION_INSPECTION:
	    			if (trim($operator)==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['operator'] = 'Error! Please enter operator!';
    				}
	    			$logbookItem->department_id = $department;
	    			$logbookItem->equipment_id = $equipment;
	    			$logbookItem->description = $description;
	    			$logbookItem->operator = $operator;
	    			break;
	    		
	    		case LogbookAction::ACTION_SAMPLING:
	    			if (trim($operator)==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['operator'] = 'Error! Please enter operator!';
    				}    	
    				if (trim($action)==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['action'] = 'Error! Please enter action!';
    				}    				
	    			$logbookItem->department_id = $department;
	    			$logbookItem->equipment_id = $equipment;
	    			$logbookItem->description = $description;
	    			$logbookItem->operator = $operator;
	    			$logbookItem->action = $action;
	    			break;
	    		
	    		case LogbookAction::ACTION_ACCIDENT_PLAN:
		    		//here we should upload the file!
		    		if ($_FILES["upload"]['tmp_name']==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['upload'] = 'Error! Please choose file!';
    				}  
		    		$logbookItem->tmp_name = $_FILES["upload"]['tmp_name'];
		    		$uploads_dir = "../docs";
		    		$currentFile['name'] = $_FILES["upload"]["name"];
		    		if (strripos($currentFile['name'],".") == false) {
			    		$ext = "";
			    		$extNumberSymbols = 0;
		    		} else {        		
			    		$ext = substr($currentFile['name'],strripos($currentFile['name'],"."));        		
			    		$extNumberSymbols = strlen($currentFile['name']) - strripos($currentFile['name'],".");
		    		}
		    		$currentFile['real_name'] = substr(md5(substr($currentFile['name'],0,-$extNumberSymbols)),0,5).time().$ext;
		    		
		    		$logbookItem->link = $uploads_dir."/".$currentFile['real_name'];
	    			break;
	    		case LogbookAction::ACTION_MALFUNCTION:
	    			if (trim($operator)==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['operator'] = 'Error! Please enter operator!';
    				}  
    				if (trim($reason)==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['reason'] = 'Error! Please enter reason!';
    				} 
	    			$logbookItem->department_id = $department;
	    			$logbookItem->equipment_id = $equipment;
	    			$logbookItem->operator = $operator;
	    			$logbookItem->reason = $reason;
	    			break;
	    		
	    		case LogbookAction::ACTION_FILTER:
	    			if (trim($filter_type)==null)
	    			{
	    				$validation['summary'] = 'failed';
    					$validation['filter_type'] = 'Error! Please enter filter type!';
    				} 
	    			$logbookItem->department_id = $department;
	    			$logbookItem->equipment_id = $equipment;
	    			$logbookItem->installed = $installed;
	    			$logbookItem->removed = $removed;
	    			$logbookItem->filter_type = $filter_type;
	    			$logbookItem->filter_size = $filter_size;
	    			break;
	    		
	    		default:
	    			throw new Exception('deny');
	    	}
	    	if ($validation['summary'] == 'success') {
	    		//var_Dump($logbookItem); exit;
	    		$logbookItem->save();
	    		return true;
	    	}
    	}
    	$constAction = array(
    		LogbookAction::ACTION_INSPECTION,
			LogbookAction::ACTION_SAMPLING,
			LogbookAction::ACTION_ACCIDENT_PLAN,
			LogbookAction::ACTION_MALFUNCTION,
			LogbookAction::ACTION_FILTER 
    	);
    	$department = new Department($db);
    	$departmentList = $department->getDepartmentListByFacility($facilityID);
    	return array(
    		'constAction' 		=> $constAction,
    		'logbookType' 		=> $logbookType,
    		'validation' 		=> $validation,
    		'data' 				=> $logbookItem,
    		'departmentList' 	=> $departmentList,
    		'equipment' 		=> (property_exists($logbookItem,'equipment_id'))?($logbookItem->equipment_id):null
    	);
    }
    
    /**
     * function prepareView($params) - prepare params for smarty to view logbook item
     * @param array $params - $db, $facilityID, $id
     * @return array for smarty
     */
    public function prepareView($params) {
    	extract($params);
    	$logbook = new Logbook($db,$facilityID);
    	$logbookItem = $logbook->getItemById($id);
    	$depObj = new Department($db);
    	$depDetails = $depObj->getDepartmentDetails($logbookItem->department_id); 
    	$logbookItem->department_name=$depDetails['name']; 
    	return array('data' => $logbookItem, 'logbookType' => $logbookItem->type);
    }
    
    /**
     * function prepareDelete($params) - prepare params for delete logbiik item
     * @param array $params - $db, $facilityID, $confirmed = false/true, $idArray
     * @return array for smarty/true
     */
    public function prepareDelete($params) {
    	extract($params);
    	if (!$confirmed) {
    		$arrayForDelete = array();
    		$logbook = new Logbook($db, $facilityID);
    		foreach($idArray as $id) {
    			$logbookItem = $logbook->getItemById($id);
    			$arrayForDelete []= array(
    				'id' => $logbookItem->id,
    				'name' => "Date: ".$logbookItem->date.". Type: $logbookItem->type. " //TODO: should we put here more info about logbook items we'l delete?
    			);
    		}
    		return array('itemForDelete' => $arrayForDelete);
    	} else {
    		$logbook = new Logbook($db, $facilityID);
    		foreach($idArray as $id) {
    			$logbookItem = $logbook->getItemById($id);
    			$logbookItem->delete();
    		}
    		return true;
    	}
    }
}
?>