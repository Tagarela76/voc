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
		$supplierIDS = $inventoryManager->getSaleUserSupplierLst($this->user->xnyo->user['user_id']);

		$vars=array	(
						'supplierIDS'		=>$supplierIDS
					);
		
		$emails = $inventoryManager->getSupplierUsersEmails($supplierIDS[0]['supplier_id']);
		foreach ($emails as $email){
			$email['url'] = "?action=editEmail&category=usersSupplier&supplierID={$supplierIDS[0]['supplier_id']}";
			$emailArr [] = $email;
		}
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
		// Check user supplier in GET
		$supplierIDS = $inventoryManager->getSaleUserSupplierLst($this->user->xnyo->user['user_id']);
		foreach($supplierIDS as $sid){
			$supplierIDArray[] = $sid['supplier_id'];
		}
		if (!in_array($supplierID, $supplierIDArray)){
			throw new Exception('404');
		}
		$emails = $inventoryManager->getSupplierUsersEmails($supplierID);
		
			$form = $_POST;

			if (count($form) > 0) {
			
				$data['supplier_id'] = $supplierID;
				$inventoryManager->beforeUpdateSupplierEmails($data);
				$i=1;
				$name = 'email'.$i;
				while(isset($form[$name])){
					$data['email'] = $form[$name];
					$inventoryManager->updateSupplierEmails($data);
					$i++;$name = 'email'.$i;
				}
				header("Location: ?action=browseCategory&category=usersSupplier");

			}
		
		$this->smarty->assign('emails', $emails);	
		$this->smarty->assign('companies', $companyList);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('tpl', 'tpls/userAdd.tpl');
		$this->smarty->display("tpls:index.tpl");
	}	
}
?>