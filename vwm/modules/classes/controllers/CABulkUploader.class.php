<?php

class CABulkUploader extends Controller {
	
	function CABulkUploader($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='bulkUploader';
		$this->parent_category='bulkUploader';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionBrowseCategory() {
		$title = new Titles($this->smarty);
		$title->titleBulkUploaderSettings();
		
		$company = new Company($this->db);
		$companyList = $company->getCompanyList();
		$companyList[] = array('id' => 0, 'name' => 'no company');
		$this->smarty->assign('companyList',$companyList);
		$this->smarty->assign('currentCompany',0);
		
		$this->smarty->assign('doNotShowControls',true);
		//	TODO: internal js script left there
		//$smarty->display("tpls:bulkUploader.tpl");
		$jsSources = array("modules/js/checkBoxes.js",
		"modules/js/reg_country_state.js");
		
		$this->smarty->assign('jsSources', $jsSources);
		$this->smarty->assign('tpl', 'tpls/bulkUploader.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionUpload() {
		$input = array (
			"maxNumber" => $this->getFromPost('maxNumber'),
			"threshold" => $this->getFromPost('threshold'),
			"update"	=> $this->getFromPost('update'),
			"companyID"	=> $this->getFromPost('companyID')
		);
		//we should check input file!
		if ($input['size']<1024000) {
			$input['inputFile'] = $_FILES['inputFile']['tmp_name'];
			$input['realFileName'] = basename($_FILES['inputFile']['name']);								
			$bu = new bulkUploader($this->db,$input);
			
			$errorCnt = count($bu->productsError);
			$correctCnt = count($bu->productsCorrect);					
			$total =  $errorCnt + $correctCnt;										
			
			$title = new Titles($this->smarty);
			$title->titleBulkUploadResults();
			
			$this->smarty->assign("categoryID","tab_".$this->getFromPost('categoryID'));
			$this->smarty->assign("productsError",$bu->productsError);
			$this->smarty->assign("errorCnt",$errorCnt);
			$this->smarty->assign("correctCnt",$correctCnt);
			$this->smarty->assign("total",$total);
			$this->smarty->assign("input",$input);
			$this->smarty->assign("insertedCnt",$bu->insertedCnt);
			$this->smarty->assign("updatedCnt",$bu->updatedCnt);
			$this->smarty->assign("validationResult",$bu->validationResult);
			$this->smarty->assign("actions",$bu->actions);
			$this->smarty->assign("parent", $this->parent_category);
			
			
			//$smarty->display('tpls:bulkUploader.tpl');
			$jsSources = array("modules/js/checkBoxes.js",
			"modules/js/reg_country_state.js");
			
			$this->smarty->assign('jsSources', $jsSources);
			$this->smarty->assign('tpl', "tpls/uploadResults.tpl");
			$this->smarty->display("tpls:index.tpl");
		}
	}
}
?>