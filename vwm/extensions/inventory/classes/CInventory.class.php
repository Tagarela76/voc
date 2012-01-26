<?php
class CInventory extends Controller
{
	function CInventory($smarty,$xnyo,$db,$user,$action)
		{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='inventory';
		}

	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
		else
			throw new Exception('404');
	}

	private function actionConfirmDelete()
	{
		foreach($this->itemID as $inventoryID)
		{
			$inventory = new Inventory($this->db, $inventoryID);
			$lastInventoryType = $inventory->getType();
			$lastInventoryFaciltiyID = $inventory->getFacilityID();

			//	setter injection
			$inventory->setTrashRecord(new Trash($this->db));
			$inventory->delete();
		}

		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=facility&id=$lastInventoryFaciltiyID&bookmark=inventory&tab=$lastInventoryType&notify=8");
	}

	private function actionDeleteItem()
		{
		if (!is_null($this->getFromRequest('departmentID')))
		{
			//	Access control
			if (!$this->user->checkAccess('department', $this->getFromRequest('departmentID'))) {
				throw new Exception('deny');
			}

			$company = new Company($this->db);
			$companyID = $company->getCompanyIDbyDepartmentID($this->getFromRequest('departmentID'));

			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=".$this->getFromRequest('departmentID')."&bookmark=inventory&tab=".$this->getFromRequest('tab'));
			$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'), array('bookmark'=>'inventory','tab'=>$this->getFromRequest('tab')));
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			$this->setPermissionsNew('viewData');

			//	calling from facility
		}
		elseif (!is_null($this->getFromRequest('facilityID')))
		{
			if (!$this->user->checkAccess('facility', $this->getFromRequest('facilityID'))) {
				throw new Exception('deny');
			}
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
			$companyID =  $facilityDetails['company_id'];
			$this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=inventory&tab=".$this->getFromRequest('tab'));
			$this->setListCategoriesLeftNew('facility',$this->getFromRequest('facilityID'), array('bookmark'=>'inventory','tab'=>$this->getFromRequest('tab')));
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			$this->setPermissionsNew('viewFacility');
		}
		if (!$this->user->checkAccess('inventory', $companyID)) {
			throw  new Exception('deny');
		}
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		$mInventory = new $moduleMap['inventory'];
		$equipment = new Equipment($this->db);
		$params = array(
			'equipment'	=> $equipment,
			'db'		=> $this->db,
			'request'	=> $this->getFromRequest()
		);
		extract($mInventory->prepareDelete($params));
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
		}

	private function actionViewDetails()
		{
		$productID = $this->getFromRequest('id');
		$category = 'facility';
		$id = $this->getFromRequest('facilityID');
		$ProductInventory = new ProductInventory($this->db);
		$inventoryManager = new InventoryManager($this->db);
		$product = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $id, $productID);
		
		
		$this->smarty->assign("product",$product);
		$this->smarty->assign("editUrl","?action=edit&category=inventory&id=".$product->product_id."&".$category."ID=".$id);
/*		if (!is_null($this->getFromRequest('facilityID')))
		{
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
			$companyID = $facilityDetails['company_id'];
			$backCategory = 'facility';
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			//$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'inventory', 'tab'=>''));
			$this->setPermissionsNew('viewData');
			$this->smarty->assign('backUrl','?action=browseCategory&category=facility&id='.$this->getFromRequest('facilityID').'&bookmark=inventory&tab=material');
		} elseif (!is_null($this->getFromRequest('departmentID')))
		{
			$department = new Department($this->db);
			$departmentDetails = $department->getDepartmentDetails($this->getFromRequest('departmentID'));
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);
			$companyID = $facilityDetails['company_id'];
			$backCategory = 'department';
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			//$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'),array('bookmark'=>'inventory', 'tab'=>''));
			$this->setPermissionsNew('viewData');
			$this->smarty->assign('backUrl','?action=browseCategory&category=department&id='.$this->getFromRequest('departmentID').'&bookmark=inventory&tab=material');
		}
		if (!$this->user->checkAccess('inventory', $companyID)) {
			throw new Exception('deny');
		}
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module)
		{
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);

		if (!$this->user->checkAccess('inventory', $companyID)) {
			throw new Exception('deny');
		}

		$mInventory = new $moduleMap['inventory'];
		$params = array(
			'db' => $this->db,
			'user' => $this->user,
			'request' => $this->getFromRequest()
		);
		$result = $mInventory->prepareView($params);
		foreach($result as $key => $value)
		{
			$this->smarty->assign($key,$value);
		}

		$this->setListCategoriesLeftNew($backCategory, $this->getFromRequest($backCategory.'ID'),array('bookmark'=>'inventory','tab'=>$result['inventory']->getType()));
		$this->smarty->assign('backUrl','?action=browseCategory&category='.$backCategory.'&id='.$this->getFromRequest($backCategory.'ID').'&bookmark=inventory&tab='.$result['inventory']->getType());
		
 */		$this->smarty->assign('tpl','inventory/design/inventoryProductsDetail.tpl');
		$this->smarty->display("tpls:index.tpl");
 
		}

	private function actionAddItem() {
		// inventory from?
		if (is_null($this->getFromRequest('facilityID')) && !is_null($this->getFromRequest('departmentID'))) {
			$parentCategory = "department";
			$request = $this->getFromRequest();
			$request['id'] = $this->getFromRequest('departmentID');
			$department = new Department($this->db);
			if(!$department->initializeByID($this->getFromRequest('departmentID'))) {
				throw new Exception('404');
			}
		} elseif (!is_null($this->getFromRequest('facilityID')) && is_null($this->getFromRequest('departmentID'))) {
			$parentCategory = "facility";
			$request = $this->getFromRequest();
			$request['id'] = $this->getFromRequest('facilityID');
			$facility = new Facility($this->db);
			if(!$facility->initializeByID($this->getFromRequest('facilityID'))) {
				throw new Exception('404');
			}
		} else {
			throw new Exception('I do not know whom I should link inventory. To department or facility?');
		}

		//	Access control
		if (!$this->user->checkAccess($parentCategory, $this->getFromRequest($parentCategory.'ID'))) {
			throw new Exception('deny');
		}

		$this->smarty->assign('parentCategory', $parentCategory);
		$request['parent_category'] = $parentCategory;

		//protected from xss
		if (isset($_POST["inventory_name"]))
			$_POST["inventory_name"]=Reform::HtmlEncode($_POST["inventory_name"]);
		if (isset($_POST["inventory_desc"]))
			$_POST["inventory_desc"]=Reform::HtmlEncode($_POST["inventory_desc"]);

		$form = $_POST;

		if (count($form)>0) {
			$form['OS_use'] = str_replace(',','.',$form['OS_use']);
			$form['CS_use'] = str_replace(',','.',$form['CS_use']);
			$form['totalQty'] = str_replace(',','.',$form['totalQty']);
			$form['unitAmount'] = str_replace(',','.',$form['unitAmount']);
			$form['unitQuantity'] = str_replace(',','.',$form['unitQuantity']);
		}

		$paramsForListLeft = array('bookmark'=>'inventory','tab'=>$this->getFromRequest('tab'));

		//	IF ERRORS OR NO POST REQUEST
		if ($parentCategory == 'facility') {
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
			$companyID = $facilityDetails['company_id'];
			$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $paramsForListLeft);
			$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
			$this->setPermissionsNew('viewFacility');

			//	set js scripts
			$jsSources = array(
				'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
				'modules/js/inventory.js'
			);
			$this->smarty->assign('jsSources', $jsSources);
			$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
			$this->smarty->assign('cssSources', $cssSources);

		} elseif ($parentCategory == 'department') {
			$department = new Department($this->db);
			$departmentDetails = $department->getDepartmentDetails($this->getFromRequest('departmentID'));
			$facility = new Facility($this->db);
			$facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);
			$companyID = $facilityDetails['company_id'];

			$this->setListCategoriesLeftNew('department', $this->getFromRequest('departmentID'));
			$this->setNavigationUpNew('department', $this->getFromRequest('departmentID'));
			$this->setPermissionsNew('viewData');

			//	set js scripts
			$jsSources = array(
				'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
				'modules/js/inventory.js'
			);
			$this->smarty->assign('jsSources', $jsSources);
			$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
			$this->smarty->assign('cssSources', $cssSources);

		} else {
			throw new Exception('I do not know whom I should link inventory. To department or facility?');
		}

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $companyID);
		}
		$this->smarty->assign('show',$showModules);

		if (!$showModules['inventory']) {
			throw new Exception('deny');
		}

		//	ok, has access to module
		$mInventory = new $moduleMap['inventory'];

		$params = array(
			'db' => $this->db,
			'request' => $request,//$this->getFromRequest(),
			'form' => $form,
			'parentCategory' => $parentCategory,
			'smarty' => $this->smarty
		);
		$result = $mInventory->prepareAdd($params);

		foreach ($result as $key=>$value) {
			$this->smarty->assign($key,$value);
		}
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit() {
		
		
							if (!is_null($this->getFromRequest('facilityID'))) {
								//	Access control
								if (!$this->user->checkAccess('facility', $this->getFromRequest('facilityID'))) {
									throw new Exception('deny');
								}

								$backCategory = 'facility';
								$facilityID = $this->getFromRequest('facilityID');

							}
		$productID = $this->getFromRequest('id');
		$category = 'facility';
		$id = $this->getFromRequest('facilityID');
		$ProductInventory = new ProductInventory($this->db);
		$inventoryManager = new InventoryManager($this->db);
		$product = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $id, $productID);

		var_dump($product);
		$this->smarty->assign("product",$product);
		
		
							$form = $_POST;

							if (count($form) > 0) {
								//protected from xss
								$form["inventory_name"]=Reform::HtmlEncode($form["inventory_name"]);
								$form["inventory_desc"]=Reform::HtmlEncode($form["inventory_desc"]);
								$form['OS_use'] = str_replace(',','.',$form['OS_use']);
								$form['CS_use'] = str_replace(',','.',$form['CS_use']);
								$form['totalQty'] = str_replace(',','.',$form['totalQty']);
								$form['unitAmount'] = str_replace(',','.',$form['unitAmount']);
								$form['unitQuantity'] = str_replace(',','.',$form['unitQuantity']);
							}
/*
							//	IF ERRORS OR NO POST REQUEST
								$facility = new Facility($this->db);
								$facilityDetails = $facility->getFacilityDetails($facilityID);
								$companyID = $facilityDetails['company_id'];

								$this->setNavigationUpNew('department', $this->getFromRequest($backCategory.'ID'));
								$this->setPermissionsNew('viewData');
							$ms = new ModuleSystem($this->db);
							$moduleMap = $ms->getModulesMap();
							foreach($moduleMap as $key=>$module) {
								$showModules[$key] = $this->user->checkAccess($key, $companyID);
							}
							$this->smarty->assign('show',$showModules);

							if(!$showModules['inventory']) {
								throw new Exception('deny');
							}

							//	ok, we have access to inventory..
							$mIventory = new $moduleMap['inventory'];

							$params = array(
									'db' => $this->db,
									'request' => $this->getFromRequest(),
									'form' => $form,
									'facilityID' => $facilityID,
									'smarty' => $this->smarty
							);
							$result = $mIventory->prepareEdit($params);

							foreach ($result as $key=>$value) {
								$this->smarty->assign($key,$value);
							}

							$this->setListCategoriesLeftNew($backCategory, $this->getFromRequest($backCategory.'ID'), array('bookmark'=>'inventory','tab'=>$result['tab']));
*/
							//	set js scripts
							$jsSources = array(
								'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
								'modules/js/inventory.js'
							);
							$this->smarty->assign('jsSources', $jsSources);
							$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
							$this->smarty->assign('cssSources', $cssSources);

							//	set tpl
		$this->smarty->assign('tpl', "inventory/design/inventoryProductsEdit.tpl");

		$this->smarty->display("tpls:index.tpl");
	}

	/**
	 * bookmarkInventory($vars)
	 * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
	 */
	protected function bookmarkInventory($vars)
	{
		/*New inventory 26 Jan 2012*/		
		extract($vars);

		$sortStr=$this->sortList('inventory',3);

		$this->smarty->assign('tab',$this->getFromRequest('tab'));

		//$facility->initializeByID($this->getFromRequest('id'));
		if (!$this->user->checkAccess('inventory', $facilityDetails['company_id']))
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$category = 'facility';
		$id = $this->getFromRequest('id');
		
		//Product Usage
		$ProductInventory = new ProductInventory($this->db);

		$inventoryManager = new InventoryManager($this->db);
		$limit = 50;
		$data = $inventoryManager->getProductUsageGetAll($ProductInventory->period_start_date, $ProductInventory->period_end_date, $category, $id);		
			foreach ($data as $value) {

				$value->url = "?action=viewDetails&category=inventory&id=".$value->product_id."&".$category."ID=".$id;		
				
			
			//	ini indicator (gauge)	
			$limit = 50;
			$pxCount = round(200 * $value->usage / $limit);
			if ($pxCount > 200) {
					$pxCount = 200;
			}				

			$value->pxCount = $pxCount;	
			
			}
		$this->smarty->assign('inStock',$limit);
		
		$this->smarty->assign('Products',$data);
		$this->smarty->assign('tpl','inventory/design/inventoryProducts.tpl');		
	
		
		


		
		
		
		
		
		
		
		
/*		
//TODEL
		$mInventory = new $moduleMap['inventory'];
		$facility->initializeByID($this->getFromRequest('id'));
		//	ini VOC indicator (gauge)
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		$params = array(
			'facility' => $facility,
			'request' => $this->getFromRequest(),
			'sort'=>$sortStr
		);

		$result = $mInventory->prepareList($params);
//TODEL
		$export=$this->getFromRequest('export');
		if ($export) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$company = new Company($this->db);
			$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->title = "Inventories of facility ".$facilityDetails['name'];
			if ($_GET['searchAction']=='search') {
				$exporter->search_term = $this->getFromRequest('q');
			} else {
				$exporter->field = $filterData['filterField'];
				$exporter->condition = $filterData['filterCondition'];
				$exporter->value = $filterData['filterValue'];
			}
			$widths = array(
				'id' => 10,
				'name' => 40,
				'description' => 50,
			);
			$header = array(
				'id' => 'ID Number',
				'name' => 'Inventory Name',
				'description' => 'Inventory Description',
			);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($result['childCategoryItems']);
			$exporter->export();
			die();
		} else {
			foreach ($result as $key=>$value) {
				$this->smarty->assign($key,$value);
			}
			//	set js scripts
			$jsSources = array('modules/js/checkBoxes.js');
			$this->smarty->assign('jsSources', $jsSources);
			$this->smarty->assign('tpl','inventory/design/inventoryProducts.tpl');
		}
*/	
	}

	/**
	 * bookmarkDInventory($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDInventory($vars)
	{

		extract($vars);
		$sortStr=$this->sortList('inventory',3);
		$this->smarty->assign('tab',$this->getFromRequest('tab'));
		$departments = new Department($this->db);
		$departments->initializeByID($this->getFromRequest('id'));
		$facility = new Facility($this->db);
		$facility->initializeByID($departments->getFacilityID());

		if (!$this->user->checkAccess('inventory', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}

		//	OK, this company has access to this module, so let's setup..
		$mInventory = new $moduleMap['inventory'];

		$params = array(
			'facility' => $facility,
			'departments'=>$departments,
			'request' => $this->getFromRequest(),
			'sort'=>$sortStr
		);
		$result = $mInventory->prepareList($params);
		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Inventories of department ".$departmentDetails['name'];
			if ($_GET['searchAction']=='search')
			{
				$exporter->search_term = $this->getFromRequest('q');
			}
			else
			{
				$exporter->field = $filterData['filterField'];
				$exporter->condition = $filterData['filterCondition'];
				$exporter->value = $filterData['filterValue'];
			}
			$widths = array(
				'id' => 10,
				'name' => 40,
				'description' => 50,
			);
			$header = array(
				'id' => 'ID Number',
				'name' => 'Inventory Name',
				'description' => 'Inventory Description',
			);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);
			$exporter->setTbody($result['childCategoryItems']);
			$exporter->export();
			die();
		}
		else
		{
			foreach ($result as $key=>$value) {
				$this->smarty->assign($key,$value);
			}
			//	set js scripts
			$jsSources = array('modules/js/checkBoxes.js');
			$this->smarty->assign('jsSources', $jsSources);
		}
		}
}
?>