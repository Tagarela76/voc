<?php

class Supplier {

	private $db;

    function __construct($db) {
    	$this->db=$db;
    }

    public function getSupplierList(Pagination $pagination = null,$filter=' TRUE ', $sort=' ORDER BY s.supplier ') {
	    $query = "SELECT s.supplier_id AS supplier_id,s.supplier AS supplier_desc,s.contact_person AS contact,s.phone AS phone,s.address AS address, c.name AS country FROM ".TB_SUPPLIER." s, ".TB_COUNTRY." c   WHERE (s.country_id=c.country_id OR s.country_id=NULL) AND $filter $sort";

    	if (isset($pagination)) {
			$query .= " LIMIT ".$pagination->getLimit()." OFFSET ".$pagination->getOffset()."";
		}
	    $this->db->query($query);
	    if ($this->db->num_rows()) {
	    	$data=$this->db->fetch_all_array();
		    return $data;
	    }
	   	return false;
    }


    public function getSupplierDetails($supplierID) {
    	$query = "SELECT s.supplier_id,s.supplier AS supplier_desc,s.contact_person AS contact,s.phone, s.address, s.country_id, c.name AS country
    				FROM ".TB_SUPPLIER." s, ".TB_COUNTRY." c
    				WHERE (s.country_id = c.country_id OR s.country_id IS NULL)
    				AND supplier_id=$supplierID LIMIT 1 ";
	    $this->db->query($query);

	    if ($this->db->num_rows() > 0) {
		    return $this->db->fetch_array(0);
	    }
	   	return false;
    }

    public function setSupplierDetails($supplierDetails){

    	//$this->db->select_db(DB_NAME);

		$query="UPDATE ".TB_SUPPLIER." SET ";

		$query.="supplier='".$supplierDetails['description']."', ";

		$query.="contact_person='".$supplierDetails['contact']."', ";

		$query.="phone='".$supplierDetails['phone']."', ";

		$query.="address='".$supplierDetails['address']."', ";

		$query.="country_id='".$supplierDetails['country_id']."' ";

		$query.=" WHERE supplier_id=".$supplierDetails['supplier_id'];

		$this->db->query($query);
    }

    public function addNewSupplier($supplierData) {
	    //$this->db->select_db(DB_NAME);

	    $query="INSERT INTO ".TB_SUPPLIER." (supplier, contact_person, phone, address, country_id) VALUES (";

	    $query.="'".$supplierData["description"]."',";

	    $query.="'".$supplierData["contact"]."',";

	    $query.="'".$supplierData["phone"]."', ";

	    $query.="'".$supplierData["address"]."', ";

	    $query.="'".$supplierData["country_id"]."'";

	    $query.=')';

	    $this->db->query($query);
    }

	public function deleteSupplier($supplierID) {
		//$this->db->select_db(DB_NAME);
		$this->db->query("DELETE FROM ".TB_SUPPLIER." WHERE supplier_id=".$supplierID);
	}


	public function queryTotalCount($filter=' TRUE ') {
		$query = "SELECT COUNT(*) cnt FROM ".TB_SUPPLIER." WHERE $filter";
		$this->db->query($query);
		return $this->db->fetch(0)->cnt;
	}

	public function assignSup2Sup($origin, $dublicate){
		$query = "UPDATE ".TB_SUPPLIER." SET original_id = '".$origin."' WHERE supplier_id = ".$dublicate;

		$this->db->query($query);
		if (mysql_errno() == 0){
			$error = "";
		} else {
			$error = "Error!";
		}

		return $error;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return boolean| int
	 */
	public function getSupplierIdByName($name) {
	    $query = "SELECT supplier_id " .
				 "FROM " . TB_SUPPLIER . " " .
				 "WHERE supplier='{$this->db->sqltext($name)}'";
	    $this->db->query($query);
	    if ($this->db->num_rows() != 0) {
	    	$data = $this->db->fetch_array(0);	
		    return $data["supplier_id"];
	    } else {
			return false;
		}
    }


}
?>