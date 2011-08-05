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
                $manager = new BookmarksManager($this->db);
                $bookmarksList = $manager->getBookmarksList();
                $totalCount = $manager->getCount();
                $this->smarty->assign("bookmarks",$bookmarksList);		
		$this->smarty->assign("itemsCount",$totalCount);		
		$this->smarty->assign('tpl', 'tpls/bookmarkSales.tpl');
		/**
		 * Потом всунуть сюда фильтр
		 */
		
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');		
		$this->smarty->display("tpls:index.tpl");
	}
}