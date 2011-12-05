<?php

class CAProduct extends Controller {
	
	function CAProduct($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='product';
		$this->parent_category='product';		
	}
	
	function runAction() {
		$this->runCommon('admin');		
		$functionName='action'.ucfirst($this->action);						
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	
	private function actionBrowseCategory() {
		$abc = range('a','z');

		$suppl = new BookmarksManager($this->db);
		$manager = new PFPManager($this->db);
		$paginationabc = new Paginationabc(1300);
		
		$paginationabc->url = "?action=browseCategory&category=product";
		$this->smarty->assign("abctabs",$abc);
		$this->smarty->assign('paginationabc', $paginationabc);
		
		$supplierID = $this->getFromRequest('subBookmark');
		$supplierID = (is_null($supplierID) || $supplierID == 'custom')?0:$supplierID;			
/**BOOKMARKS**/
		$page = substr($this->getFromRequest("letterpage"),-1);
		$supplierList = $suppl->getOriginSupplier();
		$bookmarksList = $supplierList;
		if ($page == null){$page = 'a';}
		$bookmarks[0]['supplier_id'] = 'custom';
		$bookmarks[0]['supplier'] = 'custom';
		for($i=0; $i<count($bookmarksList); $i++) {
			if (strtolower(substr($bookmarksList[$i]['supplier'],0,1)) == $page){
			$bookmarks[] = $bookmarksList[$i];
			}
		}
		$this->smarty->assign("bookmarks",$bookmarks);
/****/		


/**PRODUCTS BY SUPPLIER IN BOOKMARK		
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
**/


		$product = new Product($this->db);
		
		$subaction = $this->getFromRequest('subaction');
		$companyID = $this->getFromRequest('companyID');
		$companyID = (is_null($companyID) || $companyID == 'All companies')?0:$companyID;
		/*
		$supplierID = $this->getFromRequest('supplierID');
		$supplierID = (is_null($supplierID) || $supplierID == 'All suppliers')?0:$supplierID;		
		*/
	
		
		if (!is_null($subaction) && $companyID != 0 && $subaction != 'Filter') {
			$count = $this->getFromRequest('itemsCount');
			for ($i=0;$i<$count;$i++) {										
				if (!is_null($this->getFromRequest('item_'.$i))) {
					$productID = $this->getFromRequest('item_'.$i);
					if ($subaction == "Assign to company") {
						$product->assignProduct2Company($productID, $companyID);	
					} elseif ($subaction == "Unassign product(s)") {
						$product->unassignProductFromCompany($productID, $companyID);
					}											
				}	
			}	
		}
		
		// get Supplier list

		
		$supplierItemsCount=count($supplierList);
		/*$supplier=new Supplier($this->db);
		$supplierList=$supplier->getSupplierList();
		$supplierItemsCount=count($supplierList);
		*/
		$this->smarty->assign('supplierList', $supplierList);
		//	get company list
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		$this->smarty->assign('companyList',$companyList);												
		
		//	search??	!WITHOUT PAGINATION!								
		if (!is_null($this->getFromRequest('q'))) {
			$productsToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));										
			$productList = $product->searchProducts($productsToFind, $companyID);
			
			$this->smarty->assign('currentCompany',0);
			$this->smarty->assign('currentSupplier', 0);												
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		} else {
			/*$allsub = $suppl->getAllSuppliersByOrigin($this->getFromRequest('subBookmark'));				

			$i=0;$tmp=0;
			while($allsub[$i]){
				$productCount = $product->getProductCount($this->getFromRequest('companyID'),$allsub[$i]['supplier_id']);
				$tmp = $tmp + $productCount;
				$i++;
			}*/			
			
			$productCount = $product->getProductCount($this->getFromRequest('companyID'),$supplierID);
			
			//$productCount = $product->getProductCount($this->getFromRequest('companyID'),$this->getFromRequest('supplierID'));	
			$pagination = new Pagination($productCount);
			$pagination->url = "?action=browseCategory&companyID=".$this->getFromRequest('companyID')."&subBookmark=".$this->getFromRequest('subBookmark')."&letterpage=".$this->getFromRequest('letterpage')."&subaction=Filter&category=product";
			$this->smarty->assign('pagination', $pagination);

			if ($supplierID != 0) {
			/**get product by origin and similar suppliers	
	
			$i=0;
			while($allsub[$i]){
				$listOFproduct[$i] = $product->getProductListByMFG($allsub[$i]['supplier_id'], $companyID, $pagination,' TRUE ',$sortStr);
				$i++;
			}		
			$temp = $listOFproduct[0];
			for($i = 0; $i < count($listOFproduct)-1; $i++){
				if (($listOFproduct[$i+1])!=false){
				$temp = array_merge($temp, $listOFproduct[$i+1]);
				}
			}
			$productList = $temp;
					
				
			/***********/	
			$productList = $product->getProductListByMFG($supplierID, $companyID, $pagination,' TRUE ',$sortStr);

			} else {
				$productList = $product->getProductList($companyID, $pagination,' TRUE ',$sortStr);	
			}			
			$this->smarty->assign('currentCompany',$companyID);
			$this->smarty->assign('currentSupplier', $supplierID);
		}				
		$field = 'product_id';
		$list = $productList;
		
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			if (is_null($this->getFromRequest('q'))){
				$url="admin.php?action=viewDetails&category=product&id=".$list[$i][$field]."&subBookmark=".$this->getFromRequest('subBookmark')."&letterpage=".$this->getFromRequest('letterpage')."&page=".$pagination->getCurrentPage();
			} else {
				$url="admin.php?action=viewDetails&category=product&id=".$list[$i][$field]."&subBookmark=".$this->getFromRequest('subBookmark')."&letterpage=".$this->getFromRequest('letterpage')."";
			}
			
			$list[$i]['url']=$url;
		}
		$jsSources = array('modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/productClass.tpl');
		$this->smarty->assign('pagination', $pagination);
		
		$this->smarty->display("tpls:index.tpl");
	}
/*	protected function actionBrowseCategory($vars) {			
		$this->bookmarkProduct($vars);
	}
	
	
	protected function bookmarkProduct($vars) {
		extract($vars);
		
		$product = new Product($this->db);
		
		$subaction = $this->getFromRequest('subaction');
		$companyID = $this->getFromRequest('companyID');
		$supplierID = $this->getFromRequest('supplierID');
		$companyID = (is_null($companyID) || $companyID == 'All companies')?0:$companyID;
		$supplierID = (is_null($supplierID) || $supplierID == 'All suppliers')?0:$supplierID;		
		
		if (!is_null($subaction) && $companyID != 0 && $subaction != 'Filter') {
			$count = $this->getFromRequest('itemsCount');
			for ($i=0;$i<$count;$i++) {										
				if (!is_null($this->getFromRequest('item_'.$i))) {
					$productID = $this->getFromRequest('item_'.$i);
					if ($subaction == "Assign to company") {
						$product->assignProduct2Company($productID, $companyID);	
					} elseif ($subaction == "Unassign product(s)") {
						$product->unassignProductFromCompany($productID, $companyID);
					}											
				}	
			}	
		}
		
		// get Supplier list
		$supplier=new Supplier($this->db);
		$supplierList=$supplier->getSupplierList();
		$supplierItemsCount=count($supplierList);
		$this->smarty->assign('supplierList', $supplierList);						
		
		//	get company list
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		$this->smarty->assign('companyList',$companyList);												
		
		//	search??	!WITHOUT PAGINATION!								
		if (!is_null($this->getFromRequest('q'))) {
			$productsToFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));										
			$productList = $product->searchProducts($productsToFind, $companyID);
			
			$this->smarty->assign('currentCompany',0);
			$this->smarty->assign('currentSupplier', 0);												
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		} else {
			$productCount = $product->getProductCount($this->getFromRequest('companyID'),$this->getFromRequest('supplierID'));			
			$pagination = new Pagination($productCount);
			$pagination->url = "?action=browseCategory&companyID=".$this->getFromRequest('companyID')."&supplierID=".$this->getFromRequest('supplierID')."&subaction=Filter&category=tables&bookmark=product";
			$this->smarty->assign('pagination', $pagination);
			
			if ($supplierID != 0) {
				$productList = $product->getProductListByMFG($supplierID, $companyID, $pagination,' TRUE ',$sortStr);
			} else {
				$productList = $product->getProductList($companyID, $pagination,' TRUE ',$sortStr);	
			}			
			$this->smarty->assign('currentCompany',$companyID);
			$this->smarty->assign('currentSupplier', $supplierID);
		}				
		$field = 'product_id';
		$list = $productList;
		
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			if (is_null($this->getFromRequest('q'))){
				$url="admin.php?action=viewDetails&category=product&id=".$list[$i][$field]."&page=".$pagination->getCurrentPage();
			} else {
				$url="admin.php?action=viewDetails&category=product&id=".$list[$i][$field];
			}
			
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/productClass.tpl');
		$this->smarty->assign('pagination', $pagination);
		
		
	}
	*/
	private function actionViewDetails() {
		$product=new Product($this->db);
		$productDetails=$product->getProductDetails($this->getFromRequest('id'));
		
		//density 
		$cDensity = new Density($this->db, $productDetails['densityUnitID']);
		$densityDetailsTrue = array (
			'numeratorID'	=> $cDensity->getNumerator(),
			'denominatorID'	=> $cDensity->getDenominator(),
			'numerator'		=> '',
			'denominator'	=> ''
		);							
		
		$cUnitType = new Unittype($this->db);
		$unittypeData = $cUnitType->getUnittypeDetails($densityDetailsTrue['numeratorID']);
		$densityDetailsTrue['numerator'] = $unittypeData['name']; 
		$unittypeData = $cUnitType->getUnittypeDetails($densityDetailsTrue['denominatorID']);
		$densityDetailsTrue['denominator'] = $unittypeData['name']; 
		
		$cProductTypes = new ProductTypes($this->db);
		$productType = $cProductTypes->getTypeAndSubTypeByProductID($this->getFromRequest('id'));
		
		$msdsLink = $product->checkForAvailableMSDS($productDetails['product_id']);
		$techSheetLink = $product->checkForAvailableTechSheet($productDetails['product_id']);
		
		$this->smarty->assign('page', $this->getFromRequest('page'));
		$this->smarty->assign('productTypes', $productType);
		$this->smarty->assign('densityDetails', $densityDetailsTrue);
		$this->smarty->assign("product", $productDetails);
		$this->smarty->assign('msdsLink', $msdsLink);
		$this->smarty->assign('techSheetLink', $techSheetLink);
		$this->smarty->assign('tpl', 'tpls/viewProduct.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$cProductTypes = new ProductTypes($this->db);
		$productTypesList = $cProductTypes->getTypesWithSubTypes();
		$this->smarty->assign('productTypeList', $productTypesList);
		$this->smarty->assign('page', $this->getFromRequest('page'));
		$productType = $cProductTypes->getTypeAndSubTypeByProductID($this->getFromRequest('id'));
		$this->smarty->assign('productTypes', $productType);
				
		$product = new Product($this->db);
		$id = $this->getFromRequest('id');
		if (!is_null($this->getFromPost('save')))
		{																																																																						
			$productData = array (
				"product_id"		=>	$id,
				"product_nr"		=>	$this->getFromPost("product_nr"),
				"name"				=>	$this->getFromPost("name"),
				"component_id"		=>	$this->getFromPost("selectComponent"),
				"density"			=>	$this->getFromPost("density"),
				"density_unit_id"	=>  $this->getFromPost("selectDensityType"),
				"inventory_id"		=>	$this->getFromPost("selectInventory"),
				"coating_id"		=>	$this->getFromPost("selectCoat"),
				"specialty_coating"	=>	(is_null($this->getFromPost('specialty_coating'))) ? "no" : "yes",
				"aerosol"			=>	(is_null($this->getFromPost('aerosol'))) ? "no" : "yes",
				"specific_gravity"	=>	$this->getFromPost("specific_gravity"),
				"specific_gravity_unit_id"	=>	$this->getFromPost("selectGravityType"),
				"supplier_id"		=>	$this->getFromPost("selectSupplier"),
				"vocwx"				=>	$this->getFromPost("vocwx"),
				"voclx"				=>	$this->getFromPost("voclx"),
				"boiling_range_from"=>	$this->getFromPost("boiling_range_from"),
				"boiling_range_to"	=>	$this->getFromPost("boiling_range_to"),
				"percent_volatile_weight"=>	$this->getFromPost("percent_volatile_weight"),
				"percent_volatile_volume"	=>	$this->getFromPost("percent_volatile_volume"),
				"creator_id"		=>	18
			);
			
			//	process hazardous (chemical) classes
			$hazardous = new Hazardous($this->db);
			$chemicalClassesList = $hazardous->getChemicalClassesList();
			for ($i=0;$i<count($chemicalClassesList);$i++) {
				if (!is_null($this->getFromPost('chemicalClass_'.$i))) {
					$chemicalClass = $hazardous->getChemicalClassDetails($this->getFromPost('chemicalClass_'.$i));	
					$j = 0;
					while (!is_null($this->getFromPost('chemicalRule_'.$i.'_'.$j))) {
						$chemicalClass ['rules'][]= $this->getFromPost('chemicalRule_'.$i.'_'.$j);
						$j++;
					}
					$chemicalClasses []= $chemicalClass;
				}										
			}								
			$productData['chemicalClasses'] = $chemicalClasses;
			
			// process industry types
			$prodTypeList = $cProductTypes->getAllTypes();
			$prodSubTypeList = $cProductTypes->getAllSubTypes();
			$prodTypeAndSubTypeList = array_merge_recursive($prodTypeList, $prodSubTypeList);

			for ($i=0; $i<count($prodTypeAndSubTypeList); $i++){
				if (!is_null($this->getFromPost('typesClass_'.$i))){
					foreach ($prodTypeAndSubTypeList as $item) {
						if ($this->getFromPost('typesClass_'.$i) == $item['id']){
							$productAllTypesList[] = $item;
						}
					}
				}
			}
			$j = 0;
			foreach ($productAllTypesList as $prod){
				if ($prod['parent'] == null){
					$resProductAllTypesList[$j]['type'] = $prod['type'];
					$resProductAllTypesList[$j]['subType'] = '';
				} else {
					$resProductAllTypesList[$j]['type'] = $prod['parentType'];
					$resProductAllTypesList[$j]['subType'] = $prod['type'];
				}
				$j++;
			}

			//	process components
			$componentCount = $this->getFromPost('componentCount');
			for ($i=0;$i<$componentCount;$i++) {
				if (!is_null($this->getFromPost('component_id_'.$i))) {
					
					$component = array (
						"component_id"	=>	$this->getFromPost('component_id_'.$i),
						"comp_cas"		=>	$this->getFromPost('comp_cas_'.$i),
						"temp_vp"		=>	$this->getFromPost('temp_vp_'.$i),
						"substrate_id"		=>	$this->getFromPost('substrate_'.$i),
						"rule_id"		=>	$this->getFromPost('rule_id_'.$i),
						"mm_hg"			=>	$this->getFromPost('mm_hg_'.$i),
						"weight"		=>	$this->getFromPost('weight_'.$i),
						"type"			=>	$this->getFromPost('type_'.$i)
					);
					$components[] = $component;
				}
			}
			$productData['components'] = $components;
			$validation = new Validation($this->db);
		}
		//	IF NO POST REQUEST
		else
		{									
			$productData = $product->getProductDetails($id, true);
			
			$this->smarty->assign("componentCount", count($productData['components']));
			$this->smarty->assign("compsAdded", $productData['components']);
			
			$component=new Component($this->db);
			
			$componentsListTemp=$component->getComponentList();
			for ($i=0; $i < count($componentsListTemp); $i++) {
				$f=true;
				for ($j=0; $j < count($productData['components']); $j++) {
					if ($componentsListTemp[$i]['component_id'] == $productData['components'][$j]['component_id']) {
						$f=false;
						break;
					}
				}
				if ($f) {
					$componentsList[]=$componentsListTemp[$i];
				}
			}
			$this->smarty->assign("component", $componentsList);
			
			$componentDetails=$component->getComponentDetails($componentsList[0]['component_id'],true);
			$productData['cas']=$componentDetails['cas'];
			$productData['comp_desc']=$componentDetails['description'];
			
			$rule=new Rule($this->db);
			$this->smarty->assign("rule", $rule->getRuleList());
			
			$coat=new Coat($this->db);
			$this->smarty->assign("coat", $coat->getCoatList());
			
			$substrate=new Substrate($this->db);
			$this->smarty->assign("substrate", $substrate->getSubstrateList());
			
			$supplier=new Supplier($this->db);
			$this->smarty->assign("supplier", $supplier->getSupplierList());
			
			//	hazardous (chemical) class list (popup)
			$hazardous = new Hazardous($this->db);
			$chemicalClassesList = $hazardous->getChemicalClassesList();
			$this->smarty->assign("chemicalClassesList",$chemicalClassesList);
			
			$productTypesList = $cProductTypes->getTypesWithSubTypes();
			$this->smarty->assign('productTypeList', $productTypesList);
		
			$productType = $cProductTypes->getTypeAndSubTypeByProductID($this->getFromRequest('id'));
			$this->smarty->assign('productTypes', $productType);
			
			//density 
			$cDensity = new Density($this->db);
			$cUnitType = new Unittype($this->db);
			$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
			
			$this->smarty->assign('densityDetails', $densityDetailsTrue);
			$this->smarty->assign('densityDefault', $productData['densityUnitID']);								
		}
		//	END NO POST REQUEST
		
		if ($this->getFromPost('save') == "Save") 
		{									
			$validStatus = $validation->validateRegDataProduct($productData);
			
			//check for duplicate names
			if (!($validation->isUniqueName("product", $productData["product_nr"], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus['product_nr'] = 'alredyExist';
			}
			$product=new Product($this->db);
			
			if ($validStatus['summary'] == 'true') {

				$product->setProductDetails($productData);

				$product->unassignProductFromType($id);
				foreach ($resProductAllTypesList as $prod){
					$product->assignProduct2Type($id, $prod['type'], $prod['subType']);
				}
				
				header ('Location: admin.php?action=viewDetails&category=product&id='.$id."&subBookmark=".$this->getFromRequest('subBookmark')."&letterpage=".$this->getFromRequest('letterpage')."&page=".$this->getFromRequest('page'));
				die();																		
				
			} else {
				//	$notify = new Notify($smarty);
				//	$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleAddItem($this->getFromPost('category'));
				
				$this->smarty->assign('validStatus', $validStatus);
				
				$productData['temp_vp']		= $this->getFromPost('temp_vp');
				$productData['substrate_id']= $this->getFromPost('substrate_id'); //???
				$productData['rule_id']		= $this->getFromPost('rule_id');
				$productData['mm_hg']		= $this->getFromPost('mm_hg');
				$productData['weight']		= $this->getFromPost('weight');
				$productData['type']		= $this->getFromPost('type');
				$productData['substrate_id']= $this->getFromPost('selectSubstrate');
				$productData['rule_id']		= $this->getFromPost('selectRule');																														
				
				$product=new Product($this->db);
				$productList=$product->getProductList();
				$component=new Component($this->db);
				
				$componentsListTemp=$component->getComponentList();
				for ($i=0; $i < count($componentsListTemp); $i++) {
					$f=true;
					for ($j=0; $j < count($components); $j++) {
						if ($componentsListTemp[$i]['component_id'] == $components[$j]['component_id']) {
							$f=false;
							break;
						}
					}
					if ($f) {
						$componentsList[]=$componentsListTemp[$i];
					}
				}
				$componentDetails=$component->getComponentDetails($productData['component_id'],true);
				$productData['cas']=$componentDetails['cas'];
				$productData['comp_desc']=$componentDetails['description'];
				
				$this->smarty->assign("component", $componentsList);
				$this->smarty->assign("componentCount", count($components));
				$this->smarty->assign("compsAdded", $components);									
				
				$rule=new Rule($this->db);
				$this->smarty->assign("rule", $rule->getRuleList());
				
				$coat=new Coat($this->db);
				$this->smarty->assign("coat", $coat->getCoatList());
				
				$substrate=new Substrate($this->db);
				$this->smarty->assign("substrate", $substrate->getSubstrateList());
				
				$supplier=new Supplier($this->db);
				$this->smarty->assign("supplier", $supplier->getSupplierList());
				
				//	get hazardous (chemical) class
				$hazardous = new Hazardous($this->db);
				$chemicalClassesList = $hazardous->getChemicalClassesList();
				$this->smarty->assign("chemicalClassesList", $chemicalClassesList);
				
				//density 
				$cDensity = new Density($this->db);
				$cUnitType = new Unittype($this->db);
				$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
				
				$this->smarty->assign('densityDetails', $densityDetailsTrue);
				$this->smarty->assign('densityDefault', $productData['density_unit_id']);									
			}
		} 
		if ($this->getFromPost('save') == 'Add component to product')
		{									
			$component = new Component($this->db);
			$data2 = $component->getComponentDetails($this->getFromPost('selectComponent'),true);
			
			$componentNew = array(
				"component_id"	=>	$this->getFromPost('selectComponent'),
				"comp_cas"		=>	$data2['cas'],
				"temp_vp"		=>	$this->getFromPost('temp_vp'),
				"substrate_id"	=>	$this->getFromPost('selectSubstrate'),
				"rule_id"		=>	$this->getFromPost('selectRule'),
				"mm_hg"			=>	$this->getFromPost('mm_hg'),
				"weight"		=>	$this->getFromPost('weight'),
				"type"			=>	$this->getFromPost('type')
			);
			
			//	get hazardous (chemical) class
			$hazardous = new Hazardous($this->db);
			$chemicalClassesList = $hazardous->getChemicalClassesList();
			$this->smarty->assign("chemicalClassesList", $chemicalClassesList);
			
			$validateStatus = $validation->validateNewComponent($componentNew);
			if ($validateStatus['summary'] == "true") {
				$components[] = $componentNew;
			} else {
				$this->smarty->assign("validStatus",$validateStatus);
				$validStatus = $validateStatus;
				$productData['temp_vp']		= $this->getFromPost('temp_vp');
				$productData['substrate_id']= $this->getFromPost('selectSubstrate');
				$productData['rule_id']		= $this->getFromPost('selectRule');
				$productData['mm_hg']		= $this->getFromPost('mm_hg');
				$productData['weight']		= $this->getFromPost('weight');
				$productData['type']		= $this->getFromPost('type');										
			}
			$componentsListTemp = $component->getComponentList();
			for ($i=0; $i < count($componentsListTemp); $i++) {
				$f = true;
				for ($j=0; $j < count($components); $j++) {
					if ($componentsListTemp[$i]['component_id'] == $components[$j]['component_id']) {
						$f=false;
						break;
					}
				}
				if ($f) {
					$componentsList[] = $componentsListTemp[$i];
				}
			}
			$this->smarty->assign("component", $componentsList);
			
			if ($validateStatus['summary'] == "true") {
				$componentDetails = $component->getComponentDetails($componentsList[0]['component_id'],true);
				$productData['cas'] = $componentDetails['cas'];
				$productData['comp_desc'] = $componentDetails['description'];
			}
			
			$coat=new Coat($this->db);
			$this->smarty->assign("coat", $coat->getCoatList());
			
			$supplier=new Supplier($this->db);
			$this->smarty->assign("supplier", $supplier->getSupplierList());
			
			$substrate=new Substrate($this->db);
			$this->smarty->assign("substrate", $substrate->getSubstrateList());
			
			$rule=new Rule($this->db);
			$rulelist=$rule->getRuleList();
			$this->smarty->assign("rule", $rule->getRuleList());
			
			//density 
			$cDensity = new Density($this->db);
			$cUnitType = new Unittype($this->db);
			$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
			
			$this->smarty->assign('densityDetails', $densityDetailsTrue);
			$this->smarty->assign('densityDefault', $productData['density_unit_id']);									
			$this->smarty->assign("componentCount", count($components));
			$this->smarty->assign("compsAdded", $components);									
		}
		$data = $productData;
		$jsSources = array(
			'modules/js/PopupWindow.js', 
			'modules/js/checkBoxes.js',
			"modules/js/reg_country_state.js",
			"modules/js/componentPreview.js",
			"modules/js/getInventoryShortInfo.js",
			"modules/js/addProductQuantity.js",
			"modules/js/hazardousPopup.js",
			"modules/js/industryTypesPopup.js"
		);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl','tpls/addProductClass.tpl');
		$this->smarty->assign('data', $data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		//prepare company list	
		$this->smarty->assign('currentCompany',$this->getFromRequest('companyID'));
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		$companyList[] = array('id' => 0, 'name' => 'no company');
		$this->smarty->assign('companyList',$companyList);
		
		if (!is_null($this->getFromPost('save')))
		{
			//	replace false/true for no/yes 
			$specialty_coating 	= (is_null($this->getFromPost('specialty_coating'))) ? "no" : "yes";
			$aerosol 			= (is_null($this->getFromPost('aerosol'))) ? "no" : "yes";								
			
			$productData = array (
				"product_nr"		=>	$this->getFromPost("product_nr"),
				"name"				=>	$this->getFromPost("name"),
				"component_id"		=>	$this->getFromPost("selectComponent"),
				"density"			=>	$this->getFromPost("density"),
				"density_unit_id"	=>  $this->getFromPost("selectDensityType"),
				"coating_id"		=>	$this->getFromPost("selectCoat"),
				"specialty_coating"	=>	$specialty_coating,
				"aerosol"			=>	$aerosol,
				"specific_gravity"	=>	$this->getFromPost("specific_gravity"),
				"specific_gravity_unit_id"	=>	$this->getFromPost("selectGravityType"),
				"supplier_id"		=>	$this->getFromPost("selectSupplier"),
				"vocwx"				=>	$this->getFromPost("vocwx"),
				"voclx"				=>	$this->getFromPost("voclx"),
				"boiling_range_from"=>	$this->getFromPost("boiling_range_from"),
				"boiling_range_to"	=>	$this->getFromPost("boiling_range_to"),
				"percent_volatile_weight"=>	$this->getFromPost("percent_volatile_weight"),
				"percent_volatile_volume"	=>	$this->getFromPost("percent_volatile_volume"),
				"creator_id"			=>	18 //???
			);
			
			//	process hazardous (chemical) classes
			$hazardous = new Hazardous($this->db);
			$chemicalClassesList = $hazardous->getChemicalClassesList();
			for ($i=0;$i<count($chemicalClassesList);$i++) {
				if (!is_null($this->getFromPost('chemicalClass_'.$i))) {
					$chemicalClass = $hazardous->getChemicalClassDetails($this->getFromPost('chemicalClass_'.$i));	
					$j = 0;
					while (!is_null($this->getFromPost('chemicalRule_'.$i.'_'.$j))) {
						$chemicalClass ['rules'][]= $this->getFromPost('chemicalRule_'.$i.'_'.$j);
						$j++;
					}
					$chemicalClasses []= $chemicalClass;
				}										
			}								
			$productData['chemicalClasses'] = $chemicalClasses;
			
			// process industry types
			$cProductTypes = new ProductTypes($this->db);
			$prodTypeList = $cProductTypes->getAllTypes();
			$prodSubTypeList = $cProductTypes->getAllSubTypes();
			$prodTypeAndSubTypeList = array_merge_recursive($prodTypeList, $prodSubTypeList);

			for ($i=0; $i<count($prodTypeAndSubTypeList); $i++){
				if (!is_null($this->getFromPost('typesClass_'.$i))){
					foreach ($prodTypeAndSubTypeList as $item) {
						if ($this->getFromPost('typesClass_'.$i) == $item['id']){
							$productAllTypesList[] = $item;
						}
					}
				}
			}
			$j = 0;
			foreach ($productAllTypesList as $prod){
				if ($prod['parent'] == null){
					$resProductAllTypesList[$j]['type'] = $prod['type'];
					$resProductAllTypesList[$j]['subType'] = '';
				} else {
					$resProductAllTypesList[$j]['type'] = $prod['parentType'];
					$resProductAllTypesList[$j]['subType'] = $prod['type'];
				}
				$j++;
			}
			$productData['prodTypes'] = $resProductAllTypesList;
			//	process components
			$componentCount = $this->getFromPost('componentCount');
			for ($i=0;$i<$componentCount;$i++) {
				if (!is_null($this->getFromPost('component_id_'.$i))) {
					$component = array (
						"component_id"	=>	$this->getFromPost('component_id_'.$i),
						"comp_cas"		=>	$this->getFromPost('comp_cas_'.$i),
						"temp_vp"		=>	$this->getFromPost('temp_vp_'.$i),
						"substrate_id"		=>	$this->getFromPost('substrate_'.$i),
						"rule_id"		=>	$this->getFromPost('rule_id_'.$i),
						"mm_hg"			=>	$this->getFromPost('mm_hg_'.$i),
						"weight"		=>	$this->getFromPost('weight_'.$i),
						"type"			=>	$this->getFromPost('type_'.$i)
					);
					$components[] = $component;
				}
			}
			$productData['components'] = $components;
			$validation = new Validation($this->db);
			if ($this->getFromPost('save') == "Save") {
				
				$validStatus = $validation->validateRegDataProduct($productData);										
				//check for duplicate names
				if($productData['supplier_id']==null)
				{
					$validStatus['summary'] = 'false';
					$validStatus['supplier_id'] = 'failed';
				}
				if($productData['coating_id']==null)
				{
					$validStatus['summary'] = 'false';
					$validStatus['coating_id'] = 'failed';
				}
				if (!($validation->isUniqueName("product", $productData["product_nr"]))) {
					$validStatus['summary'] = 'false';
					$validStatus['product_nr'] = 'alredyExist';
				}
				$product = new Product($this->db);
				
				if ($validStatus['summary'] == 'true') {
					$productData['resultTypesList'] = $resProductAllTypesList;
					$product->addNewProduct($productData, $this->getFromRequest('companyID'));		
					header ('Location: admin.php?action=browseCategory&category=product&subBookmark='.$this->getFromRequest("subBookmark").'&letterpage='.$this->getFromRequest("letterpage"));
					die();
				} else {
					
					//prepare company list											
					
					//$notify = new Notify($smarty);
					//$notify->formErrors();
					$title = new Titles($this->smarty);
					$title->titleAddItem($this->getFromPost("itemID"));									
					
					$this->smarty->assign('validStatus', $validStatus);
					
					$productData['temp_vp']		= $this->getFromPost('temp_vp');
					$productData['substrate_id']= $this->getFromPost('selectSubstrate');
					$productData['rule_id']		= $this->getFromPost('selectRule');
					$productData['mm_hg']		= $this->getFromPost('mm_hg');
					$productData['weight']		= $this->getFromPost('weight');
					$productData['type']		= $this->getFromPost('type');
					
					$product=new Product($this->db);
					$productList=$product->getProductList();
					$component=new Component($this->db);
					
					$componentsListTemp=$component->getComponentList();
					for ($i=0; $i < count($componentsListTemp); $i++) {
						$f=true;
						for ($j=0; $j < count($components); $j++) {
							if ($componentsListTemp[$i]['component_id'] == $components[$j]['component_id']) {
								$f=false;
								break;
							}
						}
						if ($f) {
							$componentsList[]=$componentsListTemp[$i];
						}
					}
					$componentDetails=$component->getComponentDetails($productData['component_id'],true);
					$productData['cas']=$componentDetails['cas'];
					$productData['comp_desc']=$componentDetails['description'];
					
					$this->smarty->assign("component", $componentsList);
					$this->smarty->assign("componentCount", count($components));
					$this->smarty->assign("compsAdded", $components);
					
					$this->smarty->assign('data', $productData);						
				}
			} else {										
				$component = new Component($this->db);
				$data2 = $component->getComponentDetails($this->getFromPost('selectComponent'),true);
				$componentNew = array(
					"component_id"	=>	$this->getFromPost('selectComponent'),
					"comp_cas"		=>	$data2['cas'],
					"temp_vp"		=>	$this->getFromPost('temp_vp'),
					"substrate_id"	=>	$this->getFromPost('selectSubstrate'),
					"rule_id"		=>	$this->getFromPost('selectRule'),
					"mm_hg"			=>	$this->getFromPost('mm_hg'),
					"type"			=>	$this->getFromPost('type'),
					"weight"		=>	$this->getFromPost('weight')
				);
				$validateStatus = $validation->validateNewComponent($componentNew);
				if ($validateStatus['summary'] == "true") {
					$components[] = $componentNew;
				} else {
					$this->smarty->assign("validStatus",$validateStatus);
					$productData['temp_vp']		= $this->getFromPost('temp_vp');
					$productData['substrate_id']= $this->getFromPost('selectSubstrate');
					$productData['rule_id']		= $this->getFromPost('selectRule');
					$productData['type']		= $this->getFromPost('type');
					$productData['mm_hg']		= $this->getFromPost('mm_hg');
					$productData['weight']		= $this->getFromPost('weight');
				}
				$componentsListTemp = $component->getComponentList();
				for ($i=0; $i < count($componentsListTemp); $i++) {
					$f=true;
					for ($j=0; $j < count($components); $j++) {
						if ($componentsListTemp[$i]['component_id'] == $components[$j]['component_id']) {
							$f=false;
							break;
						}
					}
					if ($f) {
						$componentsList[]=$componentsListTemp[$i];
					}
				}
				$this->smarty->assign("component", $componentsList);
				
				if ($validateStatus['summary'] == "true") {
					$componentDetails = $component->getComponentDetails($componentsList[0]['component_id'],true);
					$productData['cas'] = $componentDetails['cas'];
					$productData['comp_desc'] = $componentDetails['description'];
				}
				
				$this->smarty->assign('data', $productData);
			}
		}	
		else	
		{
			$component=new Component($this->db);
			$componentList=$component->getComponentList();
			$this->smarty->assign("component", $componentList);
			$componentDetails=$component->getComponentDetails($componentList[0]['component_id']);
			
			$categoryDetails['cas']=$componentDetails['cas'];
			$categoryDetails['comp_desc']=$componentDetails['description'];
			
			$this->smarty->assign('data', $categoryDetails);	
			
		}
		
		$rule=new Rule($this->db);
		$this->smarty->assign("rule", $rule->getRuleList());
		
		$coat=new Coat($this->db);
		$this->smarty->assign("coat", $coat->getCoatList());
		
		$substrate=new Substrate($this->db);
		$this->smarty->assign("substrate", $substrate->getSubstrateList());
		
		/*$supplier=new Supplier($this->db);
		$this->smarty->assign("supplier", $supplier->getSupplierList());
		*/
		$suppl=new BookmarksManager($this->db);
		$this->smarty->assign("supplier",$supplier=$suppl->getOriginSupplier());
		
		//	get hazardous (chemical) class list
		$hazardous = new Hazardous($this->db);
		$chemicalClassesList = $hazardous->getChemicalClassesList();
		$this->smarty->assign("chemicalClassesList", $chemicalClassesList);
		
		$cProductTypes = new ProductTypes($this->db);
		$productTypesList = $cProductTypes->getTypesWithSubTypes();
		$this->smarty->assign('productTypeList', $productTypesList);
		
		$productType = $cProductTypes->getTypeAndSubTypeByProductID($this->getFromRequest('id'));
		$this->smarty->assign('productTypes', $productType);
		
		//density 
		$cDensity = new Density($this->db);
		$cUnitType = new Unittype($this->db);
		$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
		
		$this->smarty->assign('densityDetails', $densityDetailsTrue);
		$this->smarty->assign('densityDefault', $productData['density_unit_id']);
		
		$this->smarty->assign("componentCount", count($components));
		$this->smarty->assign("compsAdded", $components);
		$jsSources = array(
			'modules/js/PopupWindow.js', 
			'modules/js/checkBoxes.js',
			"modules/js/reg_country_state.js",
			"modules/js/componentPreview.js",
			"modules/js/getInventoryShortInfo.js",
			"modules/js/addProductQuantity.js",
			"modules/js/hazardousPopup.js",
			"modules/js/industryTypesPopup.js"
		);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/addProductClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$product=new Product($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$productDetails=$product->getProductDetails($this->getFromRequest('item_'.$i));
				$item["id"] = $productDetails["product_id"];
				$item["name"] = $productDetails["product_nr"];
				$item["links"] = $product->isInUseList($item["id"]);
				$itemForDelete []= $item;
			}
		}
		$this->smarty->assign('page', $this->getFromRequest('page'));
		$this->smarty->assign("gobackAction","browseCategory");
		$this->finalDeleteItemACommon($itemForDelete);
	}
			
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$product=new Product($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			
			$product->deleteProduct2($id);
		}
		header ('Location: admin.php?action=browseCategory&category=product&subBookmark='.$this->getFromRequest("subBookmark").'&letterpage='.$this->getFromRequest("letterpage").'&page='.$this->getFromRequest("page"));
		die();
	}
}
?>