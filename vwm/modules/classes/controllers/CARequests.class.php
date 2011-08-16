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
		$sql = "SELECT * FROM ".TB_NEW_PRODUCT_REQUEST." ORDER BY date DESC";
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
			
			$productRequests[] = $productRequest;	    	
		}

		$this->smarty->assign('productRequests', $productRequests);
		$this->smarty->assign('tpl', 'tpls/requests.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}
?>