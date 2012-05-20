<?php

/**
 * PreFormulatedProductsManager (PFPManager)
 */
class PFPManager {

	/**
	 *
	 * @var db
	 */
	private $db;

	function PFPManager($db) {
		$this->db = $db;
	}

	public function isUnique($description, $companyID) {

		$description = mysql_escape_string($description);
		$companyID = mysql_escape_string($companyID);
		$query = "SELECT count(id) as 'c' from " . TB_PFP . " WHERE description = '$description' AND company_id = $companyID LIMIT 1";
		$this->db->query($query);

		$row = $this->db->fetch_array(0);
		$c = intval($row['c']);

		return $c > 0 ? FALSE : TRUE;
	}

	public function countPFP($companyID = 0, $searchString = '') {

		$companyID = mysql_escape_string($companyID);

		$query = "SELECT count(pfp.id) as c " .
				"FROM ".TB_PFP." pfp, ".TB_PRODUCT." p, ".TB_PFP2PRODUCT." pfp2p " .
				"WHERE p.product_id = pfp2p.product_id AND pfp2p.preformulated_products_id = pfp.id AND (" .
					"pfp.description LIKE ('%".$this->db->sqltext($searchString)."%') OR " .
					"p.name LIKE ('%".$this->db->sqltext($searchString)."%') " .
				")";

		if ($companyID != 0) {
			$query .= " AND company_id = $companyID";
		}

		$query .= " GROUP BY pfp.id";

		$this->db->query($query);

		$row = $this->db->fetch_array(0);
		$c = intval($row['c']);
		return $c;
	}

	public function getCompaniesByPfpID($pfpID) {
		$query = "SELECT c.company_id, c.name FROM " . TB_COMPANY . " c, " . TB_PFP2COMPANY . " p2c WHERE c.company_id=p2c.company_id AND p2c.pfp_id=" . $pfpID;
		$this->db->query($query);
		$rows = $this->db->fetch_all();

		foreach ($rows as $row) {
			$list[$row->company_id] = $row->name;
		}

		return $list;
	}

	public function getList($companyID = null, Pagination $pagination = null, $idArray = null) {

		if ($companyID) {
			$companyID = mysql_escape_string($companyID);
			$query = "SELECT pfp.id, pfp.description, pfp.company_id FROM " . TB_PFP . " pfp, " . TB_PFP2COMPANY . " pfp2c WHERE pfp.id=pfp2c.pfp_id AND pfp2c.company_id = $companyID ";
		} else {
			$query = "SELECT * FROM " . TB_PFP . " pfp WHERE 1 ";
		}
		if (isset($pagination)) {
			$query .= "ORDER BY pfp.id LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		if (isset($idArray) and is_array($idArray) and count($idArray) > 0) {

			$count = count($idArray);
			$query .= " AND id IN ( ";

			for ($i = 0; $i < $count; $i++) {
				$query .= $idArray[$i];
				if ($i < $count - 1) {
					$query .= ", ";
				}
			}


			$query .= " )";
		}

		return $this->_processGetPFPListQuery($query);
	}


	public function searchPFP($companyID = 0, Pagination $pagination, $searchString = "") {
		$query = "SELECT pfp.id, pfp.description, pfp.company_id " .
				"FROM ".TB_PFP." pfp, ".TB_PRODUCT." p, ".TB_PFP2PRODUCT." pfp2p " .
				"WHERE p.product_id = pfp2p.product_id AND pfp2p.preformulated_products_id = pfp.id AND (" .
					"pfp.description LIKE ('%".$this->db->sqltext($searchString)."%') OR " .
					"p.name LIKE ('%".$this->db->sqltext($searchString)."%') " .
				")";

		if ($companyID != 0) {
			$query .= " AND company_id = $companyID";
		}

		$query .= " GROUP BY pfp.id";

		if (isset($pagination)) {
			$query .=  " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}

		return $this->_processGetPFPListQuery($query);
	}

	public function getPfpList($PfpIdArray = null) {
		if ($PfpIdArray != null) {
			$pmanager = new Product($this->db);
			$productsbysupplier = $pmanager->getProductListByMFG($PfpIdArray);
			$count = count($productsbysupplier);
			$PFPArray = array();
			for ($i = 0; $i < $count; $i++) {

				$getPfpQuery = "SELECT preformulated_products_id FROM " . TB_PFP2PRODUCT . " WHERE product_id = " . $productsbysupplier[$i]['product_id'];

				$this->db->query($getPfpQuery);
				$pfp = $this->db->fetch_all_array();
				foreach ($pfp as $p) {
					if ($p['preformulated_products_id']) {
						$PFPArray[] = $p['preformulated_products_id'];
					}
				}
			}
			$pfparray = array_merge(array_unique($PFPArray));
			return $pfparray;
		} else {
			return false;
		}
	}

	public function getListSpecial($companyID = null, Pagination $pagination = null, $idArray = null) {
		if ($idArray != null) {
			if ($companyID) {
				$companyID = mysql_escape_string($companyID);
				//$query = "SELECT * FROM " . TB_PFP . " WHERE company_id = $companyID";
				$query = "SELECT pfp.id, pfp.description, pfp.company_id FROM " . TB_PFP . " pfp, " . TB_PFP2COMPANY . " pfp2c WHERE pfp.id=pfp2c.pfp_id AND pfp2c.company_id = $companyID ";
			} else {
				$query = "SELECT * FROM " . TB_PFP . " WHERE 1 ";
			}
			if (isset($pagination)) {
				$query .= "ORDER BY pfp.id LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
			}

			if (isset($idArray) and is_array($idArray) and count($idArray) > 0) {

				$count = count($idArray);
				$query .= " AND id IN ( ";

				for ($i = 0; $i < $count; $i++) {
					$query .= $idArray[$i];
					if ($i < $count - 1) {
						$query .= ", ";
					}
				}


				$query .= " )";
			}

			if (!$companyID) {
				$query .= " ORDER BY id";
			}

			$this->db->query($query);

			//Init PFPProducts for each PFP...
			$pfpArray = $this->db->fetch_all_array();
			$count = count($pfpArray);


			$pfps = array(); //Array of objects PFP

			for ($i = 0; $i < $count; $i++) {

				$PFPProductsArray = array();

				$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray[$i]['id'];
				//echo "<br/>$getProductsQuery";
				$this->db->query($getProductsQuery);
				$products = $this->db->fetch_all_array();
				//var_dump($products);
				foreach ($products as $p) {
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
		} else {
			return false;
		}
	}

	public function add(PFP $product, $companyID) {

		$count = count($product->products);
		if ($count == 0) {

			return false;
		}

		if (isset($companyID) and is_array($companyID) and count($companyID) > 0) {
			$queryAddPFP = "INSERT INTO " . TB_PFP . " (description,company_id) VALUES ('" . $product->getDescription() . "',NULL)";

			$this->db->query($queryAddPFP);

			$pfpID = $this->db->getLastInsertedID();
			$i = 0;
			while ($companyID[$i]) {
				$queryAddPFPRelation2Company = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id ,company_id) VALUES (" . $pfpID . ", " . $companyID[$i]['id'] . ")";
				$this->db->query($queryAddPFPRelation2Company);

				$i++; //var_dump($companyID[$i]['id'],'QUERY',$queryAddPFP,'QUERY2',$queryAddPFPRelation2Company);
			}
		} else {
			$queryAddPFP = "INSERT INTO " . TB_PFP . " (description,company_id) VALUES ('" . $product->getDescription() . "','" . $companyID . "')";
			$this->db->query($queryAddPFP);

			$pfpID = $this->db->getLastInsertedID();

			$queryAddPFPRelation2Company = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id ,company_id) VALUES (" . $pfpID . ", " . $companyID . ")";
			$this->db->query($queryAddPFPRelation2Company);
		}
		$queryInsertPFPProducts = "INSERT INTO " . TB_PFP2PRODUCT . "(ratio,product_id,preformulated_products_id,isPrimary) VALUES ";
		for ($i = 0; $i < $count; $i++) {
			$isPrimary = $product->products[$i]->isPrimary() ? "true" : "false";
			$queryInsertPFPProducts .= " ( " . $product->products[$i]->getRatio() .
					", " . $product->products[$i]->product_id . " , $pfpID , $isPrimary) ";
			if ($i < $count - 1) {
				$queryInsertPFPProducts .= " , ";
			}
		}

		$this->db->query($queryInsertPFPProducts);

		//var_dump($companyID[$i]['id'],'QUERY',$queryAddPFP,'QUERY2',$queryAddPFPRelation2Company,'QUERY3',$queryInsertPFPProducts);
	}

	public function remove(PFP $product) {

		$this->removeProducts($product->getId());
		$delQuery = "DELETE FROM " . TB_PFP . " WHERE id = " . $product->getId();
		$this->db->query($delQuery);
	}

	public function removeList($pfpArray) {

		//$this->db->beginTransaction();
		foreach ($pfpArray as $pfp) {

			$this->remove($pfp);
		}
	}

	//TODO: is this error?
	public function unassignPFPFromCompanies($pfpID) {
		$query = "DELETE FROM " . TB_PFP2COMPANY . " WHERE pfp_id=" . $pfpID;
		$this->db->query($query);
		if (mysql_errno() == 0) {
			$error = "";
		} else {
			$error = "Error!";
		}

		return $error;
	}


	public function unassignPFPFromCompany($pfpID, $companyID) {
		$query = "DELETE FROM " . TB_PFP2COMPANY . "
				WHERE pfp_id = " . mysql_escape_string($pfpID) ."
				AND company_id = " . mysql_escape_string($companyID);
		$this->db->exec($query);
	}

	public function assignPFP2Company($pfpID, $companyID) {
		$query = "INSERT INTO " . TB_PFP2COMPANY . " (pfp_id, company_id) VALUES (" . $pfpID . ", " . $companyID . ")";
		$this->db->query($query);
		if (mysql_errno() == 0) {
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
		for ($i = 0; $i < $count; $i++) {
			$isPrimary = $to->products[$i]->isPrimary() ? "true" : "false";
			$queryInsertPFPProducts .= " ( " . $to->products[$i]->getRatio() .
					", " . $to->products[$i]->product_id . " , {$from->getId()}, $isPrimary ) ";
			if ($i < $count - 1) {
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
		foreach ($products as $p) {
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

	public function getPFPProductsbySopplier($id) {

		$query = "SELECT * FROM " . TB_PRODUCT . " WHERE supplier_id = $id";
		$this->db->query($query);
		$pfpproducts = $this->db->fetch_all_array();
		return $pfpproducts;
	}

	public function isPFPModified($pfpOld, $pfp) {
		$result = FALSE;
		if ($pfp->getDescription() == $pfpOld->getDescription()) {
			if (count($pfp->products) == count($pfpOld->products)) {
				for ($i = 0; $i < count($pfp->products); $i++) {
					if ($pfp->products[$i]->product_id == $pfpOld->products[$i]->product_id) {
						if ($pfp->products[$i]->isPrimary() == $pfpOld->products[$i]->isPrimary()) {
							if ($pfp->products[$i]->getRatio() == $pfpOld->products[$i]->getRatio()) {

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

	public function isCreaterPFP($pfpID, $companyID) {
		$result = FALSE;
		$query = "SELECT company_id FROM " . TB_PFP . " WHERE id=" . $pfpID;
		$this->db->query($query);
		$companyCreaterID = $this->db->fetch(0)->company_id;
		if ($companyCreaterID == $companyID) {
			$result = TRUE;
		}

		return $result;
	}


	public function searchAutocomplete($occurrence) {
		$occurrence = mysql_escape_string($occurrence);

		$query = "SELECT pfp.description, p.name, LOCATE('".$occurrence."', pfp.description) occurrence1, LOCATE('".$occurrence."', p.name) occurrence2  " .
				"FROM ".TB_PFP." pfp, ".TB_PRODUCT." p, ".TB_PFP2PRODUCT." pfp2p " .
				"WHERE p.product_id = pfp2p.product_id AND pfp2p.preformulated_products_id = pfp.id AND (" .
					"LOCATE('".$occurrence."', pfp.description)>0 OR " .
					"LOCATE('".$occurrence."', p.name)>0 " .
				")" .
				"LIMIT ".AUTOCOMPLETE_LIMIT;
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$pfps = $this->db->fetch_all_array();

			foreach ($pfps as $pfp) {
				if($pfp['occurrence1'] != 0) {
					$result = array (
						"pfp"		=>	$pfp['description'],
						"occurrence"	=>	$pfp['occurrence1']
					);
					$results[] = $result;
				} elseif ($pfp['occurrence2'] != 0) {
					$result = array (
						"pfp"		=>	$pfp['name'],
						"occurrence"	=>	$pfp['occurrence2']
					);
					$results[] = $result;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}


	private function _processGetPFPListQuery($query) {
		$this->db->query($query);

		//Init PFPProducts for each PFP...
		$pfpArray = $this->db->fetch_all_array();
		$count = count($pfpArray);
		//var_dump($pfpArray);

		$pfps = array(); //Array of objects PFP

		for ($i = 0; $i < $count; $i++) {

			$PFPProductsArray = array();

			$getProductsQuery = "SELECT * FROM " . TB_PFP2PRODUCT . " WHERE preformulated_products_id = " . $pfpArray[$i]['id'];
			//echo "<br/>$getProductsQuery";
			$this->db->query($getProductsQuery);
			$products = $this->db->fetch_all_array();
			//var_dump($products);
			foreach ($products as $p) {
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

}

?>