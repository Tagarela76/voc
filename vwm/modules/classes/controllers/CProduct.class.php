<?php

class CProduct extends Controller {

	protected $category;
	protected $categoryID;

	function CProduct($smarty, $xnyo, $db, $user, $action) {
		parent::Controller($smarty, $xnyo, $db, $user, $action);
		$this->category = 'product';
		$this->parent_category = 'department';
	}

	protected function actionViewDetails() {
		//  from what level this code is called?
		if ($this->getFromRequest('departmentID') !== null) {
			$level = 'department';
			$levelID = $this->getFromRequest('departmentID');
		} elseif ($this->getFromRequest('facilityID') !== null) {
			$level = 'facility';
			$levelID = $this->getFromRequest('facilityID');
		} else {
			throw new Exception('deny');
		}

		//	Access control
		if (!$this->user->checkAccess($level, $levelID)) {
			throw new Exception('deny');
		}

		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($this->getFromRequest("id"));
		$productDetails['density_unit'] = new Density($this->db, $productDetails['densityUnitID']);
		$this->smarty->assign("product", $productDetails);
		$this->smarty->assign("unittype", new Unittype($this->db));

		$this->setNavigationUpNew($level, $levelID);
		$this->setListCategoriesLeftNew($level, $levelID, array('bookmark' => 'product'));
		// if ViewDetails Product from Facility not show other facility
		if ($this->getFromRequest('facilityID') !== null) {
			$this->setPermissionsNew('facility');
		} else {
			$this->setPermissionsNew('viewData');
		}
		$this->smarty->assign('backUrl', '?action=browseCategory&category=' . $level . '&id=' . $levelID . '&bookmark=product');
		$this->smarty->assign('tpl', 'tpls/viewProduct.tpl');

		$this->smarty->display("tpls:index.tpl");
	}

	/**
	 * bookmarkAccessory($vars)
	 * @vars $vars array of variables: $moduleMap, $departmentDetails, $facilityDetails, $companyDetails
	 */
	protected function bookmarkDProduct($vars) { 
		extract($vars);

		$product = new Product($this->db);

		$sortStr = $this->sortList('chemicalProduct', 3);
		$filterStr = $this->filterList('chemicalProduct');

		//	set search criteria
		if (!is_null($this->getFromRequest('q'))) {
			$product->searchCriteria = $this->convertSearchItemsToArray($this->getFromRequest('q'));
			$this->smarty->assign('searchQuery', $this->getFromRequest('q'));
		}

		// set organization criteria
		$product->organizationCriteria['companyID'] = ($companyDetails['company_id']) ? $companyDetails['company_id'] : false;
		$product->organizationCriteria['facilityID'] = ($facilityDetails['facility_id']) ? $facilityDetails['facility_id'] : false;
		
		$libraryType = $this->getFromRequest('libraryType');
		$productLibraryType = new ProductLibraryType($this->db);		
		$productLibraryTypeID = $productLibraryType->mapping($libraryType);
		
		$productCount = $product->getProductCount(0, $filterStr, $productLibraryTypeID);

		$url = "?".$_SERVER["QUERY_STRING"];
		$url = preg_replace("/\&page=\d*/","", $url);

		$pagination = new Pagination($productCount);
		$pagination->url = $url;
		$this->smarty->assign('pagination', $pagination);

		$productList = $product->getProductList($companyDetails['company_id'], $pagination, $filterStr, $sortStr, $productLibraryTypeID);
		
		$itemsCount = ($productList) ? count($productList) : 0;

		for ($i = 0; $i < $itemsCount; $i++) {
			$url = "?action=viewDetails&category=product&id=" . $productList[$i]['product_id'] . "&" . $this->getFromRequest('category') . "ID=" . $this->getFromRequest('id');
			$productList[$i]['url'] = $url;
		}

		$this->smarty->assign("childCategoryItems", $productList);
		if (!is_null($this->getFromRequest('export'))) {
			//	EXPORT THIS PAGE
			$exporter = new Exporter(Exporter::PDF);
			$exporter->company = $companyDetails['name'];
			$exporter->facility = $facilityDetails['name'];
			$exporter->department = $departmentDetails['name'];
			$exporter->title = "Products of Department " . $departmentDetails['name'];
			if ($this->getFromRequest('searchAction') == 'search') {
				$exporter->search_term = $this->getFromRequest('q');
			} else {
				$exporter->field = $this->getFromRequest('filterField');
				$exporter->condition = $this->getFromRequest('filterCondition');
				$exporter->value = $this->getFromRequest('filterValue');
			}
			$widths = array(
				'product_id' => '8',
				'supplier' => '20',
				'product_nr' => '10',
				'name' => '28',
				'coating' => '10',
				'voclx' => '6',
				'vocwx' => '6',
				'percent_volatile_weight' => 6,
				'percent_volatile_volume' => 6
			);
			$header = array(
				'product_id' => 'ID Number',
				'supplier' => 'Supplier',
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
		} else {			
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