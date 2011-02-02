<?php

class CATables extends Controller {
	
	function CATables($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='tables';
		$this->parent_category='tables';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$bookmark=$this->getFromRequest('bookmark');
		
		/*FILTER*/									
		$filter=new Filter($this->db,$bookmark);	
		
		$this->smarty->assign('filterArray',$filter->getJsonFilterArray());
		$filterData= array
			(
				'filterField'=>$this->getFromRequest('filterField'),
				'filterCondition'=>$this->getFromRequest('filterCondition'),
				'filterValue'=>$this->getFromRequest('filterValue')
			);
		
		if ($this->getFromRequest('searchAction')=='filter') {
			$this->smarty->assign('filterData',$filterData);
			$this->smarty->assign('searchAction','filter');										
		}
		$filterStr = $filter->getSubQuery($filterData);
		/*/FILTER*/
		
		/*SORT*/
		if (!is_null($this->getFromRequest('sort')))
		{
			$sort= new Sort($this->db,$bookmark,0);
			$sortStr = $sort->getSubQuerySort($this->getFromRequest('sort'));										
			$this->smarty->assign('sort',$this->getFromRequest('sort'));
		}
		else									
			$this->smarty->assign('sort',0);
		
		if (!is_null($this->getFromRequest('searchAction')))									
			$this->smarty->assign('searchAction',$this->getFromRequest('searchAction'));
		/*/SORT*/
		
		$vars = array(
				'sortStr' => $sortStr,
				'filterStr' => $filterStr,
				'filterData' => $filterData
			);
		
		//	add checkboxes js
		$jsSources = array();			
		array_push($jsSources, 'modules/js/autocomplete/jquery.autocomplete.js');								
		array_push($jsSources, 'modules/js/checkBoxes.js');	
		$this->smarty->assign('jsSources', $jsSources);
		//$this->smarty->assign("categoryID","tab_class");//destroy it!
		//$this->smarty->assign("bookmarkType",$bookmark);
		//$this->smarty->assign("categoryType","");//destroy it!
		$this->forvard($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');		
		$this->smarty->display("tpls:index.tpl");
	}
}
?>