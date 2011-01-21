<?php
	require('config/constants.php');
	
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	
	$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 
	define ('site_path', $site_path);
	
	require_once('modules/classAutoloader.php');

	//	Start xnyo Framework
	require ('modules/xnyo/startXnyo.php');						

	$xnyo->load_plugin('auth');
	$xnyo->logout_redirect_url='admin.php';
	
	require ('modules/xnyo/smarty/startSmartyAdmin.php');

	$db->select_db(DB_NAME);	
	
	//	deny access to system while updating jobs
	if (MAINTENANCE) {
		$smarty->display('tpls:errors/maintenance.tpl');
		die();
	}
		
	$xnyo->filter_get_var('action', 'text');
	$xnyo->filter_post_var('action', 'text');
	
	$xnyo->filter_get_var('page', 'text');
	
	$ms = new ModuleSystem($db);
	$map = $ms->getModulesMap();
	
	if (VERSION == 'standalone') {
		if (!class_exists($map['reports'].'Installer')) {
			$smarty->assign('showReports','false');
		} else {
			$reportInstallerClass = $map['reports'].'Installer';
			$reportInstaller = new $reportInstallerClass($db);
			if (!$reportInstaller->checkAlreadyInstalled()) {
				$smarty->assign('showReports','false');
			}
		}
		if (!class_exists($map['carbon_footprint'].'Installer')) {
			$smarty->assign('showFootprint', 'false');
		} else {
			$cfInstallerClass = $map['carbon_footprint'].'Installer';
			$cfInstaller = new $cfInstallerClass($db);
			if (!$cfInstaller->checkAlreadyInstalled()) {
				$smarty->assign('showFootprint', 'false');
			}
		}
	}
	//$db->select_db(DB_NAME);	
	
	// convert for search
	function convertSearchItemsToArray($query) {
		$firstStep = explode(', ', $query);
		foreach ($firstStep as $item) {
			$secondStep = explode('; ', $item);
			foreach ($secondStep as $finalItem) {
				$finalItems[] = $finalItem;
			}
		}
		return $finalItems;	
	}	
	
	function showCategory ($categoryID, $itemID, $db, $smarty, $xnyo) {
		
				
		//$_SESSION["gobackAction"]="";
		
		$title=new Titles($smarty);
		$title->titleClassesAdmin($itemID);
		
		$jsSources = array();
		
		$smarty->assign("categoryID","tab_".$categoryID);
			
		switch ($categoryID) {
			case "class":
				switch ($itemID) {
					case "apmethod":
						$apmethod=new Apmethod($db);
						$pagination = new Pagination($apmethod->queryTotalCount());
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=apmethod";
						$apmethodList=$apmethod->getApmethodList($pagination);
						$field='apmethod_id';
						/*$itemsCount=count($apmethodList);
						
						for ($i=0; $i<$itemsCount; $i++) {
							$url="admin.php?action=viewDetails&categoryID=class&itemID=apmethod&id=".$apmethodList[$i]['apmethod_id'];
							$apmethodList[$i]['url']=$url;
						}*/
						$list=$apmethodList;
						$smarty->assign("bookmarkType","apmethod");
						$smarty->assign("categoryType","class");
						$smarty->assign("category",$apmethodList);
						//$smarty->assign("itemsCount",$itemsCount);
						
						$smarty->assign('tpl', 'tpls/apmethodClass.tpl');
						$smarty->assign('pagination', $pagination);						
						break;
						
					case "coat":
						$coat=new Coat($db);
						
						/*FILTER*/									
						$filter=new Filter($db,'coat');	
											
						$smarty->assign('filterArray',$filter->getJsonFilterArray());
						$xnyo->filter_get_var('filterField','text');
						$xnyo->filter_get_var('filterCondition','text');
						$xnyo->filter_get_var('filterValue','text');
						$filterData= array
						(
							'filterField'=>$_GET['filterField'],
							'filterCondition'=>$_GET['filterCondition'],
							'filterValue'=>$_GET['filterValue']
						);
									
						if ($_GET['searchAction']=='filter');
						{
							$smarty->assign('filterData',$filterData);
							$smarty->assign('searchAction','filter');										
						}
						$filterStr = $filter->getSubQuery($filterData);
						/*/FILTER*/
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr=" ORDER BY coat_desc  ";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'coat',3);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',3);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/
						
						$pagination = new Pagination($coat->queryTotalCount($filterStr));
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=coat".
							(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
							(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
							(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
							(isset($filterData['filterField'])?"&searchAction=filter":""); 
							    		
						$coatList=$coat->getCoatList($pagination,$filterStr,$sortStr);
						//$itemsCount=count($coatList);
						$field='coat_id';
						$list=$coatList;
						/*for ($i=0; $i<$itemsCount; $i++) {
							$url="admin.php?action=viewDetails&categoryID=class&itemID=coat&id=".$coatList[$i]['coat_id'];
							$coatList[$i]['url']=$url;
						}*/
						
						$smarty->assign("bookmarkType","coat");
						$smarty->assign("categoryType","class");
						$smarty->assign("category",$coatList);
						//$smarty->assign("itemsCount",$itemsCount);
						
						$smarty->assign('tpl', 'tpls/coatClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
						
					case "density":
						$density=new Density($db);
						$densityList=$density->getDensityList();
						$itemsCount=count($densityList);
						
						for ($i=0; $i<$itemsCount; $i++) {
							$url="admin.php?action=viewDetails&categoryID=class&itemID=density&id=".$densityList[$i]['density_id'];
							$densityList[$i]['url']=$url;
						}
						
						$smarty->assign("bookmarkType","density");
						$smarty->assign("categoryType","class");
						$smarty->assign("category",$densityList);
						$smarty->assign("itemsCount",$itemsCount);
						
						$smarty->assign('tpl', 'tpls/densityClass.tpl');
						break;
						
					case "country":
						$country=new Country($db);
						
						/*FILTER*/									
						$filter=new Filter($db,'country');	
											
						$smarty->assign('filterArray',$filter->getJsonFilterArray());
						$xnyo->filter_get_var('filterField','text');
						$xnyo->filter_get_var('filterCondition','text');
						$xnyo->filter_get_var('filterValue','text');
						$filterData= array
						(
							'filterField'=>$_GET['filterField'],
							'filterCondition'=>$_GET['filterCondition'],
							'filterValue'=>$_GET['filterValue']
						);
									
						if ($_GET['searchAction']=='filter');
						{
							$smarty->assign('filterData',$filterData);	
							$smarty->assign('searchAction','filter');																
						}
						$filterStr = $filter->getSubQuery($filterData);
						/*/FILTER*/
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr=" ORDER BY name ";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'country',3);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',3);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/
						
						$pagination = new Pagination($country->queryTotalCount($filterStr));
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=country".
							(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
							(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
							(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
							(isset($filterData['filterField'])?"&searchAction=filter":""); 
						$countryList=$country->getCountryList($pagination,$filterStr,$sortStr);
						
						$field = 'country_id';
						$list = $countryList;
						
						$smarty->assign('tpl', 'tpls/countryClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
						
					case "substrate":
						$substrate=new Substrate($db);
						$pagination = new Pagination($substrate->queryTotalCount());
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=substrate";
						$substrateList=$substrate->getSubstrateList($pagination);
						
						$field = 'substrate_id';
						$list = $substrateList;
						
						$smarty->assign('tpl', 'tpls/substrateClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
						
					case "supplier":					
						$supplier=new Supplier($db);
						
						/*FILTER*/									
						$filter=new Filter($db,'supplier');	
											
						$smarty->assign('filterArray',$filter->getJsonFilterArray());
						$xnyo->filter_get_var('filterField','text');
						$xnyo->filter_get_var('filterCondition','text');
						$xnyo->filter_get_var('filterValue','text');
						$filterData= array
						(
							'filterField'=>$_GET['filterField'],
							'filterCondition'=>$_GET['filterCondition'],
							'filterValue'=>$_GET['filterValue']
						);
									
						if ($_GET['searchAction']=='filter');
						{
							$smarty->assign('filterData',$filterData);	
							$smarty->assign('searchAction','filter');									
						}
						$filterStr = $filter->getSubQuery($filterData);
						/*/FILTER*/
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr=" ORDER BY  supplier";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'supplier',3);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',3);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/
						
						$pagination = new Pagination($supplier->queryTotalCount($filterStr));
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=supplier".
							(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
							(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
							(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
							(isset($filterData['filterField'])?"&searchAction=filter":""); 
						$supplierList=$supplier->getSupplierList($pagination,$filterStr,$sortStr);
						
						$field = 'supplier_id';
						$list = $supplierList;
						
						$smarty->assign('tpl', 'tpls/supplierClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
												
					case "rule":
						$rule=new Rule($db);
						
						/*FILTER*/									
						$filter=new Filter($db,'rule');	
											
						$smarty->assign('filterArray',$filter->getJsonFilterArray());
						$xnyo->filter_get_var('filterField','text');
						$xnyo->filter_get_var('filterCondition','text');
						$xnyo->filter_get_var('filterValue','text');
						$filterData= array
						(
							'filterField'=>$_GET['filterField'],
							'filterCondition'=>$_GET['filterCondition'],
							'filterValue'=>$_GET['filterValue']
						);
									
						if ($_GET['searchAction']=='filter');
						{
							$smarty->assign('filterData',$filterData);	
							$smarty->assign('searchAction','filter');									
						}
						$filterStr = $filter->getSubQuery($filterData);
						/*/FILTER*/
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr=" ORDER BY rule_desc ";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'rule',7);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',7);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/
						
						$pagination = new Pagination($rule->queryTotalCount($filterStr));
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=rule".
							(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
							(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
							(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
							(isset($filterData['filterField'])?"&searchAction=filter":""); 						
						$ruleList=$rule->getRuleList($pagination,$filterStr,$sortStr);
						
						$field = 'rule_id';
						$list = $ruleList;												
						$smarty->assign('tpl', 'tpls/ruleClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
						
					case "components":
						$components=new Component($db);
						
						/*FILTER*/									
						$filter=new Filter($db,'components');	
											
						$smarty->assign('filterArray',$filter->getJsonFilterArray());
						$xnyo->filter_get_var('filterField','text');
						$xnyo->filter_get_var('filterCondition','text');
						$xnyo->filter_get_var('filterValue','text');
						$filterData= array
						(
							'filterField'=>$_GET['filterField'],
							'filterCondition'=>$_GET['filterCondition'],
							'filterValue'=>$_GET['filterValue']
						);
									
						if ($_GET['searchAction']=='filter');
						{
							$smarty->assign('filterData',$filterData);
							$smarty->assign('searchAction','filter');										
						}
						$filterStr = $filter->getSubQuery($filterData);
						/*/FILTER*/
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr=" ORDER BY description ";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'components',5);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',5);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/
						
						$pagination = new Pagination($components->queryTotalCount($filterStr));
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=components".
							(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
							(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
							(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
							(isset($filterData['filterField'])?"&searchAction=filter":""); 
						
						$componentsList = $components->getComponentList($pagination,$filterStr,$sortStr);
						
						$field = 'component_id';
						$list = $componentsList;						
						$smarty->assign('tpl', 'tpls/componentsClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
						
					case "product":
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr="";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'product',0);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',0);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/						
												
						// filter vars
						$xnyo->filter_get_var('supplierID', 'int');
						$xnyo->filter_get_var('companyID', 'int');
						$xnyo->filter_get_var('q', 'text');
						$xnyo->filter_get_var('sortBy', 'text');
						$xnyo->filter_get_var('page', 'int');
						$xnyo->filter_get_var('subaction', 'text');
												
						
						switch($_GET['subaction'])
						{
							case 'Filter':										
								//$companyID = $_GET['companyID'];								
								break;								
							case 'Assign to company':								
							case 'Unassign product(s)':
								$xnyo->filter_get_var('itemsCount', 'text');															
								$companyID = $_GET['companyID'];					
								if (!empty($companyID)) 
								{						
									$product = new Product($db);
									for ($i=0;$i<$_GET['itemsCount'];$i++) 
									{
										$xnyo->filter_get_var("item_".$i,"int");										
										if (isset($_GET['item_'.$i])) 
										{
											
											$productID = $_GET['item_'.$i];
											if ($_GET['subaction'] == "Assign to company") {
												$product->assignProduct2Company($productID, $companyID);	
											} elseif ($_GET['subaction'] == "Unassign product(s)") {
												$product->unassignProductFromCompany($productID, $companyID);
											}											
										}	
									}						
								}							
								break;							
						}
						
						// get Supplier list
						$supplier=new Supplier($db);
						$supplierList=$supplier->getSupplierList();
						$supplierItemsCount=count($supplierList);
						$smarty->assign('supplierList', $supplierList);						
					
																						
						$product=new Product($db);
						if (isset($_GET['companyID']) && $_GET['companyID']!='All companies') {
							if (isset($_GET['supplierID']) && $_GET['supplierID']!='All suppliers') {
								$productCount = $product->getProductCount($_GET['companyID'],$_GET['supplierID']);								
							} else {
								$productCount = $product->getProductCount($_GET['companyID'],0);								
							}
						} else {
							if (isset($_GET['supplierID']) && $_GET['supplierID']!='All suppliers') {	
								$productCount= $product->getProductCount(0,$_GET['supplierID']);														
							} else {
								$productCount = $product->getProductCount(0,0);	
							}
						}
						
						//	get product list
						$company = new Company($db);
						$companyList = $company->getCompanyList();
						//$companyList[] = array('id' => 0, 'name' => 'All companies');
						$smarty->assign('companyList',$companyList);												
						
						//	search??									
						if (isset($_GET['q'])) {
							$productsToFind = convertSearchItemsToArray($_GET['q']);										
							$productList = $product->searchProducts($productsToFind, $_GET['companyID']);
							
							$smarty->assign('currentCompany',0);
							$smarty->assign('currentSupplier', 0);																						
							$smarty->assign('searchQuery', $_GET['q']);
						} else {
							
							$pagination = new Pagination($productCount);
							$pagination->url = "?action=browseCategory&companyID=".$_GET['companyID']."&supplierID=".$_GET['supplierID']."&subaction=Filter&categoryID=class&itemID=product";
							$smarty->assign('pagination', $pagination);
							
												
							
							if (isset($_GET['companyID']) && $_GET['companyID']!='All companies') {
							
								if (isset($_GET['supplierID']) && $_GET['supplierID']!='All suppliers') {
									
									//$productList = $product->getProductListByMFG($_GET['supplierID'], $_SESSION['viewProductOfCompany'], $categoryFrom, false);
									$productList = $product->getProductListByMFG($_GET['supplierID'], $_GET['companyID'], $pagination,' TRUE ',$sortStr);
									
									$smarty->assign('currentCompany',$_GET['companyID']);
									$smarty->assign('currentSupplier', $_GET['supplierID']);
								} else {
									//$productList = $product->getProductList($_SESSION['viewProductOfCompany'], $categoryFrom, false);
									$productList = $product->getProductList($_GET['companyID'], $pagination,' TRUE ',$sortStr);
									
									$smarty->assign('currentCompany',$_GET['companyID']);
									$smarty->assign('currentSupplier', 0);	
								}
							} else {
								if (isset($_GET['supplierID']) && $_GET['supplierID']!='All suppliers') {	
									//$productList = $product->getProductListByMFG($_GET['supplierID'], 0, $categoryFrom, false);
									$productList = $product->getProductListByMFG($_GET['supplierID'], 0, $pagination,' TRUE ',$sortStr);
									
									$smarty->assign('currentCompany',0);
									$smarty->assign('currentSupplier', $_GET['supplierID']);
								} else {
									$productList = $product->getProductList(0, $pagination,' TRUE ',$sortStr);
									//$productList = $product->getProductList(0, $categoryFrom, false);
									$smarty->assign('currentCompany',0);
									$smarty->assign('currentSupplier', 0);
								}
							}							
						}				
						$field = 'product_id';
						$list = $productList;
						
						//array_push($jsSources, 'modules/js/autocomplete/jquery-1.3.2.min.js');
						array_push($jsSources, 'modules/js/autocomplete/jquery.autocomplete.js');
						
						$smarty->assign('tpl', 'tpls/productClass.tpl');
						break;
						
					case "agency":
						$agency=new Agency($db);
						
						/*FILTER*/									
						$filter=new Filter($db,'agency');
											
						$smarty->assign('filterArray',$filter->getJsonFilterArray());
						$xnyo->filter_get_var('filterField','text');
						$xnyo->filter_get_var('filterCondition','text');
						$xnyo->filter_get_var('filterValue','text');
						$filterData= array
						(
							'filterField'=>$_GET['filterField'],
							'filterCondition'=>$_GET['filterCondition'],
							'filterValue'=>$_GET['filterValue']
						);
									
						if ($_GET['searchAction']=='filter');
						{
							$smarty->assign('filterData',$filterData);
							$smarty->assign('searchAction','filter');										
						}
						$filterStr = $filter->getSubQuery($filterData);
						/*/FILTER*/
						
						/*SORT*/
						$xnyo->filter_get_var('sort','text');
						$sortStr=" ORDER BY a.name_us ";
						if (isset($_GET['sort']))
						{
							$sort= new Sort($db,'agency',3);
							$sortStr = $sort->getSubQuerySort($_GET['sort']);										
							$smarty->assign('sort',$_GET['sort']);
						}
						else									
							$smarty->assign('sort',3);
									
						if (isset($_GET['searchAction']))									
							$smarty->assign('searchAction',$_GET['searchAction']);
						/*/SORT*/
						
						$pagination = new Pagination($agency->getAgencyCount($filterStr));
						$pagination->url = "?action=browseCategory&categoryID=class&itemID=agency".
							(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
							(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
							(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
							(isset($filterData['filterField'])?"&searchAction=filter":""); 
						$agencyList=$agency->getAgencyList('', $pagination,$filterStr,$sortStr);						
						$field='agency_id';
						$list=$agencyList;
						$smarty->assign("bookmarkType","agency");
						$smarty->assign("categoryType","class");
						$smarty->assign("category",$agencyList);
					
						$smarty->assign('tpl', 'tpls/agencyClass.tpl');
						$smarty->assign('pagination', $pagination);
						break;
						
					case "emissionFactor":
						
						$ms = new ModuleSystem($db);
						$map = $ms->getModulesMap();
						if (class_exists($map['carbon_footprint'])) {
							$mCarbonFootprint = new $map['carbon_footprint'];
							$result = $mCarbonFootprint->prepareAdminView(array('db' => $db));
							foreach($result as $key => $value) {
								$smarty->assign($key,$value);
							}
						}
						$smarty->assign("bookmarkType","emissionFactor");
						$smarty->assign("categoryType","class");
						$smarty->assign("unittype", new Unittype($db));
						$smarty->assign("url", 'admin.php?action=viewDetails&categoryID=class&itemID=emissionFactor&id=');	//	id will be set in tpl	
						
						array_push($jsSources, 'modules/js/checkBoxes.js');	
						$smarty->assign('jsSources', $jsSources);																	
						break;
						
					
				}
				//$smarty->display("tpls:classes.tpl");
				$itemsCount = ($list) ? count($list) : 0;
				for ($i=0; $i<$itemsCount; $i++) {
					$url="admin.php?action=viewDetails&categoryID=class&itemID=$itemID&id=".$list[$i][$field];
					$list[$i]['url']=$url;
				}
				
				$smarty->assign("bookmarkType",$itemID);
				$smarty->assign("categoryType","class");
				$smarty->assign("category",$list);
				$smarty->assign("itemsCount",$itemsCount);
				
				//	add checkboxes js											
				array_push($jsSources, 'modules/js/checkBoxes.js');	
				$smarty->assign('jsSources', $jsSources);							
				$smarty->display("tpls:index.tpl");				
				break;
			
				
			case "users":			
				$user=new User($db, $xnyo, $access, $auth);
				
				/*FILTER*/									
				$filter=new Filter($db,'users');	
											
				$smarty->assign('filterArray',$filter->getJsonFilterArray());
				$xnyo->filter_get_var('filterField','text');
				$xnyo->filter_get_var('filterCondition','text');
				$xnyo->filter_get_var('filterValue','text');
				$filterData= array
				(
					'filterField'=>$_GET['filterField'],
					'filterCondition'=>$_GET['filterCondition'],
					'filterValue'=>$_GET['filterValue']
				);
									
				if ($_GET['searchAction']=='filter');
				{
					$smarty->assign('filterData',$filterData);	
					$smarty->assign('searchAction','filter');									
				}
				$filterStr = $filter->getSubQuery($filterData);
				/*/FILTER*/	
				
				/*SORT*/
				$xnyo->filter_get_var('sort','text');
				$sortStr="";
				if (isset($_GET['sort']))
				{
					$sort= new Sort($db,'users',0);
					$sortStr = $sort->getSubQuerySort($_GET['sort']);										
					$smarty->assign('sort',$_GET['sort']);
				}
				else									
					$smarty->assign('sort',0);
									
				if (isset($_GET['searchAction']))									
					$smarty->assign('searchAction',$_GET['searchAction']);
				/*/SORT*/
				
				$pagination = new Pagination($user->queryTotalCount($itemID,$filterStr));
				$pagination->url = "?action=browseCategory&categoryID=users&itemID=$itemID".
					(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
					(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
					(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
					(isset($filterData['filterField'])?"&searchAction=filter":""); 										
				
				
				$usersList=$user->getUsersList($itemID,$pagination, $filterStr,$sortStr);
				$itemsCount=count($usersList);
				for ($i=0; $i<$itemsCount; $i++) {
					$url="admin.php?action=viewDetails&categoryID=users&itemID=$itemID&id=".$usersList[$i]['user_id'];
					$usersList[$i]['url']=$url;
				}
				$smarty->assign("bookmarkType",$itemID);
				$smarty->assign("categoryType","users");
				$smarty->assign("category",$usersList);
				$smarty->assign("itemsCount",$itemsCount);
				$jsSources = array('modules/js/checkBoxes.js');
				$smarty->assign('jsSources', $jsSources);
				$smarty->assign('tpl', 'tpls/users.tpl');
				$smarty->assign('pagination', $pagination);
				$smarty->display("tpls:index.tpl");
				break;
				
			case "issues":
				$title = new Titles($smarty);
				$title->titleIssuesList();
				
				$issue = new Issue($db);
				$issues = $issue->getIssuesList();
				$itemsCount = count($issues);
				
				for ($i=0; $i<$itemsCount; $i++) {
					$url="admin.php?action=viewDetails&categoryID=issue&itemID=issue&id=".$issues[$i]['issueID'];
					$issues[$i]['url']=$url;
				}
				
				$smarty->assign("category", $issues);
				$smarty->assign("itemsCount", $itemsCount);
				
				//$smarty->display("tpls:issuesList.tpl");
				$jsSources = array('modules/js/checkBoxes.js');
				
				$smarty->assign('jsSources', $jsSources);
				$smarty->assign('tpl', 'tpls/issuesList.tpl');
				$smarty->display("tpls:index.tpl");
				break;
			
			case "bulkUploader":
				$title = new Titles($smarty);
				$title->titleBulkUploaderSettings();
				
				$company = new Company($db);
				$companyList = $company->getCompanyList();
				$companyList[] = array('id' => 0, 'name' => 'no company');
				$smarty->assign('companyList',$companyList);
				$smarty->assign('currentCompany',0);
				
				$smarty->assign('doNotShowControls',true);
				//	TODO: internal js script left there
				//$smarty->display("tpls:bulkUploader.tpl");
				$jsSources = array("modules/js/checkBoxes.js",
									"modules/js/reg_country_state.js");
				
				$smarty->assign('jsSources', $jsSources);
				$smarty->assign('tpl', 'tpls/bulkUploader.tpl');
				$smarty->display("tpls:index.tpl");	
				break;
				
			case "vps":				
				switch ($itemID) {
					case "billing":
					
						$billing = new Billing($db);
												
						//getting available billing plans
						$billingPlanList = $billing->getAvailablePlans();
						$smarty->assign("availablePlans",$billingPlanList);
						
						//distinct months count and user count
						$months = $billing->getDistinctMonths();
						$users = $billing->getDistinctUsers();
						$smarty->assign("months",$months);
						$smarty->assign("monthsCount",count($months));
						$smarty->assign("users",$users);
						
						//add column or row
						$xnyo->filter_post_var("add","text");						
						if (isset($_POST['add'])) {
							$smarty->assign("add",$_POST['add']);							
						}	
						
						break;
						
					case "discounts":
						break;
					case "debtors":
						break;
					case "other":
						break;
				}
				$smarty->assign("bookmarkType",$itemID);
				$smarty->display("tpls:vps.tpl");
				break;
				
				
				
			case "track":							
				$smarty->display("tpls:track.tpl");
				break;
				
			case "modulars":
				$modSystem = new ModuleSystem($db);
				$modules = $modSystem->selectAllModules();
				$map = $modSystem->getModulesMap();
				
				$company = new Company($db);
				$companyList = $company->getCompanyList();
				$gacl_api = new gacl_api();
				$xnyo->filter_post_var("modularButton","text");
				$xnyo->filter_post_var("modularID",array("text"));
				
				$defaultModuleList=$modSystem->getDefaultModuleList();
				
//				//	прокомментировать зачем нужен этот foreach Ksenya: скорее всего он был нужен для добавления новых АСО для модулей автоматически
//				$ACOs=$gacl_api->get_object('access',true,'ACO');
				
				foreach($modules as $mod)
				{
					$ACOs=$gacl_api->get_object('access',true,'ACO');											
					$isACOmodule=false;					
					foreach ($ACOs as $ACO)
					{						
						$obj_data=$gacl_api->get_object_data($ACO,'ACO');
						if ($mod->name === $obj_data[0][1]) {
							$isACOmodule=true;				
							break;
						}		
					}				
					if (!$isACOmodule)
						$acoID = $gacl_api->add_object('access', $mod->name, $mod->name, 0, 0, 'ACO');	
				}
				
				if (isset($_POST['modularButton'])) 
				{
					switch ($_POST['modularButton']) 
					{
						case "save": 
						$checkedByCompanies = array();
						foreach ($_POST['modularID'] as $value)
						{
							$value = substr($value,6);
							$pos = strpos($value,'_');
							$checkedByCompanies[substr($value,0,$pos)] []= substr($value,$pos+1);									
						}
						for ($i=0;$i<count($companyList);$i++)
						{
							for ($j=0;$j<count($modules);$j++)
							{	
								$status	= (in_array($modules[$j]->id,$checkedByCompanies[$companyList[$i]["id"]]))?1:0;
								if ($defaultModuleList[$modules[$j]->name][$companyList[$i]["id"]]!= $status && class_exists($map[$modules[$j]->name]))
    							{    					
    								$modSystem->setModule2company($modules[$j]->name, $status, $companyList[$i]["id"]); 
    								$defaultModuleList[$modules[$j]->name][$companyList[$i]["id"]] = $status; //to view them without reloding page!   									
    							}								
							}
						}
						//header("Location: ?action=browseCategory&categoryID=modulars");
						//die();	
						break;
					}									
				} else {
					
				}			
											
				$smarty->assign('defaultModuleList',$defaultModuleList);			
				$smarty->assign('companyList',$companyList);
				$smarty->assign('modules',$modules);						
				//$smarty->display("tpls:Modulars.tpl");
				if (VERSION == 'standalone') {
					$smarty->assign('showInstall','true');
				}
				$smarty->assign('doNotShowControls',true);
				$jsSources = array(
								'modules/js/checkBoxes.js'								
				);
				
				$smarty->assign('jsSources', $jsSources);
				$smarty->assign('tpl', 'tpls/modulars.tpl');
				$smarty->display("tpls:index.tpl");
				
				break;	
				
				case "reports":
							
				$xnyo->filter_post_var("reportButton","text");
				$xnyo->filter_post_var("reportID",array("text"));
				$save=false;									
				if ($_POST['reportButton']=='save') 
				{		
					$save=true;			
						//header("Location: ?action=browseCategory&categoryID=reports");
						//die();																			
				}	
				if (class_exists('MReports')) {				
					$mReports= new MReports();
					$params = array(
									'db' => $db,
									'save'=>$save,
									'setCheckboxes'=>$_POST['reportID']								
									);
					$result = $mReports->prepareDoAdmin($params);
						
					foreach($result as $key => $data) 
					{
						$smarty->assign($key,$data);															
					}							
					
					$jsSources = array(
									'modules/js/checkBoxes.js'									
					);
					
					$smarty->assign('jsSources', $jsSources);
				} else {
					throw new Exception('Deny');
				}
				$smarty->assign('doNotShowControls', true);
				$smarty->display("tpls:index.tpl");
				//header("Location: ?action=browseCategory&categoryID=reports");
				//die();	
				break;	
		}
	}
	
	
	function viewDetails ($categoryID, $itemID, $id, $db, $smarty, $xnyo, User $user) {
		switch ($categoryID) {
			case "class":
				switch ($itemID) {
					case "apmethod":
						$apmethod=new Apmethod($db);
						$apmethodDetails=$apmethod->getApmethodDetails($id);
						$smarty->assign("categoryType","apmethod");
						$smarty->assign("apmethod",$apmethodDetails);
						break;
						
					case "coat":
						$coat=new Coat($db);
						$coatDetails=$coat->getCoatDetails($id);
						$smarty->assign("categoryType","coat");
						$smarty->assign("coat",$coatDetails);
						break;
						
					case "density":
						$density=new Density($db);
						$densityDetails=$density->getDensityDetails($id);
						$smarty->assign("categoryType","density");
						$smarty->assign("density",$densityDetails);
						break;
						
					case "country":
						$country=new Country($db);
						$countryDetails=$country->getCountryDetails($id);
						
						$state=new State($db);
						$stateList=$state->getStateList($countryDetails['country_id']);
						
						$smarty->assign("statesCount",count($stateList));
						$smarty->assign("states",$stateList);
						$smarty->assign("categoryType","country");
						$smarty->assign("country",$countryDetails);
						break;
						
					case "substrate":
						$substrate=new Substrate($db);
						$substrateDetails=$substrate->getSubstrateDetails($id);
						$smarty->assign("categoryType","substrate");
						$smarty->assign("substrate",$substrateDetails);
						break;
						
					case "supplier":
						$supplier=new Supplier($db);
						$supplierDetails=$supplier->getSupplierDetails($id);
						$smarty->assign("categoryType","supplier");
						$smarty->assign("supplier",$supplierDetails);
						break;
						
					case "type":
						$type=new Type($db);
						$typeDetails=$type->getTypeDetails($id);
						$smarty->assign("categoryType","type");
						$smarty->assign("type",$typeDetails);
						break;
						
					case "unittype":
						$unittype=new Unittype($db);
						$unittypeDetails=$unittype->getUnittypeDetails($id);						
						$smarty->assign("categoryType","unittype");
						$smarty->assign("unittype",$unittypeDetails);
						break;
						
					case "msds":
						$msds=new MsdsItem($db);
						$msdsDetails=$msds->getMsdsDetails($id);
						$smarty->assign("categoryType","msds");
						$smarty->assign("msds",$msdsDetails);
						break;
						
					case "lol":
						$lol=new Lol($db);
						$lolDetails=$lol->getLolDetails($id);
						$smarty->assign("categoryType","lol");
						$smarty->assign("lol",$lolDetails);
						break;
						
					case "formulas":
						$formulas=new Formulas($db);
						$formulasDetails=$formulas->getFormulasDetails($id);
						$smarty->assign("categoryType","formulas");
						$smarty->assign("formulas",$formulasDetails);
						break;
						
					case "rule":
						$rule=new Rule($db);
						$ruleDetails=$rule->getRuleDetails($id);
						$smarty->assign("categoryType","rule");
						$smarty->assign("rule",$ruleDetails);
						break;
						
					case "components":
						$components=new Component($db);
						$componentsDetails=$components->getComponentDetails($id);
						$smarty->assign("categoryType","components");
						$smarty->assign("components",$componentsDetails);
						break;
						
					case "product":
						$product=new Product($db);
						$productDetails=$product->getProductDetails($id);
																		
						//density 
						$cDensity = new Density($db, $productDetails['densityUnitID']);
						$densityDetailsTrue = array (
							'numeratorID'	=> $cDensity->getNumerator(),
							'denominatorID'	=> $cDensity->getDenominator(),
							'numerator'		=> '',
							'denominator'	=> ''
						);							
						
						$cUnitType = new Unittype($db);
						$unittypeData = $cUnitType->getUnittypeDetails($densityDetailsTrue['numeratorID']);
						$densityDetailsTrue['numerator'] = $unittypeData['name']; 
						$unittypeData = $cUnitType->getUnittypeDetails($densityDetailsTrue['denominatorID']);
						$densityDetailsTrue['denominator'] = $unittypeData['name']; 
												
						$smarty->assign('densityDetails', $densityDetailsTrue);
						$smarty->assign("categoryType","product");
						$smarty->assign("product", $productDetails);
						break;
						
					case "agency":
						$agency=new Agency($db);
						$agencyDetails=$agency->getAgencyDetails($id);
						$country = new Country($db);
						$countryData=$country->getCountryDetails($agencyDetails['country_id']);
						$agencyDetails['country']=$countryData['country_name'];
						$smarty->assign("categoryType","agency");
						$smarty->assign("agency", $agencyDetails);
						break;
						
					case "emissionFactor":
						$ms = new ModuleSystem($db);
						$map = $ms->getModulesMap();
						
						if (class_exists($map['carbon_footprint'])) {
							$mCarbonFootprint = new $map['carbon_footprint'];
							$emissionFactor = $mCarbonFootprint->getNewEmissionFactorObject($db, $id);
							
							$smarty->assign("emissionFactor", $emissionFactor);
						}
						$smarty->assign("unittype", new Unittype($db));
						break;
				}
				break;
				
			case "users":
				$userDetails=$user->getUserDetails($id);
				$smarty->assign("categoryID", "users");
				$smarty->assign("user", $userDetails);
				break;
				
			case "issue":
				$issue = new Issue($db);
				$issueDetails = $issue->getIssueDetails($id);
				$issueDetails['author'] = $user->getAccessnameByID($issueDetails['creatorID']);
				
				$smarty->assign("issue", $issueDetails);
				$smarty->assign("categoryType", "issue");
				break;
		}
		$smarty->assign("itemID",$itemID);
		$smarty->assign("categoryID",$categoryID);
		//$_SESSION["gobackAction"]="viewDetails";
		$smarty->assign("ID",$id);
		
		//		Set title
		$title=new Titles($smarty);
		$title->titleViewItemAdmin($itemID);
		
		$smarty->display("tpls:viewDetails.tpl");
	}
	
	
	function createModuleACOs($db)
	{
		$gacl_api = new gacl_api();
		$modules= array
				(
					0=>'mInventory',
					1=>'mDocs'
				);
		foreach ($modules as $value)
		{								
			$gacl_api->add_object('access', $value, $value, 0, 0, 'ACO');	
		}		
	}
	
	/**
	 * Sync PHPGACL & current Data Base
	 */
	function syncACL($db) {
		$gacl_api = new gacl_api();	
		$separator = '_';		
		$groupType = 'ARO';
		
		if (!$gacl_api->get_group_id("root"))
		{
			$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
			$aro_group_facility = $gacl_api->add_group('root', 'root', $giantcomliance, 'ARO');		
		}
		
		//if(!$gacl_api->get_object_id ('access','root','ACO'))
		//{
			$acoID = $gacl_api->add_object('access', 'root', 'root', 0, 0, 'ACO');	
			$acoArray = array('access'=>array('root'));
			$aro_group_root=$gacl_api->get_group_id("root");	
			$rootGroup = array($aro_group_root);
			$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root users has access to company ACO ');	
		//}
		
		
		
			
		//	SYNC ARO GROUPS
		$query = "SELECT * FROM ".TB_COMPANY;
		$db->query($query);
				
		if ($db->num_rows() > 0) {
			$companies = $db->fetch_all();
			foreach ($companies as $company) {			
				if (!$gacl_api->get_group_id('company'.$separator.$company->company_id, 'company'.$separator.$company->company_id, 'ARO')) {
					
					//	add to root level
					//$gacl_api->add_group('company'.$separator.$company->company_id, 'company'.$separator.$company->company_id, 0, 'ARO');
										
					//----------------------------------------------------------------
					//GACL
					//----------------------------------------------------------------
					//   ADDING COMPANY
					//   CREATE ACO
					$gacl_api = new gacl_api();
					$acoID = $gacl_api->add_object('access', 'company'.$separator.$company->company_id, 'company'.$separator.$company->company_id, 0, 0, 'ACO');
					//   CREATE ARO GROUP
					$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
					$aro_group_company = $gacl_api->add_group('company'.$separator.$company->company_id, 'company'.$separator.$company->company_id, $giantcomliance, 'ARO');
					$aro_group_root=$gacl_api->get_group_id("root");
					
					//   CREATE ACL
					$acoArray = array('access'=>array('company'.$separator.$company->company_id));		
					$companyGroup = array($aro_group_company);
					$rootGroup = array($aro_group_root);
							
					$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company\'s users has access to company ACO ');
					$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root\'s users has access to company ACO ');
					//-----------------------------------------------------------------
								
					var_dump('company'.$separator.$company->company_id);						
				} 												
			}
		}
		
		$query = "SELECT * FROM ".TB_FACILITY;
		$db->query($query);
		
		if ($db->num_rows() > 0) {
			$facilities = $db->fetch_all();
			foreach ($facilities as $facility) {			
				if (!$gacl_api->get_group_id('facility'.$separator.$facility->facility_id, 'facility'.$separator.$facility->facility_id, 'ARO')) {
					
					//   CREATE ACO
					$gacl_api = new gacl_api();
					$acoID = $gacl_api->add_object('access', 'facility'.$separator.$facility->facility_id, 'facility'.$separator.$facility->facility_id, 0, 0, 'ACO');
					//   CREATE ARO GROUPs
					$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
					$aro_group_facility = $gacl_api->add_group('facility'.$separator.$facility->facility_id, 'facility'.$separator.$facility->facility_id, $giantcomliance, 'ARO');
					$aro_group_company=$gacl_api->get_group_id ('company'.$separator.$facility->company_id);
					$aro_group_root=$gacl_api->get_group_id("root");
					//   CREATE ACL
					$acoArray = array("access"=>array('facility'.$separator.$facility->facility_id));		
					$facilityGroup = array($aro_group_facility);
					$companyGroup = array($aro_group_company);
					$rootGroup = array($aro_group_root);		
					$gacl_api->add_acl($acoArray,NULL,$facilityGroup,NULL,NULL,1,1,NULL,'facility users has access to facility ACO ');
					$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company users has access to facility ACO ');
					$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root users has access to facility ACO ');
											
					//-----------------------------------------------------------------
					var_dump('facility'.$separator.$facility->facility_id);						
				} 												
			}
		}
		
		$query = "SELECT * FROM ".TB_DEPARTMENT;
		$db->query($query);
		
		if ($db->num_rows() > 0) {
			$departments = $db->fetch_all();
			
						
			foreach ($departments as $department) {			
				if (!$gacl_api->get_group_id('department'.$separator.$department->department_id, 'department'.$separator.$department->department_id, 'ARO')) {
					//Search company_id for departaments
					$query = "SELECT * FROM ".TB_FACILITY." WHERE facility_id = ".$department->facility_id;
					$db->query($query);
					$company_id=$db->fetch(0)->company_id;	
					//----------------------------------------------------------------
					//GACL
					//----------------------------------------------------------------
										
					//   CREATE ACO
					$gacl_api = new gacl_api();
					$acoID = $gacl_api->add_object('access', 'department'.$separator.$department->department_id, 'department'.$separator.$department->department_id, 0, 0, 'ACO');
					//   CREATE ARO GROUPs
					$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
					$aro_group_department = $gacl_api->add_group('department'.$separator.$department->department_id, 'department'.$separator.$department->department_id, $giantcomliance, 'ARO');
					$aro_group_facility=$gacl_api->get_group_id ('facility'.$separator.$department->facility_id);
					$aro_group_company=$gacl_api->get_group_id ('company'.$separator.$company_id);
					$aro_group_root=$gacl_api->get_group_id("root");
					//   CREATE ACL
					$acoArray = array('access'=>array('department'.$separator.$department->department_id));		
					$departmentGroup = array($aro_group_department);
					$facilityGroup = array($aro_group_facility);
					$companyGroup = array($aro_group_company);
					$rootGroup = array($aro_group_root);	
					$gacl_api->add_acl($acoArray,NULL,$departmentGroup,NULL,NULL,1,1,NULL,'department users has access to department ACO ');	
					$gacl_api->add_acl($acoArray,NULL,$facilityGroup,NULL,NULL,1,1,NULL,'facility users has access to department ACO ');
					$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company users has access to department ACO ');
					$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root users has access to department ACO ');
					
					//-----------------------------------------------------------------
					
					var_dump('department'.$separator.$department->department_id);						
				} 												
			}
		}
					
		//	SYNC ARO'S
		$query = "SELECT * FROM ".TB_USER."";
		$db->query($query);
		
		if ($db->num_rows() > 0) {
			$users = $db->fetch_all();
			foreach ($users as $user) {			
				//if (!$gacl_api->get_object_id('users', $user->accessname, 'ARO')) {
					//$gacl_api->add_object('users', $user->accessname, $user->accessname, 0, 0, 'ARO');					
				//	$login_lower=strtolower($user->accessname);
				//	$groupID=$user->accesslevel_id+11;
				//	$gacl_api->add_object('users', $user->accessname, $login_lower, NULL, 0, 'ARO');
				//	$gacl_api->add_group_object($groupID, 'users', $login_lower, 'ARO');
											
				//	$gacl_api->add_object('users', $user->accessname, $user->accessname, 0, 0, 'ARO');		
					
					//	form group name & value
					switch($user->accesslevel_id) {
						case 0:
							$aroGroupName = 'company'.$separator.$user->company_id;				
							break;
						case 1:
							$aroGroupName = 'facility'.$separator.$user->facility_id;				
							break;
						case 2:
							$aroGroupName = 'department'.$separator.$user->department_id;				
							break;
						case 3:
							$aroGroupName = 'root';
							break;
						default:
							throw new Exception('Incorrect access level');
					}
					
					//	yes, they are equal		
					$aroGroupValue = $aroGroupName;
							
					if (false !== ($aroGroupID = $gacl_api->get_group_id($aroGroupName, $aroGroupValue, $groupType)) ) {
						//	ARO GROUP FOUND
						$gacl_api->add_group_object($aroGroupID, 'users', $user->accessname, $groupType);			 	
					} else {
						//	ARO GROUP NOT FOUND
						throw new Exception('ARO group not found');
					}
														
				//} 	
				var_dump('user'.$separator.$user->user_id);
				echo $aroGroupValue;											
			}
		}	
	}
	
	
	
	if (!isset($_GET["action"])) {
		if (isset($_POST["action"])) {
			$user = new User($db, $xnyo, $access, $auth);
			if ((!($user->isLoggedIn()) || $user->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) != 3) && $_POST["action"] != 'auth') {
				header ('Location: '.$xnyo->logout_redirect_url.'?error=auth');
			}
			switch ($_POST["action"]) {
				case 'auth':
					$xnyo->filter_post_var('accessname', 'text');
					$xnyo->filter_post_var('password', 'text');														
					
					$accessLevel=$user->getUserAccessLevelIDByAccessname($_POST["accessname"]);				
					if ($user->auth($_POST["accessname"], $_POST["password"]) && $accessLevel==3) {							
						if ($access->check('required')) {
							//	authorized
							session_start();								
							$_SESSION['user_id'] = $user->getUserIDbyAccessname($_POST["accessname"]);
							$_SESSION['accessname'] = $_POST['accessname'];
							$_SESSION['username'] = $user->getUsernamebyAccessname($_POST["accessname"]);																					
							
							header("Location: admin.php?action=browseCategory&categoryID=class&itemID=apmethod");							
						} else {
							//	not authorized
							header ('Location: '.$xnyo->logout_redirect_url.'?error=auth');
						}
					} else {
						//echo "Authorization failed!<br>";
						header ('Location: '.$xnyo->logout_redirect_url.'?error=auth');
					}					
					break;
					
					
				case "bulkUpload":				
					$xnyo->filter_post_var("categoryID","text");
					$xnyo->filter_post_var("maxNumber","int");
					$xnyo->filter_post_var("inputFile","file");
					$xnyo->filter_post_var("threshold","int");
					$xnyo->filter_post_var("update","text");
					$xnyo->filter_post_var("companyID","int");
				
					
					$input = array (
						"maxNumber" => $_POST['maxNumber'],
						"threshold" => $_POST['threshold'],
						"update"	=> $_POST['update'],
						"companyID"	=> $_POST['companyID']
					);
					//we should check input file!
					if ($input['size']<1024000) {
						$input['inputFile'] = $_FILES['inputFile']['tmp_name'];
						$input['realFileName'] = basename($_FILES['inputFile']['name']);								
						$bu = new bulkUploader($db,$input);
					
						$errorCnt = count($bu->productsError);
						$correctCnt = count($bu->productsCorrect);					
						$total =  $errorCnt + $correctCnt;										
										
						$title = new Titles($smarty);
						$title->titleBulkUploadResults();
					
						$smarty->assign("categoryID","tab_".$_POST['categoryID']);
						$smarty->assign("productsError",$bu->productsError);
						$smarty->assign("errorCnt",$errorCnt);
						$smarty->assign("correctCnt",$correctCnt);
						$smarty->assign("total",$total);
						$smarty->assign("input",$input);
						$smarty->assign("insertedCnt",$bu->insertedCnt);
						$smarty->assign("updatedCnt",$bu->updatedCnt);
						$smarty->assign("validationResult",$bu->validationResult);
						$smarty->assign("actions",$bu->actions);
					
					
						$smarty->display('tpls:bulkUploader.tpl');	
					}					
				break;	
			}			
		} else {
			//	No action
			//	Show Login page
		
			/*$smarty->assign('temp_url', 'admin.php?action=testTPL');
			$smarty->assign('register_url', 'admin.php?action=registration');*/
		
			$smarty->display('tpls:adminLogin.tpl');	
		}		
	} else {
		$smarty->assign("action", $_GET["action"]);
		$user=new User($db, $xnyo, $access, $auth);
		
		if (!($user->isLoggedIn()) || $user->getUserAccessLevelIDByAccessname($_SESSION["accessname"]) != 3) {		
			header ('Location: '.$xnyo->logout_redirect_url.'?error=timeout');
		}
		
		switch ($_GET["action"]) {			
			
			case "browseCategory":
				$xnyo->filter_get_var('itemID','text');
				$xnyo->filter_get_var('categoryID','text');
				
				$request = $_GET;
				$smarty->assign('request',$request);
				
				$itemID = $_GET['itemID'];
				$categoryID = $_GET['categoryID'];
				showCategory($categoryID, $itemID, $db, $smarty, $xnyo);
			break;
			
			
			case "viewDetails":
				$xnyo->filter_get_var('categoryID','text');
				$xnyo->filter_get_var('itemID','text');
				$xnyo->filter_get_var('id','text');
				
				$request = $_GET;
				$smarty->assign('request',$request);
				
				$categoryID = $_GET['categoryID'];
				$itemID 	= $_GET['itemID'];
				$id 		= $_GET['id'];
				viewDetails ($categoryID, $itemID, $id, $db, $smarty, $xnyo, $user);
			break;
					
			case 'edit':
				$xnyo->filter_get_var('categoryID','text');
				$xnyo->filter_get_var('itemID','text');
				$xnyo->filter_get_var('id','text');
				$xnyo->filter_post_var('save','text');
				
				$categoryID = $_GET['categoryID'];
				$itemID		= $_GET['itemID'];
				$id			= $_GET['id'];
				$save       = $_POST['save'];
				
				switch ($categoryID) {
					case "class":
						switch ($itemID) {
							case "apmethod":
								$apmethod=new Apmethod($db);							
								if ($save=='Save')
								{	
									$xnyo->filter_post_var('apmethod_desc', 'text');
									$data=array(
										"apmethod_id"	=>	$id,
										"apmethod_desc"	=>	$_POST["apmethod_desc"]
									);									
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									
									if (!($validate->isUniqueName("apmethod", $data["apmethod_desc"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['apmethod_desc'] = 'alredyExist';
									}								
									if ($validStatus["summary"] == "true") {
										$apmethod->setApmethodDetails($data);
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=apmethod&id='.$id);
										die();											
									}
								}
								else
								{									
									$data=$apmethod->getApmethodDetails($id);
								}								
								
								//	IF ERRORS OR NO POST REQUEST	
								if ($validStatus["summary"] == "false") 
								{	
									$notify=new Notify($smarty);
									$notify->formErrors();
									$title=new Titles($smarty);
									$title->titleEditItemAdmin($itemID);
								}
								$smarty->assign('tpl','tpls/addApmethodClass.tpl');
								break;
								
							case "coat":					
								$coat=new Coat($db);
								if ($save=='Save')
								{	
									$xnyo->filter_post_var('coat_desc', 'text');
									$data=array(
										"coat_id"	=>	$id,
										"coat_desc"	=>	strtoupper($_POST["coat_desc"])
									);									
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									
									if (!($validate->isUniqueName("coat", $data["coat_desc"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['coat_desc'] = 'alredyExist';
									}								
									
									if ($validStatus["summary"] == "true") {
										$coat->setCoatDetails($data);
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=coat&id='.$id);
										die();										
									} 									
								}
								else
								{									
									$data=$coat->getCoatDetails($id);
								}								
								
								//	IF ERRORS OR NO POST REQUEST
								if ($validStatus["summary"] == "false") 
								{
									$notify=new Notify($smarty);
									$notify->formErrors();
									$title=new Titles($smarty);
									$title->titleEditItemAdmin($itemID);
								}
								$smarty->assign('tpl','tpls/addCoatClass.tpl');

								
								break;
								
							case "density":			
								$density=new Density($db);					
								if ($save=='Save')
								{			
									$xnyo->filter_get_var('density_type', 'text');
									$data=array(
										"density_id"	=>	$id,
										"density_type"	=>	$_GET["density_type"]
									);							
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									
									if (!($validate->isUniqueName("density", $data["density_type"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['density_type'] = 'alredyExist';
									}									
									
									if ($validStatus["summary"] == "true") {
										$density->setDensityDetails($data);
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=density');
										die();									
									} 
								}
								else
								{									
									$data=$density->getDensityDetails($id);
								}
								
								//	IF ERRORS OR NO POST REQUEST
								if ($validStatus["summary"] == "false") 
								{
									$notify=new Notify($smarty);
									$notify->formErrors();
									$title=new Titles($smarty);
									$title->titleEditItemAdmin($itemID);
								}
								$smarty->assign('tpl','tpls/addDensityClass.tpl');
								break;
								
							case "country":
								$country=new Country($db);	
								$stateInfo=new State($db);	
								
								if (isset($save))
								{														
									$xnyo->filter_post_var("state_name", "text");
									$xnyo->filter_post_var("country_name", "text");
									$xnyo->filter_post_var('stateCount','text');
									$xnyo->filter_post_var('date_type','text');							
									$validation=new Validation($db);															
									
									if ($_POST['stateCount']=="") {
										$stateCount=0;
									} else {
										$stateCount=$_POST['stateCount'];
									}
									for ($i=0;$i<$stateCount;$i++) {
										$xnyo->filter_post_var('state_id_'.$i,'text');
										if (isset($_POST['state_id_'.$i])) {
											$xnyo->filter_post_var('state_name_'.$i,'text');
											$f=true;
											for ($j=0; $j<count($states); $j++) {
												if ($_POST['state_name_'.$i]==$states[$j]['name']) {
													$f=false;
													break;
												}
											}
											if ($_POST['state_name_'.$i]=="") {
												$f=false;
											}	
											if ($f==true) {	
												$state=array(
													"state_id"	=>	$_POST['state_id_'.$i],
													"name"	=>	$_POST['state_name_'.$i]
												);
												$states[]=$state;
											}
										}
									}
									
									$countryData=array (
										"country_id"		=>	$_GET["id"],
										"country_name"		=>	$_POST["country_name"],
										"date_type"			=>	$_POST["date_type"],
										"user_id"			=>	18,
										"states" 			=> $states
									);
								}
								//	IF NO POST REQUEST
								else
								{									
									$data=$country->getCountryDetails($id);									
									$stateList=$stateInfo->getStateList($data['country_id']);
									$data['states']=$stateList;
									$smarty->assign('statesAdded', $data);
									$smarty->assign("stateCount",count($stateList));															
//									$smarty->assign("data",$data);
								}
								//	END IF NO POST REQUEST
								
								if ($save=='Save') 
								{
									$validateStatus=$validation->validateRegDataAdminClasses($countryData);								
									if (!$validation->isUniqueName("country", $countryData['country_name'], 'none', $countryData['country_id'])) {
										$validateStatus['summary'] = 'false';
										$validateStatus['country_name'] = 'alredyExist';
									}									
									if ($validateStatus['summary'] == "true") {
										$country->setCountryDetails($countryData);										
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=country&id='.$countryData['country_id']);
										die();										
									} 
									
									//	IF ERRORS
									else {										
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
										$statesAdded['states']=$states;
										$data['country_name']=$_POST['country_name'];
										$data['date_type']=$_POST['date_type'];																
//										$smarty->assign('validStatus', $validateStatus);
										$smarty->assign('statesAdded', $statesAdded);
										$smarty->assign('stateCount', count($countryData['states']));										
									}									
								}
								
								if ($save=='Add state to country') 
								{									
									$stateForCheck['state_name']=$_POST['state_name'];
									$validateStatus=$validation->validateRegDataAdminClasses($stateForCheck);
									for ($i=0;$i<$stateCount;$i++) {
										if (trim($_POST['state_name'])==trim($states[$i]['name']) && trim($_POST['state_name'])!="") {
											$validateStatus['summary'] = 'false';
											$validateStatus['state_name'] = 'alredyExist';
										}
									}
									if ($validateStatus['summary'] == 'true') {
										$maxStateID=$states[0]['state_id'];
										for ($i=1;$i<count($states);$i++) {
											if ($states[$i]['state_id']>$maxStateID) {
												$maxStateID=$states[$i]['state_id'];
											}
										}										
										$state=array(
											"state_id"	=>	$maxStateID+1,
											"name"	=>	$_POST['state_name']
										);
										$states[]=$state;
									}
									
									$statesAdded['states']=$states;								
									$smarty->assign('statesAdded', $statesAdded);									
									if ($validateStatus['summary'] == 'true')  {
										$data["country_name"]=$_POST['country_name'];
										$data['date_type']=$_POST['date_type'];										
										$smarty->assign('stateCount', count($statesAdded['states']));
									} else {
										$notify=new Notify($smarty);
										$notify->formErrors();
										$data=array(
											"country_name"	=>	$_POST['country_name'],
											"state_name"	=>	$_POST['state_name'],
											"date_type"		=>	$_POST["date_type"]
										);										
//										$smarty->assign('validStatus', $validateStatus);
										$smarty->assign('stateCount', count($statesAdded['states']));
									}
									$title=new Titles($smarty);
									$title->titleEditItemAdmin($itemID);																	
									$doNotShow=true;
								}
								$validStatus = $validateStatus;
								$smarty->assign('tpl','tpls/addCountryClass.tpl');	
								break;
								
							case "substrate":
								$substrate=new Substrate($db);
								if ($save=='Save')
								{	
									$xnyo->filter_post_var('substrate_desc', 'text');
									$data=array(
										"substrate_id"	=>	$id,
										"substrate_desc"	=>	$_POST["substrate_desc"]
									);								
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									
									if (!($validate->isUniqueName("substrate", $data["substrate_desc"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['description'] = 'alredyExist';
									}																	
									if ($validStatus["summary"] == "true") {
										$substrate->setSubstrateDetails($data);
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=substrate&id='.$id);
										die();									
									}
									else
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									} 
								}
								else
								{									
									$data=$substrate->getSubstrateDetails($id);
								}
								$smarty->assign('tpl','tpls/addSubstrateClass.tpl');							
								break;
								
								
							case "supplier":
								$supplier=new Supplier($db);								
								if ($save=='Save')
								{	
									$xnyo->filter_post_var('supplier_desc', 'text');
									$xnyo->filter_post_var('contact', 'text');
									$xnyo->filter_post_var('phone', 'text');
									$xnyo->filter_post_var('address', 'text');
									$xnyo->filter_post_var('country','text');
									$data=array(
										"supplier_id"	=>	$id,
										"description"	=>	$_POST["supplier_desc"],
										"contact"=>	$_POST["contact"],
										"phone"	=>	$_POST["phone"],
										"address"	=>	$_POST["address"],
										"country_id"		=>  $_POST["country"]
									);
	
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									
									if (!($validate->isUniqueName("supplier", $data["description"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['description'] = 'alredyExist';
									}									
									if ($validStatus["summary"] == "true") {
										$supplier->setSupplierDetails($data);
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=supplier&id='.$id);
										die();										
									}
									else
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									} 
								}
								else 
								{									
									$data=$supplier->getSupplierDetails($id);
								}								
								$registration = new Registration($db);
								$countries = $registration->getCountryList();
								
								$smarty->assign("country",$countries);
								$smarty->assign('tpl','tpls/addSupplierClass.tpl');
								break;						
								
							case "rule":
								$rule=new Rule($db);
								if ($save=='Save')
								{	
									$xnyo->filter_post_var('rule_nr_us', 'text');
									$xnyo->filter_post_var('rule_nr_eu', 'text');
									$xnyo->filter_post_var('rule_nr_cn', 'text');
									$xnyo->filter_post_var('rule_desc', 'text');
									$xnyo->filter_post_var('country', 'text');
									$xnyo->filter_post_var('county', 'text');
									$xnyo->filter_post_var('city', 'text');
									$xnyo->filter_post_var('zip', 'text');
																		
									$data=array(
										"rule_id"	=>	$id,
										"rule_nr"	=>	$_POST[$rule->ruleNrMap[$rule->getRegion()]],
										"rule_nr_us"	=>	$_POST["rule_nr_us"],
										"rule_nr_eu"	=>	$_POST["rule_nr_eu"],
										"rule_nr_cn"	=>	$_POST["rule_nr_cn"],
										"rule_desc"	=>	$_POST["rule_desc"],
										"country"	=>	$_POST["country"],
										"county"	=>	$_POST["county"],
										"city"	=>	$_POST["city"],
										"zip"	=>	$_POST["zip"]
									);									
									
									$registration=new Registration($db);
									if ($registration->isOwnState($data["country"])) {
										$xnyo->filter_post_var("selectState", "text");
										$data["state"] = $_POST["selectState"];
									} else {
										$xnyo->filter_post_var("textState", "text");
										$data["state"] = $_POST["textState"];
									}
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);

									$checkUnique = $validate->isUniqueRule($data);
									if ($checkUnique !== true) {
										$validStatus['summary'] = 'false';
										foreach ($checkUnique as $ruleNR=>$value) {
											$validStatus[$ruleNR] = 'alredyExist';	
										}
										
									}	
																	
									if ($validStatus["summary"] == "true") {
										$rule->setRuleDetails($data);
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=rule&id='.$id);
										die();										
									} 
									else
									{
										if ($validStatus["rule_nr"] == 'failed') {											
											$validStatus[$rule->ruleNrMap[$rule->getRegion()]] = 'failed';
										}
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									}
								}
								else 
								{									
									$data=$rule->getRuleDetails($id, true);							
								}								
								$country=new Country($db);
								$countries=$country->getCountryList();
								$smarty->assign("country",$countries);
								$registration=new Registration($db);
								if ($registration->isOwnState($data["country"])) {
									$smarty->assign("selectMode", true);
									$state=new State($db);
									$states=$state->getStateList($data["country"]);
									$smarty->assign("state",$states);
								}
								$smarty->assign('tpl','tpls/addRuleClass.tpl');
								break;
								
							case "components":
								$components=new Component($db);
								if ($save=='Save')
								{	
									/*$xnyo->filter_get_var('comp_name', 'text');*/
									$xnyo->filter_post_var('description', 'text');
									$xnyo->filter_post_var('EINECS', 'text');
									$xnyo->filter_post_var('cas', 'text');
									/*$xnyo->filter_get_var('country', 'text');
									$xnyo->filter_get_var('msds', 'text');
									$xnyo->filter_get_var('product_code', 'text');
									$xnyo->filter_get_var('type', 'text');
									$xnyo->filter_get_var('weight', 'text');
									$xnyo->filter_get_var('supplier', 'text');
									$xnyo->filter_get_var('density', 'text');*/
									$regData=array(
										"component_id"	=>	$id,
										/*"comp_name"	=>	$_GET["comp_name"],*/
										"description"	=>	$_POST["description"],
										"EINECS"	=>	$_POST["EINECS"],
										"cas"	=>	$_POST["cas"]
										/*"country"	=>	$_GET["country"],
										"msds_id"	=>	$_GET["msds"],
										"product_code"	=>	$_GET["product_code"],
										"comp_type"	=>	$_GET["type"],
										"comp_weight"	=>	$_GET["weight"],
										"supplier"	=>	$_GET["supplier"],
										"comp_density"	=>	$_GET["density"]*/
																  //"sara"	=>	"yes"
									);
									/*$registration=new Registration($db);
									if ($registration->isOwnState($regData["country"])) {
										$xnyo->filter_get_var("selectState", "text");
										$regData["state"] = $_GET["selectState"];
									} else {
										$xnyo->filter_get_var("textState", "text");
										$regData["state"] = $_GET["textState"];
									}*/
									
									$agency=new Agency($db);
									$agencyCount=$agency->getAgencyCount();									
									$regData['agencies']=$components->getComponentAgencies($regData['component_id']);
									
									for ($i=0; $i < $agencyCount; $i++) {
										$xnyo->filter_post_var("agency_".$i,"text");
										if (isset($_POST['agency_'.$i])) {
											$regData['agencies'][$i]['control']='yes';
										} else {
											$regData['agencies'][$i]['control']='no';
										}
										
									}
									
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($regData);
									
									if (!($validate->isUniqueName("component", $regData['cas'], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['cas'] = 'alredyExist';
									}
									
									if ($validStatus["summary"] == "true") {
										$components->setComponentDetails($regData);
										
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=components&id='.$id);
										die();									
									}
									else
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									} 
									$data = $regData;
								}
								else 
								{									
									$data=$components->getComponentDetails($id, true);								
								}									
																	
								/*$country=new Country($db);
								$countries=$country->getCountryList();
								$smarty->assign("country",$countries);
								$registration=new Registration($db);
								if ($registration->isOwnState($regData["country"])) {
									$smarty->assign("selectMode", true);
									$state=new State($db);
									$states=$state->getStateList($regData["country"]);
									$smarty->assign("state",$states);
								}
									
								$msds=new MsdsItem($db);
								$msdsList=$msds->getMsdsList();
								$smarty->assign("msds",$msdsList);
									
								$type=new Type($db);
								$typeList=$type->getTypeList();
								$smarty->assign("type",$typeList);
									
								$supplier=new Supplier($db);
								$supplierList=$supplier->getSupplierList();
								$smarty->assign("supplier",$supplierList);*/

								$smarty->assign('tpl','tpls/addComponentsClass.tpl');
								break;
								
							case "product":
								$product = new Product($db);
								
								if (isset($save))
								{
									$xnyo->filter_post_var("save", "text");
									$xnyo->filter_post_var("product_nr", "text");
									$xnyo->filter_post_var("name", "text");
									$xnyo->filter_post_var("selectComponent", "text");
									$xnyo->filter_post_var("density", "text");
									$xnyo->filter_post_var("selectDensityType","int");
									$xnyo->filter_post_var("selectInventory", "text");
									$xnyo->filter_post_var("selectCoat", "text");
									$xnyo->filter_post_var("specialty_coating", "text");
									$xnyo->filter_post_var("aerosol", "text");
									$xnyo->filter_post_var("specific_gravity", "text");
									$xnyo->filter_post_var("selectSupplier", "text");
									$xnyo->filter_post_var("componentCount", "text");
									$xnyo->filter_post_var("voclx", "text");
									$xnyo->filter_post_var("vocwx", "text");
									$xnyo->filter_post_var("boiling_range_from", "text");
									$xnyo->filter_post_var("boiling_range_to", "text");
									$xnyo->filter_post_var("percent_volatile_weight", "text");
									$xnyo->filter_post_var("percent_volatile_volume", "text");
									//$xnyo->filter_get_var("hazardous_class_id", "text");
									//$xnyo->filter_get_var("hazardous_class", "text");
									//$xnyo->filter_get_var("irr", "text");
									//$xnyo->filter_get_var("ohh", "text");
									//$xnyo->filter_get_var("sens", "text");
									//$xnyo->filter_get_var("oxy_1", "text");								
									
									//	replace false/true for no/yes
									//$specialty_coating 	= (!isset($_GET['specialty_coating'])) ? "no" : "yes";
									//$aerosol 			= (!isset($_GET['aerosol'])) ? "no" : "yes";
									//$irr 				= (!isset($_GET['irr'])) ? "no" : "yes";
									//$ohh 				= (!isset($_GET['ohh'])) ? "no" : "yes";
									//$sens 				= (!isset($_GET['sens'])) ? "no" : "yes";
									//$oxy_1				= (!isset($_GET['oxy_1'])) ? "no" : "yes";
																																																																																
									$productData = array (
										"product_id"		=>	$id,
										"product_nr"		=>	$_POST["product_nr"],
										"name"				=>	$_POST["name"],
										"component_id"		=>	$_POST["selectComponent"],
										"density"			=>	$_POST["density"],
										"density_unit_id"	=>  $_POST["selectDensityType"],
										"inventory_id"		=>	$_POST["selectInventory"],
										"coating_id"		=>	$_POST["selectCoat"],
										"specialty_coating"	=>	(!isset($_POST['specialty_coating'])) ? "no" : "yes",
										"aerosol"			=>	(!isset($_POST['aerosol'])) ? "no" : "yes",
										"specific_gravity"	=>	$_POST["specific_gravity"],
										"supplier_id"		=>	$_POST["selectSupplier"],
										"vocwx"				=>	$_POST["vocwx"],
										"voclx"				=>	$_POST["voclx"],
										"boiling_range_from"=>	$_POST["boiling_range_from"],
										"boiling_range_to"	=>	$_POST["boiling_range_to"],
										"percent_volatile_weight"=>	$_POST["percent_volatile_weight"],
										"percent_volatile_volume"	=>	$_POST["percent_volatile_volume"],
										//"hazardous_class_id"=>	$_GET["hazardous_class_id"],
										//"hazardous_class"	=>	$_GET["hazardous_class"],
										//"class"				=>	$_GET["hazardous_class"],
										//"irr"				=>	$irr,
										//"ohh"				=>	$ohh,
										//"sens"				=>	$sens,
										//"oxy_1"				=>	$oxy_1,
										"creator_id"		=>	18
									);
									
									//	process hazardous (chemical) classes
									$hazardous = new Hazardous($db);
									$chemicalClassesList = $hazardous->getChemicalClassesList();
									for ($i=0;$i<count($chemicalClassesList);$i++) {
										$xnyo->filter_post_var("chemicalClass_".$i, "text");
										if (isset($_POST['chemicalClass_'.$i])) {
											$chemicalClass = $hazardous->getChemicalClassDetails($_POST['chemicalClass_'.$i]);										
											$chemicalClasses[] = $chemicalClass;
										}										
									}								
									$productData['chemicalClasses'] = $chemicalClasses; 
									
									//	process components
									$componentCount = $_POST['componentCount'];
									for ($i=0;$i<$componentCount;$i++) {
										$xnyo->filter_post_var('component_id_'.$i,'text');
										if (isset($_POST['component_id_'.$i])) {
											$xnyo->filter_post_var('comp_cas_'.$i,'text');
											$xnyo->filter_post_var('temp_vp_'.$i,'text');
											$xnyo->filter_post_var('substrate_'.$i,'text');
											$xnyo->filter_post_var('rule_id_'.$i,'text');
											$xnyo->filter_post_var('mm_hg_'.$i,'text');
											$xnyo->filter_post_var('weight_'.$i,'text');
											$xnyo->filter_post_var('type_'.$i,'text');
											
											$component = array (
												"component_id"	=>	$_POST['component_id_'.$i],
												"comp_cas"		=>	$_POST['comp_cas_'.$i],
												"temp_vp"		=>	$_POST['temp_vp_'.$i],
												"substrate_id"		=>	$_POST['substrate_'.$i],
												"rule_id"		=>	$_POST['rule_id_'.$i],
												"mm_hg"			=>	$_POST['mm_hg_'.$i],
												"weight"		=>	$_POST['weight_'.$i],
												"type"			=>	$_POST['type_'.$i]
											);
											$components[] = $component;
										}
									}
									$productData['components'] = $components;
									$validation = new Validation($db);
								}
								//	IF NO POST REQUEST
								else
								{									
									$productData = $product->getProductDetails($id, true);
									
									//$inventoryDetails=$inventory->getInventoryDetails($categoryDetails['inventory_id'], true);
									//$categoryDetails["inventory_desc"]=$inventoryDetails["inventory_desc"];								
									
									$smarty->assign("componentCount", count($productData['components']));
									$smarty->assign("compsAdded", $productData['components']);
									
									$component=new Component($db);
									
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
									$smarty->assign("component", $componentsList);
									
									$componentDetails=$component->getComponentDetails($componentsList[0]['component_id'],true);
									$productData['cas']=$componentDetails['cas'];
									$productData['comp_desc']=$componentDetails['description'];
									
									$rule=new Rule($db);
									$smarty->assign("rule", $rule->getRuleList());
									
									$coat=new Coat($db);
									$smarty->assign("coat", $coat->getCoatList());
									
									$substrate=new Substrate($db);
									$smarty->assign("substrate", $substrate->getSubstrateList());
									
									$supplier=new Supplier($db);
									$smarty->assign("supplier", $supplier->getSupplierList());
									
									//	hazardous (chemical) class list (popup)
									$hazardous = new Hazardous($db);
									$chemicalClassesList = $hazardous->getChemicalClassesList();
									$smarty->assign("chemicalClassesList",$chemicalClassesList);
									
									//	form chemical class pagination
									//$numberOfPages = ceil(count($chemicalClassesList)/15);
									//$smarty->assign("numberOfPages", $numberOfPages);	
									
									//density 
									$cDensity = new Density($db);
									$cUnitType = new Unittype($db);
									$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
														
									$smarty->assign('densityDetails', $densityDetailsTrue);
									$smarty->assign('densityDefault', $productData['densityUnitID']);								
								}
								//	END NO POST REQUEST
								
								if ($_POST['save'] == "Save") 
								{									
									$validStatus = $validation->validateRegDataProduct($productData);
																	
									//check for duplicate names
									if (!($validation->isUniqueName("product", $productData["product_nr"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus['product_nr'] = 'alredyExist';
									}
									$product=new Product($db);
									
									if ($validStatus['summary'] == 'true') {
										
										$product->setProductDetails($productData);
										//$product_id = $product->getProductIdByName($productData['product_nr']);
										
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=product&id='.$id);
										die();																		
										
									} else {
										$notify = new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItem($_POST["itemID"]);
										
										$smarty->assign('validStatus', $validStatus);
										
										$xnyo->filter_post_var("temp_vp","text");
										$xnyo->filter_post_var("substrate_id","text");
										$xnyo->filter_post_var("rule_id","text");
										$xnyo->filter_post_var("mm_hg","text");
										$xnyo->filter_post_var("weight","text");
										$xnyo->filter_post_var("selectSubstrate","text");
										$xnyo->filter_post_var("selectRule","text");
										$xnyo->filter_post_var("weight","text");
										$xnyo->filter_post_var("type","text");
										
										
										$productData['temp_vp']		= $_POST['temp_vp'];
										$productData['substrate_id']= $_POST['substrate_id']; //???
										$productData['rule_id']		= $_POST['rule_id'];
										$productData['mm_hg']		= $_POST['mm_hg'];
										$productData['weight']		= $_POST['weight'];
										$productData['type']		= $_POST['type'];										
										$productData['substrate_id']= $_POST['selectSubstrate'];
										$productData['rule_id']		= $_POST['selectRule'];																														
										
//										$inventory=new Inventory($db);
//										$inventoryList=$inventory->getInventoryList();
//										$smarty->assign("inventory", $inventoryList);
//										
//										$inventoryDetails=$inventory->getInventoryDetails($productData['inventory_id']);
//										$productData["inventory_desc"]=$inventoryDetails["inventory_desc"];
										
										$product=new Product($db);
										$productList=$product->getProductList();
										$component=new Component($db);
										
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
										
										$smarty->assign("component", $componentsList);
										$smarty->assign("componentCount", count($components));
										$smarty->assign("compsAdded", $components);									
										//								$smarty->assign("component", $component->getComponentList());
										
										$rule=new Rule($db);
										$smarty->assign("rule", $rule->getRuleList());
										
										$coat=new Coat($db);
										$smarty->assign("coat", $coat->getCoatList());
										
										$substrate=new Substrate($db);
										$smarty->assign("substrate", $substrate->getSubstrateList());
										
										$supplier=new Supplier($db);
										$smarty->assign("supplier", $supplier->getSupplierList());
										
										//	get hazardous (chemical) class
										$hazardous = new Hazardous($db);
										$chemicalClassesList = $hazardous->getChemicalClassesList();
										$smarty->assign("chemicalClassesList", $chemicalClassesList);
										
										//density 
										$cDensity = new Density($db);
										$cUnitType = new Unittype($db);
										$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
												
										$smarty->assign('densityDetails', $densityDetailsTrue);
										$smarty->assign('densityDefault', $productData['density_unit_id']);									
									}
								} 
								if ($_POST['save'] == 'Add component to product')
								{									
									$component = new Component($db);
									$data2 = $component->getComponentDetails($_POST['selectComponent'],true);
									$xnyo->filter_post_var("temp_vp","text");
									$xnyo->filter_post_var("selectSubstrate","text");
									$xnyo->filter_post_var("selectRule","text");
									$xnyo->filter_post_var("mm_hg","text");
									$xnyo->filter_post_var("weight","text");
									$xnyo->filter_post_var("type","text");
									
									$componentNew = array(
										"component_id"	=>	$_POST['selectComponent'],
										"comp_cas"		=>	$data2['cas'],
										"temp_vp"		=>	$_POST['temp_vp'],
										"substrate_id"	=>	$_POST['selectSubstrate'],
										"rule_id"		=>	$_POST['selectRule'],
										"mm_hg"			=>	$_POST['mm_hg'],
										"weight"		=>	$_POST['weight'],
										"type"			=>	$_POST['type']
									);
									
									//	get hazardous (chemical) class
									$hazardous = new Hazardous($db);
									$chemicalClassesList = $hazardous->getChemicalClassesList();
									$smarty->assign("chemicalClassesList", $chemicalClassesList);
									
									$validateStatus = $validation->validateNewComponent($componentNew);
									if ($validateStatus['summary'] == "true") {
										$components[] = $componentNew;
									} else {
//										$smarty->assign("validStatus",$validateStatus);
										$validStatus = $validateStatus;
										$productData['temp_vp']		= $_POST['temp_vp'];
										$productData['substrate_id']= $_POST['selectSubstrate'];
										$productData['rule_id']		= $_POST['selectRule'];
										$productData['mm_hg']		= $_POST['mm_hg'];
										$productData['weight']		= $_POST['weight'];
										$productData['type']		= $_POST['type'];										
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
									$smarty->assign("component", $componentsList);
									
									if ($validateStatus['summary'] = "true") {
										$componentDetails = $component->getComponentDetails($componentsList[0]['component_id'],true);
										$productData['cas'] = $componentDetails['cas'];
										$productData['comp_desc'] = $componentDetails['description'];
									}
									
//									$inventory = new Inventory($db);
//									$inventoryList = $inventory->getInventoryList();
//									$smarty->assign("inventory", $inventoryList);
//									$inventoryDetails = $inventory->getInventoryDetails($productData['inventory_id']);
//									$productData["inventory_desc"] = $inventoryDetails["inventory_desc"];						
									
									$coat=new Coat($db);
									$smarty->assign("coat", $coat->getCoatList());
									
									$supplier=new Supplier($db);
									$smarty->assign("supplier", $supplier->getSupplierList());
									
									$substrate=new Substrate($db);
									$smarty->assign("substrate", $substrate->getSubstrateList());
									
									$rule=new Rule($db);
									$rulelist=$rule->getRuleList();
									$smarty->assign("rule", $rule->getRuleList());
									
									//density 
									$cDensity = new Density($db);
									$cUnitType = new Unittype($db);
									$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
									
									$smarty->assign('densityDetails', $densityDetailsTrue);
									$smarty->assign('densityDefault', $productData['density_unit_id']);									
									$smarty->assign("componentCount", count($components));
									$smarty->assign("compsAdded", $components);									
								}
								$data = $productData;
//								$smarty->assign('data', $productData);
//								$smarty->assign("ID", $id);
//								$smarty->assign("currentOperation", "edit");
								$jsSources = array(
									'modules/js/PopupWindow.js', 
									'modules/js/checkBoxes.js',
									"modules/js/reg_country_state.js",
									"modules/js/componentPreview.js",
									"modules/js/getInventoryShortInfo.js",
									"modules/js/addProductQuantity.js"	
								);
								$smarty->assign('jsSources', $jsSources);
								$smarty->assign('tpl','tpls/addProductClass.tpl');
//								$smarty->display('tpls:addProductClass.tpl');
								break;
								
							case "agency":
								$agency=new Agency($db);
								if ($_POST['save'] == 'Save')
								{									
									$xnyo->filter_post_var('name_us', 'text');
									$xnyo->filter_post_var('name_eu', 'text');
									$xnyo->filter_post_var('name_cn', 'text');
									$xnyo->filter_post_var('description', 'text');
									$xnyo->filter_post_var('country','text');
									$xnyo->filter_post_var('location', 'text');
									$xnyo->filter_post_var('contact_info', 'text');
									$nameMap = $agency->getNameMap();
									$data=array(
										"agency_id"	=>	$id,
										"name_us"	=>	$_POST["name_us"],
										"name_eu"	=>	$_POST["name_eu"],
										"name_cn"	=>	$_POST["name_cn"],
										"description"	=>	$_POST["description"],
										"country_id"		=>  $_POST["country"],
										"location"	=>	$_POST["location"],
										"contact_info"	=>	$_POST["contact_info"],
										"name"	=>	$_POST[$nameMap[$agency->getRegion()]]
									);
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									if ($validStatus['name'] == 'failed') {
										$validStatus[$nameMap[$agency->getRegion()]] = 'failed';
									}
									if (!($validate->isUniqueName("agency", $data["name"], 'none', $id))) {
										$validStatus['summary'] = 'false';
										$validStatus[$nameMap[$agency->getRegion()]] = 'alredyExist';
									}									
									
									if ($validStatus["summary"] == "true") {
										$agency->setAgencyDetails($data);										
										header ('Location: admin.php?action=viewDetails&categoryID=class&itemID=agency&id='.$id);
										die();									
									} 
									else 
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									}
								}
								else
								{									
									$data=$agency->getAgencyDetails($id);
								}
								$registration = new Registration($db);
								$countries = $registration->getCountryList();
								
								$smarty->assign("country",$countries);
								$smarty->assign('tpl','tpls/addAgencyClass.tpl');
								break;
								
							case "emissionFactor":
								$ms = new ModuleSystem($db);
								$map = $ms->getModulesMap();
								if (class_exists($map['carbon_footprint'])) {
									$mCarbonFootprint = new $map['carbon_footprint'];
									$params = array(
										'db' => $db,
										'id' => $id,
										'xnyo' => $xnyo
									);
									$result = $mCarbonFootprint->prepareAdminEdit($params);
									foreach($result as $key => $value) {
										$smarty->assign($key, $value);
									}
									if ($result['validStatus']['summary'] == 'false') {
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									}
								}
								
								$smarty->display('tpls:index.tpl');														
								break;
						}
						$smarty->assign("currentOperation","edit");
						$smarty->assign("data",$data);
						$smarty->assign("itemID",$itemID);
						$smarty->assign("categoryID",$categoryID);
						$smarty->assign("ID",$id);
						$smarty->assign("validStatus",$validStatus);
						$smarty->display('tpls:index.tpl');
						break;
						
					case "issue":
						$xnyo->filter_post_var("status", "text");
						$xnyo->filter_post_var("priority", "text");
						
						//	Group data
						$issue["issueID"] = $id;
						$issue["status"] = $_POST["status"];
						$issue["priority"] = $_POST["priority"];
						
						//	Update Item
						$issueItem = new Issue($db);
						$issueItem->updateIssueDetails($issue);
						
						//	Show notify
						$notify = new Notify($smarty);
						$notify->successEditedAdmin($itemID, "");
						
						//	Display Issue View page
						header ('Location: admin.php?action=browseCategory&categoryID=issues');
						die();
						
						break;	
						
				case "users":
						
						if ($_POST['save'] == 'Save')
						{
							$xnyo->filter_post_var("accessname","text");
							$xnyo->filter_post_var("password","text");
							$xnyo->filter_post_var("confirm_password","text");
							$xnyo->filter_post_var("username","text");
							$xnyo->filter_post_var("phone","text");
							$xnyo->filter_post_var("mobile","text");
							$xnyo->filter_post_var("email","text");
							$xnyo->filter_post_var("accesslevel_id","text");
							$accessLevel=$_POST['accesslevel_id'];
							
							$data = array (
								'username'			=>	$_POST['username'],
								'accessname'		=>	$_POST['accessname'],
								'password'			=>	$_POST['password'],
								'confirm_password'	=>	$_POST['confirm_password'],
								'phone'				=>	$_POST['phone'],
								'mobile'			=>	$_POST['mobile'],
								'email'				=>	$_POST['email'],
								'accesslevel_id'	=>	$_POST['accesslevel_id'],
								'grace'				=>	14
							);							
							
							$check = array (
								'username'			=>	'ok',
								'accessname'		=>	'ok',
								'password'			=>	'ok',
								'confirm_password'	=>	'ok',
								'phone'				=>	'ok',
								'mobile'			=>	'ok',
								'email'				=>	'ok',
								'accesslevel_id'	=>	'ok',
							);
							
							if ($accessLevel!=3) {
								$xnyo->filter_post_var("company_id","text");
								$data['company_id']=$_POST['company_id'];
								$check['company_id']='ok';
								if ($accessLevel==1 || $accessLevel==2) {
									$xnyo->filter_post_var("facility_id","text");
									$data['facility_id']=$_POST['facility_id'];
									$check['facility_id']='ok';
								}
								if ($accessLevel==2) {
									$xnyo->filter_post_var("department_id","text");
									$data['department_id']=$_POST['department_id'];
									$check['department_id']='ok';
								}
							}							
							
							$user=new User($db, $xnyo);
							
							if (strlen(trim($data['password'])) == 0 && strlen(trim($data['confirm_password'])) == 0) {
								$data['password'] 			= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
								$data['confirm_password']	= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
							}					
							if (!$user->isUniqueAccessName($data['accessname'],$id)) {
								$check['accessname'] = 'alreadyExist';
							}
							if ($user->isValidRegData($data, $check)) 
							{
								$data['user_id'] = $id; 
								if ($data['password'] == "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__") {
									$user->setUserDetails($data);							
								} else {
									$user->setUserDetails($data, true);
								}								
								header ('Location: admin.php?action=browseCategory&categoryID=users&itemID='.$itemID);								
								die();								
							} 
							else 
							{
								$data['password']="";
								$data['confirm_password']="";
								if ($data['accesslevel_id']!=3) {
									$company=new Company($db);
									$companyList=$company->getCompanyList();
									$smarty->assign("company",$companyList);
									if ($data['accesslevel_id']==1 || $data['accesslevel_id']==2) {
										$facility=new Facility($db);
										$facilityList=$facility->getFacilityListByCompany($data['company_id']);
										$smarty->assign("facility",$facilityList);
									}
									if ($data['accesslevel_id']==2) {
										$department=new Department($db);
										$departmentList=$department->getDepartmentListByFacility($data['facility_id']);
										$smarty->assign("department",$departmentList);
									}
								}								
								$smarty->assign('check', $check);								
							}							
						}
						else
						{						
							$data = $user->getUserDetails($id, true);
							switch ($itemID) {
							case "company":
								$company=new Company($db);
								$companyList=$company->getCompanyList();																							
								$smarty->assign("company",$companyList);
													
								break;
								
							case "facility":
								$company=new Company($db);
								$companyList=$company->getCompanyList();
								$facility=new Facility($db);
								$facilityList=$facility->getFacilityListByCompany($data['company_id']);
								$smarty->assign("company",$companyList);
								$smarty->assign("facility",$facilityList);
								
								break;
								
							case "department":							
								$company=new Company($db);
								$companyList=$company->getCompanyList();
								$facility=new Facility($db);
								$facilityList=$facility->getFacilityListByCompany($data['company_id']);
								$department=new Department($db);
								$departmentList=$department->getDepartmentListByFacility($data['facility_id']);
								$smarty->assign("company",$companyList);
								$smarty->assign("facility",$facilityList);
								$smarty->assign("department",$departmentList);								
								break;							
							}
						}																
						
						$smarty->assign("accesslevel", $itemID);
						$smarty->assign("currentOperation","edit");
						$smarty->assign("reg_field",$data);	
						$smarty->assign('update','yes');
						$smarty->assign('ID',$id);
						$smarty->display('tpls:register.tpl');
						break; 											
				}				
				break;
			
						
			case 'addItem':
				$xnyo->filter_get_var('categoryID','text');
				$xnyo->filter_get_var('itemID','text');
				$xnyo->filter_post_var('save','text');
				$categoryID=$_GET['categoryID'];
				$itemID=$_GET['itemID'];				
				switch ($categoryID) {
					case "class":
						switch ($itemID) {
							case "apmethod":								
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('apmethod_desc','text');
									$apmethodData=array(
										"apmethod_desc"	=>	$_POST['apmethod_desc']
									);
									
									$validation=new Validation($db);
									$validStatus=$validation->validateRegDataAdminClasses($apmethodData);
									
									if (!($validation->isUniqueName("apmethod", $apmethodData["apmethod_desc"]))) {
										$validStatus['summary'] = 'false';
										$validStatus['apmethod_desc'] = 'alredyExist';
									}								
									if ($validStatus['summary'] == 'true') {	
										$apmethod=new Apmethod($db);							
										$apmethod->addNewApmethod($apmethodData);								
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=apmethod');
										die();										
									} 
									else 
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}
								$smarty->assign("data",$apmethodData);
								$smarty->assign("validStatus",$validStatus);
								$smarty->assign("currentOperation","addItem");
								$smarty->assign('tpl',"tpls/addApmethodClass.tpl");
//								$smarty->display("tpls:addApmethodClass.tpl");
								
								break;
								
							case "coat":
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('coat_desc','text');
									$coatData=array(
										"coat_desc"	=>	strtoupper($_POST['coat_desc'])
									);
									
									$validation=new Validation($db);
									$validStatus=$validation->validateRegDataAdminClasses($coatData);
									
									if (!($validation->isUniqueName("coat", $coatData["coat_desc"]))) {
										$validStatus['summary'] = 'false';
										$validStatus['coat_desc'] = 'alredyExist';
									}
									
									if ($validStatus['summary'] == 'true') {
										$coat=new Coat($db);
										$coat->addNewCoat($coatData);									
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=coat');
										die();	
									} 
									else 
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}
								$smarty->assign("data",$coatData);
								$smarty->assign("validStatus",$validStatus);
								$smarty->assign("currentOperation","addItem");
								$smarty->assign('tpl',"tpls/addCoatClass.tpl");
//								$smarty->display("tpls:addCoatClass.tpl");
								
								break;
													
								
							case "country":									
								if (isset($_POST['save']))
								{																										
									$country=new Country($db);
									$xnyo->filter_post_var("save", "text");
									$xnyo->filter_post_var("state_name", "text");
									$xnyo->filter_post_var("country_name", "text");
									$xnyo->filter_post_var('stateCount','text');
									$xnyo->filter_post_var('date_type','text');
									
									$validation=new Validation($db);
									$stateInfo=new State($db);
									
									if ($_POST['stateCount']=="") {
										$stateCount=0;
									} else {
										$stateCount=$_POST['stateCount'];
									}
									
									for ($i=0;$i<$stateCount;$i++) {
										$xnyo->filter_post_var('state_id_'.$i,'text');
										if (isset($_POST['state_id_'.$i])) {
											$xnyo->filter_post_var('state_name_'.$i,'text');
											$f=true;
											for ($j=0; $j<count($states); $j++) {
												if ($_POST['state_name_'.$i]==$states[$j]['name']) {
													$f=false;
													break;
												}
											}
											if ($_POST['state_name_'.$i]=="") {
												$f=false;
											}	
											if ($f==true) {	
												$state=array(
													"state_id"	=>	$_POST['state_id_'.$i],
													"name"	=>	$_POST['state_name_'.$i]
												);
												$states[]=$state;
											}
										}
									}
									
									$countryData=array (
										"country_name"	=>	$_POST["country_name"],
										"name" 			=>	$_POST["country_name"],
										"date_type"		=>	$_POST["date_type"],
										"user_id"		=>	18,
										"states"		=>	$states
									);
								
								
									if ($_POST['save']=="Save") 
									{
										$validateStatus=$validation->validateRegDataAdminClasses($countryData);
										if (!$validation->isUniqueName("country", $countryData['country_name'])) {
											$validateStatus['summary'] = 'false';
											$validateStatus['country_name'] = 'alredyExist';
										}
										
										if ($validateStatus['summary'] == "true") {
											$country->addNewCountry($countryData);
											header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=country');
											die();										
										} else {
											$notify=new Notify($smarty);
											$notify->formErrors();
											$title=new Titles($smarty);
											$title->titleAddItemAdmin($itemID);
											$statesAdded['states']=$states;
											$Data['name']=$_POST['country_name'];
											$Data['date_type']=$_POST['date_type'];
											$smarty->assign('data', $Data);
											$smarty->assign('validStatus', $validateStatus);
											$smarty->assign('statesAdded', $statesAdded);
											$smarty->assign('stateCount', count($countryData['states']));
											//$smarty->assign('currentOperation', 'addItem');
											//$smarty->assign('categoryID', 'class');
											//$smarty->assign('itemID', 'country');
											//$smarty->display('tpls:addCountryClass.tpl');																
										}
									} 
									
									if ($_POST['save']=='Add state to country')  
									{
										$stateForCheck['state_name']=$_POST['state_name'];
										$validateStatus=$validation->validateRegDataAdminClasses($stateForCheck);
										for ($i=0;$i<$stateCount;$i++) {
											if (trim($_POST['state_name'])==trim($states[$i]['name']) && trim($_POST['state_name'])!="") {
												$validateStatus['summary'] = 'false';
												$validateStatus['state_name'] = 'alredyExist';
											}
										}
										if ($validateStatus['summary'] == 'true') {
											$maxStateID=$states[0]['state_id'];
											for ($i=1;$i<count($states);$i++) {
												if ($states[$i]['state_id']>$maxStateID)
													$maxStateID=$states[$i]['state_id'];
											}
											
											$state=array(
												"state_id"	=>	$maxStateID+1,
												"name"	=>	$_POST['state_name']
											);
											$states[]=$state;
										}
										
										$statesAdded['states']=$states;
										
										$smarty->assign('statesAdded', $statesAdded);
										$smarty->assign('id', $_POST['id']);										
										if ($validateStatus['summary'] == 'true') {
											$Data["country_name"]=$_POST['country_name'];
											$Data['date_type']=$_POST['date_type'];
											$smarty->assign('data', $Data);
											$smarty->assign('stateCount', count($statesAdded['states']));
										} else {
											$notify=new Notify($smarty);
											$notify->formErrors();
											
											$Data=array(
												"country_name"	=>	$_POST['country_name'],
												"state_name"	=>	$_POST['state_name'],
												"date_type"     =>  $_POST['date_type']
											);
											$smarty->assign('data', $Data);
											$smarty->assign('validStatus', $validateStatus);
											$smarty->assign('stateCount', count($statesAdded['states']));
										}
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}						
								$smarty->assign('currentOperation', 'addItem');
								$smarty->assign('categoryID', 'class');
								$smarty->assign('itemID', 'country');
								$smarty->assign('tpl', 'tpls/addCountryClass.tpl');
//								$smarty->display('tpls:editDetailsCategory.tpl');									
								$doNotShow=true; //???
								
								break;
								
							case "substrate":
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('substrate_desc','text');
									$substrateData=array(
										"description"	=>	$_POST['substrate_desc']
									);
									
									$validation=new Validation($db);
									$validStatus=$validation->validateRegDataAdminClasses($substrateData);
									
									if (!($validation->isUniqueName("substrate", $substrateData["description"]))) {
										$validStatus['summary'] = 'false';
										$validStatus['description'] = 'alredyExist';
									}
									
									if ($validStatus['summary'] == 'true') {
										$substrate=new Substrate($db);
										$substrate->addNewSubstrate($substrateData);
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=substrate');
										die();
									} 
									else 
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}
								$smarty->assign("data",$substrateData);
								$smarty->assign("validStatus",$validStatus);								
								$smarty->assign('currentOperation', 'addItem');
								$smarty->assign('categoryID', 'class');
								$smarty->assign('itemID', 'substrate');
								$smarty->assign('tpl', 'tpls/addSubstrateClass.tpl');
//								$smarty->display('tpls:editDetailsCategory.tpl');
								
								break;
							
							case "supplier":
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('supplier_desc','text');
									$xnyo->filter_post_var('contact','text');
									$xnyo->filter_post_var('phone','text');
									$xnyo->filter_post_var('address','text');
									$xnyo->filter_post_var('country','text');
									
									$supplierData=array(
										"description"	=>	$_POST['supplier_desc'],
										"contact"	=>	$_POST['contact'],
										"phone"	=>	$_POST['phone'],
										"address"	=>	$_POST['address'],
										"country_id"		=>  $_POST['country']
									);
									
									$validation=new Validation($db);
									$validStatus=$validation->validateRegDataAdminClasses($supplierData);
									if (!($validation->isUniqueName("supplier", $supplierData["description"]))) {
										$validStatus['summary'] = 'false';
										$validStatus['description'] = 'alredyExist';
									}
									
									if ($validStatus['summary'] == 'true') {
										$supplier=new Supplier($db);
										$supplier->addNewSupplier($supplierData);
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=supplier');
										die();									
									} 
									else 
									{
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}
								
								$registration = new Registration($db);
								$countries = $registration->getCountryList();
								
								$smarty->assign("country",$countries);
								$smarty->assign("data",$supplierData);
								$smarty->assign("validStatus",$validStatus);
								$smarty->assign('currentOperation', 'addItem');
								$smarty->assign('categoryID', 'class');
								$smarty->assign('itemID', 'supplier');
								$smarty->assign('tpl', 'tpls/addSupplierClass.tpl');
//								$smarty->display('tpls:editDetailsCategory.tpl');								
								break;			

								
							case "rule":
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('rule_nr_us', 'text');
									$xnyo->filter_post_var('rule_nr_eu', 'text');
									$xnyo->filter_post_var('rule_nr_cn', 'text');
									$xnyo->filter_post_var('rule_desc','text');
									$xnyo->filter_post_var('country','text');
									$xnyo->filter_post_var('county','text');
									$xnyo->filter_post_var('city','text');
									$xnyo->filter_post_var('zip','text');
									
									$rule=new Rule($db);
									
									$ruleData=array(										
										"rule_nr"	=>	$_POST[$rule->ruleNrMap[$rule->getRegion()]],
										"rule_nr_us"	=>	$_POST["rule_nr_us"],
										"rule_nr_eu"	=>	$_POST["rule_nr_eu"],
										"rule_nr_cn"	=>	$_POST["rule_nr_cn"],
										"rule_desc"	=>	$_POST['rule_desc'],
										"country"	=>	$_POST['country'],
										"county"	=>	$_POST['county'],
										"city"	=>	$_POST['city'],
										"zip"	=>	$_POST['zip']
									);
									
									$registration=new Registration($db);
									if ($registration->isOwnState($ruleData['country'])) {
										$xnyo->filter_post_var('selectState','text');
										$ruleData['state']=$_POST['selectState'];
									} else {
										$xnyo->filter_post_var('textState','text');
										$ruleData['state']=$_POST['textState'];
									}
									$validation=new Validation($db);
									$validStatus=$validation->validateRegDataAdminClasses($ruleData);
									
									
									$checkUnique = $validation->isUniqueRule($ruleData);
									if ($checkUnique !== true) {
										$validStatus['summary'] = 'false';
										foreach ($checkUnique as $ruleNR=>$value) {
											$validStatus[$ruleNR] = 'alredyExist';	
										}
										
									}										
									
									if ($validStatus['summary'] == 'true') {										
										$rule->addNewRule($ruleData);
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=rule');
										die();
										//$notify=new Notify($smarty);
										//$notify->successAddedAdmin($itemID, $ruleData['rule_nr']);
										//showCategory($categoryID, $itemID, $db, $smarty, $xnyo);
										
									} else {
										if ($validStatus["rule_nr"] == 'failed') {											
											$validStatus[$rule->ruleNrMap[$rule->getRegion()]] = 'failed';
										}
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}
								
								$country=new Country($db);
								$countries=$country->getCountryList();
								$smarty->assign("country",$countries);
								$registration=new Registration($db);
								if ($registration->isOwnState($ruleData["country"])) {
									$smarty->assign("selectMode", true);
									$state=new State($db);
									$states=$state->getStateList($ruleData["country"]);
									$smarty->assign("state",$states);
								}
								$smarty->assign("data",$ruleData);
								$smarty->assign("validStatus",$validStatus);
								$smarty->assign('currentOperation', 'addItem');
								$smarty->assign('categoryID', 'class');
								$smarty->assign('itemID', 'rule');
								$smarty->assign('tpl', 'tpls/addRuleClass.tpl');
//								$smarty->display('tpls:editDetailsCategory.tpl');
								
								break;
								
							case "components":
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('cas', 'text');
									$xnyo->filter_post_var('description', 'text');
									$xnyo->filter_post_var('EINECS', 'text');
									/*$xnyo->filter_get_var('country', 'text');
									$xnyo->filter_get_var('msds', 'text');
									$xnyo->filter_get_var('product_code', 'text');
									$xnyo->filter_get_var('type', 'text');
									$xnyo->filter_get_var('weight', 'text');
									$xnyo->filter_get_var('supplier', 'text');
									$xnyo->filter_get_var('density', 'text');*/
									$data=array(
										"cas"	=>	$_POST["cas"],
										"EINECS"	=>	$_POST["EINECS"],
										"description"	=>	$_POST["description"]
										/*"country"	=>	$_GET["country"],
										"msds_id"	=>	$_GET["msds"],
										"product_code"	=>	$_GET["product_code"],
										"comp_type"	=>	$_GET["type"],
										"comp_weight"	=>	$_GET["weight"],
										"supplier"	=>	$_GET["supplier"],
										"comp_density"	=>	$_GET["density"]*/
																  //"sara"	=>	"yes"
									);
									/*$registration=new Registration($db);
									if ($registration->isOwnState($compData["country"])) {
										$xnyo->filter_get_var("selectState", "text");
										$compData["state"] = $_GET["selectState"];
									} else {
										$xnyo->filter_get_var("textState", "text");
										$compData["state"] = $_GET["textState"];
									}*/
									
									$agency=new Agency($db);
									$agencyCount=$agency->getAgencyCount();
									$components=new Component($db);
									$data['agencies']=$components->getComponentAgencies("");
									
									for ($i=0; $i < $agencyCount; $i++) {
										$xnyo->filter_post_var("agency_".$i,"text");
										if (isset($_POST['agency_'.$i])) {
											$data['agencies'][$i]['control']='yes';
										} else {
											$data['agencies'][$i]['control']='no';
										}
										
									}
									
									$validate=new Validation($db);
									$validStatus=$validate->validateRegDataAdminClasses($data);
									
									if (!($validate->isUniqueName("component", $data['cas']))) {
										$validStatus['summary'] = 'false';
										$validStatus['cas'] = 'alredyExist';
									}
									
									if ($validStatus["summary"] == "true") {
										$components->addNewComponent($data);
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=components');
										die();										
									} else {
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									}
								}
								else
								{											
									$agency=new Agency($db);
									$agencies=$agency->getAgencyList('id');
									$data['agencies']=$agencies;									
								}
								$smarty->assign("currentOperation","addItem");									
								$smarty->assign("data",$data);
									
								/*$country=new Country($db);
								$countries=$country->getCountryList();
								$smarty->assign("country",$countries);
								$registration=new Registration($db);
								if ($registration->isOwnState($compData["country"])) {
									$smarty->assign("selectMode", true);
									$state=new State($db);
									$states=$state->getStateList($compData["country"]);
									$smarty->assign("state",$states);
								}
									
								$msds=new MsdsItem($db);
								$msdsList=$msds->getMsdsList();
								$smarty->assign("msds",$msdsList);
									
								$type=new Type($db);
								$typeList=$type->getTypeList();
								$smarty->assign("type",$typeList);
									
								$supplier=new Supplier($db);
								$supplierList=$supplier->getSupplierList();
								$smarty->assign("supplier",$supplierList);*/
									
								$smarty->assign("itemID",$itemID);
								$smarty->assign("categoryID",$categoryID);
								$smarty->assign("ID",$id);
								$smarty->assign("validStatus",$validStatus);
								$smarty->assign('tpl', 'tpls/addComponentsClass.tpl');
//								$smarty->display('tpls:editDetailsCategory.tpl');
								
								break;
								
							case "product":
								//prepare company list	
								$xnyo->filter_get_var("companyID", "int");
								$smarty->assign('currentCompany',$_GET['companyID']);
								$company = new Company($db);
								$companyList = $company->getCompanyList();
								$companyList[] = array('id' => 0, 'name' => 'no company');
								$smarty->assign('companyList',$companyList);
								
								if (isset($_POST['save']))
								{
									$xnyo->filter_post_var("save", "text");
									$xnyo->filter_post_var("product_nr", "text");
									$xnyo->filter_post_var("name", "text");
									$xnyo->filter_post_var("selectComponent", "text");
									$xnyo->filter_post_var("density", "text");
									$xnyo->filter_post_var("selectDensityType","int");
									$xnyo->filter_post_var("selectInventory", "text");
									$xnyo->filter_post_var("selectCoat", "text");
									$xnyo->filter_post_var("specialty_coating", "text");
									$xnyo->filter_post_var("aerosol", "text");
									$xnyo->filter_post_var("specific_gravity", "text");
									$xnyo->filter_post_var("selectSupplier", "text");									
									$xnyo->filter_post_var("componentCount", "text");
									$xnyo->filter_post_var("voclx", "text");
									$xnyo->filter_post_var("vocwx", "text");
									$xnyo->filter_post_var("boiling_range_from", "text");
									$xnyo->filter_post_var("boiling_range_to", "text");
									$xnyo->filter_post_var("percent_volatile_weight", "text");
									$xnyo->filter_post_var("percent_volatile_volume", "text");
									//$xnyo->filter_get_var("hazardous_class", "text");
									//$xnyo->filter_get_var("irr", "text");
									//$xnyo->filter_get_var("ohh", "text");
									//$xnyo->filter_get_var("sens", "text");
									//$xnyo->filter_get_var("oxy_1", "text");
									$xnyo->filter_post_var("creator_id", "text");
									
									//	replace false/true for no/yes 
									$specialty_coating 	= (!isset($_POST['specialty_coating'])) ? "no" : "yes";
									$aerosol 			= (!isset($_POST['aerosol'])) ? "no" : "yes";								
									
									/*if (!isset($_GET['irr'])) {
										$irr="no";
									} else {
										$irr="yes";
									}
									if (!isset($_GET['ohh'])) {
										$ohh="no";
									} else {
										$ohh="yes";
									}
									if (!isset($_GET['sens'])) {
										$sens="no";
									} else {
										$sens="yes";
									}
									if (!isset($_GET['oxy_1'])) {
										$oxy_1="no";
									} else {
										$oxy_1="yes";
									}*/
									
									$productData = array (
										"product_nr"		=>	$_POST["product_nr"],
										"name"				=>	$_POST["name"],
										"component_id"		=>	$_POST["selectComponent"],
										"density"			=>	$_POST["density"],
										"density_unit_id"	=>  $_POST["selectDensityType"],
										"inventory_id"		=>	$_POST["selectInventory"],
										"coating_id"		=>	$_POST["selectCoat"],
										"specialty_coating"	=>	$specialty_coating,
										"aerosol"			=>	$aerosol,
										"specific_gravity"	=>	$_POST["specific_gravity"],
										"supplier_id"		=>	$_POST["selectSupplier"],
										"vocwx"				=>	$_POST["vocwx"],
										"voclx"				=>	$_POST["voclx"],
										"boiling_range_from"=>	$_POST["boiling_range_from"],
										"boiling_range_to"	=>	$_POST["boiling_range_to"],
										"percent_volatile_weight"=>	$_POST["percent_volatile_weight"],
										"percent_volatile_volume"	=>	$_POST["percent_volatile_volume"],
										//"hazardous_class"			=>	$_GET["hazardous_class"],
										//"class"			=>	$_GET["hazardous_class"],
										//"irr"			=>	$irr,
										//"ohh"			=>	$ohh,
										//"sens"			=>	$sens,
										//"oxy_1"			=>	$oxy_1,
										"creator_id"			=>	18
									);
									
									//	process hazardous (chemical) classes
									$hazardous = new Hazardous($db);
									$chemicalClassesList = $hazardous->getChemicalClassesList();
									for ($i=0;$i<count($chemicalClassesList);$i++) {
										$xnyo->filter_post_var("chemicalClass_".$i, "text");
										if (isset($_POST['chemicalClass_'.$i])) {
											$chemicalClass = $hazardous->getChemicalClassDetails($_POST['chemicalClass_'.$i]);										
											$chemicalClasses[] = $chemicalClass;
										}										
									}								
									$productData['chemicalClasses'] = $chemicalClasses;
									
									//	process components
									$componentCount = $_POST['componentCount'];
									for ($i=0;$i<$componentCount;$i++) {
										$xnyo->filter_post_var('component_id_'.$i,'text');
										if (isset($_POST['component_id_'.$i])) {
											$xnyo->filter_post_var('comp_cas_'.$i,'text');
											$xnyo->filter_post_var('temp_vp_'.$i,'text');
											$xnyo->filter_post_var('substrate_'.$i,'text');
											$xnyo->filter_post_var('rule_id_'.$i,'text');
											$xnyo->filter_post_var('mm_hg_'.$i,'text');
											$xnyo->filter_post_var('weight_'.$i,'text');
											$xnyo->filter_post_var('type_'.$i,'text');
											
											$component = array (
												"component_id"	=>	$_POST['component_id_'.$i],
												"comp_cas"		=>	$_POST['comp_cas_'.$i],
												"temp_vp"		=>	$_POST['temp_vp_'.$i],
												"substrate_id"		=>	$_POST['substrate_'.$i],
												"rule_id"		=>	$_POST['rule_id_'.$i],
												"mm_hg"			=>	$_POST['mm_hg_'.$i],
												"weight"		=>	$_POST['weight_'.$i],
												"type"			=>	$_POST['type_'.$i]
											);
											$components[] = $component;
										}
									}
									$productData['components'] = $components;
									$validation = new Validation($db);
									if ($_POST['save'] == "Save") {
										
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
										$product = new Product($db);
										
										if ($validStatus['summary'] == 'true') {
											$product->addNewProduct($productData, $_GET['companyID']);										
											//$product_id=$product->getProductIdByName($productData['product_nr']);
											
											//$inventory=new Inventory($db);										
											header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=product');
											die();
											//	Set Notify 
											//$notify = new Notify($smarty);
											//$notify->successAdded($_POST["itemID"],$_POST['product_nr']);
											
											//showCategory ($categoryID, $itemID, $db, $smarty, $xnyo);
										} else {
											
											//prepare company list											
									
											$notify = new Notify($smarty);
											$notify->formErrors();
											$title = new Titles($smarty);
											$title->titleAddItem($_POST["itemID"]);									
											
											$smarty->assign('validStatus', $validStatus);
											
											$xnyo->filter_post_var("temp_vp","text");
											$xnyo->filter_post_var("selectSubstrate","text");
											$xnyo->filter_post_var("selectRule","text");
											$xnyo->filter_post_var("mm_hg","text");
											$xnyo->filter_post_var("weight","text");
											$xnyo->filter_post_var("type","text");
											
											$productData['temp_vp']		= $_POST['temp_vp'];
											$productData['substrate_id']= $_POST['selectSubstrate'];
											$productData['rule_id']		= $_POST['selectRule'];
											$productData['mm_hg']		= $_POST['mm_hg'];
											$productData['weight']		= $_POST['weight'];
											$productData['type']		= $_POST['type'];
											
//											$inventory=new Inventory($db);
//											$inventoryList=$inventory->getInventoryList();
//											$smarty->assign("inventory", $inventoryList);
//											
//											$inventoryDetails=$inventory->getInventoryDetails($productData['inventory_id']);
//											$productData["inventory_desc"]=$inventoryDetails["inventory_desc"];
											
											$product=new Product($db);
											$productList=$product->getProductList();
											$component=new Component($db);
											
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
											
											$smarty->assign("component", $componentsList);
											$smarty->assign("componentCount", count($components));
											$smarty->assign("compsAdded", $components);
											
											$smarty->assign('data', $productData);						
										}
									} else {										
										$component = new Component($db);
										$data2 = $component->getComponentDetails($_POST['selectComponent'],true);
										$xnyo->filter_post_var("temp_vp","text");
										$xnyo->filter_post_var("selectSubstrate","text");
										$xnyo->filter_post_var("selectRule","text");
										$xnyo->filter_post_var("mm_hg","text");
										$xnyo->filter_post_var("weight","text");
										$xnyo->filter_post_var("type","text");
										
										$componentNew = array(
											"component_id"	=>	$_POST['selectComponent'],
											"comp_cas"		=>	$data2['cas'],
											"temp_vp"		=>	$_POST['temp_vp'],
											"substrate_id"	=>	$_POST['selectSubstrate'],
											"rule_id"		=>	$_POST['selectRule'],
											"mm_hg"			=>	$_POST['mm_hg'],
											"type"			=>	$_POST['type'],
											"weight"		=>	$_POST['weight']
										);
										$validateStatus = $validation->validateNewComponent($componentNew);
										if ($validateStatus['summary'] == "true") {
											$components[] = $componentNew;
										} else {
											$smarty->assign("validStatus",$validateStatus);
											$productData['temp_vp']		= $_POST['temp_vp'];
											$productData['substrate_id']= $_POST['selectSubstrate'];
											$productData['rule_id']		= $_POST['selectRule'];
											$productData['type']		= $_POST['type'];
											$productData['mm_hg']		= $_POST['mm_hg'];
											$productData['weight']		= $_POST['weight'];
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
										$smarty->assign("component", $componentsList);
										
										if ($validateStatus['summary']="true") {
											$componentDetails = $component->getComponentDetails($componentsList[0]['component_id'],true);
											$productData['cas'] = $componentDetails['cas'];
											$productData['comp_desc'] = $componentDetails['description'];
										}
										
	//									$inventory=new Inventory($db);
	//									$inventoryList=$inventory->getInventoryList();
	//									$smarty->assign("inventory", $inventoryList);
	//									$inventoryDetails=$inventory->getInventoryDetails($productData['inventory_id']);
	//									$productData["inventory_desc"]=$inventoryDetails["inventory_desc"];
										
										$smarty->assign('data', $productData);
									}
								}	
								else	
								{
									//$inventory=new Inventory($db);
									//$inventoryList=$inventory->getInventoryList();
									//$smarty->assign("inventory", $inventoryList);														
									
									$component=new Component($db);
									$componentList=$component->getComponentList();
									$smarty->assign("component", $componentList);
									$componentDetails=$component->getComponentDetails($componentList[0]['component_id']);
									
									$categoryDetails['cas']=$componentDetails['cas'];
									$categoryDetails['comp_desc']=$componentDetails['description'];
									
									$smarty->assign('data', $categoryDetails);	
									
								}
								
								$rule=new Rule($db);
								$smarty->assign("rule", $rule->getRuleList());
									
								$coat=new Coat($db);
								$smarty->assign("coat", $coat->getCoatList());
									
								$substrate=new Substrate($db);
								$smarty->assign("substrate", $substrate->getSubstrateList());
									
								$supplier=new Supplier($db);
								$smarty->assign("supplier", $supplier->getSupplierList());
									
								//	get hazardous (chemical) class list
								$hazardous = new Hazardous($db);
								$chemicalClassesList = $hazardous->getChemicalClassesList();
								$smarty->assign("chemicalClassesList", $chemicalClassesList);
									
								//density 
								$cDensity = new Density($db);
								$cUnitType = new Unittype($db);
								$densityDetailsTrue = $cDensity->getAllDensity($cUnitType);
													
								$smarty->assign('densityDetails', $densityDetailsTrue);
								$smarty->assign('densityDefault', $productData['density_unit_id']);
									
								$smarty->assign("componentCount", count($components));
								$smarty->assign("compsAdded", $components);
								$smarty->assign('currentOperation', 'addItem');
								$smarty->assign('categoryID', 'class');
								$smarty->assign('itemID', 'product');
								$jsSources = array(
									'modules/js/PopupWindow.js', 
									'modules/js/checkBoxes.js',
									"modules/js/reg_country_state.js",
									"modules/js/componentPreview.js",
									"modules/js/getInventoryShortInfo.js",
									"modules/js/addProductQuantity.js"	
								);
								$smarty->assign('jsSources', $jsSources);
								$smarty->assign('tpl', 'tpls/addProductClass.tpl');
//								$smarty->display('tpls:editDetailsCategory.tpl');
								
								break;
								
							case "agency":
								if ($_POST['save'] == 'Save')
								{
									$xnyo->filter_post_var('name_us','text');
									$xnyo->filter_post_var('name_eu','text');
									$xnyo->filter_post_var('name_cn','text');
									$xnyo->filter_post_var('description','text');
									$xnyo->filter_post_var('country','text');
									$xnyo->filter_post_var('location','text');
									$xnyo->filter_post_var('contact_info','text');
									
									$agency=new Agency($db);
									$nameMap = $agency->getNameMap();
									
									$agencyData=array(
										"agency_id"			=>	false,
										"agency_id"			=>	false,
										"name_us"			=>	$_POST['name_us'],
										"name_eu"			=>	$_POST['name_eu'],
										"name_cn"			=>	$_POST['name_cn'],
										"description"	 	=>	$_POST['description'],
										"country_id"		=>  $_POST['country'],
										"location"			=>	$_POST['location'],
										"contact_info"		=>	$_POST['contact_info'],
										"name"				=> $_POST[$nameMap[$agency->getRegion()]]
									);
									
									$validation=new Validation($db);
									$validStatus=$validation->validateRegDataAdminClasses($agencyData);
									if ($validStatus['name'] == 'failed') {
										$validStatus[$nameMap[$agency->getRegion()]] = 'failed';
									}
									if (!($validation->isUniqueName("agency", $agencyData["name"]))) {
										$validStatus['summary'] = 'false';
										$validStatus[$nameMap[$agency->getRegion()]] = 'alredyExist';
									}
									
									if ($validStatus['summary'] == 'true') {
										
										$agency->addNewAgency($agencyData);
										
										header ('Location: admin.php?action=browseCategory&categoryID=class&itemID=agency');
										die();										
									}
									else {
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleAddItemAdmin($itemID);
									}
								}
								
								$registration = new Registration($db);
								$countries = $registration->getCountryList();
								
								$smarty->assign("country",$countries);
								$smarty->assign("data",$agencyData);
								$smarty->assign("validStatus",$validStatus);
								$smarty->assign("currentOperation","addItem");
								$smarty->assign('tpl', 'tpls/addAgencyClass.tpl');
//								$smarty->display("tpls:addAgencyClass.tpl");
									
								break;
								
							case 'emissionFactor':
								$ms = new ModuleSystem($db);
								$map = $ms->getModulesMap();
								if (class_exists($map['carbon_footprint'])) {
									$mCarbonFootprint = new $map['carbon_footprint'];
									$params = array(
										'db' => $db,
										'id' => null, //because it's add item and we dont know id
										'xnyo' => $xnyo
									);
									$result = $mCarbonFootprint->prepareAdminEdit($params);
									foreach($result as $key => $value) {
										$smarty->assign($key, $value);
									}
									if ($result['validStatus']['summary'] == 'false') {
										$notify=new Notify($smarty);
										$notify->formErrors();
										$title=new Titles($smarty);
										$title->titleEditItemAdmin($itemID);
									}
								}

								$smarty->assign('currentOperation', 'addItem');
							
								break;
						}
						$smarty->display('tpls:index.tpl');
						break;
							
					case "users":						
						if ($_POST['save'] == 'Register')
						{
							$xnyo->filter_post_var("accessname","text");
							$xnyo->filter_post_var("password","text");
							$xnyo->filter_post_var("confirm_password","text");
							$xnyo->filter_post_var("username","text");
							$xnyo->filter_post_var("phone","text");
							$xnyo->filter_post_var("mobile","text");
							$xnyo->filter_post_var("email","text");
							$xnyo->filter_post_var("accesslevel_id","text");
							$accessLevel=$_POST['accesslevel_id'];
							
							$data = array (
								'username'			=>	$_POST['username'],
								'accessname'		=>	$_POST['accessname'],
								'password'			=>	$_POST['password'],
								'confirm_password'	=>	$_POST['confirm_password'],
								'phone'				=>	$_POST['phone'],
								'mobile'			=>	$_POST['mobile'],
								'email'				=>	$_POST['email'],
								'accesslevel_id'	=>	$_POST['accesslevel_id'],
								'grace'				=>	14
							);							
							
							$check = array (
								'username'			=>	'ok',
								'accessname'		=>	'ok',
								'password'			=>	'ok',
								'confirm_password'	=>	'ok',
								'phone'				=>	'ok',
								'mobile'			=>	'ok',
								'email'				=>	'ok',
								'accesslevel_id'	=>	'ok',
							);
							
							if ($accessLevel!=3) {
								
								$xnyo->filter_post_var("company_id","text");
								$data['company_id']=$_POST['company_id'];
								$check['company_id']='ok';
								if ($accessLevel==1 || $accessLevel==2) {
									$xnyo->filter_post_var("facility_id","text");
									$data['facility_id']=$_POST['facility_id'];
									$check['facility_id']='ok';
								}
								if ($accessLevel==2) {
									$xnyo->filter_post_var("department_id","text");
									$data['department_id']=$_POST['department_id'];
									$check['department_id']='ok';
								}
							}							
							
							$user=new User($db, $xnyo);
							
							if (strlen(trim($data['password'])) == 0 && strlen(trim($data['confirm_password'])) == 0) {
								$data['password'] 			= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
								$data['confirm_password']	= "__updatingUserFlag=WeCanLiveThisFieldEmptyButValidationWillBeFailed__";
							}						
							if (!$user->isUniqueAccessName($data['accessname'])) {
								$check['accessname'] = 'alreadyExist';
							}
							if ($user->isValidRegData($data, $check)) 
							{							
								$user->addUser($data);								
								header ('Location: admin.php?action=browseCategory&categoryID=users&itemID='.$itemID);										
								die();								
							} 
							else 
							{
								$data['password']="";
								$data['confirm_password']="";
								if ($data['accesslevel_id']!=3) {
									$company=new Company($db);
									$companyList=$company->getCompanyList();
									$smarty->assign("company",$companyList);
									if ($data['accesslevel_id']==1 || $data['accesslevel_id']==2) {
										$facility=new Facility($db);
										$facilityList=$facility->getFacilityListByCompany($data['company_id']);
										$smarty->assign("facility",$facilityList);
									}
									if ($data['accesslevel_id']==2) {
										$department=new Department($db);
										$departmentList=$department->getDepartmentListByFacility($data['facility_id']);
										$smarty->assign("department",$departmentList);
									}
								}													
								$smarty->assign('check', $check);
								$smarty->assign("reg_field",$data);									
							}							
						}
						else
						{							
							switch ($itemID) {
							case "company":
								$company=new Company($db);
								$companyList=$company->getCompanyList();																							
								$smarty->assign("company",$companyList);
													
								break;
								
							case "facility":
								$company=new Company($db);
								$companyList=$company->getCompanyList();
								$facility=new Facility($db);
								$facilityList=$facility->getFacilityListByCompany($userDetails['company_id']);
								$smarty->assign("company",$companyList);
								$smarty->assign("facility",$facilityList);
								
								break;
								
							case "department":							
								$company=new Company($db);
								$companyList=$company->getCompanyList();
								$facility=new Facility($db);
								$facilityList=$facility->getFacilityListByCompany($userDetails['company_id']);
								$department=new Department($db);
								$departmentList=$department->getDepartmentListByFacility($userDetails['facility_id']);
								$smarty->assign("company",$companyList);
								$smarty->assign("facility",$facilityList);
								$smarty->assign("department",$departmentList);								
								break;							
							}
						}																
						
						$smarty->assign("accesslevel", $itemID);
						$smarty->assign("currentOperation","addItem");						
						$smarty->assign('ID',$id);
						$smarty->display('tpls:register.tpl');
						break; 	
				}
				break;
			
			
			case "deleteItem":
				$xnyo->filter_get_var('categoryID','text');
				$xnyo->filter_get_var('itemID','text');
				$xnyo->filter_get_var('itemsCount','text');
				$categoryID=$_GET['categoryID'];
				$itemID=$_GET['itemID'];
				
				//$smarty->assign("gobackAction",$_SESSION["gobackAction"]);
				
				
				$itemsCount=$_GET['itemsCount'];
				for ($i=0; $i<$itemsCount; $i++) {
					$xnyo->filter_get_var('item_'.$i, "text");
					if (isset($_GET['item_'.$i])) {
						$itemIDs[]=$_GET['item_'.$i];
					}
				}
				$itemsCount=count($itemIDs);
				switch ($categoryID) {
					case "class":
						switch ($itemID) {
							case "apmethod":
								$apmethod=new Apmethod($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$apmethodDetails=$apmethod->getApmethodDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$apmethodDetails["apmethod_id"];
									$itemForDelete[$i]["name"]		=	$apmethodDetails["apmethod_desc"];
								}
								break;
								
							case "coat":
								$coat=new Coat($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$coatDetails=$coat->getCoatDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$coatDetails["coat_id"];
									$itemForDelete[$i]["name"]		=	$coatDetails["coat_desc"];
								}
								break;
								
							case "density":
								$density=new Density($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$densityDetails=$density->getDensityDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$densityDetails["density_id"];
									$itemForDelete[$i]["name"]		=	$densityDetails["density_type"];
								}
								break;
								
							case "country":
								$country=new Country($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$countryDetails=$country->getCountryDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$countryDetails["country_id"];
									$itemForDelete[$i]["name"]		=	$countryDetails["country_name"];
								}
								break;
								
							case "substrate":
								$substrate=new Substrate($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$substrateDetails=$substrate->getSubstrateDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$substrateDetails["substrate_id"];
									$itemForDelete[$i]["name"]		=	$substrateDetails["substrate_desc"];
								}
								break;
								
							case "supplier":
								$supplier=new Supplier($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$supplierDetails=$supplier->getSupplierDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$supplierDetails["supplier_id"];
									$itemForDelete[$i]["name"]		=	$supplierDetails["supplier_desc"];
								}
								break;
								
							case "type":
								$type=new Type($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$typeDetails=$type->getTypeDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$typeDetails["type_id"];
									$itemForDelete[$i]["name"]		=	$typeDetails["type_desc"];
								}
								break;
								
							case "unittype":
								$unittype=new Unittype($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$unittypeDetails=$unittype->getUnittypeDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$unittypeDetails["unittype_id"];
									$itemForDelete[$i]["name"]		=	$unittypeDetails["description"];
								}
								break;
								
							case "msds":
								$msds=new MsdsItem($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$msdsDetails=$msds->getMsdsDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$msdsDetails["msds_id"];
									$itemForDelete[$i]["name"]		=	$msdsDetails["cas"];
								}
								break;
								
							case "lol":
								$lol=new Lol($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$lolDetails=$lol->getLolDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$lolDetails["lol_id"];
									$itemForDelete[$i]["name"]		=	$lolDetails["lol_name"];
								}
								break;
								
							case "formulas":
								$formulas=new Formulas($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$formulasDetails=$formulas->getFormulasDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$formulasDetails["formula_id"];
									$itemForDelete[$i]["name"]		=	$formulasDetails["formula_desc"];
								}
								break;
								
							case "rule":
								$rule=new Rule($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$ruleDetails=$rule->getRuleDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$ruleDetails["rule_id"];
									$itemForDelete[$i]["name"]		=	$ruleDetails["rule_nr"];
								}
								break;
								
							case "components":
								$components=new Component($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$componentsDetails=$components->getComponentDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$componentsDetails["component_id"];
									$itemForDelete[$i]["name"]		=	$componentsDetails["cas"];
									$itemForDelete[$i]["links"] = $components->isInUseList($itemForDelete[$i]["id"]);
								}
								break;
								
							case "product":
								$product=new Product($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$productDetails=$product->getProductDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$productDetails["product_id"];
									$itemForDelete[$i]["name"]		=	$productDetails["product_nr"];
									$itemForDelete[$i]["links"] = $product->isInUseList($itemForDelete[$i]["id"]);								
								}
								break;
								
							case "agency":
								$agency=new Agency($db);
								for ($i=0; $i<$itemsCount; $i++) {
									$agencyDetails=$agency->getAgencyDetails($itemIDs[$i]);
									$itemForDelete[$i]["id"]		=	$agencyDetails["agency_id"];
									$itemForDelete[$i]["name"]		=	$agencyDetails["name"];
								}
								break;
							case "clearAll":
								$itemForDelete[0]["id"]		=	"!!!!!";
								$itemForDelete[0]["name"]		= "all rows in DB";
								break;
							case "fillDB":
								$itemForDelete[0]["id"]		=	"!!!!!";
								$itemForDelete[0]["name"]		= "fill all rows in DB";
								break;
						}
						break;
						
					case "users":
						for ($i=0; $i<$itemsCount; $i++) {
							$userDetails=$user->getUserDetails($itemIDs[$i]);
							$itemForDelete[$i]["id"]		=	$userDetails["user_id"];
							$itemForDelete[$i]["name"]		=	$userDetails["username"];
						}
						break;
				}
				
				$notify=new Notify($smarty);
				$title=new Titles($smarty);
				if ($itemsCount==0)  {
					$notify->notSelected($itemID);
					$title->titleDeleteItemsAdmin($itemID);
				} else {
					if (count($itemIDs)==1) {
						$notify->warnDeleteAdmin($itemID,$itemForDelete[0]["name"]);
						$title->titleDeleteItemsAdmin($itemID,$itemForDelete[0]["name"]);
					} else {
						$notify->warnDeleteAdmin($itemID);
						$title->titleDeleteItemsAdmin($itemID);
					}
				}
				$smarty->assign("categoryID", $categoryID);
				$smarty->assign("itemID", $itemID);
				$smarty->assign("itemForDelete", $itemForDelete);
				$smarty->assign("itemsCount", $itemsCount);
				
				$smarty->display("tpls:confirmDeleteCategory.tpl");
				break;
			
			
			case "confirmDelete":
				$xnyo->filter_get_var('categoryID','text');
				$xnyo->filter_get_var('itemID','text');
				$categoryID=$_GET['categoryID'];
				$itemID=$_GET['itemID'];
				
				$xnyo->filter_get_var('itemsCount', 'text');
				$itemsCount=$_GET['itemsCount'];
				for ($i=0; $i<$itemsCount; $i++) {
					$xnyo->filter_get_var('item_'.$i, "text");
					if (isset($_GET['item_'.$i])) {
						$itemIDs[]=$_GET['item_'.$i];
						
					}
				}
				
				switch ($categoryID) {
					case "class":
						switch ($itemID) {
							case "apmethod":
								$apmethod=new Apmethod($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$apmethodDetails=$apmethod->getApmethodDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$apmethodDetails["apmethod_desc"];
									$apmethod->deleteApmethod($itemIDs[$i]);
								}
								break;
								
							case "coat":
								$coat=new Coat($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$coatDetails=$coat->getCoatDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$coatDetails["coat_desc"];
									$coat->deleteCoat($itemIDs[$i]);
								}
								break;
								
							case "density":
								$density=new Density($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$densityDetails=$density->getDensityDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$densityDetails["density_type"];
									$density->deleteDensity($itemIDs[$i]);
								}
								break;
								
							case "country":
								$country=new Country($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$countryDetails=$country->getCountryDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$countryDetails["country_name"];
									$country->deleteCountry($itemIDs[$i]);
								}
								break;
								
							case "substrate":
								$substrate=new Substrate($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$substrateDetails=$substrate->getSubstrateDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$substrateDetails["substrate_desc"];
									$substrate->deleteSubstrate($itemIDs[$i]);
								}
								break;
								
							case "supplier":
								$supplier=new Supplier($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$supplierDetails=$supplier->getSupplierDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$supplierDetails["supplier_desc"];
									$supplier->deleteSupplier($itemIDs[$i]);
								}
								break;
								
							case "type":
								$type=new Type($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$typeDetails=$type->getTypeDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$typeDetails["type_desc"];
									$type->deleteType($itemIDs[$i]);
								}
								break;
								
							case "unittype":
								$unittype=new Unittype($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$unittypeDetails=$unittype->getUnittypeDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$unittypeDetails["description"];
									$unittype->deleteUnittype($itemIDs[$i]);
								}
								break;
								
							case "msds":
								$msds=new MsdsItem($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$msdsDetails=$msds->getMsdsDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$msdsDetails["cas"];
									$msds->deleteMsds($itemIDs[$i]);
								}
								break;
								
							case "lol":
								$lol=new Lol($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$lolDetails=$lol->getLolDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$lolDetails["lol_name"];
									$lol->deleteLol($itemIDs[$i]);
								}
								break;
								
							case "formulas":
								$formulas=new Formulas($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$formulasDetails=$formulas->getFormulasDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$formulasDetails["formula"];
									$formulas->deleteFormulas($itemIDs[$i]);
								}
								break;
								
							case "rule":
								$rule=new Rule($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$ruleDetails=$rule->getRuleDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$ruleDetails["rule_nr"];
									$rule->deleteRule($itemIDs[$i]);
								}
								break;
								
							case "components":
								$components=new Component($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$componentsDetails=$components->getComponentDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$componentsDetails["comp_name"];
									$components->deleteComponent($itemIDs[$i]);
								}
								break;
								
							case "product":
								$product=new Product($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$productDetails=$product->getProductDetails($itemIDs[$i]);
									$itemForDeleteName[]=$productDetails["product_nr"];
									$product->deleteProduct2($itemIDs[$i]);
								}
								break;
								
							case "agency":
								$agency=new Agency($db);
								
								for ($i=0; $i<count($itemIDs); $i++) {
									$agencyDetails=$agency->getAgencyDetails($itemIDs[$i]);
									$itemForDeleteName[]		=	$agencyDetails["name"];
									$agency->deleteAgency($itemIDs[$i]);
								}
								break;
							case "clearAll":
								$usage = new Usage($db);
								$usage->clearUsage();
						
								$equipment = new Equipment($db);
								$equipment->clearEquipment();
				
								$inventory = new Inventory($db);
								$inventory->clearInventory();
								
								$product = new Product($db);
								$product->clearProduct();
								
								$component = new Component($db);
								$component->clearComponent();
								
								$department = new Department($db);
								$department->clearDepartment();
								
								$facility = new Facility($db);
								$facility->clearFacility();
								
								//clearCompany also cleans all gacl% tables
								$company = new Company($db);
								$company->clearCompany();
								
								$user = new User($db);
								$user->clearUser();
																
								$itemID="apmethod";
								break;
								
							case "fillDB":
								$usage = new Usage($db);
								$usage->fillUsage();
						
								$equipment = new Equipment($db);
								$equipment->fillEquipment();
				
								$inventory = new Inventory($db);
								$inventory->fillInventory();
								
								$product = new Product($db);
								$product->fillProduct();
								
								$component = new Component($db);
								$component->fillComponent();
								
								$department = new Department($db);
								$department->fillDepartment();
								
								$facility = new Facility($db);
								$facility->fillFacility();
								
								//fillCompany also fills all gacl% tables
								$company = new Company($db);
								$company->fillCompany();
								
								$user = new User($db);
								$user->fillUser();
								
								$itemID="apmethod";
								break;
				
						}
						break;
						
					case "users":
						for ($i=0; $i<count($itemIDs); $i++) {
							$userDetails=$user->getUserDetails($itemIDs[$i]);
							$itemForDeleteName[]		=	$userDetails["username"];
							$user->deleteUser($itemIDs[$i]);
						}
						break;
				}
				
				$notify=new Notify($smarty);
				$title=new Titles($smarty);
				if (count($itemIDs)!=0) {
					$notify->successDeletedAdmin($itemID,$itemForDeleteName);
				}
				header ('Location: admin.php?action=browseCategory&categoryID='.$categoryID.'&itemID='.$itemID);
				die();
				//showCategory($categoryID, $itemID, $db, $smarty, $xnyo);
				break;
				
			case "viewIssue":
				$xnyo->filter_get_var("id", text);
				$issueID = $_GET["id"];
				
				$issue = new Issue($db);
				$issueDetails = $issue->getIssueDetails($issueID);
				
				$smarty->assign("issue", $issueDetails);
				$smarty->display("tpls:issueDetails.tpl");
				break;
			
			//vps include	
			case "vps":			
				require ('vps_admin.php');				
				break;		
			case "my_check":
				$test = new VPSUser($db);
				$limit = array(
					'limit_id' => 2,
					'current_value' => 213,
					'max_value' => 1000
				);
				var_dump($test->setCustomerLimitByID (125, $limit));
				break;		
				
			case "stats":
				$xnyo->filter_post_var("startDate", "text");
				$xnyo->filter_post_var("finishDate", "text");				
				
				if (isset($_POST['startDate']) && isset($_POST['finishDate'])) {
					//	js give us timestamp with milliseconds
					$timestampStart = floor($_POST['startDate']/1000);
					$timestampFinish = floor($_POST['finishDate']/1000);
				
					$stats = new Statistics($db);
					$output = $stats->show($timestampStart, $timestampFinish);
					
					echo json_encode($output);
				} else {
					$smarty->display("tpls:stats.tpl");	
				}
				
				break;
			
			case "logout":
				
				$user->logout();
			break;
			
			case 'pylesos':				
				$invoice = new Invoice($db);
				$invoice->getInvoiceStatusListNew(134) ;
				
				break;
			
			case 'baikonur':
				syncACL($db);
				  
				break;
															
			default:
				//	Show Login page
				$smarty->assign('temp_url', 'admin.php?action=testTPL');
				$smarty->assign('register_url', 'admin.php?action=registration');
			
				$smarty->display('tpls:admin.tpl');
		}
	}
?>
