<?php
class CLogbook extends Controller
{
	function CLogbook($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='logbook';
		$this->parent_category='facility';
	}

	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);
		if (method_exists($this,$functionName))
			$this->$functionName();
	}

	private function actionConfirmDelete()
	{
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromPost('facilityID'));

		if (!$this->user->checkAccess('logbook', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mLogbook = new $moduleMap['logbook'];

		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromPost('facilityID'),
						'confirmed' => true,
						'idArray' => $this->itemID
						);
		$mLogbook->prepareDelete($params);

		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromPost('facilityID')."&bookmark=logbook&notify=26");
	}


	private function actionDeleteItem()
	{
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		//delete of direct carbon footprint!!
		$facility->initializeByID($this->getFromRequest('facilityID'));

		if (!$this->user->checkAccess('logbook', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mLogbook = new $moduleMap['logbook'];
		$idArray = $this->getFromRequest('checkLogbook');

		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('facilityID'),
						'confirmed' => false,
						'idArray' => $idArray
						);
	 	extract($mLogbook->prepareDelete($params));

		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'logbook'));
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$this->setPermissionsNew('facility');

		$this->smarty->assign('facilityID', $this->getFromRequest('facilityID'));
		$this->smarty->assign('cancelUrl', "?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=logbook");
		$this->smarty->assign('notViewChildCategory', true);
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}

	private function actionAddItem()
	{
		$request=$this->getFromRequest();
		$facility = new Facility($this->db);
		$facility->initializeByID($request['facilityID']);
		$facilityDetails = $facility->getFacilityDetails($request['facilityID']);

		$this->setListCategoriesLeftNew('facility', $request["facilityID"], array('bookmark'=>'logbook'));
		$this->setNavigationUpNew('facility', $request["facilityID"]);
		$this->setPermissionsNew('viewFacility');

		//voc indicator
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		if (!$this->user->checkAccess('logbook', $facilityDetails['company_id']))
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..

		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mLogbook = new $moduleMap['logbook'];
		$params = array(
						'db' => $this->db,
						'facilityID' => $request['facilityID'],
						'request' => $request,
						'logbookType' => $this->getFromPost('typeOfRecord'),
						'date' => $this->getFromPost('date'),
						'removed' => ($this->getFromPost('setFilter') == 'removed')?true:false,
						'installed' => ($this->getFromPost('setFilter') == 'installed')?true:false,
						'filter_size' => $this->getFromPost('filterSize'),
						'filter_type' => $this->getFromPost('filterType'),
						'description' => $this->getFromPost('description'),
						'operator' => $this->getFromPost('operator'),
						'reason' => $this->getFromPost('reason'),
						'action' => $this->getFromPost('action'),
						'department' => $this->getFromPost('department'),
						'equipment' => $this->getFromPost('equipment'),
						'save' => (!is_null($this->getFromPost('save')))?true:false
						);
		$result = $mLogbook->prepareAdd($params);
		if ($result === true)
		{
			//	redirect
			header("Location: ?action=browseCategory&category=facility&id=".$request['facilityID']."&bookmark=logbook&notify=24");
			die();
		}
		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources',$cssSources);
		$this->smarty->assign('tpl','logbook/design/addLogbookRecord.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit()
	{
		$request=$this->getFromRequest();
		$facility = new Facility($this->db);
		$facility->initializeByID($request['facilityID']);
		$facilityDetails = $facility->getFacilityDetails($request['facilityID']);

		$this->setListCategoriesLeftNew('facility', $request["facilityID"],array('bookmark'=>'logbook'));
		$this->setNavigationUpNew('facility', $request["facilityID"]);
		$this->setPermissionsNew('viewFacility');

		//voc indicator
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		if (!$this->user->checkAccess('logbook', $facilityDetails['company_id']))
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..

		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mLogbook = new $moduleMap['logbook'];
		$params = array(
						'db' => $this->db,
						'facilityID' => $request['facilityID'],
						'request' => $request,
						'logbookType' => $this->getFromPost('typeOfRecord'),
						'date' => $this->getFromPost('date'),
						'removed' => ($this->getFromPost('setFilter') == 'removed')?true:false,
						'installed' => ($this->getFromPost('setFilter') == 'installed')?true:false,
						'filter_size' => $this->getFromPost('filterSize'),
						'filter_type' => $this->getFromPost('filterType'),
						'description' => $this->getFromPost('description'),
						'operator' => $this->getFromPost('operator'),
						'reason' => $this->getFromPost('reason'),
						'action' => $this->getFromPost('action'),
						'department' => $this->getFromPost('department'),
						'equipment' => $this->getFromPost('equipment'),
						'save' => (!is_null($this->getFromPost('save')))?true:false
					   );
		$result = $mLogbook->prepareAdd($params);
		if ($result === true)
		{
			//	redirect
			header("Location: ?action=browseCategory&category=facility&id=".$request['facilityID']."&bookmark=logbook&notify=25");
			die();
		}

		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources',$cssSources);
		$this->smarty->assign('tpl','logbook/design/addLogbookRecord.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionViewDetails()
	{
		$facility = new Facility($this->db);
		$facility->initializeByID($this->getFromRequest('facilityID'));
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));

		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'logbook'));
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$this->setPermissionsNew('viewFacility');

		//voc indicator
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		if (!$this->user->checkAccess('logbook', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..

		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mLogbook = new $moduleMap['logbook'];
		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('facilityID'),
						'id' => $this->getFromRequest('id')
						);
		$result = $mLogbook->prepareView($params);

		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		$this->smarty->assign('editUrl',"?action=edit&category=logbook&facilityID=".$this->getFromRequest('facilityID')."&id=".$this->getFromRequest('id')."");
		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('backUrl','?action=browseCategory&category=facility&id='.$this->getFromRequest('facilityID').'&bookmark=logbook&tab=month');
		$this->smarty->assign('tpl','logbook/design/viewLogbook.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	/**
     * bookmarkLogbook($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
	protected function bookmarkLogbook($vars)
	{
		extract($vars);



		$sortStr=$this->sortList('logbook',2);
		$filterStr=$this->filterList('logbook');

		$facility->initializeByID($this->getFromRequest('id'));

		//voc indicator
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		if (!$this->user->checkAccess('logbook', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$mLogbook = new $moduleMap['logbook'];

		//	search??
		if ($this->getFromRequest('searchAction')=='search') {
			$toFind = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}
		else
		{
			$toFind = false;
		}
		//$page = (isset($request['page']))?$request['page']:false;

		$filterData= array
		(
			'filterField'=>$this->getFromRequest('filterField'),
			'filterCondition'=>$this->getFromRequest('filterCondition'),
			'filterValue'=>$this->getFromRequest('filterValue')
		);

		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('id'),
						'toFind' => $toFind,
						//'page' => $page,
						'filter'=>$filterStr,
						'filterData'=>$filterData,
						'q' => $this->getFromRequest('q'),
						'sort' => $sortStr
						);
		$result = $mLogbook->prepareBrowse($params);


		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}
			$jsSources = array
			(
				'modules/js/autocomplete/jquery.autocomplete.js',
			);
			$this->smarty->assign('jsSources',$jsSources);
			$this->smarty->assign('tpl','logbook/design/listOfLastRecords.tpl');
	}
}
?>