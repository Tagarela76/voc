<?php

interface iCustomer {

}


class VPSCustomer implements iCustomer {

	public $db;
	public $id;
	public $billing_id;
	public $next_billing_id;
	public $status;
	public $discount;
	public $balance;
	public $currency_id;

	public $name;
	public $phone;

	public $contact;
	public $address;
	public $city;
	public $zip;
	public $fax;
	public $title;
	/**
	* @var DateTime
	*/
	public $creation_date;

    /**
    * @var boolean is user registered at VPS?
    */
	public $registered = false;

	const STATUS_ON = 'on';
	const STATUS_OFF = 'off';

	/**
	 * @var iBilling
	 */
	private $billing;

	/**
	 * @var iCurrency
	 */
	private $currency;

	public function __construct(db $db, $id = false) {
		$this->db = $db;
		if ($id !== false) {
			$this->_load($id);
		}
	}

	public function setBilling(iBilling $billing) {
		$this->billing = $billing;
	}

	/**
	 * @return iBilling or false
	 */
	public function getBilling() {
		return isset($this->billing) ? $this->billing : false;
	}

	public function setCurrency(iCurrency $currency) {
		$this->currency = $currency;
	}

	/**
	 * @return iCurrency
	 */
	public function getCurrency() {
		return isset($this->currency) ? $this->currency : false;
	}


	public function save() {
		if ($this->registered) {
			$this->_update();
		} else {
			$this->_insert();
		}
	}



	private function _load($id) {
		$sql = "SELECT c.company_id, c.name, c.phone, c.email, c.creation_date, c.contact,  c.address, c.city, c.zip, c.fax, c.title, " .
				"vc.customer_id, vc.billing_id, vc.next_billing_id, vc.status, vc.discount, vc.balance, vc.currency_id, " .
				"vb.bplimit, vb.months_count, vb.type, vb.name as 'vbname'," .
				"vcurrency.sign, vcurrency.id, vcurrency.iso," .
				"df.format date_format " .
				"FROM " . TB_COMPANY . " c " .
				"LEFT JOIN " . TB_VPS_CUSTOMER . " vc ON c.company_id = vc.customer_id " .
				"LEFT JOIN " . TB_VPS_BILLING . " vb ON vb.billing_id = vc.billing_id " .
				"LEFT JOIN " . TB_VPS_CURRENCY . " vcurrency ON vc.currency_id = vcurrency.id " .
				"LEFT JOIN " . TB_DATE_FORMAT . " df ON c.date_format_id = df.id " .
			"WHERE c.company_id = ".  mysql_escape_string($id);
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			throw new Exception('No customer with id '.$id);
		}

		$row = $this->db->fetch(0);
		$this->id = $row->company_id;
		$this->name = $row->name;
		$this->phone = $row->phone;
		$this->email = $row->email;

		$this->contact = $row->contact;
		$this->city = $row->city;
		$this->zip = $row->zip;
		$this->fax = $row->fax;
		$this->address= $row->address;
		$this->title= $row->title;
		$this->billing_id = $row->billing_id;
		$this->next_billing_id = $row->next_billing_id;
//		$trialEndDate = DateTime::createFromFormat(MYSQL_DATE_FORMAT, $row->trial_end_date);
//		$this->trial_end_date = $trialEndDate->format(DEFAULT_DATE_FORMAT);
		$this->creation_date = DateTime::createFromFormat(MYSQL_DATE_FORMAT, $row->creation_date);
 
		//  this means that user is registered in VPS
		if ($row->customer_id) {
			$this->registered = true;

			if ($row->billing_id !== null) {
				$vpsBilling = new VPSBilling($this->db);
				$vpsBilling->billing_id = $row->billing_id;
				$vpsBilling->name = $row->vbname;
				$vpsBilling->next_billing_id = $row->next_billing_id;
				$vpsBilling->bplimit = $row->bplimit;
				$vpsBilling->months_count = $row->months_count;
				$vpsBilling->type = $row->type;
				$this->setBilling($vpsBilling);
			}

			$vpsCurrency = new VPSCurrency($this->db);
			$vpsCurrency->sign = $row->sign;
			$vpsCurrency->id = $row->id;
			$vpsCurrency->iso = $row->iso;
			$this->setCurrency($vpsCurrency);

			$this->status = $row->status;
			$this->discount = $row->discount;
			$this->balance = $row->balance;
			$this->currency_id = $row->currency_id;
		}
	}

	private function _insert() {
		//	this can be NULL
		$billingID = (is_null($this->billing_id)) ? 'NULL' : mysql_escape_string($this->billing_id);
		$nextBillingID = (is_null($this->next_billing_id)) ? 'NULL' : mysql_escape_string($this->next_billing_id);

		$sql = "INSERT INTO ".TB_VPS_CUSTOMER." (customer_id, billing_id, next_billing_id, status, discount, balance, currency_id) VALUES
			(".mysql_escape_string($this->id).",
			".$billingID.",
			".$nextBillingID.",
			'".mysql_escape_string($this->status)."',
			".mysql_escape_string($this->discount).",
			".mysql_escape_string($this->balance).",
			".mysql_escape_string($this->currency_id)."
			)";

		if (!$this->db->exec($sql)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to insert VPS customer: ".$sql);
		}
	}
	
	private function _update() {
		//	this can be NULL
		$billingID = (is_null($this->billing_id)) ? 'NULL' : mysql_escape_string($this->billing_id);
		$nextBillingID = (is_null($this->next_billing_id)) ? 'NULL' : mysql_escape_string($this->next_billing_id);

		$sql = "UPDATE ".TB_VPS_CUSTOMER." 
				SET
				customer_id = ".mysql_escape_string($this->id).", 
				billing_id = ".$billingID.", 
				next_billing_id = ".$nextBillingID.", 
				status = '".mysql_escape_string($this->status)."', 
				discount = ".mysql_escape_string($this->discount).", 
				balance = ".mysql_escape_string($this->balance).", 
				currency_id = ".mysql_escape_string($this->currency_id)."
				WHERE customer_id = ".mysql_escape_string($this->id)."";

		if (!$this->db->exec($sql)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to insert VPS customer: ".$sql);
		}
	}	

}

?>
