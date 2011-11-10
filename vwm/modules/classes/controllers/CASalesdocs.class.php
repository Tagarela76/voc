<?php
class CASalesdocs extends Controller {
	
	function CASalesdocs($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='salesdocs';
		$this->parent_category='salesdocs';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);	
			
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$ms = new ModuleSystem($this->db);
		$moduleMap = $ms->getModulesMap();
		$mDocs = new $moduleMap['docs'];

		$params = array(
			'db' => $this->db,
			'facilityID' => '95'
		);
		$result = $mDocs->prepareView($params);

		foreach($result as $key => $data) {
			$this->smarty->assign($key,$data);
		}
		$this->smarty->assign('tpl', 'tpls/salesdocs.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
}