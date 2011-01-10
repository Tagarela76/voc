<?php

class Unittype {
	
	private $db;
	
	function Unittype($db) {
		//print_r($db);
		$this->db=$db;
	}
	
	public function getUnittypeList() {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_UNITTYPE.",".TB_TYPE." WHERE type.type_id = unittype.type_id ORDER BY name");
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$unittype=array (
					'unittype_id'			=>	$data->unittype_id,
					'description'			=>	$data->name,
					'type_id'				=>  $data->type_id,
					'type'					=>  $data->type_desc
					
				);
				$unittypes[]=$unittype;
			}
		}
		
		return $unittypes;
	}
	
	
	public function getUnittypeDetails($unittypeID, $vanilla=false) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_UNITTYPE.",".TB_TYPE." WHERE type.type_id = unittype.type_id AND unittype_id=".$unittypeID);
		$data=$this->db->fetch(0);
		$unittypeDetails=array(
			'unittype_id'	=>	$data->unittype_id,
			'name'	=>	$data->name,
			'description'	=>	$data->unittype_desc,
			'formula'	=>	$data->formula,
			'type_id' => $data->type_id,
			'type' => $data->type_desc
		);
		//	formulas are not implemented
//		if (!$vanilla) {
//			$this->db->query("SELECT * FROM ".TB_FORMULA." WHERE formula_id=".$data->formula);
//			$data=$this->db->fetch(0);
//			$unittypeDetails['formula']=$data->formula;
//		}
		
		return $unittypeDetails;
	}
	
	
	public function setUnittypeDetails($unittypeDetails){
		
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_UNITTYPE." SET ";
		
		$query.="name='".$unittypeDetails['name']."', ";
		$query.="unittype_desc='".$unittypeDetails['description']."', ";
		$query.="formula='".$unittypeDetails['formula']."', ";
		$query.="type_id='".$unittypeDetails['type']."'";
		
		$query.=" WHERE unittype_id=".$unittypeDetails['unittype_id'];
		
		$this->db->query($query);
	}
	
	public function addNewUnittype($unittypeData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_UNITTYPE." (name, unittype_desc, formula, type_id) VALUES (";
		
		$query.="'".$unittypeData["name"]."', ";
		$query.="'".$unittypeData["description"]."', ";
		$query.="'".$unittypeData["formula"]."', ";
		$query.="'".$unittypeData["type"]."'";
		$query.=')';
		
		$this->db->query($query);
	}
	
	
	public function deleteUnittype($unittypeID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_UNITTYPE." WHERE unittype_id=".$unittypeID);
	}
	
	public function getUnittypeListDefault($sysType = "USALiquid") {
		//$this->db->select_db(DB_NAME);
		switch ($sysType){
			case 'USALiquid':
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc in ('Volume Liquid','Volume') " .
					"ORDER BY ut.unittype_id";	
				break;
			case 'USADry':
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc in ('Volume Dry','Volume') " .
					"ORDER BY ut.unittype_id";	
				break;
			case 'USAWght':
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'USA' " .
					"AND t.type_desc = 'Weight' " .
					"ORDER BY ut.unittype_id";	
				break;
			case 'MetricVlm':
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'metric' " .
					"AND t.type_desc = 'Volume' " .
					"ORDER BY ut.unittype_id";	
				break;
			case 'MetricWght':
				$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .
					"AND ut.system = 'metric' " .
					"AND t.type_desc = 'Weight' " .
					"ORDER BY ut.unittype_id";	
				break;
		}
				
		$this->db->query($query);							
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$unittype=array (
					'unittype_id'			=>	$data->unittype_id,
					'description'			=>	$data->name,
					'type_id'				=>  $data->type_id,
					'type'					=>  $data->type_desc,
					'unittype_desc'			=>  $data->unittype_desc,
					'system'				=>  $data->system
				);
				$unittypes[]=$unittype;
			}
		}
		
		return $unittypes;
	}
	
	public function getUnittypeClass($unittypeID) {
		
		//$this->db->select_db(DB_NAME);
		$query="SELECT * FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
				"WHERE ut.type_id = t.type_id " .
				"AND ut.unittype_id = ".$unittypeID;
		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {			
			$data=$this->db->fetch(0);
			$unittype=array (
				'unittype_id'			=>	$data->unittype_id,
				'description'			=>	$data->name,
				'type_id'				=>  $data->type_id,
				'type'					=>  $data->type_desc,
				'system'				=>	$data->system
			);						
		}
		
		if ( $unittype['system'] == 'USA' && ($unittype['type'] == 'Volume Liquid' || $unittype['type'] == 'Volume') ) {
			return 'USALiquid';
		} elseif ( $unittype['system'] == 'USA' && ($unittype['type'] == 'Volume Dry' || $unittype['type'] == 'Volume') ) {
			return 'USADry';
		} elseif ( $unittype['system'] == 'USA' && $unittype['type'] == 'Weight' ) {
			return 'USAWght';			
		} elseif ( $unittype['system'] == 'metric' && $unittype['type'] == 'Volume' ) {
			return 'MetricVlm';
		} elseif ( $unittype['system'] == 'metric' && $unittype['type'] == 'Weight' ) {
			return 'MetricWght';
		}
						
	}
	
	public function getClassesOfUnits() {
		/*	
		 * TODO: sql issues
		 * 		1) тяжело читать. надо:
		 * 			SELECT *
		 * 			FROM
		 * 			WHERE
		 * 			GROUP BY
		 * 			ORDER BY
		 *		2) system <> 'NULL' - неправильно, тк в кавычки - это строка, а NULL - это не даже не пустая строка. надо:
		 *			system IS NOT NULL
		 *
		 * 		3) а почему цикл по $j? по всем законом первым циклом должен идти $i.
		 * 			Используй foreach - он быстрее
		 *  
		 * */
		
		$this->db->query("SELECT * " .
				"FROM ".TB_UNITTYPE.",".TB_TYPE." " .
				"WHERE type.type_id = unittype.type_id AND unittype.system IS NOT NULL AND type.type_id <> '3' " .
				"ORDER BY type.type_id ASC");		
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);			
			
				$unittype[$i]=array (
					'unittype_id'			=>	$data->unittype_id,
					'name'					=>	$data->name,
					'unittype_desc'			=>  $data->unittype_desc,
					'type_id'				=>  $data->type_id,
					'type_desc'				=>	$data->type_desc,
					'system'				=>  $data->system,
					'unit_class_id'			=>  $data->unit_class_id
				);						
			}
		}
				
		return $unittype;
		
	}
	
	public function getDefaultUnitTypelist($companyID) {
		
		$query ="SELECT * FROM ".TB_DEFAULT." d WHERE d.id_of_object=".(int)$companyID;
		$query.=" AND subject='unittype'";
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($j=0; $j < $this->db->num_rows(); $j++) {
				$data=$this->db->fetch($j);			
				$unittype[$j]=$data->id_of_subject;
//				$unittype[$j]=array (
//					'subject'				=>	$data->subject,
//					'id_of_subject'			=>	$data->id_of_subject,
//					'object'				=>  $data->object,
//					'id_of_object'			=>  $data->id_of_object					
//				);						
			}
		}
		
		return $unittype;
	}
	
	
	
	public function setDefaultUnitTypelist($unitTypeID, $categoryName, $companyID) {
		
		$this->deleteDefaultUnitType($companyID);
				
		$query = "SELECT ".TB_UNITTYPE.".unittype_id FROM ".TB_UNITTYPE." WHERE ".TB_UNITTYPE.".system <> 'NULL' AND ".TB_UNITTYPE.".unittype_id IN ".
					"(SELECT DISTINCT unit_type FROM ".TB_MIXGROUP." WHERE ".TB_MIXGROUP.".mix_id IN ". 
   						"(SELECT mix_id FROM ".TB_USAGE." WHERE ".TB_USAGE.".department_id IN ".
    						"(SELECT department_id FROM ".TB_DEPARTMENT." WHERE ".TB_DEPARTMENT.".facility_id IN ".
     							"(SELECT facility_id FROM ".TB_FACILITY." WHERE ".TB_FACILITY.".company_id='".$companyID."'))))";
     	
     	
     	$this->db->query($query);
     	
     	// select unit types for which has already created products
     	if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$unittype[$i]=$data->unittype_id;					
			}
		}
		
		// insert unit types that exist but are not marked
		$i = 0;
		$j = 0;
		$flag = 0;
		while ($unittype[$j]) {
			while ($unitTypeID[$i]) {
				if ($unittype[$j] == $unitTypeID[$i]) {
					$flag = 1;
				}
				$i++;
			}
			if ($flag == 0) {
				$this->insertDefaultUnitType('unittype', $unittype[$j], $categoryName, $companyID);
			}
			$i = 0;
			$j++;
			$flag = 0;
		}
		
		// insert marked unit types
		$i = 0;
		while ($unitTypeID[$i]) {
			$this->insertDefaultUnitType('unittype', $unitTypeID[$i], $categoryName, $companyID);
			$i++;
		}
	}
	
	private function deleteDefaultUnitType($companyID) {
		
		$query = "DELETE FROM ".TB_DEFAULT." WHERE `id_of_object` = '".$companyID."'" ;
		$query.= " AND subject='unittype'"; 
		$this->db->query($query);
	}
	
	private function insertDefaultUnitType($unittypeName, $unittypeID, $companyName, $companyID) {
		//$this->db->select_db(DB_NAME);
		$query = "INSERT INTO ".TB_DEFAULT." (subject, id_of_subject, object, id_of_object) " .
				 "VALUES ('".$unittypeName."', ".(int)$unittypeID.", '".$companyName."', ".(int)$companyID.")";
		$this->db->query($query);		
	} 
	
	public function getUnitTypeExist($companyID) {
		
		$query = "SELECT * " .
				"FROM ".TB_UNITTYPE." " .
				"WHERE ".TB_UNITTYPE.".unittype_id IN " .
						"(SELECT d.id_of_subject " .
						"FROM ".TB_DEFAULT." d " .
						"WHERE d.id_of_object='".$companyID."') " .
				"AND ".TB_UNITTYPE.".system IS NOT NULL AND  ".TB_UNITTYPE.".type_id <> '3'";
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$unittype=array (
					'unittype_id'			=>	$data->unittype_id,
					'type_id'				=>  $data->type_id,
					'name'					=>	$data->name
				);
				$unittypes[]=$unittype;
			}
		}
		
		return $unittypes;
	}
	
	public function getAllClassesOfUnitTypes(){
		$query = "SELECT * FROM `unit_class`";
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$unitClasslist=array (
					'id'					=>	$data->id,
					'name'					=>  $data->name,
					'description'			=>	$data->description
				);
				$unitClasses[]=$unitClasslist;
			}
		}
		
		return $unitClasses;
	}
	
		public function getUnittypeListDefaultByCompanyId($companyID,$typeClass) {
		//$this->db->select_db(DB_NAME);

				$query="SELECT ut.unittype_id, ut.name, ut.type_id, t.type_desc, ut.unittype_desc, ut.system FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t, ".TB_DEFAULT." def, ".TB_UNITCLASS." uc ".
					"WHERE def.subject = 'unittype' ".
					"AND ut.unittype_id = def.id_of_subject ".
					"AND ut.type_id = t.type_id ".
					"AND def.object = 'company' ".
					"AND def.id_of_object = '".$companyID."' ".
					"AND ut.unit_class_id = uc.id ".
					"AND uc.name = '".$typeClass."' ".
					"ORDER BY ut.unittype_id";	
				
				
		$this->db->query($query);							
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$unittype=array (
					'unittype_id'			=>	$data->unittype_id,
					'description'			=>	$data->name,
					'type_id'				=>  $data->type_id,
					'type'					=>  $data->type_desc,
					'unittype_desc'			=>  $data->unittype_desc,
					'system'				=>  $data->system
				);
				$unittypes[]=$unittype;
			}
		} else {
			$unittypes=$this->getUnittypeListDefault($typeClass);
		}

		return $unittypes;
	}
	
	public function getNameByID($id) {
		$query = "SELECT name FROM ".TB_UNITTYPE." WHERE unittype_id = ".$id."";
		$this->db->query($query);
		
		return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->name : false;
	}
	
	public function getDescriptionByID($id) {
		$query = "SELECT unittype_desc FROM ".TB_UNITTYPE." WHERE unittype_id = ".$id."";
		$this->db->query($query);
		
		return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->unittype_desc : false;
	}
	
	public function isWeightOrVolume($unittypeID) {
		$query="SELECT t.type_desc FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t ".
					"WHERE ut.unittype_id = '".$unittypeID."' ".
					"AND ut.type_id = t.type_id ";	
		$this->db->query($query);
		if ($this->db->num_rows()) {
			$data=$this->db->fetch(0);
			switch ($data->type_desc) {
				case 'Weight':
					return 'weight';
					break;
				case 'Volume':
				case 'Volume Liquid':
				case 'Volume Dry':
					return 'volume';
					break;
				case 'Distance':
					return false;
					break;
				case 'Energy':
					return 'energy';
					break;
			}
		}
	}
	
	public function getUnittypListFromClassOfUnittypeID($unittypeID)
	{
		
		switch ($this->isWeightOrVolume($unittypeID))
		{
			case 'weight':
			{
				$query="SELECT ut.unittype_id, ut.unittype_desc FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .					
					"AND t.type_desc = 'Weight' " .
					"ORDER BY ut.unittype_id";					
					break;
					
			}
			case 'volume':
			{
				$query="SELECT ut.unittype_id, ut.unittype_desc FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .					
					"AND t.type_desc in ('Volume','Volume Liquid','Volume Dry'" .
					"ORDER BY ut.unittype_id";
				break;
			}
			case 'energy':
			{
				$query="SELECT ut.unittype_id, ut.unittype_desc FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t " .
					"WHERE ut.type_id = t.type_id " .					
					"AND t.type_desc = 'Energy'" .
					"ORDER BY ut.unittype_id";
				break;
			}
			default: 
				return false;
		}
		$this->db->query($query);
		if ($this->db->num_rows()) {
			$unittypes=$this->db->fetch_all_array();
			return $unittypes;	
		}
		else return false;	
	}
	
	
	public function getUnittypeListByCategory($category, $companyID = null) {
		$types = $this->getAllTypesByCategory($category);
		$query = "SELECT ut.unittype_id, ut.name, ut.unittype_desc FROM ".TB_UNITTYPE." ut, ".TB_TYPE." t ";
		if ($companyID != null) {
			$query .= ", ".TB_DEFAULT." def ";
		}
		$query .= " WHERE (";
		foreach ($types as $typeName) {
			$query .= "t.type_desc = '$typeName' OR ";
		}
		$query = substr($query,0,-3);
		$query .= ") AND ut.type_id = t.type_id ";
		if ($category != 'energy') {
			$query .= " AND ut.system IS NOT NULL ";
		}
		if ($companyID != null) {
			$query .= " AND def.id_of_subject = ut.unittype_id AND def.id_of_object = '$companyID' AND def.subject = 'unittype' AND def.object = 'company'";
		}
		$this->db->query($query);
		if ($this->db->num_rows()>0) {
		$unittypeList = $this->db->fetch_all_array();
		//$unittypeList = array();
		/*foreach ($data as $unittype) {
			$unittypeList []= array(
				'id' => $unittype->unittype_id,
				'name' => $unittype->name,
				'description' => $unittype->unittype_desc
			); 
		}*/
		} elseif($companyID != null) {
			$unittypeList = $this->getUnittypeListByCategory($category);
		}
		return $unittypeList;
	}
	
	public function getAllTypesByCategory($category) {
		switch($category) {
			case 'weight':
				return array('Weight');
				break;
			case 'volume':
				return array('Volume', 'Volume Liquid', 'Volume Dry');
				break;
			case 'energy':
				return array('Energy');
				break;
		}
		return false;
	} 
}
?>