<?php


class NewProductRequestManager {
	/**	 
	 * @var db
	 */
	private $db;
	
	protected $emailService;


	public function __construct(db $db) {
		$this->db = $db;
	}
	
	public function setEmailService($emailService) {
		$this->emailService = $emailService;
	}

	/**
	 * TODO: add pagination
	 * @return \NewProductRequest
	 */
	public function getRequestsList() {
		$sql = "SELECT npr.id, npr.product_id, npr.supplier, npr.status, " .
			"npr.description, npr.date, mf.real_name, u.username ".
			   " FROM ".TB_NEW_PRODUCT_REQUEST." npr ".
               " LEFT JOIN ".TB_MSDS_FILE." mf ON npr.msds_id=mf.msds_file_id ".
               " LEFT JOIN ".TB_USER." u ON npr.user_id=u.user_id ".
               " ORDER BY npr.date DESC";
		$this->db->query($sql);
		$rows = $this->db->fetch_all();
		$productRequests = array();
		foreach($rows as $row) {
		    $productRequest = new NewProductRequest($this->db);
			$productRequest->setId($row->id);
		    $productRequest->setSupplier($row->supplier);
			$productRequest->setProductId($row->product_id);
			$productRequest->setName($row->name);
			$productRequest->setDescription($row->description);
			$productRequest->setDate(DateTime::createFromFormat('U', $row->date));
			$productRequest->setStatus($row->status);
			
			$productRequest->setUser(new stdClass());
            $productRequest->getUser()->username = $row->username;
			
			$productRequest->setMsds(new stdClass());
            if ($row->real_name !== NULL) {
				$productRequest->getMsds()->name = "../msds/".$row->real_name;
            } else {
				$productRequest->getMsds()->name = null;
            }							
			$productRequests[] = $productRequest;	    	
		}
		
		return $productRequests;
	}
	
	
	/**
	 * Get product request by id. Optimized for view template	 
	 * @param int $id
	 * @return null|\NewProductRequest
	 */
	public function getNewProductRequestDetailedView($id) {
		$sql = "SELECT npr.id, npr.product_id, npr.name, npr.supplier, " .
				"npr.status, npr.description, npr.date, npr.user_id, " .
				"mf.real_name, u.username".
			   " FROM ".TB_NEW_PRODUCT_REQUEST." npr ".
               " LEFT JOIN ".TB_MSDS_FILE." mf ON npr.msds_id=mf.msds_file_id".
               " LEFT JOIN ".TB_USER." u ON npr.user_id=u.user_id".
			   " WHERE npr.id={$this->db->sqltext($id)}";
		$this->db->query($sql);
		
		if($this->db->num_rows() == 0) {
			return NULL;
		}
		
		$row = $this->db->fetch(0);
		
		$productRequest = new NewProductRequest($this->db);
		$productRequest->setId($row->id);
		$productRequest->setSupplier($row->supplier);
		$productRequest->setProductId($row->product_id);
		$productRequest->setName($row->name);
		$productRequest->setDescription($row->description);
		$productRequest->setDate(DateTime::createFromFormat('U', $row->date));
		$productRequest->setStatus($row->status);

		$productRequest->setUser(new stdClass());
		$productRequest->getUser()->username = $row->username;

		$productRequest->setMsds(new stdClass());
		if ($row->real_name !== NULL) {
			$productRequest->getMsds()->name = "../msds/" . $row->real_name;
		} else {
			$productRequest->getMsds()->name = null;
		}
		
		return $productRequest;
	}
	
	/**
	 * Sends email to the super admins	 
	 * @param NewProductRequest $productRequest
	 */
	public function sendNewEmailNotification(NewProductRequest $productRequest) {
		$msg = "New product requested. You can view it here ".
				"http://vocwebmanager.com/vwm/admin.php?action=viewDetails" .
					"&category=productRequest&id=".$productRequest->getId();
		
		$this->emailService->sendMail(
				'newproductrequest@vocwebmanager.com', 
				array('denis.nt@kttsoft.com', 'jgypsyn@gyantgroup.com'), 
				'New Product Request',
				$msg
				);
	}

}

?>
