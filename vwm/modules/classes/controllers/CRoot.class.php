<?php
class CRoot extends Controller
{
	function CRoot($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='root';				
	}
	
	protected function actionBrowseCategory()
	{
        $industryType = new IndustryType($this->db);
		$productIndustryTypeList = $industryType->getTypesWithSubTypes();
		$this->smarty->assign("productTypeList", $productIndustryTypeList);
        
		$companies = new Company($this->db);
        
        $productCategory = $this->getFromRequest('productCategory');
		$companyList = $companies->getCompanyList($productCategory);
		
		foreach ($companyList as $key=>$company) 
		{
			$url = "?action=browseCategory&category=company&id=".$company['id'];								
			$companyList[$key]['url'] = $url;
		}																		
							
		$this->smarty->assign('childCategoryItems', $companyList);
		$this->smarty->assign('childCategory', 'company');												
							
		//	set js
		$jsSources = array('modules/js/checkBoxes.js');
		$this->smarty->assign('jsSources', $jsSources);
							
		//	set tpl							
		$this->smarty->assign('tpl','tpls/root.tpl');
		$this->smarty->display("tpls:index.tpl");		
	}	
}
?>