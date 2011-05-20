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
	
	public function getList($companyID, Pagination $pagination = null, $idArray = null) {
		
		$companyID = mysql_escape_string($companyID);
		$query = "SELECT * FROM " . TB_PFP . " WHERE company_id = $companyID";
		
		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
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
}
?>