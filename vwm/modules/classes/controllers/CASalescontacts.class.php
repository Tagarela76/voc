<?php
class CASalescontacts extends Controller {
	
	function CASalescontacts($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='salescontacts';
		$this->parent_category='salescontacts';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);	
			
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$bookmark=$this->getFromRequest('bookmark');
		
		/**
		 * Потом всунуть сюда фильтр
		 */
		
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');		
		$this->smarty->display("tpls:index.tpl");
	}
}