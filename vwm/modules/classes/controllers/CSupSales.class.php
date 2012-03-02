<?php
class CSupSales extends Controller
{
	function CSupSales($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='sales';
		$this->parent_category='root';
	}
	
	function runAction()
	{
		$this->runCommon('supplier');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory(){

		$request = $this->getFromRequest();
		$bookmark = $this->getFromRequest('bookmark');
		$urlRoot = "?action=browseCategory&category=root";
		$this->smarty->assign('urlRoot', $urlRoot);	
		
		$this->smarty->assign('request', $request);
		$inventoryManager = new InventoryManager($this->db);
		$supplierIDS = $inventoryManager->getSaleUserSupplierLst($this->user->xnyo->user['user_id']);

		$vars=array	('jobberID' => $request['jobberID']);
		$jsSources = array('modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign("parent",$this->category);
		$this->smarty->assign('supplierID', $supplierIDS[0]['supplier_id']);
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'supplier');
		$this->smarty->display("tpls:index.tpl");		
	}
	
	private function actionAddItem(){
		//	Access control
		if (!$this->user->checkAccess('root', null)) {
			throw new Exception('deny');
		}	
		$request = $this->getFromRequest();
		$jobberManager = new JobberManager($this->db);
		
		$form = $_POST;
		if (count($form) > 0) {
	
			//	"Init state" dances
			$registration = new Registration($this->db);
			if($registration->isOwnState($form["country"]))
			{
				$state = $form["selectState"];
			}
			else
			{
				$state = $form["textState"];
			}

			$jobberData = array(
				"name"				=>	$form["name"],
				"address"			=>	$form["address"],
				"city"				=>	$form["city"],
				"zip"				=>	$form["zip"],
				"county"			=>	$form["county"],
				"state"				=>	$state,
				"country"			=>	$form["country"],
				"phone"				=>	$form["phone"],
				"fax"				=>	$form["fax"],
				"email"				=>	$form["email"],
				"contact"			=>	$form["contact"],
				"title"				=>	$form["title"],
				"creater_id"		=>	$_SESSION['user_id'],
				"supplier"			=>	$form["supplier"]

			);

			$validation = new Validation($this->db);
			$validStatus = $validation->validateRegData($jobberData);
			//	check for duplicate names
			if (!($validation->isUniqueName("jobber", $jobberData["name"]))) {
				$validStatus['summary'] = 'false';
				$validStatus['name'] = 'alredyExist';
			}

			if ($validStatus['summary'] == 'true') {
				$jobber = new Jobber($this->db,$jobberData);
				//	setter injection
				$jobber->setTrashRecord(new Trash($this->db));

				$jobberID = $jobber->save();
				$result = $jobberManager->updateJobberSuppliers($jobberID,$jobberData['supplier']);

				//	redirect
				if ($result)
				header("Location: ?action=browseCategory&category=root");
				die();

			}else {
				//	Errors on validation of adding for a new company
				/* old school style */
				//$notify = new Notify($this->smarty);
				//$notify->formErrors();

				/*	the modern style */
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);

				$this->smarty->assign('validStatus', $validStatus);

			}
		}	
		
		//	IF ERRORS OR NO POST REQUEST

		
		$suppl = new BookmarksManager($this->db);
		$supplierList = $suppl->getOriginSupplier();
        //var_dump($supplierList);
		$this->smarty->assign("supplier",$supplierList);	
		
		$registration = new Registration($this->db);
		$countries = $registration->getCountryList();
		$country = new Country($this->db);

		switch (REGION)
		{
			case 'eu_uk':
			{
				$usaID = $country->getCountryIDByName('United Kingdom');
				break;
			}
			default:
			{
				$usaID = $country->getCountryIDByName('USA');
				break;
			}
		}


		$jobberData['country'] = $jobberData['country'] ? $jobberData['country'] : $usaID;
		//$this->smarty->assign("data", $data);
		$this->smarty->assign('data', $jobberData);
		$state = new State($this->db);
		$stateList = $state->getStateList($jobberData['country']);
		if(empty($stateList)){
			$this->smarty->assign("selectMode", false);
		}
		else{
			$this->smarty->assign("state", $stateList);
			$this->smarty->assign("selectMode", true);
		}

		$this->smarty->assign("country", $countries);
		$this->smarty->assign("state", $stateList);

		//	set permissions

		$this->setListCategoriesLeftNew($this->category, $this->getFromRequest('id'));
		$this->setNavigationUpNew ('root', $this->getFromRequest('id'));
		$this->setPermissionsNew('viewRoot');
		
		//	set js scripts
		$jsSources = array(
			'modules/js/form.js',
			'modules/js/reg_country_state.js',
			'modules/js/PopupWindow.js',
			'modules/js/checkBoxes.js',
			'modules/js/addJobberPopups.js'
		);
		$this->smarty->assign('jobberList', $jobberList);
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('sendFormAction', '?action=addItem&category='.$this->category);
		$this->smarty->assign('tpl','tpls/addJobber.tpl');
		$this->smarty->display("tpls:index.tpl");		
	}
	
	private function actionDeleteItem(){
	
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);

		$jobberManager = new JobberManager($this->db);
		if (!is_null($this->getFromRequest('id'))) {
		foreach ($req_id as $jobberID){
			//	Access control
				if (!$this->user->checkAccess($this->getFromRequest('category'), $jobberID))
				{
					//throw new Exception('deny');
				}

				$jobberDetails = $jobberManager->getJobberDetails($jobberID,true);
				
				if ($jobberDetails['jobber_id'] == null) {
					throw new Exception('404');
				}

				$delete["id"]		=	$jobberDetails["jobber_id"];
				$delete["name"]		=	$jobberDetails["name"];
				$delete["address"]	=	$jobberDetails["address"];
				$delete["phone"]	=	$jobberDetails["phone"];
				$delete["contact"]	=	$jobberDetails["contact"];
				$itemForDelete[] 	=	$delete;
			}
		}
		
		$form = $_POST;
		if (count($form) > 0) {

			for ($i = 0; $i < $form['itemsCount'];$i++){
				if ($form['item_'.$i]){
					$jobberManager->deleteJobber($form['item_'.$i]);
				}
				
			}
			
				header("Location: ?action=browseCategory&category=root");
				die();			

		}		
		$this->setListCategoriesLeftNew('root', '');
		$this->setNavigationUpNew ('root', '');
		$this->setPermissionsNew('viewCompany');
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=root");
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);
	}	

}
?>