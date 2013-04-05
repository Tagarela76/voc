<?php

class CAComponents extends Controller {
	
	function CAComponents($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='components';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkComponents($vars) {
		extract($vars);
		$components=new Component($this->db);
		
		$pagination = new Pagination($components->queryTotalCount($filterStr));
		$pagination->url = "?action=browseCategory&category=tables&bookmark=components".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 
		
		if (is_null($sortStr)) {
			$sortStr=" ORDER BY description ";
		}
		$componentsList = $components->getComponentList($pagination,$filterStr,$sortStr);
		
		$field = 'component_id';
		$list = $componentsList;
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=components&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/componentsClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
        $vocPmList = array('VOC', 'PM');
		$components=new Component($this->db);
		$componentsDetails=$components->getComponentDetails($this->getFromRequest('id'));
        
        $this->smarty->assign('vocPm',$vocPmList[$componentsDetails['VOC_PM']]);
		$this->smarty->assign("components",$componentsDetails);
		$this->smarty->assign('tpl', 'tpls/viewComponents.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$id = $this->getFromRequest('id');
		$components=new Component($this->db);
        $vocPmList = array('VOC', 'PM');
        
		if ($this->getFromPost('save')=='Save')
		{	
			$regData=array(
				"component_id"	=>	$id,
				"description"	=>	$this->getFromPost("description"),
				"EINECS"	=>	$this->getFromPost("EINECS"),
				"cas"	=>	$this->getFromPost("cas"),
                'vocPm' => $this->getFromPost("vocPm")
			);
			
			
			$agency=new Agency($this->db);
			$agencyCount=$agency->getAgencyCount();									
			$regData['agencies']=$components->getComponentAgencies($regData['component_id']);
			
			for ($i=0; $i < $agencyCount; $i++) {
				if (!is_null($this->getFromPost('agency_'.$i))) {
					$regData['agencies'][$i]['control']='yes';
				} else {
					$regData['agencies'][$i]['control']='no';
				}
				
			}
			
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($regData);
			
			/*if (!($validate->isUniqueName("component", $regData['cas'], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus['cas'] = 'alredyExist';
			}*/
			
			if ($validStatus["summary"] == "true") {
				$components->setComponentDetails($regData);
				
				header ('Location: admin.php?action=viewDetails&category=components&id='.$id);
				die();									
			}
			else
			{
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			} 
			$data = $regData;
		}
		else 
		{									
			$data=$components->getComponentDetails($id, true);								
		}									
		
		$this->smarty->assign('tpl','tpls/addComponentsClass.tpl');
        $this->smarty->assign('vocPmList',$vocPmList);
		$this->smarty->assign('data', $data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$data=array(
				"cas"	=>	$this->getFromPost("cas"),
				"EINECS"	=>	$this->getFromPost("EINECS"),
				"description"	=>	$this->getFromPost("description")
			);
						
			$agency=new Agency($this->db);
			$agencyCount=$agency->getAgencyCount();
			$components=new Component($this->db);
			$data['agencies']=$components->getComponentAgencies("");
			
			for ($i=0; $i < $agencyCount; $i++) {
				if (!is_null($this->getFromPost('agency_'.$i))) {
					$data['agencies'][$i]['control']='yes';
				} else {
					$data['agencies'][$i]['control']='no';
				}
				
			}
			
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			
			if (!($validate->isUniqueName("component", $data['cas']))) {
				$validStatus['summary'] = 'false';
				$validStatus['cas'] = 'alredyExist';
			}
			
			if ($validStatus["summary"] == "true") {
				$components->addNewComponent($data);
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=components');
				die();										
			} else {
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			}
		}
		else
		{											
			$agency=new Agency($this->db);
			$agencies=$agency->getAgencyList('id');
			$data['agencies']=$agencies;									
		}									
		$this->smarty->assign("data",$data);
		
		$this->smarty->assign("validStatus",$validStatus);
		$this->smarty->assign('tpl', 'tpls/addComponentsClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$components=new Component($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$componentsDetails=$components->getComponentDetails($this->getFromRequest('item_'.$i));
				$item["id"] = $componentsDetails["component_id"];
				$item["name"] = $componentsDetails["cas"];
				$item["links"] = $components->isInUseList($item["id"]);
				$itemForDelete []= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$components=new Component($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$components->deleteComponent($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>