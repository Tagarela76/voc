<?php
class CReduction extends Controller
{	
	function CReduction($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='reduction';
		$this->parent_category='facility';			
	}		
	
	/**
     * bookmarkReduction($vars)     
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */       
	protected function bookmarkReduction($vars)
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
																		
		$error = false;
		if (($this->getFromPost('saveARE')) && ((!is_numeric(str_replace(',','.',$this->getFromPost('textARE'))) || $this->getFromPost('textARE')<=0) && $type = 'ARE') || 
			($this->getFromPost('saveTargetEmission')) && ((!is_numeric(str_replace(',','.',$this->getFromPost('textTargetEmission')))) || 
			$this->getFromPost('textTargetEmission')<=0) && $type = 'TE') 
		{
			$error = true;
			
			
			
			$this->smarty->assign('error',array(
											'type' => $type, 
											'error' => 'Error! Please enter valid number!', 
											'value' => ($type == 'ARE')?$this->getFromPost('textARE'):$this->getFromPost('textTargetEmission')
										));
			
		}
									
		if (!$error)
		{
			if ($this->getFromPost('saveARE')) 
			{
				$params = array(
								'db' => $this->db,
								'facilityID' => $this->getFromRequest('id'),
								'AREfactor' => str_replace(',','.',$this->getFromPost('textARE')),
								'TEfactor' => null
								);
				$result = $mReduction->prepareSaveFactors($params);
				$notify = new Notify(null, $this->db);
				$message = $notify->getPopUpNotifyMessage(17);
				$this->smarty->assign("notify",$message);
			} 
			elseif ($this->getFromPost('saveTargetEmission')) 
			{
				$params = array(
								'db' => $this->db,
								'facilityID' => $this->getFromRequest('id'),
								'TEfactor' => str_replace(',','.',$this->getFromPost('textTargetEmission')),
								'AREfactor' => null
								);
				$result = $mReduction->prepareSaveFactors($params);
				$notify = new Notify(null, $this->db);
				$message = $notify->getPopUpNotifyMessage(18);
				$this->smarty->assign("notify",$message);
			}
		}									
		$params = array(
						'db' => $this->db,
						'facilityID' => $this->getFromRequest('id')
						);
		$result = $mReduction->prepareView($params);
									
		foreach($result as $key => $data) 
		{
			$this->smarty->assign($key,$data);
		}
		$this->smarty->assign('tpl','reduction/design/reduction.tpl');		
	}
}
?>