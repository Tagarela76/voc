<?php

class CAPfpLibrary extends Controller {
	
	function CAPfpLibrary($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='pfpLibrary';
		$this->parent_category='pfps';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	/*private function actionBrowseCategory() {
		$bookmark=$this->getFromRequest('bookmark');
		
		        $manager = new BookmarksManager($this->db);
                $bookmarksList = $manager->getBookmarksListSupplier();                
                $this->smarty->assign("bookmarks",$bookmarksList);
				
				
				$bmcount = $manager->getCountSupplier();
				for ($i=0;$i<$bmcount;$i++){
					if ($_GET['subBookmark'] == $bookmarksList[$i]['supplier_id']){
					$check = $i;break; }
				}
				if ($check < $bmcount/3){
					$tmp = $check;
					$indent = $check/2;
				}
				if ( $bmcount/3 <= $check AND $check < 2*($bmcount/3)){
				
				$indent = $check/2 + $check/10;	
				}
				if ($check >= 2*($bmcount/3)){
				$indent = $check/2 + 2*($check/10) ;	
				}	
				if ($check == $bmcount){
				$indent = $check/2 + 2*($check/10) ;	
				}
				$this->smarty->assign('selectedBookmark',$indent);	


		//die(var_dump($_GET,$bookmarksList));
		//$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');	
		
		//FILTER

		$filter=new Filter($this->db,$bookmark);

		$this->smarty->assign('filterArray',$filter->getJsonFilterArray());
		$filterData= array
			(
				'filterField'=>$this->getFromRequest('filterField'),
				'filterCondition'=>$this->getFromRequest('filterCondition'),
				'filterValue'=>$this->getFromRequest('filterValue')
			);

		if ($this->getFromRequest('searchAction')=='filter') {
			$this->smarty->assign('filterData',$filterData);
			$this->smarty->assign('searchAction','filter');
		}
		$filterStr = $filter->getSubQuery($filterData);
		//FILTER

		//SORT
		if (!is_null($this->getFromRequest('sort')))
		{
			$sort= new Sort($this->db,$bookmark,0);
			$sortStr = $sort->getSubQuerySort($this->getFromRequest('sort'));
			$this->smarty->assign('sort',$this->getFromRequest('sort'));
		}
		else
			$this->smarty->assign('sort',0);

		if (!is_null($this->getFromRequest('searchAction')))
			$this->smarty->assign('searchAction',$this->getFromRequest('searchAction'));
		//SORT

		$vars = array(
				'sortStr' => $sortStr,
				'filterStr' => $filterStr,
				'filterData' => $filterData
			);
				
				
		/*$manager = new PFPManager($this->db);
		$pfps = $manager->getList();
		$this->smarty->assign('itemsCount', count($pfps));
		$jsSources = array  ('modules/js/checkBoxes.js',
                             'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		//$this->smarty->assign('tpl', 'tpls/pfpLibraryClass.tpl');
		
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');
		$this->smarty->display("tpls:index.tpl");
		}*/
	
	protected function actionBrowseCategory($vars) {			
		$this->bookmarkPfpLibrary($vars);
	}
	
	
	
	protected function bookmarkPfpLibrary($vars) {
		extract($vars);
		$abc = range('a','z');

		$manager = new PFPManager($this->db);
		$suppl = new BookmarksManager($this->db);		
		$pagination = new Paginationabc(1300);
		
		$pagination->url = "?action=browseCategory&category=pfps&bookmark=pfpLibrary";
		$this->smarty->assign('paginationabc', $pagination);
		
		//$bookmarksList = $suppl->getBookmarksListSupplier();
		$page = substr($this->getFromRequest("letterpage"),-1);
		
		$tmp = $suppl->getOriginSupplier();
		$bookmarksList = $tmp;
		
		if ($page == null){$page = 'a';}
		$bookmarks[0]['supplier_id'] = 'custom';
		$bookmarks[0]['supplier'] = 'custom';
		for($i=0; $i<count($bookmarksList); $i++) {
			if (strtolower(substr($bookmarksList[$i]['supplier'],0,1)) == $page){
			$bookmarks[] = $bookmarksList[$i];
			}
		}

		$this->smarty->assign("bookmarks",$bookmarks);
		//$pfplist = $manager->getList();
		
		$sub = $this->getFromRequest("subBookmark");
		
	    if ($sub != 'custom'){
			
		$allsub = $suppl->getAllSuppliersByOrigin($sub);
			$i=0;
			while($allsub[$i]){
				$listOFpfp[$i] = $manager->getPfpList($allsub[$i]['supplier_id']);
				$i++;
			}
			$temp = $listOFpfp[0];
			for($i = 0; $i < count($listOFpfp)-1; $i++){
				$temp = array_merge($temp, $listOFpfp[$i+1]);
			}
			$listOFpfp = array_unique($temp);		
		}else{
		$listOFpfp = $manager->getPfpList($sub);	
		}

		$pfps = $manager->getListSpecial(null,null,$listOFpfp);
		
		/*
		$pfplist = $manager->getPfpList($sub);
		$pfps = $manager->getListSpecial(null,null,$pfplist);
		*/
		
		
		$this->smarty->assign('itemsCount', count($pfps));
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign("abctabs",$abc);
		$this->smarty->assign('tpl', 'tpls/pfpLibraryClass.tpl');
		$jsSources = array  ('modules/js/checkBoxes.js','modules/js/autocomplete/jquery.autocomplete.js');		
		$this->smarty->assign('jsSources', $jsSources);
	}
	
	private function actionViewDetails() {
		$manager = new PFPManager($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$pfp = $manager->getPFP($this->getFromRequest("id"));
		$this->smarty->assign("deleteUrl","admin.php?action=deleteItem&category=pfps&bookmark=pfpLibrary&id={$this->getFromRequest("id")}&letterpagee={$this->getFromRequest("letterpage")}&productCategory={$this->getFromRequest("productCategory")}");
		$this->smarty->assign("editUrl","admin.php?action=edit&category=pfps&bookmark=pfpLibrary&subBookmark={$_GET['subBookmark']}&id={$_GET['id']}&page={$_GET['page']}&productCategory={$this->getFromRequest("productCategory")}");
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
		$this->smarty->assign("sendFormAction","admin.php?action=confirmEdit&category=pfpLibrary&subBookmark=".$this->getFromRequest('subBookmark')."&id=".$this->getFromRequest('id')."&letterpage=".$this->getFromRequest('letterpage')."&productCategory=".$this->getFromRequest("productCategory")."");
		$this->smarty->assign("request",$_GET);
		$this->smarty->assign('show',true);
		$this->smarty->assign('tpl','tpls/addPfpLibraryTEMP.tpl');
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
		header("Location: admin.php?action=viewDetails&category=pfpLibrary&bookmark=pfps&subBookmark=".$this->getFromRequest('subBookmark')."&id=".$pfpID."&letterpage=".$this->getFromRequest('letterpage')."&productCategory=".$this->getFromRequest("productCategory")."");
	}

	private function actionAddItem() {
		$manager = new PFPManager($this->db);
		$pmanager = new Product($this->db);
		$type = new ProductTypes($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$id = $this->getFromRequest("id");
		$pfp = $manager->getPFP($id);
		
		
		$sub = $this->getFromRequest("subBookmark");
		if($sub == 'custom'){
			
			$pfpproduct = $pmanager->getProductList();
		}else{
			$pfpproduct = $manager->getPFPProductsbySopplier($sub);
		}
		
		/* SORT PRODUCT BY TYPES*/
		if ($this->getFromRequest('productCategory')!=0){
			$SubTypes = $type->getSubTypesByTypeID($this->getFromRequest('productCategory'));
			$ProductsByType = $type->getProductsByType($this->getFromRequest('productCategory'));
			if(isset($SubTypes)){ 	
				for($i = 0; $i < count($SubTypes); $i++){
					$ProductsByType = array_merge($ProductsByType, $type->getProductsByType($SubTypes[$i]['id']));
				}		
			}

			for($j=0; $j< count($ProductsByType); $j++) {
				for($i=0; $i< count($pfpproduct); $i++) {
					if ($pfpproduct[$i]['product_id'] == $ProductsByType[$j]['product_id']){
						$productspfp[]=$pfpproduct[$i];
					}
				}
			}
			$pfpproduct=$productspfp;
		}
		//var_dump('SubTypes',$SubTypes,'ProductsByType',$ProductsByType,count($productspfp),count($pfpproduct));
		/* SORT PRODUCT BY TYPES*/
		
		
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		
		//	Getting Product list
		$productsIDArray = array();
		/*foreach($pfp->products as $p) {
			$productsIDArray[] = $p->product_id;
		}

		//$productsListGrouped = $this->getProductsListGrouped($companyID,$productsIDArray);*/
		$this->smarty->assign('products', $pfpproduct);
		
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
		
		$this->smarty->assign("sendFormAction","admin.php?action=confirmAddItem&category=pfpLibrary&subBookmark=".$this->getFromRequest('subBookmark')."&id=".$this->getFromRequest('id')."&letterpage=".$this->getFromRequest('letterpage')."&productCategory=".$this->getFromRequest('productCategory')."");
		$this->smarty->assign("request",$_GET);
		
		$this->smarty->assign('show', true);
		$this->smarty->assign('tpl', 'tpls/addPfpLibraryTEMP.tpl');
		$this->smarty->display("tpls:index.tpl");
		}
	
		private function actionConfirmAddItem() {
			$formGet = $this->getFromRequest();
			$form = $this->getFromPost();
			
			$pfp_primary_product_id = $form['pfp_primary'];
			$productCount = intval($form['productCount']);
			
			//$departmentID = intval($formGet['departmentID']);
			
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

			$pfp = new PFP($products);
			$pfp->setDescription($descr);
			
			$companyID = array();
			
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
			$manager->add($pfp,$companyAllList);
			header("Location: ?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=".$formGet['subBookmark']."&letterpage=".$this->getFromRequest('letterpage')."&productCategory=".$this->getFromRequest("productCategory")."");
		}
		
		private function actionDeleteItem() {
		$manager = new PFPManager($this->db);
		$idArray = is_array($this->getFromRequest("id")) ? $this->getFromRequest("id") : array($this->getFromRequest("id"));
		
		$pfps = $manager->getList(null,null,$idArray);
		
		$this->smarty->assign("cancelUrl", "admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=".$this->getFromRequest('subBookmark')."&letterpage=".$this->getFromRequest('letterpage')."&productCategory=".$this->getFromRequest("productCategory")."");

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
		$i=0;
		while($itemID[$i]){
		$manager->unassignPFPFromCompanies($itemID[$i]);
		$i++;
		}
		$manager->removeList($pfpList);
		header("Location: admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=".$this->getFromRequest('subBookmark')."&letterpage=".$this->getFromRequest('letterpage')."&productCategory=".$this->getFromRequest("productCategory")."");
		die();
	}
}
?>