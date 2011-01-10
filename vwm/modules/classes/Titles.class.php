<?php

class Titles {
	
	private $smarty;
	
	
	function Titles($smarty) {
		$this->smarty=$smarty;
	}
	
	function titleLogin(){
		$this->smarty->assign("title", "");
	}
	
	function titleCategoryList($category, $companyName="", $facilityName=""){
		switch ($category){
			case "company":
				$this->smarty->assign("title", VOCNAME. ": Companies");
				break;
			case "facility":
				$this->smarty->assign("title", VOCNAME. ": Facilities in company " .$companyName);
				break;
			case "department":
				$this->smarty->assign("title", VOCNAME. ": Departments of facility " .$facilityName. " in company " .$companyName);
				break;
		}
	}
	
	function titleInsideDepartment ($bookmarkType, $departmentName=""){
		switch ($bookmarkType) {
			case "equipment":
				$bookmark="Equipment";
				$this->smarty->assign("title", VOCNAME. ": " .$bookmark. " of department " .$departmentName);
				break;
			
			case "inventory":
				$bookmark="Inventories";
				$this->smarty->assign("title", VOCNAME. ": " .$bookmark. " of facility " .$departmentName);
				break;
				
			case "product":
				$bookmark="Products";
				$this->smarty->assign("title", VOCNAME. ": " .$bookmark);
				break;
				
			case "usage":
				$bookmark="Mixes";
				$this->smarty->assign("title", VOCNAME. ": " .$bookmark. " of department " .$departmentName);
				break;
		}
	}
	
	function titleDeleteItems($categoryType, $categoryName = ""){
		if ($categoryName=="") {
			switch ($categoryType) {
				case "company":
					$categories="companies";
					break;
					
				case "facility":
					$categories="facilities";	
					break;
					
				case "department":
					$categories="departments";
					break;
					
				case "inventory":
					$categories="inventories";
					break;
					
				case "equipment":
					$categories="equipment";
					break;
					
				case "usage":
					$categories="mixes";
					break;									
					
				default: ;
			}
			$this->smarty->assign("title", VOCNAME. ": Delete " .$categories);
		} else {
			switch ($categoryType) {
				case "company":
					$category="company";
					break;
					
				case "facility":
					$category="facility";
					break;
					
				case "department":
					$category="department";
					break;
					
				case "inventory":
					$category="inventory";
					break;
					
				case "equipment":
					$category="equipment";
					break;
					
				case "usage":
					$category="mix";
					break;
					
				case "MSDS Sheet":
					$this->smarty->assign("title", VOCNAME. ": Unlink " .$categoryType);
					return;
					
				default: ;
			}
			$this->smarty->assign("title", VOCNAME. ": Delete " .$category. " " .$categoryName);
		}
	}
	
	function titleViewItem($categoryType){
		$this->smarty->assign("title", VOCNAME. ": View " .$categoryType. " information");
	}
	
	function titleEditItem($categoryType){
		$this->smarty->assign("title", VOCNAME. ": Edit " .$categoryType. " information");
	}
	
	function titleAddItem($categoryType){
		$this->smarty->assign("title", VOCNAME. ": Add new " .$categoryType);
	}
	
	function titleViewItemAdmin($categoryType){
		switch ($categoryType) {
			case "apmethod":
				$bookmark="AP method";
				break;
			case "coat":
				$bookmark="coat";
				break;
			case "product":
				$bookmark="product";
				break;
			case "components":
				$bookmark="component";
				break;
			case "density":
				$bookmark="density";
				break;
			case "country":
				$bookmark="country";
				break;
			case "msds":
				$bookmark="MSDS";
				break;
			case "lol":
				$bookmark="List of list";
				break;
			case "rule":
				$bookmark="rule";
				break;
			case "substrate":
				$bookmark="substrate";
				break;
			case "supplier":
				$bookmark="supplier";
				break;
			case "type":
				$bookmark="type";
				break;
			case "unittype":
				$bookmark="unittype";
				break;
			case "formulas":
				$bookmark="formula";
				break;
			
			case "issue":
				$bookmark = "issue";
				break;
		}
		
		$this->smarty->assign("title", VOCNAME. ": Admin: View ".$bookmark." information");
	}
	
	function titleEditItemAdmin($categoryType){
		switch ($categoryType) {
			case "apmethod":
				$bookmark="AP method";
				break;
			case "coat":
				$bookmark="coat";
				break;
			case "product":
				$bookmark="product";
				break;
			case "components":
				$bookmark="component";
				break;
			case "density":
				$bookmark="density";
				break;
			case "country":
				$bookmark="country";
				break;
			case "msds":
				$bookmark="MSDS";
				break;
			case "lol":
				$bookmark="List of list";
				break;
			case "rule":
				$bookmark="rule";
				break;
			case "substrate":
				$bookmark="substrate";
				break;
			case "supplier":
				$bookmark="supplier";
				break;
			case "type":
				$bookmark="type";
				break;
			case "unittype":
				$bookmark="unittype";
				break;
			case "formulas":
				$bookmark="formula";
				break;
		}
		
		$this->smarty->assign("title", VOCNAME. ": Admin: Edit ".$bookmark." information");
	}
	
	function titleAddItemAdmin($categoryType){
		switch ($categoryType) {
			case "apmethod":
				$bookmark="AP method";
				break;
			case "coat":
				$bookmark="coat";
				break;
			case "product":
				$bookmark="product";
				break;
			case "components":
				$bookmark="component";
				break;
			case "density":
				$bookmark="density";
				break;
			case "country":
				$bookmark="country";
				break;
			case "msds":
				$bookmark="MSDS";
				break;
			case "lol":
				$bookmark="List of list";
				break;
			case "rule":
				$bookmark="rule";
				break;
			case "substrate":
				$bookmark="substrate";
				break;
			case "supplier":
				$bookmark="supplier";
				break;
			case "type":
				$bookmark="type";
				break;
			case "unittype":
				$bookmark="unittype";
				break;
			case "formulas":
				$bookmark="formula";
				break;
		}
		
		$this->smarty->assign("title", VOCNAME. ": Admin: Add new " .$bookmark);
	}
	
	
	function titleClassesAdmin($bookmarkType){
		switch ($bookmarkType) {
			case "apmethod":
				$bookmark="AP Methods";
				break;
			case "coat":
				$bookmark="Coat";
				break;
			case "product":
				$bookmark="Products";
				break;
			case "components":
				$bookmark="Components";
				break;
			case "density":
				$bookmark="Densities";
				break;
			case "country":
				$bookmark="Countries";
				break;
			case "state":
				$bookmark="States";
				break;
			case "lol":
				$bookmark="List of lists";
				break;
			case "msds":
				$bookmark="MSDS";
				break;
			case "rule":
				$bookmark="Rules";
				break;
			case "substrate":
				$bookmark="Substrates";
				break;
			case "supplier":
				$bookmark="Suppliers";
				break;
			case "type":
				$bookmark="Types";
				break;
			case "unittype":
				$bookmark="Unittypes";
				break;
			case "formulas":
				$bookmark="Formulas";
				break;
		}
		$this->smarty->assign("title", VOCNAME. ": Admin: " .$bookmark);
	}
	
	function titleDeleteItemsAdmin($categoryType, $categoryName = ""){
		if ($categoryName=="") {
			switch ($categoryType) {
				case "apmethod":
					$bookmark="AP methods";
					break;
				case "coat":
					$bookmark="coat";
					break;
				case "product":
					$bookmark="products";
					break;
				case "components":
					$bookmark="components";
					break;
				case "density":
					$bookmark="densities";
					break;
				case "country":
					$bookmark="countries";
					break;
				case "state":
					$bookmark="states";
					break;
				case "lol":
					$bookmark="list of lists";
					break;
				case "msds":
					$bookmark="MSDS";
					break;
				case "rule":
					$bookmark="rules";
					break;
				case "substrate":
					$bookmark="substrates";
					break;
				case "supplier":
					$bookmark="suppliers";
					break;
				case "type":
					$bookmark="types";
					break;
				case "unittype":
					$bookmark="unittypes";
					break;
				case "formulas":
					$bookmark="formulas";
					break;
					
				default: ;
			}
			$this->smarty->assign("title", VOCNAME. ": Admin : Delete " .$bookmark);
		} else {
			switch ($categoryType) {
				case "apmethod":
					$bookmark="AP method";
					break;
				case "coat":
					$bookmark="Coat";
					break;
				case "product":
					$bookmark="Product";
					break;
				case "components":
					$bookmark="Component";
					break;
				case "density":
					$bookmark="Density";
					break;
				case "country":
					$bookmark="Country";
					break;
				case "state":
					$bookmark="State";
					break;
				case "lol":
					$bookmark="List of list";
					break;
				case "msds":
					$bookmark="MSDS";
					break;
				case "rule":
					$bookmark="Rule";
					break;
				case "substrate":
					$bookmark="Substrate";
					break;
				case "supplier":
					$bookmark="Supplier";
					break;
				case "type":
					$bookmark="Type";
					break;
				case "unittype":
					$bookmark="Unittype";
					break;
				case "formulas":
					$bookmark="Formula";
					break;
					
				default: ;
			}
			$this->smarty->assign("title", VOCNAME. ": Admin : Delete " .$bookmark. " " .$categoryName);
		}
	}
	
	public function titleIssueReport() {
		$this->smarty->assign("title", "Issue Report Form");
	}
	
	public function titleIssuesList() {
		$this->smarty->assign("title", "Issues List");
	}
	
	public function titleBulkUploaderSettings() {
		$this->smarty->assign("title", "VOC-WEB-MANAGER: Bulk Uploader Settings");
	}
	
	public function titleBulkUploadResults() {
		$this->smarty->assign("title", "VOC-WEB-MANAGER: Bulk Upload results");
	}
	
	public function titleCreateReport($category, $categoryName) {
		$this->smarty->assign("title", "Report creation form for ".$category." ".$categoryName);
	}
	
	public function titleSettings($tab) {
		$this->smarty->assign("title", "VOC-WEB-MANAGER: Settings: ".$tab);
	}
	
	public function titleMsdsUploader($step, $type) {
		switch ($step) {
			case "main":
				$this->smarty->assign("title", "VOC-WEB-MANAGER: MSDS ".$type." Uploader");
				break;
			case "assign":
				$this->smarty->assign("title", "VOC-WEB-MANAGER: MSDS ".$type." Uploader: Assign Step");
				break;
		}		
	}
}
?>