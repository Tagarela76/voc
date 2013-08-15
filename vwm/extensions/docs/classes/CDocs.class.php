<?php
class CDocs extends Controller
{
	function CDocs($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='docs';
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

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module)
		{
			$showModules[$key] = $this->user->checkAccess($key, $facilityDetails['company_id']);
		}
		$this->smarty->assign('show',$showModules);

		if (!$this->user->checkAccess('docs', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}

		//	ok, we have access..
		$mDocs = new $moduleMap['docs'];
		$params = array(
							'db' => $this->db,
							'facilityID' => $this->getFromPost('facilityID'),
							'xnyo' => $this->xnyo
						);
		$successDeleteInventories = $mDocs->prepareDelete($params);	//$successDeleteInventories ?????????????

		if ($this->successDeleteInventories)
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromPost('facilityID')."&bookmark=docs&notify=11");
	}

	private function actionDeleteItem()
	{
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);

		if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
			throw new Exception('deny');
		}

		$this->setListCategoriesLeftNew('facility', $this->getFromRequest("facilityID"), array('bookmark'=>'docs'));
		$this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
		$this->setPermissionsNew('facility');

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest("facilityID"));
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module) {
			$showModules[$key] = $this->user->checkAccess($key, $facilityDetails['company_id']);
		}
		$this->smarty->assign('show',$showModules);

		if (!$this->user->checkAccess('docs', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}

		//	ok, we have access to module ..
		$mDocs = new $moduleMap['docs'];
		if ($this->getFromPost('step') == null)
		{
			$this->smarty->assign('step','choose');
		}
		else
		{
			$params = array(
							'db' => $this->db,
							'facilityID' => $this->getFromRequest("facilityID"),
							'xnyo' => $this->xnyo
							);
			$result = $mDocs->prepareViewDelete($params);
			foreach($result as $key => $data) {
				$this->smarty->assign($key,$data);
			}
		}

		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest("facilityID")
						);
		$result = $mDocs->prepareView($params);

		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}

		//	set js scripts
		$jsSources = array('modules/js/listDocs.js');
		$this->smarty->assign('jsSources', $jsSources);

		$this->smarty->assign('tpl','docs/design/deleteDocItem.tpl');
		$this->smarty->assign('request',$this->getFromRequest());
		$this->smarty->display("tpls:index.tpl");
		//$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
		die();
	}

	private function actionAddItem()
	{
		$request= $this->getFromRequest();
		$request['id'] = $request['facilityID'];
		$request['parent_category'] = 'facility';

		//	Access control
		if (!$this->user->checkAccess($request['parent_category'], $request['id']))
		{
			throw new Exception('deny');
		}

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($request['facilityID']);

		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		if (!$this->user->checkAccess('docs', $facilityDetails['company_id']))
		{
			throw new Exception('deny');
		}

		$this->setListCategoriesLeftNew('facility', $request["facilityID"], array('bookmark'=>'docs'));
		$this->setNavigationUpNew('facility', $request["facilityID"]);
		$this->setPermissionsNew('facility');

		$mDocs = new $moduleMap['docs'];

		if($_FILES)
		{
			$params = $this->getFromPost();
			$params['db'] = $this->db;
			$params['facilityID'] = $request['facilityID'];
			$result = $mDocs->prepareAdd($params);

			foreach($result as $key => $data)
			{
				$this->smarty->assign($key,$data);
			}

		}
		else
		{
			$result = $mDocs->prepareConstants($params);
			$this->smarty->assign('info',array('folder' => 'none', 'item_type' => $result['doc_item']));
		}

		$params = array(
						'db' => $this->db,
						'facilityID' => $request['id']
						);
		$result = $mDocs->prepareView($params);
		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}

		//	set js scripts
		$jsSources = array(
							'modules/js/addDocItem.js',
							'modules/js/listDocs.js'
						  );
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl','docs/design/addDocItem.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	private function actionEdit()
	{
		//	Access control
		$request=$this->getFromRequest();
		if (!$this->user->checkAccess('facility', $request["facilityID"])) {
			throw new Exception('deny');
		}

		$this->setListCategoriesLeftNew('facility', $request["facilityID"],array('bookmark'=>'docs'));
		$this->setNavigationUpNew('facility', $request["facilityID"]);
		$this->setPermissionsNew('facility');

		// protecting from xss
		$post =$this->getFromPost();
		foreach ($post as $key=>$value)
		{
			$post[$key]=Reform::HtmlEncode($value);
		}

		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($request['facilityID']);

		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		foreach($moduleMap as $key=>$module)
		{
			$showModules[$key] = $this->user->checkAccess($key, $facilityDetails['company_id']);
		}
		$this->smarty->assign('show',$showModules);

		if (!$this->user->checkAccess('docs',$facilityDetails['company_id']))
		{
			throw new Exception('deny');
		}

		//	ok, we have access to module docs..
		$mDocs = new $moduleMap['docs'];

		if(!is_null($this->getFromPost('file')))
		{
			$params = $post;
			$params['db'] = $this->db;
			$params['facilityID'] = $request['facilityID'];
			$result = $mDocs->prepareEdit($params);
			foreach($result as $key => $data)
			{
				$this->smarty->assign($key,$data);
			}
		}
		elseif (($post['folder']!= null) || ($post['name']!=null) || ($post['description']!=null))
		{
			$this->smarty->assign('info',$post);
			$this->smarty->assign('error','Choose the document to edit it!');
		}
		else
		{
			$result = $mDocs->prepareConstants($this->db);
			$this->smarty->assign('info',array('item_type' => $result['doc_item']));
		}
		$params = array(
							'db' => $this->db,
							'facilityID' => $request['facilityID']
						);
		$result = $mDocs->prepareView($params);

		foreach($result as $key => $data)
		{
			$this->smarty->assign($key,$data);
		}

		//	set js scripts
		$jsSources = array(
								'modules/js/addDocItem.js',
								'modules/js/listDocs.js'
						  );
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl','docs/design/editDocItem.tpl');
		$this->smarty->display("tpls:index.tpl");
	}

	/**
     * bookmarkDocs($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
	protected function bookmarkDocs($vars)
	{
		extract($vars);

		$facility->initializeByID($this->getFromRequest('id'));

		//voc indicator
		$this->setIndicator($facility->getMonthlyLimit(), $facility->getCurrentUsage());

		if (!$this->user->checkAccess('docs', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}

		//	OK, this company has access to this module, so let's setup..
		$mDocs = new $moduleMap['docs'];

		$params = array(
			'db' => $this->db,
			'facilityID' => $this->getFromRequest('id')
		);
		$result = $mDocs->prepareView($params);
        //sort documents by file name
        $docList = $result['InfoTree'];
        usort($docList, function($a,$b) {
                    return strcmp($a["info"]["name"], $b["info"]["name"]);
                });
        $result['InfoTree'] = $docList;
        
		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}

		$this->smarty->assign('tpl','docs/design/documentsList.tpl');
	}
    
    

}
?>