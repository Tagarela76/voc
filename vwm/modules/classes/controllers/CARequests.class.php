<?php

class CARequests extends Controller {

	function CARequests($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='requests';
		$this->parent_category='requests';
	}

	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}

	private function actionBrowseCategory() {
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
		$this->smarty->assign('tpl', 'tpls/requests.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}
?>