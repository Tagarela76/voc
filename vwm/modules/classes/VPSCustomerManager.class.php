<?php

class VPSCustomerManager {

	/**
	 * @var db
	 */
	private $db;
	public $registeredCustomersCount = 0;
	public $unRegisteredCustomersCount = 0;

	public function __construct(db $db) {
		$this->db = $db;
	}

	public function getCustomerList() {
		$query = "SELECT c.company_id, c.name, c.phone, c.email, c.creation_date, c.contact, " .
				"vc.customer_id, vc.billing_id, vc.next_billing_id, vc.status, vc.discount, vc.balance, vc.currency_id, " .
				"vb.bplimit, vb.months_count, vb.type, vb.name as nameb," .
				"vcurrency.sign, " .
				"df.format date_format " .
				"FROM " . TB_COMPANY . " c " .
				"LEFT JOIN " . TB_VPS_CUSTOMER . " vc ON c.company_id = vc.customer_id " .
				"LEFT JOIN " . TB_VPS_BILLING . " vb ON vb.billing_id = vc.billing_id " .
				"LEFT JOIN " . TB_VPS_CURRENCY . " vcurrency ON vc.currency_id = vcurrency.id " .
				"LEFT JOIN " . TB_DATE_FORMAT . " df ON c.date_format_id = df.id ";
		$this->db->query($query);
//echo $query;
		if ($this->db->num_rows() == 0) {
			return false;
		}

		$rows = $this->db->fetch_all();
		$customers = array();
		foreach ($rows as $row) {
			$customer = new VPSCustomer($this->db);

			$customer->id = $row->company_id;
			$customer->name = $row->name;
			$customer->phone = $row->phone;
			$customer->email = $row->email;
			$customer->contact = $row->contact;		
			$customer->billing_id = $row->billing_id;
			$customer->next_billing_id = $row->next_billing_id;
			$customer->creation_date = DateTime::createFromFormat(MYSQL_DATE_FORMAT, $row->creation_date);

			//  this means that user is registered in VPS
			if ($row->customer_id) {
				$customer->registered = true;

				if ($row->billing_id !== null) {
					$vpsBilling = new VPSBilling($this->db);
					$vpsBilling->billing_id = $row->billing_id;
					$vpsBilling->bplimit = $row->bplimit;
					$vpsBilling->months_count = $row->months_count;
					$vpsBilling->type = $row->type;
					$vpsBilling->name = $row->nameb;
					$customer->setBilling($vpsBilling);
				}

				$vpsCurrency = new VPSCurrency($this->db);
				$vpsCurrency->sign = $row->sign;
				$customer->setCurrency($vpsCurrency);

				$customer->status = $row->status;
				$customer->discount = $row->discount;
				$customer->balance = $row->balance;
				$customer->currency_id = $row->currency_id;
				$customer->bplimit_current = $row->bplimit_current;

				$this->registeredCustomersCount++;
			} else {
				$this->unRegisteredCustomersCount++;
			}

			$customers[] = $customer;
		}
		return $customers;
	}

}

?>
