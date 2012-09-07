<?php

class SalesBrochure {

	/**
	 *
	 * @var int 
	 */
	public $id;
	
	/**
	 *
	 * @var int 
	 */
	public $sales_client_id;
	
	/**
	 *
	 * @var string 
	 */
	public $title_up;
	
	/**
	 *
	 * @var string 
	 */
	public $title_down;
	
	/**
	 * db connection
	 * @var db 
	 */
	private $db;


	function __construct(db $db, $salesBrochureId = null) {
		$this->db = $db;

		if (isset($salesBrochureId)) {
			$this->id = $salesBrochureId;
			$this->_load();
		}
	}

	/**
	 * add work order
	 * @return int 
	 */
	public function addSalesBrochure() {

		$query = "INSERT INTO " . TB_SALES_BROCHURE . "(sales_client_id, title_up, title_down) 
				VALUES ( 
				" . $this->db->sqltext($this->sales_client_id) . "
				, '" . $this->db->sqltext($this->title_up) . "'
				, '" . $this->db->sqltext($this->title_down) . "'
				)";
		$this->db->query($query); 
		$salesBrochureId = $this->db->getLastInsertedID();
		$this->id = $salesBrochureId;
		return $salesBrochureId;
	}

	/**
	 * update work order
	 * @return int 
	 */
	public function updateSalesBrochure() {

		$query = "UPDATE " . TB_SALES_BROCHURE . "
					set sales_client_id=" . $this->db->sqltext($this->sales_client_id) . ",
						title_up='" . $this->db->sqltext($this->title_up) . "',
						title_down='" . $this->db->sqltext($this->title_down) . "'
					WHERE id= " . $this->db->sqltext($this->id);
		$this->db->query($query);

		return $this->id;
	}

	/**
	 *
	 * delete work order
	 */
	public function delete() {

		$sql = "DELETE FROM " . TB_SALES_BROCHURE . "
				 WHERE id=" . $this->db->sqltext($this->id);
		$this->db->query($sql);
	}

	/**
	 * insert or update work order
	 * @return int 
	 */
	public function save() {

		if (!isset($this->id)) {
			$salesBrochureId = $this->addSalesBrochure();
		} else {
			$salesBrochureId = $this->updateSalesBrochure();
		}
		return $salesBrochureId;
	}
	

	private function _load() {

		if (!isset($this->id)) {
			return false;
		}
		$sql = "SELECT * " .
				"FROM " . TB_SALES_BROCHURE . " " .
				"WHERE id={$this->db->sqltext($this->id)} LIMIT 1";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}
		$rows = $this->db->fetch(0);

		foreach ($rows as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
	}

}

?>