<?php

class VPSInvoice {

	/**
	* @var db
	*/
	private $db;

	public $invoice_id;
	public $customer_id;
	public $discount = 0;
	public $total = 0;
	/**
	* @var DateTime
	*/
	public $generation_date;
	/**
	* @var DateTime
	*/
	public $suspension_date;
	/**
	* @var DateTime
	*/	
	public $period_start_date;
	/**
	* @var DateTime
	*/	
	public $period_end_date;
	public $status;
	public $suspension_disable;
	public $currency_id;

    /**
    * @var SplObjectStorage invoice items
    */
	private $items;

	/**
	* @var VPSCustomer
	*/
	private $customer;

	/**
	* @var boolean We do not want to calculate discount for more than 1 time :)
	*/
	private $isCheckedForDiscount = false;

	const STATUS_DUE = 1;
	const STATUS_PAID = 2;
	const STATUS_CANCELED = 3;	

	public function __construct(db $db, $id = null) {
		$this->db = $db;
		$this->items = new SplObjectStorage();

		if($id !== null) {
			//	load
			$this->isCheckedForDiscount = true;
			$this->invoice_id = $id;
			$this->_load();
		} else {
			$this->generation_date = new DateTime();
		}
		
	}

    /**
    */
	public function addItem(iInvoiceItem $item, $updateTotal = true) {
		//	update invoice id at item by reference
		//	any time we update invoice id, item's invoice id is updated also
		$item->invoice_id = &$this->invoice_id;

		if ($updateTotal) {
			//	update invoice total
			$this->total += $item->amount + $item->one_time_charge;
		}

		//	attach item to invoice
		$this->items->attach($item);
	}

	/**
	* get invoice items
	* @return SplObjectStorage invoice items 
	*/
	public function getItems() {
		return $this->items;
	}


	public function setCustomer(iCustomer $customer) {
		$this->customer_id = $customer->id;
		$this->customer = $customer;
	}


	public function getCustomer() {
		return (isset($this->customer)) ? $this->customer : false;
	}

	/**
	* @return boolean true if discount already calculated
	*/
	public function isCheckedForDiscount() {
		return $this->isCheckedForDiscount;
	}


	public function save() {
		if ($this->invoice_id !== null) {
			$this->_update();
		} else {
			$this->_insert();
		}
	}


	public static function validate(VPSInvoice $invoice) {
		return true;
	}


	private function _insert() {
	
		if (!$this->isCheckedForDiscount()) {
			$this->applyDiscount();
		}

		if (!self::validate($this)) {
			throw new Exception('Invoice validation failed');
		}

        $suspensionDisable = ($this->suspension_disable) ? 1 : 0;
		$sql = "INSERT INTO ".TB_VPS_INVOICE." (customer_id, discount, total, generation_date, suspension_date,
			period_start_date, period_end_date, status, suspension_disable, currency_id) VALUES (
			".mysql_escape_string($this->customer_id).",
			".mysql_escape_string($this->discount).",
			".mysql_escape_string($this->total).",
			'".mysql_escape_string($this->generation_date->format(MYSQL_DATE_FORMAT))."',
			'".mysql_escape_string($this->suspension_date->format(MYSQL_DATE_FORMAT))."',
			'".mysql_escape_string($this->period_start_date->format(MYSQL_DATE_FORMAT))."',
			'".mysql_escape_string($this->period_end_date->format(MYSQL_DATE_FORMAT))."',
			".mysql_escape_string($this->status).",
			".$suspensionDisable.",
			".mysql_escape_string($this->currency_id).")";

		if(!$this->db->exec($sql)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to insert Invoice: ".$sql);
		}

		//	at this moment invoice_id property updated at all invoice items also
		//	because they are linked by reference at VPSInvoice::addItem()
        $this->invoice_id = $this->db->getLastInsertedID();

        //	now it's time is to save invoice items
		foreach($this->items as $invoiceItem) {
			$invoiceItem->save();
		}
		
	}
	
	private function _update() {
	
		if (!$this->isCheckedForDiscount()) {
			$this->applyDiscount();
		}

		if (!self::validate($this)) {
			throw new Exception('Invoice validation failed');
		}

        $suspensionDisable = ($this->suspension_disable) ? 1 : 0;
    	
		$query = "UPDATE ".TB_VPS_INVOICE." " .
				 "SET 
					customer_id=".mysql_escape_string($this->customer_id).",
					discount=".mysql_escape_string($this->discount).",
					total=".mysql_escape_string($this->total).",
					generation_date='".mysql_escape_string($this->generation_date->format(MYSQL_DATE_FORMAT))."',
					suspension_date='".mysql_escape_string($this->suspension_date->format(MYSQL_DATE_FORMAT))."',
					period_start_date='".mysql_escape_string($this->period_start_date->format(MYSQL_DATE_FORMAT))."', 
					period_end_date='".mysql_escape_string($this->period_end_date->format(MYSQL_DATE_FORMAT))."', 
					status=".mysql_escape_string($this->status).", 
					suspension_disable=".$suspensionDisable.", 
					currency_id=".mysql_escape_string($this->currency_id)."
    			 WHERE invoice_id = ".$this->invoice_id;		

		if(!$this->db->exec($query)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to insert Invoice: ".$sql);
		}

		
	}	
	
	
	private function _load() {
		if (!$this->invoice_id) {
			throw new Exception('Invoice ID is not set');
		}
		$sql = "SELECT * 
				FROM ".TB_VPS_INVOICE."  i, ".TB_VPS_INVOICE_ITEM." ii, ".TB_VPS_CUSTOMER." c 
				LEFT JOIN ".TB_VPS_BILLING." b ON c.billing_id = b.billing_id 
				WHERE i.invoice_id = ii.invoice_id
				AND c.customer_id = i.customer_id
				AND i.invoice_id = ".mysql_real_escape_string($this->invoice_id)."";	
		
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			throw new Exception('Invoice with this id does not exist');
		}
		
		$rows = $this->db->fetch_all();
		//var_dump($rows);
		$invoiceItemPropertyList = array('id','one_time_charge','amount','invoice_item_type','module_id','description');
		foreach ($rows as $row) {
			//	process invoice items
			$invoiceItem = new VPSInvoiceItem($this->db);
			foreach ($invoiceItemPropertyList as $invoiceItemProperty) {
				$invoiceItem->$invoiceItemProperty = $row->$invoiceItemProperty;	
			}
			$this->addItem($invoiceItem);
			
			//	process customer
		}
		foreach($this->items as $itm) {
			var_dump($itm);
		}			
		die();
	}

	
	public function applyDiscount() {
		$customer = $this->getCustomer();
		if (!$customer) {
			throw new Exception('Unable to calculate discount. Please set VPSCustomer.');
		}
		//	calc discount
		$this->discount = round($this->total * $customer->discount / 100, 2);

		//	and update total
		$this->total -= $this->discount;

		$this->isCheckedForDiscount = true;
	}




	public static function buildObjectFromDBRow(stdClass $invoiceRow, VPSInvoice $invoice) {
		//	bulk assignment for some fields
		$invoiceFields = array(
		    'invoice_id','customer_id', 'discount', 'total', 'status',
		    'suspension_disable', 'currency_id'
		);
		foreach($invoiceFields as $invoiceField) {
			$invoice->$invoiceField = $invoiceRow->$invoiceField;
		}

		//	and now create DateTime objects for dates
		$invoiceDateFields = array(
		    'generation_date', 'suspension_date', 'period_start_date', 'period_end_date'
		);
		foreach($invoiceDateFields as $invoiceDateField) {
			$invoice->$invoiceDateField = DateTime::createFromFormat(MYSQL_DATE_FORMAT, $invoiceRow->$invoiceDateField);
		}

		//	invoice is loaded from database. assume it checked for discout
		$invoice->isCheckedForDiscount = true;

		return $invoice;
	}
}