<?php
class UserLoggingManager {

        private $db;

	function __construct($db) {
                $this->db=$db;
        }

	public function getCountLogs($userID = null, $companyID = null, $facilityID = null, $departmentID = null) {
		$table = " user_logging ul ";
		if ($userID && $userID != 'All users'){
			$sql = " ul.user_id = {$userID} AND ";
		}
		if ($companyID && $companyID != 'All companies'){
			$table .= " , user u ";
			$sql .= " u.user_id = ul.user_id AND u.company_id = {$companyID} AND ";
		}else{
			//$sql .= " u.company_id IS NULL AND ";
		}
		if ($facilityID && $facilityID != 'All facilities'){
			$sql .= " u.facility_id = {$facilityID} AND ";
		}else{
			//$sql .= " u.facility_id IS NULL AND ";
		}
		if ($departmentID && $departmentID != 'All departments'){
			$sql .= " u.department_id = {$departmentID} AND ";
		}else{
			//$sql .= " u.department_id IS NULL AND ";
		}
		$sql .= " 1 ";
		$query = "SELECT COUNT(ul.log_id) cnt FROM {$table} WHERE {$sql}";
		//echo $query;
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}

	public function MakeLog($get, $post, $user_id) {
		if ($user_id){
			$arr['get'] = $get;
			$arr['post'] = $post;
			$arr['link'] = $_SERVER['REQUEST_URI'];
			$data['action_type'] = 'GET';
			if($get['action'] == 'logout'){
				$data['action_type'] = 'LOGOUT';
			}
			$data['user_id'] = $user_id;
			$data['action'] = json_encode($arr);

			if ($post){
				$data['action_type'] = 'POST';
			}if($post['action'] == 'auth'){
				$data['action_type'] = 'AUTH';
			}
			//var_dump($_SERVER['REQUEST_URI']); //Link to page if need
			$userLogging = new UserLogging($this->db, $data);

			$userLogging->save();

		}else{
			// SOME ERROR?
		}
	}

    public function getAllLogs($userID = null,$companyID = null,$facilityID = null,$departmentID = null,$sort=' ORDER BY ul.date DESC ', $pagination = null) {
    	$sqlSelect ='';
		$tables = '';
		if ($userID && $userID != 'All users'){
			$sqlSelect .= " ul.user_id = {$userID} AND ";
		}
		if ($companyID && $companyID != 'All companies'){
			$tables .= " , user u ";
			$sqlSelect .= " u.user_id = ul.user_id AND u.company_id = {$companyID} AND ";
		}
		if ($facilityID && $facilityID != 'All facilities'){
			$sqlSelect .= " u.facility_id = {$facilityID} AND ";
		}
		if ($departmentID && $departmentID != 'All departments'){
			$sqlSelect .= " u.department_id = {$departmentID} AND ";
		}

		$sqlSelect .= " 1 ";
    	$query = "SELECT ul.* FROM user_logging ul {$tables} WHERE {$sqlSelect} ";
		if (!$sort){
			$sort=' ORDER BY ul.date DESC ';
		}
		$query .= $sort;
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}

		//echo $query;
    	$this->db->query($query);

    	if ($this->db->num_rows())
    	{
    		return $this->db->fetch_all_array();
    	}
    	else
    		return false;
    }

    public function getLogDetail($logID) {

    	$query = "SELECT ul.* FROM user_logging ul WHERE log_id = {$logID} ";

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
		$users = new User($this->db);
		for ($i=0; $i<count($logList); $i++)
			{
				$url="?action=viewDetails&category=logging&id=".$logList[$i]['log_id'];
				$logList[$i]['url']=$url;
				$userDetail = $users->getUserDetails($logList[$i]['user_id']);
				$logList[$i]['username']=$userDetail['username'];

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
		
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {

			$searched = $this->db->fetch_all_array();
		}
		return (isset($searched)) ? $searched : null;
    }


}