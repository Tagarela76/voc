<?php

class CAProductRequest extends Controller {

	function CAProductRequest($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='productRequest';
		$this->parent_category='requests';
	}

	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}

	protected function actionBrowseCategory($vars) {
		$this->bookmarkProductRequest($vars);
	}
	
	protected function bookmarkProductRequest($vars){
		extract($vars);
		
		$sql = "SELECT npr.id, npr.product_id, npr.supplier, npr.status, npr.description, npr.date, mf.real_name, u.username".
			   " FROM ".TB_NEW_PRODUCT_REQUEST." npr ".
               " LEFT JOIN ".TB_MSDS_FILE." mf ON npr.msds_id=mf.msds_file_id".
               " LEFT JOIN ".TB_USER." u ON npr.user_id=u.user_id".
               " ORDER BY npr.date DESC";
		$this->db->query($sql);
		$rows = $this->db->fetch_all();
		$productRequests = array();
		foreach($rows as $row) {
		    $productRequest = new NewProductRequest($this->db);
		    $productRequest->setSupplier($row->supplier);
			$productRequest->setProductId($row->product_id);
			$productRequest->setName($row->name);
			$productRequest->setDescription($row->description);
			$productRequest->setDate(DateTime::createFromFormat('U', $row->date));
			$productRequest->setStatus($row->status);
            $productRequest->setUserName($row->username);
            if ($row->real_name <> NULL) {
				$productRequest->setMsdsName("../msds/".$row->real_name);
            } else {
				$productRequest->setMsdsName(NULL);
            }
			$productRequest->setURL("admin.php?action=viewDetails&category=productRequest&id=".$row->id);
			$productRequest->setStatus($row->status);
			$productRequests[] = $productRequest;	    	
		}
		
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('productRequests', $productRequests);
		$this->smarty->assign('tpl', 'tpls/productRequest.tpl');
	}
	
	private function actionViewDetails() {
		$productRequest = new NewProductRequest($this->db);
		$sql = "SELECT npr.id, npr.product_id, npr.name, npr.supplier, npr.status, npr.description, npr.date, npr.user_id, mf.real_name, u.username".
			   " FROM ".TB_NEW_PRODUCT_REQUEST." npr ".
               " LEFT JOIN ".TB_MSDS_FILE." mf ON npr.msds_id=mf.msds_file_id".
               " LEFT JOIN ".TB_USER." u ON npr.user_id=u.user_id".
			   " WHERE npr.id=".$this->getFromRequest('id');
		$this->db->query($sql);
		$row = $this->db->fetch(0);
		$row->msds_link = "../msds/".$row->real_name;
		$productRequest->setDate(DateTime::createFromFormat('U', $row->date));
		$row->date = $productRequest->getDate()->format(DEFAULT_DATE_FORMAT);
		$row->back_url = "admin.php?action=browseCategory&category=requests&bookmark=productRequest";
		
		$this->smarty->assign('productRequest', $row);
		$this->smarty->assign('tpl', 'tpls/viewProductRequest.tpl');
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->display("tpls:index.tpl");
	}
}
?>