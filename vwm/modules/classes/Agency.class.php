<?php

class Agency {
	
	private $db;
	
	private $region = REGION;

	/**	 
	 * Region 2 field map
	 * @var array
	 */
	public $nameMap = array(
		'us'	=>	'name_us',
		'eu_uk'	=>	'name_eu',
		'cn'	=>	'name_cn',
	);
		
	function Agency($db) {
		$this->db=$db;
	}
	
	public function getNameMap() {
		return $this->nameMap;
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
			
	public function getAgencyList($sort='', Pagination $pagination = null, $filter=' TRUE ', $sortStr=' ORDER BY a.name_us ') {
		$field = $this->nameMap[$this->getRegion()];
		$query = "SELECT a.agency_id AS agency_id ,a.name_us AS name_us, a.name_eu AS name_eu, a.name_cn AS name_cn, a.$field AS name, a.description AS description, a.location AS location,a.contact_info AS contact_info,c.name AS country FROM ".TB_AGENCY." a, ".TB_COUNTRY."".
					" c WHERE (a.country_id=c.country_id OR a.country_id=NULL) AND $filter  $sortStr ";
		
		if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			return $this->db->fetch_all_array();
		}		
		return null;
	}
	
	public function getAgencyDetails($agencyID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT *, ".$this->nameMap[$this->getRegion()]." AS name FROM ".TB_AGENCY." WHERE agency_id= $agencyID LIMIT 1");	
		return $this->db->fetch_array(0) ;		
	}
	
	public function setAgencyDetails($agencyDetails){
		
		//$this->db->select_db(DB_NAME);
		
		$query="UPDATE ".TB_AGENCY." SET ";		
		foreach($this->nameMap as $field) {
		$query.="$field='".$agencyDetails[$field]."', ";
		}
		$query.="description='".$agencyDetails['description']."', ";
		$query.="country_id='".$agencyDetails['country_id']."', ";
		$query.="location='".$agencyDetails['location']."', ";
		$query.="country_id='".$agencyDetails['country_id']."', ";
		$query.="contact_info='".$agencyDetails['contact_info']."' ";		
		$query.=" WHERE agency_id=".$agencyDetails['agency_id'];
		
		$this->db->query($query);
	}
	
	public function addNewAgency($agencyData) {
		//$this->db->select_db(DB_NAME);
		
		$query="INSERT INTO ".TB_AGENCY." (name_us,name_eu,name_cn,description,country_id,location,contact_info) VALUES (";		
		$query.="'".$agencyData["name_us"]."', ";
		$query.="'".$agencyData["name_eu"]."', ";
		$query.="'".$agencyData["name_cn"]."', ";		
		$query.="'".$agencyData["description"]."', ";
		$query.="'".$agencyData["country_id"]."', ";
		$query.="'".$agencyData["location"]."', ";
		$query.="'".$agencyData["contact_info"]."'";
		$query.=')';
		
		$this->db->query($query);
	}
	
	public function deleteAgency($agencyID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_AGENCY." WHERE agency_id=".$agencyID);
	}
	
	public function getAgencyCount($filter=' TRUE ') {	
		$query = "SELECT COUNT(*) cnt FROM ".TB_AGENCY." WHERE $filter";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt']; 		
	}

}
?>