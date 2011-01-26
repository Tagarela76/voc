<?php
class Controller 
{
	protected $smarty;
	protected $xnyo;
	protected $db;
	protected $user;
	protected $action;
	private   $post;
	private	  $request;
	protected $category;
	protected $parentCategory;	
	protected $filter;
	
	function Controller($smarty,$xnyo,$db,$user,$action)
	{		
		$this->smarty	=$smarty;
		$this->xnyo		=$xnyo;
		$this->db		=$db;
		$this->user		=$user;
		$this->action	=$action;
		$this->request	=$_GET;
		$this->post		=$_POST;		

		if(isset($this->request['notify']) and is_numeric($this->request['notify']))
		{
			$notifyc = new Notify(null, $db);
			
			$notify = $notifyc->getPopUpNotifyMessage($_GET['notify']);
			
			$this->smarty->assign("notify", $notify);
			
		}
	}
		
	protected function forvard($controller,$function,$vars)
	{
		$className="C".ucfirst($controller);
		if (class_exists($className)) {
			$controllerObj=new $className($this->smarty,$this->xnyo,$this->db,$this->user,$this->action);
		} else {
			throw new Exception('404');
		}
		if (method_exists($controllerObj,$function))						
			$controllerObj->$function($vars);
		else 
			throw new Exception('404');
	
		return $controllerObj;
	}
	
	protected function runCommon()
	{			
		$title = new TitlesNew($this->smarty, $this->db);		
		$title->getTitle($this->getFromRequest());
		
		$functionName='action'.ucfirst($this->action).'Common';		
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function filterList($category)
	{									
		$this->filter=new Filter($this->db,$category);								
		$this->smarty->assign('filterArray',$this->filter->getJsonFilterArray());
									
		$filterData= array
		(
			'filterField'		=>$this->getFromRequest('filterField'),
			'filterCondition'	=>$this->getFromRequest('filterCondition'),
			'filterValue'		=>$this->getFromRequest('filterValue')
		);
												
		if ($this->getFromRequest('searchAction')=='filter')
		{										
			$this->smarty->assign('filterData',$filterData);
			$this->smarty->assign('searchAction','filter');										
		}
		$filterStr = $this->filter->getSubQuery($filterData);
				
		return $filterStr;
	}
	
	protected function sortList($category,$defaultNum)
	{		
		$sort= new Sort($this->db,$category,$defaultNum);
		$getSort=$this->getFromRequest('sort');		
		if (isset($getSort))
		{
			$sortStr = $sort->getSubQuerySort($this->getFromRequest('sort'));										
			$this->smarty->assign('sort',$this->getFromRequest('sort'));
		}		
		else
		{	
			$sortStr = $sort->getSubQuerySort();								
			$this->smarty->assign('sort',$defaultNum);
		}
		
		$getSearchAction=$this->getFromRequest('searchAction');			
		if (isset($getSearchAction))									
			$this->smarty->assign('searchAction',$this->getFromRequest('searchAction'));
			
		return 	$sortStr;	
	}	
	
	private function actionShowIssueReportCommon()
	{
		$request = $this->getFromRequest();					
		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);		
		$title->getTitle($request);					
		$this->noname($request, $this->user, $this->db, $this->smarty);
					
		$this->smarty->assign('accessname', $_SESSION['username']);
		$this->smarty->assign('request', $request);					
						
		$this->smarty->assign("referer", $_SERVER["HTTP_REFERER"]);
		$this->smarty->assign("tpl", "tpls/issueReportForm.tpl");
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionReportIssueCommon()
	{
		//titles new!!! {panding}
		$request = $this->getFromRequest();
		$title = new TitlesNew($this->smarty, $this->db);
		$title->getTitle($request);
		
		
						
		if ($this->getFromPost("issueAction") == "Send") 
		{					
			//	Group issue details
			
			$issueDetails["title"] = $this->getFromPost("issueTitle");
			$issueDetails["description"] = $this->getFromPost("issueDescription");
			$issueDetails["referer"] = $this->getFromPost("referer");
			$issueDetails["creatorID"] = $this->getFromPost("user_id");
			
			$userID = $this->user->getLoggedUserID();	
			
			if(!$userID) /*User id is not defined*/
			{
				throw new Exception("creatorID doesnot exists in POST.");
			}
			else
			{
				$issueDetails["creatorID"] = $userID;
			}
						
			//	Validate issue
			$validation = new Validation();
			$validationStatus = $validation->validateIssue($issueDetails);
						
			if ($validationStatus["summary"] == "true") 
			{
				//	Add issue to DB
				$issue = new Issue($this->db);
				$issue->addIssue($issueDetails);
							
				//	Redirect to previous page
				header("Location:".$issueDetails["referer"]."&message=Issuereported&color=green");
			} 
			else 
			{
				//	Incorrect input
				$this->smarty->assign("issueTitle", $issueDetails["title"]);
				$this->smarty->assign("issueDescription", $issueDetails["description"]);
				$this->smarty->assign("referer", $issueDetails["referer"]);
							
				//	Prepare Notify system
				$notify = new Notify($this->smarty);
				$notify->formErrors();
							
				$this->smarty->assign("validStatus", $validationStatus);
							
				$this->noname();
							
				$this->smarty->assign('accessname', $_SESSION['username']);
				$this->smarty->assign('request', $request);					
							
//				$title = new Titles($smarty);
//				$title->titleIssueReport();
														
				$this->smarty->assign("tpl", "tpls/issueReportForm.tpl");
				$this->smarty->display("tpls:index.tpl");
			}
		} else 
		{
			//	Discard issue
			header("Location:".$_POST["referer"]);
		}					
	}
	
	private function actionSendReportCommon()
	{		
		$request = $this->getFromRequest();	
		$this->smarty->assign("request", $request);
		$this->noname();
		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);	
		$title->getTitle($request);	
					
		switch ($request['category']) 
		{
			case 'company':
				$companyID = $request['id'];
				break;
			case 'facility':
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($request['id']);
				$companyID = $facilityDetails['company_id'];
				break;
			case 'department':
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($request['id']);
				break;
		}
						
		$reportType = $request['reportType'];					
					
		if (!$this->user->checkAccess('reports', $companyID)) 
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
					
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mReport = new $moduleMap['reports'];
		
		$params = array(
						'db' => $this->db,								
						'reportType' => $reportType,
						'companyID' => $companyID,
						'request' => $request
						);
		$result = $mReport->prepareSendReport($params);
		
		foreach($result as $key => $data) 
		{
			$this->smarty->assign($key,$data);												
		}									
					
		//	set js scripts				
		$jsSources = array(
						'modules/js/reports.js',						
						'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
						);							
		$this->smarty->assign('jsSources', $jsSources);
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources',$cssSources);
		$this->smarty->assign('backUrl','?action=createReport&category='.$request['category'].'&id='.$request['id']);

		$this->smarty->display("tpls:index.tpl");	
	} 	
	
	private function actionCreateReportCommon()
	{
		$request = $this->getFromRequest();				
		$this->noname($request);					
		$this->smarty->assign('request', $request);
		$this->smarty->assign('accessname', $_SESSION['username']);
					
		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $tihs->db);
		
		$title->getTitle($request);	
					
		switch ($request['category']) 
		{
			case 'company':
				$companyID = $request['id'];
				break;
			case 'facility':
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($request['id']);
				$companyID = $facilityDetails['company_id'];
				break;
			case 'department':
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($request['id']);
				break;
		}
						
		$reportType = $this->getFromRequest('reportType');					
					
		if (!$this->user->checkAccess('reports', $companyID)) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
					
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mReport = new $moduleMap['reports'];
		$result = $mReport->getAvailableReportsList($this->db, $companyID);
		$this->smarty->assign('reports',$result);
						
		$this->smarty->assign('tpl', 'reports/design/createReport.tpl');
		$this->smarty->display("tpls:index.tpl");	
	}
	
	private function actionSettingsCommon()
	{		
		$this->smarty->assign('request', $this->getFromRequest());
					
		$cfd = $this->noname();		

		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);		
		$title->getTitle($this->getFromRequest());	
						
		//	Get rule list
		$rule = new Rule($this->db);
		$ruleList = $rule->getRuleList();
		//$cfd =  getCompanyFacilityDepartment($db);	//	Company Facility Department
		$customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $cfd['companyID'], $cfd['facilityID'], $cfd['departmentID']);					
		$this->smarty->assign('ruleList', $ruleList);							
		$this->smarty->assign('customizedRuleList', $customizedRuleList);
		$this->smarty->assign('cfd', $cfd);
		$this->smarty->assign('userID', $_SESSION['user_id']);
		$emailNotifications = new EmailNotifications($this->db);
		$this->smarty->assign('notificationsList',$emailNotifications->getAllLimits());
		$this->smarty->assign('notificationsListSelected',$emailNotifications->getLimitsListByUser($_SESSION['user_id']));
					
		if (isset($request['bookmark'])) 
		{
			$backUrl = "?action=browseCategory&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id')."&bookmark=".$this->getFromRequest('bookmark');
			switch($this->getFromRequest('bookmark')) 
			{
				case 'inventory':
					$backUrl .= "&tab=material";
					break;
				case 'solventplan':
				case 'carbonfootprint':
					$backUrl .= "&tab=month";
					break;
				case 'wastestorage':
					$backUrl .= "&tab=active";
					break;
			}
		} 
		elseif ($this->getFromRequest('category') == 'company') 
		{
			$backUrl = "?action=browseCategory&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id');
		} 
		else 
		{
			$backUrl = "?action=viewDetails&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id');
		}
		$this->smarty->assign('backUrl',$backUrl);
		//fullNavigation($_SESSION['overCategoryType'], $user, $db, $smarty, $xnyo);					
		$this->smarty->display("tpls:settings.tpl");
	}
	
	private function actionConfirmDeleteCommon()
	{					
		if ($this->getFromPost('confirm') != 'Yes') 
			throw new Exception('404');
			
		$this->smarty->assign("accessname", $_SESSION["username"]);	
		$itemsCount = $this->getFromPost('itemsCount');
		for ($i=0; $i<$itemsCount; $i++) 
		{
			if (!is_null($this->getFromPost('item_'.$i))) 
			{
				$itemID[] = $this->getFromPost('item_'.$i);
			}
		}
		$this->overCategoryType = $this->getFromPost('itemID');
						
		//we will need this var in future, dont delete it:
		$this->successDeleteInventories=true;
		$this->itemID=$itemID;
	}	
	
	private function actionDeleteItemCommon()
	{		
		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);
		$request = $this->getFromRequest();
		$title->getTitle($this->getFromRequest());	
		$request = $this->getFromRequest();
		$this->smarty->assign("request", $this->getFromRequest());
		$this->smarty->assign("accessname", $_SESSION["username"]);
	}
	
	protected function finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info)
	{
		$notify = new Notify($this->smarty);		
		switch (count($itemForDelete)) 
		{
			case 0:
				$notify->notSelected($this->getFromRequest('category'));
				$this->smarty->assign("tpl", "tpls/deleteCategories.tpl");
				break;
			case 1:	
				$this->smarty->assign("tpl", "tpls/deleteCategory.tpl");
				break;
			default:
				$notify->warnDelete($this->getFromRequest('category'),"",$linkedNotify,$count,$info);
				$this->smarty->assign("tpl", "tpls/deleteCategories.tpl");
			break;						
		}
		$this->smarty->assign("itemForDelete", $itemForDelete);	
		$this->smarty->assign("itemType", $this->getFromRequest('category'));					
		$this->smarty->assign("itemsCount", count($itemForDelete));						
		$this->smarty->display("tpls:index.tpl");
	} 
	
	private function actionViewDetailsCommon()
	{					
		$title = new TitlesNew($this->smarty, $this->db);
		$title->getTitle($this->request);		
		$this->smarty->assign("request", $this->request);
		$this->smarty->assign("accessname", $_SESSION["username"]);
	}
	
	private function actionBrowseCategoryCommon()
	{
		$paramsForListLeft = array();
		if (!is_null($this->getFromRequest('bookmark'))) {
			$paramsForListLeft ['bookmark']= $this->getFromRequest('bookmark');
		}
		if (!is_null($this->getFromRequest('tab'))) {
			$paramsForListLeft ['tab']= $this->getFromRequest('tab');
		}
		
		$this->setListCategoriesLeftNew($this->getFromRequest('category'), $this->getFromRequest('id'), $paramsForListLeft); //TODO add in all Controls paramsForListLeft!
		$this->setNavigationUpNew ($this->getFromRequest('category'), $this->getFromRequest('id'));
		$this->setPermissionsNew($this->getFromRequest('category'));
		
		$this->smarty->assign('accessname', $_SESSION['username']);		
		$this->smarty->assign('request', $this->request);
					
		//	Access control					
		if (!$this->user->checkAccess($this->getFromRequest('category'),$this->getFromRequest('id'))) {						
			throw new Exception('deny');
		}
					
		$paramsForListLeft = array();
		if (!empty($this->request['bookmark'])) {
			$paramsForListLeft ['bookmark']= $this->request['bookmark'];
		}
		if (!empty($this->request['tab'])) {
			$paramsForListLeft ['tab']= $this->getFromRequest('tab');
		}		
	}
	
	private function actionAddItemCommon() {
		$title = new TitlesNew($this->smarty, $this->db);
		$request = $_GET;
		$title->getTitle($request);	
		$this->smarty->assign('request', $request);
		$this->smarty->assign("accessname", $_SESSION["username"]);	
	}
	
	private function actionEditCommon() {
		$title = new TitlesNew($this->smarty, $this->db);
		$request = $_GET;
		$title->getTitle($request);	
		
		$this->smarty->assign('request', $request);
		$this->smarty->assign("accessname", $_SESSION["username"]);				
	}
		
	//	voc indicator
	protected function setIndicator($vocLimit, $totalUsage) {
		$this->smarty->assign('vocLimit', $vocLimit);
		$this->smarty->assign('currentUsage', round($totalUsage, 2));
		$pxCount = round(200*$totalUsage/$vocLimit);
		if ($pxCount > 200) {
			$pxCount = 200;
		}
		$this->smarty->assign('pxCount', $pxCount);	//	200px - indicator length
	}
	
	protected function convertSearchItemsToArray($query) {
		$firstStep = explode(',', $query);
		foreach ($firstStep as $item) {
			$secondStep = explode(';', $item);
			foreach ($secondStep as $finalItem) {
				$finalItems[] = trim($finalItem);
			}
		}
		return $finalItems;	
	}
	
	protected function setListCategoriesLeftNew($category, $id,$params = null) {		
		$tail = '';
		if (!is_null($params)) {
			foreach($params as $key => $value) {
				$tail .= "&$key=$value";
			}
		}
		switch ($category) {
			case "company":
				$companyObj = new Company($this->db);
				$companyList = $companyObj->getCompanyList();
				foreach($companyList as $key=>$company) {
					$url = "?action=browseCategory&category=company&id=".$company['id'].$tail;
					$companyList[$key]['url'] = $url;
				}				
				$this->smarty->assign("upCategory", $companyList);
				$this->smarty->assign("upCategoryName", LABEL_LEFT_COMPANIES_TITLE);				
				break;
			case "carbonfootprint":
			case "facility":						
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($id);
				$facilityList = $facility->getFacilityListByCompany($facilityDetails['company_id']);
				for ($i=0; $i < count($facilityList); $i++) {
					$url="?action=browseCategory&category=facility&id=".$facilityList[$i]['id'].(($tail == '')?"&bookmark=department":$tail);
					$facilityList[$i]['url'] = $url;
				}				
				$this->smarty->assign("upCategory", $facilityList);
				$this->smarty->assign("upCategoryName", LABEL_LEFT_FACILITIES_TITLE);
				break;
			case "department":
				$departments = new Department($this->db);
				$departmentDetails = $departments->getDepartmentDetails($id);
				$departmentList = $departments->getDepartmentListByFacility($departmentDetails['facility_id']);
				for ($i=0; $i < count($departmentList); $i++) {
					$url="?action=browseCategory&category=department&id=".$departmentList[$i]['id'].(($tail == '')?"&bookmark=mix":$tail);
					$departmentList[$i]['url']=$url;
				}			
				$this->smarty->assign("upCategory", $departmentList);
				$this->smarty->assign("upCategoryName", LABEL_LEFT_DEPARTMENTS_TITLE);
				break;			
		}
		$this->smarty->assign("leftCategoryID", $id);
	}
		
	
	protected function setNavigationUpNew ($category, $id) {
		switch ($category) {
			case "root":
				$this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
				break;
			case "company":
				$this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
				
				$company = new Company($this->db);
				$companyDetails = $company->getCompanyDetails($id);
				
				$this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=" .$id);
				$this->smarty->assign('companyName', $companyDetails['name']);
				$this->smarty->assign('address', $companyDetails['address']);
				$this->smarty->assign('contact', $companyDetails['contact']);
				$this->smarty->assign('phone', $companyDetails['phone']);
				break;
			case "facility":
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($id);
				
				$company = new Company($this->db);
				$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);
				
				$this->smarty->assign("companyName", $companyDetails['name']);
				$this->smarty->assign("facilityName", $facilityDetails['name']);
				$this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
				$this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=".$facilityDetails['company_id']);
				$this->smarty->assign('urlFacility', "?action=browseCategory&category=facility&id=".$id."&bookmark=department");
				
				$this->smarty->assign('address', $facilityDetails['address']);
				$this->smarty->assign('contact', $facilityDetails['contact']);
				$this->smarty->assign('phone', $facilityDetails['phone']);
				break;
			case "department":			
				$department = new Department($this->db);
				$departmentDetails = $department->getDepartmentDetails($id);
				
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);
				
				$company = new Company($this->db);
				$companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);
				
				$this->smarty->assign("departmentName", $departmentDetails['name']);
				$this->smarty->assign("facilityName", $facilityDetails['name']);
				$this->smarty->assign("companyName", $companyDetails['name']);
				$this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
				$this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=".$facilityDetails['company_id']);
				$this->smarty->assign('urlFacility', "?action=browseCategory&category=facility&id=".$departmentDetails['facility_id']."&bookmark=department");
				$this->smarty->assign('urlDepartment', "?action=browseCategory&category=department&id=".$id."&bookmark=mix");							
				break;
		}
	}	
	
	protected function setPermissionsNew($category) {
		
		switch ($category) {
			case "root":
				$permissions['viewItem'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['addItem'] = $this->user->isHaveAccessTo('add', 'company') ? true : false;
				$permissions['deleteItem'] = $this->user->isHaveAccessTo('delete', 'company') ? true : false;
				if ($permissions['deleteItem'] == true || $permissions['addItem'] == true) {
					$permissions['showSelectAll'] = true;
				}
				break;
			case "company":
				$permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['viewCategory'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['deleteCategory'] = $this->user->isHaveAccessTo('delete', 'company') ? true : false;
				$permissions['viewItem'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['addItem'] = $this->user->isHaveAccessTo('add', 'facility') ? true : false;
				$permissions['deleteItem'] = $this->user->isHaveAccessTo('delete', 'facility') ? true : false;
				if ($permissions['deleteItem'] == true || $permissions['addItem'] == true) {
					$permissions['showSelectAll'] = true;
				}
				break;
			
			case "facility":
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['department']['view']=$this->user->isHaveAccessTo('view', 'department') ? true : false;
				$permissions['department']['edit']=$this->user->isHaveAccessTo('edit', 'department') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['viewCategory']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['viewItem']=$this->user->isHaveAccessTo('view', 'department') ? true : false;
				$permissions['addItem']=$this->user->isHaveAccessTo('add', 'department') ? true : false;
				$permissions['deleteItem']=$this->user->isHaveAccessTo('delete', 'department') ? true : false;
				$permissions['deleteCategory']=$this->user->isHaveAccessTo('delete', 'facility') ? true : false;
				if ($permissions['deleteItem']==true || $permissions['addItem']==true) {
					$permissions['showSelectAll']=true;
				}	
				
				$permissions['data']['view']=$this->user->isHaveAccessTo('view', 'data') ? true : false;
				$permissions['data']['edit']=$this->user->isHaveAccessTo('edit', 'data') ? true : false;
				$permissions['data']['add']=$this->user->isHaveAccessTo('add', 'data') ? true : false;
				$permissions['data']['delete']=$this->user->isHaveAccessTo('delete', 'data') ? true : false;
				if ($permissions['data']['delete']==true || $permissions['data']['add']==true) {
					$permissions['data']['showSelectAll']=true;
				}			
				
				break;
				
			case "department":	
			
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['department']['view']=$this->user->isHaveAccessTo('view', 'department') ? true : false;
				$permissions['deleteCategory']=$this->user->isHaveAccessTo('delete', 'department') ? true : false;
				$permissions['viewCategory']=$this->user->isHaveAccessTo('view', 'department') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				
				$permissions['equipment']['view']=$this->user->isHaveAccessTo('view', 'equipment') ? true : false;
				$permissions['equipment']['edit']=$this->user->isHaveAccessTo('edit', 'equipment') ? true : false;
				$permissions['equipment']['add']=$this->user->isHaveAccessTo('add', 'equipment') ? true : false;
				$permissions['equipment']['delete']=$this->user->isHaveAccessTo('delete', 'equipment') ? true : false;
				if ($permissions['equipment']['delete']==true || $permissions['equipment']['add']==true) {
					$permissions['equipment']['showSelectAll']=true;
				}
				
				$permissions['user']['view']=$this->user->isHaveAccessTo('view', 'user') ? true : false;
				$permissions['user']['edit']=$this->user->isHaveAccessTo('edit', 'user') ? true : false;
				$permissions['user']['add']=$this->user->isHaveAccessTo('add', 'user') ? true : false;
				$permissions['user']['delete']=$this->user->isHaveAccessTo('delete', 'user') ? true : false;
				if ($permissions['user']['delete']==true || $permissions['user']['add']==true) {
					$permissions['user']['showSelectAll']=true;
				}
				
				$permissions['data']['view']=$this->user->isHaveAccessTo('view', 'data') ? true : false;
				$permissions['data']['edit']=$this->user->isHaveAccessTo('edit', 'data') ? true : false;
				$permissions['data']['add']=$this->user->isHaveAccessTo('add', 'data') ? true : false;
				$permissions['data']['delete']=$this->user->isHaveAccessTo('delete', 'data') ? true : false;
				if ($permissions['data']['delete']==true || $permissions['data']['add']==true) {
					$permissions['data']['showSelectAll']=true;
				}				
				break;									
				//-----------------------------------------------------------------------------------
			case "insideDepartment":
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['department']['view']=$this->user->isHaveAccessTo('view', 'department') ? true : false;
				$permissions['deleteCategory']=$this->user->isHaveAccessTo('delete', 'department') ? true : false;
				$permissions['viewCategory']=$this->user->isHaveAccessTo('view', 'department') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				
				$permissions['equipment']['view']=$this->user->isHaveAccessTo('view', 'equipment') ? true : false;
				$permissions['equipment']['edit']=$this->user->isHaveAccessTo('edit', 'equipment') ? true : false;
				$permissions['equipment']['add']=$this->user->isHaveAccessTo('add', 'equipment') ? true : false;
				$permissions['equipment']['delete']=$this->user->isHaveAccessTo('delete', 'equipment') ? true : false;
				if ($permissions['equipment']['delete']==true || $permissions['equipment']['add']==true) {
					$permissions['equipment']['showSelectAll']=true;
				}
				
				$permissions['user']['view']=$this->user->isHaveAccessTo('view', 'user') ? true : false;
				$permissions['user']['edit']=$this->user->isHaveAccessTo('edit', 'user') ? true : false;
				$permissions['user']['add']=$this->user->isHaveAccessTo('add', 'user') ? true : false;
				$permissions['user']['delete']=$this->user->isHaveAccessTo('delete', 'user') ? true : false;
				if ($permissions['user']['delete']==true || $permissions['user']['add']==true) {
					$permissions['user']['showSelectAll']=true;
				}
				
				$permissions['data']['view']=$this->user->isHaveAccessTo('view', 'data') ? true : false;
				$permissions['data']['edit']=$this->user->isHaveAccessTo('edit', 'data') ? true : false;
				$permissions['data']['add']=$this->user->isHaveAccessTo('add', 'data') ? true : false;
				$permissions['data']['delete']=$this->user->isHaveAccessTo('delete', 'data') ? true : false;
				if ($permissions['data']['delete']==true || $permissions['data']['add']==true) {
					$permissions['data']['showSelectAll']=true;
				}
				
				break;
				
			case "viewRoot":
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				break;	
			case "viewCompany":
				$permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['edit']=$this->user->isHaveAccessTo('edit', 'company') ? true : false;
				$permissions['company']['delete']=$this->user->isHaveAccessTo('delete', 'company') ? true : false;								
				break;
			case "viewFacility":
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['facility']['edit']=$this->user->isHaveAccessTo('edit', 'facility') ? true : false;
				$permissions['facility']['delete']=$this->user->isHaveAccessTo('delete', 'facility') ? true : false;
				break;
			case "viewDepartment":
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['department']['edit']=$this->user->isHaveAccessTo('edit', 'department') ? true : false;
				$permissions['department']['delete']=$this->user->isHaveAccessTo('delete', 'department') ? true : false;
				break;
			case "viewEquipment":
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['equipment']['edit']=$this->user->isHaveAccessTo('edit', 'equipment') ? true : false;
				$permissions['equipment']['delete']=$this->user->isHaveAccessTo('delete', 'equipment') ? true : false;
				break;
			case "viewUser":
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['user']['edit']=$this->user->isHaveAccessTo('edit', 'user') ? true : false;
				$permissions['user']['delete']=$this->user->isHaveAccessTo('delete', 'user') ? true : false;
				break;
			case "viewData":
				$permissions['showOverCategory']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;				
				$permissions['data']['edit']=$this->user->isHaveAccessTo('edit', 'data') ? true : false;
				$permissions['data']['delete']=$this->user->isHaveAccessTo('delete', 'data') ? true : false;
				break;
			case "viewInsideDepartment":
				$permissions['root']['view']=$this->user->isHaveAccessTo('view', 'root') ? true : false;
				$permissions['company']['view']=$this->user->isHaveAccessTo('view', 'company') ? true : false;
				$permissions['facility']['view']=$this->user->isHaveAccessTo('view', 'facility') ? true : false;				
				break;
			
		}
		$this->smarty->assign('permissions', $permissions);	
	}
	
	function noname($request=null) {
		if ($request==null)
			$request=$this->request;
		switch ($request['category']) {
			case 'company':
				$companyID = $request['id'];
				$facilityID = null;
				$departmentID = null;
				$bookmark = null;
				//	set permissions
				$this->setListCategoriesLeftNew($request['category'], $request['id']);
				$this->setNavigationUpNew ($request['category'], $request['id']);			
				$this->setPermissionsNew($request['category']);
				$this->smarty->assign('categoryName', 'company');		
				break;
			case 'facility':
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($request['id']);
				$companyID = $facilityDetails['company_id'];
				$facilityID = $request['id'];
				$departmentID = null;
				$bookmark = 'department';
				//	set permissions
				$this->setListCategoriesLeftNew($request['category'], $request['id']);
				$this->setNavigationUpNew ($request['category'], $request['id']);			
				$this->setPermissionsNew($request['category']);
				$this->smarty->assign('categoryName', 'facility');
				break;
			case 'department':					
				$department = new Department($this->db);
				$departmentDetails = $department->getDepartmentDetails($request['id']);
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($request['id']);						
				$facilityID = $departmentDetails['facility_id'];
				$departmentID = $request['id'];
				$bookmark = 'mix';
				//	set permissions
				$this->setListCategoriesLeftNew($request['category'], $request['id']);
				$this->setNavigationUpNew ($request['category'], $request['id']);			
				$this->setPermissionsNew($request['category']);
				$this->smarty->assign('categoryName', 'department');
				break;
			case 'mix':			
				$mix = new Mix($this->db);
				$mixDetails = $mix->getMixDetails($request['id']);						 		
				$department = new Department($this->db);
				$departmentDetails = $department->getDepartmentDetails($mixDetails['department_id']);
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($mixDetails['department_id']);						
				$facilityID = $departmentDetails['facility_id'];
				$departmentID = $mixDetails['department_id'];
				$bookmark = 'mix';
				//	set permissions
				$this->setListCategoriesLeftNew('department', $departmentID);
				$this->setNavigationUpNew ('department', $departmentID);			
				$this->setPermissionsNew('department');
				$this->smarty->assign('categoryName', 'department');
				break;
			case 'equipment':			
				$equipment = new Equipment($this->db);
				$equipmentDetails = $equipment->getEquipmentDetails($request['id'], true);						 		
				$department = new Department($this->db);
				$departmentDetails = $department->getDepartmentDetails($equipmentDetails['department_id']);
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($equipmentDetails['department_id']);						
				$facilityID = $departmentDetails['facility_id'];
				$departmentID = $equipmentDetails['department_id'];
				$bookmark = 'equipment';
				$this->setListCategoriesLeftNew('department', $departmentID);
				$this->setNavigationUpNew ('department', $departmentID);			
				$this->setPermissionsNew('department');
				$this->smarty->assign('categoryName', 'department');
				break;

		}
		return array(
					'companyID'=>$companyID,
					'facilityID'=>$facilityID,
					'departmentID'=>$departmentID,
					'bookmark'=>$bookmark
				);
	}
	
	protected function getFromRequest($key)
	{		
		if (isset($key))
		{
			if (isset($this->request[$key]))
				return $this->request[$key];
			else 
				return null;
		}
		else 
			return $this->request;
	}	
	
	protected function getFromPost($key)
	{		
		if (isset($key))
		{
			if (isset($this->post[$key]))
				return $this->post[$key];
			else 
				return null;
		}
		else 
			return $this->post;
	}	
}
?>