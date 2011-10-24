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
	private $adress;
	private $city;
	private $county;
	private $state;
	private $state_id;
	private $zip_postal_code;
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
    private $user_id;
    private $status;
    
    const STATUS_NEW = 'new';
    
    public function __construct(db $db) {
        $this->db = $db;
        $this->setDate(new DateTime());
        $this->user_id = $_SESSION['user_id'];
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
	
	public function setAdress($adress) {
        $this->adress = $adress;
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
	
	public function setZipPostalCode($zipPostalCode) {
        $this->zip_postal_code = $zipPostalCode;
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
	
	public function getParentName() {
        return $this->parent_name;
    }
	
	public function getAdress() {
        return $this->adress;
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
	
	public function getZipPostalCode() {
        return $this->zip_postal_code;
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
    
    public function getStatus() {
        return $this->status;
    }
	
	private function validate($category){
		if ($category == 'company'){
			if ($this->name == '' || $this->epa_number == '' || $this->voc_monthly_limit == '' || $this->voc_annual_limit == '' || $this->email == ''){
				$error = "Incorrect data!";
			} else {
				$error = '';
			}
		} elseif ($category == 'facility'){
			if ($this->name == '' || $this->voc_monthly_limit == '' || $this->voc_annual_limit == ''){
				$error = "Incorrect data!";
			} else {
				$error = '';
			}
		}
		
		
		return $error;
	}
	
	public function save($category) {
		$error = $this->validate($category);
		$values = "'".$category."', ";
		$values .= $this->parent_id.", ";
		$values .= "'".$this->name."', ";
		if ($category == 'company'){
			$fields = " (category, parent_id, name, epa, voc_monthly_limit, voc_annual_limit, adress, city, county, zip_code,".
					  " country_id, state, phone, fax, email, contact, title, date, creator_id, status) ";
			$values .= "'".$this->epa_number."', " ;
			$values .= $this->voc_monthly_limit.", ";
			$values .= $this->voc_annual_limit.", ";
			$values .= "'".$this->adress."', ";
			$values .= "'".$this->city."', ";
			$values .= "'".$this->county."', ";
			$values .= $this->zip_postal_code.", ";
			$values .= $this->country_id.", ";
			$values .= "'".$this->state."', ";
			$values .= "'".$this->phone."', ";
			$values .= "'".$this->fax."', ";
			$values .= "'".$this->email."', ";
			$values .= "'".$this->contact."', ";
			$values .= "'".$this->title."', ";
		} elseif ($category == 'facility'){
			$fields = " (category, parent_id, name, voc_monthly_limit, voc_annual_limit, date, creator_id, status) ";
			$values .= $this->voc_monthly_limit.", ";
			$values .= $this->voc_annual_limit.", ";
		}
		$values .= $this->date->getTimestamp().", ";
		$values .= $this->user_id.", ";
		$values .= "'".$this->status."'";
		
		$query = "INSERT INTO ".TB_COMPANY_SETUP_REQUEST.
				 $fields.
				 " VALUES (".
				 $values.
				 ")";
		//echo $query;
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
