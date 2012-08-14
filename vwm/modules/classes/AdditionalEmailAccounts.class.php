<?php

class AdditionalEmailAccounts {

	/**
	 *
	 * @var int 
	 */
	public $id;
	
	/**
	 *
	 * @var string 
	 */
	public $username;
	
	/**
	 *
	 * @var string 
	 */
	public $email;
	
	/**
	 *
	 * @var int 
	 */
	public $company_id;
	
	/**
	 * db connection
	 * @var db 
	 */
	private $db;


	function __construct(db $db, $additionalEmailAccounts = null) {
		$this->db = $db;

		if (isset($additionalEmailAccounts)) {
			$this->id = $additionalEmailAccounts;
			$this->_load();
		}
	}

	/**
	 * add an additional Email Account 
	 * @return int 
	 */
	public function addAdditionalEmailAccount() {

		$query = "INSERT INTO " . TB_ADDITIONAL_EMAIL_ACCOUNTS . "(username, email, company_id) 
				VALUES ( 
				'" . $this->db->sqltext($this->username) . "'
				, '" . $this->db->sqltext($this->email) . "'	
				, " . $this->db->sqltext($this->company_id) . "	
				)";
		$this->db->query($query); 
		$additionalEmailAccountsId = $this->db->getLastInsertedID();
		$this->id = $additionalEmailAccountsId;
		return $additionalEmailAccountsId;
	}

	/**
	 *
	 * delete an additional Email Account
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_ADDITIONAL_EMAIL_ACCOUNTS . "
				 WHERE id=" . $this->db->sqltext($this->id);
		$this->db->query($sql);
	}

	/**
	 * insert a new additional Email Account (or return FALSE if this email account exist)
	 * @return int 
	 */
	public function save() {
		
		// check if account with current email already exist in additioanl email account table
		$sql = "SELECT * 
				FROM " . TB_ADDITIONAL_EMAIL_ACCOUNTS . "
				 WHERE email='" . $this->db->sqltext($this->email) .
				"'";
		$this->db->query($sql);
		$emailAccount = $this->db->num_rows();
		
		// check if account with current email already exist in user table
		$sql = "SELECT * 
				FROM " . TB_USER . "
				 WHERE email='" . $this->db->sqltext($this->email) .
				"'";
		$this->db->query($sql);
		$emailAccount += $this->db->num_rows();
		if ($emailAccount == 0) {
			$additionalEmailAccountsId = $this->addAdditionalEmailAccount();
		} else {
			$additionalEmailAccountsId = false;
		}
		
		return $additionalEmailAccountsId;
	}

	/**
	 *
	 * Overvrite get property if property is not exists or private.
	 * @param string $name - property name. method call method get_%property_name%, if method does not exists - return property value;
	 */
	public function __get($name) {

		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else {
			return $this->$name;
		}
	}

	/**
	 * Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	 * @param string $name - name of property
	 * @param mixed $value - value to set
	 */
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/*
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
		 * it will craete dynamic property, like usually does PHP
		 */ else if (!property_exists($this, $name)) {
			$this->$name = $value;
		}
		/*
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}

	/**
	 * load one additional email account
	 * @return boolean 
	 */
	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * 
				FROM " . TB_ADDITIONAL_EMAIL_ACCOUNTS . "
				 WHERE id='" . $this->db->sqltext($this->id) .
				"' LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch(0);

		foreach ($rows as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * method that returs all additional email accounts
	 * @param int $companyId
	 * @return boolean|\AdditionalEmailAccounts 
	 */
	public function getAdditionalEmailAccountsByCompany($companyId) {
		
		$query = "SELECT * FROM " . TB_ADDITIONAL_EMAIL_ACCOUNTS . 
				 " WHERE company_id={$this->db->sqltext($companyId)}";
		$this->db->query($query);
		$rows = $this->db->fetch_all_array();

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$additionalEmailAccounts = array();
		foreach ($rows as $row) {
			$additionalEmailAccount = new AdditionalEmailAccounts($this->db);
			foreach ($row as $key => $value) {
				if (property_exists($additionalEmailAccount, $key)) {
					$additionalEmailAccount->$key = $value;
				}
			}
			$additionalEmailAccounts[] = $additionalEmailAccount;
		}
		return $additionalEmailAccounts;
	}

}

?>