<?php

class Rule {
	
	private $db;
	
	private $region = REGION;

	/**	 
	 * Region 2 field map
	 * @var array
	 */
	public $ruleNrMap = array(
		'us'	=>	'rule_nr_us',
		'eu_uk'	=>	'rule_nr_eu',
		'cn'	=>	'rule_nr_cn',
	);
	
	function Rule($db) {
		$this->db=$db;
		$this->db->select_db(DB_NAME);
	}
	
	public function getRuleNRMap() {
		return $this->ruleNrMap;
	}
	
	/**	 
	 * Rules can have different names at US, UK, CHINA
	 * @param string $region - "us", "eu_uk" or "cn"
	 */
	public function setRegion($region) {
		$region = trim(strtolower($region));
		$possibleValues = array('us','eu_uk','cn');
		if (array_search($region, $possibleValues)) {
			$this->region = $region;
		}
	}
	public function getRegion() {
		return $this->region;
	}
		
	
	
	
	public function clearCounty(){
		//$this->db->select_db(DB_NAME);
		$query="UPDATE ".TB_RULE." SET ";
		
		$query.="county=''";
		$this->db->query($query);
	}
	
	public function getRuleList(Pagination $pagination = null,$filter=" TRUE ", $sort=' ORDER BY rule_desc ') {
		$defultRuleNrPropoperty = $this->ruleNrMap[$this->region];
		$query = "SELECT * FROM ".TB_RULE." WHERE $filter $sort  ";
				
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			
			$rulesArr = $this->db->fetch_all_array();			
			foreach ($rulesArr as $rule) {			
				$rule['description'] = $rule['rule_desc'];
				$rule['rule_nr'] = $rule[$defultRuleNrPropoperty];
				$rules[]=$rule;
			}
																	
		}
		
		return $rules;
	}
	
	public function getRuleDetails($ruleID, $vanilla=false) {
		$defultRuleNrPropoperty = $this->ruleNrMap[$this->region];
		$this->db->query("SELECT * FROM ".TB_RULE." WHERE rule_id=".$ruleID);
		
		$data = $this->db->fetch(0);
		
		//$ruleDetails['rule_nr'] = $ruleDetails[ $defultRuleNrPropoperty];
		//var_dump($ruleDetails);
		//var_dump($defultRuleNrPropoperty);
		
		$ruleDetails = array(
			'rule_id'	=>	$data->rule_id,
			'country'	=>	$data->country,
			'state'		=>	$data->state,
			'county'	=>	$data->county,
			'city'		=>	$data->city,
			'zip'		=>	$data->zip,
			'rule_nr'	=>	$data->$defultRuleNrPropoperty,
			'rule_desc'	=>	$data->rule_desc
		);
		
		
		
		foreach ($this->ruleNrMap as $region=>$field) {
			$ruleDetails[$field] = $data->$field;
		}
		
		if (!$vanilla){			
			$this->db->query("SELECT * FROM ".TB_COUNTRY." WHERE country_id=".$data->country);
			$data2=$this->db->fetch(0);
			$ruleDetails['country']=$data2->name;
			
			$registration=new Registration($this->db);
			if ($registration->isOwnState($data->country)) {
				$this->db->query("SELECT * FROM ".TB_STATE." WHERE state_id=".$data->state);
				$data2=$this->db->fetch(0);
				$ruleDetails['state']=$data2->name;
			}
		}
		
		return $ruleDetails;
	}
	
	
	public function setRuleDetails($ruleDetails){
				
		$query="UPDATE ".TB_RULE." SET ";
		
		$query.="country='".$ruleDetails['country']."', ";
		$query.="state='".$ruleDetails['state']."', ";
		$query.="county='".$ruleDetails['county']."', ";
		$query.="city='".$ruleDetails['city']."', ";
		$query.="zip='".$ruleDetails['zip']."', ";
		$query.="rule_nr_us = '".$ruleDetails['rule_nr_us']."', ";
		$query.="rule_nr_eu = '".$ruleDetails['rule_nr_eu']."', ";
		$query.="rule_nr_cn = '".$ruleDetails['rule_nr_cn']."', ";
		$query.="rule_desc='".$ruleDetails['rule_desc']."'";
		
		$query.=" WHERE rule_id=".$ruleDetails['rule_id'];
		
		$this->db->query($query);
	}
	
	public function addNewRule($ruleData) {		
		$query="INSERT INTO ".TB_RULE." (country, state, county, city, zip, rule_nr_us, rule_nr_eu, rule_nr_cn,rule_desc) VALUES (";
		
		$query.="'".$ruleData["country"]."', ";
		$query.="'".$ruleData["state"]."', ";
		$query.="'".$ruleData["county"]."', ";
		$query.="'".$ruleData["city"]."', ";
		$query.="'".$ruleData["zip"]."', ";
		$query.="'".$ruleData["rule_nr_us"]."', ";
		$query.="'".$ruleData["rule_nr_eu"]."', ";
		$query.="'".$ruleData["rule_nr_cn"]."', ";
		$query.="'".$ruleData["rule_desc"]."'";
		
		$query.=')';
		
		$this->db->query($query);
	}
	
	public function deleteRule($ruleID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_RULE." WHERE rule_id=".$ruleID);
	}
	
	public function getRuleListFromMix($category, $id){
		//$this->db->select_db(DB_NAME);
		$defultRuleNrPropoperty = $this->ruleNrMap[$this->region];
		
		switch ($category) {
			case "company": //company level
			$query = "SELECT r.rule_id, r.$defultRuleNrPropoperty, r.rule_desc " .
					 "FROM facility f, department d, mix m, rule r " .
					 "WHERE d.facility_id = f.facility_id " .
					 "AND m.department_id = d.department_id " .
					 "AND r.rule_id = m.rule_id " .
					 "AND f.company_id = ".$id." " .
					 "GROUP BY r.rule_id";
				break;
				
			case "facility":
			$query = "SELECT r.rule_id, r.$defultRuleNrPropoperty, r.rule_desc " .
					 "FROM department d, mix m, rule r " .					 
					 "WHERE m.department_id = d.department_id " .
					 "AND r.rule_id = m.rule_id " .
					 "AND d.facility_id = ".$id." " .
					 "GROUP BY r.rule_id";					 
				break;		
				
			case "department":						
			$query = "SELECT r.rule_id, r.$defultRuleNrPropoperty, r.rule_desc " .
					 "FROM mix m, rule r " .					 					 
					 "WHERE r.rule_id = m.rule_id " .
					 "AND m.department_id = ".$id." " .
					 "GROUP BY r.rule_id";					 	
				break;				
		}		
		$this->db->query($query);

		$defultRuleNrPropoperty = $this->ruleNrMap[$this->region];
		
		if ($this->db->num_rows()) {
			$rows = $this->db->fetch_all();					
			foreach ($rows as $data) {				
				$rule = array (
					'rule_id'			=>	$data->rule_id,
					'rule_nr'			=>	$data->$defultRuleNrPropoperty,
					'description'		=>	$data->rule_desc					
				);				
				$rules[]=$rule;
			}
		}	
		return $rules;
	}
	
	
	public function getCustomizedRuleList($userID, $companyID = false, $facilityID = false, $departmentID = false) {
		//$this->db->select_db(DB_NAME);
		
		//	try to find by user
		if (false === ($rules = $this->tryFindRuleList('user', $userID))) {
			//	try to find by department
			if (!empty($departmentID)) {
				if (false === ($rules = $this->tryFindRuleList('department' , $departmentID))) {
					//	try to find by facility
					if (!empty($facilityID)) {
						if (false === ($rules = $this->tryFindRuleList('facility' , $facilityID))) {
							//	try to find by company
							if (!empty($companyID)) {
								if (false === ($rules = $this->tryFindRuleList('company' , $companyID))) {
								}
							}	
						}	
					}
				}	
			}	
		}
		
		if (!$rules) {
			// load all list
			$query = "SELECT rule_id FROM ".TB_RULE;
			$this->db->query($query);
			$rules = $this->db->fetch_all_array();
		}
		
		foreach ($rules as $rule) {
			$ruleDetails = $this->getRuleDetails($rule['rule_id']);
			$customizedRuleList[] = $ruleDetails;
		}
			
		return $customizedRuleList;	
	}
	
	
	public function setCustomizedRuleList($ruleList, $category, $categoryID) {
		
		$category = $this->db->sqltext($category);
		$categoryID = $this->db->sqltext($categoryID);
		
		$this->removeCustomizedRuleList($category, $categoryID);
		
		foreach ($ruleList as $rule) {
			$rule = $this->db->sqltext($rule);
			$this->insertCustomizedRuleList($rule, $category, $categoryID);
		}
	}
	
	
	
	public function queryTotalCount($filter=" TRUE ") {
		$query = "SELECT COUNT(*) cnt FROM ".TB_RULE." WHERE $filter";
		$this->db->query($query);
		return $this->db->fetch(0)->cnt;
	}
	
	/*
	 * Если надо найти количество строчек, лучше использовать запрос SELECT count(rule_id) FROM ....
	 * */
	private function tryFindRuleList($category, $categoryID) {
		$query = "SELECT rule_id FROM ".TB_SELECTED_RULES_LIST." WHERE category = '".$category."' AND category_id = ".$categoryID;
		$this->db->query($query);
		
		return ($this->db->num_rows()) ? $this->db->fetch_all_array() : false; 
	}
	
	
	private function removeCustomizedRuleList( $category, $categoryID) {
		$query = "DELETE FROM ".TB_SELECTED_RULES_LIST." WHERE category = '".$category."' AND category_id = ".$categoryID;
		$this->db->query($query);
	}
	
	private function insertCustomizedRuleList($ruleID, $category, $categoryID) {			
		$query = "INSERT INTO ".TB_SELECTED_RULES_LIST." (rule_id, category, category_id) VALUES (".$ruleID.", '".$category."', ".$categoryID.")";
		$this->db->query($query);
	}
	
}
?>