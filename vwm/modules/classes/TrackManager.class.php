<?php

	class TrackManager extends TrackingSystem {
	
		private $db;

    	public function TrackManager($db) {
    		$this->db = $db;
    	}
    	
    	
    	public function trackAutocomplete($request) 
    	{    		
			$fields = array('table_name');
			$query = "SELECT ";
			foreach ($fields as $field) {
				$query .= " `$field`, LOCATE('$occurrence',`$field`) AS occurrence$field,";
			}
			$query = substr($query,0,-1);
			$query .= " FROM ".self::TRASH_TB_NAME;

//			$query = "SELECT `$field`, LOCATE('$occurrence',`$field`) AS occurrence FROM logbook WHERE  LOCATE('$occurrence',`$field`) != 0  ORDER BY occurrence  DESC";
			$this->db->query($query);
			//var_dump($query);
			$data = $this->db->fetch_all();
			$array = array();
			foreach ($data as $record) {
				foreach($fields as $field) {
					$occurname = "occurrence".$field;
					if($record->$occurname != 0 && !in_array($record->$field,$array)) {
						$array []= $record->$field;
						break;
					}
				}
			}
			return $array;
    	}
    	
    	public function getTrackList($searchText) 
    	{
    		$subQuery="";
    		if ($searchText!=null &&  $searchText!='false')
    		{    	
    			$crudText=false;		
    			switch (strtolower($searchText))
    			{
    				case 'cr':
    				case 'cre':
    				case 'crea':
    				case 'creat':
    				case 'create':
    					$crudText='C';
    					break;
    				case 'up':
    				case 'upd':
    				case 'upda':
    				case 'updat':
    				case 'update':
    					$crudText='U';
    					break;    				
    				case 'de':
    				case 'del':
    				case 'dele':
    				case 'delet':
    				case 'delete':
    					$crudText='D';
    					break;   					
    			}
    			/*$subQuery=	" AND (".(($crudText!=false)? " CRUD='$crudText' ":" FALSE "). 
    						" OR user_id IN (SELECT user_id FROM user WHERE accessname like '%$searchText%' ) ".
    						" OR  IF((data REGEXP '&[^\s]*;'),HTML_UnEncode(data),data) like '%description\":\"$searchText%' ".
    						" OR IF((data REGEXP '&[^\s]*;'),HTML_UnEncode(data),data) like '%equip_desc\":\"$searchText%' ".
    						" OR IF((data REGEXP '&[^\s]*;'),HTML_UnEncode(data),data) like '%name\":\"$searchText%' ".
    						" OR IF((table_name REGEXP '&[^\s]*;'),HTML_UnEncode(table_name),table_name) like '%$searchText%' ) ";*/
    			$searchText = mysql_escape_string($searchText);
    			$subQuery = " AND (".(($crudText!=false)? " CRUD='$crudText' ":" FALSE "). 
    						" OR user_id IN (SELECT user_id FROM user WHERE accessname like '%$searchText%' ) ".
    						" OR  IF((data REGEXP '&[^\s]*;'),data,data) like '%description\":\"$searchText%' ".
    						" OR IF((data REGEXP '&[^\s]*;'),data,data) like '%equip_desc\":\"$searchText%' ".
    						" OR IF((data REGEXP '&[^\s]*;'),data,data) like '%name\":\"$searchText%' ".
    						" OR IF((table_name REGEXP '&[^\s]*;'),table_name,table_name) like '%$searchText%' ) ";
    			
    						
    		}    		
    		$query = 	"SELECT id FROM ".self::TRASH_TB_NAME." WHERE referrer IS NULL ".
    					$subQuery.
						"ORDER BY date DESC LIMIT 100";
			
    					//echo $query;
    		$this->db->query($query);     		 		

    		if ($this->db->num_rows() > 0) {
		    	$dataRows = $this->db->fetch_all();
		    	foreach ($dataRows as $dataRow) {			    			    		
		    		$tracks[] = TrackingSystem::getTrashByID($this->db, $dataRow->id);
		    	}		    	
		    	return $tracks;
    		} else {
    			return false;
    		}
    	}
    	
    	
    	
    	
    	public function makeUserFriendly(iTrash $trashRecord, User $userObj) {
	    	//	friendly type
	    	switch ($trashRecord->getCRUD()) {
		    	case "C":
			    	$friendlyType = "Create";
			    	break;
		    	case "U":
			    	$friendlyType = "Update";
			    	break;
		    	case "D":
			    	$friendlyType = "Delete";
			    	break;
	    	}
	    	
	    	//	friendly item
	    	$friendlyItem = $trashRecord->getTable();
	    	$friendlyItem = substr_replace($friendlyItem, strtoupper($friendlyItem{0}), 0, 1);	//	make first letter capital					
	    	
	    	//	friendly item name
	    	$originalData = json_decode($trashRecord->getData());	    	
	    	switch ($trashRecord->getTable()) {
		    	case TB_COMPANY:
			    	$friendlyItemName = $originalData->name." (".$originalData->company_id.")";
			    	break;
		    	case TB_FACILITY:
			    	$friendlyItemName = $originalData->name." (".$originalData->facility_id.")";
			    	break;
		    	case TB_DEPARTMENT:
			    	$friendlyItemName = $originalData->name." (".$originalData->department_id.")";
			    	break;
//		    	case TB_INVENTORY:
//			    	$friendlyItemName = $originalData->inventory_name." (".$originalData->inventory_id.")";
//			    	break;
			    case TB_INVENTORY:
			    	$friendlyItemName = $originalData->name." (".$originalData->id.")";
			    	break;
		    	case TB_EQUIPMENT:
			    	$friendlyItemName = $originalData->equip_desc." (".$originalData->equipment_id.")";
			    	break;
		    	case TB_USAGE:
			    	$friendlyItemName = $originalData->description." (".$originalData->mix_id.")";
			    	break;
			    case TB_ACCESSORY:
			    	$friendlyItemName = $originalData->name." (".$originalData->id.")";
			    	break;
	    	}					
	    			    		    
	    	//	friendly username
	    	$accessName = $userObj->getAccessnameByID($trashRecord->getUserID());
	    	
	    	$friendlyRecord = array(
		    	'id'		=>	$trashRecord->getID(),
				'type'		=>	$friendlyType,
				'item'		=>	$friendlyItem,
				'itemName'	=>	$friendlyItemName,
				'user'		=>	$accessName,
				'date'		=>	date('m/d/Y H:i:s',$trashRecord->getDate())				
	    	);	    	
	    	return $friendlyRecord;
    	}
    	
    	
    	
    	
    	public function getTracks(string $property, string $value) {
    		
    	}    	    	    	    	    	    	    	    	    	    	    	
    	
    	
    	
		//	public method - Dependencies supported    	
    	public function rollback(iTrash $trashRecord) {
    		//before rollback we should change back outputs for solvent plan in case we try to rollback mix and company has module reduction
	    	$this->checkForSolventPlanCorrection($trashRecord);
    		
    		//rollback:
    		switch ($trashRecord->getCRUD()) {
    			case "C":
	    			//	find younger trash records on this object
    				//	maybe it's too late to rollback...
    				//	...	execute DELETE
    		   		$this->_deleteData($trashRecord);
    				if (false !== ($dependedTrashRecords = $trashRecord->getDependencies(self::ALL_DEPENDENCIES, 'directback'))) {    					
    					foreach ($dependedTrashRecords as $dependedTrashRecord) {     						   			    						
	    					$this->_deleteData($dependedTrashRecord);
    					}
    				}
    				break;
    			case "U":
    				//	find younger trash records on this object
    				//	check integrity
    				//	maybe it's too late to rollback...
    				$youngerRecords = $trashRecord->areJuniorTrashesSet();
    				if ($youngerRecords['result']) {
    					return false;
    				}   				
    				$validation = $trashRecord->validateIntegrity();
    				if (!$validation['result']) {
    					return false;
    				}
    				 
    				$this->_rollback($trashRecord);
	   				$dependencies = $this->loadDependencies();
    				if (false !== ($dependedTrashRecords = $trashRecord->getDependencies(self::ALL_DEPENDENCIES, 'back'))) {
						$updatePerAnus = array();		
    					foreach ($dependedTrashRecords as $dependedTrashRecord) {       				
    						//	update per anus
    						//	delete everything once and than insert ar _rollback()
    						if (isset($dependencies[$dependedTrashRecord->getTable()]['updatePerAnus']) && $dependedTrashRecord->getCRUD() == 'U') {
	    						$data = json_decode($dependedTrashRecord->getData());
	    						$tableWhereWasUpdate = $dependencies[$dependedTrashRecord->getTable()]['updatePerAnus'];
	    						
	    						if (!$updatePerAnus[$data->$dependencies[$tableWhereWasUpdate]['foreignField']]) {									
									$query = "DELETE FROM ".$dependedTrashRecord->getTable()." " .
			    						"WHERE ".$dependencies[$tableWhereWasUpdate]['foreignField']." = '".$data->$dependencies[$tableWhereWasUpdate]['foreignField']."'";
		    						$this->db->query($query);		    								    						
		    						$updatePerAnus[$data->$dependencies[$tableWhereWasUpdate]['foreignField']] = true;	    							
	    						}	
    						}
			
							$this->_rollback($dependedTrashRecord);
    					}
    				}
    				    				
    				break;
    			case "D":
    				//	check integrity
    				//	load all back dependencies 
    				$validation = $trashRecord->validateIntegrity();
    				if (!$validation['result']) {
    					return false;
    				}
    				 
    				$this->_rollback($trashRecord);
    				if (false !== ($dependedTrashRecords = $trashRecord->getDependencies(self::ALL_DEPENDENCIES, 'back'))) {    				
    					foreach ($dependedTrashRecords as $dependedTrashRecord) {				
							$this->_rollback($dependedTrashRecord);							
    					}    				
    				}  	    				    				    							    				
    				break;
    			default:    		
    				echo "Silly input<br>";
    				return false;    				
    		}
    		//	DEPRECATED
    		//	exception part. Fking mix limit recalculation
//    		$data = json_decode($trashRecord->getData());  
//    		switch ( $trashRecord->getTable() ) {
//	    		case 'mix':
//		    		$creationMonth = (empty($data->creationTime)) ? date('m') : substr($data->creationTime,0,2);
//		    		$query = "SELECT mix_id FROM ".TB_USAGE." WHERE MONTH(creation_time) = ".$creationMonth." AND department_id = ".$data->department_id;				
//		    		$this->db->query($query);
//		    		if ($this->db->num_rows() > 0) {
//			    		$mixesData = $this->db->fetch_all();
//			    		$mixObj = new Mix($this->db);
//			    		foreach ($mixesData as $mixData) {				
//				    		$mixObj->calculateAndSaveMixLimits($mixData->mix_id);		
//			    		}	
//		    		}
//		    		break;
//	    		case 'equipment':
//		    		ini_set("max_execution_time","180"); 	    					
//		    		$query = "SELECT mix_id FROM ".TB_USAGE." WHERE equipment_id = ".$data->equipment_id;		
//		    		$this->db->query($query);
//		    		
//		    		$dbData = $this->db->fetch_all();
//		    		$mixObj = new Mix($this->db);												
//		    		foreach($dbData as $mixData) {									
//			    		$mixObj->calculateAndSaveMixLimits($mixData->mix_id);
//		    		}			
//		    		break;
//	    		case 'department':
//		    		break;
//	    		case 'facility':
//		    		break;
//    		}  	   
    						
    		return true;
    	}    	    	
    	
    	
    	
    	/**
    	 * 
    	 * Track action
    	 * @param string $table table name where action is (inventory, mix..)
    	 * @param string $primaryKey PK value of action record to track
    	 * @param string $CRUD Create Read Update Delete
    	 * @param iTrash $parentTrashRecord
    	 */
    	public function save2trash($table, $primaryKey, $CRUD, iTrash $parentTrashRecord = null) {    		    		
    		$dependencies = $this->loadDependencies();
    		$query = "SELECT * FROM ".$table." WHERE ".$dependencies[$table]['field']." = '".$primaryKey."'";
			$this->db->query($query);
			$dataRows = $this->db->fetch_all();
			
			$trashRecord = new Trash($this->db);
		
			foreach ($dataRows as $dataRow) {
				$parentID = (isset($parentTrashRecord)) ? $parentTrashRecord->getID() : null;

				$records = TrackingSystem::properties2array($dataRow);		
				$trashRecord->setTable($table);		
				$trashRecord->setData(json_encode($records[0]));
				$trashRecord->setUserID($_SESSION['user_id']);
				$trashRecord->setCRUD($CRUD);		//	C - Create, U - update, D - delete
				$trashRecord->setDate(time());	//	current time
				$trashRecord->setReferrer($parentID);

				$trashRecord->save();				
			}
			
			if ($CRUD != 'D') {
				//	load and save dependencies
				if (false !== ($dependencies = $trashRecord->getDependencies(TrackingSystem::HIDDEN_DEPENDENCIES))) {	

					foreach ($dependencies as $dependency) {
						$parentID = ($dependency->getParentObj() !== null) ? $dependency->getParentObj()->getID() : null;
						$dependency->setUserID($_SESSION['user_id']);
						$dependency->setCRUD($CRUD);		//	C - Create, U - update, D - delete
						$dependency->setDate(time());	//	current time					
						$dependency->setReferrer($parentID);
						$dependency->save();												
					}
				}
			}

			return $trashRecord;
    	}
    	
    	
    	
    	//	end layer rollback method - Dependencies are not supported
    	private function _rollback($trashRecord) {
    		//before rollback we should change back outputs for solvent plan in case we try to rollback mix and company has module reduction
			$this->checkForSolventPlanCorrection($trashRecord);
    		
    		$data = json_decode($trashRecord->getData());
    		switch ($trashRecord->getCRUD()) {
    			
    			
    			case "C":
    				//	...	execute DELETE    				
    				$dependencies = $this->loadDependencies();
    				$query = "DELETE FROM ".$trashRecord->getTable()." " .
    						"WHERE ".$dependencies[$trashRecord->getTable()]['field']." = ".$data->$dependencies[$trashRecord->getTable()]['field']."";
    				$this->db->query($query);
    				
		    		//		due to usage stats
    				if ($trashRecord->getTable() == TB_USAGE) {
    					$department = new Department($this->db);
    					$creationMonth = substr($data->creation_time,5,2);
						$creationYear = substr($data->creation_time,0,4);						
    					$department->decrementUsage($creationMonth, $creationYear, $data->voc, $data->department_id);
    				}
    				break;
    				
    				
    			case "U":
    				//	...	execute UPDATE
    				//	... DELETE at rollback() and INSERT exeptions like mixgroup! (updatePerAnus)    				
    				$dependencies = $this->loadDependencies();    	
    				if (isset($dependencies[$trashRecord->getTable()]['updatePerAnus'])) {    					
    					//	...  and insert
    					$sql = "";
    					foreach ($data as $field) {
	    					$sql .= (is_null($field)) ? "NULL, " : "'".$field."', ";
    					}
    					$sql = substr($sql,0,-2);
    					$query = "INSERT INTO ".$trashRecord->getTable()." () VALUES (".$sql.")";    					    					    				
    					$this->db->query($query);    				    			
    				} else {
    					//	due to usage stats
    					if ($trashRecord->getTable() == TB_USAGE) {
    						$query = 'SELECT voc, creation_time FROM '.TB_USAGE.' WHERE mix_id = '. $data->mix_id;		
							$this->db->query($query);
							if ($this->db->num_rows() > 0) {
								$oldData = $this->db->fetch(0);								
								//$oldVoc = $this->db->fetch(0)->voc;
    							$department = new Department($this->db);
    							$creationMonthOld = substr($oldData->creation_time,5,2);
								$creationYearOld = substr($oldData->creation_time,0,4);
    							$creationMonth = substr($data->creation_time,5,2);
								$creationYear = substr($data->creation_time,0,4);
								$department->decrementUsage($creationMonthOld, $creationYearOld, $oldData->voc, $data->department_id);						
    							$department->incrementUsage($creationMonth, $creationYear, $data->voc, $data->department_id);
							}    							
    					}
    					//	update
    					$sql = "";
    					foreach ($data as $property=>$value) {
    						$sql .= (is_null($value)) ? $property." = NULL, " : $property." = '".$value."', ";    				
    					}
						$sql = substr($sql,0,-2);					
    					$query = "UPDATE ".$trashRecord->getTable()." " .
    							"SET ".$sql." " .
    							"WHERE ".$dependencies[$trashRecord->getTable()]['field']." = ".$data->$dependencies[$trashRecord->getTable()]['field']."";    					
    					$this->db->query($query);
    				}			    			    				    				    				
    				break;
    				
    				
    			case "D":
    				//	...	execute INSERT     				   							
    				$sql = "";
    				foreach ($data as $field) {
    					$sql .= (is_null($field)) ? "NULL, " : "'".$field."', ";
    				}
    				$sql = substr($sql,0,-2);
    				$query = "INSERT INTO ".$trashRecord->getTable()." () VALUES (".$sql.")";    				
    				$this->db->query($query); 	

		    		//	due to usage stats
    				if ($trashRecord->getTable() == TB_USAGE) {
    					$department = new Department($this->db);
    					$creationMonth = substr($data->creation_time,5,2);
						$creationYear = substr($data->creation_time,0,4);						
    					$department->incrementUsage($creationMonth, $creationYear, $data->voc, $data->department_id);
    				}
    				break;
    				
    				
    			default:    		
    				echo "Silly input";    				
    				break;    				
    		}
    		
    		//	delete me from trash
    		$trashRecord->delete();
    	} 
    	
    	
    	
    	//	How to rollback "C - Create" action? Sure, Delete, ha-ha-ha *evil_laugh*!
    	private function _deleteData($trashRecord) {
    		$data = json_decode($trashRecord->getData());    		
    		$dependencies = $this->loadDependencies();
    		$query = "DELETE FROM ".$trashRecord->getTable()." " .
    				"WHERE ".$dependencies[$trashRecord->getTable()]['field']." = ".$data->$dependencies[$trashRecord->getTable()]['field']."";
    		$this->db->query($query);
    		$trashRecord->delete();
    	}  
    	
    	private function checkForSolventPlanCorrection($trashRecord) {
    		if ($trashRecord->getTable() == TB_USAGE) {
		    	$data = json_decode($trashRecord->getData());
		    	$company = new Company($this->db);
		    	$companyID = $company->getCompanyIDbyDepartmentID($data->department_id);
		    	$user = new User($this->db);
		    	if($user->checkAccess('reduction', $companyID)) {
			    	$ms = new ModuleSystem($this->db);
			    	$moduleMap = $ms->getModulesMap();
			    	$mRedaction = new $moduleMap['reduction'];
			    	$params = array(
				    	'db' => $this->db,
						'data' => $data,
						'crud' => $trashRecord->getCRUD()
			    	);
			    	$mRedaction->prepareMixRollback($params);
		    	}
	    	}
    	} 	    	    	
	}
?>