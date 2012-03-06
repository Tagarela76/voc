<?php
class CSupUsersSupplier extends Controller
{
	function CSupUsersSupplier($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='users';
		$this->parent_category='users';
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
		
		$jsSources = array();
		array_push($jsSources, 'modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		
		$this->smarty->assign('request', $request);
		$inventoryManager = new InventoryManager($this->db);
		if (!$request['jobberID']){
			
			$jobberID = $inventoryManager->getSaleUserJobberID($this->user->xnyo->user['user_id']);
					
			$jobberID = $jobberID['jobber_id'];
		}else{
			$jobberID = $request['jobberID'];
		}
		$supplierIDS = $inventoryManager->getSuppliersByJobberID($jobberID);
		
		$emails = $inventoryManager->getSupplierUsersEmails($jobberID);
		foreach ($emails as $email){
			$email['url'] = "?action=editEmail&category=usersSupplier&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}";
			$emailArr [] = $email;
		}
		$jobberDetails = $inventoryManager->getJobberDetails($request['jobberID']);

		$this->smarty->assign("jobberDetails", $jobberDetails);		
		//$this->setListCategoriesLeftNew('sales', $this->getFromRequest('jobberID'));	
		
		$this->smarty->assign("emails",$emailArr);
		$this->smarty->assign("parent",$this->category);
		$this->smarty->assign('supplierID', $supplierIDS[0]['supplier_id']);
		$this->smarty->assign('tpl', 'tpls/bookmarkUsers.tpl');
		$this->smarty->display("tpls:index.tpl");		
	}
	
	private function actionAddItem() {
		$inventoryManager = new InventoryManager($this->db);		
		
		

		$request = $this->getFromRequest();
		$supplierID = $request['supplierID'];
		if (!$request['jobberID']){
			
			$jobberID = $inventoryManager->getSaleUserJobberID($this->user->xnyo->user['user_id']);
					
			$jobberID = $jobberID['jobber_id'];
		}else{
			$jobberID = $request['jobberID'];
		}		
		// Check user supplier in GET
		$supplierIDS = $inventoryManager->getSuppliersByJobberID($jobberID);
		foreach($supplierIDS as $sid){
			$supplierIDArray[] = $sid['supplier_id'];
		}
		if (!in_array($supplierID, $supplierIDArray)){
			throw new Exception('404');
		}
		$emails = $inventoryManager->getSupplierUsersEmails($request['jobberID']);
		
			$form = $_POST;

			if (count($form) > 0) {
	
				$data['supplier_id'] = $supplierID;
				$data['jobber_id'] = $request['jobberID'];
			
				$inventoryManager->beforeUpdateSupplierEmails($data);
				$i=1;
				$name = 'email'.$i;
				while(isset($form[$name])){
					$data['email'] = $form[$name];
							
					$inventoryManager->updateSupplierEmails($data);
					$i++;$name = 'email'.$i;
				}
				header("Location: ?action=browseCategory&category=usersSupplier&jobberID={$request['jobberID']}&supplierID={$request['supplierID']}");

			}
		
		$this->smarty->assign('emails', $emails);	
		$this->smarty->assign('companies', $companyList);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('tpl', 'tpls/userAdd.tpl');
		$this->smarty->display("tpls:index.tpl");
	}	
}
?>