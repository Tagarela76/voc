<?php
/**
 * PreFormulatedProductsManager (PFPManager)
 */

class PFPManager
{
	private $db;
	
	function PFPManager($db)
	{
		$this->db = $db;
	}
	
	public function isUnique($description,$companyID) {
		
		$description = mysql_escape_string($description);
		$companyID = mysql_escape_string($companyID);
		$query = "SELECT count(id) as 'c' from ". TB_PFP . " WHERE description = '$description' AND company_id = $companyID LIMIT 1";
		$this->db->query($query);
		
		$row = $this->db->fetch_array(0);
		$c = intval($row['c']);
		
		return $c > 0 ? FALSE : TRUE;
	}
	
	public function countPFP($companyID) {
		
		$companyID = mysql_escape_string($companyID);
		$query = "SELECT count(id) as 'c' from ". TB_PFP . " WHERE company_id = $companyID";
		$this->db->query($query);
		
		$row = $this->db->fetch_array(0);
		$c = intval($row['c']);
		return $c;
	}
	
	public function getCompaniesByPfpID($pfpID){
		$query = "SELECT c.company_id, c.name FROM ".TB_COMPANY." c, ".TB_PFP2COMPANY." p2c WHERE c.company_id=p2c.company_id AND p2c.pfp_id=".$pfpID;
		$this->db->query($query);
		$rows = $this->db->fetch_all();
		foreach ($rows as $row){
			$list[$row->company_id] = $row->name;
		}
		
		return $list;
	}

	public function getList($companyID = null, Pagination $pagination = null, $idArray = null) {
		
		if ($companyID){
		$companyID = mysql_escape_string($companyID);
		//$query = "SELECT * FROM " . TB_PFP . " WHERE company_id = $companyID";
		$query = "SELECT pfp.id, pfp.description, pfp.company_id FROM " . TB_PFP . " pfp, ".TB_PFP2COMPANY." pfp2c WHERE pfp.id=pfp2c.pfp_id AND pfp2c.company_id = $companyID ";
		} else {
			$query = "SELECT * FROM " . TB_PFP . " WHERE 1 ";
		}
		if (isset($pagination)) {
			$query .=  "ORDER BY pfp.id LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
		
		if(isset($idArray) and is_array($idArray) and count($idArray) > 0) {
			
			$count = count($idArray);
			$query .= " AND id IN ( ";
			
			for($i=0; $i<$count; $i++) {
				$query .= $idArray[$i];
				if($i < $count-1) {
					$query .= ", ";
				} 
			}
			
			
			$query .= " )";
		}
		
		if (!$companyID){
			$query .= " ORDER BY id";
		}
		
		$this->db->query($query);
		
		//Init PFPProducts for each PFP...
		$pfpArray = $this->db->fetch_all_array();
		$count = count($pfpArray);
		//var_dump($pfpArray);
		
		$pfps = array(); //Array of objects PFP
		
		for($i=0; $i< $count; $i++) {
			
			$PFPProductsArray = array();
			
			$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray[$i]['id'];
			//echo "<br/>$getProductsQuery";
			$this->db->query($getProductsQuery);
			$products = $this->db->fetch_all_array();
			//var_dump($products);
			foreach($products as $p) {
				$prodtmp = new PFPProduct($this->db); 
				$prodtmp->setRatio($p['ratio']);
				$prodtmp->initializeByID($p['product_id']);
				$prodtmp->setIsPrimary($p['isPrimary']);
				$PFPProductsArray[] = $prodtmp;
			}
			
			//var_dump($PFPProductsArray);
			$pfp = new PFP($PFPProductsArray);
			$pfp->setID($pfpArray[$i]['id']);
			$pfp->setDescription($pfpArray[$i]['description']);
			$pfp->products = $PFPProductsArray;
			$pfps[] = $pfp;
		}
		return $pfps;
	}
	
	public function add(PFP $product, $companyID) {
		
		
		
		$count = count($product->products);
		if($count == 0) {
		
			return false;
		}
		
		//$this->db->beginTransaction();
		$queryAddPFP = "INSERT INTO " . TB_PFP . " (description,company_id) VALUES ('". $product->getDescription() . "', $companyID)";
		
		$this->db->query($queryAddPFP);
		
		$pfpID = $this->db->getLastInsertedID();
		
		$queryAddPFPRelation2Company = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id ,company_id) VALUES (".$pfpID.", ".$companyID.")";
		$this->db->query($queryAddPFPRelation2Company);
		
		$queryInsertPFPProducts = "INSERT INTO " . TB_PFP2PRODUCT . "(ratio,product_id,preformulated_products_id,isPrimary) VALUES ";
		for($i=0; $i<$count; $i++) {
			$isPrimary = $product->products[$i]->isPrimary() ? "true" : "false";
			$queryInsertPFPProducts .= " ( " . $product->products[$i]->getRatio() .
										", " .$product->products[$i]->product_id. " , $pfpID , $isPrimary) ";
			if($i < $count-1) {
				$queryInsertPFPProducts .= " , ";
			}
		}
		
		$this->db->query($queryInsertPFPProducts);
	}
	
	public function remove(PFP $product) {
		
		$this->removeProducts($product->getId());
		$delQuery = "DELETE FROM " . TB_PFP . " WHERE id = ". $product->getId();
		$this->db->query($delQuery);
	}
	
	public function removeList($pfpArray) {
		
		//$this->db->beginTransaction();
		foreach($pfpArray as $pfp) {
			
			$this->remove($pfp);
		}
	}
	
	public function unassignPFPFromCompanies($pfpID){
		$query = "DELETE FROM ".TB_PFP2COMPANY." WHERE pfp_id=".$pfpID;
		$this->db->query($query);
		if (mysql_errno() == 0){
			$error = "";
		} else {
			$error = "Error!";
		}
		
		return $error;
	}
	
	public function assignPFP2Company($pfpID, $companyID){
		$query = "INSERT INTO ".TB_PFP2COMPANY." (pfp_id, company_id) VALUES (".$pfpID.", ".$companyID.")";
		$this->db->query($query);
		if (mysql_errno() == 0){
			$error = "";
		} else {
			$error = "Error!";
		}
		
		return $error;
	}

	public function update(PFP $from, PFP $to) {
		
		//echo "FROM:";
		//var_dump($from);
		//echo "TO:";
		//var_Dump($to);
		
		$this->db->beginTransaction();
		
		$this->removeProducts($from->getId());
		
		$updatePFPQuery = "UPDATE " . TB_PFP . " SET " .
							" description = '" . mysql_escape_string($to->getDescription()) . "'
							WHERE id = " . $from->getId();
		//echo "<br/>".$updatePFPQuery;
		$this->db->query($updatePFPQuery);
		
		$count = count($to->products);
		$queryInsertPFPProducts = "INSERT INTO " . TB_PFP2PRODUCT . "(ratio,product_id,preformulated_products_id,isPrimary) VALUES ";
		for($i=0; $i<$count; $i++) {
			$isPrimary = $to->products[$i]->isPrimary() ? "true" : "false";
			$queryInsertPFPProducts .= " ( " . $to->products[$i]->getRatio() .
										", " .$to->products[$i]->product_id. " , {$from->getId()}, $isPrimary ) ";
			if($i < $count-1) {
				$queryInsertPFPProducts .= " , ";
			}
		}
		
		//echo "<br/>$queryInsertPFPProducts";
		$this->db->query($queryInsertPFPProducts);
		//exit;
		//$this->db->commitTransaction();
	}
	
	private function removeProducts($pfpID) {
		$pfpID = mysql_escape_string($pfpID);
		$query = "DELETE FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = $pfpID";
		//echo "<br/>".$query;
		$this->db->query($query);
	}
	
	public function getPFP($id) {
		$id = mysql_escape_string($id);
		$query = "SELECT * FROM " . TB_PFP . " WHERE id = $id";
		
		$this->db->query($query);
		
		//Init PFPProducts for each PFP...
		$pfpArray = $this->db->fetch_array(0);
		
		//var_dump($pfpArray);
		
		$PFPProductsArray = array();
		
		$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray['id'];
		//echo "<br/>$getProductsQuery";
		$this->db->query($getProductsQuery);
		$products = $this->db->fetch_all_array();
		//var_dump($products);
		foreach($products as $p) {
			$prodtmp = new PFPProduct($this->db); 
			$prodtmp->setRatio($p['ratio']);
			$prodtmp->setIsPrimary($p['isPrimary']);
			$prodtmp->setId($p['id']);
			$prodtmp->initializeByID($p['product_id']);
			$PFPProductsArray[] = $prodtmp;
		}
		
		//var_dump($PFPProductsArray);
		$pfp = new PFP($PFPProductsArray);
		$pfp->setID($pfpArray['id']);
		$pfp->setDescription($pfpArray['description']);
		$pfp->products = $PFPProductsArray;
		
		return $pfp;
	}
	public function getPFPProduct($id) {
		$prodtmp = new PFPProduct($this->db); 
		
	}
	
	public function isPFPModified($pfpOld, $pfp){
		$result = FALSE;
		if ($pfp->getDescription() == $pfpOld->getDescription()){
			if (count($pfp->products) == count($pfpOld->products)){
				for ($i=0; $i<count($pfp->products); $i++){
					if ($pfp->products[$i]->product_id == $pfpOld->products[$i]->product_id){
						if ($pfp->products[$i]->isPrimary() == $pfpOld->products[$i]->isPrimary()){
							if ($pfp->products[$i]->getRatio() == $pfpOld->products[$i]->getRatio()){
								
							} else {
								$result = TRUE;
								break;
							}
						} else {
							$result = TRUE;
							break;
						}
					} else {
						$result = TRUE;
						break;
					}
				}
			} else {
				$result = TRUE;
			}
		} else {
			$result = TRUE;
		}
		
		return $result;
	}
	
	public function isCreaterPFP($pfpID, $companyID){
		$result = FALSE;
		$query = "SELECT company_id FROM ".TB_PFP." WHERE id=".$pfpID;
		$this->db->query($query);
		$companyCreaterID = $this->db->fetch(0)->company_id;
		if ($companyCreaterID == $companyID){
			$result = TRUE;
		}
		
		return $result;
	}
}
?>