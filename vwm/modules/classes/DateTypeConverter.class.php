<?php
class DateTypeConverter {
//	Properties
	private $db;

	//	Methods

	function DateTypeConverter($db) {
		$this->db=$db;
	}

	public function getDatetypebyID($equipmentID) {
    	$query = "SELECT c.date_type ".
				"FROM ".TB_EQUIPMENT." e, ".TB_DEPARTMENT." d, ".TB_FACILITY." f, ".TB_COUNTRY." c ".
				"WHERE e.department_id = d.department_id ".
				"AND d.facility_id = f.facility_id ".
				"AND f.country = c.country_id ".
				"AND e.equipment_id = ".$equipmentID;
		$this->db->query($query);

		return ($this->db->num_rows() > 0) ? $this->db->fetch(0)->date_type : false;
    }

    public function getDatetype($facilityID) {
    	//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT country FROM ".TB_FACILITY." WHERE facility_id=".$facilityID);
		$data_c = $this->db->fetch(0);
		$this->db->query("SELECT date_type FROM ".TB_COUNTRY." WHERE country_id=".$data_c->country);
		$data_date = $this->db->fetch(0);
		return $data_date->date_type;
    }
}
?>