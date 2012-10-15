<?php

class CAProductRequest extends Controller {

	public function __construct ($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='productRequest';
		$this->parent_category='requests';
	}	

	protected function actionBrowseCategory($vars) {
		$this->bookmarkProductRequest($vars);
	}
	
	protected function bookmarkProductRequest($vars){
		extract($vars);
		
		$newProductRequestManager = new NewProductRequestManager($this->db);
		$productRequests = $newProductRequestManager->getRequestsList();
		foreach ($productRequests as $productRequest) {
			$productRequest->url = "admin.php?action=viewDetails&" .
					"category=productRequest&id=".$productRequest->getId();
		}
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->assign('productRequests', $productRequests);
		$this->smarty->assign('tpl', 'tpls/productRequest.tpl');
	}
	
	protected function actionViewDetails() {		
		
		if($this->getFromPost()) {
			$productRequest = new NewProductRequest($this->db, $this->getFromRequest('id'));		
			$productRequest->setStatus($this->getFromPost('status'));
			if(!$productRequest->save()) {
				throw new \Exception("Something wrong. This should not happen");
			}
			header("Location: admin.php?action=browseCategory" .
				"&category=requests&bookmark=productRequest");
		}		
		
		$productRequestManager = new NewProductRequestManager($this->db);
		$productRequest = $productRequestManager->getNewProductRequestDetailedView(
				$this->getFromRequest('id')
				);
		
		$productRequest->back_url = "admin.php?action=browseCategory" .
				"&category=requests&bookmark=productRequest";
		
		$this->smarty->assign('productRequest', $productRequest);
		$this->smarty->assign('tpl', 'tpls/viewProductRequest.tpl');
		$this->smarty->assign('doNotShowControls', true);
		$this->smarty->display("tpls:index.tpl");
	}
}
?>