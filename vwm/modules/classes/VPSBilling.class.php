<?php
interface iBilling {

}

class VPSBilling implements iBilling {

	/**
	* @var db
	*/
	private $db;

	public $billing_id;
	public $name;
	public $description;
	public $bplimit;
	public $months_count;
	public $type;
	public $defined;

	//	from billing2currency
	public $price;
	public $one_time_charge;

	/**
	*@var iCurrency
	*/
	private $currency;

	const TRIAL_PERIOD_ID = 0;


	public function __construct(db $db, $id = null, iCurrency $currency = null) {
		$this->db = $db;
		if ($id !== null) {
			$this->billing_id = $id;
			if ($currency === null) {
				throw new Exception('Need to add currency into constructor');				
			}
			$this->setCurrency($currency);
			$this->_load();
		}
		
	}


	public function setCurrency(iCurrency $currency) {
		$this->currency = $currency;
	}
	
	
	/**	 	
	 * @return iCurrency
	 */
	public function getCurrency() {
		return $this->currency;
	}


	private function _load() {
		$sql = "SELECT *
				FROM ".TB_VPS_BILLING." b, ".TB_VPS_BILLING2CURRENCY." bc
				WHERE b.billing_id = bc.billing_id
				AND bc.currency_id = ".mysql_escape_string($this->currency->id). "
				AND b.billing_id = ".mysql_escape_string($this->billing_id);
//$sql = "SELECT * FROM ".TB_VPS_BILLING." WHERE billing_id = ".mysql_escape_string($this->billing_id);
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			throw new Exception("No billing plan with ID ".$this->billing_id);
		}

		$row = $this->db->fetch(0);
		foreach($row as $property=>$value) {
		    if (property_exists($this, $property)) {
				$this->$property = $value;
		    }
		}
	}
}

?>
