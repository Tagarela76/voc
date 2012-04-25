<?php

class CPfpLibrary extends Controller {

	public function __contstruct($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'pfpLibrary';
		$this->parent_category = 'department';
	}
	
	
	protected function bookmarkDPfpLibrary($vars) {
		extract($vars);		
	
		$manager = new PFPManager($this->db);		
		
		$pfpCount = ($this->getFromRequest('tab') == 'all') ? $manager->countPFP() 
				: $manager->countPFP($companyDetails['company_id']);
		
		$pagination = new Pagination((int)$pfpCount);
		$pagination->url = "?action=browseCategory&category=department&id=".$this->getFromRequest('id')
				."&bookmark=".$this->getFromRequest('bookmark')
				."&tab=".$this->getFromRequest('tab');
		
		$pfps = ($this->getFromRequest('tab') == 'all') ? $manager->getList(null, $pagination) 
				: $manager->getList($companyDetails['company_id'], $pagination);
		$jsSources = array  ('modules/js/checkBoxes.js',
                                     'modules/js/autocomplete/jquery.autocomplete.js');
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('pagination', $pagination);
		$this->smarty->assign('pfps', $pfps);
		$this->smarty->assign('childCategoryItems', $pfps);
		$this->smarty->assign('tpl', 'tpls/pfpMixList.tpl');
	}
}
