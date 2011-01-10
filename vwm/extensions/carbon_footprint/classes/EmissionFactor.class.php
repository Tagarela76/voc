<?php
	class EmissionFactor {
	
		//	hello xnyo
		protected $db;
	
		public $id;	
		public $name;
		public $unittype_id;
		public $emission_factor;
				
		const ELECTRICITY_FACTOR_ID = 25;

		
		/**
		 * 
		 * ini Emission Factor
		 * @param xnyo $db
		 * @param int $id of Emission Factor
		 */
    	public function EmissionFactor($db, $id = null) {    	
	    	$this->db = $db;
	    	if (!is_null($id)) {
	    		$this->id = mysql_real_escape_string($id);
	    		return $this->_load();	
	    	}
    	}
    	    			
		
		
		/**
		 * save emission factor
		 * All properties should be set before calling (except id)
		 * @return true or false if fail
		 */
		public function save() {
			
			if (!isset($this->name)) return false;
			if (!isset($this->unittype_id)) return false;
			if (!isset($this->emission_factor)) return false;
			
			$this->name = mysql_real_escape_string($this->name);
			$this->unittype_id = mysql_real_escape_string($this->unittype_id);
			$this->emission_factor = mysql_real_escape_string($this->emission_factor);
			
			if (!isset($this->id)) {
				//	insert
				$query = "INSERT INTO emission_factor (name, unittype_id, emission_factor) VALUES " .
							"('".$this->name."', ".$this->unittype_id.", ".$this->emission_factor.")";
			} else {
				
				$this->id = mysql_real_escape_string($this->id);
				
				//	update
				$query = "UPDATE emission_factor SET " .
							"name = '".$this->name."', " .
							"unittype_id = ".$this->unittype_id.", " .
							"emission_factor = ".$this->emission_factor." " .
							"WHERE id = ".$this->id."";
			}
			
			$this->db->exec($query);
			return true;
		}
		
		
		private function _load() {
			
			if (!isset($this->id)) return false;
			
			$query = 'SELECT * FROM emission_factor WHERE id = '.$this->id;
			$this->db->query($query);
			
			if ($this->db->num_rows() > 0) {
				$emissionFactorRecord = $this->db->fetch(0);
				foreach($emissionFactorRecord as $property=>$value) {
					if (property_exists($this, $property)) {
			 			$this->$property = $value;
			 		}
				}
				
				return true;				
			} else {
				//	no emission factor with such ID				
				return false;				
			}
		}
    	
	}
    	