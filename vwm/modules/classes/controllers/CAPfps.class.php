<?php

class CAPfps extends Controller {
	
	function CAPfps($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='pfps';
		$this->parent_category='pfps';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$bookmark=$this->getFromRequest('bookmark');
		
		        $manager = new BookmarksManager($this->db);
                $bookmarksList = $manager->getBookmarksListSupplier();                
                $this->smarty->assign("bookmarks",$bookmarksList);
				
				
	

				
				/*** calculating the length of the menu !!!!!!!!!!!!!!!NEED REWRITE!!!!!!!!!!
				$bmcount = $manager->getCountSupplier();
				for ($i=0;$i<$bmcount;$i++){
					if ($_GET['subBookmark'] == $bookmarksList[$i]['supplier_id']){
					$check = $i;break; }
				}
				if ($check < round($bmcount/3)){
					$tmp = $check;
					$indent = $check;
				}
				if ( round($bmcount/3) <= $check AND $check <= round(2*($bmcount/3))){
				
				$indent = $check + $check/5 ;	
				}
				if ($check > round(2*($bmcount/3))){
				$indent = $check+$check/3 ;	
				}	
				
				$this->smarty->assign('selectedBookmark',round($indent));	
***/

	//	$manager = new PFPManager($this->db);
	//	$pfps = $manager->getList();
	//	$this->smarty->assign('itemsCount', count($pfps));
		$jsSources = array  ('modules/js/checkBoxes.js',
                             'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
	//	$this->smarty->assign('pfps', $pfps);
	//	$this->smarty->assign('childCategoryItems', $pfps);
		//$this->smarty->assign('tpl', 'tpls/pfpLibraryClass.tpl');
		
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');
		$this->smarty->display("tpls:index.tpl");
	
	}
	
/*	protected function actionBrowseCategory($vars) {			
		$this->bookmarkPfpLibrary($vars);
	}
	
	
	
	protected function bookmarkPfps($vars) {
		extract($vars);
				
		$manager = new PFPManager($this->db);
		$pfps = $manager->getList();
		$this->smarty->assign('itemsCount', count($pfps));
		$jsSources = array  ('modules/js/checkBoxes.js',
                             'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign('tpl', 'tpls/pfpLibraryClass.tpl');
		
	}*/
/*	
	private function actionViewDetails() {
		$manager = new PFPManager($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$pfp = $manager->getPFP($this->getFromRequest("id"));
		$this->smarty->assign("deleteUrl","admin.php?action=deleteItem&category=tables&bookmark=pfpLibrary&id={$this->getFromRequest("id")}");
		$this->smarty->assign("editUrl","admin.php?action=edit&category=tables&bookmark=pfpLibrary&id={$_GET['id']}");
		$this->smarty->assign('companyListPFP', $companyListPFP);
		$this->smarty->assign("pfp",$pfp);
		$this->smarty->assign("request",$this->getFromRequest());
		$this->smarty->assign('tpl', 'tpls/viewPfpLibrary.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$manager = new PFPManager($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$id = $this->getFromRequest("id");
		$pfp = $manager->getPFP($id);
		
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		
		//	Getting Product list
		$productsIDArray = array();
		foreach($pfp->products as $p) {
			$productsIDArray[] = $p->product_id;
		}

		//$productsListGrouped = $this->getProductsListGrouped($companyID,$productsIDArray);
		$this->smarty->assign('products', $productsListGrouped);
		
		$this->smarty->assign('companyList', $companyList);
		$this->smarty->assign('companyListPFP', $companyListPFP);
		$jsSources = array ('modules/js/flot/jquery.flot.js',
							'modules/js/addPFP.js',
							'modules/js/PopupWindow.js', 
							'modules/js/checkBoxes.js',
							'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
							'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
							'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js',
							'modules/js/companiesPopup.js');
        $this->smarty->assign('jsSources',$jsSources);
		$this->smarty->assign("productCount",$pfp->getProductsCount());
		$this->smarty->assign("pfp",$pfp);
		$this->smarty->assign("edit",true);
		$this->smarty->assign("sendFormAction","admin.php?action=confirmEdit&category=pfpLibrary&id=".$this->getFromRequest('id'));
		$this->smarty->assign("request",$_GET);
		$this->smarty->assign('tpl','tpls/addPfpLibrary.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionConfirmEdit(){
		$formGet = $this->getFromRequest();
		$form = $this->getFromPost();
		$pfp_primary_product_id = $form['pfp_primary'];
		$productCount = intval($form['productCount']);
		$departmentID = intval($formGet['departmentID']);
		$descr = $form['pfp_description'];
		$products = array();

		for($i=0; $i<$productCount; $i++) {
			$productID = $form["product_{$i}_id"];
			$ratio = $form["product_{$i}_ratio"];

			$product = new PFPProduct($this->db);
			$product->setRatio($ratio);
			$product->initializeByID($productID);
			if($productID == $pfp_primary_product_id) {
				$product->setIsPrimary(true);
			} else {
				$product->setIsPrimary(false);
			}

			$products[] = $product;
		}
		
		// process industry types
			$company = new Company($this->db);
			$companyList = $company->getCompanyList();
			
			for ($i=0; $i<count($companyList); $i++){
				if (!is_null($this->getFromPost('company_'.$i))){
					foreach ($companyList as $item) {
						if ($this->getFromPost('company_'.$i) == $item['id']){
							$companyAllList[] = $item;
						}
					}
				}
			} 

		$manager = new PFPManager($this->db);
		$pfpOld = $manager->getPFP($this->getFromRequest('id'));
		$pfp = new PFP($products);
		$pfp->setDescription($descr);
		$pfp->setID($this->getFromRequest('id'));
		$manager->update($pfpOld, $pfp);
		$manager->unassignPFPFromCompanies($this->getFromRequest('id'));
		$pfpID = $this->getFromRequest('id');
		foreach ($companyAllList as $companyItem){
			$manager->assignPFP2Company($pfpID, $companyItem['id']);
		}
		header("Location: admin.php?action=viewDetails&category=pfpLibrary&id=".$pfpID);
	}

	private function actionAddItem() {
		$this->smarty->assign('show', false);
		$this->smarty->assign('tpl', 'tpls/addPfpLibraryTEMP.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$manager = new PFPManager($this->db);
		$idArray = is_array($this->getFromRequest("id")) ? $this->getFromRequest("id") : array($this->getFromRequest("id"));
		
		$pfps = $manager->getList(null,null,$idArray);
		
		$this->smarty->assign("cancelUrl", "admin.php?action=browseCategory&category=pfpLibrary");

		foreach ($pfps as $p) {
				$delete["id"] =	$p->getId();
				$delete["name"] = $p->getDescription();
				$itemForDelete[] = $delete;
		}
		//var_dump($itemForDelete); die();
		$this->smarty->assign("gobackAction","browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}
			
	private function actionConfirmDelete() {
		$itemsCount = $this->getFromRequest('itemsCount');
		for ($i=0; $i<$itemsCount; $i++){
			$id = $this->getFromRequest('item_'.$i);
			$itemID[] = $id;
		}

		$manager = new PFPManager($this->db);
		$pfpList = $manager->getList(null,null,$itemID);
		$manager->removeList($pfpList);
		header("Location: admin.php?action=browseCategory&category=pfpLibrary");
		die();
	}*/
}
?>