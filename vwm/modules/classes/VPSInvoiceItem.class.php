<?php

interface iInvoiceItem {

}


class VPSInvoiceItem extends VPSInvoice implements iInvoiceItem {

	public $id;
	public $one_time_charge;
	public $amount;
	public $invoice_item_type;
	public $module_id;
	public $description;

	const INVOICE_ITEM_TRIAL = 0;
	const INVOICE_ITEM_BILLING = 1;


	public function __construct(db $db, $id = null) {
		$this->db = $db;
	}

	public function setup4trialPeriod() {
		$this->one_time_charge = 0;
		$this->amount = 0;
		$this->invoice_item_type = self::INVOICE_ITEM_TRIAL;
		$this->module_id = null;
		$this->description = null;
	}


	public function setup4paidPeriod(VPSBilling $billing) {
		$this->one_time_charge = $billing->one_time_charge;
		$this->amount = $billing->price;
		$this->invoice_item_type = self::INVOICE_ITEM_BILLING;
		$this->module_id = null;
		$this->description = $billing->description;
	}


	public function save() {
		if ($this->id !== null) {
		} else {
			$this->_insert();
		}
	}


	public static function validate(iInvoiceItem $invoiceItem) {
		return true;

	}


	private function _insert() {
		if (!self::validate($this)) {
			throw new Exception('Invoice Item validation failed');
		}

		$moduleID = (isset($this->module_id)) ? mysql_escape_string($this->module_id) : 'NULL';
		$description = (isset($this->description)) ? "'".mysql_escape_string($this->description)."'" : 'NULL';

		$sql = "INSERT INTO ".TB_VPS_INVOICE_ITEM." (invoice_id, one_time_charge, amount,
					invoice_item_type, module_id, description) VALUES (
					".mysql_escape_string($this->invoice_id).",
					".mysql_escape_string($this->one_time_charge).",
					".mysql_escape_string($this->amount).",
					".mysql_escape_string($this->invoice_item_type).",
					".$moduleID.",
					".$description.")";
		
		if(!$this->db->exec($sql)) {
			$this->db->rollbackTransaction();
			throw new Exception("Failed to insert Invoice Item: ".$sql);
		}

		$this->id = $this->db->getLastInsertedID();
	}


	public static function buildObjectFromDBRow(stdClass $row, iInvoiceItem $invoiceItem) {
		//	bulk assignment for some fields
		$invoiceItemFields = array(
		    'id','one_time_charge', 'invoice_item_type', 'amount', 'module_id',
		    'description'
		);
		foreach($invoiceItemFields as $invoiceItemField) {
			$invoiceItem->$invoiceItemField = $row->$invoiceItemField;
		}

		return $invoiceItem;

	}
}