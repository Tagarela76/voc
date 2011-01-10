<?php

	interface iTrash {
		public function setID($id);    	
    	public function setTable($table);
    	public function setData($data);
    	public function setUserID($userID);
    	public function setCRUD($CRUD);
    	public function setDate($date);
    	public function setReferrer($referrer);
    	public function setParentObj($parentObj);
    	
    	public function getID();
    	public function getTable();
    	public function getData();
    	public function getUserID();
    	public function getCRUD();
    	public function getDate();
    	public function getReferrer();
    	public function getParentObj();
    	
    	public function save();
    	public function getDependencies();
    	public function areJuniorTrashesSet();
    	public function validateIntegrity();
    	public function delete();
    	
	}





	class Trash extends TrackingSystem implements iTrash {

		private $id;
		private $table;
		private $data;		//	JSON - table row
		private $userID;
		private $CRUD;		//	Create Read Update Delete - action type
		private $date;		//	timestamp
		private $referrer;	//	action linked to
		
		private $parentObj;
		
		private $db;
		
    	public function Trash($db) {
    		$this->db = $db;
    	}
    	
    	
    	
    	public function setID($id) {
    		$this->id = $id;
    	}    	
    	public function setTable($table) {
    		$this->table = $table;
    	}    	
    	public function setData($data) {
    		$this->data = $data;
    	}    	
    	public function setUserID($userID) {
    		$this->userID = $userID;
    	}    	
    	public function setCRUD($CRUD) {
    		$this->CRUD = $CRUD;
    	}    	
    	public function setDate($date) {
    		$this->date = $date;
    	}    	
    	public function setReferrer($referrer) {
    		$this->referrer = $referrer;
    	}
    	public function setParentObj($parentObj) {
    		$this->parentObj = $parentObj;
    	}
    	
    	
    	
    	public function getID() {
    		return $this->id;
    	}    	
    	public function getTable() {
    		return $this->table;
    	}    	
    	public function getData() {
    		return $this->data;
    	}    	
    	public function getUserID() {
    		return $this->userID;
    	}    	
    	public function getCRUD() {
    		return $this->CRUD;
    	}    	
    	public function getDate() {
    		return $this->date;
    	}    	
    	public function getReferrer() {
    		return $this->referrer;
    	}
    	public function getParentObj() {
    		return $this->parentObj;
    	}
    	
    	
    	
    	
    	public function delete() {
    		if (!is_null($this->getID())) {
    			$query = "DELETE FROM ".self::TRASH_TB_NAME." WHERE id = ".$this->getID()."";
    			$this->db->query($query);
    			$this->setSuccess(true);
    		} else {
    			$this->setSuccess(false);
    		}
    		return $this->isLastOperationSuccessful();
    	}
    	
    	
    	
    	
    	public function save() {    		
    		if ($this->_areProprtiesSet()) {
    			$referrer = is_null($this->referrer) ? "NULL" : $this->referrer;	
    			    	
	    		$query = "INSERT INTO ".self::TRASH_TB_NAME." (table_name, data, user_id, CRUD, date, referrer) VALUES (" .
		    		"'".$this->table."', " .
					"'".$this->data."', " .
					"".$this->userID.", " .
					"'".$this->CRUD."', " .
					"".$this->date.", " .
					"".$referrer.")";	    
	    		$this->db->query($query);
	    		$this->db->query("SELECT LAST_INSERT_ID() id");	    		
	    		$this->setID($this->db->fetch(0)->id);	    		
	    			    				    		    			    		
	    		$this->setSuccess(true);
    		} else {
    			$this->setSuccess(false);    			      			  			
    		}	
    		
    		return $this->isLastOperationSuccessful();
    	}
    	    	    	    	    	    	    	    	    
    		

    	//	direct 		 - search at database. Calling before save trash
    	//	back		 - search at trash bin. Calling before rollback
    	//	directback	 - search at database and then search at trash by result. 
    	//					Calling before 'C' rollback (sample: rollback 'c' department when mixes are already added to department)
    	public function getDependencies($dependencyType = self::ALL_DEPENDENCIES, $direction = "direct") {
    		
    		switch ($direction) {	    		
	    		case "direct":    	    			
		    		$dependencies = $this->loadDependencies();
		    		$trashRecords = array($this);
		    		
		    		//	searching subdependecies	    			    				    	
		    		$allDependenciesFound = false;			    		
		    		while ( !$allDependenciesFound ) {			    		    			
		    						    								    				    						    			
			    		$lastDependencyLayer = true;
			    		$currentIterationRecords = array();			    						    				    			
			    		foreach($trashRecords as $trashRecord) {				    						    		
				    		if ( false !== ($subTrashRecords = $this->_searchDependedRecords($dependencyType, $dependencies, $trashRecord)) ) {
					    		$lastDependencyLayer = false;					    							    							    	
					    		foreach ($subTrashRecords as $subTrashRecord) {					    						    									    			
						    		$output[] = $subTrashRecord;	//	will be returned
						    		$currentIterationRecords[] = $subTrashRecord;	//	will be send to next iteration to search subdependencies							    		
					    		}						    				
				    		}
			    		}					    				    		
			    		if ($lastDependencyLayer) {
				    		$allDependenciesFound = true;
			    		} else {			    			
				    		$trashRecords = $currentIterationRecords;
			    		}				    		
		    		}
		    		
		    		return (count($output) == 0) ? false : $output;		    				
    				break;
    				
    				
    			case "back":    				
    				$query = "SELECT id FROM ".self::TRASH_TB_NAME." WHERE referrer = ".$this->getID()."";
    				$this->db->query($query);
    				
    				if ($this->db->num_rows() > 0) {
	    				$dataRows = $this->db->fetch_all();
	    				foreach ($dataRows as $dataRow) {
	    					$referredTrash = self::getTrashByID($this->db, $dataRow->id);
	    					$referredTrashes[] = $referredTrash;
	    				}
	    				$output = $referredTrashes;
	    				
	    				$allDependenciesFound = false;	    				
			    		while ( !$allDependenciesFound ) {			    		
			    			$lastDependencyLayer = true;
			    			$referredSubtrashes = array();	 
	    					foreach ($referredTrashes as $referredTrash) {
		    					$query = "SELECT id FROM ".self::TRASH_TB_NAME." WHERE referrer = ".$referredTrash->getID()."";		    							    				
		    					$this->db->query($query);
		    					if ($this->db->num_rows() > 0) {
		    						$lastDependencyLayer = false;
			    					$dataRows = $this->db->fetch_all();
	    							foreach ($dataRows as $dataRow) {
	    								$referredSubtrash = self::getTrashByID($this->db, $dataRow->id);
	    								$output[] = $referredSubtrash;
	    								$referredSubtrashes[] = $referredSubtrash;	    								
	    							}
		    					}
	    					}
	    					if ($lastDependencyLayer) {
					    		$allDependenciesFound = true;
				    		} else {
					    		$referredTrashes = $referredSubtrashes;		
				    		}
			    		}			    					    		
			    		return $output;		    			    						    				    				    		
    				} else {
    					return false;
    				}    				
    				break;
    				
    			
    			
    			case "directback":
    				$dependencies = $this->loadDependencies();
		    		if ( false !== ($trashRecords = $this->_searchDependedRecords($dependencyType, $dependencies, $this, false)) ) {			    		
			    		//	searching subdependecies			    			    			    	
			    		$output = $trashRecords;
			    		$allDependenciesFound = false;
			    		while ( !$allDependenciesFound ) {
				    		$lastDependencyLayer = true;
				    		$currentIterationRecords = array();		    			
				    		foreach($trashRecords as $trashRecord) {
					    		if ( false !== ($subTrashRecords = $this->_searchDependedRecords($dependencyType, $dependencies, $trashRecord, false)) ) {
						    		$lastDependencyLayer = false;
						    		foreach ($subTrashRecords as $subTrashRecord) {
							    		$output[] = $subTrashRecord;
							    		$currentIterationRecords[] = $subTrashRecord;
						    		}			
					    		}
				    		}
				    		if ($lastDependencyLayer) {
					    		$allDependenciesFound = true;
				    		} else {
				    			$trashRecords = $currentIterationRecords;
					    		//$trashRecords = $subTrashRecords;	
				    		}				    		
			    		}	    		    		
			    		return $output;	
		    		} else {
			    		return false;
		    		}    		
    				break;	
    				
    				
    			//	does not use?		
    			case "backUnlinked":
	    			$dependencies = $this->loadDependencies();
	    			if ( false !== ($trashRecords = $this->_searchDependedUnlinkedRecords($dependencies, $this)) ) {			    		
			    		//	searching subdependecies	    			    	
			    		$output = $trashRecords;
			    		$allDependenciesFound = false;
			    		while ( !$allDependenciesFound ) {
				    		$lastDependencyLayer = true;						    				    		
				    		foreach($trashRecords as $trashRecord) {
					    		if ( false !== ($subTrashRecords = $this->_searchDependedUnlinkedRecords($dependencies, $trashRecord)) ) {
						    		$lastDependencyLayer = false;
						    		foreach ($subTrashRecords as $subTrashRecord) {
							    		$output[] = $subTrashRecord;
						    		}			
					    		}
				    		}
				    		if ($lastDependencyLayer) {
					    		$allDependenciesFound = true;
				    		} else {
					    		$trashRecords = $subTrashRecords;	
				    		}				    		
			    		}	    		    					    		
			    		return $output;	
		    		} else {
			    		return false;
		    		}    				    			    			
    				
    				break;    			
    		}    		    		
    	}
    	
    	
    	
    	public function validateIntegrity() {
    		//	are all foreign keys alive?
    		$dependencies = $this->loadDependencies();
    		if (!isset($dependencies[$this->getTable()]['tables']['M1'])) {
    			return false;
    		}     		
    		foreach ($dependencies[$this->getTable()]['tables']['M1'] as $tmpTables) {
	    		$visibleAndHidden[] = $tmpTables;
    		}     				    				
    		$many2oneTables = explode(', ', implode(', ', $visibleAndHidden));    		
    		$originalData = json_decode($this->getData());
    		
    		$integrityTrashRecords = array();    		
    		foreach ($many2oneTables as $many2oneTable) {    			
    			$query = "SELECT * " .
    					"FROM ".$many2oneTable." " .
    					"WHERE ".$dependencies[$many2oneTable]['field']." = '".$originalData->$dependencies[$many2oneTable]['foreignField']."'";     					   				
    			$this->db->query($query);
    			if ($this->db->num_rows() == 0) {
    				$query = "SELECT id " .
    					"FROM ".self::TRASH_TB_NAME." " .
    					"WHERE table_name = '".$many2oneTable."' AND " .
    					"data LIKE '%".'"'.$dependencies[$many2oneTable]['field'].'":"'.$originalData->$dependencies[$many2oneTable]['foreignField'].'"'."%'";	//	LIKE '%"mix_id":"666"%'
    				$this->db->query($query);
    				if ($this->db->num_rows() > 0) {
	    				$dataRows = $this->db->fetch_all();		    		
	    				foreach ($dataRows as $dataRow) {
		    				$integrityTrashRecords[] = TrackingSystem::getTrashByID($this->db, $dataRow->id);
	    				}	    					    					
    				}    				
    			}
    		}
    		
    		return (count($integrityTrashRecords) == 0) ? array('result'=>true) : array('result'=>false,'records'=>$integrityTrashRecords);    		
    	}
    	
    	
    	
    	
    	public function areJuniorTrashesSet() {
    		//	are there any junior trash records?
    		$dependencies = $this->loadDependencies();
    		$originalData = json_decode($this->getData());
    		$key = $dependencies[$this->getTable()]['field'];
    		$value = $originalData->$key; 
    		    		
    		$query = "SELECT id " .
    				"FROM ".self::TRASH_TB_NAME." " .
    				"WHERE table_name = '".$this->getTable()."' AND " .
    				"data LIKE '%".'"'.$key.'":"'.$value.'"'."%' AND " .	//	LIKE '%"mix_id":"666"%'
    				"date > ".$this->getDate();
    		$this->db->query($query);
    		
    		if ($this->db->num_rows() == 0) {
    			return array('result'=>false);
    		}
    		$dataRows = $this->db->fetch_all();		    		
    		foreach ($dataRows as $dataRow) {
    			$trashRecords[] = TrackingSystem::getTrashByID($this->db, $dataRow->id);
    		}
    		
    		return array('result'=>true, 'trashRecords'=>$trashRecords);
    	}
    	
    	
    	
    	
    	private function _areProprtiesSet() {    		
    		foreach ($this as $key=>$value) {
    			if (!($key == 'id' || $key == 'db' || $key == 'referrer' || $key == 'parentObj')) {	//	these fields are not mandatory
    				if ($value === null) {    					   					
    					return false;
    				}
    			}
    		}  
    		return true;
    	}    	
    	
    	
    	
    	
    	private function _searchDependedRecords($dependencyType, $dependencies, $trash, $beforeSave = true) {    		
    		    		    		
    		switch ($dependencyType) {
    			case self::ALL_DEPENDENCIES:	//	допустим работает
    				if (!isset($dependencies[$trash->getTable()]['tables']['1M'])) {    		
    					return false;
    				}    				
    				foreach ($dependencies[$trash->getTable()]['tables']['1M'] as $tmpTables) {
    					$visibleAndHidden[] = $tmpTables;
    				}     				    				
    				$dependedTables = explode(', ', implode(', ', $visibleAndHidden));
    				break;
    			
    			case self::HIDDEN_DEPENDENCIES:
    				if (!isset($dependencies[$trash->getTable()]['tables']['1M']['hidden'])) {    		
    					return false;
    				}
    				$dependedTables = explode(', ', $dependencies[$trash->getTable()]['tables']['1M']['hidden']);
    				break;
    			
    			case self::VISIBLE_DEPENDENCIES:
    				if (!isset($dependencies[$trash->getTable()]['tables']['1M']['visible2user'])) {    		
    					return false;
    				}
    				$dependedTables = explode(', ', $dependencies[$trash->getTable()]['tables']['1M']['visible2user']);
    				break;
    		}

    		$originalData = json_decode($trash->getData());    		    		
	    		    		
    		foreach ($dependedTables as $dependedTable) {
    			$query = "SELECT * FROM ".$dependedTable." WHERE ".$dependencies[$trash->getTable()]['foreignField']." = '".$originalData->$dependencies[$trash->getTable()]['field']."'";
    			$this->db->query($query);  
		
    			if ($this->db->num_rows() > 0) {
    				$dataRows = $this->db->fetch_all();
	    			if ($beforeSave) {
	    				//	before inserting new trash record	    							    					    		
		    			$trashRecords = TrackingSystem::XNYO2Trash($this->db, $dataRows);	
		    			foreach($trashRecords as $trashRecord) {
			    			$trashRecord->setTable($dependedTable);		    			
			    			$trashRecord->setParentObj($trash);
			    			
			    			$output[] = $trashRecord;
		    			}		    				 	
    				} else {
    					//	before rollback 'create' return linked trash records
    					foreach($dataRows as $dataRow) {
			    			$query = "SELECT id " .
			    				"FROM ".self::TRASH_TB_NAME." " .
								"WHERE table_name = '".$dependedTable."' AND " .
								"data LIKE '%".'"'.$dependencies[$dependedTable]['field'].'":"'.$dataRow->$dependencies[$dependedTable]['field'].'"'."%' ";	//	LIKE '%"mix_id":"666"%'    							    					
		    				$this->db->query($query);		
		    				if ($this->db->num_rows() > 0) {
			    				$trashRows = $this->db->fetch_all();		
			    				foreach ($trashRows as $trashRow){
				    				$output[] = TrackingSystem::getTrashByID($this->db, $trashRow->id);
			    				}    							    					
		    				}		    				
		    			}		    					
    				}		    		   				    		
    			}
    		}
  
    		return $output;
    	}
    	
    	
    	
    	//	not in use?
    	private function _searchDependedUnlinkedRecords($dependencies, $trash) {
	    	if (!isset($dependencies[$trash->getTable()]['tables']['1M'])) {
    			return false;
    		} 	    		
	    	$originalData = json_decode($trash->getData());	    	
	    	$dependedTables = explode(', ', $dependencies[$trash->getTable()]['tables']['1M']);
	    	
	    	foreach ($dependedTables as $dependedTable) {	    			    		    		
		    	$query = "SELECT id " .
			    	"FROM ".self::TRASH_TB_NAME." " .
					"WHERE table_name = '".$dependedTable."' AND " .
					"data LIKE '%".'"'.$dependencies[$trash->getTable()]['foreignField'].'":"'.$originalData->$dependencies[$trash->getTable()]['foreignField'].'"'."%' ";	//	LIKE '%"mix_id":"666"%'    							    					
		    	$this->db->query($query);
		    	
		    	if ($this->db->num_rows() > 0) {
			    	$dataRows = $this->db->fetch_all();	
			    	foreach($dataRows as $dataRow) {
				    	$trashRecords[] = TrackingSystem::getTrashByID($this->db, $dataRow->id);
			    	}			    					    				    				    		
		    	}
	    	}
	    		    	
	    	return $trashRecords;
    	}
    	
    	
	}	
?>