<?php

namespace VWM\Apps\Sales\Entity;

class SalesContact {
	
	/**
	 * @var \db xnyo database
	 */
	private $db;

	private $id;
	private $company;
	private $contact;
	private $phone;
	private $fax;
	private $email;
	private $website;
	private $title;
	private $government_agencies;
	private $affiliations;
	private $industry;
	private $comments;
	private $state;
	private $city;
	private $zip_code;
	private $country_id;
	private $state_id;
	private $mail;
	private $cellphone;
	private $type; //Contacts, Government or Affiliations (table contacts_type)

	private $creater_id; //ID sales manager who had created this contact
	private $acc_number; //ID sales manager who had created this contact

	private $paint_supplier; //List of contact's suppliers
	private $paint_system; //contact paint system
	private $jobber; //
	private $shop_type;

	private $country_name; // Inits dynamicly, when calls outside by country_id
	private $state_name; // Inits dynamicly, when calls outside by state_id or state


	private $viewDetailsUrl; // Url for smarty. Builds dynamicly

	public $errors;

    private $features; //services wich used by gyant cliens
    
	const SHOP_SINLGE = 0;
	const SHOP_MULTIPLE = 1;
    
    const VOC_FEATURE = 0;
    const NOX_FEATURE = 1;
    const IIPP_PLAN_FEATURE = 2;
    const HMMP_FEATURE = 3;

	/**
	 * array of meetings
	 * @var MeetingWithContact[]
	 */
	protected $meetings = false;

	public function __construct($db,Array $array = null) {
		$this->db=$db; //control_panel_center

		if(isset($array)) {
			$this->initByArray($array);
		}
	}

	private function initByArray($array) {

		foreach($array as $key => $value) {
			try {
				//Set values trough setter

				$this->__set($key, $value);
			}catch(\Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
	}

	/**
	 * GETTERS
	 */



	public function getCommentsHTML() {
		//$value = nl2br( $this->comments);
		$value = str_replace ("\r\n", "<br/>" ,  $this->comments); //Escaped by any setter
		return $value;
	}

	public function get_viewDetailsUrl() {
		return "admin.php?action=viewDetails&category=contacts&id={$this->id}";
	}

	public function get_viewDetailsUrlSales() {
		return "sales.php?action=viewDetails&category=contacts&id={$this->id}";
	}

	public function get_country_name() {
		if(isset($this->country_id) and !isset($this->country_name)) {
			$country = new \Country($this->db);
			$details = $country->getCountryDetails($this->country_id);
			$this->country_name = $details['country_name'];

		}
		return $this->country_name;
	}

	public function get_state_name() {
		if(!isset($this->state_id) and isset($this->state)) {
			return $this->state;
		} else if (isset($this->state_id)) {
			$state = new \State($this->db);
			$details = $state->getStateDetails($this->state_id);
			return $details['name'];
		}

	}


	/**
	 * SETTERS
	 */
    
    public function set_features($features) {
        $this->features = $features;
    }
    
	public function unsafe_set_value($property,$value) {
		$this->$property = $value;
	}

	private function set_type($value) {
		try {
			$this->type = $value;
		} catch(\Exception $e) {
			throw new \Exception("Contact Type: " . $e->getMessage());
		}
	}

	private function set_mail($value) {
		try {
			$this->mail = $value;
		} catch(\Exception $e) {
			throw new \Exception("Contact Mail: " . $e->getMessage());
		}
	}

	private function set_cellphone($value) {
		try {
			$this->cellphone = $value;
		} catch(\Exception $e) {
			throw new \Exception("Contact Cellphone: " . $e->getMessage());
		}
	}

	private function set_id($value) {
		try {
			$this->id = $value;
		} catch(\Exception $e) {
			throw new \Exception("Id cannot be empty!");
		}
	}

	private function set_company($value) {
		try {
			//$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->company = $value;
		} catch(\Exception $e) {
			$this->errors['company'] = $e->getMessage();
			throw new \Exception("Company cannot be empty!");
		}

		$this->company = $value;
	}

	private function set_contact($value) {

		try {
			$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->contact = $value;
		} catch(\Exception $e) {
			$this->errors["contact"] = $e->getMessage();
			throw new \Exception("contact cannot be empty!");
		}
	}

	private function set_phone($value) {
		try {
			//$this->checkEmpty($value);
			if(isset($value) and !empty($value)) {
				$this->checkPhone($value);
			}
			$value = $this->escapeValue($value);
			$this->phone = $value;
		} catch(\Exception $e) {
			$this->errors["phone"] = $e->getMessage();
			throw new \Exception("phone cannot be empty!");
		}
	}
	private function set_fax($value) {
		try {
			//$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->fax = $value;
		} catch(\Exception $e) {
			$this->errors["fax"] = $e->getMessage();
			throw new \Exception("fax cannot be empty!");
		}
	}
	private function set_email($value) {
		try {
			//$this->checkEmpty($value);
			if(isset($value) and !empty($value)) {
				$this->checkEmail($value);
			}
			$value = $this->escapeValue($value);
			$this->email = $value;
		} catch(\Exception $e) {
			$this->errors["email"] = $e->getMessage();
			throw new \Exception("mail cannot be empty!");
		}
	}
	private function set_website($value) {
		try {
			//$this->checkEmpty($value);

			$value = $this->escapeValue($value);
			$this->website = $value;
		} catch(\Exception $e) {
			$this->website["website"] = $e->getMessage();
			throw new \Exception("website cannot be empty!");
		}
	}
	private function set_title($value) {
		try {
			//$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->title = $value;
		} catch(\Exception $e) {
			$this->errors["title"] = $e->getMessage();
			throw new \Exception("title cannot be empty!");
		}
	}

	private function set_industry($value) {
		try {
			$value = $this->escapeValue($value);
			$this->industry = $value;
		} catch(\Exception $e) {
			$this->errors["industry"] = $e->getMessage();
			throw new \Exception("industry cannot be empty!");
		}
	}
	private function set_comments($value) {
		try {
			$value = $this->escapeValue($value);
			$this->comments = $value;
		} catch(\Exception $e) {
		}
	}
	private function set_state($value) {
		try {
			//$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->state = $value;
		} catch(\Exception $e) {
			$this->errors["state"] = $e->getMessage();
			throw new \Exception("state cannot be empty!");
		}
	}
	private function set_city($value) {
		try {
			//$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->city = $value;
		} catch(\Exception $e) {
			$this->city["city"] = $e->getMessage();
			throw new \Exception("city cannot be empty!");
		}
	}
	private function set_zip_code($value) {
		try {
			//$this->checkEmpty($value);
			$value = $this->escapeValue($value);
			$this->zip_code = $value;
		} catch(\Exception $e) {
			$this->errors["zip_code"] = $e->getMessage();
			throw new \Exception("zip code cannot be empty!");
		}
	}
	private function set_country_id($value) {
		try {

			$this->checkNumber($value);
			$this->country_id = $value;
		} catch(\Exception $e) {
			$this->errors["country_id"] = $e->getMessage();
			throw new \Exception("set country_id: " . $e->getMessage());
		}

	}

	private function set_creater_id($value) {
		try {

			$this->checkNumber($value);
			$this->creater_id = $value;
		} catch(\Exception $e) {
			$this->errors["creater_id"] = $e->getMessage();
			throw new \Exception("set creater_id: " . $e->getMessage());
		}

	}

	private function set_acc_number($value) {
		try {

			//$this->checkNumber($value);
			$this->acc_number = $value;
		} catch(\Exception $e) {
			$this->errors["acc_number"] = $e->getMessage();
			throw new \Exception("set acc_number: " . $e->getMessage());
		}

	}

	private function set_paint_supplier($value) {
		try {

			//$this->checkNumber($value);
			$this->paint_supplier = $value;
		} catch(\Exception $e) {
			$this->errors["paint_supplier"] = $e->getMessage();
			throw new \Exception("set paint_supplier: " . $e->getMessage());
		}

	}

	private function set_paint_system($value) {
		try {

			//$this->checkNumber($value);
			$this->paint_system = $value;
		} catch(\Exception $e) {
			$this->errors["paint_system"] = $e->getMessage();
			throw new \Exception("set paint_system: " . $e->getMessage());
		}

	}

	private function set_jobber($value) {
		try {

			//$this->checkNumber($value);
			$this->jobber = $value;
		} catch(\Exception $e) {
			$this->errors["jobber"] = $e->getMessage();
			throw new \Exception("Jobber cannot be empty! " . $e->getMessage());
		}

	}

	private function set_shop_type($value) {
		$this->shop_type = $value;
	}


	private function set_state_id($value) {
		try{
			$this->checkNumber($value);
			$this->state_id = $value;
		} catch(\Exception $e) {
			$this->errors["state_id"] = $e->getMessage();
			throw new \Exception("set state_id: " . $e->getMessage());
		}

	}

	private function checkNumber($value) {

		$isN = is_numeric($value);
		if( !$isN  ) {
			throw new \Exception("Value ($value) is not numeric!");
		} else if(!isset($value)) {
			throw new \Exception("Value ($value) is not set!");
		}
	}

	private function checkEmpty($value) {
		if(!isset($value) or empty($value)) {
			throw new \Exception("Value is empty");
		} else if(strlen($value) > 255) {
			throw new \Exception("Value is too long (max 255 symbols)");
		}
	}

	private function escapeValue($value) {
		$value = strip_tags($value);
		return $value;
	}

	private function checkEmail($value) {
		$validator = new \Validation($this->db);
		$res = $validator->check_email($value);
		if(!$res) {
			throw new \Exception("Email is invalid");
		}
	}

	private function checkPhone($value) {
		$validator = new \Validation($this->db);
		$res = $validator->check_phone($value);
		if(!$res) {
			throw new \Exception("Phone is invalid");
		}
	}

        public function getContactsList(\Pagination $pagination = null,$filter=' TRUE ',$sort=' ORDER BY id DESC ') {
		$departmentID=mysql_escape_string($departmentID);
		$query = "SELECT * FROM ".TB_CONTACTS." WHERE $filter $sort ";
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}

		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$contacts[] = $usage;
			}

		}
		return $contacts;
	}



	/**
	 *
	 * Overvrite get property if property is not exists or private.
	 * @param unknown_type $name - property name. method call method get_%property_name%, if method does not exists - return property value;
	 */
	public function __get($name) {
        	if(method_exists($this, "get_".$name)) {
        		$methodName = "get_".$name;
        		$res = $this->$methodName();
        		return $res;
        	}
	        else if(property_exists($this,$name)) {
	        	return $this->$name;
	        } else {
	        	return false;
	        }
	}

	/**
	*
	* Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	* @param unknown_type $name - name of property
	* @param unknown_type $value - value to set
	*/
	public function __set($name,$value) {

	    	/*Call setter only if setter exists*/
	        if(method_exists($this, "set_".$name)) {
        		$methodName = "set_".$name;
        		$this->$methodName($value);
        	}
        	/**
        	 * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
        	 * it will craete dynamic property, like usually does PHP
        	*/
	        else if(!property_exists($this,$name)){
	        	/**
	        	 * Disallow add new properties dynamicly (cause of its change type of object to stdObject, i dont want that)
	        	 */
	        	//$this->$name = $value;
	        }
	        /**
	         * property exists and private or protected, do not touch. Keep OOP
	         */
	        else {
	        	//Do nothing
	        }
	 }

	 public function getErrorMessage() {
	 	if(!empty($this->errors)) {
	 		foreach($this->errors as $e) {
	 			$msg .= $e . "<br/>";
	 		}
	 		return $msg;
	 	} else {
	 		return false;
	 	}
	 }

	 /**	  
	  * @return MeetingWithContact[]
	  */
	 public function getMeetings() {
		if ($this->meetings === false) {
			$sql = "SELECT * " .
					"FROM " . MeetingWithContact::TABLE_NAME . " " .
					"WHERE contact_id = {$this->db->sqltext($this->id)} " .
					"ORDER BY meeting_date DESC";
			$this->db->query($sql);
			if($this->db->num_rows() == 0) {
				$this->meetings = array();
			} else {
				$rows = $this->db->fetch_all();
				$this->meetings = array();				
				foreach ($rows as $row) {
					$meeting = new MeetingWithContact($this->db);
					$meeting->initByArray($row);
					$this->meetings[] = $meeting;
				}
			}
		}

		return $this->meetings;
	}


	public function getShopTypeOptions() {
		return array(
			'Single' => self::SHOP_SINLGE,
			'Multiple' => self::SHOP_MULTIPLE,
		);
	}


	public function getShopTypeName() {
		$options = $this->getShopTypeOptions();
		foreach ($options as $key => $option) {
			if($option == $this->shop_type) {
				return $key;
			}
		}
	}
    
    /**
     * Return voc system's features 
     * @return array
     */
    public function getFeaturesOptions() {
		return array(
			'VOC' => self::VOC_FEATURE,
			'NOx' => self::NOX_FEATURE,
            'IIPP plan' => self::IIPP_PLAN_FEATURE,
            'HMMP' => self::HMMP_FEATURE
		);
	}
    
    /**
     * Return features wich used by customers
     * @return boolean|array
     */
    public function getFeaturesName() {     
        if (!is_null($this->features) && $this->features != '') {
           $options = $this->getFeaturesOptions();
            $features = explode(",", $this->features);
            $featuresName = array();
            foreach ($options as $key => $option) {
                if (in_array($option, $features)) {
                    $featuresName[] = $key;
                }
            }
            return $featuresName; 
        } else {
            return false;
        }
	}
}