<?php




class VPSCustomerInvoices {

	/**
	 * @var db
	 */
	private $db;

	/**
	 * @var iCustomer
	 */
	private $customer;

	/**
	 * @var SplObjectStorage
	 */
	private $invoices;

	/**
	 * @var VPSInvoice
	 */
	private $currentInvoice;

	/**
	 * @var VPSInvoice
	 */
	private $nextPeriodInvoice;


	/**
	 * @var VPSInvoice
	 */
	private $trialInvoice;

	/**
	 * @var SplObjectStorage
	 */
	private $dueInvoices;

	/**
	 * @var SplObjectStorage
	 */
	private $paidInvoices;





	public function __construct(db $db, iCustomer $customer) {
		$this->db = $db;
		$this->customer = $customer;
		$this->invoices = new SplObjectStorage();
		$this->dueInvoices = new SplObjectStorage();
		$this->paidInvoices = new SplObjectStorage();
		$this->canceledInvoices = new SplObjectStorage();
		$this->nextPeriodInvoice = false;
		$this->_load();
	}




	public function addInvoice(VPSInvoice $invoice) {
		$today = new DateTime();

		//	check for current invoice
		if ($today >= $invoice->period_start_date
				&& $today <= $invoice->period_end_date) {
			//	current invoice
			$this->setCurrentInvoice($invoice);
		}

		//	check for trial invoice
		$invoiceItems = $invoice->getItems();
		foreach ($invoiceItems as $invoiceItem) {
			if ($invoiceItem->invoice_item_type == VPSInvoiceItem::INVOICE_ITEM_TRIAL) {
				$this->setTrialInvoice($invoice);
				//	do not add trial invoice to invoice list, because it is invisible
				return;
			}
		}

		//	check for nextperiodinvoice
		if ($invoice->period_start_date > new DateTime()) {// && $invoice->status != VPSInvoice::STATUS_CANCELED
			$this->nextPeriodInvoice = $invoice;
		}


		//	check for due
		if ($invoice->status == VPSInvoice::STATUS_DUE) {
			$this->dueInvoices->attach($invoice);
		}

		//	check for paid
		if ($invoice->status == VPSInvoice::STATUS_PAID) {
			$this->paidInvoices->attach($invoice);
		}
		
		//	check for canceled
		if ($invoice->status == VPSInvoice::STATUS_CANCELED) {
			$this->canceledInvoices->attach($invoice);
		}		

		$this->invoices->attach($invoice);
	}



	/**
	 *
	 * @return SplObjectStorage
	 */
	public function getAllInvoices() {
		return $this->invoices;
	}


	/**
	 *
	 * @return SplObjectStorage
	 */
	public function getDueInvoices() {
		return $this->dueInvoices;
	}

	/**
	 *
	 * @return SplObjectStorage
	 */
	public function getPaidInvoices() {
		return $this->paidInvoices;
	}
	
	/**
	 *
	 * @return SplObjectStorage
	 */
	public function getCanceledInvoices() {
		return $this->canceledInvoices;
	}	


	public function setCurrentInvoice(VPSInvoice $currentInvoice) {
		$this->currentInvoice = $currentInvoice;
	}




	public function getCurrentInvoice() {
		return $this->currentInvoice;
	}


	public function getNextPeriodInvoice() {
		return $this->nextPeriodInvoice;
	}




	public function setTrialInvoice(VPSInvoice $trialInvoice) {
		$this->trialInvoice = $trialInvoice;
	}




	public function getTrialInvoice() {
		return $this->trialInvoice;
	}




	private function _load() {
		$sql = "SELECT *
			FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_INVOICE_ITEM . " ii
			WHERE i.invoice_id = ii.invoice_id
			AND i.customer_id = " . mysql_escape_string($this->customer->id);

		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}

		$rows = $this->db->fetch_all();
		$invoiceList = array();
		foreach ($rows as $row) {
			if (!isset($invoiceList[$row->invoice_id])) {
				$invoiceList[$row->invoice_id] = VPSInvoice::buildObjectFromDBRow($row, new VPSInvoice($this->db));
			}

			$invoiceItem = new VPSInvoiceItem($this->db);
			$invoiceItem = VPSInvoiceItem::buildObjectFromDBRow($row, $invoiceItem);
			$invoiceList[$row->invoice_id]->addItem($invoiceItem, false);
		}

		foreach ($invoiceList as $invoice) {
			$this->addInvoice($invoice);
		}
	}
	
	public function printSplObjectStorage($object) {
		foreach ($object as $value) {
			$arr[] = $value;
		}
		return $arr;
	}
}