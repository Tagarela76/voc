<?php
class CProduct extends Controller 
{	
        protected $category;
        protected $categoryID;
        
	function CProduct($smarty,$xnyo,$db,$user,$action)
	{
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='product';
		$this->parent_category='department';			
	}
	
	function runAction()
	{
		$this->runCommon();
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
	private function actionViewDetails()
	{
		//  from what level this code is called?
                if($this->getFromRequest('departmentID') !== null) {
                    $level = 'department';
                    $levelID = $this->getFromRequest('departmentID');                    
                } elseif ($this->getFromRequest('facilityID') !== null) {
                    $level = 'facility';
                    $levelID = $this->getFromRequest('facilityID');                    
                } else {
                    throw new Exception('deny');
                }

		//	Access control
		if (!$this->user->checkAccess($level, $levelID)) 
		{	
			throw new Exception('deny');
		}					
							

                
                
/*		if (!$this->user->checkAccess('facility', $this->getFromRequest('facilityID')))                         
		{						
			throw new Exception('deny');
		}					
*/							
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest("id"));
		$productDetails['density_unit'] = new Density($this->db, $productDetails['densityUnitID']);						
		$this->smarty->assign("product", $productDetails);
		$this->smarty->assign("unittype", new Unittype($this->db));
							
		$this->setNavigationUpNew($level, $levelID);
		$this->setListCategoriesLeftNew($level, $levelID,  array('bookmark'=>'product'));
		// if ViewDetails Product from Facility not show other facility
		if ($this->getFromRequest('facilityID') !== null) { 
			$this->setPermissionsNew('facility');			
		} else {
			$this->setPermissionsNew('viewData');			
		}
		$this->smarty->assign('backUrl','?action=browseCategory&category='.$level.'&id='.$levelID.'&bookmark=product');
		$this->smarty->assign('tpl', 'tpls/viewProduct.tpl');
		
		$this->smarty->display("tpls:index.tpl");	
	}			
	
	/**
     * bookmarkAccessory($vars)     
     * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
     */       
	protected function bookmarkDProduct($vars)
	{		
		extract($vars);
		/*
		$current_category = $this->getFromRequest('category');
		$current_category_id = $this->getFromRequest('id');
		if ($current_category == 'department') {
			$cDepartment = new Department($this->db);
			$department_details = $cDepartment->getDepartmentDetails($current_category_id);
			$facility_id = $department_details['facility_id'];
		} else {
			$facility_id = $current_category_id;
		}
		*/
		$product = new Product($this->db);
																		
		$sortStr=$this->sortList('chemicalProduct',3);
		$filterStr=$this->filterList('chemicalProduct');							
		//	search??									
		if ($this->getFromRequest('searchAction')=='search') 
		{										
			//$productsToFind = convertSearchItemsToArray($request['q']);														
			//$productList = $product->searchProducts($productsToFind, $facilityDetails['company_id']);
			$fields=array(0=>'p.product_nr',1=>'p.name');
			$searchStr=$this->filter->getSearchSubQuery($fields,$this->getFromRequest('q'));
			if (!is_null($this->getFromRequest('export'))) 
			{
				$pagination = null;											
			} 
			else 
			{																					
				$pagination = new Pagination((int)$product->countProducts($facilityDetails['company_id'],$facilityDetails['facility_id'],$searchStr));												
				$pagination->url = "?action=browseCategory&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id')."&bookmark=".$this->getFromRequest('bookmark').
				(!is_null($this->getFromRequest('q'))?"&q=".$this->getFromRequest('q')."&searchAction=search":"");			
				$this->smarty->assign('pagination',$pagination);
			}																											
			$productList = $product->getProductList($companyDetails['company_id'], $pagination,$searchStr,false);
			$productList = $product->filterProductsByFacility($companyDetails['company_id'], $facilityDetails['facility_id'], $productList);
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		} 
		else 
		{			
			if (!is_null($this->getFromRequest('export'))) 
			{
				$pagination = null;											
			} 
			else 
			{								
				//$company_id = $this->getFromRequest('company_id');
				$company_id = $companyDetails['company_id'];
				$facility_id = $facilityDetails['facility_id'];
				
				$productsCount = (int)$product->countProducts($company_id,$facility_id,$filterStr);
				$pagination = new Pagination($productsCount);
				
				$pagination->url = "?action=browseCategory&category=".$this->getFromRequest('category')."&id=".$this->getFromRequest('id')."&bookmark=".$this->getFromRequest('bookmark').
					(!is_null($this->getFromRequest('filterField'))?"&filterField=".$this->getFromRequest('filterField'):"").
					(!is_null($this->getFromRequest('filterCondition'))?"&filterCondition=".$this->getFromRequest('filterCondition'):"").
					(!is_null($this->getFromRequest('filterValue'))?"&filterValue=".$this->getFromRequest('filterValue'):"").
					(!is_null($this->getFromRequest('filterField'))?"&searchAction=filter":"");
                                
				$this->smarty->assign('pagination',$pagination);
			}																											
			$productList = $product->getProductList($companyDetails['company_id'], $pagination,$filterStr,$sortStr,false);
			$productList = $product->filterProductsByFacility($companyDetails['company_id'], $facilityDetails['facility_id'], $productList);
		}																																	
		$itemsCount = ($productList) ? count($productList) : 0;
		
                
                
                
                for ($i=0; $i<$itemsCount; $i++) 
		{				
			$url="?action=viewDetails&category=product&id=".$productList[$i]['product_id']."&".$this->getFromRequest('category')."ID=".$this->getFromRequest('id');
			$productList[$i]['url']=$url;
		}	
                
                
                
                
                
		$this->smarty->assign("childCategoryItems", $productList);															
		if (!is_null($this->getFromRequest('export'))) 
		{
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Products of department ".$departmentDetails['name'];
			if ($this->getFromRequest('searchAction')=='search') 
			{
				$exporter->search_term = $this->getFromRequest('q');
			} 
			else 
			{
				$exporter->field = $this->getFromRequest('filterField');
				$exporter->condition = $this->getFromRequest('filterCondition');
				$exporter->value = $this->getFromRequest('filterValue');
			}
			$widths = array(
							'product_id' => '7',
							'supplier_id' => '20',
							'product_nr' => '13',
							'name' => '28',
							'coating' => '10',
							'voclx' => '6',
							'vocwx' => '6',
							'percent_volatile_weight' => 5,
							'percent_volatile_volume' => 5	
							);										
			$header = array(
							'product_id' => 'ID Number',
							'supplier_id' => 'Supplier',
							'product_nr' => 'Product No',
							'name' => 'Product Name',
							'coating' => 'Coating',
							'voclx' => 'VOCLX',
							'vocwx' => 'VOCWX',
							'percent_volatile_weight' => '% (V/W)',
							'percent_volatile_volume' => '% (V/V)'						
							);
			$exporter->setColumnsWidth($widths);
			$exporter->setThead($header);										
			$exporter->setTbody($productList);
			$exporter->export();
			die();
		} 
		else 
		{
			//	set js scripts
			$jsSources = array(
								'modules/js/checkBoxes.js',										
								'modules/js/autocomplete/jquery.autocomplete.js',								
							  );
			$this->smarty->assign('jsSources', $jsSources);
											
			//	set tpl
			$this->smarty->assign('tpl', 'tpls/productListNew.tpl');
		}
	}	
}
?>