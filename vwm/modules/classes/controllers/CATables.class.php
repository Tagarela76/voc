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
		$this->forward($bookmark,'bookmark'.ucfirst($bookmark),$vars,'admin');
		$this->smarty->display("tpls:index.tpl");
	}
	
	
	private function actionUploadOneMsds() {
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest('productID'));
		if ($productDetails['product_id'] === null) {
			throw new Exception('404');
		}
		//var_dump($productDetails['product_id']);
		//var_dump($_POST); die();
		if ($_POST['fileType'][0] == 'msds'){
		$success = true;
		if (count($_FILES) > 0) {			
			$msds = new MSDS($this->db);
			$msdsUploadResult = $msds->upload('basic');
			if (isset($msdsUploadResult['filesWithError'][0])) {
				$success = false;
				$error = $msdsUploadResult['filesWithError'][0]['error'];
			} else {				
				if ($msdsUploadResult['msdsResult']) {
					$msdsUploadResult['msdsResult'][0]['productID'] = $productDetails['product_id'];
					$input = array(
						'msds' => $msdsUploadResult['msdsResult']
					);					
					$msds->addSheets($input);					
					header('Location: ?action=viewDetails&category=product&id='.$productDetails['product_id']);
				} else {
					$success = false;	
					$error = 'msdsResult is not set';
				}				
			}
						
		}
		} elseif ($_POST['fileType'][0] == 'techsheet') {
		$success = true;
		if (count($_FILES) > 0) {			
			$techSheet = new TechSheet($this->db);
			$techSheetUploadResult = $techSheet->upload('basic');
			//var_dump($techSheetUploadResult);
			if (isset($techSheetUploadResult['filesWithError'][0])) {
				$success = false;
				$error = $techSheetUploadResult['filesWithError'][0]['error'];
			} else {
				if ($techSheetUploadResult['techSheetResult']) {
					$techSheetUploadResult['techSheetResult'][0]['productID'] = $productDetails['product_id'];
					$input = array(
						'techSheets' => $techSheetUploadResult['techSheetResult']
					);	
					//var_dump($input);
					$techSheet->addSheets($input);					
					header('Location: ?action=viewDetails&category=product&id='.$productDetails['product_id']);
				} else {
					$success = false;	
					$error = 'techSheetResult is not set';
				}				
			}
						
		}	
		}
		if (!$success) {
			$this->smarty->assign("error", $error);	
		}
		
		$this->smarty->assign("productDetails",$productDetails);
		$this->smarty->assign("tpl","tpls/uploadOneMsds.tpl");
		$this->smarty->display("tpls:index.tpl");
	}
	
	
	private function actionUnlinkMsds() {
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest('productID'));
		if ($productDetails['product_id'] === null) {
			throw new Exception('404');
		}
		
		$msds = new MSDS($this->db);
		$sheet = $msds->getSheetByProduct($this->getFromRequest('productID'));
		if (!$sheet) {
			throw new Exception('This product does not have MSDS');
		}
		
		$msds->unlinkMsdsSheet($sheet['id']);
		header('Location: ?action=viewDetails&category=product&id='.$this->getFromRequest('productID'));
	}
	
	private function actionUnlinkTechSheet() {
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest('productID'));
		if ($productDetails['product_id'] === null) {
			throw new Exception('404');
		}
		
		$techSheet = new TechSheet($this->db);
		$sheet = $techSheet->getSheetByProduct($this->getFromRequest('productID'));
		if (!$sheet) {
			throw new Exception('This product does not have Tech Sheet');
		}
		
		$techSheet->unlinkTechSheet($sheet['id']);
		header('Location: ?action=viewDetails&category=product&id='.$this->getFromRequest('productID'));
	}
}
?>