<?php

class CAEmissionFactor extends Controller {
	
	function CAEmissionFactor($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='apmethod';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	protected function bookmarkEmissionFactor($vars) {
		extract($vars);
		$ms = new ModuleSystem($this->db);
		$map = $ms->getModulesMap();
		if (class_exists($map['carbon_footprint'])) {
			$mCarbonFootprint = new $map['carbon_footprint'];
			$result = $mCarbonFootprint->prepareAdminView(array('db' => $this->db));
			foreach($result as $key => $value) {
				$this->smarty->assign($key,$value);
			}
		}
		$this->smarty->assign("unittype", new Unittype($this->db));
		$this->smarty->assign("url", 'admin.php?action=viewDetails&category=emissionFactor&id=');	//	id will be set in tpl
	}
	
	private function actionViewDetails() {
		$ms = new ModuleSystem($this->db);
		$map = $ms->getModulesMap();
		
		if (class_exists($map['carbon_footprint'])) {
			$mCarbonFootprint = new $map['carbon_footprint'];
			$emissionFactor = $mCarbonFootprint->getNewEmissionFactorObject($this->db, $this->getFromRequest('id'));
			
			$this->smarty->assign("emissionFactor", $emissionFactor);
		}
		$this->smarty->assign("unittype", new Unittype($this->db));
		$this->smarty->assign('tpl', 'carbon_footprint/design/viewEmissionFactor.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionEdit() {
		$ms = new ModuleSystem($this->db);
		$map = $ms->getModulesMap();
		if (class_exists($map['carbon_footprint'])) {
			$mCarbonFootprint = new $map['carbon_footprint'];
			$params = array(
				'db' => $this->db,
				'id' => $this->getFromRequest('id')
			);
			$result = $mCarbonFootprint->prepareAdminEdit($params);
			foreach($result as $key => $value) {
				$this->smarty->assign($key, $value);
			}
			if ($result['validStatus']['summary'] == 'false') {
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			}
		}
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionAddItem() {
		$ms = new ModuleSystem($this->db);
		$map = $ms->getModulesMap();
		if (class_exists($map['carbon_footprint'])) {
			$mCarbonFootprint = new $map['carbon_footprint'];
			$params = array(
				'db' => $this->db,
				'id' => null, //because it's add item and we dont know id
			);
			$result = $mCarbonFootprint->prepareAdminEdit($params);
			foreach($result as $key => $value) {
				$this->smarty->assign($key, $value);
			}
			if ($result['validStatus']['summary'] == 'false') {
				//$notify=new Notify($smarty);
				//$notify->formErrors();
				$title=new Titles($this->smarty);
				$title->titleEditItemAdmin($this->getFromRequest('category'));
			}
		}
		
		$this->smarty->assign('currentOperation', 'addItem');
		$this->smarty->display("tpls:index.tpl");
	}
}
?>