<?php

class Invoice {

	public $totalPaid = 0;
	public $totalDue = 0;
	public $totalCanceled = 0;
	public $currentDate;
	private $db;
	private $payment;

	function __construct($db) {
		$this->db = $db;
		$this->currentDate = date("Y-m-d");
	}

	function setPayment(Payment $payment) {
		$this->payment = $payment;
	}

	/**
	 * Creates multi invoice for billing and modules with same month_count
	 * also create invoices for other chosen modules and applies all modules
	 * @param int $customerID
	 * @param date $periodStartDate
	 * @param int $billingID
	 * @param array() $multiInvoiceData
	 * @param status = due. if not due = autopay is disabled
	 */
	public function createMultiInvoiceForNewCustomer($customerID, DateTime $periodStartDate, $billingID, $multiInvoiceData, $status = 'due') {

		$billing = new Billing($this->db);
		$currencyDetails = $billing->getCurrencyByCustomer($customerID);

		$billingPlanDetails = $billing->getBillingPlanDetails($billingID, $customerID, $currencyDetails['id']);
		$amount = $billingPlanDetails['price'];

		$suspesionDate = clone $periodStartDate;
		$billingInfo = "'Sources: " . $billingPlanDetails['bplimit'] . "," .
				" Months: " . $billingPlanDetails['months_count'] .
				", Type: " . $billingPlanDetails['type'] . "'";

		$oneTimeCharge = $billingPlanDetails['one_time_charge'];

		//Calculate total sum with modules
		$totalSum = 0;

		foreach ($multiInvoiceData['appliedModules'] as $module) {
			$totalSum += $module['price'];
		}
		$totalSum += $amount;

		$discount = $this->calculateDiscount($oneTimeCharge, $totalSum, $customerID, 0);

		$total = $this->calculateTotal($oneTimeCharge, $totalSum, $discount);



		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		if (!$currencyDetails) {
			throw new Exception('No currency for customer ' . $customerID);
		}

		$periodEndDate = clone $periodStartDate;
		$periodEndDate->add(new DateInterval("P" . intval($billingPlanDetails['months_count']) . "M")); //+ month count

		$invoiceData = array(
			'customerID' => $customerID,
			'oneTimeCharge' => $oneTimeCharge,
			'amount' => $amount,
			'discount' => $discount,
			'total' => $total,
			'paid' => 0, //always $0.00 when creating new invoice
			'due' => $total, //due is always == total when creating new invoice
			'generationDate' => mktime(),
			'suspensionDate' => $suspesionDate->getTimestamp(),
			'periodStartDate' => $periodStartDate->getTimestamp(),
			'periodEndDate' => $periodEndDate->getTimestamp(), //"'".date('Y-m-d',strtotime($periodStartDate." + ".$billingPlanDetails['months_count']." months"))."'",
			'billingInfo' => $billingInfo,
			'limitInfo' => "NULL",
			'customInfo' => "NULL",
			'module_id' => "NULL",
			'status' => $status,
			'suspensionDisable' => 1,
			'currency_id' => $currencyDetails['id']
		);
		//var_dump($invoiceData);

		if ($status != "due") {
			$autopay = false;
		} else {
			$autopay = true;
		}
		$invoiceID = $this->insertInvoice($invoiceData, $autopay); // Create Invoice items For Billing

		if (!isset($invoiceID) or $invoiceID == 0) {
			die("invoiceID is 0");
			return;
		}

		$modulesToBilling = array();

		foreach ($multiInvoiceData['appliedModules'] as $module) {
			$moduleData = array(
				'invoiceID' => $invoiceID,
				'oneTimeCharge' => 0,
				'amount' => $module['price'],
				'billingInfo' => "NULL",
				'limitInfo' => "NULL",
				'customInfo' => "NULL",
				'module_id' => $module['module_id'],
				'currency_id' => $currencyDetails['id']
			);

			$this->insertMultiInvoiceItem($moduleData);

			$modulesToBilling [] = $module['id'];
		}
		foreach ($multiInvoiceData['not_approach_modules'] as $module) { //Create Invoice for each module
			$amount = $module['price'];
			$oneTimeCharge = 0;

			$config = $this->loadConfig();

			$suspensionDate = clone $periodStartDate; //date('Y-m-d',strtotime($periodStartDate." + ".$config['limit_suspension_period']." days"));
			$suspensionDate->add(new DateInterval("P" . intval($config['limit_suspension_period']) . "D"));

			$discount = $this->calculateDiscount($oneTimeCharge, $amount, $customerID, 0); // $backToCustomer = 0

			$total = $this->calculateTotal($oneTimeCharge, $amount, $discount);
			$status = "due";

			$periodEndDate = new DateTime();
			$periodEndDate->add(new DateInterval("P{$module['month_count']}M"));
			$periodEndDate->sub(new DateInterval("P1D"));

			$invoiceData = array(
				'customerID' => $customerID,
				'oneTimeCharge' => $oneTimeCharge,
				'amount' => $amount,
				'discount' => $discount,
				'total' => $amount,
				'paid' => 0, //always $0.00 when creating new invoice
				'due' => $total, //due is always == total when creating new invoice
				'generationDate' => mktime(),
				'suspensionDate' => $suspensionDate->getTimestamp(),
				'periodStartDate' => $periodStartDate->getTimestamp(),
				'periodEndDate' => $periodEndDate->getTimestamp(), //end_date = start_date + months - 1 day
				'billingInfo' => "NULL",
				'limitInfo' => "NULL",
				'customInfo' => "NULL",
				'module_id' => $module['module_id'],
				'status' => $status,
				'suspensionDisable' => 0,
				'currency_id' => $currencyDetails['id']
			);

			$this->insertInvoice($invoiceData, $autopay);

			$modulesToBilling [] = $module['id'];
		}

		//now we should add all modules to billing

		$billing->insertModuleBillingPlan($customerID, $periodStartDate, $modulesToBilling);

		return $invoiceID;
	}

	/**
	 * function createInvoiceForModule - create invoice(only invoice, not aplied modules!) for modules
	 * @param customerID
	 * @param startDate
	 * @param moduleBillingPlanID - can be an array and an integer
	 */
	public function createInvoiceForModule($customerID, DateTime $startDate, $moduleBillingPlanID, $status = "due") {
		//if $moduleBillingPlanID is a number(only one id) its should be in array too

		$billing = new Billing($this->db);
		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		if (!$currencyDetails) {
			throw new Exception('No currency for customer ' . $customerID);
		}

		if (!is_array($moduleBillingPlanID)) {
			$moduleBillingPlanID = array($moduleBillingPlanID);
		}



		$billingPlanDetails = $billing->getModuleBillingPlans($moduleBillingPlanID, $currencyDetails['id']);

		if (!$billingPlanDetails) {
			//	no such billings
			return false;
		}

		//calculate total for invoice info
		$total = 0;
		foreach ($billingPlanDetails as $billingPlan) {
			$total+= $billingPlan['price'];
		}

		//cut from all plans one - to add with insertInvoice function
		$billingPlanDetailsForMainInvoice = array_pop($billingPlanDetails);

		if ($billingPlanDetailsForMainInvoice) {
			$backToCustomer = 0;
			$amount = 0;

			$amount = $billingPlanDetailsForMainInvoice['price'];
			$oneTimeCharge = 0;

			$config = $this->loadConfig();

			$suspensionDate = new DateTime();
			$suspensionDate->add(new DateInterval("P{$config['limit_suspension_period']}D"));

			//$suspensionDate = date('Y-m-d',strtotime($startDate." + ".$config['limit_suspension_period']." days"));

			$discount = $this->calculateDiscount($oneTimeCharge, $total, $customerID, 0); // $backToCustomer = 0

			$total = $this->calculateTotal($oneTimeCharge, $total, $discount);

			$period_end_date = clone $startDate;
			$period_end_date->add(new DateInterval("P{$billingPlanDetailsForMainInvoice['month_count']}M"));
			$period_end_date->sub("P1D");

			$invoiceData = array(
				'customerID' => $customerID,
				'oneTimeCharge' => $oneTimeCharge,
				'amount' => $amount,
				'discount' => $discount,
				'total' => $total,
				'paid' => 0, //always $0.00 when creating new invoice
				'due' => $total, //due is always == total when creating new invoice
				'generationDate' => mktime(),
				'suspensionDate' => $suspensionDate->getTimestamp(),
				'periodStartDate' => $startDate->getTimestamp(),
				'periodEndDate' => $period_end_date->getTimestamp(), // "'".date('Y-m-d',strtotime($startDate." + ".$billingPlanDetailsForMainInvoice['month_count']." months") - 86400)."'" //end_date = start_date + months - 1 day
				'billingInfo' => "NULL",
				'limitInfo' => "NULL",
				'customInfo' => "NULL",
				'module_id' => $billingPlanDetailsForMainInvoice['module_id'],
				'status' => $status,
				'suspensionDisable' => 0,
				'currency_id' => $currencyDetails['id']
			);


			$invoiceID = $this->insertInvoice($invoiceData);


			if (!isset($invoiceID) or $invoiceID == 0) {
				throw new Exception("invoiceid is null");
				die("invoiceID is 0");
				return;
			}
			//now we can insert other multi invoice items(from $billingPlanDetails)
			foreach ($billingPlanDetails as $module) {
				$moduleData = array(
					'invoiceID' => $invoiceID,
					'oneTimeCharge' => 0,
					'amount' => $module['price'],
					'billingInfo' => "NULL",
					'limitInfo' => "NULL",
					'customInfo' => "NULL",
					'module_id' => $module['module_id']
				);
				$this->insertMultiInvoiceItem($moduleData);
			}


			return $invoiceID;
		} else {
			//	no such billings
			return false;
		}
	}

	public function createInvoiceForModuleOld($customerID, $startDate, $moduleBillingPlanID) {

		$billing = new Billing($this->db);
		$billingPlanDetails = $billing->getModuleBillingPlans($moduleBillingPlanID);
		$billingPlanDetails = $billingPlanDetails[0];

		if ($billingPlanDetails) {
			$currentInvoice = $this->getCurrentInvoiceForModule($customerID, $moduleBillingPlanID);
			$backToCustomer = 0;
			$amount = 0;

			if ($currentInvoice) {
				//cancel invoice for future period if exist
				$invoiceForFutureBP = $this->getModuleInvoiceForFuturePeriod($customerID, $moduleBillingPlanID);

				if ($invoiceForFutureBP) {
					$this->cancelInvoice($invoiceForFutureBP['invoiceID']);
				}

				$amount = $billingPlanDetails['price'];

				if (strtolower($currentInvoice['status']) == 'paid') {
					$backToCustomer = $this->partialRefund($currentInvoice);
					$this->manualBalanceChange($customerID, '+', $backToCustomer);
				}

				$suspesionDate = (strtotime($startDate . " + " . $billingPlanDetails['month_count'] . " months") > strtotime($currentInvoice['periodEndDate'])) ? $currentInvoice['periodEndDate'] : date('Y-m-d', strtotime($startDate . " + " . $billingPlanDetails['month_count'] . " months"));
				$billingInfo = "'Months: " . $billingPlanDetails['month_count'] . ", Type: " . $billingPlanDetails['type'] . ", ASAP'";

				//trial period
			} else {
				//cancel invoice for future period
				$invoiceForFutureBP = $this->getModuleInvoiceWhenTrialPeriod($customerID, $moduleBillingPlanID);

				if ($invoiceForFutureBP) {
					$this->cancelInvoice($invoiceForFutureBP['invoiceID']);
				}

				$amount = $billingPlanDetails['price'];

				$suspesionDate = $invoiceForFutureBP['suspensionDate'];
				$periodStartDate = $invoiceForFutureBP['periodStartDate'];
				$billingInfo = "' Months: " . $billingPlanDetails['month_count'] . ", Type: " . $billingPlanDetails['type'] . ", ASAP'";
			}

			$oneTimeCharge = 0;
			$discount = $this->calculateDiscount($oneTimeCharge, $amount, $customerID, 0); // $backToCustomer = 0

			$total = $this->calculateTotal($oneTimeCharge, $amount, $discount);
			$status = "due";

			$currencyDetails = $billing->getCurrencyByCustomer($customerID);
			if (!$currencyDetails) {
				throw new Exception('No currency for customer ' . $customerID);
			}

			$invoiceData = array(
				'customerID' => $customerID,
				'oneTimeCharge' => $oneTimeCharge,
				'amount' => $amount,
				'discount' => $discount,
				'total' => $total,
				'paid' => 0, //always $0.00 when creating new invoice
				'due' => $total, //due is always == total when creating new invoice
				'generationDate' => date('Y-m-d'),
				'suspensionDate' => $suspesionDate,
				'periodStartDate' => "'" . $startDate . "'",
				'periodEndDate' => "'" . date('Y-m-d', strtotime($startDate . " + " . $billingPlanDetails['month_count'] . " months")) . "'",
				'billingInfo' => "NULL",
				'limitInfo' => "NULL",
				'customInfo' => "NULL",
				'module_id' => $billingPlanDetails['module_id'],
				'status' => $status,
				'suspensionDisable' => 1,
				'currency_id' => $currencyDetails['id']
			);

			$this->insertInvoice($invoiceData);

			return $invoiceData;
		} else {
			return false;
		}
	}

	public function createInvoiceItemForBilling($customerID, $periodStartDate, $billingID, $asap = false) { //DEPRECATED
		$invoice = getInvoiceForFuturePeriod($customerID);

		if (!$invoice) {
			return false;
		}

		$billing = new Billing($this->db);
		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		$billingPlanDetails = $billing->getBillingPlanDetails($billingID, $customerID, $currencyDetails['id']);

		$billingInfo = "'Sources: " . $billingPlanDetails['bplimit'] . ", Months: " . $billingPlanDetails['months_count'] . ", Type: " . $billingPlanDetails['type'] . ", ASAP'";

		$data['invoiceID'] = $invoice['invoice_id'];
		$data['one_time_charge'] = $billingPlanDetails['one_time_charge'];
		$data['amount'] = $billingPlanDetails['price'];
		$data['billing_info'] = $billingInfo;
		$data['limit_info'] = 'NULL';
		$data['custom_info'] = 'NULL';
		$data['module_id'] = 'NULL';
	}

	public function createInvoiceForBilling($customerID, DateTime $periodStartDate, $billingID, $asap = false) {

		$billing = new Billing($this->db);
		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		$billingPlanDetails = $billing->getBillingPlanDetails($billingID, $customerID, $currencyDetails['id']);



		$currentInvoice = $this->getCurrentInvoice($customerID);
		$backToCustomer = 0;
		$amount = 0;

		if ($currentInvoice) {

			//cancel invoice for future period if exist
			$invoiceForFutureBP = $this->getInvoiceForFuturePeriod($customerID);
			if ($invoiceForFutureBP) {
				$this->cancelInvoice($invoiceForFutureBP['invoiceID']);
			}


			$amount = $billingPlanDetails['price'];
			if (!$asap) {
				$suspesionDate = clone $periodStartDate;
				$billingInfo = "'Sources: " . $billingPlanDetails['bplimit'] . ", Months: " . $billingPlanDetails['months_count'] . ", Type: " . $billingPlanDetails['type'] . "'";
			} else {
				//partial refund

				if (!$invoiceForFutureBP) {
					$backToCustomer = $this->partialRefund($currentInvoice);
				} else {
					$backToCustomer = 0.0;
				}

				$newEndDateTimestamp = clone $periodStartDate;
				$newEndDateTimestamp->add(new DateInterval("P" . intval($billingPlanDetails['months_count']) . "M")); //Add month count

				$currentInvoicePeriodEndDate = new DateTime();
				$currentInvoicePeriodEndDate->setTimestamp($currentInvoice['periodEndDate']);


				if ($newEndDateTimestamp->diff($currentInvoicePeriodEndDate)->invert == true) {

					$suspesionDate = $currentInvoicePeriodEndDate;
				} else {

					$suspesionDate = clone $newEndDateTimestamp; //date('Y-m-d', $newEndDateTimestamp);
				}

				//end createInvoiceForBilling
				$billingInfo = "'Sources: " . $billingPlanDetails['bplimit'] . ", Months: " . $billingPlanDetails['months_count'] . ", Type: " . $billingPlanDetails['type'] . ", ASAP'";
			}

			//trial period
		} else {
			//cancel invoice for future period

			$invoiceForFutureBP = $this->getInvoiceWhenTrialPeriod($customerID);

			if ($invoiceForFutureBP) {
				$this->cancelInvoice($invoiceForFutureBP['invoiceID']);
			}

			$amount = $billingPlanDetails['price'];
			if (!$asap) {
				$suspesionDate = clone $periodStartDate;
				$billingInfo = "'Sources: " . $billingPlanDetails['bplimit'] . ", Months: " . $billingPlanDetails['months_count'] . ", Type: " . $billingPlanDetails['type'] . "'";
			} else {

				if (!$invoiceForFutureBP) {
					/*
					 * User is changing asap billing plan. This means he had other billing plan earlier - and he had at list one invoice.
					 * If no current invoice and no invoice for future - billing issue.
					 */
					throw new Exception('Customer ' . $customerID . ' has no current and future invoices - and this is impossible. ' .
							'Please report denis.nt@kttsoft.com. Today is ' . $this->currentDate . '.');
				}

				$suspesionDate = DateTime::setTimestamp($invoiceForFutureBP['suspensionDate']);
				$periodStartDate = DateTime::setTimestamp($invoiceForFutureBP['periodStartDate']);
				$billingInfo = "'Sources: " . $billingPlanDetails['bplimit'] . ", Months: " . $billingPlanDetails['months_count'] . ", Type: " . $billingPlanDetails['type'] . ", ASAP'";
			}
		}

		$balance = $this->getBalance($customerID);


		$oneTimeCharge = $billingPlanDetails['one_time_charge'];
		$discount = $this->calculateDiscount($oneTimeCharge, $amount, $customerID, $backToCustomer);

		$total = $this->calculateTotal($oneTimeCharge, $amount, $discount);
		$status = "due";


		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		if (!$currencyDetails) {
			throw new Exception('No currency for customer ' . $customerID);
		}

		$periodEndDate = clone $periodStartDate;
		$periodEndDate->add(new DateInterval("P" . intval($billingPlanDetails['months_count']) . "M"));
		$invoiceData = array(
			'customerID' => $customerID,
			'oneTimeCharge' => $oneTimeCharge,
			'amount' => $amount,
			'discount' => $discount,
			'total' => $total,
			'paid' => 0, //always $0.00 when creating new invoice
			'due' => $total, //due is always == total when creating new invoice
			'generationDate' => mktime(),
			'suspensionDate' => $suspesionDate->getTimestamp(),
			'periodStartDate' => $periodStartDate->getTimestamp(),
			'periodEndDate' => $periodEndDate->getTimestamp(), //"'".date('Y-m-d',strtotime($periodStartDate." + ".$billingPlanDetails['months_count']." months"))."'",
			'billingInfo' => $billingInfo,
			'limitInfo' => "NULL",
			'customInfo' => "NULL",
			'module_id' => "NULL",
			'status' => $status,
			'suspensionDisable' => 1,
			'currency_id' => $currencyDetails['id']
		);


		$this->insertInvoice($invoiceData);


		return $invoiceData;
	}

	public function getDatesForCustomerList($arrayOfCustomerIDs) {
		if (isset($arrayOfCustomerIDs) and !is_array($arrayOfCustomerIDs) and (count($arrayOfCustomerIDs)) == 0) {
			return false;
		}
		$customers = implode(", ", $arrayOfCustomerIDs);
		$query = "SELECT customer_id,period_start_date, period_end_date, suspension_date, status " .
				"FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_INVOICE_ITEM . " ii " .
				"WHERE i.invoice_id = ii.invoice_id " .
				"AND i.customer_id IN ($customers) " .
				"AND i.status != 'CANCELED' " .
				"AND ii.billing_info IS NOT NULL " .
				"ORDER BY i.customer_id ASC, i.suspension_date ASC"; //by suspension_date ASC(not DESC) because of in result erlier values replased by last => needed invoice should be the last

		$this->db->query($query);

		$data = $this->db->fetch_all_array();

		$result = array();

		foreach ($data as $row) {
			$result[$row['customer_id']] = array(
				'period_start_date' => $row['period_start_date'],
				'period_end_date' => $row['period_end_date'],
				'suspension_date' => $row['suspension_date'],
				'status' => strtoupper($row['status'])
			);
		}
		return $result;
	}

	public function createInvoiceForLimit($customerID, $amount, $limitInfo) {

		$config = $this->loadConfig();

		$oneTimeCharge = 0;
		$suspensionDate = date('Y-m-d', strtotime(date('Y-m-d') . " + " . $config['limit_suspension_period'] . " days"));

		$discount = $this->calculateDiscount($oneTimeCharge, $amount, $customerID);
		$total = $this->calculateTotal($oneTimeCharge, $amount, $discount);

		$billing = new Billing($this->db);
		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		if (!$currencyDetails) {
			throw new Exception('No currency for customer ' . $customerID);
		}

		$invoiceData = array(
			'customerID' => $customerID,
			'oneTimeCharge' => $oneTimeCharge,
			'amount' => $amount,
			'discount' => $discount,
			'total' => $total,
			'paid' => 0, //always $0.00 when creating new invoice
			'due' => $total, //due is always == total when creating new invoice
			'generationDate' => $this->currentDate,
			'suspensionDate' => $suspensionDate,
			'periodStartDate' => "NULL",
			'periodEndDate' => "NULL",
			'billingInfo' => "NULL",
			'limitInfo' => $limitInfo,
			'customInfo' => "NULL",
			'module_id' => "NULL",
			'status' => "due",
			'suspensionDisable' => 0,
			'currency_id' => $currencyDetails['id']
		);

		$this->insertInvoice($invoiceData);

		return $invoiceData;
	}

	/**
	 *
	 * @param $customerID
	 * @param $amount
	 * @param $suspensionDate
	 * @param $suspensionDisable
	 * @param $customInfo
	 * @param $status
	 * @return unknown_type
	 */
	public function createCustomInvoice($customerID, $amount, $suspensionDate, $suspensionDisable, $customInfo, $status = 'due') {

		$oneTimeCharge = 0;
		$discount = $this->calculateDiscount($oneTimeCharge, $amount, $customerID);
		$total = $this->calculateTotal($oneTimeCharge, $amount, $discount);

		$customInfo = isset($customInfo) ? $customInfo : "NULL";

		$billing = new Billing($this->db);
		$currencyDetails = $billing->getCurrencyByCustomer($customerID);
		if (!$currencyDetails) {
			throw new Exception('No currency for customer ' . $customerID);
		}
		$now = new DateTime("now");


		$invoiceData = array(
			'customerID' => $customerID,
			'oneTimeCharge' => 0,
			'amount' => $amount,
			'discount' => $discount,
			'total' => $total,
			'paid' => 0, //always $0.00 when creating new invoice
			'due' => $total, //due is always == total when creating new invoice
			'generationDate' => $now->getTimestamp(),
			'suspensionDate' => $suspensionDate,
			'periodStartDate' => "NULL",
			'periodEndDate' => "NULL",
			'billingInfo' => "NULL",
			'limitInfo' => "NULL",
			'customInfo' => $customInfo,
			'module_id' => "NULL",
			'status' => $status,
			'suspensionDisable' => $suspensionDisable,
			'currency_id' => $currencyDetails['id']
		);

		$id = $this->insertInvoice($invoiceData);
		return $id;
	}

	/**
	 *
	 * Pay for $invoiceID sum equals to $paid.
	 * @param int $invoiceID
	 * @param float $paid
	 * @return void
	 */
	public function updateInvoice($invoiceID, $paid) {

		$invoiceDetails = $this->getInvoiceDetails($invoiceID);

		if (!$invoiceDetails) {
			throw new Exception('Paying for invoice ' . $invoiceID . ' failed. No invoice with this id.');
		}

		$newPaid = $invoiceDetails['paid'] + $paid;
		$newDue = $invoiceDetails['due'] - $paid;

		if ($newDue <= 0) {
			$newDue = "0.00";
			$status = "paid";

			$vps2voc = new VPS2VOC($this->db); // Bridge
//			if (!is_null($invoiceDetails['billingInfo'])) {
//
//    			$newPeriodEndDate = $invoiceDetails['periodEndDate'];
//    			//$bridge->setCustomerPeriodEndDate($invoiceDetails['customerID'], $newPeriodEndDate); //DEPRECATED
//    		}

			if ((int) $invoiceDetails['suspensionDisable'] == 1) {

				$new_deadlinecounter = "NULL";

				$query = "SELECT MIN(DATEDIFF(i.suspension_date, CURDATE())) days_left " .
						"FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_CUSTOMER . " c " .
						"WHERE i.customer_id = c.customer_id AND i.suspension_disable=1 AND i.status = 'due' AND c.customer_id=" . (int) $invoiceDetails['customerID'] . " AND ( i.invoice_id<>" . $invoiceID . " )";
				$this->db->query($query);

				if ($this->db->num_rows() > 0) {

					$data = $this->db->fetch(0);
					if (!is_null($data->days_left))
						$new_deadlinecounter = (int) $data->days_left;
				}

				//$bridge->setCustomerDeadLineCounter($invoiceDetails['customerID'], $new_deadlinecounter); //DEPRECATED
			}


			if (preg_match('/ASAP$/', $invoiceDetails['billingInfo'])) {

				//set billing plan ASAP
				$billing = new Billing($this->db);
				$scheduleBilling = $billing->getScheduledPlanByCustomer($invoiceDetails['customerID']);
				$billing->setCustomerPlan($invoiceDetails['customerID'], $scheduleBilling['billingID']);
				$billing->deletePlanFromSchedule($scheduleBilling['id']);
			}

			//increase limit
			if ($invoiceDetails['limitInfo'] != NULL) {
				$billing = new Billing($this->db);
				$billing->increaseLimit($invoiceID);
			}
		}

		$query = "UPDATE " . TB_VPS_INVOICE . " " .
				"SET paid = " . $newPaid . ", " .
				"due = " . $newDue . ", " .
				"status = '" . $status . "' " .
				"WHERE invoice_id = " . $invoiceID;
		$this->db->query($query);

		//change balance
		//$this->increaseBalance($paid, $invoiceDetails['customerID']); //changes for new balance system

		$balance = $this->getBalance($invoiceDetails['customerID']);
		$this->db->query("SELECT max(payment_date) last_payment_date FROM " . TB_VPS_PAYMENT . " WHERE invoice_id = " . $invoiceID . " AND status = 'Completed'");
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$this->db->query("UPDATE " . TB_VPS_PAYMENT . " SET balance = '" . $balance . "' WHERE invoice_id = " . $invoiceID . " AND payment_date = '" . $data->last_payment_date . "'");
		}
		if ($status == 'paid' && !is_null($invoiceDetails['moduleID']) && $invoiceDetails['periodStartDate'] <= date('Y-m-d')) {
			$ms = new ModuleSystem($this->db);
			$moduleName = $vps2voc->getModuleNameByID($invoiceDetails['moduleID']);
			$ms->setModule2company($moduleName, 1, $invoiceDetails['customerID']);
		}

		if ($status == 'paid' and
				isset($invoiceDetails['modules']) and is_array($invoiceDetails['modules']) and count($invoiceDetails['modules']) > 0) {
			$ms = new ModuleSystem($this->db);
			foreach ($invoiceDetails['modules'] as $module) {
				$moduleName = $vps2voc->getModuleNameByID($module['moduleID']);
				$ms->setModule2company($moduleName, 1, $invoiceDetails['customerID']);
			}
		}
	}

	public function getInvoiceItemsDetails($invoiceID) {
		$query = "SELECT item.id,inv.*, item.*, DATEDIFF(period_end_date, CURDATE()) end_BP_days_left, DATEDIFF(period_end_date, period_start_date) days_count_at_BP, cur.sign
    			 FROM " . TB_VPS_INVOICE . " inv, " . TB_VPS_INVOICE_ITEM . " item, " . TB_VPS_CURRENCY . " cur " .
				"WHERE inv.invoice_id = $invoiceID AND item.invoice_id = inv.invoice_id and inv.currency_id = cur.id";
		$this->db->query($query);
		if ($this->db->num_rows()) {
			$data = $this->db->fetch_all_array();

			$oneTimeCharge = 0.0;
			$amount = 0.0;

			foreach ($data as $row) {
				$customerID = $row['customer_id'];

				if (!isset($invoice[$row['invoice_id']])) { // set once is enough and necessary
					$invoice[$row['invoice_id']] = array(
						'invoiceID' => $row['invoice_id'],
						'customerID' => $row['customer_id'],
						'discount' => $row['discount'],
						'total' => $row['total'],
						'paid' => $row['paid'],
						'due' => $row['due'],
						'balance' => $row['balance'],
						'generationDate' => $row['generation_date'],
						'suspensionDate' => $row['suspension_date'],
						'periodStartDate' => $row['period_start_date'],
						'periodEndDate' => $row['period_end_date'],
						'status' => strtoupper($row['status']),
						'currency_id' => $row['currency_id'],
						'suspensionDisable' => $row['suspension_disable'],
						'daysLeft2BPEnd' => $row['end_BP_days_left'],
						'daysCountAtBP' => $row['days_count_at_BP'],
						'sign' => $row['sign']
					);
				}



				//suspension_disable to boolen
				$invoice['suspensionDisable'] = ($invoice['suspensionDisable'] == 0) ? false : true;

				if (!isset($invoice[$row['invoice_id']]['invoice_items'])) {
					$invoice[$row['invoice_id']]['invoice_items'] = array();
				}


				$item = array(//Adding Invoice Items
					'invoiceItemID' => $row['id'],
					'oneTimeCharge' => $row['one_time_charge'],
					'amount' => $row['amount'],
					'billingInfo' => $row['billing_info'],
					'limitInfo' => $row['limit_info'],
					'customInfo' => $row['custom_info'],
					'moduleID' => $row['module_id'],
					'total' => $row['one_time_charge'] + $row['amount']
				);

				if (isset($row['module_id'])) {
					$this->db->query("SELECT module.name from module where id = " . $row['module_id']);
					$arr = $this->db->fetch_array(0);

					$item['module_name'] = $arr['name'];
				}

				array_push($invoice[$row['invoice_id']]['invoice_items'], $item);

				//getting "bill to" info
				$vps2voc = new VPS2VOC($this->db);

				$customerID = $invoice[$row['invoice_id']]['customerID'];


				$customerDetails = ($customerID != null) ? $vps2voc->getCustomerDetails($customerID) : null;
				$invoice[$row['invoice_id']]['customerDetails'] = $customerDetails;

				if (isset($row['module_id'])) {
					$allAvailableModules = $vps2voc->getModules();
					$invoice[$row['invoice_id']]['module_name'] = $allAvailableModules[$row['module_id']];
				}

				$oneTimeCharge += $row['one_time_charge'];
				$amount += $row['amount'];
			}

			$discount = $this->calculateDiscount($oneTimeCharge, $amount, $customerID);

			$invoice[$invoiceID]['total'] = $amount + $oneTimeCharge;
			$invoice[$invoiceID]['discountSum'] = $discount;
			$invoice [$invoiceID]['totalSum'] = $amount + $oneTimeCharge - $discount;
		}

		return $invoice[$invoiceID];
	}

	public function getInvoiceDetails($invoiceID, $convertDates = false) {


		$query = "SELECT inv.*,
    			 item.*, item.billing_info as 'item_billing_info', cur.sign
    			 FROM " . TB_VPS_INVOICE . " inv, " . TB_VPS_INVOICE_ITEM . " item, " . TB_VPS_CURRENCY . " cur " .
				"WHERE inv.invoice_id = $invoiceID AND item.invoice_id = inv.invoice_id AND cur.id = inv.currency_id";



		$this->db->query($query);

		if ($this->db->num_rows()) {

			$data = $this->db->fetch_all_array();

			if (!isset($invoiceDetails['modules'])) {
				$invoiceDetails['modules'] = array();
			}

			$flag = true; // to fill invoice details once



			foreach ($data as $row) {


				$period_start_date = new DateTime();
				$period_start_date->setTimestamp($row['period_start_date']);
				$period_end_date = new DateTime();
				$period_end_date->setTimestamp($row['period_end_date']);
				$now = new DateTime("now");



				$daysLeft2BPEnd = $period_end_date->diff($now)->days;


				$daysCountAtBP = $period_end_date->diff($period_start_date)->days;


				if ($flag) {
					$invoiceDetails = array(
						'invoiceID' => $row['invoice_id'],
						'customerID' => $row['customer_id'],
						'oneTimeCharge' => $row['one_time_charge'],
						'amount' => $row['amount'],
						'discount' => $row['discount'],
						'total' => $row['total'],
						'paid' => $row['paid'],
						'due' => $row['due'],
						'balance' => $row['balance'],
						'generationDate' => $row['generation_date'],
						'suspensionDate' => $row['suspension_date'],
						'periodStartDate' => $row['period_start_date'],
						'periodEndDate' => $row['period_end_date'],
						'billingInfo' => $row['billing_info'],
						'limitInfo' => $row['limit_info'],
						'customInfo' => $row['custom_info'],
						'moduleID' => $row['module_id'],
						'status' => $row['status'],
						'currency_id' => $row['currency_id'],
						'suspensionDisable' => $row['suspension_disable'],
						'daysLeft2BPEnd' => $daysLeft2BPEnd,
						'daysCountAtBP' => $daysCountAtBP,
						'sign' => $row['sign']
					);
					$flag = false;

					if ($convertDates) {
						$invoiceDetails['generationDate'] = VOCApp::get_instance()->printDatetimeByTimestampInCurrentDateformat($invoiceDetails['generationDate'], false);
						$invoiceDetails['suspensionDate'] = VOCApp::get_instance()->printDatetimeByTimestampInCurrentDateformat($invoiceDetails['suspensionDate'], false);
						$invoiceDetails['periodStartDate'] = VOCApp::get_instance()->printDatetimeByTimestampInCurrentDateformat($invoiceDetails['periodStartDate'], false);
						$invoiceDetails['periodEndDate'] = VOCApp::get_instance()->printDatetimeByTimestampInCurrentDateformat($invoiceDetails['periodEndDate'], false);
					}
				}


				if (isset($row['module_id'])) {
					$invoiceDetails['modules'][] = array('moduleID' => $row['module_id']);
				} elseif (isset($row['billing_info'])) {
					$amount = $row['amount'];
					$oneTimeCharge = $row['one_time_charge'];

					$discount = $this->calculateDiscount($oneTimeCharge, $amount, $row['customer_id'], 0);

					$total = $this->calculateTotal($oneTimeCharge, $amount, $discount);
					$invoiceDetails['billing_total_price'] = $total;
				}
			}


			//getting invoice status
			$invoiceDetails['status'] = strtoupper($invoiceDetails['status']);

			//suspension_disable to boolen
			$invoiceDetails['suspensionDisable'] = ($invoiceDetails['suspensionDisable'] == 0) ? false : true;

			//getting "bill to" info
			$vps2voc = new VPS2VOC($this->db, $this->currentDate);
			$customerDetails = ($invoiceDetails['customerID'] != null) ? $vps2voc->getCustomerDetails($invoiceDetails['customerID']) : null;
			$invoiceDetails['customerDetails'] = $customerDetails;

			$allAvailableModules = $vps2voc->getModules();
			$invoiceDetails['module_name'] = $allAvailableModules[$invoiceDetails['moduleID']];

			return $invoiceDetails;
		} else
			return false;
	}

	public function getCurrentInvoiceForModule($customerID, $moduleBillingPlanID, $date = 'today') {

		$date = ($date == 'today') ? date('Y-m-d') : $date;

		//do not match canceled invoices
		$query = "SELECT i.invoice_id " .
				"FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_MODULE_BILLING . " mb " .
				"WHERE i.module_id IS NOT NULL " .
				"AND mb.id=$moduleBillingPlanID " .
				"AND mb.module_id=i.module_id " .
				"AND i.customer_id = " . $customerID . " " .
				(($date == 'future') ? "AND '" . date('Y-m-d') . "' < i.period_start_date " : "AND '$date' BETWEEN i.period_start_date AND i.period_end_date ") .
				"AND i.status IN ('paid', 'due') " .
				"ORDER BY i.period_end_date DESC";

		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoice = $this->getInvoiceDetails($data->invoice_id);
			return $invoice;
		} else {
			return false;
		}
	}

	public function getCurrentOrFutureInvoiceForModule($customerID, $moduleBillingPlanID, $date = null) {

		if (!is_null($date)) {
			$date = date('Y-m-d', strtotime($date));
			$invoice = $this->getCurrentInvoiceForModule($customerID, $moduleBillingPlanID, $date);
		} else {
			$invoice = $this->getCurrentInvoiceForModule($customerID, $moduleBillingPlanID);
		}
		if (!$invoice) {
			$invoice = $this->getCurrentInvoiceForModule($customerID, $moduleBillingPlanID, 'future');
		}
		return $invoice;
	}

	public function getInvoiceForModuleByStartDate($customerID, $moduleBillingPlanID, $dateBegin) {

		$query = "SELECT i.invoice_id
    			 FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_MODULE_BILLING . " mb, " . TB_VPS_INVOICE_ITEM . " item
    			 WHERE item.module_id IS NOT NULL
    			 AND item.invoice_id = i.invoice_id
    			 AND mb.id = $moduleBillingPlanID
    			 AND mb.module_id = item.module_id
    			 AND i.customer_id = $customerID
    			 AND '$dateBegin' = i.period_start_date
    			 AND i.status IN ('paid', 'due')
    			  LIMIT 1";


		$this->db->query($query);



		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoice = $this->getInvoiceDetails($data->invoice_id);
			return $invoice;
		} else {
			return false;
		}
	}

	public function getModuleIDByBillingModuleID($billingModuleID) {
		$query = "SELECT module_id FROM vps_module_billing WHERE id = $billingModuleID LIMIT 1";
		$this->db->query($query);
		echo $query;
		$row = $this->db->fetch_array(0);
		$moduleID = $row['module_id'];
		return $moduleID;
	}

	/**
	 * If invoice invoice_items more than 1, it means invoice is multi
	 */
	public function getInvoiceIDByBillingModuleID($customerID, $billingModuleID) {
		$query = "SELECT module_id FROM vps_module_billing WHERE id = $billingModuleID LIMIT 1";

		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		$moduleID = $row['module_id'];



		$query = "SELECT inv.invoice_id
					FROM vps_invoice_item item, vps_invoice inv
					WHERE inv.invoice_id = item.invoice_id
					AND item.module_id = $moduleID
					AND inv.customer_id = $customerID
					AND inv.status != 'CANCELED'
					AND inv.status != 'canceled'
					LIMIT 1";

		$this->db->query($query);

		$row = $this->db->fetch_array(0);
		$invoiceID = $row['invoice_id'];



		return $invoiceID;
	}

	public function getInvoiceDetailsByBillingModuleID($customerID, $billingModuleID) {
		$invoiceID = $this->getInvoiceIDByBillingModuleID($customerID, $billingModuleID);

		$invoiceDetails = $this->getInvoiceDetails($invoiceID);

		//adding modules info
		$query = "SELECT mb.id, mb.month_count, mb2c.price, mb.module_id, mb.type, m2c.start_date
				FROM vps_module2customer m2c, vps_module_billing mb, vps_module2currency mb2c, vps_invoice_item item
				WHERE mb.id = m2c.module_billing_id
				AND mb2c.module_billing_id = mb.id
				AND m2c.customer_id = $customerID
				AND mb2c.currency_id = {$invoiceDetails['currency_id']}
				AND item.invoice_id = {$invoiceDetails['invoiceID']}
				AND item.module_id = mb.module_id";

		$this->db->query($query);

		$modules = $this->db->fetch_all_array();

		$invoiceDetails['modules'] = $modules;

		return $invoiceDetails;
	}

	public function isModuleInMultiinvoice($customerID, $moduleBillingID) {

		$invoiceID = $this->getInvoiceIDByBillingModuleID($customerID, $moduleBillingID);

		$query = "SELECT COUNT(item.id) as 'count'
					FROM vps_invoice inv, vps_invoice_item item
					WHERE item.invoice_id = inv.invoice_id AND item.invoice_id = $invoiceID";

		$this->db->query($query);

		$row = $this->db->fetch_array(0);
		$count = $row['count'];

		if ($count and $count > 1) {
			return true;
		} else {
			return false;
		}
	}

	public function getCurrentInvoice($customerID, $date = 'today', $doNotSelectCanceled = true) {


		$date = ($date == 'today') ? date('Y-m-d') : $date;



		$query = "SELECT inv . *
			FROM " . TB_VPS_INVOICE . " inv, " . TB_VPS_INVOICE_ITEM . " item
			WHERE inv.customer_id = $customerID
			AND inv.invoice_id = item.invoice_id
			AND item.billing_info IS NOT NULL
			AND inv.status <> 'canceled'";

		//do not match canceled invoices
		if ($doNotSelectCanceled) {
			$query .= "AND inv.status = 'paid' ";
		} else {

			$query .= " AND status IN ('paid','canceled') ";
		}
		$query .= " ORDER BY period_end_date DESC";

		$this->db->query($query);
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoice = $this->getInvoiceDetails($data->invoice_id);
			return $invoice;
		} else {
			return false;
		}
	}

	public function getLastInvoice($customerID, $doNotSelectCanceled = true) {


		$query = "SELECT inv.invoice_id
				FROM vps_invoice inv, vps_invoice_item item
				WHERE
				inv.customer_id = $customerID AND
				inv.invoice_id = item.invoice_id AND
				item.billing_info IS NOT NULL AND ";
		if ($doNotSelectCanceled) {

			$query.= "inv.status <> 'canceled'";
		}
		$query .= " ORDER BY inv.generation_date DESC
				LIMIT 1 ";



		$this->db->query($query);

		$data = $this->db->fetch(0);

		$invoice = $this->getInvoiceDetails($data->invoice_id);

		return $invoice;
	}

	/**
	 * getSqlQueryGetAllInvoices is build sql-query to fetch invoice(s) detail(s) by parameters
	 * @customerID -	 will return invoices and by customer_id, may be NULL
	 * @status 			 will return invoices and by status
	 * @moduleID		 will return invoice and by module_id
	 */
	private function getSqlQueryGetAllInvoices($customerID, $status = NULL, $moduleID = NULL) {
		$query = "SELECT COUNT(it.id) as 'items_included', SUM(it.one_time_charge) as 'oneTimeCharge', SUM(it.amount) as 'amount', DATEDIFF(inv.suspension_date, CURDATE()) days_left,
					inv.invoice_id as 'invoiceID',
					inv.customer_id as 'customerID',
					inv.discount,
					inv.total,
					inv.paid,
					inv.due,
					inv.balance,
					inv.generation_date as 'generationDate',
					inv.suspension_date as 'suspensionDate',
					inv.period_start_date as 'periodStartDate',
					inv.period_end_date as 'periodEndDate',
					it.billing_info as 'billingInfo',
					it.limit_info as 'limitInfo',
					it.custom_info as 'customInfo',
					it.module_id as 'moduleID',
					inv.status,
					inv.currency_id,
					inv.suspension_disable as 'suspensionDisable',
					vps_customer.balance as 'customer_balance'	,
					vps_currency.sign
					FROM vps_invoice inv, vps_invoice_item it, vps_customer, vps_currency WHERE inv.invoice_id = it.invoice_id and inv.customer_id = vps_customer.customer_id
					and vps_currency.id = inv.currency_id ";
		if ($customerID and is_numeric($customerID)) {
			$query .= " AND inv.customer_id = $customerID";
		}
		if ($status) {
			if (is_array($status) and count($status) > 0) {// If statues are array - than insert sql code like IN('asdf','asdf')
				$tmp = "AND IN(";
				foreach ($status as $i) {
					$tmp .= "'$i',";
				}
				$tmp = substr_replace($tmp, "", strlen($tmp) - 2, 1);
				$tmp .= ") ";
				$query .= $tmp;
			} elseif ($status) {
				$query .= " AND inv.status = '$status'";
			}
		}
		if ($moduleID and is_numeric($moduleID)) {
			$query .= " AND inv.module_id = $moduleID";
		}

		$query .= " GROUP BY inv.invoice_id";


		return $query;
	}

	public function getAllInvoicesList($customerID) {

		$query = $this->getSqlQueryGetAllInvoices($customerID);
		$this->db->query($query);
		$invoices = $this->db->fetch_all_array();
		return $invoices;
	}

	private function getInvoiceArrFromDBObj($data, $needCompany = false, $needModules = false) {
		$invoiceDetails = array(
			'invoiceID' => $data->invoice_id,
			'customerID' => $data->customer_id,
			'oneTimeCharge' => $data->one_time_charge,
			'amount' => $data->amount,
			'discount' => $data->discount,
			'total' => $data->total,
			'paid' => $data->paid,
			'due' => $data->due,
			'balance' => $data->balance,
			'generationDate' => $data->generation_date,
			'suspensionDate' => $data->suspension_date,
			'periodStartDate' => $data->period_start_date,
			'periodEndDate' => $data->period_end_date,
			'billingInfo' => $data->billing_info,
			'limitInfo' => $data->limit_info,
			'customInfo' => $data->custom_info,
			'moduleID' => $data->module_id,
			'status' => $data->status,
			'currency_id' => $data->currency_id,
			'suspensionDisable' => $data->suspension_disable,
			'daysLeft2BPEnd' => $data->end_BP_days_left,
			'daysCountAtBP' => $data->days_count_at_BP
		);

		//getting invoice status
		$invoiceDetails['status'] = strtoupper($invoiceDetails['status']);

		//suspension_disable to boolen
		$invoiceDetails['suspensionDisable'] = ($invoiceDetails['suspensionDisable'] == 0) ? false : true;

		//getting "bill to" info
		if ($needCompany) {
			$vps2voc = new VPS2VOC($this->db);
			$customerDetails = ($invoiceDetails['customerID'] != null) ? $vps2voc->getCustomerDetails($invoiceDetails['customerID']) : null;
			$invoiceDetails['customerDetails'] = $customerDetails;
		}

		if ($needModules) {
			$allAvailableModules = $vps2voc->getModules();
			$invoiceDetails['module_name'] = $allAvailableModules[$invoiceDetails['moduleID']];
		}

		return $invoiceDetails;
	}

	public function getPaidInvoicesList($customerID) {

		$query = "SELECT vi . * , vii . * , DATEDIFF( vi.period_end_date, CURDATE( ) ) end_BP_days_left, DATEDIFF( vi.period_end_date, vi.period_start_date )
					FROM vps_invoice AS vi, vps_invoice_item vii
					WHERE vii.invoice_id = vi.invoice_id
						AND
						 	customer_id = $customerID
						AND
							vi.status = 'paid'
					ORDER BY vi.invoice_id DESC";



		$query = $this->getSqlQueryGetAllInvoices($customerID, "paid");



		$this->db->query($query);

		$invoices = $this->db->fetch_all_array();

		$this->totalPaid = 0;
		foreach ($invoices as $i) {
			if (!$i['customInfo']) {
				$this->totalPaid += $i['total'];
			}
		}



		return $invoices;
	}

	public function getDueInvoicesList($customerID) {


		$query = $this->getSqlQueryGetAllInvoices($customerID, "due");


		$this->db->query($query);

		$invoices = $this->db->fetch_all_array();


		if ($this->db->num_rows()) {
			$totalDueInvoiceCount = $this->db->num_rows();

			$totalDueInvoiceCount = count($invoices);
			for ($i = 0; $i < $totalDueInvoiceCount; $i++) {
				$this->totalDue += $invoices[$i]['due'];
				$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);

				$history = $payment->getHistory($invoices[$i]['invoiceID']);
				foreach ($history as $paymentAction) {
					if ($paymentAction['status'] != "--") { // it means that there was smth like "PENDING" txns, so user shouldn't see Pay Now button
						$stillProcessing = true;
					}
					if ($paymentAction['status'] == "Reversed" || // txn is reversed. Show Pay Now button for new payment
							$paymentAction['status'] == "Refunded" ||
							$paymentAction['status'] == "Completed") { //	txn is refunded.
						$stillProcessing = false;
					}
				}

				if (!$stillProcessing) { //	show/do not show Pay Now button
					//	vwm/ - working directory
					// 	test/VOC15/ - test directory
					//$URLBody = "http://vocwebmanager.com/vwm/";
					$URLBody = "http://" . DOMAIN . "/";
					$URLBody .= (ENVIRONMENT == "server") ? "vwm/" : "dev/VOC15/";

					//	paypal settings----
					$this->db->query("SELECT value FROM " . TB_VPS_CONFIG . " WHERE name = 'paypal_merchant_email'");
					$paypalMerchantEmail = $this->db->fetch(0);

					$billing = new Billing($this->db);

					if ($invoices[$i]['billingInfo'] != null) {
						$billingPlan = $billing->getCustomerPlan($customerID);
						$itemName = $billingPlan['name'];
					} elseif ($invoices[$i]['limitInfo'] != null) {
						$itemName = $invoices[$i]['limitInfo'];
					} elseif ($invoices[$i]['customInfo'] != null) {
						$itemName = $invoices[$i]['customInfo'];
					}

					$currencyDetails = $billing->getCurrencyDetails($invoices[$i]['currency_id']);

					$invoices[$i]['paypal'] = array(
						'merchantEmail' => $paypalMerchantEmail->value, //	merchants email (paypal account)
						'itemName' => $itemName, //	item name (it will be shown to user)
						'itemNumber' => $invoices[$i]['invoiceID'], //	invoice ID (for us and IPN)
						'amount' => number_format($invoices[$i]['due'], 2), //	how much customer should pay
						'currency_code' => $currencyDetails['iso'], //	iso code of invoice currency
						'noShipping' => "1", //	no shipping (we provide service, service cannot be shipped)
						'noNote' => "0", //	customer is not prompted to include a note
						'returnURL' => $URLBody . "vps.php?action=viewDetails&category=invoices&invoiceID=" . $invoices[$i]['invoiceID'] . "&successPayment=1", //	return URL
						'cancelURL' => $URLBody . "vps.php?action=viewDetails&category=invoices&invoiceID=" . $invoices[$i]['invoiceID'] . "&successPayment=0", //	cansel URL
						'notifyURL' => $URLBody . "payments/ipn.php"				 //	IPN script path
					);
					//	-------------------
				}
			}
		}

		return $invoices;
	}

	public function getCanceledInvoicesList($customerID) {


		$query = $this->getSqlQueryGetAllInvoices($customerID, "canceled");

		$this->db->query($query);



		$invoices = $this->db->fetch_all_array();

		$this->totalCanceled = 0;
		foreach ($invoices as $i) {
			$this->totalCanceled += $i['total'];
		}

		return $invoices;
	}

	public function getDiscount($customerID) {

		$query = "SELECT discount FROM " . TB_VPS_CUSTOMER . " WHERE customer_id = " . $customerID;
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			return $data->discount;
		} else {
			return false;
		}
	}

	public function getBalance($customerID) {

		$query = "SELECT balance FROM " . TB_VPS_CUSTOMER . " WHERE customer_id = " . $customerID;
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			return $data->balance;
		} else {
			return false;
		}
	}

	public function getModuleInvoiceWhenTrialPeriod($customerID, $moduleBillingPlanID) {

		$query = "SELECT i.invoice_id " .
				"FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_MODULE_BILLING . " mb " .
				"WHERE i.customer_id = " . $customerID . " " .
				"AND i.module_id IS NOT NULL " .
				"AND mb.id=$moduleBillingPlanID " .
				"AND mb.module_id=i.module_id " .
				"AND i.status <> 'canceled'";
		$this->db->query($query);
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoiceDetails = $this->getInvoiceDetails($data->invoice_id);
			return $invoiceDetails;
		} else {
			return false;
		}
	}

	public function getInvoiceWhenTrialPeriod($customerID) {

		$query = "SELECT inv.invoice_id " .
				"FROM " . TB_VPS_INVOICE . " as inv, " . TB_VPS_INVOICE_ITEM . " as item " .
				"WHERE inv.invoice_id = item.invoice_id
			 AND inv.customer_id = " . $customerID . " " .
				"AND item.billing_info IS NOT NULL " .
				"AND inv.status <> 'canceled'";

		$this->db->query($query);


		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoiceDetails = $this->getInvoiceDetails($data->invoice_id);
			return $invoiceDetails;
		} else {
			return false;
		}
	}

	public function getModuleInvoiceForFuturePeriod($customerID, $moduleID) {

		$query = "SELECT i.invoice_id, i.total, i.status " .
				"FROM " . TB_VPS_INVOICE . " i " .
				"WHERE i.module_id = " . $moduleID . " " .
				"AND i.customer_id = " . $customerID . " " .
				"AND i.period_start_date > CURDATE() " .
				"AND i.status IN ('due','paid') ";
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoiceForFuturePeriod = array(
				'invoiceID' => $data->invoice_id,
				'total' => $data->total,
				'status' => $data->status
			);
			return $invoiceForFuturePeriod;
		} else {
			return false;
		}
	}

	public function getInvoiceForFuturePeriod($customerID) {

		$query = "SELECT invoice_id, total, status " .
				"FROM " . TB_VPS_INVOICE . " " .
				"WHERE customer_id = " . $customerID . " " .
				"AND suspension_date > CURDATE() " .
				"AND status IN ('due','paid') " .
				"AND billing_info IS NOT NULL";

		$query = "SELECT inv.invoice_id, inv.total, inv.status
					FROM " . TB_VPS_INVOICE . " inv, " . TB_VPS_INVOICE_ITEM . " it
					WHERE inv.customer_id = $customerID AND
					it.invoice_id = inv.invoice_id AND
					inv.suspension_date > CURDATE() AND
					inv.status IN ('due','paid') AND
					it.billing_info IS NOT NULL";

		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			$invoiceForFuturePeriod = array(
				'invoiceID' => $data->invoice_id,
				'total' => $data->total,
				'status' => $data->status
			);
			return $invoiceForFuturePeriod;
		} else {
			return false;
		}
	}

	private function createInvoiceForBallanceChange($customerID, $amount, $customInfo) {

	}

	public function manualBalanceChange($customerID, $operation, $balance) {

		if ($operation == "+") {
			$amount = -$balance;
		} else {
			$amount = $balance;
		}

		$desc .= $operation == "+" ? "Increase" : "Decrease";
		$desc .= " balance to customer <b>№$customerID</b> on <b>$";
		if ($amount < 0) {
			$desc .= $amount * -1;
		} else {
			$desc .= $amount;
		}
		$desc .= "</b>";
		$insertedInvoiceID = $this->createCustomInvoice($customerID, $amount, date("Y-m-d"), 0, "'$desc'", 'CANCELED');

		$this->updateInvoice($insertedInvoiceID, $paid);
	}

	//	Restores Billing and Custom invoices. (Limit invoices aren't cancelable, so we do not restore them)
	//	$shift	=	number of days for which we should shift dates. Shift is only for Billing Invoices
	public function restoreInvoice($invoiceID, $shift, $shiftStartDate = false) {

		//copy invoice
		$insertedID = $this->cloneInvoice($invoiceID);

		$originalInvoiceDetails = $this->getInvoiceDetails($invoiceID);

		//apply period shift and restore status

		$newPeriodEndDate = date('Y-m-d', strtotime($originalInvoiceDetails['periodEndDate'] . " + " . $shift . " days"));
		$newSuspensionDate = date('Y-m-d', strtotime($originalInvoiceDetails['suspensionDate'] . " + " . $shift . " days")); //for custom invoices
		$status = ($originalInvoiceDetails['total'] == $originalInvoiceDetails['paid']) ? "paid" : "due";


		//$bridge = new Bridge($this->db); // Bridge

		$new_counter_value = "";
		$query = "SELECT MIN(DATEDIFF(i.suspension_date, CURDATE())) days_left " .
				"FROM " . TB_VPS_INVOICE . " i, " . TB_VPS_CUSTOMER . " c " .
				"WHERE i.customer_id = c.customer_id AND i.suspension_disable=1 AND i.status = 'due' AND c.customer_id=" . (int) $originalInvoiceDetails['customerID'] . " AND i.invoice_id<>" . $insertedID;
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$data = $this->db->fetch(0);
			if (!is_null($data->days_left))
				$new_counter_value = (int) $data->days_left; //get min deadline_counter from table where invoice_id<>cloneInvoiceID (insertedID) that has bad/old suspension_date and status
		}

		if (!$shiftStartDate) {

			if ((int) $originalInvoiceDetails['suspensionDisable'] == 1 && $status == "paid")
				if ($new_counter_value == "")
					$new_counter_value = "NULL"; // if $new_counter_value != "" set new deadline_counter (if this invoice hadn't real value of deadline_counter than just old=new counter)

				if (!is_null($originalInvoiceDetails['customInfo'])) { //custom invoice -> shift suspension date
				$query = "UPDATE " . TB_VPS_INVOICE . " " .
						"SET suspension_date = '" . $newSuspensionDate . "', " .
						"generation_date = '" . date('Y-m-d') . "', " .
						"status = '" . $status . "' " .
						"WHERE invoice_id = " . $insertedID;
			} else {
				$query = "UPDATE " . TB_VPS_INVOICE . " " .
						"SET period_end_date = '" . $newPeriodEndDate . "', " .
						"generation_date = '" . date('Y-m-d') . "', " .
						"status = '" . $status . "' " .
						"WHERE invoice_id = " . $insertedID;
			}
		} else {

			$newPeriodStartDate = date('Y-m-d', strtotime($originalInvoiceDetails['periodStartDate'] . " + " . $shift . " days"));

			if ((int) $originalInvoiceDetails['suspensionDisable'] == 1) {

				if ($status == "due") {

					//set new deadline_counter comparing for this one invoiceID with min(for all other due invoices)
					$buff_counter_value = strtotime($newPeriodStartDate) - strtotime(date('Y-m-d'));
					$buff_counter_value = (int) round($buff_counter_value / 60 / 60 / 24);
					$new_counter_value = ($new_counter_value != "" && $buff_counter_value > (int) $new_counter_value) ? $new_counter_value : $buff_counter_value;
				}

				if ($status == "paid")
					if ($new_counter_value == "")
						$new_counter_value = "NULL";
			}

			$query = "UPDATE " . TB_VPS_INVOICE . " " .
					"SET period_start_date = '" . $newPeriodStartDate . "', " .
					"suspension_date = '" . $newSuspensionDate . "', " .
					"period_end_date = '" . $newPeriodEndDate . "', " .
					"generation_date = '" . date('Y-m-d') . "', " .
					"status = '" . $status . "' " .
					"WHERE invoice_id = " . $insertedID;
		}

		$this->db->query($query);
	}

	public function changeInvoiceStatus($invoiceID, $newStatus, $newPaymentMethodID = null, $newDueAmount = false, $note = null) {


		switch (strtoupper($newStatus)) {
			case "DUE":
				$invoiceDetails = $this->getInvoiceDetails($invoiceID);
				if (!$newDueAmount) {
					$newDueAmount = $invoiceDetails['total'];
				}
				$this->dueInvoice($invoiceID, $newDueAmount);

				if (empty($note)) {
					$note = "Due by Super User";
				}
				$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);
				$payment->createPayment($invoiceID, 0, $note, "Completed", $invoiceDetails['total'] - $newDueAmount); // 0 - superAdmin
				break;
			case "PAID":
				$invoiceDetails = $this->getInvoiceDetails($invoiceID);

				$paymentMethod = $this->getPaymentMethods($newPaymentMethodID);
				if (empty($note)) {
					$note = "Paid by Super User";
				}

				$note = $paymentMethod['method'] . " payment. " . $note . ".";
				$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);
				$payment->createPayment($invoiceID, 0, $note, "Completed", $invoiceDetails['due'], $newPaymentMethodID); // 0 - superAdmin

				$this->updateInvoice($invoiceID, $invoiceDetails['due']);
				break;
			case "DEACTIVATED":
				$currentInvoice = $this->getInvoiceDetails($invoiceID);
				$backToCustomer = $this->partialRefund($currentInvoice);
				$this->manualBalanceChange($currentInvoice['customerID'], '+', $backToCustomer);
				$this->cancelInvoice($invoiceID, 'deactivated');
				break;
			case "CANCELED" || "CANCEL":
				$this->cancelInvoice($invoiceID);
				break;
		}
	}

	public function cancelInvoice($invoiceID, $type = 'CANCELED') {

		$invoiceDetails = $this->getInvoiceDetails($invoiceID);
		if (!$invoiceDetails || strtoupper($invoiceDetails['status']) == 'CANCELED') {
			return false;
		}

		$q = "SELECT module_id FROM vps_invoice_item where invoice_id = $invoiceID and module_id IS NOT NULL";
		$this->db->query($q) or die(mysql_error());
		$modules = $this->db->fetch_all_array();
		$customerID = $invoiceDetails['customerID'];

		$ms = new ModuleSystem($this->db);
		$vps2voc = new VPS2VOC($this->db);

		foreach ($modules as $m) {
			$query = "SELECT m2c . *
					FROM vps_module2customer m2c, vps_module_billing mb, module m
					WHERE m2c.module_billing_id = mb.id
					AND mb.module_id = m.id
					AND m2c.customer_id = $customerID
					AND m.id = {$m['module_id']}
					LIMIT 1"; //Get id from table module2customer
			$this->db->query($query);

			$ar = $this->db->fetch_all_array();
			$m2cID = $ar[0]['id'];


			$moduleName = $vps2voc->getModuleNameByID($m['module_id']);

			$ms->setModule2company($moduleName, 0, $customerID);


			$query = "DELETE FROM " . TB_VPS_MODULE2CUSTOMER . " WHERE id = $m2cID";



			$this->db->exec($query);
		}

		$this->increaseBalance($invoiceDetails['paid'], $invoiceDetails['customerID']); //if invoice was paid and then canceled	we should return cash

		$query = "UPDATE " . TB_VPS_INVOICE . " " .
				"SET status = '$type' " .
				"WHERE invoice_id = " . $invoiceDetails['invoiceID'];

		$this->db->query($query);

		$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);
		$payment->cancelInvoicePayment($invoiceID, 0, $type); // 0 - superAdmin
	}

	public function getCustomDueInvoices($customerID) {

		$query = "SELECT it.invoice_id
				FROM " . TB_VPS_INVOICE . " inv, " . TB_VPS_INVOICE_ITEM . " it
				WHERE inv.customer_id = $customerID AND
				inv.invoice_id = it.invoice_id AND
				inv.status = 'due' AND
				it.custom_info IS NOT NULL";

		$this->db->query($query);
		if ($this->db->num_rows()) {
			$invoiceList = $this->db->fetch_all();
			for ($i = 0; $i < count($invoiceList); $i++) {
				$invoice = $this->getInvoiceDetails($invoiceList[$i]->invoice_id);
				$invoices[] = $invoice;
			}
			return $invoices;
		} else
			return false;
	}

	public function restoreDueCustomInvoices($customerID, $shift) {


		$query = "SELECT it.invoice_id
				FROM " . TB_VPS_INVOICE . " inv, " . TB_VPS_INVOICE_ITEM . " it
				WHERE inv.customer_id = $customerID AND
				inv.invoice_id = it.invoice_id AND
				inv.status = 'canceled' AND
				it.custom_info IS NOT NULL AND
				inv.due > 0";

		$this->db->query($query);
		if ($this->db->num_rows()) {
			$invoiceList = $this->db->fetch_all();
			for ($i = 0; $i < count($invoiceList); $i++) {
				$this->restoreInvoice($invoiceList[$i]->invoice_id, $shift);
			}
			return true;
		} else
			return false;
	}

	public function calculateTotal($oneTimeCharge, $amount, $discount = 0) {

		$total = $oneTimeCharge + $amount - $discount;
		return $total;
	}

	//	returns array of possible invoice statuses
	public function getInvoiceStatusList() {

		$invoiceStatusList = array();
		$dueType = array('label' => 'DUE', 'style' => 'color:red;', 'status' => 'DUE', 'paymentMethodID' => null);
		$invoiceStatusList[0] = $dueType;

		$paymentMethods = $this->getPaymentMethods();
		foreach ($paymentMethods as $paymentMethod) {
			$paidType = array('label' => 'PAID (' . $paymentMethod['method'] . ')', 'style' => 'color:green;', 'status' => 'PAID', 'paymentMethodID' => $paymentMethod['id']);
			$invoiceStatusList[] = $paidType;
		}

		$canceledType = array('label' => 'CANCELED', 'style' => 'color:blue;', 'status' => 'CANCELED', 'paymentMethodID' => null);
		$invoiceStatusList[] = $canceledType;

		return $invoiceStatusList;
	}

	public function getInvoiceStatusListNew($invoiceID) {


		if ($invoiceID != null) {
			$invoiceDetails = $this->getInvoiceDetails($invoiceID);
			$dateStartArray = explode("-", $invoiceDetails['periodStartDate']);
			$date_start = mktime(0, 0, 0, $dateStartArray[1], $dateStartArray[2], $dateStartArray[0]);

			$dateEndArray = explode("-", $invoiceDetails['periodEndDate']);
			$date_end = mktime(0, 0, 0, $dateEndArray[1], $dateEndArray[2], $dateEndArray[0]);
		}


		if (time() < $dateEndArray && time() > $date_start)
			$truePeriod = true;
		else
			$truePeriod = false;

		$invoiceStatusList = array();
		$dueType = array('label' => 'DUE', 'style' => 'color:red;', 'status' => 'DUE', 'paymentMethodID' => null);
		$invoiceStatusList[0] = $dueType;

		if ((strtoupper($invoiceDetails['status']) != 'CANCELED' && strtoupper($invoiceDetails['status']) != 'DEACTIVATED') || $invoiceID == null) {
			$paymentMethods = $this->getPaymentMethods();
			foreach ($paymentMethods as $paymentMethod) {
				$paidType = array('label' => 'PAID (' . $paymentMethod['method'] . ')', 'style' => 'color:green;', 'status' => 'PAID', 'paymentMethodID' => $paymentMethod['id']);
				$invoiceStatusList[] = $paidType;
			}
		}

		if (($truePeriod) || $invoiceID == null) {
			$canceledType = array('label' => 'CANCELED', 'style' => 'color:blue;', 'status' => 'CANCELED', 'paymentMethodID' => null);
			$invoiceStatusList[] = $canceledType;
		}
		if (($truePeriod && $invoiceDetails['module_id'] && strtoupper($invoiceDetails['status']) == 'PAID') || $invoiceID == null) {
			$deactivatedType = array('label' => 'DEACTIVATED', 'style' => 'color:#904e00;', 'status' => 'DEACTIVATED', 'paymentMethodID' => null);
			$invoiceStatusList[] = $deactivatedType;
		}

		return $invoiceStatusList;
	}

	private function calculateDiscount($oneTimeCharge, $amount, $customerID, $backToCustomer = 0) {

		$discountPercent = $this->getDiscount($customerID);
		$discount = round((($oneTimeCharge + $amount) * $discountPercent) / 100, 2) + $backToCustomer;
		return $discount;
	}

	private function insertMultiInvoiceItem($invoiceData) {
		$invoiceData['customInfo'] = isset($invoiceData['customInfo']) ? $invoiceData['customInfo'] : "BLAHBLAH";

		$query = "INSERT INTO " . TB_VPS_INVOICE_ITEM . " (invoice_id,one_time_charge,amount,billing_info,limit_info,custom_info,module_id)
    				VALUES(
    					{$invoiceData['invoiceID']},
    					{$invoiceData['oneTimeCharge']},
    					{$invoiceData['amount']},
    					{$invoiceData['billingInfo']},
    					{$invoiceData['limitInfo']},
    					{$invoiceData['customInfo']},
    					{$invoiceData['module_id']}
    				)";

		$this->db->query($query) or die("mysql query error: <h3>$query</h3>" . mysql_error());
		echo "<h3>insertMultiInvoiceItem query:$query </h3>";
	}

	private function insertInvoice($invoiceData, $autopay = true) {
		//echo "<br/>insertInvoice";

		$query = "INSERT INTO " . TB_VPS_INVOICE . " (customer_id, discount, total, paid, due, generation_date, suspension_date, period_start_date, period_end_date, status, suspension_disable, currency_id) VALUES ( " .
				"" . $invoiceData['customerID'] . ", " .
				"'" . $invoiceData['discount'] . "', " .
				"'" . $invoiceData['total'] . "', " .
				"'" . $invoiceData['paid'] . "', " .
				"'" . $invoiceData['due'] . "', " .
				$invoiceData['generationDate'] . ", " .
				$invoiceData['suspensionDate'] . ", " .
				$invoiceData['periodStartDate'] . ", " .
				$invoiceData['periodEndDate'] . ", " .
				"'" . $invoiceData['status'] . "', " .
				"" . $invoiceData['suspensionDisable'] . ", " .
				"" . $invoiceData['currency_id'] . ")";


		$this->db->query($query);

		if (mysql_error()) {
			throw new Exception(mysql_error() . " query: $query");
		}

		$insertedInvoiceID = $this->db->getLastInsertedID();


		$invoiceData['billingInfo'] = $this->NormalizeSqlParam($invoiceData['billingInfo']);
		$invoiceData['limitInfo'] = $this->NormalizeSqlParam($invoiceData['limitInfo']);
		$invoiceData['customInfo'] = $this->NormalizeSqlParam($invoiceData['customInfo']);

		$query = "INSERT INTO " . TB_VPS_INVOICE_ITEM . " (invoice_id,one_time_charge,amount,billing_info,limit_info,custom_info,module_id)
    				VALUES(
    					$insertedInvoiceID,
    					{$invoiceData['oneTimeCharge']},
    					{$invoiceData['amount']},
    					{$invoiceData['billingInfo']},
    					{$invoiceData['limitInfo']},
    					{$invoiceData['customInfo']},
    					{$invoiceData['module_id']}
    				)";


		$this->db->query($query);

		if (mysql_error()) {
			throw new Exception(mysql_error() . "<br/>query: $query");
		}

		$balance = $this->getBalance($invoiceData['customerID']);

		//pay from/to balance
		$invoiceData['invoiceID'] = $insertedInvoiceID;
		if ($autopay) {
			$this->payInvoiceFromBalance($invoiceData);
		}

		$this->emailNotification($invoiceData['customerID'], 'new');

		return $insertedInvoiceID;
	}

	private function NormalizeSqlParam($param) {
		if (is_null($param)) {
			return "NULL";
		} elseif ($param == "NULL") {
			return "NULL";
		} else {
			/* If single quotes are already exists */
			if (strlen($param) > 2) {
				$l = strlen($param);
				if ($param[0] == "'" and $param[$l - 1] == "'") {
					return $param; /* than do not change param */
				}
			}
			return "'$param'";
		}
	}

	public function payInvoiceFromBalance($invoiceData) {

		$invoiceID = $invoiceData['invoiceID'];
		$balance = $this->getBalance($invoiceData['customerID']);

		if ($balance - $invoiceData['total'] >= 0) {


			//save current customer balance to invoice details
			$balance = $this->getBalance($invoiceData['customerID']);


			$this->db->query("UPDATE " . TB_VPS_INVOICE . " SET balance = '" . $balance . "' WHERE invoice_id = " . $invoiceID);

			//pay from balance

			$this->decreaseBalance($invoiceData['due'], $invoiceData['customerID']);

			$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);
			if ($invoiceData['total'] <= 0) {
				$paid = $payment->createPayment($invoiceID, 0, "to Balance", "Completed"); //0 - SuperUser(system)
			} else {
				$paid = $payment->createPayment($invoiceID, 0, "from Balance", "Completed"); //0 - SuperUser(system)
			}
			$this->updateInvoice($invoiceID, $paid);
		} else {
			$balance = $this->getBalance($invoiceData['customerID']);
			$this->db->query("UPDATE " . TB_VPS_INVOICE . " SET balance = '" . $balance . "' WHERE invoice_id = " . $invoiceID);
		}
	}

	private function decreaseBalance($minusBalance, $customerID) {

		$query = "UPDATE " . TB_VPS_CUSTOMER . " " .
				"SET balance = balance - " . $minusBalance . " " .
				"WHERE customer_id = " . $customerID;
		$this->db->query($query);
	}

	private function increaseBalance($plusBalance, $customerID) {
		//$this->db->select_db(DB_NAME);
		$query = "UPDATE " . TB_VPS_CUSTOMER . " " .
				"SET balance = balance + " . $plusBalance . " " .
				"WHERE customer_id = " . $customerID;
		$this->db->query($query);
	}

	public function partialRefund($currentInvoice) {

		//new correct. I'm rounding end result ($backToCustomer) here!

		/**
		 * If parameters is not correct, add info and stack to dump file
		 */
		$now = new DateTime("now");
		$period_start_date = new DateTime();
		$period_start_date->setTimestamp($currentInvoice["periodStartDate"]);


		if (!$currentInvoice && !$currentInvoice['total'] && !$currentInvoice['daysCountAtBP'] && !$currentInvoice['daysLeft2BPEnd'] and
				$now->diff($period_start_date) == true) { //If period start date exists in future..

			$funcs = xdebug_get_function_stack();
			$this->dumpStackFromPartialRefund($currentInvoice, $funcs);
		} else {

			$oneDayCost = floatval($currentInvoice['total']) / intval($currentInvoice['daysCountAtBP']);
			$backToCustomer = round($oneDayCost * intval($currentInvoice['daysLeft2BPEnd']), 2);
			return floatval($backToCustomer);
		}
	}

	private function dumpStackFromPartialRefund($invoice, $stack, $filename = '/home/developer/mywork/www/dump_log/vpc.html') {

		$f = fopen($filename, 'a+') or die('file open error');
		if (!$f) {
			return;
		}

		ob_start();
		var_dump($invoice);
		$invoiceDump = ob_get_contents();
		ob_end_clean();

		ob_start();
		var_dump($stack);
		$functionsStackDump = ob_get_contents();
		ob_end_clean();

		fwrite($f, "Stack from " . date("F j, Y, g:i a") . "\r\n");
		fwrite($f, "invoice: \r\n");
		fwrite($f, $invoiceDump);
		fwrite($f, "Functions Stack: \r\n");
		fwrite($f, $functionsStackDump);
		fclose($f);
	}

	private function dueInvoice($invoiceID, $newDueAmount) {

		$query = "UPDATE " . TB_VPS_INVOICE . " " .
				"SET due = '" . $newDueAmount . "', " .
				"paid = (total - " . $newDueAmount . "), " .
				"status = 'DUE' " . ////////	'due' ????
				"WHERE invoice_id = " . $invoiceID;
		$this->db->query($query);

		$invoiceDetails = $this->getInvoiceDetails($invoiceID);
	}

	private function cloneInvoice($invoiceID) {

		$query = "INSERT INTO " . TB_VPS_INVOICE . " ( customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable, currency_id) " .
				"(SELECT customer_id, one_time_charge, amount, discount, total, paid, due, balance, generation_date, suspension_date, period_start_date, period_end_date, billing_info, limit_info, custom_info, status, suspension_disable, currency_id " .
				"FROM " . TB_VPS_INVOICE . " " .
				"WHERE invoice_id = " . $invoiceID . " )";
		$this->db->query($query);

		$insertedID = $this->db->getLastInsertedID(); //mysql_insert_id(); OLD

		$payment = (isset($this->payment)) ? $this->payment : new Payment($this->db);
		$payment->clonePayments($invoiceID, $insertedID);

		return $insertedID;
	}

	private function emailNotification($customers, $action) {
		$email = new EMail();
		$from = VPS_SENDER_EMAIL;

		$config = $this->loadConfig();

		switch ($action) {
			case 'new':
				$subject = $config['new_invoice_email_subject'];
				$message = $config['new_invoice_email_message'];
				if (!is_array($customers)) {
					$customers = array($customers);
				}
				foreach ($customers as $customer) {
					$to = $this->getCustomerEmail($customer);
					//	$email->sendMail($from, $to, $subject, $message);
				}

				break;
		}
	}

	private function loadConfig() {

		$query = "SELECT * FROM " . TB_VPS_CONFIG;
		$this->db->query($query);
		if ($this->db->num_rows()) {
			$numRows = $this->db->num_rows();
			for ($i = 0; $i < $numRows; $i++) {
				$data = $this->db->fetch($i);
				$config[$data->name] = stripslashes($data->value);
			}
			return $config;
		} else {
			return false;
		}
	}

	private function getCustomerEmail($customerID) {
		$vps2voc = new VPS2VOC($this->db);
		$customerDetails = $vps2voc->getCustomerDetails($customerID);
		return $customerDetails["email"];
	}

	//	get all payment methods array
	private function getPaymentMethods($paymentMethodID = false) {
		//$this->db->select_db(DB_NAME);

		if ($paymentMethodID) {

			//	get payment method by ID
			$query = "SELECT * FROM " . TB_PAYMENT_METHOD . " WHERE id = " . $paymentMethodID;
			$this->db->query($query);
			if ($this->db->num_rows()) {
				$methodData = $this->db->fetch(0);
				$method = array(
					'id' => $methodData->id,
					'method' => $methodData->payment_method,
				);
				return $method;
			} else {
				return false;
			}
		} else {

			//	return all methods
			$query = "SELECT * FROM " . TB_PAYMENT_METHOD;
			$this->db->query($query);

			if ($this->db->num_rows()) {
				$methodsData = $this->db->fetch_all();
				foreach ($methodsData as $methodData) {
					$method = array(
						'id' => $methodData->id,
						'method' => $methodData->payment_method,
					);
					$methods[] = $method;
				}
				return $methods;
			} else {
				return false;
			}
		}
	}

}

?>