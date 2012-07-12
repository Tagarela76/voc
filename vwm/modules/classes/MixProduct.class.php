<?php

	class MixProduct extends Product {

		public $mixgroup_id;
		public $mix_id;
		public $product_id;
		public $quantity;
		public $unit_type;
		public $quantity_lbs;
		public $is_primary = 0;
		public $ratio_to_save;

		public $name;
		public $voclx;
		public $vocwx;
		public $density;
		public $supplier;
		public $product_nr;

		public $unittypeDetails;
		public $density_unit_id;
		public $coating_id;
		public $specialty_coating;
		public $aerosol;
		public $specific_gravity;
		public $boiling_range_from;
		public $boiling_range_to;
		public $supplier_id;
		public $coatDesc;
		public $unitTypeList;



		public function __construct(db $db, $mixgroup_id = null) {
			$this->db = $db;
			if (isset($mixgroup_id)) {
				$this->$mixgroup_id = $mixgroup_id;
				$this->_load();
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



		/** overrides super class method **/
		public function initializeByID($productID) {

			$query = "SELECT sup.*,  p.*, coat.coat_desc as coatDesc " .
							" FROM ".TB_PRODUCT." p, ". TB_SUPPLIER ." sup, " . TB_COAT .
							" WHERE p.supplier_id = sup.supplier_id " .
							" AND coat.coat_id = coating_id " .
							" AND p.product_id = {$this->db->sqltext($productID)}";

			$this->db->query($query);

			if ($this->db->num_rows() == 0) return false;

			$productData = $this->db->fetch(0);
			$this->perccentVolatileVolume = $productData->percent_volatile_volume;
			$this->perccentVolatileWeight = $productData->percent_volatile_weight;

			foreach ($productData as $property =>$value) {
				if (property_exists($this,$property)) {
					$this->$property = $productData->$property;
				}
			}

			return true;
		}

		public function initUnittypeList($unittype) {

			//echo "init unitTypeList by unittype_id: {$this->unittypeDetails['unittype_id']}";
			$unittypeClass = $unittype->getUnittypeClass($this->unittypeDetails['unittype_id']);
			//echo " and class: <b>$unittypeClass</b>";
			$this->unitTypeList = $unittype->getUnittypeListDefault($unittypeClass);
			//var_dump($this->unitTypeList);
			$this->unittypeDetails['unittypeClass'] = $unittypeClass;
		}

		/*public function getVoclx() {
			return $this->voclx;
		}

		public function getVocwx() {
			return $this->vocwx;
		}*/
	}