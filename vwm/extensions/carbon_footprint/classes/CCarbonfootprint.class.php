<?php
class CCarbonfootprint extends Controller
{	
	function CCarbonfootprint($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='carbonfootprint';
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
								
		if (!$this->user->checkAccess('carbon_footprint', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mCarbonFootprint = new $moduleMap['carbon_footprint'];

		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromPost('facilityID'),
						'confirmed' => true,
						'idArray' => $this->itemID
						);
		$mCarbonFootprint->prepareDelete($params);		
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromPost('facilityID')."&bookmark=carbonfootprint&tab=month&notify=22");	
	}
	
	private function actionDeleteItem()
	{
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		//delete of direct carbon footprint!!
		$facility->initializeByID($this->getFromRequest('facilityID'));
							
		if (!$this->user->checkAccess('carbon_footprint', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mCarbonFootprint = new $moduleMap['carbon_footprint'];
														
		$idArray = $this->getFromPost('checkCarbonFootprint');
	
		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('facilityID'),
						'confirmed' => /*(isset($_POST['confirm']))?true:*/false,
						'idArray' => $idArray
						);
	 	extract($mCarbonFootprint->prepareDelete($params));

		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'carbonfootprint','tab'=>'month'));
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$this->setPermissionsNew('facility');
							
		$this->smarty->assign('facilityID', $this->getFromRequest('facilityID'));
		$this->smarty->assign('cancelUrl', "?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=carbonfootprint&tab=month");
		$this->smarty->assign('notViewChildCategory', true);
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}
	
	private function actionAddItem() 
	{
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		//add of direct carbon footprint!!
		$facility->initializeByID($this->getFromRequest('facilityID'));
							
		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'carbonfootprint','tab'=>'month'));
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$this->setPermissionsNew('viewFacility');
							
		//voc indicator
		$this->setIndicator($this->smarty, $facility->getDailyLimit(), $facility->getCurrentUsage());
							
		if (!$this->user->checkAccess('carbon_footprint', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mCarbonFootprint = new $moduleMap['carbon_footprint'];
	
		$params = array(
								'db' => $this->db,
								'facilityID' => $this->getFromRequest('facilityID'),
								'save' => (!is_null($this->getFromPost('save')))?true:false,
								'emission_factor_id' => $this->getFromPost('fuel'),
								'description' => $this->getFromPost('description'),
								'quantity' =>  str_replace(',','.',$this->getFromPost('quantity')),
								'adjustment' =>  str_replace(',','.',$this->getFromPost('adjustment')),
								'unittype_id' => $this->getFromPost('unittype'),
								'request' =>  $this->getFromRequest(),
								'month' =>  $this->getFromPost('selectMonth'),
								'year' => $this->getFromPost('selectYear')
						);
	 	$result = $mCarbonFootprint->prepareAddEditDirect($params);
		if ($result === true) {
			//	redirect
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=carbonfootprint&tab=month&notify=19");
			die();
		}
		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		$this->smarty->assign('notViewChildCategory', true);
		$this->smarty->assign('tpl','carbon_footprint/design/addDirectEmission.tpl');
		$this->smarty->display("tpls:index.tpl");
	}	
	
	private function actionEdit() {
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		//edit of direct carbon footprint!!
		$facility->initializeByID($this->getFromRequest('facilityID'));
		
		$this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), array('bookmark'=>'carbonfootprint', 'tab'=>'month'));
		$this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
		$this->setPermissionsNew('viewFacility');
		
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
		
		if (!$this->user->checkAccess('carbon_footprint', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mCarbonFootprint = new $moduleMap['carbon_footprint'];
		
		switch($this->getFromRequest('tab')) {
			case 'indirect':
				$params = array(
					'db' => $this->db,
					'facilityID' => $this->getFromRequest('facilityID'),
					'save' => (!is_null($this->getFromPost('save')))?true:false,
					'certificate_value' => str_replace(',','.',$this->getFromPost('certificate_value')),
					'credit_value' => str_replace(',','.',$this->getFromPost('credit_value')),
					'quantity' =>  str_replace(',','.',$this->getFromPost('quantity')),
					'adjustment' =>  str_replace(',','.',$this->getFromPost('adjustment')),
					'request' =>  $this->getFromRequest(),
					'month' =>  $this->getFromPost('selectMonth'),
					'year' => $this->getFromPost('selectYear')
				);
				$result = $mCarbonFootprint->prepareEditIndirect($params);
				$this->smarty->assign('tpl','carbon_footprint/design/editIndirectEmission.tpl');
				if($result){
					$notifyID = 21;
				}
				break;
			case 'direct':
				$params = array(
					'db' => $this->db,
					'facilityID' => $this->getFromRequest('facilityID'),
					'save' => (!is_null($this->getFromPost('save')))?true:false,
					'emission_factor_id' => $this->getFromPost('emission_factor_id'),										
					'description' => $this->getFromPost('description'),
					'unittype_id' => $this->getFromPost('unittype'),
					'quantity' =>  str_replace(',','.',$this->getFromPost('quantity')),
					'adjustment' =>  str_replace(',','.',$this->getFromPost('adjustment')),
					'request' =>  $this->getFromRequest(),
					'month' =>  $this->getFromPost('selectMonth'),
					'year' => $this->getFromPost('selectYear')
				);
				$result = $mCarbonFootprint->prepareAddEditDirect($params);
				if($result){
					$notifyID = 20;
				}
				
				$this->smarty->assign('tpl','carbon_footprint/design/addDirectEmission.tpl');
				break;
			default:
				throw new Exception('404');
			break;
		}
		if ($result === true) {
			//	redirect
			header("Location: ?action=browseCategory&category=facility&id=".$this->getFromRequest('facilityID')."&bookmark=carbonfootprint&tab=month" . ($notifyID ? "&notify=" . $notifyID : ""));
			die();
		}
		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		$this->smarty->assign('notViewChildCategory', true);
		$this->smarty->display("tpls:index.tpl");
	}	
	
	/**
     * bookmarkCarbonfootprint($vars)     
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */       
	protected function bookmarkCarbonfootprint($vars)
	{			
		extract($vars);	
		$facility->initializeByID($this->getFromRequest('id'));
									
		//voc indicator
		$this->setIndicator($facility->getDailyLimit(), $facility->getCurrentUsage());
									
		if (!$this->user->checkAccess('carbon_footprint', $facilityDetails['company_id'])) {
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..
		$mCarbonFootprint = new $moduleMap['carbon_footprint'];
		switch($this->getFromRequest('tab')) 
		{
			case 'month':
			case 'quarter':
			case 'semi-year':
			case 'year':
				$periodType = $this->getFromRequest('tab');

				switch($periodType) 
				{
					case 'month':
						$period = array(
										'month' => ($this->getFromPost('selectMonth')!== null)?$this->getFromPost('selectMonth'):substr(date("m-d-Y", time()),0,2),
										'year' => ($this->getFromPost('selectYear')!== null)?$this->getFromPost('selectYear'):substr(date("Y-m-d", time()),0,4)
										);
						break;
					case 'quarter':
						$period = array(
										'quarter' => ($this->getFromPost('selectQuarter')!== null)?$this->getFromPost('selectQuarter'):round((substr(date("m-d-Y", time()),0,2)+1)/3,0),
										'year' => ($this->getFromPost('selectYear')!==null)?$this->getFromPost('selectYear'):substr(date("Y-m-d", time()),0,4)
										);
						break;
					case 'semi-year':
						$period = array(
										'period' => ($this->getFromPost('selectSemiyear')!== null)?$this->getFromPost('selectSemiyear'):((substr(date("m-d-Y", time()),0,2)>6)?2:1),
										'year' => ($this->getFromPost('selectYear')!== null)?$this->getFromPost('selectYear'):substr(date("Y-m-d", time()),0,4)
										);
						break;
					case 'year':
						$period = array('year' => ($this->getFromPost('selectYear')!== null)?$this->getFromPost('selectYear'):substr(date("Y-m-d", time()),0,4));
						break;
				}
								
				$params = array(
								'db' => $this->db,
								'facilityID' => $this->getFromRequest('id'),
								'period' => $period,
								'periodType' => $periodType
								);
				$result = $mCarbonFootprint->prepareView($params);
											
				foreach($result as $key => $data) 
				{
					$this->smarty->assign($key,$data);												
				}
											
				$this->smarty->assign('tpl','carbon_footprint/design/carbonFootprintView.tpl');
				break;
			case 'setLimit':											
							
				$params = array(
								'db' => $this->db,
								'facilityID' => $this->getFromRequest('id'),
								'save' => (!is_null($this->getFromPost('save')))?true:false,
								'monthlyLimit' => $this->getFromPost('monthLimit'),
								'annualLimit' => $this->getFromPost('annualLimit'),
								'monthlyShow' => (!is_null($this->getFromPost('showMonthly')))?1:0,
								'annualShow' => (!is_null($this->getFromPost('showAnnual')))?1:0
								);
				$result = $mCarbonFootprint->prepareSetLimits($params);
				if ($result === true) 
				{
					//	redirect
					header("Location: ?action=browseCategory&category=facility&id=".$this->getFromRequest('id')."&bookmark=carbonfootprint&tab=month&notify=23");
					die();
				}
				foreach($result as $key => $data) 
				{
					$this->smarty->assign($key,$data);
				}
				$this->smarty->assign('tpl','carbon_footprint/design/setLimit.tpl');
				break;
			default:
				throw new Exception('404');
				break;
		}
		$this->smarty->assign('notViewChildCategory', true);	
	}
}
?>