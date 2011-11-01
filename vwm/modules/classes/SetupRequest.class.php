<?php
class SetupRequest {
    /**
     *
     * @var db
     */
    private $db;
    
	private $epa_number;
	private $voc_monthly_limit;
	private $voc_annual_limit;
	private $name;
	private $parent_id;
	private $parent_name;
	private $address;
	private $city;
	private $county;
	private $state;
	private $state_id;
	private $zip_code;
	private $country_id;
	private $phone;
	private $fax;
	private $email;
	private $contact;
	private $title;
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
	
	public function setEPANumber($epaNumber) {
        $this->epa_number = $epaNumber;
    }
	
	public function setVOCMonthlyLimit($vocMonthlyLimit) {
        $this->voc_monthly_limit = $vocMonthlyLimit;
    }
	
	public function setVOCAnnualLimit($vocAnnualLimit) {
        $this->voc_annual_limit = $vocAnnualLimit;
    }
	
	public function setName($name) {
        $this->name = $name;
    }
	
	public function setParentID($parentID) {
        $this->parent_id = $parentID;
    }
	
	public function setParentName($parentName) {
        $this->parent_name = $parentName;
    }
	
	public function setAddress($address) {
        $this->address = $address;
    }
		
	public function setCity($city) {
        $this->city = $city;
    }
	
	public function setCounty($county) {
        $this->county = $county;
    }
	
	public function setState($state) {
        $this->state = $state;
    }
	
	public function setStateID($state_id) {
        $this->state_id = $state_id;
    }
	
	public function setZipCode($zipCode) {
        $this->zip_code = $zipCode;
    }
	
	public function setCountryID($country_id) {
        $this->country_id = $country_id;
    }
	
	public function setPhone($phone) {
        $this->phone = $phone;
    }
	
	public function setFax($fax) {
        $this->fax = $fax;
    }
	
	public function setEmail($email) {
        $this->email = $email;
    }
	
	public function setContact($contact) {
        $this->contact = $contact;
    }
	
	public function setTitle($title) {
        $this->title = $title;
    }
	
    public function setDate(DateTime $date) {
        $this->date = $date;
    }
	
	public function setCreaterID($createrID) {
        $this->creater_id = $createrID;
    }
    
    public function setStatus($status) {
        $this->status = $status;        
    }
    
    public function getEPANumber() {
        return $this->epa_number;
    }
	
	public function getVOCMonthlyLimit() {
        return $this->voc_monthly_limit;
    }
	
	public function getVOCAnnualLimit() {
        return $this->voc_annual_limit;
    }
	
	public function getName() {
        return $this->name;
    }
	
	public function getParentID() {
        return $this->parent_id;
    }
	
	public function getParentName() {
        return $this->parent_name;
    }
	
	public function getAddress() {
        return $this->address;
    }
		
	public function getCity() {
        return $this->city;
    }
	
	public function getCounty() {
        return $this->county;
    }
	
	public function getState() {
        return $this->state;
    }
	
	public function getStateID() {
        return $this->state_id;
    }
	
	public function getZipCode() {
        return $this->zip_code;
    }
	
	public function getCountryID() {
        return $this->country_id;
    }
	
	public function getPhone() {
        return $this->phone;
    }
	
	public function getFax() {
        return $this->fax;
    }
	
	public function getEmail() {
        return $this->email;
    }
	
	public function getContact() {
        return $this->contact;
    }
	
	public function getTitle() {
        return $this->title;
    }
	
    public function getDate() {
        return $this->date;
    }
	
	public function getCreaterID() {
        return $this->creater_id;
    }
    
    public function getStatus() {
        return $this->status;
    }
	
	private function validate($category){
		if ($category == 'company'){
			if ($this->name == '' || $this->email == '' || (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $this->email))){
				$error = "Incorrect data!";
			} else {
				$error = '';
			}
		} elseif ($category == 'facility'){
			if ($this->name == '' || $this->epa_number == '' || $this->voc_monthly_limit == '' || $this->voc_annual_limit == '' || $this->email == '' || (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $this->email))){
				$error = "Incorrect data!";
			} else {
				$error = '';
			}
		} elseif ($category == 'department'){
			if ($this->name == '' || $this->voc_monthly_limit == '' || $this->voc_annual_limit == '' || $this->email == '' || (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $this->email))){
				$error = "Incorrect data!";
			} else {
				$error = '';
			}
		}
		
		return $error;
	}
	
	public function update($requestID, $addComments = ''){
		$query = "UPDATE ".TB_COMPANY_SETUP_REQUEST." SET status='".mysql_escape_string($this->status)."' WHERE id=".$requestID;
		$this->db->query($query);
		if (mysql_errno() != 0){
			$error = "Error!";
		} else {
			$error = "";
		}
		
		return $error;	
	}
	
	public function addNewCompany($requestID, $addComments = ''){
		return $error = "Error!";
	}
	
	public function addNewFacility($requestID, $addComments = ''){
		return $error = "Error!";
	}
	
	public function addNewDepartment($requestID, $addComments = ''){
		return $error = "Error!";
	}
	
	public function denySetupRequest($requestID, $addComments = ''){
		$this->db->query("SELECT * FROM ".TB_COMPANY_SETUP_REQUEST." WHERE id=".$requestID);
		$data = $this->db->fetch(0);
		$newMail = new EMail();
		switch ($data->category){
			case 'company':
				$message = "Your request to add new company is denied.\n";
				break;
			case 'facility':
				$message = "Your request to add new facility is denied.\n";
				break;
			case 'department':
				$message = "Your request to add new department is denied.\n";
				break;
		}
		$message .= $addComments;
		$newMail->sendMail('newuserrequest@vocwebmanager.com', $data->email, 'Setup Request', $message);
	}

	public function save($category) {
		$error = $this->validate($category);
		$values = "'".$category."', ";
		$values .= $this->parent_id.", ";
		$values .= "'".$this->name."', ";
		if ($category == 'company'){
			$fields = " (category, parent_id, name, address, city, county, zip_code,".
					  " country_id, state, state_id, phone, fax, email, contact, title, date, creater_id, status) ";
			$values .= "'".$this->address."', ";
			$values .= "'".$this->city."', ";
			$values .= "'".$this->county."', ";
			if ($this->zip_code){
				if (preg_match("/^[0-9]+$/",  $this->zip_code)){
					$values .= $this->zip_code.", ";
				} else {
					$values .= "0, ";
				}
			} else {
				$values .= "0, ";
			}
			$values .= $this->country_id.", ";
			$values .= "'".$this->state."', ";
			if ($this->state_id){
				$values .= $this->state_id.", ";
			} else {
				$values .= "NULL, ";
			}	
			$values .= "'".$this->phone."', ";
			$values .= "'".$this->fax."', ";
			$values .= "'".$this->email."', ";
			$values .= "'".$this->contact."', ";
			$values .= "'".$this->title."', ";
		} elseif ($category == 'facility') {
			$fields = " (category, parent_id, name, epa, voc_monthly_limit, voc_annual_limit, adress, city, county, zip_code,".
					  " country_id, state, state_id, phone, fax, email, contact, title, date, creater_id, status) ";
			$values .= "'".$this->epa_number."', " ;
			$values .= $this->voc_monthly_limit.", ";
			$values .= $this->voc_annual_limit.", ";
			$values .= "'".$this->address."', ";
			$values .= "'".$this->city."', ";
			$values .= "'".$this->county."', ";
			if ($this->zip_code){
				if (preg_match("/^[0-9]+$/",  $this->zip_code)){
					$values .= $this->zip_code.", ";
				} else {
					$values .= "0, ";
				}
			} else {
				$values .= "0, ";
			}
			$values .= $this->country_id.", ";
			$values .= "'".$this->state."', ";
			if ($this->state_id){
				$values .= $this->state_id.", ";
			} else {
				$values .= "NULL, ";
			}	
			$values .= "'".$this->phone."', ";
			$values .= "'".$this->fax."', ";
			$values .= "'".$this->email."', ";
			$values .= "'".$this->contact."', ";
			$values .= "'".$this->title."', ";
		} elseif ($category == 'department'){
			$fields = " (category, parent_id, name, voc_monthly_limit, voc_annual_limit, email, date, creater_id, status) ";
			$values .= $this->voc_monthly_limit.", ";
			$values .= $this->voc_annual_limit.", ";
			$values .= "'".$this->email."', ";
		}
		$values .= $this->date->getTimestamp().", ";
		if ($category == 'company') {
			$values .= "NULL, ";	
		} else {
			$values .= $this->creater_id.", ";
		}
		$values .= "'".$this->status."'";
		
		$query = "INSERT INTO ".TB_COMPANY_SETUP_REQUEST." ".$fields." VALUES (".$values.")";
		
		if ($error == ''){
			$this->db->query($query);
		}
		
		if (mysql_errno() != 0){
			$error = "Incorrect data!";
		}
		
		return $error;
    }
}

?>
