<?php
class UserRequest {
    /**
     *
     * @var db
     */
    private $db;
    
    private $action;
    private $user_id;
	private $username;
	private $new_username;
	private $new_accessname;
	private $email;
	private $phone;
	private $mobile;
	private $category_type;
	private $category_id;
    /**
     *
     * @var DateTime
     */
    private $date;
    private $creater_id;
    private $status;
    
    const STATUS_NEW = 'new';
    
    public function __construct(db $db) {
        $this->db = $db;
        $this->setDate(new DateTime());
        $this->creater_id = $_SESSION['user_id'];
        $this->status = self::STATUS_NEW;
    }

    public function setAction($action){
        $this->action = $action;
    }
	
	public function setUserID($userID) {
        $this->user_id = $userID;
    }
	
	public function setUserName($username) {
        $this->username = $username;
    }
	
	public function setNewUserName($new_username) {
        $this->new_username = $new_username; 
    }
    
	public function setNewAccessName($new_accessname){
        $this->new_accessname = $new_accessname;
    }
	
	public function setEmail($email){
        $this->email = $email;
    }
	
	public function setPhone($phone){
        $this->phone = $phone;
    }
	
	public function setMobile($mobile){
        $this->mobile = $mobile;
    }
	
	public function setCategoryType($category_type) {
        $this->category_type = $category_type;
    }
	
	public function setCategoryID($category_id){
        $this->category_id = $category_id;
    }
	
	public function setDate(DateTime $date) {
        $this->date = $date;
    }
	
    public function setCreaterID($usernameID) {
        $this->creater_id = $usernameID;
    }
    
    public function setStatus($status) {
        $this->status = $status;        
    }
    
	public function setALL($action, $user_id, $username, $new_username, $new_accessname, $email, $phone, $mobile,
						   $category_type, $category_id){ 
		$this->action = $action;
		$this->user_id = $user_id;
		$this->username = $username;
		$this->new_username = $new_username;
		$this->new_accessname = $new_accessname;
		$this->email = $email;
		$this->phone = $phone;
		$this->mobile = $mobile;
		$this->category_type = $category_type;
		$this->category_id = $category_id;
	}

	public function getAction(){
        return $this->action;
    }
	
	public function getUserID(){
        return $this->user_id;
    }
	
	public function getUserName(){
        return $this->username;
    }
	
	public function getNewUserName(){
        return $this->new_username;
    }
    
	public function getNewAccessName(){
        return $this->new_accessname;
    }
	
	public function getEmail(){
        return $this->email;
    }
	
	public function getPhone(){
        return $this->phone;
    }
	
	public function getMobile(){
        return $this->mobile;
    }
	
	public function getCategoryType(){
        return $this->category_type;
    }
	
	public function getCategoryID(){
        return $this->category_id;
    }
	
	public function getDate(){
        return $this->date;
    }
	
    public function getCreaterID(){
        return $this->creater_id;
    }
    
    public function getStatus(){
        return $this->status;        
    }

    public function validate($user) {
        $result["summary"] = "true";
		
		$result["productSupplier"] = "failed";
		if (isset($product["productSupplier"])) {
			$product["productSupplier"] = trim($product["productSupplier"]);
			$supplierLength = strlen($product["productSupplier"]);
			
			if ($supplierLength > 0 && $supplierLength < 120) {
				$result["productSupplier"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
		
		$result["productId"] = "failed";
		if (isset($product["productId"])) {
			$product["productId"] = trim($product["productId"]);
			$idLength = strlen($product["productId"]);
			
			if ($idLength > 0 && $idLength < 20) {
				$result["productId"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
		
                $result["productName"] = "failed";
		if (isset($product["productName"])) {
			$product["productName"] = trim($product["productName"]);
			$nameLength = strlen($product["productName"]);
			
			if ($nameLength > 0 && $nameLength < 120) {
				$result["productName"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
                
                $result["productDescription"] = "failed";
		if (isset($product["productDescription"])) {
			$product["productDescription"] = trim($product["productDescription"]);
			$descriptionLength = strlen($product["productDescription"]);
			
			if ($descriptionLength > 0 && $descriptionLength < 120) {
				$result["productDescription"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}
		return $result;
    }
    
    public function save() {
        $query = "INSERT INTO ".TB_USER_REQUEST." (action, user_id, username, new_username, new_accessname, email, phone, mobile, category_type, category_id, date, creater_id, status)".
				" VALUES (".
                "'".mysql_escape_string($this->action)."', ".
                "".mysql_escape_string($this->user_id).", ".
				"'".mysql_escape_string($this->username)."', ".
                "'".mysql_escape_string($this->new_username)."', ".
				"'".mysql_escape_string($this->new_accessname)."', ".
				"'".mysql_escape_string($this->email)."', ".
				"'".mysql_escape_string($this->phone)."', ".
				"'".mysql_escape_string($this->mobile)."', ".
                "'".mysql_escape_string($this->category_type)."', ".
                "".mysql_escape_string($this->category_id).", ".
                "".mysql_escape_string($this->date->getTimestamp()).", ".
                "".mysql_escape_string($this->creater_id).", ".
                "'".mysql_escape_string($this->status)."')";
		$this->db->query($query);
		
		if (mysql_errno() != 0){
			$error = "Error!";
		} else {
			$error = "";
		}
		
		return $error;
    }
	
	public function update($requestID, $addComments = ''){
		$query = "UPDATE ".TB_USER_REQUEST." SET status='".mysql_escape_string($this->status)."' WHERE id=".$requestID;
		$this->db->query($query);
		if (mysql_errno() != 0){
			$error = "Error!";
		} else {
			$error = "";
			if ($this->status == 'deny'){
				$query = "SELECT email FROM ".TB_USER_REQUEST." WHERE id=".$requestID;
				$this->db->query($query);
				$userEmail = $this->db->fetch(0)->email;
				$newMail = new EMail();
				$message = "To create a new user denied.\n";
				$message .= $addComments;
				$newMail->sendMail('newuserrequest@vocwebmanager.com', $userEmail, 'User Request', $message);
			}
		}
		
		return $error;	
	}
	
	public function addNewUser($requestID, $addComments = ''){
		$query = "SELECT * FROM ".TB_USER_REQUEST." WHERE id=".$requestID;
		$this->db->query($query);
		$row = $this->db->fetch(0);
		$passLength = 7;
		$password = $this->generate_password($passLength);
		$columns = "username, accessname, password, phone, mobile, email, ";
		$data = "'".$row->new_username."', '".$row->new_accessname."', '".md5($password)."', '".$row->phone."', '".$row->mobile."', '".$row->email."', ";
		switch ($row->category_type){
			case 'company':
				$columns .= "accesslevel_id, company_id, ";
				$data .= "0, ".$row->category_id.", ";
				break;
			case 'facility':
				$this->db->query("SELECT company_id FROM ".TB_FACILITY." WHERE facility_id=".$row->category_id);
				$companyID = $this->db->fetch(0)->company_id;
				$columns .= "accesslevel_id, company_id, facility_id, ";
				$data .= "1, ".$companyID.", ".$row->category_id.", ";
				break;
			case 'department':
				$this->db->query("SELECT facility_id FROM ".TB_DEPARTMENT." WHERE department_id=".$row->category_id);
				$facilityID = $this->db->fetch(0)->facility_id;
				$this->db->query("SELECT company_id FROM ".TB_FACILITY." WHERE facility_id=".$facilityID);
				$companyID = $this->db->fetch(0)->company_id;
				$columns .= "accesslevel_id, company_id, facility_id, department_id, ";
				$data .= "2, ".$companyID.", ".$facilityID.", ".$row->category_id.", ";
				break;
		}
		$columns .= "creater_id, terms_conditions";
		if ($row->creater_id != NULL){
			$createrID = $row->creater_id;
		} else {
			$createrID = 'NULL';
		}
		$data .= $createrID.", 0";
		
		$queryUnique = "SELECT accessname FROM ".TB_USER;
		$this->db->query($queryUnique);
		$names = $this->db->fetch_all();
		if (in_array($row->new_accessname, $names->accessname)){
			$error = "This Accessname already exists!";
		} else {
			$quesrySave = "INSERT INTO ".TB_USER." (".$columns.") VALUES (".$data.")";
			$this->db->query($quesrySave);
			
			if (mysql_errno() == 0){
				$error = '';
				$newMail = new EMail();
				$message = "New User Created.\n";
				$message .= "Accessname: ".$row->new_accessname."\n";
				$message .= "Password: ".$password."\n\n";
				$message .= $addComments;
				$newMail->sendMail('newuserrequest@vocwebmanager.com', $row->email, 'User Request', $message);
			} else {
				$error = "Error!";
			}
		}
		
		return $error;
	}
	
	public function deleteUser($requestID, $addComments = ''){
		$query = "SELECT * FROM ".TB_USER_REQUEST." WHERE id=".$requestID;
		$this->db->query($query);
		$userToDelete = $this->db->fetch(0)->user_id;
		$userEmail = $this->db->fetch(0)->email;
		$username = $this->db->fetch(0)->username;
		
		$queryDelete = "DELETE FROM ".TB_USER." WHERE user_id=".$userToDelete;
		$this->db->query($queryDelete);
		
		if (mysql_errno() == 0){
			$error = '';
			$newMail = new EMail();
			$message = "User ".$username." Deleted.\n\n";
			$message .= $addComments;
			$newMail->sendMail('newuserrequest@vocwebmanager.com', $userEmail, 'User Request', $message);
		} else {
			$error = "Error!";
		}
		
		return $error;
	}
	
	public function changeUser($requestID, $addComments = ''){
		$query = "SELECT * FROM ".TB_USER_REQUEST." WHERE id=".$requestID;
		$this->db->query($query);
		$userToChange = $this->db->fetch(0)->user_id;
		$newUsername = $this->db->fetch(0)->new_username;
		$username = $this->db->fetch(0)->username;
		$userEmail = $this->db->fetch(0)->email;
		
		$queryChange = "UPDATE ".TB_USER." SET username='".$newUsername."' WHERE user_id=".$userToChange;
		$this->db->query($queryChange);
		
		if (mysql_errno() == 0){
			$error = '';
			$newMail = new EMail();
			$message = "Username changed.\n";
			$message .= "Old username: ".$username."\n";
			$message .= "New username: ".$newUsername."\n\n";
			$message .= $addComments;
			$newMail->sendMail('newuserrequest@vocwebmanager.com', $userEmail, 'User Request', $message);
		} else {
			$error = "Error!";
		}
		
		return $error;
	}

	public function sendMail($message){
		$newUserMail = new EMail();
		$newUserMail->sendMail('newuserrequest@vocwebmanager.com', array('denis.nt@kttsoft.com', 'jgypsyn@gyantgroup.com'), 'New User Request', $message);
		//$newUserMail->sendMail('userrequest@vocwebmanager.com', 'dmitry.ds@kttsoft.com', 'New User Request', $message);
	}
	
	public function changePassword($userID, $oldPass, $newPass, $reNewPass){
		$query = "SELECT accessname, password, email FROM ".TB_USER." WHERE user_id=".$userID;
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$result = $this->db->fetch(0);
			if ($result->password == md5(trim($oldPass))){
				if ((strlen(trim($newPass)) > 5) && (strlen(trim($reNewPass)) > 5) && 
					(trim($newPass) == trim($reNewPass)) && 
					(strlen(trim($newPass)) < 12) && (strlen(trim($reNewPass)) < 12)){
					//save new password to DB in md5
					$query = "UPDATE ".TB_USER." SET password = '".md5($newPass)."' WHERE user_id=".$userID;
					$this->db->query($query);
					if (mysql_errno() == 0){
						$newUserMail = new EMail();
						$message = "Username: ".$result->accessname."\n";	
						$message .= "Password: ".$newPass;
						$newUserMail->sendMail('userrequest@vocwebmanager.com', $result->email, 'User Request Password', $message);
					}
				} else {
					$error = 'Incorrect password! Password length is 5-12 symbols.';
				}
			} else {
				$error = 'Incorrect old password!';
			}
		} else {
			$error = 'Password is not changed.';
		}
		return $error;
	}
	
	public function lostPassword($userID){
		$query = "SELECT accessname, password, email FROM ".TB_USER." WHERE user_id=".$userID;
		$this->db->query($query);
		if ($this->db->num_rows() > 0){
			$result = $this->db->fetch(0);
			//generate new password. length = 7. save it and send to email
			$passLength = 7;
			$newPassword = $this->generate_password($passLength);
			if ($result->email != ''){
				$query = "UPDATE ".TB_USER." SET password = '".md5($newPassword)."' WHERE user_id=".$userID;
				$this->db->query($query);
				if (mysql_errno() == 0){
					$newUserMail = new EMail();
					$message = "Username: ".$result->accessname."\n";
					$message .= "Password: ".$newPassword;
					$newUserMail->sendMail('userrequest@vocwebmanager.com', $result->email, 'User Request Password', $message);
				} else {
					$error = 'Password is not changed.';
				}
			}
		} else {
			$error = 'Password is not changed.';
		}
		return $error;
	}
	
	private function generate_password($passLength){
		$arr = array('a','b','c','d','e','f',
					 'g','h','i','j','k','l',
			         'm','n','o','p','q','r','s',
				     't','u','v','w','x','y','z',
					 'A','B','C','D','E','F',
			         'G','H','I','J','K','L',
				     'M','N','O','P','Q','R','S',
					 'T','U','V','W','X','Y','Z',
		             '1','2','3','4','5','6',
			         '7','8','9','0');
		// generate password
		$pass = "";
		for($i = 0; $i < $passLength; $i++){
			// Вычисляем случайный индекс массива
			$index = rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}
		
		return $pass;
	}
   
}

?>
