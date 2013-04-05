<?php

class Component {
	
	private $db;
	
	function Component($db) {
		$this->db=$db;
		//$this->db->select_db(DB_NAME);
	}

	public function getComponentList(Pagination $pagination = null, $filter=' TRUE ', $sort=' ORDER BY description ') {
		$query = "SELECT component_id, cas, description, EINECS FROM ".TB_COMPONENT." WHERE $filter $sort";
		
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		$this->db->query($query);		
		
		if ($this->db->num_rows()) {
			$components=$this->db->fetch_all_array();			
		}
		
		return $components;
	}
	
	/*public function getComponentDetails($compID, $vanilla=false) {
		
		$this->db->query("SELECT * FROM ".TB_COMPONENT." WHERE component_id=".$compID);
		$data=$this->db->fetch(0);
		$compDetails=array(
			'component_id'	=>	$data->component_id,
			'country'	=>	$data->country,
			'state'	=>	$data->state,
			'msds_id'	=>	$data->msds_id,
			'product_code'	=>	$data->product_code,
			'comp_name'	=>	$data->comp_name,
			'comp_type'	=>	$data->comp_type,
			'comp_weight'	=>	$data->comp_weight,
			'comp_density'	=>	$data->comp_density,
			'description'	=>	$data->description,
			'supplier'	=>	$data->supplier,
			'sara'	=>	$data->sara
		);
		
		if (!$vanilla){
			
			$this->db->query("SELECT * FROM ".TB_COUNTRY." WHERE country_id=".$data->country);
			$data2=$this->db->fetch(0);
			$compDetails['country']=$data2->name;
			
			$registration=new Registration($this->db);
			if ($registration->isOwnState($data->country)) {
				$this->db->query("SELECT * FROM ".TB_STATE." WHERE state_id=".$data->state);
				$data2=$this->db->fetch(0);
				$compDetails['state']=$data2->name;
			}
			
			$this->db->query("SELECT * FROM ".TB_MSDS." WHERE msds_id=".$data->msds_id);
			$data2=$this->db->fetch(0);
			$compDetails['msds_id']=$data2->cas_desc;
			
			$this->db->query("SELECT * FROM ".TB_TYPE." WHERE type_id=".$data->comp_type);
			$data2=$this->db->fetch(0);
			$compDetails['comp_type']=$data2->type_desc;
			
			$this->db->query("SELECT * FROM ".TB_SUPPLIER." WHERE supplier_id=".$data->supplier);
			$data2=$this->db->fetch(0);
			$compDetails['supplier']=$data2->supplier;
			
		}
		
		return $compDetails;
	}*/
	
	public function getComponentDetails($compID, $vanilla=false) {
		
		$this->db->query("SELECT * FROM ".TB_COMPONENT." WHERE component_id=".$compID);
		$compDetails=$this->db->fetch_array(0);
		/*$compDetails=array(
			'component_id'	=>	$data->component_id,
			'cas'	=>	$data->cas,
			'EINECS'	=>	$data->EINECS,
			'description'	=>	$data->description
		);*/
		
		$compDetails['agencies']=$this->getComponentAgencies($compDetails['component_id']);
		
		return $compDetails;
	}
	
	public function getComponentIDByCas($cas) {
		$query="SELECT component_id FROM ".TB_COMPONENT." WHERE cas='".$cas."'";
		$this->db->query($query);
		$data=$this->db->fetch(0);
		return $data->component_id;
	}
	
	/**
	 *
	 * @param int $productID
	 * @return mixed object 
	 * public 'component_group_id' => string '4560' (length=4)
	   public 'component_id' => string '12' (length=2)
       public 'product_id' => string '611' (length=3)
       public 'substrate_id' => null
      public 'rule_id' => null
      public 'mm_hg' => string '0.00' (length=4)
      public 'temp' => string '0' (length=1)
      public 'weight_from' => string '80.00' (length=5)
      public 'weight_to' => null
      public 'type' => string 'VOC' (length=3)
      public 'einecs_elincs' => null
      public 'substance_symbol' => null
      public 'cas' => string '28182-81-2' (length=10)
      public 'description' => string 'HOMOPOLYMER OF HDI (1,60HEXAMETHYLENE)' (length=38)
      public 'sara313' => string '' (length=0)
      public 'caab2588' => string '' (length=0)
      public 'EINECS' => null
	 * 
	 * or false
	 */
	public function getComponentDetailsByProduct($productID) {
		$sql = "SELECT * 
				FROM ".TB_COMPONENTGROUP." cg, ".TB_COMPONENT." c
				WHERE cg.component_id = c.component_id
				AND product_id=".mysql_real_escape_string($productID)." ";
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		
		$data = $this->db->fetch_all();
		return $data;		
	}
	
	public function deleteComponent($compID){
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_COMPONENTGROUP." WHERE component_id=".$compID);
		
		$productList = $this->db->fetch_all();
		$product=new Product($this->db);
							
		for ($i=0; $i < count($productList); $i++) {
		    $data=$productList[$i];	    		   		     		   
			$product->deleteProduct2($data->product_id);
		}
		$this->db->query("DELETE FROM ".TB_COMPONENTGROUP." WHERE component_id=".$compID);
		$this->db->query("DELETE FROM ".TB_COMPONENT." WHERE component_id=".$compID);
				
	}
	
	public function clearComponent() {
		//$this->db->select_db(DB_NAME);
    	
    	$query = "DELETE FROM ".TB_COMPONENT;
    	$this->db->query($query);    
	}
	
	public function fillComponent() {
		$this->db->select_db(DB_IMPORT);    	
    	$query = "INSERT INTO ".DB_NAME.".".TB_COMPONENT." SELECT * FROM ".DB_IMPORT.".".TB_COMPONENT;
    	$this->db->query($query);    	    	    
	}
	
	public function addNewComponent($compData){
		/*$query="SELECT * FROM ".TB_MSDS." WHERE msds_id=".$compData['msds_id'];
		$this->db->query($query);
		$data=$this->db->fetch(0);
		
		if ($this->checkSara($data->cas)=="true") {
			$compData["sara"]="yes";
		} else {
			$compData["sara"]="no";
		}*/
//		$query="INSERT INTO ".TB_COMPONENT." (country, state, msds_id, product_code, comp_name, comp_type, comp_weight, comp_density, description, supplier, sara) VALUES (";
		$query="INSERT INTO ".TB_COMPONENT." (cas,EINECS,description) VALUES (";
		
		$query.="'".$compData["cas"]."', ";
		$query.="'".$compData["EINECS"]."', ";
		$query.="'".$compData["description"]."'";
		
		$query.=')';		 
		
		$this->db->query($query);
		
		$compData['component_id']=$this->getComponentIDByCas($compData['cas']);
		
		$this->setAgencyBelong($compData);
		return $compData['component_id'];
	}
	
	
	public function setComponentDetails($compDetails){
		/*$query="SELECT * FROM ".TB_MSDS." WHERE msds_id=".$compDetails['msds_id'];
		$this->db->query($query);
		$data=$this->db->fetch(0);
		
		if ($this->checkSara($data->cas)=="true") {
			$compDetails["sara"]="yes";
		} else {
			$compDetails["sara"]="no";
		}*/
		
		$query="UPDATE ".TB_COMPONENT." SET ";
		
		/*$query.="country='".$compDetails['country']."', ";
		$query.="state='".$compDetails['state']."', ";
		$query.="msds_id='".$compDetails['msds_id']."', ";
		$query.="product_code='".$compDetails['product_code']."', ";
		$query.="comp_name='".$compDetails['comp_name']."', ";
		$query.="comp_type='".$compDetails['comp_type']."', ";
		$query.="comp_weight='".$compDetails['comp_weight']."', ";
		$query.="comp_density='".$compDetails['comp_density']."', ";*/
		$query.="description='".$compDetails['description']."', ";
		$query.="EINECS='".$compDetails['EINECS']."', ";
        $query.="VOC_PM='".$compDetails['vocPm']."', ";
		$query.="cas='".$compDetails['cas']."'";
        
		/*$query.="supplier='".$compDetails['supplier']."', ";
		$query.="sara='".$compDetails['sara']."'";*/
		
		$query.=" WHERE component_id=".$compDetails['component_id'];
		$this->db->query($query);
		$this->setAgencyBelong($compDetails);
	}
	
	function checkSara ($cas) {
		$this->db->query("SELECT * FROM `".TB_LOL."` WHERE cas=".$cas);
		if ($this->db->num_rows()==0) {
			return false;
		} else {
			return true;
		}
	}
	
	function getComponentAgencies($comp_id="") {
		$agency=new Agency($this->db);
		$agencyList=$agency->getAgencyList('id');
		
		if ($comp_id!="") {
			$query="SELECT * FROM ".TB_AGENCY_BELONG." WHERE component_id=".$comp_id;
			$this->db->query($query);
			$count=count($agencyList);
			$agencyBelongCount=$this->db->num_rows();
			for ($i=0; $i < $count; $i++) {
				for ($j=0; $j < $agencyBelongCount; $j++) {
					$data=$this->db->fetch($j);
					if ($data->agency_id == $agencyList[$i]['agency_id']) {
						$agencyList[$i]['control']='yes';
						break;
					}
				}
			}
		}
		$agenciesList=$agencyList;
		return $agenciesList;
	}
	
	function setAgencyBelong($componentData) {
		$id=$componentData['component_id'];
		$query="DELETE FROM ".TB_AGENCY_BELONG." WHERE component_id=".$id;
		$this->db->query($query);
		$agencies=$componentData['agencies'];
		$newAgencyCount=count($agencies);
		for ($i=0; $i < $newAgencyCount; $i++) {
			if ($agencies[$i]['control']=='yes') {
				$query="INSERT INTO ".TB_AGENCY_BELONG." (agency_id, component_id) VALUES (";
				$query.="'".$agencies[$i]['agency_id']."', ";
				$query.="'".$id."'";
				$query.=")";
				$this->db->query($query);
			}
		}
	}
	
	public function isInUseList($component_id) {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_COMPONENTGROUP." WHERE component_id = ".$component_id;
				
		$this->db->query($query);
		
		$productList = $this->db->fetch_all();
		$product = new Product($this->db);
		$output = array (
			"productCnt" => 0,
			"inventoryCnt" => 0,
			"equipmentCnt" => 0,
			"mixCnt" => 0
		);
		for ($i=0; $i < count($productList); $i++){
			$product_id = $productList[$i]->product_id;
			$info = $product->isInUseList($product_id);
			$output["inventoryCnt"] =+ $info["inventoryCnt"];
			$output["equipmentCnt"] =+ $info["equipmentCnt"];
			$output["mixCnt"] =+ $info["mixCnt"];
			$products[] = $product_id;						
		}
		
		$output["productCnt"] = count($products);
		return $output;
	}
	
	function getComponentRules($compID) {
		//$this->db->select_db(DB_NAME);
		$rule = new Rule($this->db);
		$rule_nr_byRegion = $rule->ruleNrMap[$rule->getRegion()];
		$query="SELECT r.rule_id, r.$rule_nr_byRegion FROM ".TB_COMPONENTGROUP." cg, ".TB_RULE." r WHERE cg.component_id=".$compID." AND r.rule_id = cg.rule_id ";
		$this->db->query($query);
		$count=$this->db->num_rows();
		$data=$this->db->fetch_all();
		foreach ($data as $record) {
			if ($record->rule_id) {
			//	$query="SELECT rule_nr FROM ".TB_RULE." WHERE rule_id=".$data[$i]->rule_id;
			//	$this->db->query($query);
			//	$data2=$this->db->fetch(0);
				$rule=array(
					"id"	=>	$record->rule_id,
					"rule_nr"	=>	$record->$rule_nr_byRegion
				);
				$rules[]=$rule;
			}
		}
		return $rules;
	}
	
	
	public function queryTotalCount($filter=' TRUE ') {
		$query = "SELECT COUNT(*) cnt FROM ".TB_COMPONENT." WHERE $filter";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
}
?>