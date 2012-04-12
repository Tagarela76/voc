<?php

class CMSDS_Sheet extends Controller{

    function CMSDS_Sheet($smarty,$xnyo,$db,$user,$action) 
    {
    	parent::Controller($smarty,$xnyo,$db,$user,$action);    			
    }   
	
	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	function actionDeleteItem()
	{
		$req_id=$this->getFromRequest('id');
		if (!is_array($req_id))
			$req_id=array($req_id);
			
		$request=$this->getFromRequest();
		$msds = new MSDS($this->db);
		$msdsSheet = $msds->getSheetByProduct($req_id[0]);
							
		$itemForDelete[0]["id"]		=	$msdsSheet['id'];
		$itemForDelete[0]["name"]	=	$msdsSheet['name'];
		$this->smarty->assign("cancelUrl", "?action=browseCategory&category=department&id=".$request['departmentID']."&bookmark=product");
							
		if (!$this->user->checkAccess('department', $request['departmentID'])) 
		{						
			throw new Exception('deny');
		}
							
		$this->setListCategoriesLeftNew('department', $request['departmentID']);
		$this->setNavigationUpNew('department', $request['departmentID']);
		$this->setPermissionsNew('viewData');		
							
		$this->smarty->assign('departmentID', $request['departmentID']);
		$this->finalDeleteItemCommon($itemForDelete,$linkedNotify,$count,$info);						
	}
	
	private function actionConfirmDelete()
	{			
		$msds = new MSDS($this->db);							
		$msdsSheet = $msds->getSheetDetails($this->itemID[0]);
		$msds->unlinkMsdsSheet($this->itemID[0]);
								
		$itemForDeleteName[0] = $msdsSheet['name'];		
		if ($this->successDeleteInventories)											
			header("Location: ?action=browseCategory&category=department&id=".$this->getFromPost('departmentID')."&bookmark=product");	
	}
}
?>