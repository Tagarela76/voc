<?php

class CASupplier extends Controller {
	
	function CASupplier($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='supplier';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkSupplier($vars) {
		extract($vars);
		$supplier=new Supplier($this->db);
		
		$pagination = new Pagination($supplier->queryTotalCount($filterStr));
		$pagination->url = "?action=browseCategory&category=tables&bookmark=supplier".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 
		if (is_null($sortStr)) {
			$sortStr=" ORDER BY  supplier";
		}
		$supplierList=$supplier->getSupplierList($pagination,$filterStr,$sortStr);
		
		$field = 'supplier_id';
		$list = $supplierList;
		
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=supplier&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/supplierClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		$supplier=new Supplier($this->db);
		$suppl = new BookmarksManager($this->db);
		$SuppliersByOrigin = $suppl->getAllSuppliersByOrigin($this->getFromRequest('id'));
		$supplierDetails=$supplier->getSupplierDetails($this->getFromRequest('id'));
		$this->smarty->assign('SuppliersByOrigin',$SuppliersByOrigin);
		$this->smarty->assign("supplier",$supplierDetails);
		$this->smarty->assign('tpl', 'tpls/viewSupplier.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$supplier=new Supplier($this->db);
		$suppl = new BookmarksManager($this->db);
		$id = $this->getFromRequest('id');	
		
		$supplierList = $supplier->getSupplierList();
		$SuppliersByOrigin = $suppl->getAllSuppliersByOrigin($this->getFromRequest('id'));
		
		if ($this->getFromPost('save')=='Save')
		{	

			for ($i=0; $i<count($supplierList); $i++){
				if (!is_null($this->getFromPost('supplier_'.$i))){
					foreach ($supplierList as $item) {
						if ($this->getFromPost('supplier_'.$i) == $item['supplier_id']){
							$sipplierAllList[] = $item;
						}
					}
				}
			}
			
			foreach ($sipplierAllList as $supplierItem) {
				$supplier->assignSup2Sup($id , $supplierItem['supplier_id']);
			}

			
			$data=array(
				"supplier_id"	=>	$id,
				"description"	=>	$this->getFromPost("supplier_desc"),
				"contact"		=>	$this->getFromPost("contact"),
				"phone"			=>	$this->getFromPost("phone"),
				"address"		=>	$this->getFromPost("address"),
				"country_id"	=>  $this->getFromPost("country")
			);
			
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			
			if (!($validate->isUniqueName("supplier", $data["description"], 'none', $id))) {
				$validStatus['summary'] = 'false';
				$validStatus['description'] = 'alredyExist';
			}									
			if ($validStatus["summary"] == "true") {
				$supplier->setSupplierDetails($data);
				header ('Location: admin.php?action=viewDetails&category=supplier&id='.$id);
				die();										
			}
			else
			{
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			} 
		}
		else 
		{									
			$data=$supplier->getSupplierDetails($id);
		}								
		$registration = new Registration($this->db);
		$countries = $registration->getCountryList();
		
		
		
		
		
		$jsSources = array (
							'modules/js/PopupWindow.js', 
							'modules/js/checkBoxes.js',
		
							'modules/js/supplierPopup.js');
		$this->smarty->assign('SuppliersByOrigin',$SuppliersByOrigin);
		$this->smarty->assign('supplierList',$supplierList);	
        $this->smarty->assign('jsSources',$jsSources);		
		$this->smarty->assign("country",$countries);
		$this->smarty->assign('tpl','tpls/addSupplierClass.tpl');
		$this->smarty->assign('data', $data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$supplierData=array(
				"description"	=>	$this->getFromPost('supplier_desc'),
				"contact"	=>	$this->getFromPost('contact'),
				"phone"	=>	$this->getFromPost('phone'),
				"address"	=>	$this->getFromPost('address'),
				"country_id"		=>  $this->getFromPost('country')
			);
			
			$validation=new Validation($this->db);
			$validStatus=$validation->validateRegDataAdminClasses($supplierData);
			if (!($validation->isUniqueName("supplier", $supplierData["description"]))) {
				$validStatus['summary'] = 'false';
				$validStatus['description'] = 'alredyExist';
			}
			
			if ($validStatus['summary'] == 'true') {
				$supplier=new Supplier($this->db);
				$supplier->addNewSupplier($supplierData);
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=supplier');
				die();									
			} 
			else 
			{
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleAddItemAdmin($this->getFromRequest('category'));
			}
		}
		
		$registration = new Registration($this->db);
		$countries = $registration->getCountryList();
		
		$this->smarty->assign("country",$countries);
		$this->smarty->assign("data",$supplierData);
		$this->smarty->assign("validStatus",$validStatus);
		$this->smarty->assign('tpl', 'tpls/addSupplierClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$supplier=new Supplier($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$supplierDetails	= $supplier->getSupplierDetails($this->getFromRequest('item_'.$i));
				$item["id"]		= $supplierDetails["supplier_id"];
				$item["name"]	= $supplierDetails["supplier_desc"];
				$itemForDelete []	= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$supplier=new Supplier($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$supplier->deleteSupplier($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>