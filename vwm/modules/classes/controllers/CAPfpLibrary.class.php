<?php
use \VWM\Hierarchy\CompanyManager;


class CAPfpLibrary extends Controller {

	function CAPfpLibrary($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'pfpLibrary';
		$this->parent_category = 'pfps';
	}

	function runAction() {
		$this->runCommon('admin');
		$functionName = 'action' . ucfirst($this->action);
		if (method_exists($this, $functionName))
			$this->$functionName();
	}

	protected function actionBrowseCategory($vars) {
		$this->bookmarkPfpLibrary($vars);
	}

	protected function bookmarkPfpLibrary($vars) {
        $companyId = $this->getFromRequest('companyId');
        if(is_null($companyId) || $companyId == 'All companies'){
            $companyId = 0;
        }
		extract($vars);
		$abc = range('a', 'z');
        
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);
        
		$manager = new PFPManager($this->db);
		$suppl = new BookmarksManager($this->db);
		$pagination = new Paginationabc(1300);

		$pagination->url = "?action=browseCategory&category=pfps&bookmark=pfpLibrary";
		$this->smarty->assign('paginationabc', $pagination);

		//$bookmarksList = $suppl->getBookmarksListSupplier();
		$page = substr($this->getFromRequest("letterpage"), -1);

		$tmp = $suppl->getOriginSupplier();
		$bookmarksList = $tmp;

		if ($page == null) {
			$page = 'a';
		}
		$bookmarks[0]['supplier_id'] = 'custom';
		$bookmarks[0]['supplier'] = 'custom';
		for ($i = 0; $i < count($bookmarksList); $i++) {
			if (strtolower(substr($bookmarksList[$i]['supplier'], 0, 1)) == $page) {
				$bookmarks[] = $bookmarksList[$i];
			}
		}

		$this->smarty->assign("bookmarks", $bookmarks);
		//$pfplist = $manager->getList();

		$sub = $this->getFromRequest("subBookmark");

		$supplierID = $this->getFromRequest('subBookmark');
		$supplierID = (is_null($supplierID) || $supplierID == 'custom') ? 0 : $supplierID;

		//	set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$manager->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}
        
		$pfpsCount = $manager->countPFPAll($companyId, '', $this->getFromRequest('productCategory'), $supplierID);

		$url = "?" . $_SERVER["QUERY_STRING"];
		$url = preg_replace("/\&page=\d*/", "", $url);

		$pagination = new Pagination($pfpsCount);
		$pagination->url = $url;
		$this->smarty->assign('pagination', $pagination);

		$productCategory = ($this->getFromRequest('productCategory')) ? $this->getFromRequest('productCategory') : 0;
        if($companyId == 0){
            $pfps = $manager->getListAll(null, $pagination, null, $productCategory, $supplierID);
        }else{
            $pfps = $manager->getListAll($companyId, $pagination, null, $productCategory, $supplierID);
        }

        $this->smarty->assign('currentCompany', $companyId);
        $this->smarty->assign('pfpsCount', $pfpsCount);
		$this->smarty->assign('itemsCount', count($pfps));
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign("abctabs", $abc);
		$this->smarty->assign('tpl', 'tpls/pfpLibraryClass.tpl');
		$jsSources = array('modules/js/checkBoxes.js', 'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
	}

	private function actionViewDetails() {
		$manager = new PFPManager($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$pfp = $manager->getPFP($this->getFromRequest("id"));
		$this->smarty->assign("deleteUrl", "admin.php?action=deleteItem&category=pfps&bookmark=pfpLibrary&id={$this->getFromRequest("id")}&letterpagee={$this->getFromRequest("letterpage")}&productCategory={$this->getFromRequest("productCategory")}");
		$this->smarty->assign("editUrl", "admin.php?action=edit&category=pfps&bookmark=pfpLibrary&subBookmark={$_GET['subBookmark']}&id={$_GET['id']}&page={$_GET['page']}&productCategory={$this->getFromRequest("productCategory")}");
		$this->smarty->assign('companyListPFP', $companyListPFP);
		$this->smarty->assign("pfp", $pfp);
		$this->smarty->assign("request", $this->getFromRequest());
		$this->smarty->assign('tpl', 'tpls/viewPfpLibrary.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit() {
		$manager = new PFPManager($this->db);
		$pmanager = new Product($this->db);
		$type = new ProductTypes($this->db);
        $industryType = new IndustryType($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$id = $this->getFromRequest("id");
		$pfp = $manager->getPFP($id);

		$company = new Company($this->db);
		$companyList = $company->getCompanyList();

		$sub = $this->getFromRequest("subBookmark");
		if ($sub == 'custom') {

			$pfpproduct = $pmanager->getProductList();
		} else {
			$pfpproduct = $manager->getPFPProductsbySopplier($sub);
		}

		/* SORT PRODUCT BY TYPES */
/*		if ($this->getFromRequest('productCategory') != 0) {  / TODO I don't know WTF???????????????
			$SubTypes = $type->getSubTypesByTypeID($this->getFromRequest('productCategory'));
			$ProductsByType = $type->getProductsByType($this->getFromRequest('productCategory'));
			if (isset($SubTypes)) {
				for ($i = 0; $i < count($SubTypes); $i++) {
					$ProductsByType = array_merge($ProductsByType, $type->getProductsByType($SubTypes[$i]['id']));
				}
			}

			for ($j = 0; $j < count($ProductsByType); $j++) {
				for ($i = 0; $i < count($pfpproduct); $i++) {
					if ($pfpproduct[$i]['product_id'] == $ProductsByType[$j]['product_id']) {
						$productspfp[] = $pfpproduct[$i];
					}
				}
			}
			$pfpproduct = $productspfp;
		}
*/
	
		$this->smarty->assign('products', $pfpproduct);

		$this->smarty->assign('companyList', $companyList);
		$this->smarty->assign('companyListPFP', $companyListPFP);
		$jsSources = array('modules/js/flot/jquery.flot.js',
			'modules/js/addPFP.js',
			'modules/js/PopupWindow.js',
			'modules/js/checkBoxes.js',
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js',
			'modules/js/companiesPopup.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("productCount", $pfp->getProductsCount());
		$this->smarty->assign("pfp", $pfp);
		$this->smarty->assign("edit", true);
		$this->smarty->assign("sendFormAction", "admin.php?action=confirmEdit&category=pfpLibrary&subBookmark=" . $this->getFromRequest('subBookmark') . "&id=" . $this->getFromRequest('id') . "&letterpage=" . $this->getFromRequest('letterpage') . "&productCategory=" . $this->getFromRequest("productCategory") . "");
		$this->smarty->assign("request", $_GET);
		$this->smarty->assign('show', true);
		$this->smarty->assign('tpl', 'tpls/addPfpLibraryTEMP.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionConfirmEdit() {
		$formGet = $this->getFromRequest();
		$form = $this->getFromPost();
		$pfp_primary_product_id = $form['pfp_primary'];
		$productCount = intval($form['productCount']);
		$departmentID = intval($formGet['departmentID']);
		$descr = $form['pfp_description'];
		$products = array();

		for ($i = 0; $i < $productCount; $i++) {
			$productID = $form["product_{$i}_id"];
			$ratio = $form["product_{$i}_ratio"];

			$product = new PFPProduct($this->db);
			$product->setRatio($ratio);
			$product->initializeByID($productID);
			if ($productID == $pfp_primary_product_id) {
				$product->setIsPrimary(true);
			} else {
				$product->setIsPrimary(false);
			}

			$products[] = $product;
		}

		// process industry types
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();

		for ($i = 0; $i < count($companyList); $i++) {
			if (!is_null($this->getFromPost('company_' . $i))) {
				foreach ($companyList as $item) {
					if ($this->getFromPost('company_' . $i) == $item['id']) {
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
		foreach ($companyAllList as $companyItem) {
			$manager->assignPFP2Company($pfpID, $companyItem['id']);
		}
		header("Location: admin.php?action=viewDetails&category=pfpLibrary&bookmark=pfps&subBookmark=" . $this->getFromRequest('subBookmark') . "&id=" . $pfpID . "&letterpage=" . $this->getFromRequest('letterpage') . "&productCategory=" . $this->getFromRequest("productCategory") . "");
	}

	private function actionAddItem() {
		$manager = new PFPManager($this->db);
		$pmanager = new Product($this->db);
		$type = new ProductTypes($this->db);
		$companyListPFP = $manager->getCompaniesByPfpID($this->getFromRequest('id'));
		$id = $this->getFromRequest("id");
		$pfp = $manager->getPFP($id);


		$sub = $this->getFromRequest("subBookmark");
		if ($sub == 'custom') {

			$pfpproduct = $pmanager->getProductList();
		} else {
			$pfpproduct = $manager->getPFPProductsbySopplier($sub);
		}

		/* SORT PRODUCT BY TYPES */
/*		if ($this->getFromRequest('productCategory') != 0) { TODO WTF?????????????
			$SubTypes = $type->getSubTypesByTypeID($this->getFromRequest('productCategory'));
			$ProductsByType = $type->getProductsByType($this->getFromRequest('productCategory'));
			if (isset($SubTypes)) {
				for ($i = 0; $i < count($SubTypes); $i++) {
					$ProductsByType = array_merge($ProductsByType, $type->getProductsByType($SubTypes[$i]['id']));
				}
			}

			for ($j = 0; $j < count($ProductsByType); $j++) {
				for ($i = 0; $i < count($pfpproduct); $i++) {
					if ($pfpproduct[$i]['product_id'] == $ProductsByType[$j]['product_id']) {
						$productspfp[] = $pfpproduct[$i];
					}
				}
			}
			$pfpproduct = $productspfp;
		}*/
		
		/* SORT PRODUCT BY TYPES */


		$company = new Company($this->db);
		$companyList = $company->getCompanyList();

		//	Getting Product list
		$productsIDArray = array();
		/* foreach($pfp->products as $p) {
		  $productsIDArray[] = $p->product_id;
		  }

		  //$productsListGrouped = $this->getProductsListGrouped($companyID,$productsIDArray); */
		$this->smarty->assign('products', $pfpproduct);

		$this->smarty->assign('companyList', $companyList);
		$this->smarty->assign('companyListPFP', $companyListPFP);
		$jsSources = array('modules/js/flot/jquery.flot.js',
			'modules/js/addPFP.js',
			'modules/js/PopupWindow.js',
			'modules/js/checkBoxes.js',
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js',
			'modules/js/companiesPopup.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("productCount", $pfp->getProductsCount());
		$this->smarty->assign("pfp", $pfp);

		$this->smarty->assign("sendFormAction", "admin.php?action=confirmAddItem&category=pfpLibrary&subBookmark=" . $this->getFromRequest('subBookmark') . "&id=" . $this->getFromRequest('id') . "&letterpage=" . $this->getFromRequest('letterpage') . "&productCategory=" . $this->getFromRequest('productCategory') . "");
		$this->smarty->assign("request", $_GET);

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

		for ($i = 0; $i < $productCount; $i++) {
			$productID = $form["product_{$i}_id"];
			$ratio = $form["product_{$i}_ratio"];

			$product = new PFPProduct($this->db);
			$product->setRatio($ratio);
			$product->initializeByID($productID);
			if ($productID == $pfp_primary_product_id) {
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

		for ($i = 0; $i < count($companyList); $i++) {
			if (!is_null($this->getFromPost('company_' . $i))) {
				foreach ($companyList as $item) {
					if ($this->getFromPost('company_' . $i) == $item['id']) {
						$companyAllList[] = $item;
					}
				}
			}
		}

		$manager = new PFPManager($this->db);
		$manager->add($pfp, $companyAllList);
		header("Location: ?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=" . $formGet['subBookmark'] . "&letterpage=" . $this->getFromRequest('letterpage') . "&productCategory=" . $this->getFromRequest("productCategory") . "");
	}

	private function actionDeleteItem() {
		$manager = new PFPManager($this->db);
		$idArray = is_array($this->getFromRequest("id")) ? $this->getFromRequest("id") : array($this->getFromRequest("id"));

	//	$pfps = $manager->getList(null, null, $idArray);

		$this->smarty->assign("cancelUrl", "admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=" . $this->getFromRequest('subBookmark') . "&letterpage=" . $this->getFromRequest('letterpage') . "&productCategory=" . $this->getFromRequest("productCategory") . "");

	/*	foreach ($pfps as $p) {
			$delete["id"] = $p->getId();
			$delete["name"] = $p->getDescription();
			$itemForDelete[] = $delete;
		}*/
		$itemForDelete = array();
		foreach ($idArray as $id) {
			$pfp = $manager->getPFP($id); 
			$delete["id"] = $id;
			$delete["name"] = $pfp->getDescription();
			$itemForDelete[] = $delete;
		}
		
		$this->smarty->assign("gobackAction", "browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}

	private function actionConfirmDelete() {
		
		$itemIDs = array();
		$itemsCount = $this->getFromRequest('itemsCount');
		for ($i = 0; $i < $itemsCount; $i++) {
			$id = $this->getFromRequest('item_' . $i);
			$itemIDs[] = $id;
		}

		$manager = new PFPManager($this->db);

		foreach ($itemIDs as $itemID) {
			$manager->unassignPFPFromCompanies($itemID);
			$pfp = $manager->getPFP($itemID);
			$manager->remove($pfp);
		}		
		
		header("Location: admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=" . $this->getFromRequest('subBookmark') . "&letterpage=" . $this->getFromRequest('letterpage') . "&productCategory=" . $this->getFromRequest("productCategory") . "");
		die();
	}

	/**
	 * TODO: refactor needed
	 */
	protected function actionAccessToCompany() {
		$supplierObj = new Supplier($this->db);
		$supplierDetails = $supplierObj->getSupplierDetails($this->getFromRequest('supplier'));
		if(!$supplierDetails) {
			throw new Exception(404);
		}
		
		if ($_POST['assign'] == "Assign") {
			$industry_type = $_POST['industryType'];
			$company_id = $_POST['company'];
			$cPFPManager = new PFPManager($this->db);

			$filterBySupplier = ($this->getFromRequest('supplier') != 'custom')
					? ' AND p.supplier_id = '.$this->db->sqltext($this->getFromRequest('supplier')).' '
					: '';

			// $query - get all PFPs where primary product belongs to $industry_type
			$query = "SELECT pfp.id, pfp.description FROM " . TB_PFP . " pfp, " . TB_PFP2PRODUCT . " p2p, " . TB_PRODUCT . " p, " . TB_PRODUCT2INDUSTRY_TYPE . " p2t" .
					" WHERE p2p.preformulated_products_id = pfp.id " .
					" AND p2p.product_id = p.product_id " .
					" AND p.product_id = p2t.product_id " .
					" AND p2p.isPrimary = 1 " .
					$filterBySupplier.
					" AND (p2t.industry_type_id IN " .
					" (SELECT id FROM " . TB_INDUSTRY_TYPE .
					" WHERE parent = {$this->db->sqltext($industry_type)}) OR p2t.industry_type_id = {$this->db->sqltext($industry_type)})";
			$query .= " GROUP BY pfp.id";

			$this->db->query($query);
			$pfp_list = $this->db->fetch_all_array();
			// $query_pfp2company - get all relations PFP2Company
			$query_pfp2company = "SELECT * FROM " . TB_PFP2COMPANY . " WHERE 1";
			$this->db->query($query_pfp2company);
			$pfp2company = $this->db->fetch_all_array();
			//$result_log = "";
			foreach ($pfp_list as $pfp_list_item) {
				foreach ($company_id as $company_id_item) {
					$already_assign = false;
					foreach ($pfp2company as $pfp2company_item) {
						if ((intval($pfp_list_item['id']) == intval($pfp2company_item['pfp_id']))
								&& (intval($company_id_item) == intval($pfp2company_item['company_id']))) {
							$already_assign = true;  // is $pfp_list_item assigned to $company_id_item
						}
					}
					//if (!$already_assign) { // assign it if not assigned yet
					$cPFPManager->availablePFP2Company(intval($pfp_list_item['id']), intval($company_id_item));
					//$result_log .= "<b>Success!</b> ".$pfp_list_item['description']." assigned to company ".$company_id_item."<br/>";
					//} else {
					//$result_log .= "<b>Error!</b> ".$pfp_list_item['description']." is already assigned to company ".$company_id_item."<br/>";
					//}
				}
			}
			//empty($result_log) ? $result_log = "PFP's were not assigned to companies.<br/>" : "";
			//$this->smarty->assign("log", $result_log);
			header("Location: admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary");
		} else if ($_POST['unassign'] == "Unassign") {
			$industry_type = $_POST['industryType'];
			$company_id = $_POST['company'];
			$cPFPManager = new PFPManager($this->db);

			$filterBySupplier = ($this->getFromRequest('supplier') != 'custom')
					? ' AND p.supplier_id = '.$this->db->sqltext($this->getFromRequest('supplier')).' '
					: '';

			// $query - get all PFPs where primary product belongs to $industry_type
			$query = "SELECT pfp.id, pfp.description FROM " . TB_PFP . " pfp, " . TB_PFP2PRODUCT . " p2p, " . TB_PRODUCT . " p, " . TB_PRODUCT2INDUSTRY_TYPE . " p2t" .
					" WHERE p2p.preformulated_products_id = pfp.id " .
					" AND p2p.product_id = p.product_id " .
					" AND p.product_id = p2t.product_id " .
					" AND p2p.isPrimary = 1 " .
					$filterBySupplier.
					" AND (p2t.industry_type_id IN " .
					" (SELECT id FROM " . TB_INDUSTRY_TYPE .
					" WHERE parent = {$this->db->sqltext($industry_type)}) OR p2t.industry_type_id = {$this->db->sqltext($industry_type)})";
			$query .= " GROUP BY pfp.id";
			$this->db->query($query);
			$pfp_list = $this->db->fetch_all_array();
			foreach ($pfp_list as $pfp_list_item) {
				foreach ($company_id as $company_id_item) {
					$cPFPManager->unavailablePFPFromCompany(intval($pfp_list_item['id']), intval($company_id_item));
				}
			}
			header("Location: admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary");
		}
		// get all industry types and sub-types
		$industryType = new IndustryType($this->db);
		$industryTypeList = $industryType->getTypesWithSubTypes();
		$this->smarty->assign("typesList", $industryTypeList);

		// get company list
		$cCompany = new Company($this->db);
		$company_list = $cCompany->getCompanyList();
		$this->smarty->assign("companyList", $company_list);		
		$this->smarty->assign("supplierDetails",$supplierDetails);

		$jsSources = array('modules/js/checkBoxes.js');

		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('request', $this->getFromRequest());
		$this->smarty->assign('tpl', 'tpls/accessToCompany.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
    
    protected function actionAssignPfpToComapny()
    {
        $subaction = $this->getFromRequest('subaction');
        $companyId = $this->getFromRequest('companyId');
        $companyManager = new CompanyManager();
        $companyIds = array();
        if($companyId = 'All Companies'){
            $companyList = $companyManager->getCompanyList();
            foreach ($companyList as $company){
                $companyIds[] = $company['id'];
            }
            
        }else{
            $companyIds[] = $companyId;
        }
        
        $pfpIds = $this->getFromRequest('id');
        $pfpManager = new \VWM\Apps\WorkOrder\Manager\PfpManager();
        
        foreach ($pfpIds as $pfpId) {
            foreach ($companyIds as $id) {
                if ($subaction == 'Unassign product(s)') {
                    $pfpManager->unAssignPFP2Company($pfpId, $id);
                } else {
                    $pfpManager->assignPFP2Company($pfpId, $id);
                }
            }
        }
        
        header("Location: admin.php?action=browseCategory&category=pfps&bookmark=pfpLibrary&subBookmark=custom&letterpage=1a&productCategory=0&companyId=".$companyId);
        die();
    }
    protected function actionFilter()
    {
        //$bookmark = $this->getFromRequest('bookmark');
        $companyId = $this->getFromRequest('companyId');
        
        $industryType = new IndustryType($this->db);	 
		$productIndustryTypeList = $industryType->getTypesWithSubTypes();
		$this->smarty->assign("productTypeList", $productIndustryTypeList);
        
		extract($vars);
        
		$abc = range('a', 'z');
        //get company List
        $companyManager = new CompanyManager();
        $companyList = $companyManager->getCompanyList();
        $this->smarty->assign('companyList', $companyList);
        
        
		$manager = new PFPManager($this->db);
		$suppl = new BookmarksManager($this->db);
		$pagination = new Paginationabc(1300);

		$pagination->url = "?action=browseCategory&category=pfps&bookmark=pfpLibrary";
		$this->smarty->assign('paginationabc', $pagination);

		
		$page = substr($this->getFromRequest("letterpage"), -1);

		$tmp = $suppl->getOriginSupplier();
		$bookmarksList = $tmp;

		if ($page == null) {
			$page = 'a';
		}
		$bookmarks[0]['supplier_id'] = 'custom';
		$bookmarks[0]['supplier'] = 'custom';
		for ($i = 0; $i < count($bookmarksList); $i++) {
			if (strtolower(substr($bookmarksList[$i]['supplier'], 0, 1)) == $page) {
				$bookmarks[] = $bookmarksList[$i];
			}
		}

		$this->smarty->assign("bookmarks", $bookmarks);
		//$pfplist = $manager->getList();

		$sub = $this->getFromRequest("subBookmark");

		$supplierID = $this->getFromRequest('subBookmark');
		$supplierID = (is_null($supplierID) || $supplierID == 'custom') ? 0 : $supplierID;

		//	set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$manager->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}

        if ($companyId == 'All companies') {
            $companyId = 0;
        }
        $pfpsCount = $manager->countPFPAll($companyId, '', $this->getFromRequest('productCategory'), $supplierID);

		$url = "?" . $_SERVER["QUERY_STRING"];
		$url = preg_replace("/\&page=\d*/", "", $url);

		$pagination = new Pagination($pfpsCount);
		$pagination->url = $url;
		$this->smarty->assign('pagination', $pagination);

		$productCategory = ($this->getFromRequest('productCategory')) ? $this->getFromRequest('productCategory') : 0;
        if ($companyId == 'All companies' || $companyId==0) {
            $pfps = $manager->getListAll(null, $pagination, null, $productCategory, $supplierID);
        }else{
            $pfps = $manager->getListAll($companyId, $pagination, null, $productCategory, $supplierID);
        }

        $this->smarty->assign('currentCompany', $companyId);
        $this->smarty->assign('pfpsCount', $pfpsCount);
		$this->smarty->assign('itemsCount', count($pfps));
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign("abctabs", $abc);
		$this->smarty->assign('tpl', 'tpls/pfpLibraryClass.tpl');
		$jsSources = array('modules/js/checkBoxes.js', 'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
        
        $request['action'] = 'browseCategory';
        $request['category'] = "pfps";
        $request['bookmark']='pfpLibrary';
        $request['productCategory'] = $productCategory;
        
        $this->smarty->assign('request', $request);
		$this->smarty->display("tpls:index.tpl");
    }

}

?>