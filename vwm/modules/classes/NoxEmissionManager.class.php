<?php
class NoxEmissionManager{
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getCountNoxByDepartment($departmentID) {

		$query = "SELECT COUNT(*) cnt FROM nox WHERE department_id = {$departmentID} ";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}
	
	public function getCountBurnerByDepartment($departmentID) {

		$query = "SELECT COUNT(*) cnt FROM burner WHERE department_id = {$departmentID} ";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}	
	
	public function getNoxListByDepartment($departmentID,$sortStr = null, $pagination = null) {
		$query = "SELECT * FROM nox WHERE department_id = {$departmentID} ";
		if (isset($sortStr)){
			$query .= $sortStr;
		}
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
    	$this->db->query($query);

    	if ($this->db->num_rows()) 
    	{   $data = $this->db->fetch_all_array();		
    		return $data;
    	}
    	else return false;		
	}

	
	public function getBurnerListByDepartment($departmentID,$sortStr = null, $pagination = null) {
		$query = "SELECT * FROM burner WHERE department_id = {$departmentID} ";
		if (isset($sortStr)){
			$query .= $sortStr;
		}
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
    	$this->db->query($query);
    	
    	if ($this->db->num_rows()) 
    	{   $data = $this->db->fetch_all_array();		
    		return $data;
    	}
    	else return false;		
	}	
	

    public function getBurnerDetail($burnerID) {

    	$query = "SELECT * FROM burner WHERE burner_id = {$burnerID} ";

		//echo $query;
    	$this->db->query($query);
    	
    	if ($this->db->num_rows()) 
    	{   $data = $this->db->fetch_all_array();		
    		return $data[0];
			
    	}
    	else
    		return false;
    }
	
    public function getLogDataReadable($logList) {
		for ($i=0; $i<count($logList); $i++) 
			{
				$url="?action=viewDetails&category=logging&id=".$logList[$i]['log_id'];
				$logList[$i]['url']=$url;
				$action = json_decode($logList[$i]['action']);
					
				if ($logList[$i]['action_type'] == "AUTH"){
					$logList[$i]['action'] = "Authorization";
				}elseif ($logList[$i]['action_type'] == "LOGOUT"){
					$logList[$i]['action'] = "Logout";
				}else{
					$logList[$i]['action'] = $action->get->action." in category ".$action->get->category;
				}					
	
				$date = $logList[$i]['date'];
				$logList[$i]['date'] = date("d/m/Y H:i:s", $date);
			}
			return $logList;
	}
	
	public function loggingAutocomplete($occurrence) {

		$occurrence=mysql_escape_string($occurrence);

			$query = "SELECT u.username, LOCATE('".$occurrence."', u.username) occurrence " .
				"FROM ".TB_USER." u WHERE LOCATE('".$occurrence."', u.username)>0 LIMIT ".AUTOCOMPLETE_LIMIT;

		$this->db->query($query);
//echo $query;
		if ($this->db->num_rows() > 0) {
			$userData = $this->db->fetch_all();
			for ($i = 0; $i < count($userData); $i++) {
				if ($userData[$i]->occurrence) {
					$user = array (
						"username"		=>	$userData[$i]->username,
						"occurrence"	=>	$userData[$i]->occurrence
					);
					$results[] = $user;

				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}
	
    public function searchLog($log, $companyID = null,$facilityID = null, $departmentID = null, $pagination = null) {
    	$companyID=mysql_escape_string($companyID);		
		$facilityID=mysql_escape_string($facilityID);		
		$departmentID=mysql_escape_string($departmentID);		
		$query = "SELECT ul.* FROM user_logging ul, user u";
		$sql = '';
		if ($facilityID && $facilityID!='All facilities'){
			$sql .= " u.facility_id = {$facilityID} AND ";
		}
		if ($departmentID && $departmentID!="All departments"){
			$sql .= " u.department_id = {$departmentID} AND ";
		}		
		
		
		if ($companyID && $companyID!='All companies'){
			$query .= " WHERE company_id = ".$companyID." AND {$sql} (";		
		}else{
			$query .= " WHERE {$sql} (";
		}
		if (!is_array($log)) {
			$log = array($log);
		}
		
		$sqlParts = array();
		foreach ($log as $log_item) {
			$log_item=mysql_escape_string($log_item);
			$sqlParts[] = "u.username LIKE '%".$log_item."%'";		
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql.") AND u.user_id = ul.user_id";		
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}		
		var_dump($query);
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
				
			$searched = $this->db->fetch_all_array();
		}
		return (isset($searched)) ? $searched : null;	
    }	
	
	
}