<?php
require_once('modules/phpgacl/gacl.class.php');
require_once('modules/phpgacl/gacl_api.class.php');
class User {
	//	properties
	var $db;
	var $xnyo;
	var $access;
	var $auth;
	
	public function getLoggedUserID()
	{
		if($_SESSION['user_id'])
		{
			return $_SESSION['user_id'];
		}
		else
		{
			return false;
		}
	}
	

	function User($db, $xnyo, $access, $auth) {
		
		$this->db=$db;
		$this->xnyo=$xnyo;
		$this->access=$access;
		$this->auth=$auth;
		
	}
	
	function addUser($userData) {
		//$this->db->select_db(DB_NAME);
				
		
		$accesslevel_id = $userData["accesslevel_id"] == null ? "NULL" : $userData["accesslevel_id"];
		$company_id = $userData["company_id"] == null ? "NULL" : $userData["company_id"];
		$facility_id = $userData["facility_id"] == null	? "NULL" : $userData["facility_id"];
		$department_id = $userData["department_id"] == null ? "NULL": $userData["department_id"];
		$creater_id = $userData["creater_id"] == null ? "NULL" : $userData["creater_id"];		
		$jobber_id = $userData["jobber_id"] == null	? "NULL" : $userData["jobber_id"];
		
		$query="INSERT INTO ".TB_USER." (accessname, password, username, phone, mobile, email, accesslevel_id, company_id, facility_id, department_id, grace, creater_id) VALUES (";
		$query.="'".mysql_escape_string($userData["accessname"])."', ";
		$query.="'".md5($userData["password"])."', ";
		$query.="'".mysql_escape_string($userData["username"])."', ";
		$query.="'".mysql_escape_string($userData["phone"])."', ";
		$query.="'".mysql_escape_string($userData["mobile"])."', ";
		$query.="'".mysql_escape_string($userData["email"])."', ";
		$query.=$accesslevel_id.", ";
		$query.=$company_id.", ";
		$query.=$facility_id.", ";	
		$query.=$department_id.", ";
		$query.="'".mysql_escape_string($userData["grace"])."', ";
		$query.=$creater_id;		
		$query.=')';
		
		$this->db->query($query);
		$insertedUserID = $this->db->getLastInsertedID();
		
		$sql = "INSERT INTO users2jobber (id , user_id, jobber_id) VALUES (NULL , {$insertedUserID} , {$jobber_id})";
		$this->db->query($sql);
		/**
		 * add new User in Bridge
		 
//		//add new User in Bridge
//		$query = "SELECT user_id FROM ".TB_USER." order by user_id DESC Limit 1";
//		$this->db->query($query);
//		$data = $this->db->fetch(0);
//		
//		if (isset($data->user_id)) {
//			
//				$userData4Bridge = $userData;	//	do not rewrite input vars
//				
//				$userID = $data->user_id;
//				$bridge = new Bridge($this->db);				
//				$userData4Bridge["password"] = md5($userData4Bridge["password"]);
//				$userData4Bridge['facility_id'] = 0; 			// only company level
//				$userData4Bridge['department_id'] = 0;			// only company level
//				$bridge->addNewUser($userID, $userData4Bridge);
//		}
//		//end of Bridge
		 * 
		 */
		
		$gacl_api = new gacl_api();
		$login_lower=strtolower($userData["accessname"]);
		$groupID=$userData["accesslevel_id"]+11;
		$gacl_api->add_object('users', $userData["accessname"], $login_lower, NULL, 0, 'ARO');
		$gacl_api->add_group_object($groupID, 'users', $login_lower, 'ARO');
		
		
		//	NEW ACCESS CONTROL | June 15, 2010, Denis & Yura
		$groupType = 'ARO';
		$separator = '_';
			
		//$gacl_api->add_object('users', $userData["accessname"], $userData["accessname"], 0, 0, 'ARO');		
		
		//	form group name & value
		switch($userData["accesslevel_id"]) {
			case 0:
				$aroGroupName = 'company'.$separator.$userData["company_id"];				
				break;
			case 1:
				$aroGroupName = 'facility'.$separator.$userData["facility_id"];				
				break;
			case 2:
				$aroGroupName = 'department'.$separator.$userData["department_id"];				
				break;
			case 3:
				$aroGroupName = 'root';
				break;
			case 4:
				$aroGroupName = 'sales';
				break;
			case 5:
				$aroGroupName = 'supplier';
				break;			
			default:
				throw new Exception('Incorrect access level');
		}
		
		//	yes, they are equal		
		$aroGroupValue = $aroGroupName;
	
		if (false !== ($aroGroupID = $gacl_api->get_group_id($aroGroupName, $aroGroupValue, $groupType)) ) {
			//	ARO GROUP FOUND
			$gacl_api->add_group_object($aroGroupID, 'users', $userData["accessname"], $groupType);			 	
		} else {
			//	ARO GROUP NOT FOUND
			throw new Exception('ARO group not found');
		}		
		
		return $insertedUserID;						  							
	}
	
	
	
	function getUserDetails($user_id, $vanilla=false) {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT * FROM ".TB_USER." WHERE user_id=".$user_id;
		$this->db->query($query);
		$userDetails=$this->db->fetch_array(0);
		
		/*$userDetails=array (
			'user_id'			=>	$data->user_id,
			'accessname'		=>	$data->accessname,
			'username'			=>	$data->username,
			'phone'				=>	$data->phone,
			'mobile'			=>	$data->mobile,
			'email'				=>	$data->email,
			'accesslevel_id'	=>	$data->accesslevel_id,
			'company_id'		=>	$data->company_id,
			'facility_id'		=>	$data->facility_id,
			'department_id'		=>	$data->department_id,
			'grace'				=>	$data->grace
		);*/
		
		
		
		$userDetails['startPoint']=$this->getUserStartPoint($userDetails['user_id']);
		if (!$userDetails['startPoint']){

					$sp=$this->getSupplierStartPoint($userDetails['user_id']);
					if ($sp){
						$supList = '';
						foreach($sp as $sup){
							$supList .= $sup['supplier'].". ";
						}
						$userDetails['startPoint']=$supList;
					}			
		}
		if (!$vanilla) {
			switch ($userDetails['accesslevel_id']) {
				case 0:
					$userDetails['accesslevel_id']="Company Level";
					break;
				case 1:
					$userDetails['accesslevel_id']="Facility Level";
					break;
				case 2:
					$userDetails['accesslevel_id']="Department Level";
					break;
				case 3:
					$userDetails['accesslevel_id']="Superuser level (Admin)";
					break;
				case 4:
					$userDetails['accesslevel_id']="Sales level";
					break;
			}
						//TODO: WTF id=name?!
						
			//$company=new Company($this->db);
			//$companyDetails=$company->getCompanyDetails($userDetails['company_id']);

			//$userDetails['company_id']=$companyDetails['name'];
			
			//$facility=new Facility($this->db);
			//$facilityDetails=$facility->getFacilityDetails($userDetails['facility_id']);
			//$userDetails['facility_id']=$facilityDetails['name'];
			
			//$department=new Department($this->db);
			//$departmentDetails=$department->getDepartmentDetails($userDetails['department_id']);
			//$userDetails['department_id']=$departmentDetails['name'];
		}
		
		return $userDetails;
	}
	
	function setUserDetails($userData, $fullUpdate=false) {
		//$this->db->select_db(DB_NAME);
		
		$groupType = 'ARO';
		$separator = '_';
		$gacl_api = new gacl_api();		
		
		//Delete ARO user with last group
		$this->db->query("SELECT * FROM ".TB_USER." WHERE user_id=".$userData["user_id"]);
		$data=$this->db->fetch(0);		
		$object_id=$gacl_api->get_object_id('users',$data->accessname,'ARO');
		$gacl_api->del_object($object_id,'ARO',true);
		
		$userData['company_id'] = ($userData['company_id'] == null) ? "NULL" : $userData['company_id'];
		$userData['facility_id'] = ($userData['facility_id'] == null) ? "NULL" : $userData['facility_id'];
		$userData['department_id'] = ($userData['department_id'] == null) ? "NULL" : $userData['department_id'];
		
		if ($fullUpdate) {	//	update with accessname, password and accesslevel - for ADMINs
			$query="UPDATE ".TB_USER." SET ";
			
			$query.="accessname='".		$userData['accessname']."', ";
			$query.="password='".		md5($userData['password'])."', ";
			$query.="username='".		$userData['username']."', ";
			$query.="phone='".			$userData['phone']."', ";
			$query.="mobile='".			$userData['mobile']."', ";
			$query.="email='".			$userData['email']."', ";
			$query.="accesslevel_id=".	$userData['accesslevel_id'].", ";
			$query.="company_id=".		$userData['company_id'].", ";
			$query.="facility_id=".		$userData['facility_id'].", ";
			$query.="department_id=".	$userData['department_id']." ";			
			
			$query.="WHERE user_id=".	$userData["user_id"];
			
		} else {
			
			$query="UPDATE ".TB_USER." SET ";
			
			$query.="accessname='".		$userData['accessname']."', ";			
			$query.="username='".		$userData['username']."', ";
			$query.="phone='".			$userData['phone']."', ";
			$query.="mobile='".			$userData['mobile']."', ";
			$query.="email='".			$userData['email']."', ";
			$query.="accesslevel_id=".	$userData['accesslevel_id'].", ";
			$query.="company_id=".		$userData['company_id'].", ";
			$query.="facility_id=".		$userData['facility_id'].", ";
			$query.="department_id=".	$userData['department_id']." ";			
			
			$query.="WHERE user_id=".	$userData["user_id"];
		}		
		$this->db->query($query);
			$query="UPDATE users2supplier SET ";
			$query.="supplier_id=".		$userData['supplier_id']."";			
			$query.="WHERE user_id=".	$userData["user_id"];
		$this->db->query($query);	
//		// set user data to Bridge XML
//		$userData4Brdige = $userData;
//		$userID = (int)$userData4Brdige["user_id"];
//		$bridge = new Bridge($this->db);
//		if ($fullUpdate) $userData4Brdige["password"] = md5($userData4Brdige["password"]);
//		 else unset($userData4Brdige["password"]);
//		
//		 
//		$userData4Brdige['facility_id'] = 0;  		// only company level
//		$userData4Brdige['department_id'] = 0; 	// only company level		  
//		$bridge->setUserDetails($userID, $userData4Brdige);
//		//end of Bridge XML
		
		
		$login_lower=strtolower($userData["accessname"]);
		$groupID=$userData["accesslevel_id"]+11;
		$gacl_api->add_object('users', $userData["accessname"], $login_lower, NULL, 0, 'ARO');
		$gacl_api->add_group_object($groupID, 'users', $login_lower, 'ARO');		
		
		//	NEW ACCESS CONTROL | June 15, 2010, Denis & Yura					
		//$gacl_api->add_object('users', $userData["accessname"], $userData["accessname"], 0, 0, 'ARO');		
		
		//	form group name & value
		switch($userData["accesslevel_id"]) {
			case 0:
				$aroGroupName = 'company'.$separator.$userData["company_id"];				
				break;
			case 1:
				$aroGroupName = 'facility'.$separator.$userData["facility_id"];				
				break;
			case 2:
				$aroGroupName = 'department'.$separator.$userData["department_id"];				
				break;
			case 3:
				$aroGroupName = 'root';
				break;
			case 4:
				$aroGroupName = 'sales';
				break;
			case 5:
				$aroGroupName = 'supplier';
				break;			
			default:
				throw new Exception('Incorrect access level');
		}
		
		//	yes, they are equal		
		$aroGroupValue = $aroGroupName;
	
		if (false !== ($aroGroupID = $gacl_api->get_group_id($aroGroupName, $aroGroupValue, $groupType)) ) {
			//	ARO GROUP FOUND
			$gacl_api->add_group_object($aroGroupID, 'users', $userData["accessname"], $groupType);			 	
		} else {
			//	ARO GROUP NOT FOUND
			throw new Exception('ARO group not found');
		}		
		
		
	}
	
	function getUsersList($itemID="",Pagination $pagination = null,$filter=' TRUE ',$sort='') {
		//$this->db->select_db(DB_NAME);
		if ($itemID=="") {
			$query="SELECT * FROM ".TB_USER;
		} else {
			switch ($itemID) {
				case "company":
					$access_level=0;
					break;
				case "facility":
					$access_level=1;
					break;
				case "department":
					$access_level=2;
					break;
				case "admin":
					$access_level=3;
					break;
				case "sales":
					$access_level=4;
					break;
				case "supplier":
					$access_level=5;
					break;				
			}
			$query="SELECT * FROM ".TB_USER." WHERE accesslevel_id=$access_level AND $filter $sort";
			
			if (isset($pagination)) {
				$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
			}
		}
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$user=array(
					'user_id'	=>	$data->user_id,
					'accessname'	=>	$data->accessname,
					'password'		=>	$data->password,
					'username'		=>	$data->username,
					'phone'			=>	$data->phone,
					'mobile'		=>	$data->mobile,
					'email'			=>	$data->email,
					'accesslevel_id'=>	$data->accesslevel_id,
					'company_id'	=>	$data->company_id,
					'facility_id'	=>	$data->facility_id,
					'department_id'	=>	$data->department_id,
					'grace'			=>	$data->grace
				);
				$users[]=$user;
			}
			if ($itemID == 'supplier'){
				for ($i=0; $i < count($users); $i++) {
					$sp=$this->getSupplierStartPoint($users[$i]['user_id']);
					if ($sp){
						$supList = '';
						foreach($sp as $sup){
							$supList .= $sup['supplier'].". ";
						}
						$users[$i]['startPoint']=$supList;
					}
					
				}				
			}else{
				for ($i=0; $i < count($users); $i++) {
					$sp=$this->getUserStartPoint($users[$i]['user_id']);
					$users[$i]['startPoint']=$sp;
				}
			}
		}
		
		return $users;
	}
	
	function getUserListByCompany ($company_id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_USER." WHERE company_id=".$company_id);
		
		if ($this->db->num_rows()) {
//			for ($i=0; $i<$this->db->num_rows(); $i++) {
//				$data=$this->db->fetch($i);
//				$user=array(
//					'user_id'		=>	$data->user_id,
//					'accessname'	=>	$data->accessname,
//					'password'		=>	$data->password,
//					'username'		=>	$data->username,
//					'phone'			=>	$data->phone,
//					'mobile'		=>	$data->mobile,
//					'email'			=>	$data->email,
//					'accesslevel_id'=>	$data->accesslevel_id,
//					'company_id'	=>	$data->company_id,
//					'facility_id'	=>	$data->facility_id,
//					'department_id'	=>	$data->department_id,
//					'grace'			=>	$data->grace
//				);
//				$users[]=$user;
//			}
			$users = $this->db->fetch_all_array();
		}
		return $users;
	}
	
	function getUserListByFacility($facility_id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_USER." WHERE facility_id=".$facility_id);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$user=array(
					'user_id'		=>	$data->user_id,
					'accessname'	=>	$data->accessname,
					'password'		=>	$data->password,
					'username'		=>	$data->username,
					'phone'			=>	$data->phone,
					'mobile'		=>	$data->mobile,
					'email'			=>	$data->email,
					'accesslevel_id'=>	$data->accesslevel_id,
					'company_id'	=>	$data->company_id,
					'facility_id'	=>	$data->facility_id,
					'department_id'	=>	$data->department_id,
					'grace'			=>	$data->grace
				);
				$users[]=$user;
			}
		}
		return $users;
	}
	
	function getUserListByDepartment($department_id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_USER." WHERE department_id=".$department_id);
		
		if ($this->db->num_rows()) {
			for ($i=0; $i<$this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$user=array(
					'user_id'		=>	$data->user_id,
					'accessname'	=>	$data->accessname,
					'password'		=>	$data->password,
					'username'		=>	$data->username,
					'phone'			=>	$data->phone,
					'mobile'		=>	$data->mobile,
					'email'			=>	$data->email,
					'accesslevel_id'=>	$data->accesslevel_id,
					'company_id'	=>	$data->company_id,
					'facility_id'	=>	$data->facility_id,
					'department_id'	=>	$data->department_id,
					'grace'			=>	$data->grace
				);
				$users[]=$user;
			}
		}
		return $users;
	}
	
	function getUserIDbyAccessname($accessname) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT user_id FROM ".TB_USER." WHERE accessname='".$accessname."'");
		$data=$this->db->fetch(0);
		return $data->user_id;
	}
	
	function getUsernamebyAccessname($accessname) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT username FROM ".TB_USER." WHERE accessname='".$accessname."'");
		$data=$this->db->fetch(0);
		return $data->username;
	}
	
	
	function getUserAccessLevel($id) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT accesslevel_id FROM ".TB_USER." WHERE user_id=".$id);
		$data=$this->db->fetch(0);
		$accesslevel_id=$data->accesslevel_id;
		switch ($accesslevel_id) {
			case 3:
				return "SuperuserLevel";
				break;
				
			case 0:
				return "CompanyLevel";
				break;
				
			case 1:
				return "FacilityLevel";
				break;
				
			case 2:
				return "DepartmentLevel";
				break;
		}
	}
	
	function getUserAccessLevelByAccessname($accessname) {
		//$this->db->select_db(DB_NAME);
		$query="SELECT accesslevel_id FROM ".TB_USER." WHERE accessname='".$accessname."'";
		$this->db->query($query);
		$data=$this->db->fetch(0);
		$accesslevel_id=$data->accesslevel_id;
		switch ($accesslevel_id) {
			case 3:
				return "SuperuserLevel";
				break;
			case 0:
				return "CompanyLevel";
				break;
			case 1:
				return "FacilityLevel";
				break;
			case 2:
				return "DepartmentLevel";
				break;
		}
	}
	
	function getUserAccessLevelIDByAccessname($accessname) {
		//$this->db->select_db(DB_NAME);
		$query="SELECT accesslevel_id FROM ".TB_USER." WHERE accessname='".$accessname."'";
		$this->db->query($query);
		$data=$this->db->fetch(0);
		return $data->accesslevel_id;
	}
	
	
	function getAccessnameByID($id) {
		//$this->db->select_db(DB_NAME);
		$query="SELECT accessname FROM ".TB_USER." WHERE user_id = ".$id;
		$this->db->query($query);		
		return $this->db->fetch(0)->accessname;
	}
	
	public function isUniqueAccessName($accessname , $id = null) {
		$query = "SELECT accessname FROM ".TB_USER;
		$query .= " WHERE accessname = '$accessname'";
		if (!is_null($id)) $query .= " AND user_id != '$id' ";
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			return false;
		} else return true;
	}
	
	public function isValidRegData($data, &$check) {
		$isValid=true;
		//	check for email
		require_once('modules/Validate.php');
		if (validate::email($data['email']) == 0) {
			//	email failed
			$isValid=false;
			$check['email']='failed';
		}
		
		//	check for username
		if (strlen(trim($data['username'])) == 0) {
			$isValid=false;
			$check['username']='failed';
		}
		//	check for accessname
		if (strlen(trim($data['accessname'])) == 0) {
			$isValid=false;
			$check['accessname']='failed';
		} elseif ($check['accessname'] == 'alreadyExist') {
			$isValid=false;
		}
		//	check for phone
		if (strlen(trim($data['phone'])) == 0) {
			$isValid=false;
			$check['phone']='failed';
		}
		//	check for mobile
		if (strlen(trim($data['mobile'])) == 0) {
			$isValid=false;
			$check['mobile']='failed';
		}
		//	check for password
		if ($_POST['password'] != $_POST['confirm_password']) {
			$isValid=false;
			$check['password']='different';
		} 
		/*elseif (strlen(trim($_POST['password'])) == 0) {
			$isValid=false;
			$check['password']='failed';
		} elseif (strlen(trim($_POST['confirm_password'])) == 0) {
			$isValid=false;
			$check['confirm_password']='failed';						
		}
			*/	
		return $isValid;
	}
	
	public function isLoggedIn(){
		if ($this->access->check("required")) {
			return true;
		} else {
			return false;
		}
	}
	
	public function logout() {
		$this->access->logout();
		header ('Location: '.$this->xnyo->logout_redirect_url);
	}
	
	
	public function isUserExists($accessname,$md5password,$accesslevel_id = 0) {
		$query = "SELECT user_id FROM ".TB_USER.
				" WHERE accessname = '$accessname' ".
				" AND password = '$md5password' " .
				" AND accesslevel_id = '$accesslevel_id' " .
				" LIMIT 1 ";
		$this->db->query($query);
		
		$numRows = $this->db->num_rows();
		if ($numRows > 0) 
		{
			return true; //Reutrn true if user exists
		}
		else
		{
			return false; // Return false if user does not exist
		} 
		
	}
	
		
	public function isHaveAccessTo($action, $category) {
		$accessname=strtolower($_SESSION['accessname']);
		$gacl_api = new gacl_api();
		if ($gacl_api->acl_check('access', $category, 'users', $accessname, 'action', $action)) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * ask PHPGACL about access to something
	 * @param string Level - company | facility | department OR module name
	 * @param int Level's ID of companyID if level is module
	 * @return bool Result
	 */
	 public function checkAccess($level, $levelsID = null) {
	 	//	isModule flag
	 	$module = false;
	 	//	separator that used in Object names at GACL
	 	$separator = '_';
	 	$acoValue = ($level !== 'root') ? $level.$separator.$levelsID : $level;
	 	$gacl_api = new gacl_api();
	 	$ms = new ModuleSystem($this->db);
	 	
	 	foreach($ms->getModulesMap() as $key => $value) {
	 		if ($key == $level) {
				
	 			$acoValue = $level;
	 			$module = true;
	 		}
	 	}

	 	//	Super user should see the same picture as whole company, поэтому хитрость мля
	 	if ($this->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) == 3 && $module && !is_null($levelsID)) {
	 		$access = false;
	 		$acls = $ms->searchModule2company($level, $levelsID);
	 		
	 		foreach ($acls as $acl) {
	 			$aclInfo = $gacl_api->get_acl($acl);
	 			if($aclInfo['allow']) {
	 				$access = true;
	 			}
	 		}
	 		return ($access) ? true : false;	 		
	 	//	End of хитрость
	 	 	
	 	} else {	 			 			 		 		
	 		if ($gacl_api->acl_check('access', $acoValue, 'users', $_SESSION['accessname'])) {
	 			return true;
	 		} else {
	 			return false;
	 		}
	 	}
	 }
	
	/*
	private function isCompanyLevelUser($accessname, $password) {
		//$this->db->select_db(DB_NAME);
		$query = "SELECT accesslevel_id FROM ".TB_USER." WHERE accessname='".$accessname
	}
	*/
	
	public function auth($accessname, $password) {		
		if ($this->auth->login($accessname, $password)) {						
			/*
			//	VPS Validation
			//	Validate if company level user
			if ($this->isCompanyLevelUser($accessname, $password)) {
				//	Send to VOCtoVPS
				
			}
			*/
			return true;
		} else {
			return false;
		} 
	}
	
	public function getUserStartPoint($userID) {
		//$this->db->select_db(DB_NAME);
		$query="SELECT * FROM ".TB_USER." WHERE user_id=".$userID;
		$this->db->query($query);
		$data=$this->db->fetch(0);
		$startPoint=array(
			"company"	=>	$data->company_id,
			"facility"	=>	$data->facility_id,
			"department"	=>	$data->department_id
		);
		$company=new Company($this->db);
		$companyDetails=$company->getCompanyDetails($startPoint['company']);
		$path=$companyDetails['name'];
		if ($startPoint['facility']!=0) {
			$facility=new Facility($this->db);
			$facilityDetails=$facility->getFacilityDetails($startPoint['facility']);
			$path.=" > ".$facilityDetails['name'];
		}
		if ($startPoint['department']!=0) {
			$department=new Department($this->db);
			$departmentDetails=$department->getDepartmentDetails($startPoint['department']);
			$path.=" > ".$departmentDetails['name'];
		}
		return $path;
	}
	
	public function getSupplierStartPoint($userID) {
		//$this->db->select_db(DB_NAME);
		$query="SELECT s.supplier FROM users2supplier us, supplier s WHERE us.supplier_id = s.original_id AND s.supplier_id = s.original_id AND us.user_id=".$userID;
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$data=$this->db->fetch_all_array();

		return $data;
	}	
	
	public function deleteUser ($id) {
		
		$gacl_api = new gacl_api();			
		//Delete ARO user with last group
		$this->db->query("SELECT * FROM ".TB_USER." WHERE user_id=".$id);
		$data=$this->db->fetch(0);		
		$object_id=$gacl_api->get_object_id('users',$data->accessname,'ARO');		
		$res=$gacl_api->del_object($object_id,'ARO',true);		
		$query="DELETE FROM ".TB_USER." WHERE user_id=".$id;
		$this->db->query($query);
		
//		//delete user from Bridge XML
//		$bridge = new Bridge($this->db);
//		$bridge->deleteUser($id);
//		//end of Bridge
	}
	
	public function clearUser() {
		//$this->db->select_db(DB_NAME);
		$query="DELETE FROM ".TB_USER;
		$this->db->query($query);
		
//		//delete all users from Bridge XML
//		$bridge = new Bridge($this->db);
//		$bridge->deleteAllUsers();
//		//end of Bridge
	}
	
	public function fillUser() {
		$this->db->select_db(DB_IMPORT);    	
    	$query = "INSERT INTO ".DB_NAME.".".TB_USER." SELECT * FROM ".DB_IMPORT.".".TB_USER;
    	$this->db->query($query);
	}
	
	public function queryTotalCount($itemID="",$filter=" TRUE ") {
		if ($itemID=="") {
			$query="SELECT COUNT(*) cnt FROM ".TB_USER;
		} else {
			switch ($itemID) {
				case "company":
					$access_level=0;
					break;
				case "facility":
					$access_level=1;
					break;
				case "department":
					$access_level=2;
					break;
				case "admin":
					$access_level=3;
					break;
				case "supplier":
					$access_level=5;
					break;				
			}
			$query="SELECT COUNT(*) cnt FROM ".TB_USER." WHERE accesslevel_id=$access_level AND $filter";
		}		
		$this->db->query($query);
		return $this->db->fetch(0)->cnt;
	}
}
?>