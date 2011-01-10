<?php
	
	class Logbook {
	
		//	hello xnyo
		protected $db;								
		protected $facility_id;
		
		
		const LOGBOOK_PREFIX = "Logbook";
							
    	public function Logbook($db, $facilityID = null) {    	
	    	$this->db = $db;	    	
	    	if (!is_null($facilityID)) {
	    		$this->facility_id = mysql_real_escape_string($facilityID);	    			    			    		
	    	}
    	}
    	
    	
    	public function getActionList(Pagination $pagination = null,$filter= ' TRUE ',$sort='ORDER BY date DESC') {    		
    		//	no facility is bad..
			if (!isset($this->facility_id)) return false;
			//$to = ROW_COUNT;
			$query = "SELECT * FROM logbook WHERE facility_id = ".$this->facility_id." AND $filter $sort";
			if (isset($pagination)) {
				$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
			}
			
			$this->db->query($query);
			
			//	empty list
			if ($this->db->num_rows() == 0) return false;
			
			$logbookList = array();
			$logbookData = $this->db->fetch_all();
			foreach ($logbookData as $logbookRecord) {
				$potentialClassName = self::LOGBOOK_PREFIX.$logbookRecord->type; 
				if(class_exists($potentialClassName)) {
					$logbookAction = new $potentialClassName($this->db);
					foreach ($logbookRecord as $property =>$value) {
						if (property_exists($logbookAction,$property)) {
							$logbookAction->$property = $logbookRecord->$property;
						}
					}
					if ($potentialClassName == 'LogbookFilter') {
		    			$logbookAction->installed = ($logbookAction->action == 'installed')?true:false;
						$logbookAction->removed = ($logbookAction->action == 'removed')?true:false;
		    		}
					$logbookList []= $logbookAction; 
				}
			}
			return $logbookList;
    	} 
    	
    	public function getItemById($id) {
    		//here we don't interested in facility...
    		$query = "SELECT * FROM logbook WHERE id = '$id' LIMIT 1";
    		$this->db->query($query);
    		if ($this->db->num_rows() == 0) return false;
    		
    		$logbookRecord = $this->db->fetch(0);
    		$potentialClassName = self::LOGBOOK_PREFIX.$logbookRecord->type;
    		if(class_exists($potentialClassName)) {
	    		$logbookAction = new $potentialClassName($this->db);
	    		foreach ($logbookRecord as $property =>$value) {
		    		if (property_exists($logbookAction,$property)) {
			    		$logbookAction->$property = $logbookRecord->$property;
		    		}
	    		}
	    		if ($potentialClassName == 'LogbookFilter') {
	    			$logbookAction->installed = ($logbookAction->action == 'installed')?true:false;
					$logbookAction->removed = ($logbookAction->action == 'removed')?true:false;
	    		}
	    		return $logbookAction;
    		} else return false;
    	}
    	
    	public function logbookAutocomplete($occurrence) {
    		//	no facility is bad..
			if (!isset($this->facility_id)) return false;
			$fields = array('type', 'operator', 'date', 'department_id', 'equipment_id', 'description');
			$query = "SELECT ";
			foreach ($fields as $field) {
				$query .= " `$field`,"." LOCATE('$occurrence',`$field`) AS occurrence$field,";
			}
			$query = substr($query,0,-1);
			$query .= " FROM logbook WHERE facility_id = '$this->facility_id' AND (";
			foreach ($fields as $field) {
				$query .= "  LOCATE('$occurrence',`$field`)>0 OR";
			}
			$query = substr($query,0,-2);
			$query .= ") LIMIT ".AUTOCOMPLETE_LIMIT;

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
    	
    	public function logbookSeach($occurrences, Pagination $pagination = null) {
    		//	no facility is bad..
			if (!isset($this->facility_id)) return false;
			$fields = array('type', 'operator', 'date', 'department_id', 'equipment_id', 'description');
			$logbookList = array();
			$idArray = array();
			//$to = ROW_COUNT;
			$query = "SELECT * ";

			$query .= " FROM logbook WHERE facility_id = '$this->facility_id' AND (";
			foreach ($occurrences as $occurrence) {
				foreach ($fields as $field) {
					$query .= " IFNULL(LOCATE('$occurrence',`$field`),0) +";
				}
			}
			$query = substr($query,0,-1);
			$query .= " != 0) ";
			$query .= " ORDER BY date DESC";
    		if (isset($pagination)) {
				$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
			}
			
			//			$query = "SELECT `$field`, LOCATE('$occurrence',`$field`) AS occurrence FROM logbook WHERE  LOCATE('$occurrence',`$field`) != 0  ORDER BY occurrence  DESC";
			$this->db->query($query);
			//var_dump($query);
			$data = $this->db->fetch_all();
			
			foreach ($data as $logbookRecord) {	
				if(!in_array($logbookRecord->id,$idArray)) {
					$potentialClassName = self::LOGBOOK_PREFIX.$logbookRecord->type; 
					if(class_exists($potentialClassName)) {
						$logbookAction = new $potentialClassName($this->db);
						foreach ($logbookRecord as $property =>$value) {
							if (property_exists($logbookAction,$property)) {
								$logbookAction->$property = $logbookRecord->$property;
							}
						}
						if ($potentialClassName == 'LogbookFilter') {
							$logbookAction->installed = ($logbookAction->action == 'installed')?true:false;
							$logbookAction->removed = ($logbookAction->action == 'removed')?true:false;
						}
						$logbookList []= $logbookAction; 
						$idArray []= $logbookRecord->id;
					}
				}
			}
			
			return $logbookList;
    	}
    	
    	public function countLogbookRecords($occurrences = false,$filter) {
    		if (!isset($this->facility_id)) return false;
			$query = "SELECT count(id) recordCount FROM logbook WHERE facility_id = '".$this->facility_id."' AND $filter";
			if ($occurrences !== false) {
				$fields = array('type', 'operator', 'date', 'department_id', 'equipment_id', 'description');
				$query .= "AND (";
				foreach ($occurrences as $occurrence) {
					foreach ($fields as $field) {
						$query .= " IFNULL(LOCATE('$occurrence',`$field`),0) +";
					}
				}
				$query = substr($query,0,-1);
				$query .= " != 0) ";
			}
			$this->db->query($query);
			return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->recordCount : false;	
    	}
	}