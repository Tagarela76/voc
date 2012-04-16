<?php

class Calendar {
	
	protected $db;
	private $event_id;	
	private $user_id;
	private $title;
	private $description;
	private $url;
	private $email;
	private $category;
	private $day;
	private $month;
	private $year;
	private $approved = self::APPROVED;	
		
	const APPROVED = 1;

	public function __construct(db $db, Array $array = null) {
		$this->db = $db;
		if (isset($array)) {
			$this->initByArray($array);
		}
	}

	protected function initByArray($array) {
		foreach ($array as $key => $value) {
			try {
				$this->__set($key, $value);
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
	}
	
	/**
	 * 
	 * Overvrite get property if property is not exists or private.
	 * @param unknown_type $name - property name. method call method get_%property_name%, if method does not exists - return property value; 
	 */
	public function __get($name) {
		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else if (property_exists($this, $name)) {
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
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/**
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties), 
		 * it will craete dynamic property, like usually does PHP
		 */ else if (!property_exists($this, $name)) {
			/**
			 * Disallow add new properties dynamicly (cause of its change type of object to stdObject, i dont want that)
			 */
			//$this->$name = $value;
		}
		/**
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}	
	

	
	public function save() {

                if ($this->event_id != NULL){

                        $query = "UPDATE calendar_events SET 
						description = '".mysql_escape_string($this->description)."',
						title = '".mysql_escape_string($this->title)."',
						url = '".mysql_escape_string($this->url)."',
						email = '".mysql_escape_string($this->email)."'
						WHERE id = {$this->event_id}";
                }
                else {                        
   
                            $query = "INSERT INTO calendar_events VALUES (NULL,'"
												.mysql_escape_string($this->user_id)."','"												
												.mysql_escape_string($this->title)."','" 
                                                .mysql_escape_string($this->description)."','" 
												.mysql_escape_string($this->url)."','"
												.mysql_escape_string($this->email)."','"
												.mysql_escape_string($this->category)."','"
												.mysql_escape_string($this->day)."','"
												.mysql_escape_string($this->month)."','"
												.mysql_escape_string($this->year)."','"
												.mysql_escape_string($this->approved). "')";
                                               

                }	

		$this->db->query($query);

		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	 
	
	public function get_event_id() {
		return $this->event_id;
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_title() {
		return $this->title;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_url() {
		return $this->url;
	}

	public function get_email() {
		return $this->email;
	}

	public function get_category() {
		return $this->category;
	}

	public function get_day() {
		return $this->day;
	}
	public function get_month() {
		return $this->month;
	}	
	public function get_year() {
		return $this->yesr;
	}	
	public function get_approved() {
		return $this->approved;
	}	
	
	
	public function set_event_id($value) {
		try {
			$this->event_id = $value;
		} catch (Exception $e) {
			throw new Exception("Event ID cannot be empty!" . $e->getMessage());
		}
	}

	public function set_title($value) {
		try {
			$this->title = $value;
		} catch (Exception $e) {
			throw new Exception("Title cannot be empty!" . $e->getMessage());
		}
	}

	public function set_description($value) {
		try {
			$this->description = $value;
		} catch (Exception $e) {
			throw new Exception("Description id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_url($value) {
		try {
			$this->url = $value;
		} catch (Exception $e) {
			throw new Exception("URL id cannot be empty!" . $e->getMessage());
		}
	}

	public function set_category($value) {

		$this->category = $value;
	}

	public function set_day($value) {

		$this->day = $value;
	}

	public function set_month($value) {

		$this->month = $value;
	}
	public function set_year($value) {

		$this->year = $value;
	}	
	public function set_approved($value) {

		$this->approved = $value;
	}
	
	public function set_user_id($value) {

		$this->user_id = $value;
	}
	
	public function set_email($value) {

		$this->email = $value;
	}	
	
	

}

?>
