<?php

class Product extends ProductProperties {

	protected $db;
	private $productType;

	function Product($db) {
		$this->db=$db;
		$this->productType = new ProductTypes($db);
	}



	public function getProductList($companyID = 0, Pagination $pagination = null,$filter=' TRUE ', $sort=' ORDER BY s.supplier ') {
		if (is_null($pagination)) {
			//here we get product list for dropdown => we shouls sort it by product_nr too
			if (strstr($sort,'product_nr') == 0) {
				$sort .= ', p.product_nr ';
			}
		}

		$products = $this->selectProductsByCompany($companyID, 0, $pagination,$filter, $sort);

		if ($products) {
			//	if asking without pagination we don't need MSDS links, cuz I think they need product list for dropdown
			if ($pagination) {
				for ($i=0;$i<count($products);$i++) {
					$products[$i]['msdsLink'] = $this->checkForAvailableMSDS($products[$i]['product_id']);
					$products[$i]['techSheetLink'] = $this->checkForAvailableTechSheet($products[$i]['product_id']);
				}
			}
			return $products;
		} else
			return false;
	}

	public function getCountSupplierProducts($supplierID) {
	
		$query = "SELECT COUNT(*) cnt FROM product p, supplier s WHERE p.supplier_id = s.supplier_id AND s.original_id = {$supplierID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}

	public function getProductListByMFG($supplierID, $companyID = 0, Pagination $pagination = null,$filter=' TRUE ', $sort=' ORDER BY s.supplier ') {
		$products = $this->selectProductsByCompany($companyID, $supplierID, $pagination,$filter,$sort);

		$supplier = new Supplier($this->db);
		$supplierDetails = $supplier->getSupplierDetails($supplierID);

		if ($products) {
			if ($pagination) {
				for ($i=0;$i<count($products);$i++) {
					$products[$i]['msdsLink'] = $this->checkForAvailableMSDS($products[$i]['product_id']);
					$products[$i]['techSheetLink'] = $this->checkForAvailableTechSheet($products[$i]['product_id']);
				}
			}
			return $products;
		} else
			return false;
	}
	
	public function getProductPrice($productID, $jobberID = null, Pagination $pagination = null, $filter = ' TRUE ', $sort = ' ORDER BY s.supplier ') {

		$query =	"SELECT pp.* " .
					"FROM price4product pp  ".
					"WHERE pp.product_id = " . (int) $productID . " ";

		if ($jobberID){
			$query .= " AND pp.jobber_id = " . (int) $jobberID . "";
		}

//echo $query;
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();
		$productPrice = array();
			foreach($arr as $b) {

					$productPrice[] = $b;
            
			}		

		return $productPrice;
	}
	
	public function getProductPriceBySupplier($supplierID, $priceID = null, $jobberID, Pagination $pagination = null, Sort $sortStr = null) {

		$query =	"SELECT p.product_id, p.product_nr, pp.* " .
					"FROM " . TB_SUPPLIER . " s , price4product pp  , ". TB_PRODUCT . " p ".
					"WHERE p.supplier_id = s.supplier_id " .
					"AND s.original_id = " . (int) $supplierID . " AND pp.product_id = p.product_id AND pp.jobber_id = {$jobberID} ";
		if ($priceID){
			$query .= " AND pp.price_id = " . (int) $priceID . "";
		}
	
			$query .= " GROUP BY p.product_id ";
	
		if ($sortStr){
			$query .= $sortStr;
		}else{
			$query .= " ORDER BY p.product_id ASC ";
		}		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		$this->db->query($query);
//echo $query;
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();
		$productPrice = array();
			foreach($arr as $b) {

					$productPrice[] = $b;
            
			}		

		return $productPrice;
	}	
	
	public function getCompanyListWhichProductUse($prductID) {

		$query =	"SELECT p.product_id, c.name " .
					"FROM product p, company c, facility f,product2inventory pi ".
					"WHERE f.facility_id = pi.facility_id AND f.company_id = c.company_id AND pi.product_id = p.product_id " .
					"AND p.product_id = " . (int) $prductID . " ";

			$query .= " ORDER BY c.name ASC";

		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$companyList = $this->db->fetch_all_array();
		return $companyList;
	}	
/*
 
 




 */

	private function getPaintMaterialByProductID($productID) {
		$query = "SELECT id FROM ".TB_MATERIAL2INVENTORY." WHERE product_id=".(int)$productID;

		$this->db->query($query);

		if ($this->db->num_rows() != 0) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$m2iID = $data->id;
				$m2iIDList[] = $m2iID;
			}
		}

		return $m2iIDList;
	}

	public function isInUse($productID) {
		return false;
	}


	public function getProductDetails($productID, $vanilla = false, $addNew = false) {
		settype($productID,"integer");

		$this->db->query("SELECT * FROM ".TB_PRODUCT." WHERE product_id = ".(int)$productID." ORDER BY product_nr");
		$data = $this->db->fetch(0);

		if ($addNew){
			$this->db->query("SELECT * FROM ".TB_COMPONENT." ORDER BY comp_name");
			$data3 = $this->db->fetch(0);
			$data->product_id 	= "";
			$data->product_nr 	= "";
			$data->product_desc = "";
			$data->component_id = $data3->component_id;
			$data->densityuse 	= "";
		}

		$product = array(
			'product_id'				=>	$data->product_id,
			'product_nr'				=>	$data->product_nr,
			'name'						=>	$data->name,
			'voclx'						=>	$data->voclx,
			'vocwx'						=>	$data->vocwx,
			'density'					=>	$data->density,
			'densityUnitID'				=>	$data->density_unit_id,
			'coating_id'				=>	$data->coating_id,
			'specialty_coating'			=>	$data->specialty_coating,
			'aerosol'					=>	$data->aerosol,
			'specific_gravity'			=>	$data->specific_gravity,
			'specific_gravity_unit_id'	=>	$data->specific_gravity_unit_id,
			'supplier_id'				=>	$data->supplier_id,
			'boiling_range_from'		=>	$data->boiling_range_from,
			'boiling_range_to'			=>	$data->boiling_range_to,
			'percent_volatile_weight'	=>	$data->percent_volatile_weight,
			'percent_volatile_volume'	=>	$data->percent_volatile_volume,
			
			'product_instock'	=>	$data->product_instock,
			'product_limit'	=>	$data->product_limit,
			'product_amount'	=>	$data->product_amount
		);
		$hazardous = new Hazardous($this->db);
		$product['chemicalClasses'] = $hazardous->getChemicalClassification($productID);


		if (!$vanilla){
			$this->db->query("SELECT * FROM ".TB_SUPPLIER." WHERE supplier_id = ".$data->supplier_id);
			$data2 = $this->db->fetch(0);
			$product['supplier_id'] = $data2->supplier;
			$product['supplier'] = $data2->supplier;

			$this->db->query("SELECT * FROM ".TB_COAT." WHERE coat_id = ".$data->coating_id);
			$data2 = $this->db->fetch(0);
			$product['coating_id'] = $data2->coat_desc;
		}

		$rule = new Rule($this->db);

		$query = "SELECT * FROM ".TB_COMPONENTGROUP." WHERE product_id = ".$data->product_id;
		$this->db->query($query);
		$componentsCount = $this->db->num_rows();
		$data = $this->db->fetch_all();
		for ($i=0; $i<$componentsCount; $i++) {
			$query = "SELECT * FROM ".TB_COMPONENT." WHERE component_id = ".$data[$i]->component_id;
			$this->db->query($query);
			$data2 = $this->db->fetch(0);
			$component = array (
				"component_id"	=>	$data2->component_id,
				"cas"			=>	$data2->cas,
				"comp_cas"		=>	$data2->cas,
				"description"	=>	$data2->description
			);

			$component['mm_hg']   = $data[$i]->mm_hg;
			$component['temp']   = $data[$i]->temp;
			$component['weightFrom'] = $data[$i]->weight;
			$component['type']	 = $data[$i]->type;
			$component['temp_vp'] = $data[$i]->temp;
			$component['rule_id'] = $data[$i]->rule_id;
			$component['substrate_id'] = $data[$i]->rule_id;

			/* DEPRICATED - compounds do not have these properties*/
//			$query_2 = "SELECT ".$rule->ruleNrMap[$rule->getRegion()]." FROM ".TB_RULE." WHERE rule_id = ".$data3->rule_id;
//			$this->db->query($query_2);
//			$data4 = $this->db->fetch(0);
//			$component['rule'] = $data4->$rule->ruleNrMap[$rule->getRegion()];
//
//			$query_2 = "SELECT substrate_desc FROM ".TB_SUBSTRATE." WHERE substrate_id = ".$data3->substrate_id;
//			$this->db->query($query_2);
//			$data4 = $this->db->fetch(0);
//			$component['substrate'] = $data4->substrate_desc;

			$components[] = $component;
		}

		$product['components'] = $components;

		return $product;
	}



	public function addNewProduct($productData, $companyID = 0) {

		//screening of quotation marks
		foreach ($productData as $key=>$value)
		{
			switch ($key)
			{
				case 'chemicalClasses': break;
				case 'components':break;
				default:
				{
					$productData[$key]=mysql_escape_string($value);
				}
			}
		}

		$query="INSERT INTO ".TB_PRODUCT." (product_nr, name, voclx, vocwx, density, density_unit_id, coating_id, " .
				"specific_gravity, specific_gravity_unit_id, specialty_coating, aerosol, boiling_range_from, boiling_range_to, " .
				"supplier_id, percent_volatile_weight, percent_volatile_volume,product_instock,product_limit,product_amount,product_stocktype) VALUES (";

		$query.="'".$productData["product_nr"]."', ";
		$query.="'".$productData["name"]."', ";
		$query.="'".$productData["voclx"]."', ";
		$query.="'".$productData["vocwx"]."', ";
		$query.="'".$productData["density"]."', ";
		$query.="'".$productData["density_unit_id"]."', ";
		$query.="'".$productData["coating_id"]."', ";
		$query.="'".$productData["specific_gravity"]."', ";
		$query.="'".$productData["specific_gravity_unit_id"]."', ";
		$query.="'".$productData["specialty_coating"]."', ";
		$query.="'".$productData["aerosol"]."', ";
		$query.="'".$productData["boiling_range_from"]."', ";
		$query.="'".$productData["boiling_range_to"]."', ";
		$query.="'".$productData["supplier_id"]."', ";
		$query.="'".$productData["percent_volatile_weight"]."', ";
		$query.="'".$productData["percent_volatile_volume"]."', ";
		
		$query.="'".$productData["product_instock"]."', ";
		$query.="'".$productData["product_limit"]."', ";
		$query.="'".$productData["product_amount"]."', ";
		$query.="'".$productData["product_stocktype"]."', ";
		

		$query.=')';


		$this->db->query($query);

		$this->db->query("SELECT LAST_INSERT_ID() id");
		$productID = $this->db->fetch(0)->id;
		
		//assign product2types
		
		foreach ($productData['resultTypesList'] as $prod){
			$this->assignProduct2Type($productID, $prod['type'], $prod['subType']);
		}
		
		//assign product2company
		if (!empty($companyID)) {
			$this->assignProduct2Company($productID, $companyID);
		}

		//	set hazardous (chemical) classes
		$chemicalClasses = $productData['chemicalClasses'];
		$chemicalClassIDArray = array();
		foreach ($chemicalClasses as $chemicalClass) {
			$chemicalClassIDArray[] = $chemicalClass['id'];
		}
		$hazardous = new Hazardous($this->db);
		$hazardous->setProduct2ChemicalClasses($productID, $chemicalClassIDArray);

		//	add	components
		$this->addComponentgroupNR($productData['components'], $productID);
		return $productID;
	}


	public function deleteProduct2($productID){
		settype($productID,"integer");
		// Delete from Material2Inventory
		$m2iIDList = $this->getPaintMaterialByProductID($productID);

		foreach ($m2iIDList as $m2iID) {
			$m2i = new PaintMaterial($this->db, $m2iID);
			$m2i->delete();
		}

		$query = "SELECT * FROM ".TB_MIXGROUP." WHERE product_id=".$productID;
		$this->db->query($query);

		$mixGroupList = $this->db->fetch_all();
		$mix=new Mix($this->db);
		for ($i=0; $i < count($mixGroupList); $i++) {
		    $data=$mixGroupList[$i];
			$mix->deleteUsage($data->mix_id);
		}

		//	Delete Product's Component Group
		$this->deleteComponentgroupNR($productID);

		//	Delete Product
		$this->db->query("DELETE FROM ".TB_PRODUCT." WHERE product_id=".$productID);

		//	unassign product from all companies
		$this->unassignProductFromCompany($productID);

		//	unassign product from all companies
		$hazardous = new Hazardous($this->db);
		$hazardous->deleteProduct2ChemicalClassesLink($productID);
	}





	public function setProductDetails ($productData) {

		//screening of quotation marks
		foreach ($productData as $key=>$value)
		{
			switch ($key)
			{
				case 'chemicalClasses': break;
				case 'components':break;
				default:
				{
					$productData[$key]=mysql_escape_string($value);
				}
			}
		}

		//	set hazardous (chemical) classes
		$chemicalClasses = $productData['chemicalClasses'];
		$chemicalClassIDArray = array();
		foreach ($chemicalClasses as $chemicalClass) {
			$chemicalClassIDArray[] = $chemicalClass['id'];
		}
		$hazardous = new Hazardous($this->db);
		$hazardous->setProduct2ChemicalClasses($productData['product_id'], $chemicalClassIDArray);

		$query = "UPDATE ".TB_PRODUCT." SET ";

		$query .= "product_nr = '".$productData["product_nr"]."', ";
		$query .= "name = '".$productData["name"]."', ";
		$query .= "voclx = '".$productData["voclx"]."', ";
		$query .= "vocwx = '".$productData["vocwx"]."', ";
		$query .= (empty($productData["density"])) ? "density = NULL, " : "density = '".$productData["density"]."', ";
		$query .= "density_unit_id = '".$productData["density_unit_id"]."', ";
		$query .= "coating_id = '".$productData["coating_id"]."', ";
		$query .= "specialty_coating = '".$productData["specialty_coating"]."', ";
		$query .= "aerosol = '".$productData["aerosol"]."', ";
		$query .= "specific_gravity = '".$productData["specific_gravity"]."', ";
		$query .= "specific_gravity_unit_id = '".$productData["specific_gravity_unit_id"]."', ";
		$query .= "boiling_range_from = '".$productData["boiling_range_from"]."', ";
		$query .= "boiling_range_to = '".$productData["boiling_range_to"]."', ";
		$query .= "supplier_id = '".$productData["supplier_id"]."', ";
		$query .= "percent_volatile_weight = '".$productData["percent_volatile_weight"]."', ";
		$query .= "percent_volatile_volume = '".$productData["percent_volatile_volume"]."', ";
		
		$query .= "product_instock = '".$productData["product_instock"]."', ";
		$query .= "product_limit = '".$productData["product_limit"]."', ";
		$query .= "product_amount = '".$productData["product_amount"]."', ";
		$query .= "product_stocktype = '".$productData["product_stocktype"]."' ";

		$query .= " WHERE product_id = ".$productData['product_id'];

		$this->db->query($query);

		$this->deleteComponentgroupNR($productData['product_id']);
		$this->addComponentgroupNR($productData['components'], $productData['product_id']);

	}


	public function getProductIdByName($productName){

		$productName=mysql_escape_string($productName);

		$this->db->query("SELECT * FROM ".TB_PRODUCT." where product_nr='".$productName."'");
		$data=$this->db->fetch(0);
		$productID=$data->product_id;

		return $productID;
	}


	public function deleteProductOnly($productID){

		settype($productID,"integer");

		$this->db->query("DELETE FROM ".TB_PRODUCTGROUP." WHERE product_id=".$productID);
		$this->db->query("SELECT componentgroup_nr FROM ".TB_PRODUCT." WHERE product_id=".$productID);
		$data=$this->db->fetch(0);
		$nr=$data->componentgroup_nr;
		$this->deleteComponentgroupNR($nr);
		$this->db->query("DELETE FROM ".TB_PRODUCT." WHERE product_id=".$productID);

		//	unassign product from all companies
		$this->unassignProductFromCompany($productID);

		//	unassign product from all companies
		$hazardous = new Hazardous($this->db);
		$hazardous->deleteProduct2ChemicalClassesLink($productID);
	}

	public function getMaxComponentgroupNR() {
		$query="SELECT componentgroup_nr FROM ".TB_PRODUCT." ORDER BY componentgroup_nr";
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$data=$this->db->fetch($this->db->num_rows() - 1);
			$maxNR=$data->componentgroup_nr;
		} else {
			$maxNR=0;
		}
		return $maxNR;
	}

	public function getVocSums($productList) {
		$sums=array(
			"voc"	=>	"0.00",
			"voclx"	=>	"0.00",
			"vocwx"	=>	"0.00"
		);
		for ($i=0; $i < count($productList); $i++) {
			$data=$productList[$i];
			$productInfo=$this->getProductDetails($data['product_id']);
			$sums['voc']+=$productInfo['vocwx'];
			$sums['voclx']+=$productInfo['voclx'];
			$sums['vocwx']+=$productInfo['vocwx'];
		}
		return $sums;
	}

	public function deleteComponentgroupNR($productID) {

		settype($productID,"integer");

		$query = "DELETE FROM ".TB_COMPONENTGROUP." WHERE product_id = ".$productID;
		$this->db->query($query);
	}

	public function addComponentgroupNR($components, $id) {

		for ($i=0; $i < count($components); $i++) {
			//screening of quotation marks
			foreach ($components[$i] as $key=>$value)
			{
				$components[$i][$key]=mysql_escape_string($value);
			}

			$query="INSERT INTO ".TB_COMPONENTGROUP." (component_id, product_id, substrate_id, rule_id, temp, mm_hg, weight_from, type) VALUES (";
			$query.="'".$components[$i]['component_id']."', ";
			$query.="'".$id."', ";
			$query.="'".$components[$i]['substrate_id']."', ";
			$query.="'".$components[$i]['rule_id']."', ";
			$query.="'".$components[$i]['temp_vp']."', ";
			$query.="'".$components[$i]['mm_hg']."', ";
			$query.="'".$components[$i]['weightFrom']."', ";
			$query.="'".$components[$i]['type']."'";

			$query.=")";
			$this->db->query($query);
		}
	}

	public function initializeByID($productID) {

		settype($productID,"integer");

		$query = "SELECT * FROM ".TB_PRODUCT." WHERE product_id = ".$productID;

		$this->db->query($query);

		if ($this->db->num_rows() != 0) {
			$productFields = $this->db->fetch(0);

			$this->voclx = $productFields->voclx;
			$this->vocwx = $productFields->vocwx;
			$this->density = $productFields->density;
			$this->densityUnitID = $productFields->density_unit_id;	//	density's unit type
			$this->perccentVolatileWeight = $productFields->percent_volatile_weight;
			echo "<h2>initializeByID perccentVolatileWeight: {$this->perccentVolatileWeight}</h2>";
			$this->perccentVolatileVolume = $productFields->percent_volatile_volume;	//	density's unit type
			return true;
		} else {
			return false;
		}

	}
	public function toUpperCase() {
		$this->db->query("SELECT * FROM ".TB_COAT);
		$coatList = $this->db->fetch_all();

		for ($i=0; $i < count($coatList); $i++) {
			$data = $coatList[$i];
			$id = $data->coat_id;
			$coat = $data->coat_desc;
			$coat = strtoupper($coat);
			//$coats[] = $coat;

			$query="UPDATE ".TB_COAT." SET coat_desc='".$coat."' WHERE coat_id='".$id."'";

			print_r($query." ");
			$this->db->query($query);

		}
	}

	function getProductInfoInMixes($productID) {
		settype($productID,"integer");

		$query = "SELECT * FROM ".TB_PRODUCT." WHERE product_id = '".$productID."'";
		$this->db->query($query);
		$data=$this->db->fetch(0);
		$description = $data->name;

		$query = "SELECT * FROM ".TB_COAT." WHERE coat_id=".$data->coating_id;
		$this->db->query($query);
		$coatName = $this->db->fetch(0)->coat_desc;
		$info=array(
			"desc"	=>	$description,
			"coat"	=>	$coatName
		);
		return $info;
	}

	public function isInUseList($productID) {
		settype($productID,"integer");

		//Looking in mix groups by product id for linked mixes
		$query = "SELECT * FROM ".TB_MIXGROUP." WHERE product_id=".$productID;
		$this->db->query($query);

		$mixList = $this->db->fetch_all();
		for ($i=0; $i < count($mixList); $i++){
			$mix = $mixList[$i]->mix_id;
			$mixes[] = $mix;
		}

		//Looking in product group by product id for linked inventory id
		$query = "SELECT distinct inventory_id as inventory_id FROM ".TB_PRODUCTGROUP." WHERE product_id=".$productID;
		$this->db->query($query);

		$inventoryList = $this->db->fetch_all();
		for ($i=0; $i < count($inventoryList); $i++) {
		    $inventory = $inventoryList[$i]->inventory_id;
			//Looking in equipment by inventory for linked equipment
			$query = "SELECT * FROM ".TB_EQUIPMENT." WHERE inventory_id=".$inventory;
			$this->db->query($query);

			$equipmentList= $this->db->fetch_all();
			for ($j=0; $j < count($equipmentList); $j++) {
		    	$equipment = $equipmentList[$j]->equipment_id;

		    	//Looking in mix by equipment for linked mix

		    	$query = "SELECT * FROM ".TB_USAGE." WHERE equipment_id = ".$equipment;
				$this->db->query($query);

				$mixList = $this->db->fetch_all();
					for ($k=0; $k < count($mixList); $k++) {
		    			$mix = $mixList[$k]->mix_id;
		    			$mixes[] = $mix;
					}
		    	$equipments[] = $equipment;
			}
			$inventories[] = $inventory;
		}

		if (count($mixes) != 0) {
			$mixCnt = count (array_unique($mixes));
		} else {
			$mixCnt = 0;
		}
		$output = array (
			"inventoryCnt" => count($inventories),
			"equipmentCnt" => count($equipments),
			"mixCnt" => $mixCnt
		);

		return $output;
	}


	public function assignProduct2Company($productID, $companyID) {
		if (!$this->checkIsProduct2CompanyLink($productID, $companyID)) {
			$this->insertProduct2CompanyLink($productID, $companyID);
		}
	}
	
	public function assignGOM2Jobber($gomID, $jobberID) {
		if (!$this->checkIsGOM2JobberLink($gomID, $jobberID)) {
			$this->insertGOM2JobberLink($gomID, $jobberID);
		}
	}	

	public function unassignProductFromCompany($productID = false, $companyID = false) {
		if ($this->checkIsProduct2CompanyLink($productID, $companyID)) {
			$this->deleteProduct2CompanyLink($productID, $companyID);
		}
	}

	public function unassignGOMFromJobber($gomID = false, $jobberID = false) {
		if ($this->checkIsGOM2JobberLink($gomID, $jobberID)) {
			$this->deleteGOM2JobberLink($gomID, $jobberID);
		}
	}	

	//	getting product list in format: SUPPLIER		PRODUCT_NR		PRODUCT_NAME
	public function getFormatedProductList($companyID, $products = false) {	//	arr $products = do not show these products

		$productListTemp = $this->getProductList($companyID);

		//	only if products are in company
		if ($productListTemp) {
			//$maxValues = $this->getMaxLenghtSupplierAndProductNR($productListTemp);
			for ($i = 0; $i<count($productListTemp); $i++) {
				$show = true;
				if ($products) {
					for ($j=0; $j<count($products); $j++) {
						if ($products[$j]['product_id'] == $productListTemp[$i]['product_id']) {
							$show = false;
							break;
						}
					}
				}
				$formattedProduct = $productListTemp[$i]['product_nr']." &mdash;  	 ".$productListTemp[$i]['name'];
				$productListTemp[$i]['formattedProduct'] = $formattedProduct;

				$productList[]=$productListTemp[$i];
			}
			return $productList;
		} else
			return false;
	}



	//	get number of products for company or false
	public function countProducts($companyID,$filter=' TRUE ') {

		settype($companyID,"integer");

		$query = "SELECT count(product_id) productCount FROM product2company WHERE company_id = $companyID AND $filter";
		$this->db->query($query);
		return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->productCount : false;
	}




	public function productAutocomplete($occurrence, $companyID = 0) {

		$occurrence=mysql_escape_string($occurrence);
		settype($companyID,"integer");

		if ($companyID === 0){
			$query = "SELECT product_nr, name, LOCATE('".$occurrence."', product_nr) occurrence, LOCATE('".$occurrence."', name) occurrence2 " .
				"FROM ".TB_PRODUCT." p WHERE LOCATE('".$occurrence."', product_nr)>0 OR LOCATE('".$occurrence."', name)>0 LIMIT ".AUTOCOMPLETE_LIMIT;
		} else {
			$query = "SELECT product_nr, name, LOCATE('".$occurrence."', product_nr) occurrence, LOCATE('".$occurrence."', name) occurrence2 " .
				"FROM ".TB_PRODUCT." p, product2company p2c " .
				"WHERE p.product_id = p2c.product_id " .
				"AND p2c.company_id = ".$companyID."  AND (LOCATE('".$occurrence."', product_nr)>0 OR LOCATE('".$occurrence."', name)>0) LIMIT ".AUTOCOMPLETE_LIMIT;
		}

		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$productsData = $this->db->fetch_all();
			for ($i = 0; $i < count($productsData); $i++) {
				if ($productsData[$i]->occurrence) {
					$product = array (
						"productNR"		=>	$productsData[$i]->product_nr,
						"occurrence"	=>	$productsData[$i]->occurrence
					);
					$results[] = $product;

				} elseif ($productsData[$i]->occurrence2) {
					$product = array (
						"productNR"		=>	$productsData[$i]->name,
						"occurrence"	=>	$productsData[$i]->occurrence2
					);
					$results[] = $product;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}
//	autocomplete product search in admin.Not work yet
	 public function productAutocompleteAdmin($occurrence, $sub) {
		$occurrence = mysql_escape_string($occurrence);   
                $query = "SELECT product_nr, name, LOCATE('".$occurrence."', product_nr) occurrence, LOCATE('".$occurrence."', name) occurrence2 " .
					"FROM ".TB_PRODUCT." p, ".TB_SUPPLIER." s " .
					"WHERE p.supplier_id = s.supplier_id " .
					"AND s.original_id =".(int)$sub. " ORDER BY  p.product_id ASC"; 
		$query = "SELECT product_nr, name, LOCATE('".$occurrence."', product_nr) occurrence, LOCATE('".$occurrence."', name) occurrence2 " .
				 "FROM ".TB_PRODUCT." p, ".TB_SUPPLIER." s WHERE p.supplier_id = s.supplier_id AND s.original_id =".(int)$sub." AND LOCATE('".$occurrence."', product_nr)>0 OR LOCATE('".$occurrence."', name)>0 LIMIT ".AUTOCOMPLETE_LIMIT;				
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$productsData = $this->db->fetch_all();
			for ($i = 0; $i < count($productsData); $i++) {
				if ($productsData[$i]->occurrence) {
					$product = array (
						"productNR"		=>	$productsData[$i]->product_nr,
						"occurrence"	=>	$productsData[$i]->occurrence
					);
					$results[] = $product;

				} elseif ($productsData[$i]->occurrence2) {
					$product = array (
						"productNR"		=>	$productsData[$i]->name,
						"occurrence"	=>	$productsData[$i]->occurrence2
					);
					$results[] = $product;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;		
	
	}
	//	search product by product_nr or name.
	public function searchProducts($products, $companyID = 0) {
		$products = (!is_array($products))?array($products):$products;
		$where = "";
		foreach($products as $product) {
			$product = mysql_escape_string($product);
			$where .= " (LOCATE('$product',product_nr) > 0 OR LOCATE('$product', name) > 0) OR";
		}
		$where = substr($where,0,-2);
		if ($companyID === 0){
			$query = "SELECT * FROM ".TB_PRODUCT." " .
					" WHERE $where LIMIT ".AUTOCOMPLETE_LIMIT;
		} else {
			$query = "SELECT * FROM ".TB_PRODUCT." p, product2company p2c " .
				"WHERE p.product_id = p2c.product_id " .
				"AND p2c.company_id = ".$companyID."  AND $where LIMIT ".AUTOCOMPLETE_LIMIT;
		}
		$this->db->query($query);
		$searchedProducts = $this->db->fetch_all_array();
		$coat = new Coat($this->db);
		$coatList = $coat->getCoatArrayListedById();
		foreach($searchedProducts as $key => $product) {
			$searchedProducts [$key]['coating'] = $coatList[$product['coating_id']];
			$searchedProducts [$key]['msdsLink'] = $this->checkForAvailableMSDS($product['product_id']);
		}

		return (isset($searchedProducts)) ? $searchedProducts : null;
	}


	public function getProductUsageByDays(TypeChain $beginDate, TypeChain $endDate, $category, $categoryID) {
            
        $categoryDependedSql = "";
		$tables = TB_USAGE." m, ".TB_PRODUCT." p, ".TB_MIXGROUP." mg";
		switch ($category) {
			case "company":
				$tables .= ", ".TB_DEPARTMENT." d, ".TB_FACILITY." f ";
				$categoryDependedSql = " m.department_id = d.department_id "
                                                        ." AND d.facility_id = f.facility_id "
                                                        ." AND f.company_id = {$categoryID} ";
				break;
			case "facility":
				$tables .= ", ".TB_DEPARTMENT." d ";
				$categoryDependedSql = " m.department_id = d.department_id AND d.facility_id = {$categoryID} ";
				break;
			case "department":				
				$categoryDependedSql = " m.department_id = {$categoryID} ";
				break;
			default :
				throw new Exception('Unknown category for DailyEmissions');
				break;
		}
		
		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name, m.creation_time " .
				" FROM {$tables} " .
				" WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."'".
				" GROUP BY mg.product_id, m.creation_time " .
				" ORDER BY p.product_id ";
		
		//"AND m.creation_time BETWEEN '".$beginDate->formatInput()."' AND '".$endDate->formatInput()."' " .
                //echo $query;
		$this->db->query($query);
		$productUsageData = $this->db->fetch_all();
		$result = array();

		//get empty template for output for each product
		$emptyProductData = array();
		$day = 86400; // Day in seconds
		$daysCount = round((strtotime($endDate->formatInput()) - strtotime($beginDate->formatInput()))/$day) + 1;
		$curDay = $beginDate->formatInput();
		for($i = 0; $i< $daysCount; $i++) {
			$emptyProductData []= array(strtotime($curDay)*1000, 0);
			$curDay = date('Y-m-d',strtotime($curDay.' + 1 day'));
		}

		//get all used products list
		$productList = array();
		foreach($productUsageData as $data) {
			if (!in_array($data->product_nr,$productList)) {
				$productList []= $data->product_nr;
			}
		}
		$this->setProductNR($productList);

		if (count($productList) == 0) {
			$productList []= 'products not used!';
		}

		//format output for all products
		foreach($productList as $data) {
			$result[$data] = $emptyProductData;
		}


		foreach ($productUsageData as $data) {
			//$key = round((strtotime($data->creation_time) - strtotime($beginDate->formatInput()))/$day); //$key == day from the begin date
			//$result[$data->product_nr][$key] = array(strtotime($data->creation_time)*1000, $data->sum);
			$key = round(($data->creation_time - $beginDate->getTimestamp())/$day, 2);
			//$key = intval(date("d",$key));
			$result[$data->product_nr][$key][1] += $data->sum;
		}

		return $result;
	}

	private function checkIsProduct2CompanyLink($productID = false, $companyID = false) {

		$productID=mysql_escape_string($productID);
		$companyID=mysql_escape_string($companyID);

		//analyze different input situations
		if (!$productID && $companyID) {
			$query = "SELECT id FROM product2company WHERE company_id = ".$companyID;
		} elseif (!$companyID && $productID) {
			$query = "SELECT id FROM product2company WHERE product_id = ".$productID;
		} elseif (!$companyID && !$productID) {
			$query = "SELECT id FROM product2company";
		} else {
			$query = "SELECT id FROM product2company WHERE product_id = ".$productID." AND company_id = ".$companyID;
		}
		$this->db->query($query);
		return ($this->db->num_rows()) ? true : false;
	}
	
	private function checkIsGOM2JobberLink($gomID = false, $jobberID = false) {

		$gomID=mysql_escape_string($gomID);
		$jobberID=mysql_escape_string($jobberID);

		//analyze different input situations
		if (!$gomID && $jobberID) {
			$query = "SELECT id FROM accessory2jobber WHERE jobber_id = ".$jobberID;
		} elseif (!$jobberID && $gomID) {
			$query = "SELECT id FROM accessory2jobber WHERE accessory_id = ".$gomID;
		} elseif (!$jobberID && !$gomID) {
			$query = "SELECT id FROM accessory2jobber";
		} else {
			$query = "SELECT id FROM accessory2jobber WHERE accessory_id = ".$gomID." AND jobber_id = ".$jobberID;
		}
		$this->db->query($query);
		return ($this->db->num_rows()) ? true : false;
	}	

	private function insertProduct2CompanyLink($productID, $companyID) {

		settype($companyID,"integer");
		settype($productID,"integer");

		$query = "INSERT INTO product2company (product_id, company_id) VALUES (".$productID.", ".$companyID.")";
		$this->db->query($query);
	}

	private function insertGOM2JobberLink($gomID, $jobberID) {

		settype($jobberID,"integer");
		settype($gomID,"integer");

		$query = "INSERT INTO accessory2jobber (accessory_id, jobber_id) VALUES (".$gomID.", ".$jobberID.")";
		$this->db->query($query);
	}	

	private function deleteProduct2CompanyLink($productID, $companyID) {
		settype($companyID,"integer");
		settype($productID,"integer");

		//analyze different input situations
		if (!$productID && $companyID) {
			$query = "DELETE FROM product2company WHERE company_id = ".$companyID;
		} elseif (!$companyID && $productID) {
			$query = "DELETE FROM product2company WHERE product_id = ".$productID;
		} elseif (!$companyID && !$productID) {
			$query = "DELETE FROM product2company";
		} else {
			$query = "DELETE FROM product2company WHERE product_id = ".$productID." AND company_id = ".$companyID;
		}
		$this->db->query($query);
	}
	
	private function deleteGOM2JobberLink($gomID, $jobberID) {
		settype($gomID,"integer");
		settype($jobberID,"integer");

		//analyze different input situations
		if (!$gomID && $jobberID) {
			$query = "DELETE FROM accessory2jobber WHERE jobber_id = ".$jobberID;
		} elseif (!$jobberID && $gomID) {
			$query = "DELETE FROM accessory2jobber WHERE accessory_id = ".$gomID;
		} elseif (!$jobberID && !$gomID) {
			$query = "DELETE FROM accessory2jobber";
		} else {
			$query = "DELETE FROM accessory2jobber WHERE accessory_id = ".$gomID." AND jobber_id = ".$jobberID;
		}
		$this->db->query($query);
	}	


	public function getProductCount($companyID, $supplierID)
	{
		settype($companyID,"integer");
		if (empty($companyID)) {
			if ($supplierID == 0) {
				$query = "SELECT count(*) AS cnt " .
					"FROM ".TB_PRODUCT." p, ".TB_SUPPLIER." s, ".TB_COAT." coat " .
					"WHERE p.supplier_id = s.supplier_id " .
					"AND coat.coat_id = p.coating_id ";
			} else {
				/*$query = "SELECT count(*) AS cnt " .
					"FROM ".TB_PRODUCT." p " .
					"WHERE p.supplier_id = ".(int)$supplierID;
				*/
					$query = "SELECT count(*) AS cnt
								FROM product p, supplier s
								WHERE p.supplier_id = s.supplier_id
								AND s.original_id = ".(int)$supplierID;  
			}
		} else {
			if ($supplierID == 0) {
				$query = "SELECT count(*) AS cnt " .
					"FROM ".TB_PRODUCT." p, product2company p2c, ".TB_COMPANY." c, ".TB_SUPPLIER." s, ".TB_COAT." coat " .
					"WHERE p.product_id = p2c.product_id " .
					"AND p2c.company_id = c.company_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND coat.coat_id = p.coating_id " .
					"AND c.company_id = ".$companyID." ";
			} else {
			/*	$query = "SELECT count(*) AS cnt " .
					"FROM ".TB_PRODUCT." p, product2company p2c,supplier s " .
					"WHERE p.product_id = p2c.product_id " .
					"AND p.supplier_id = ".(int)$supplierID." " .
					"AND p2c.company_id = ".$companyID;
			 
			 */
				$query = "SELECT count(*) AS cnt " .
					"FROM ".TB_PRODUCT." p, product2company p2c,supplier s " .
					"WHERE p.product_id = p2c.product_id " .
					"AND p.supplier_id = s.supplier_id" .
					"AND p2c.company_id = ".(int)$supplierID."".	
					"AND p2c.company_id = ".$companyID;
			}
		}

		$this->db->query($query);

		$numRows = $this->db->num_rows();
		if ($numRows==1) {
			return $this->db->fetch(0)->cnt;
		} else {
			return false;
		}
	}
	
	public function assignProduct2Type($productID, $industryType, $industrySubType){
		if ($industrySubType !== ''){
			$query = "SELECT id FROM ".TB_INDUSTRY_TYPE." WHERE type = '".$industrySubType.
					 "' AND parent = ".
					 " (SELECT id FROM ".TB_INDUSTRY_TYPE." WHERE type = '".$industryType."' AND parent is NULL)";
			$this->db->query($query);
			if ($this->db->num_rows() > 0) {
				$resultSubType = $this->db->fetch(0);
				$this->db->query("SELECT * FROM ".TB_PRODUCT2TYPE." WHERE product_id = ".$productID." AND type_id = ".$resultSubType->id);
				if ($this->db->num_rows() == 0){ 
					$this->db->query("INSERT INTO ".TB_PRODUCT2TYPE." (product_id, type_id) VALUES (".$productID.", ".$resultSubType->id.")");
				}
			} else {
				//create new Type or SubType
				$query = "SELECT id FROM ".TB_INDUSTRY_TYPE." WHERE type = '".$industryType."' AND parent is NULL";
				$this->db->query($query);
				//$resultType = $this->db->fetch(0);
				if ($this->db->num_rows() > 0){
					$resultID = $this->productType->createNewSubType($industryType, $industrySubType);
					$this->db->query("INSERT INTO ".TB_PRODUCT2TYPE." (product_id, type_id) VALUES (".$productID.", ".$resultID.")");
				} else {
					$resultID = $this->productType->createNewType($industryType, $industrySubType);
					$this->db->query("INSERT INTO ".TB_PRODUCT2TYPE." (product_id, type_id) VALUES (".$productID.", ".$resultID.")");
				}
			}
		} else {
			$query = "SELECT id FROM ".TB_INDUSTRY_TYPE." WHERE type = '".$industryType."' AND parent is NULL";
			$this->db->query($query);
			if ($this->db->num_rows() > 0) {
				$resultType = $this->db->fetch(0);
				$this->db->query("INSERT INTO ".TB_PRODUCT2TYPE." (product_id, type_id) VALUES (".$productID.", ".$resultType->id.")");
			} else {
				// create new Type
				$resultID = $this->productType->createNewType($industryType, $industrySubType);
				$this->db->query("INSERT INTO ".TB_PRODUCT2TYPE." (product_id, type_id) VALUES (".$productID.", ".$resultID.")");
			}
		}
	}
	
	public function assignProduct2WasteClass($productID, $wasteClassID) {
		
		$productID=mysql_escape_string($productID);
		$wasteClassID=mysql_escape_string($wasteClassID);
		
		$query = "SELECT id FROM product2waste_class WHERE product_id = ".$productID." AND waste_class_id = ".$wasteClassID."";
		$this->db->query($query);
		
		if ($this->db->num_rows() == 0) {
			$query = "INSERT INTO product2waste_class (product_id, waste_class_id) VALUES (" .
					"".$productID.", ".$wasteClassID.")";
			$this->db->exec($query);
		}
	}
	
	public function unassignProductFromType($productID){
		$this->db->query("DELETE FROM ".TB_PRODUCT2TYPE." WHERE product_id = ".$productID);
	}

	private function selectProductsByCompany($companyID, $supplierID, Pagination $pagination = null,$filter=' TRUE ', $sort=' ORDER BY s.supplier ') {
		settype($companyID,"integer");
		
		if (empty($companyID)) {
			if ($supplierID == 0) {
				$query = "SELECT p.product_id, p.product_nr, p.name, coat.coat_desc, p.supplier_id, s.supplier, p.voclx, p.vocwx, p.percent_volatile_weight, p.percent_volatile_volume " .
					"FROM ".TB_PRODUCT." p, ".TB_SUPPLIER." s, ".TB_COAT." coat " .
					"WHERE p.supplier_id = s.supplier_id " .
					"AND coat.coat_id = p.coating_id " .
					"AND $filter ".
					" $sort ";
			} else {
			/*	$query = "SELECT * " .
					"FROM ".TB_PRODUCT." p " .
					"WHERE p.supplier_id = ".(int)$supplierID;*/
				$query = "SELECT * " .
					"FROM ".TB_PRODUCT." p, ".TB_SUPPLIER." s " .
					"WHERE p.supplier_id = s.supplier_id " .
					"AND s.original_id =".(int)$supplierID. " ORDER BY  p.product_id ASC"; 
				
			}
		} else {
			if ($supplierID == 0) {
				$query = "SELECT p.product_id, p.product_nr, p.name, coat.coat_desc, p.supplier_id, s.supplier, p.voclx, p.vocwx, p.percent_volatile_weight, p.percent_volatile_volume " .
					"FROM ".TB_PRODUCT." p, product2company p2c, ".TB_COMPANY." c, ".TB_SUPPLIER." s, ".TB_COAT." coat " .
					"WHERE p.product_id = p2c.product_id " .
					"AND p2c.company_id = c.company_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND coat.coat_id = p.coating_id " .
					"AND c.company_id = ".$companyID." " .
					"AND $filter ".
					" $sort ";
			} else {
			/*	$query = "SELECT * " .
					"FROM ".TB_PRODUCT." p, product2company p2c " .
					"WHERE p.product_id = p2c.product_id " .
					"AND p.supplier_id = ".(int)$supplierID." " .
					"AND p2c.company_id = ".$companyID;
			*/
				$query = "SELECT * " .
					"FROM ".TB_PRODUCT." p, product2company p2c, ".TB_SUPPLIER." s " .
					"WHERE p.product_id = p2c.product_id " .
					"AND p.supplier_id = s.supplier_id " .
					"AND p2c.company_id = ".$companyID."".
					"AND s.original_id =".(int)$supplierID. " ORDER BY  p.product_id ASC";
			}
		}

		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}

		$this->db->query($query);
		$numRows = $this->db->num_rows();
		if ($numRows) {
			for ($i=0; $i < $numRows; $i++) {
				$productData = $this->db->fetch($i);
				$product = array (
					'product_id'				=>	$productData->product_id,
					'product_nr'				=>	$productData->product_nr,
					'name'						=>	$productData->name,
					'voclx'						=>	$productData->voclx,
					'vocwx'						=>	$productData->vocwx,
					'supplier_id'				=>	$productData->supplier_id,	//clean it, was $productData->supplier why?
					'supplier'					=>	$productData->supplier,
					'coating'					=>	$productData->coat_desc,
					'percent_volatile_weight'	=>	$productData->percent_volatile_weight,
					'percent_volatile_volume'	=>	$productData->percent_volatile_volume,
				);
				$products[] = $product;
			}

			return $products;
		} else {

			return false;
		}
	}




	public function checkForAvailableMSDS($productID) {
		settype($productID,"integer");

		$query = "SELECT real_name FROM ".TB_MSDS_FILE." WHERE product_id = ".$productID." LIMIT 1";
		$this->db->query($query);

		if ($this->db->num_rows()) {
			return "../msds/" . $this->db->fetch(0)->real_name;
		} else return false;
	}
	
	public function checkForAvailableTechSheet($productID) {
		settype($productID,"integer");

		$query = "SELECT real_name FROM ".TB_TECH_SHEET_FILE." WHERE product_id = ".$productID." LIMIT 1";
		$this->db->query($query);

		if ($this->db->num_rows()) {
			return "../tech_sheet/" . $this->db->fetch(0)->real_name;
		} else return false;
	}

	private function getMaxLenghtSupplierAndProductNR($productList) {
		foreach($productList as $value) {
			$supplierLength[] = strlen($value['supplier_id']);
			$productNrLength[] = strlen($value['product_nr']);
		}
		$maxValues = array (
			'supplier'	=> max($supplierLength),
			'productNR'	=> max($productNrLength)
		);
		return $maxValues;
	}




	private function selectProduct($product, $byField, $companyID = 0) {

		$product=mysql_escape_string($product);
		$byField=mysql_escape_string($byField);
		settype($companyID,"integer");

		if ($companyID === 0) {
			$query = "SELECT p.product_id " .
				"FROM ".TB_PRODUCT." p " .
				"WHERE p.".$byField." = '".$product."'";

		} else {
			$query = "SELECT p.product_id " .
				"FROM ".TB_PRODUCT." p, product2company p2c " .
				"WHERE p.product_id = p2c.product_id " .
				"AND p.".$byField." = '".$product."' " .
				"AND p2c.company_id = ".$companyID;

		}

		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$productsData = $this->db->fetch_all();
			for ($i = 0; $i < count($productsData); $i++) {
				$productDetails = $this->getProductDetails($productsData[$i]->product_id);
				$productDetails['coating'] = $productDetails['coating_id'];
				$productDetails['msdsLink'] = $this->checkForAvailableMSDS($productsData[$i]->product_id);
				$selectedProducts[] = $productDetails;
			}
			return $selectedProducts;
		} else {
			return false;
		}
	}
}
?>