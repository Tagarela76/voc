<?php
class CSupProfile extends Controller
{
	function CSupProfile($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='profile';
		$this->parent_category='profile';
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
		$error = $request['error'];
		$bookmark = $request['bookmark'];

		
		$inventoryManager = new InventoryManager($this->db);
		if ( $error == null ){
				$form = $_POST;

				if (count($form) > 0) {
				
					$result = $inventoryManager->updateSupplierDetails($this->user->xnyo->user['user_id'],$form['email']);
					
					if ($result){
						
						header("Location: ?action=browseCategory&category=profile");
					}else{
						header("Location: ?action=browseCategory&category=profile&error=false");
					}
				}
		}		

		$supplierIDS = $inventoryManager->getSaleUserJobberID($this->user->xnyo->user['user_id']);
		$vars=array	(
						'supplierIDS'=>$supplierIDS
					);
		$supplierDetails = $inventoryManager->getSupplierEmail($supplierIDS[0]['supplier_id']);
		$jsSources = array();
		array_push($jsSources, 'modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('request', $request);
		$this->smarty->assign("supplier",$supplierDetails);
	    $this->smarty->assign("parent",$this->category);
		$this->smarty->assign('tpl', 'tpls/profile.tpl');
		$this->smarty->display("tpls:index.tpl");	

		
	}

}
?>