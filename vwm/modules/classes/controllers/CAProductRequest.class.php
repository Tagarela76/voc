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
		
		$sql = "SELECT npr.product_id, npr.supplier, npr.description, npr.date, mf.real_name, u.username".
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
                        
			$productRequests[] = $productRequest;	    	
		}
		
		$this->smarty->assign('productRequests', $productRequests);
		$this->smarty->assign('tpl', 'tpls/productRequest.tpl');
	}
}
?>