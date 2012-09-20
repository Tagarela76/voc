<?php

class NoxEmissionManager {

	/**
	 *
	 * @var db
	 */
	private $db;
	private $burnerDetails = array();

	/*
	 * 0.092 - EFx - emission factor for NOx (lbs/MMBtu)
	 */
	const EMISSION_FACTOR_FOR_NOX = 0.092;

	function __construct(db $db) {
		$this->db = $db;
	}

	public function getCountNoxByDepartment($departmentID) {

		$query = "SELECT COUNT(*) cnt FROM nox WHERE department_id = {$departmentID} ";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}

	public function getCountNoxByFacility($facilityID) {
		$query = "SELECT COUNT(*) cnt
					FROM nox, department d
					WHERE d.department_id = nox.department_id
					AND d.facility_id = " . mysql_escape_string($facilityID);
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}

	public function getCountBurnerByDepartment($departmentID) {

		$query = "SELECT COUNT(*) cnt FROM burner WHERE department_id = {$departmentID} ";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['cnt'];
	}

	public function getNoxListByDepartment($departmentID, $sortStr = null, $pagination = null) {
		$query = "SELECT * FROM nox WHERE department_id = {$this->db->sqltext($departmentID)} ";
		if (isset($sortStr)) {
			$query .= $sortStr;
		}

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch_all_array();
			return $data;
		}
		else
			return false;
	}

	public function getNoxListByFacility($facilityID, $sortStr = null, $pagination = null) {
		$query = "SELECT nox.*
					FROM nox, department d
					WHERE d.department_id = nox.department_id
					AND d.facility_id = {$this->db->sqltext($facilityID)}";

		if (isset($sortStr)) {
			$query .= $sortStr;
		}

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch_all_array();
			return $data;
		}
		else {
			return false;
		}
	}

	public function getBurnerListByDepartment($departmentID, $sortStr = null, $pagination = null) {
		$query = "SELECT * FROM burner WHERE department_id = {$departmentID} ";
		if (isset($sortStr)) {
			$query .= $sortStr;
		}

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch_all_array();
			return $data;
		}
		else
			return false;
	}

	public function getBurnerDetail($burnerID) {

		if ($this->burnerDetails[$burnerID]) {
			//	we already calculated this
			return $this->burnerDetails[$burnerID];
		}
		$query = "SELECT * FROM burner WHERE burner_id = {$burnerID} ";

		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch_array(0);
			$this->burnerDetails[$data['burner_id']] = $data;
			return $data;
		} else {
			return false;
		}
	}

	public function getLogDataReadable($logList) {
		for ($i = 0; $i < count($logList); $i++) {
			$url = "?action=viewDetails&category=logging&id=" . $logList[$i]['log_id'];
			$logList[$i]['url'] = $url;
			$action = json_decode($logList[$i]['action']);

			if ($logList[$i]['action_type'] == "AUTH") {
				$logList[$i]['action'] = "Authorization";
			} elseif ($logList[$i]['action_type'] == "LOGOUT") {
				$logList[$i]['action'] = "Logout";
			} else {
				$logList[$i]['action'] = $action->get->action . " in category " . $action->get->category;
			}

			$date = $logList[$i]['date'];
			$logList[$i]['date'] = date("d/m/Y H:i:s", $date);
		}
		return $logList;
	}

	public function loggingAutocomplete($occurrence) {

		$occurrence = mysql_escape_string($occurrence);

		$query = "SELECT u.username, LOCATE('" . $occurrence . "', u.username) occurrence " .
				"FROM " . TB_USER . " u WHERE LOCATE('" . $occurrence . "', u.username)>0 LIMIT " . AUTOCOMPLETE_LIMIT;

		$this->db->query($query);
//echo $query;
		if ($this->db->num_rows() > 0) {
			$userData = $this->db->fetch_all();
			for ($i = 0; $i < count($userData); $i++) {
				if ($userData[$i]->occurrence) {
					$user = array(
						"username" => $userData[$i]->username,
						"occurrence" => $userData[$i]->occurrence
					);
					$results[] = $user;
				}
			}
			return (isset($results)) ? $results : false;
		} else
			return false;
	}

	public function searchLog($log, $companyID = null, $facilityID = null, $departmentID = null, $pagination = null) {
		$companyID = mysql_escape_string($companyID);
		$facilityID = mysql_escape_string($facilityID);
		$departmentID = mysql_escape_string($departmentID);
		$query = "SELECT ul.* FROM user_logging ul, user u";
		$sql = '';
		if ($facilityID && $facilityID != 'All facilities') {
			$sql .= " u.facility_id = {$facilityID} AND ";
		}
		if ($departmentID && $departmentID != "All departments") {
			$sql .= " u.department_id = {$departmentID} AND ";
		}


		if ($companyID && $companyID != 'All companies') {
			$query .= " WHERE company_id = " . $companyID . " AND {$sql} (";
		} else {
			$query .= " WHERE {$sql} (";
		}
		if (!is_array($log)) {
			$log = array($log);
		}

		$sqlParts = array();
		foreach ($log as $log_item) {
			$log_item = mysql_escape_string($log_item);
			$sqlParts[] = "u.username LIKE '%" . $log_item . "%'";
		}
		$sql = implode(' OR ', $sqlParts);
		$query .= $sql . ") AND u.user_id = ul.user_id";

		if (isset($pagination)) {
			$query .= " LIMIT " . $pagination->getLimit() . " OFFSET " . $pagination->getOffset() . "";
		}

		$this->db->query($query);
		if ($this->db->num_rows() > 0) {

			$searched = $this->db->fetch_all_array();
		}
		return (isset($searched)) ? $searched : null;
	}

	public function deleteNoxEmissionsByID($noxEmissionID) {
		$query = "DELETE FROM nox WHERE nox_id = ".  mysql_escape_string($noxEmissionID);
		$this->db->query($query);
	}

	public function getNoxEmissionDetails($noxEmissionID) {
		$sql = "SELECT * FROM `nox` WHERE nox_id = " . mysql_escape_string($noxEmissionID);
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			return false;
		}

		return $this->db->fetch_array(0);
	}

	public function calculateNox(NoxEmission $noxEmission) {
		$burnerDetails = $this->getBurnerDetail($noxEmission->burner_id);
		$noxEmission->gas_unit_used = (float)$noxEmission->gas_unit_used;

		if ($noxEmission->gas_unit_used == 0) {
			$hours = ($noxEmission->end_time - $noxEmission->start_time)/3600;
		} else {
			//	gas_unit_used is in terms
			//	1 term = 100000 BTU
			$hours = $noxEmission->gas_unit_used * 100000/$burnerDetails['btu'];
		}

		$nox = $burnerDetails['btu'] * self::EMISSION_FACTOR_FOR_NOX/1000000 * $hours;

		return $nox;

	}

	/**
	 * Get list of burner manufacturers
	 * @return array|boolean
	 */
	public function getBurnerManufacturerList() {
		$sql = "SELECT * FROM burner_manufacturer ORDER BY name";
		$this->db->query($sql);

		if($this->db->num_rows() > 0) {
			return $this->db->fetch_all_array();
		} else {
			return false;
		}
	}


	public function getBurnerManfucaturer($id) {
		$sql = "SELECT * FROM burner_manufacturer WHERE id = ".  mysql_escape_string($id);
		$this->db->query($sql);

		if($this->db->num_rows() > 0) {
			return $this->db->fetch_array(0);
		} else {
			return false;
		}
	}

	public function getTotalSumNoxByDepartment($departmentID) {

		$query = "SELECT SUM(nox) nox_sum FROM nox WHERE department_id = {$departmentID} ";
		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['nox_sum'];
	}


	/**
	 * Get NOx usage for defined month (current month by default)
	 * @param int $departmentID
	 * @param string $month
	 * @param string $year
	 * @return string
	 */
	public function getCurrentUsageOptimizedByDepartment($id, $category, $month = 'MONTH(CURRENT_DATE)', $year = 'YEAR(CURRENT_DATE)') {

		if ($category == 'department') {
			$query = "SELECT sum( n.nox ) total_usage
				FROM ".TB_DEPARTMENT." d, nox n " .
				"WHERE n.department_id = d.department_id " .
				"AND MONTH(FROM_UNIXTIME(n.start_time)) = ".$this->db->sqltext($month)." " .
				"AND YEAR(FROM_UNIXTIME(n.start_time)) = ".$this->db->sqltext($year)." " .
				"AND d.department_id = ".$id;
		} else {
			$query = "SELECT sum( n.nox ) total_usage
				FROM ".TB_DEPARTMENT." d, nox n " .
				"WHERE n.department_id = d.department_id " .
				"AND MONTH(FROM_UNIXTIME(n.start_time)) = ".$this->db->sqltext($month)." " .
				"AND YEAR(FROM_UNIXTIME(n.start_time)) = ".$this->db->sqltext($year)." " .
				"AND d.facility_id = ".$id;
		}

		$this->db->query($query);
		$row = $this->db->fetch_array(0);
		return $row['total_usage'];

	}
	
	public function getBurnerListByFacility($facilityId) {
				
		$query = "SELECT * 
				  FROM burner WHERE department_id IN (
							SELECT department_id 
							FROM ".TB_DEPARTMENT." 
							WHERE facility_id={$this->db->sqltext($facilityId)})";
		$this->db->query($query);

		if ($this->db->num_rows()) {
			$data = $this->db->fetch_all_array();
			return $data;
		}
		else
			return false;
	}

}