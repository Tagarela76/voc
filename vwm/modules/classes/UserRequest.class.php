<?php
class UserRequest {
    /**
     *
     * @var db
     */
    private $db;
    
    private $action;
    private $username_id;
    private $new_username;
    private $category_type;
	private $category_id;
    /**
     *
     * @var DateTime
     */
    private $date;
    private $user_id;
    private $status;
    
    const STATUS_NEW = 'new';
    
    public function __construct(db $db) {
        $this->db = $db;
        $this->setDate(new DateTime());
        $this->user_id = $_SESSION['user_id'];
        $this->status = self::STATUS_NEW;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function setDate(DateTime $date) {
        $this->date = $date;
    }
	
	public function setUserID($userID) {
        $this->user_id = $userID;
    }
    
    public function setUserNameID($usernameID) {
        $this->username_id = $usernameID;
    }
    
    public function setNewUserName($new_username) {
        $this->new_username = $new_username; 
    }


    public function setCategoryType($category_type) {
        $this->category_type = $category_type;
    }
    
    public function setStatus($status) {
        $this->status = $status;        
    }
    
    public function setCategoryID($category_id){
        $this->category_id = $category_id;
    }
    
    public function getAction() {
		return $this->action;
    }

    public function getUserNameID() {
		return $this->username_id;		
    }

    public function getNewUserName() {
		return $this->new_username;
    }

    public function getCategoryType() {
	   return $this->category_type;
    }

    public function getDate() {
		return $this->date;
    }

    public function getUserID() {
		return $this->user_id;    
    }
    
    public function getCategoryID(){
		return $this->category_id;
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
        $query = "INSERT INTO ".TB_USER_REQUEST." (action, username_id, new_username, category_type, category_id, date, user_id, status) VALUES (".
                "'".mysql_escape_string($this->action)."', ".
                "".mysql_escape_string($this->username_id).", ".
                "'".mysql_escape_string($this->new_username)."', ".
                "'".mysql_escape_string($this->category_type)."', ".
                "".mysql_escape_string($this->category_id).", ".
                "".mysql_escape_string($this->date->getTimestamp()).", ".
                "".mysql_escape_string($this->user_id).", ".
                "'".mysql_escape_string($this->status)."')";
		$this->db->query($query);
		
		if (mysql_errno() != 0){
			$error = "Error!";
		} else {
			$error = "";
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
