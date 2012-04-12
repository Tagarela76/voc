<?php

class CARule extends Controller {
	
	function CARule($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='rule';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkRule($vars) {
		extract($vars);
		$rule=new Rule($this->db);
		
		$pagination = new Pagination($rule->queryTotalCount($filterStr));
		$pagination->url = "?action=browseCategory&category=tables&bookmark=rule".
			(isset($filterData['filterField'])?"&filterField=".$filterData['filterField']:"").
			(isset($filterData['filterCondition'])?"&filterCondition=".$filterData['filterCondition']:"").
			(isset($filterData['filterValue'])?"&filterValue=".$filterData['filterValue']:"").
			(isset($filterData['filterField'])?"&searchAction=filter":""); 	
		if (is_null($sortStr)) {
			$sortStr=" ORDER BY rule_desc ";
		}					
		$ruleList=$rule->getRuleList($pagination,$filterStr,$sortStr);
		
		$field = 'rule_id';
		$list = $ruleList;
		$itemsCount = ($list) ? count($list) : 0;
		for ($i=0; $i<$itemsCount; $i++) {
			$url="admin.php?action=viewDetails&category=rule&id=".$list[$i][$field];
			$list[$i]['url']=$url;
		}
		$this->smarty->assign("category",$list);
		$this->smarty->assign("itemsCount",$itemsCount);
		
		$this->smarty->assign('tpl', 'tpls/ruleClass.tpl');
		$this->smarty->assign('pagination', $pagination);
	}
	
	private function actionViewDetails() {
		$rule=new Rule($this->db);
		$ruleDetails=$rule->getRuleDetails($this->getFromRequest('id'));
		$this->smarty->assign("rule",$ruleDetails);
		$this->smarty->assign('tpl', 'tpls/viewRule.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$rule=new Rule($this->db);
		$id = $this->getFromRequest('id');
		if ($this->getFromPost('save')=='Save')
		{									
			$data=array(
				"rule_id"	=>	$id,
				"rule_nr"	=>	$this->getFromPost($rule->ruleNrMap[$rule->getRegion()]),
				"rule_nr_us"	=>	$this->getFromPost("rule_nr_us"),
				"rule_nr_eu"	=>	$this->getFromPost("rule_nr_eu"),
				"rule_nr_cn"	=>	$this->getFromPost("rule_nr_cn"),
				"rule_desc"	=>	$this->getFromPost("rule_desc"),
				"country"	=>	$this->getFromPost("country"),
				"county"	=>	$this->getFromPost("county"),
				"city"	=>	$this->getFromPost("city"),
				"zip"	=>	$this->getFromPost("zip")
			);									
			
			$registration=new Registration($this->db);
			if ($registration->isOwnState($data["country"])) {
				$data["state"] = $this->getFromPost("selectState");
			} else {
				$data["state"] = $this->getFromPost("textState");
			}
			$validate=new Validation($this->db);
			$validStatus=$validate->validateRegDataAdminClasses($data);
			
			$checkUnique = $validate->isUniqueRule($data);
			if ($checkUnique !== true) {
				$validStatus['summary'] = 'false';
				foreach ($checkUnique as $ruleNR=>$value) {
					$validStatus[$ruleNR] = 'alredyExist';	
				}
				
			}	
			
			if ($validStatus["summary"] == "true") {
				$rule->setRuleDetails($data);
				header ('Location: admin.php?action=viewDetails&category=rule&id='.$id);
				die();										
			} 
			else
			{
				if ($validStatus["rule_nr"] == 'failed') {											
					$validStatus[$rule->ruleNrMap[$rule->getRegion()]] = 'failed';
				}
				//$notify=new Notify($this->smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			}
		}
		else {									
			$data=$rule->getRuleDetails($id, true);							
		}								
		$country=new Country($this->db);
		$countries=$country->getCountryList();
		$this->smarty->assign("country",$countries);
		$registration=new Registration($this->db);
		if ($registration->isOwnState($data["country"])) {
			$this->smarty->assign("selectMode", true);
			$state=new State($this->db);
			$states=$state->getStateList($data["country"]);
			$this->smarty->assign("state",$states);
		}
		$this->smarty->assign('tpl','tpls/addRuleClass.tpl');
		$this->smarty->assign('data', $data);
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		if ($this->getFromPost('save') == 'Save')
		{
			$rule=new Rule($this->db);
			
			$ruleData=array(										
				"rule_nr"	=>	$this->getFromPost($rule->ruleNrMap[$rule->getRegion()]),
				"rule_nr_us"	=>	$this->getFromPost("rule_nr_us"),
				"rule_nr_eu"	=>	$this->getFromPost("rule_nr_eu"),
				"rule_nr_cn"	=>	$this->getFromPost("rule_nr_cn"),
				"rule_desc"	=>	$this->getFromPost('rule_desc'),
				"country"	=>	$this->getFromPost('country'),
				"county"	=>	$this->getFromPost('county'),
				"city"	=>	$this->getFromPost('city'),
				"zip"	=>	$this->getFromPost('zip')
			);
			
			$registration=new Registration($this->db);
			if ($registration->isOwnState($ruleData['country'])) {
				$ruleData['state']=$this->getFromPost('selectState');
			} else {
				$ruleData['state']=$this->getFromPost('textState');
			}
			$validation=new Validation($this->db);
			$validStatus=$validation->validateRegDataAdminClasses($ruleData);
			
			
			$checkUnique = $validation->isUniqueRule($ruleData);
			if ($checkUnique !== true) {
				$validStatus['summary'] = 'false';
				foreach ($checkUnique as $ruleNR=>$value) {
					$validStatus[$ruleNR] = 'alredyExist';	
				}
				
			}										
			
			if ($validStatus['summary'] == 'true') {										
				$rule->addNewRule($ruleData);
				header ('Location: admin.php?action=browseCategory&category=tables&bookmark=rule');
				die();
				//$notify=new Notify($smarty);
				//$notify->successAddedAdmin($itemID, $ruleData['rule_nr']);
				//showCategory($categoryID, $itemID, $db, $smarty, $xnyo);
				
			} else {
				if ($validStatus["rule_nr"] == 'failed') {											
					$validStatus[$rule->ruleNrMap[$rule->getRegion()]] = 'failed';
				}
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleAddItemAdmin($this->getFromrequest('category'));
			}
		}
		
		$country=new Country($this->db);
		$countries=$country->getCountryList();
		$this->smarty->assign("country",$countries);
		$registration=new Registration($this->db);
		if ($registration->isOwnState($ruleData["country"])) {
			$this->smarty->assign("selectMode", true);
			$state=new State($this->db);
			$states=$state->getStateList($ruleData["country"]);
			$this->smarty->assign("state",$states);
		}
		$this->smarty->assign("data",$ruleData);
		$this->smarty->assign("validStatus",$validStatus);
		$this->smarty->assign('tpl', 'tpls/addRuleClass.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionDeleteItem() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$itemForDelete = array();
		$rule=new Rule($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			if (!is_null($this->getFromRequest('item_'.$i))) {
				$item = array();
				$ruleDetails=$rule->getRuleDetails($this->getFromRequest('item_'.$i));
				$item["id"]		=	$ruleDetails["rule_id"];
				$item["name"]		=	$ruleDetails["rule_nr"];
				$itemForDelete []= $item;
			}
		}
		$this->finalDeleteItemACommon($itemForDelete);
	}
	
	private function actionConfirmDelete() {
		$itemsCount= $this->getFromRequest('itemsCount');
		$rule=new Rule($this->db);
		for ($i=0; $i<$itemsCount; $i++) {
			$id = $this->getFromRequest('item_'.$i);
			$rule->deleteRule($id);
		}
		header ('Location: admin.php?action=browseCategory&category=tables&bookmark='.$this->getFromRequest('category'));
		die();
	}
}
?>