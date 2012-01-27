<?php
class CSSalescontacts extends Controller {
	
	function CSSalescontacts($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='salescontacts';
		$this->parent_category='salescontacts';		
	}
	
	function runAction() {
		$this->runCommon('sales');
		$functionName='action'.ucfirst($this->action);	
			
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$bookmark=$this->getFromRequest('bookmark');
		
                $manager = new BookmarksManager($this->db);
                $bookmarksList = $manager->getBookmarksList($this->user->xnyo->user['user_id']);          
                $totalCount = $manager->getCount();

        $this->smarty->assign("bookmarks",$bookmarksList);		
		$this->smarty->assign("itemsCount",$totalCount);		
		$this->smarty->assign('tpl', 'tpls/bookmarkSales.tpl');
		
		$vars = array('bookmarksList'=>$bookmarksList);
		/**
		 * Потом всунуть сюда фильтр
		 */
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'sales');		
		$this->smarty->display("tpls:index.tpl");
	}
}