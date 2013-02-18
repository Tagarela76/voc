<?php

namespace VWM\Apps\WorkOrder\Entity;

use VWM\Framework\Model;

class Pfp extends Model {

	public $id;
	public $description;
	public $company_id;
	public $creator_id;
	public $last_update_time;
	public $is_proprietary = 0;
	public $products;

	const TABLE_NAME = 'preformulated_products';
	const TABLE_PFP2COMPANY = 'pfp2company';
    const TABLE_PFP2PRODUCT = 'pfp2product';

	public function __construct(\db $db) {
		$this->db = $db;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getCompanyId() {
		return $this->company_id;
	}

	public function setCompanyId($company_id) {
		$this->company_id = $company_id;
	}

	public function getCreatorId() {
		return $this->creator_id;
	}

	public function setCreatorId($creator_id) {
		$this->creator_id = $creator_id;
	}

	public function getLastUpdateTime() {
		return $this->last_update_time;
	}

	public function setLastUpdateTime($last_update_time) {
		$this->last_update_time = $last_update_time;
	}

	public function getProducts() {
		return $this->products;
	}

	public function setProducts($products) {
		$this->products = $products;
	}

	public function getIsProprietary() {
		return $this->is_proprietary;
	}

	public function setIsProprietary($isProprietary) {
		$isProprietary = $this->convertPfpIProprietary($isProprietary);
		if($isProprietary){
			$this->is_proprietary = $isProprietary;
		}else{
			return $isProprietary;
		}
	}



	protected function _insert() {

		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'" : "NULL";

		$sql = "INSERT INTO " . self::TABLE_NAME .
				"(description, company_id, creater_id, last_update_time, is_proprietary" .
				") VALUES (" .
				"'{$this->db->sqltext($this->getDescription())}', " .
				"{$this->db->sqltext($this->getCompanyId())}, " .
				"NULL, " .
				"{$lastUpdateTime}, " .
				"{$this->db->sqltext($this->getIsProprietary())})";
		$response = $this->db->exec($sql);
		if ($response) {
			$this->setId($this->db->getLastInsertedID());

			if ($this->getCompanyId() != 0) {
				$sql = "INSERT INTO " . self::TABLE_PFP2COMPANY .
					"(pfp_id ,company_id" .
					") VALUES (" .
					"{$this->db->sqltext($this->getId())}, " .
					"{$this->db->sqltext($this->getCompanyId())})";
				$this->db->query($sql);
			}
			return $this->getId();
		} else {
			return false;
		}
	}

	protected function _update() {
		$lastUpdateTime = ($this->getLastUpdateTime())
				? "'{$this->getLastUpdateTime()}'" : "NULL";

		$sql = "UPDATE preformulated_products SET ".
					"company_id = {$this->db->sqltext($this->getCompanyId())}, ".
					"is_proprietary = {$this->db->sqltext($this->getIsProprietary())}, ".
					"last_update_time = {$lastUpdateTime} ".
					"WHERE id = {$this->db->sqltext($this->getId())}";

		$response = $this->db->exec($sql);
		if ($response) {
			if ($this->getCompanyId() != 0) {
				$sql = "SELECT * FROM ". self::TABLE_PFP2COMPANY .
						"WHERE company_id = {$this->db->sqltext($this->getCompanyId())}";
				$response = $this->db->exec($sql);
				if (!$response) {
					$sql = "INSERT INTO " . self::TABLE_PFP2COMPANY .
							"(pfp_id ,company_id" .
							") VALUES (" .
							"{$this->db->sqltext($this->getId())}, " .
							"{$this->db->sqltext($this->getCompanyId())})";
					$this->db->query($sql);
				}
			}
			return $this->getId();
		} else {
			return false;
		}
	}


	public function load() {
		if (is_null($this->getId())) {
			return false;
		}

		$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id =" .
				$this->db->sqltext($this->getId());
		$this->db->query($sql);
		if ($this->db->num_rows() == 0) {
			return false;
		}
		$row = $this->db->fetch(0);
		$this->initByArray($row);
	}


	/**
	 * function for converting pfps intellectual proprietary to boolean type
	 * @string isProprietary
	 * return bool
	 */
	private function convertPfpIProprietary($isProprietary=0){
		//correct values

		if($isProprietary == '1' || $isProprietary == '0'){
			return $isProprietary;
		}
		elseif($isProprietary == 'IP'){
			return 1;
		}
		elseif(trim($isProprietary == '')){
			return 0;
		}else{
			return false;
		}

	}

	public function getProductsCount(){
		return count($this->getProducts());
	}


	public function getRatio($htmlFormatting) {

		$products = $this->getProducts();

        foreach ($products as $product) {
            if($product->isPrimary() && $htmlFormatting){
                $res[] = "<b>".$product->getRatio()."</b>";
            } else {
                $res[] = $product->getRatio();
            }
        }
		return implode(':', $res);
	}


}

?>
