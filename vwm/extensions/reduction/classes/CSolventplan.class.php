<?php
class CSolventplan extends Controller
{	
	function CSolventplsn($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='solventplan';
		$this->parent_category='facility';			
	}		

	function runAction() {
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionEdit() {
		$facility = new Facility($this->db);
							$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
							$facility->initializeByID($this->getFromRequest('facilityID'));
							
							$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
							$this->setPermissionsNew('viewFacility');
							
							//voc indicator
							$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
							
							if (!$this->user->checkAccess('reduction', $facilityDetails['company_id'])) {
								throw new Exception('deny');
							}
							//	OK, this company has access to this module, so let's setup..
							$ms = new ModuleSystem($this->db);	//	TODO: show?
							$moduleMap = $ms->getModulesMap();
							$mReduction = new $moduleMap['reduction'];
							$fields = $mReduction->getFields();
							$outputs = array();
							foreach ($fields as $field) {
								$outputs [$field]= $this->getFromPost($field);
							}

									$params = array(
										'db' => $this->db,
										'facilityID' => $this->getFromRequest('facilityID'),
										'save' => (!is_null($this->getFromPost('save')))?true:false,
										'outputs' => $outputs,
										'request' =>  $this->getFromRequest(),
										'month' => ($this->getFromPost('selectMonth') !== null)?$this->getFromPost('selectMonth'):$this->getFromRequest('mm'),
										'year' => ($this->getFromPost('selectYear') !== null)?$this->getFromPost('selectYear'):$this->getFromRequest('yyyy')
									);
			 						$result = $mReduction->prepareAddSolventPlan($params);
			 						$this->smarty->assign('tpl','reduction/design/solventPlanEdit.tpl');
			 						$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), (($result['noOutputs'] !== true)?array('bookmark'=>'solventplan','tab'=>'month'):array('bookmark'=>'department')));

							if ($result === true) {
								 //	redirect
								header("Location: ?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=solventplan&tab=month&notify=16");
								die();
							}
							foreach($result as $key => $data) {
								$this->smarty->assign($key,$data);
							}
							$this->smarty->assign('notViewChildCategory', true);
		$this->smarty->display("tpls:index.tpl");
	}
	
	/**
     * bookmarkSolventplan($vars)     
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */       
	protected function bookmarkSolventplan($vars)
	{			
		extract($vars);	
		$facility->initializeByID($this->getFromRequest('id'));
									
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
									
		if (!$this->user->checkAccess('reduction', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$mReduction = new $moduleMap['reduction'];		
									
		$periodType = $this->getFromRequest('tab');
		$currentDate = getdate();
		$period = array('year' => (	$this->getFromPost('selectYear')!== null && 
									$this->getFromPost('selectYear') <= $currentDate['year'])?
									$this->getFromPost('selectYear'):(($periodType === 'month')?null:$currentDate['year'])
						);
		
		switch($periodType) 
		{
			case 'month':
				if ($this->getFromPost('selectMonth')!== null) 
				{
					$period['month'] = $this->getFromPost('selectMonth');
				} 
				else 
				{
					$period['month'] = null;
					break;
				}
		
				if($period['year'] == $currentDate['year'] && $period['month'] >= $currentDate['mon']) 
				{
					$period['month'] = $currentDate['mon']-1;
				}
				if ($period['month'] === 0) 
				{
					$period['year'] = $period['year'] - 1;
					$period['month'] = 12;
				}
				break;
			case 'quarter':
				$period['quarter'] = ($this->getFromPost('selectQuarter')!== null)?$this->getFromPost('selectQuarter'):round(($currentDate['mon']/*+1*/)/3,0);
				if($period['year'] == $currentDate['year'] && $period['quarter'] > round(($currentDate['mon'])/3,0)) 
				{
					$period['quarter'] = round(($currentDate['mon'])/3,0);
				}
				break;
			case 'semi-year':
				$period['period'] = ($this->getFromPost('selectSemiyear')!== null)?$this->getFromPost('selectSemiyear'):(($currentDate['mon']>7)?2:1);
				if($period['year'] == $currentDate['year'] && $period['period'] == 2 && $currentDate['mon']<7) 
				{
					$period['period'] = 1;
				}
				break;
		}
									
		$params = array(
							'db' => $this->db,
							'facilityID' => $this->getFromRequest('id'),
							'period' => $period,
							'periodType' => $periodType,
							'companyID' => $facilityDetails['company_id']
						);
		$result = $mReduction->prepareViewSolventPlan($params);
		foreach($result as $key => $data) 
		{
			$this->smarty->assign($key,$data);
		}
		$this->smarty->assign('tpl','reduction/design/solventPlanView.tpl');	//assign('show'
		
	}
}
?>