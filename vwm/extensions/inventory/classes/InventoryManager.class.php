<?php
class InventoryManager {
        
        private $db;
    
	function __construct($db) {
                $this->db=$db;
        }

	public function getProductUsageGetAll(DateTime $beginDate, DateTime $endDate, $category, $categoryID, $productID = null, $sortStr = null) {

        $categoryDependedSql = "";
		$tables = TB_USAGE." m, ".TB_MIXGROUP." mg";
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

			$tables .= ", ".TB_PRODUCT." p";
		
		
		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name,p.product_id, pi.* ";
				
		$query .= " FROM {$tables} " .
				  " LEFT JOIN product2inventory pi ON p.product_id = pi.product_id AND pi.facility_id = {$categoryID} " .
				  " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN ".$beginDate->getTimestamp()." AND ".$endDate->getTimestamp()." ";

		if ($productID){
			$query .= "AND p.product_id = {$productID} ";
		}
		$query .= " GROUP BY mg.product_id ";
		if ($sortStr){
			$query .= $sortStr;
		}else{
			$query .= " ORDER BY p.product_id ";
		}
/*			
		$query = "SELECT sum(mg.quantity_lbs) as sum, p.product_nr, p.name,p.product_id ";
		if (!$productID){
			$query .= " ,pi.* ";
			
		}				
		$query .= " FROM {$tables} " ;
		if (!$productID){
			$query .= " LEFT JOIN product2inventory pi ON p.product_id = pi.product_id AND pi.facility_id = {$categoryID} ";
			
		}				  
		$query .= " WHERE {$categoryDependedSql} " .
					"AND p.product_id = mg.product_id " .
					"AND m.mix_id = mg.mix_id " .
					"AND m.creation_time BETWEEN '".$beginDate->getTimestamp()."' AND '".$endDate->getTimestamp()."'";

		if ($productID){
			$query .= "AND p.product_id = {$productID} ";
			$inventoryDetails = $this->checkInventory( $productID, $categoryID );
		}
		$query .= " GROUP BY mg.product_id " .
				  " ORDER BY p.product_id ";			
*/		

		//echo $query;
		$this->db->query($query);

		
		$arr = $this->db->fetch_all_array();

		// If no usage for the period of the last delivery
		if (!$arr){
			if ($productID){
				$inventoryDetails = $this->checkInventory( $productID, $categoryID );
				$arr[] = $inventoryDetails;
				//var_dump($arr);
			}			
		}
		
		$productUsageData = array();
			foreach($arr as $b) {
				$productinv = new ProductInventory($this->db, $b);
				$productUsageData[] = $productinv;                        
			}

		return $productUsageData;
	}
	
	public function getProductsSupplierList($facilityID, $productID = null , $sortStr = null) {

			$tables = " ".TB_PRODUCT." p, " . TB_SUPPLIER . " s ,  product2inventory pi "; //m.department_id = d.department_id AND 
			
			


			$query	=    "SELECT p.supplier_id, p.product_nr , s.original_id , di.discount, di.discount_id, s.supplier, pi.product_id , pi.in_stock_unit_type";

			$query .=	" FROM {$tables} " .
						" LEFT JOIN discounts2inventory di ".
						" ON di.product_id = pi.product_id AND di.facility_id = {$facilityID} ".
						" WHERE p.supplier_id  = s.supplier_id AND pi.facility_id = {$facilityID} " ;
			if ($productID){
				$query .=   " AND p.product_id  = {$productID} ";
			}
				$query .=   " AND p.product_id = pi.product_id ";
			
			if ($sortStr){
				$query .=   " {$sortStr} ";
			}
			
			$this->db->query($query);
//echo $query;
			$arr = $this->db->fetch_all_array();

			$SupData = array();
				foreach($arr as $b) { 
					if ( $b['supplier_id'] <> $b['original_id'] ){
						$query = "SELECT supplier FROM " . TB_SUPPLIER . " WHERE original_id=supplier_id AND original_id=" .$b['original_id']. " ";
						
						$this->db->query($query);
						$suppliername = $this->db->fetch_all_array();
						$b['supplier'] = $suppliername[0]['supplier'];
						$SupData[] = $b;
					}else{
						$SupData[] = $b;
					}

				}

			return $SupData;		
		
/*		
		}else{		
			$query	=	"SELECT DISTINCT di.*, s.supplier ";

			$query .=	" FROM discounts2inventory di, " . TB_SUPPLIER . " s   " .
						" WHERE di.facility_id =  {$facilityID} AND di.supplier_id = s.original_id AND s.supplier_id = s.original_id";	
			$this->db->query($query);
echo $query;
			$SupData = $this->db->fetch_all_array();
			return $SupData;	
*/				


	

	}	
	
	public function getSupplierOrders($facilityID = null, $productID = null, Pagination $pagination = null, Sort $sortStr = null) {
        $time = new DateTime('first day of this month');

        $query = "SELECT io.* ";
				
		$query .=	" FROM inventory_order io" .

					" WHERE ";
		 
		if ($facilityID != null){
			$query .=	" io.order_facility_id = {$facilityID} AND ";
		}		
		if ($productID != null){
			$query .=	" io.order_product_id = {$productID} ";
		}else{
			$query .=	" io.order_created_date >= {$time->getTimestamp()} ";
		}
		
		if ($sortStr != null) {
			//, Pagination $pagination = null
			$query .= $sortStr;
		}else{
			$query .=" ORDER BY io.order_completed_date DESC ";
		}	
		
		if (isset($pagination)) {
			//, Pagination $pagination = null
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}			
		//echo $query;
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$arr = $this->db->fetch_all_array();
		//echo $query;
		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
	}
	
	public function getCountSupplierOrders($facilityID) {
	
		$query = "SELECT COUNT(*) cnt FROM inventory_order WHERE order_facility_id = {$facilityID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}	
	
	public function getSupplierOrdersStatusList() {
        $query = "SELECT * FROM inventory_order_status ";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		//echo $query;
		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
	}	
	
	public function getSupplierOrderDetails($facilityID,$orderID) {
        $time = new DateTime('first day of this month');

        $query = "SELECT io.*";
				
		$query .=	" FROM inventory_order io " .

					" WHERE io.order_id = {$orderID}  AND io.order_created_date >= {$time->getTimestamp()}";

		$this->db->query($query);
	
		$arr = $this->db->fetch_all_array();
		
		//echo $query;
		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b;
            
			}
	
		return $SupData;
	}
	
	public function getLastOrderId() {
        $query = "SELECT io.order_id ";
				
		$query .=	" FROM inventory_order io " .

					" ORDER BY io.order_created_date DESC LIMIT 1";

		$this->db->query($query);
	
		$arr = $this->db->fetch_all_array();
		
		//echo $query;
		$data = array();
			foreach($arr as $b) {

					$data = $b;
            
			}
	
		return $data;
	}	
	
	public function updateSupplierOrder( $data ) {

            $query = "UPDATE inventory_order SET order_status = {$data['status']}, order_completed_date = {$data['order_completed_date']} WHERE order_id = {$data['order_id']}";			



		$this->db->query($query);

		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function getSupplierDiscounts($facilityID, $supplierID ) {
            
        $query = "SELECT di.*, s.supplier ";
				
		$query .= " FROM supplier s " .
				  " LEFT JOIN discounts2inventory di ".
				  " ON di.supplier_id =  {$supplierID} AND di.facility_id = {$facilityID} ".
				  " WHERE s.supplier_id =  {$supplierID} ";


		//echo $query;
		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}
	
	public function getDiscountsBySupplier($supplierID ) {
            
        $query = "SELECT di.* ";
				
		$query .= " FROM discounts2inventory di ".
				  " WHERE di.supplier_id =  {$supplierID} ";


		//echo $query;
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}		
		$arr = $this->db->fetch_all_array();

		$SupData = array();
			foreach($arr as $b) {
					$SupData[] = $b;
			}
		return $SupData;
	}	
	
	public function updateSupplierDiscounts( $form ) {

										
		if ($form['discount_id'] == null){
			$query = "INSERT INTO discounts2inventory VALUES (NULL,". $form['facilityID'] .",". $form['supplier_id'] .",". $form['product_id'] .",". mysql_real_escape_string($form['discount']) .") ";
				

		}else{
            $query = "UPDATE discounts2inventory SET discount = ".mysql_real_escape_string($form['discount'])." WHERE discount_id = {$form['discount_id']}";			

		}

		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}	
	
	public function getSupplierSettings($facilityID) {
            
        $query = "SELECT * FROM email2inventory WHERE facility_id = {$facilityID} ";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}
	
	public function updateSupplierSettings( $form ) {

										
		if ($form['email_id'] == null){
			$query = "INSERT INTO email2inventory VALUES (NULL,". $form['facilityID'] .",'". $form['email_all'] ."','". $form['email_manager'] ."') ";
				

		}else{
            $query = "UPDATE email2inventory SET 
			email_all = '".  mysql_real_escape_string($form['email_all'])."',
			email_manager = '".mysql_real_escape_string($form['email_manager'])."'
			WHERE email_id = {$form['email_id']} ";			

		}

		$this->db->query($query);
		
		if(mysql_error() == '') {
			return true;
		} else {
			throw new Exception(mysql_error());
		}
	}
	
	public function getInventoryByID($inventoryID) {
            
        $query = "SELECT * FROM product2inventory WHERE inventory_id = {$inventoryID} ";

		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();
		
		
		$SupData = array();
			foreach($arr as $b) {

					$SupData = $b;
            
			}
	
		return $SupData;
	}
	
	public function getInventoryPrductIdByFacility($facilityID, Pagination $pagination = null) {
            
        $query = "SELECT product_id FROM product2inventory WHERE facility_id = {$facilityID} ";
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		$this->db->query($query);
		
		$arr = $this->db->fetch_all_array();

		$SupData = array();
			foreach($arr as $b) {

					$SupData[] = $b['product_id'];
            
			}
	
		return $SupData;
	}	
	
	public function getCountInventoryPrduct($facilityID) {
	
		$query = "SELECT COUNT(*) cnt FROM product2inventory WHERE facility_id = {$facilityID}";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}	

	public function checkInventory( $productID, $facilityID ) {
	
		$query =	"SELECT pi.* , p.product_nr  FROM product2inventory pi , ".TB_PRODUCT." p WHERE pi.product_id = ".$productID." AND pi.facility_id = ".$facilityID." AND p.product_id = {$productID} ";				
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();

		$data = array();
			foreach($arr as $b) {
					$data = $b;
			}
		return $data;

	}	
	
	
	public function getInventoryInfoForProduct( $productID, $facilityID, $mixID = null) {
							//ORDERS FOR THIS PODUCT
								$orderList = $this->getSupplierOrders($facilityID, $productID);		

								if ($orderList[0]['order_completed_date'] != null && $orderList[0]['order_status'] == OrderInventory::COMPLETED){

									//$dateBegin = DateTime::createFromFormat('U', $orderList[0]['order_completed_date']);
									$dateBegin = $orderList[0]['order_completed_date'];
								}else{
									$dateBegin = new DateTime('first day of this month');
									$dateBegin = $dateBegin->getTimestamp();
								}
							//
								$endDate = new DateTime();
						
				
	$query = "SELECT sum(mg.quantity_lbs) as sum, mg.quantity_lbs, p.product_nr, pi.* 
				FROM mix m, mixgroup mg, department d , product p 
				LEFT JOIN product2inventory pi ON p.product_id = pi.product_id 
				WHERE m.department_id = d.department_id
				AND p.product_id = ".$productID."
				AND pi.facility_id = ".$facilityID."
				AND d.facility_id = ".$facilityID."
				AND p.product_id = mg.product_id 
				AND m.creation_time BETWEEN ".$dateBegin." AND ".$endDate->getTimestamp()."
				AND m.mix_id = mg.mix_id";	
	//$query .= " AND m.mix_id = ".$mixID.""; 

		//echo $query;
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		
		$arr = $this->db->fetch_all_array();
		$inventory = $arr[0];
		$inventory['product_id'] = ($inventory['product_id'] == null) ? $productID : $inventory['product_id'];
		return $inventory;
	}	
	
	
	/**
	 * Check for active orders by product
	 * @param int $product_id
	 * @param int $facility_id
	 * @return boolean false if no active orders, true if this product has order with any not completed status
	 */
	public function isThereActiveOrdersByProductID($product_id, $facility_id){
				
        $query = "SELECT * 
				FROM inventory_order 
				WHERE order_product_id = {$product_id} 
				AND order_facility_id = {$facility_id}
				AND order_status NOT IN (".OrderInventory::COMPLETED.", ".OrderInventory::CANCELED.")";
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		} else {
			return true;
		}				
	}
	
	public function runInventoryOrderingSystem( $mix ) {
		$productObjArray = $mix->products;
		$text = $this->getEmailText($mix->facility_id);
		// TODO reduce the amount of product in stock with type convert!!
		foreach ($productObjArray as $productObj){

			$inventory = $this->getInventoryInfoForProduct($productObj->product_id, $mix->facility_id, $mix->mix_id);

			if (!$inventory) {
				throw new Exception("No inventory found :(");
			}
			$inventory['facility_id'] = $mix->facility_id;
			$productUsageData = new ProductInventory($this->db, $inventory);

			if ($productUsageData->id == null){
				
				$productUsageData->save();

			}else if ($productUsageData->in_stock - $inventory['sum']  <= $productUsageData->limit){
				//$productUsageData->in_stock - $productObj->quantity  <= $productUsageData->limit
				$isThereActiveOrders = $this->isThereActiveOrdersByProductID($productUsageData->product_id, $mix->facility_id);
				
				if (!$isThereActiveOrders){
					//Create new Order
					$newOrder = new OrderInventory($this->db);
					//TODO get price
					$price = 10;
					$newOrder->order_product_id = $productUsageData->product_id;
					$newOrder->order_facility_id = $mix->facility_id;
					$newOrder->order_name = 'Order for product "'.$productUsageData->product_nr.'"';
					$newOrder->order_total = $productUsageData->amount * $price;
					$newOrder->order_amount = $productUsageData->amount;
					$newOrder->save();

					$supplierDetails = $this->getProductsSupplierList($mix->facility_id,$productUsageData->product_id);
					//TODO NEED supplier email
					$supplierDetails[0]['email'] = 'jgypsyn@gyantgroup.com';			

					$this->checkSupplierEmail($supplierDetails[0]['email'],$text);
				}else{
/*					$supplierDetails = $this->getProductsSupplierList($mix->facility_id,$productUsageData->product_id);
					$supplierDetails[0]['email'] = '2reckiy@gmail.com';
					$this->checkSupplierEmail($supplierDetails[0]['email']);
*/					
					
				}
				
			}else{
				
			}			
		}
		
	}

	public function unitTypeConverter($inventory) {
		// UNITTYPE CONVERTER
		$unittype = new Unittype($this->db);
		$product = new Product($this->db);
		$productDetails = $product->getProductDetails($inventory->product_id);
		$densityObj = new Density($this->db, $productDetails['densityUnitID']);

		//	check density
		if (empty($productDetails['density']) || $productDetails['density'] == '0.00') {
			$productDetails['density'] = false;
			$isThereProductWithoutDensity = true;
		}

		// get Density Type
		$densityType = array(
			'numerator' => $unittype->getDescriptionByID($densityObj->getNumerator()),
			'denominator' => $unittype->getDescriptionByID($densityObj->getDenominator())
		);


		$defaultType = $unittype->getUnittypeClass($inventory->in_stock_unit_type);
		$unittypeDetails = $unittype->getUnittypeDetails($inventory->in_stock_unit_type);
		$unitTypeConverter = new UnitTypeConverter($defaultType);
		$quantitiWeightSum = $unitTypeConverter->convertFromTo($inventory->usage, "lb", $unittypeDetails['description'], $productDetails['density'], $densityType); //	in weight
		//quantity array in gallon
		/* $quantitiVolumeSum = $unitTypeConverter->convertFromTo($data[0]->usage,
		  "us gallon",
		  $unittypeDetails['description'],
		  $productDetails['density'],
		  $densityType);//	in volume ;
		 * 
		 */
		//$inventory->set_sum();

		if ($quantitiWeightSum != null && $quantitiWeightSum != 0 && $quantitiWeightSum != ''){
			$data = array();
			$data['usage'] = number_format($quantitiWeightSum, 2, '.', '');
			$data['unittype'] = $unittypeDetails['name'];

			return  $data;
		}else{
			return false;
		}
		
		//var_dump($inventory->product_nr,$quantitiWeightSum,$unittypeDetails['description'],$inventory->usage);

	}

	private function checkSupplierEmail($email,$text){
		$user = new User($this->db);
		$userDetails = $user->getUserDetails($_SESSION['user_id']);
		if (isset($email) && $email != ''){
			$this->sendEmailToAll($email,$userDetails['email'], $text['email_all']);
		}else{
			$this->sendEmailToManager($userDetails['email'],$text['email_manager']);
		}
	}	

	private function sendEmailToAll($supplierEmail, $userEmail, $text){
		$hash = array();
		$hash = $this->generateOrderHash($supplierEmail);
		$userEmailb64 = base64_encode($userEmail);


		$links  = "\r\n"."For confirm this order click here: <a href='http://www.vocwebmanager.com/vwm/?action=processororder&category=inventory&to={$userEmailb64}&hash={$hash[confirm]}'>CONFIRM</a>"."\r\n"."For cancel this order click here: <a href='http://localhost/voc_src/vwm/?action=processororder&category=inventory&to={$userEmailb64}&hash={$hash[cancel]}'>CANCEL</a>";
		$text .= $links;
		$h = 'Cc: '.$userEmail. "\r\n";
		$subject = "*** New Order on www.vocwebmanager.com *** \r\n\r\n";

/*
$data = $text;
$data.= $h ;
$data.= $subject ;


$file="text.txt";
//если файла нету... тогда

$fp = fopen($file, "a"); // ("r" - считывать "w" - создавать "a" - добовлять к тексту), мы создаем файл
fwrite($fp, $data);
fclose ($fp);
*/	
		
		mail('jgypsyn@gyantgroup.com', $subject, $text, $h);
		mail('denis.nt@kttsoft.com ', $subject, $text, $h);

	}	
	
	public function sendEmailToManager($userEmail,$text){
		
		$subject = "*** New Order on www.vocwebmanager.com ***";

		mail($userEmail, $subject, $text);		
		
	}
	
	private function getEmailText($facilityID){
		$query = "SELECT * FROM email2inventory WHERE facility_id = {$facilityID} ";
		$this->db->query($query);
		$arr = $this->db->fetch_all_array();
		$text = array();
			foreach($arr as $b) {
					$text = $b;
			}
		return $text;		
	}	
	
	
	
	private function generateOrderHash($forIncode){
		$newHash = array();
		$orderID = $this->getLastOrderId();

		$newHash['order_id'] = $orderID['order_id'];
		$newHash['cancel'] = md5($newHash['order_id']."cancel".time().$forIncode);
		$newHash['confirm'] = md5($newHash['order_id']."confirm".time().$forIncode);
		$newHash['sent_date'] = time();
		$this->saveOrderHash($newHash);
		return $newHash;
		
	}	

	private function saveOrderHash($hash){
		
		$query = "INSERT INTO inventory_order_hash VALUES (NULL,". $hash['order_id'] .",'cancel','". $hash['cancel'] ."','". $hash['sent_date'] ."') ";
		$this->db->query($query);

		$query = "INSERT INTO inventory_order_hash VALUES (NULL,". $hash['order_id'] .",'confirm','". $hash['confirm'] ."','". $hash['sent_date'] ."') ";
		$this->db->query($query);

	}	
	
	
	/**
	 *
	 * @param type $hash
	 * @return OrderInventory 
	 */
	public function getOrderDetailsByHash($hash){		
		
		$query = "SELECT ioh.*, io.* , pi.amount 
				FROM inventory_order_hash ioh , inventory_order io , product2inventory pi 
				WHERE ioh.hash= '".mysql_escape_string($hash)."' 
				AND ioh.order_id = io.order_id 
				AND io.order_product_id = pi.product_id 
				AND io.order_status != ".OrderInventory::COMPLETED;
		
		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return false;
		}
				
		$arr = $this->db->fetch_all_array();						
		return new OrderInventory($this->db, $arr[0]);		
	}	
	
	public function getDefaultTypesAndUnitTypes($companyID) {

		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		$companyEx = 1;
		if (!$unitTypeEx) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
			$companyEx = 0;
		}

        $k = 1;
		$count = 1;
		$flag = 1;
		$typeEx = Array();

                // 80% of U.S. customers use the system USAWeight, so make it default
                //$usWgt = Array('OZS', 'LBS', 'GRAIN', 'CWT');
				$usWgt = Array('7', '2', '12', '20');
                for ($ii=0; $ii<count($unitTypeEx); $ii++){
                    for ($jj=0; $jj<count($usWgt); $jj++){
                        if ($unitTypeEx[$ii]['unittype_id'] == $usWgt[$jj]){
                                $typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$ii]['unittype_id']);
                        }
                    }
                }
                if ($typeEx[0] == ''){
                    $typeEx[0] = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
                }

		while ($unitTypeEx[$k]){
			$idn = $cUnitTypeEx->getUnittypeClass($unitTypeEx[$k]['unittype_id']);

			for($j=0; $j < $count; $j++) {
				if ($idn == $typeEx[$j] ) {
					$flag=0;
					break;
				}
			}
			if ($flag) {
				$typeEx[$count] = $idn;
				$count++;
			}
			$k++;
			$flag = 1;
		}

		return Array("typeEx" => $typeEx, "companyEx" => $companyEx, "unitTypeEx" => $unitTypeEx);
	}
	
	public function getUnitTypeList($companyID) {
		$unittype = new Unittype($this->db);
		$cUnitTypeEx = new Unittype($this->db);
		$unitTypeEx = $cUnitTypeEx->getUnitTypeExist($companyID);
		if ($unitTypeEx === null) {
			$unitTypeEx = $cUnitTypeEx->getClassesOfUnits();
		}
		$unitTypeClass = $cUnitTypeEx->getUnittypeClass($unitTypeEx[0]['unittype_id']);
		$unittypeList = $unittype->getUnittypeListDefaultByCompanyId($companyID, $unitTypeClass);
		return $unittypeList;
	}	
}