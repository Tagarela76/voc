<?php

	class MixProduct extends Product {
		
		public $mixgroup_id;
		public $mix_id;
		public $product_id;
		public $quantity;
		public $unit_type;
		public $quantity_lbs;
		
		
		public function __construct($db, $mixgroup_id = null) {
			$this->db = $db;
			if (isset($mixgroup_id)) {
				$this->$mixgroup_id = $mixgroup_id;					
			}			
		}
		
		
		private function _load() {
			if (!isset($this->mixgroup_id)) return false;
			
			$mixGroupID = mysql_escape_string($this->mixgroup_id);
			
			$query = 'SELECT * FROM '.TB_MIXGROUP.' WHERE mixgroup_id = '.$mixGroupID.'';
			$this->db->query($query);
			
			if ($this->db->num_rows() == 0) return false;
			
			$mixGroupData = $this->db->fetch(0);
			foreach ($mixGroupData as $property =>$value) {
				if (property_exists($this,$property)) {
					$this->$property = $mixGroupData->$property;
				}
			}
		}
	}