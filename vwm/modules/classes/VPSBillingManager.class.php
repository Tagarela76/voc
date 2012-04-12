<?php




class VPSBillingManager {

	/**
	 * @var db
	 */
	private $db;

	/**
	 * @var iCurrency
	 */
	private $currency;

	/**
	 * @var SplObjectStorage
	 */
	private $billingPlans;




	public function __construct(db $db, iCurrency $currency) {
		$this->db = $db;
		$this->billingPlans = new SplObjectStorage();
		$this->currency = $currency;
	}




	/**
	 * @return SplObjectStorage
	 */
	public function getAll() {
		if ($this->billingPlans->count() > 0) {
			return $this->billingPlans;
		}

		$sql = "SELECT *
				FROM " . TB_VPS_BILLING . " b, " . TB_VPS_BILLING2CURRENCY . " bc
				WHERE b.billing_id = bc.billing_id
				AND bc.currency_id = " . mysql_escape_string($this->currency->id) . "
				AND b.defined = 0";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}

		$rows = $this->db->fetch_all();
		foreach ($rows as $row) {
			$billing = new VPSBilling($this->db);
			foreach ($row as $property => $value) {
				if (property_exists($billing, $property)) {
					$billing->$property = $value;
				}
			}
			$billing->setCurrency($this->currency);
			$this->addBillingPlan($billing);
		}
				

		return $this->billingPlans;
	}




	public function addBillingPlan(iBilling $billing) {
		$this->billingPlans->attach($billing);
	}

}