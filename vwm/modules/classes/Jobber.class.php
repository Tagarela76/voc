<?php

require_once('modules/phpgacl/gacl.class.php');
require_once('modules/phpgacl/gacl_api.class.php');

class Jobber {

	private $db;
	private $trashRecord;
	private $jobber_id;

	public function __construct(db $db, Array $array = null) {
		$this->db = $db;


		if (isset($array)) {
			$this->initByArray($array);
		}
	}

	private function initByArray($array) {

		foreach ($array as $key => $value) {
			try {
				$this->__set($key, $value);
			} catch (Exception $e) {
				$this->errors[] = $e->getMessage();
			}
		}
	}

	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;
	}

	/**
	 *
	 * Overvrite get property if property is not exists or private.
	 * @param unknown_type $name - property name. method call method get_%property_name%, if method does not exists - return property value;
	 */
	public function __get($name) {
		if (method_exists($this, "get_" . $name)) {
			$methodName = "get_" . $name;
			$res = $this->$methodName();
			return $res;
		} else if (property_exists($this, $name)) {
			return $this->$name;
		} else {
			return false;
		}
	}

	/**
	 *
	 * Overvrive set property. If property reload function set_%property_name% exists - call it. Else - do nothing. Keep OOP =)
	 * @param unknown_type $name - name of property
	 * @param unknown_type $value - value to set
	 */
	public function __set($name, $value) {

		/* Call setter only if setter exists */
		if (method_exists($this, "set_" . $name)) {
			$methodName = "set_" . $name;
			$this->$methodName($value);
		}
		/**
		 * Set property value only if property does not exists (in order to do not revrite privat or protected properties),
		 * it will craete dynamic property, like usually does PHP
		 */ else if (!property_exists($this, $name)) {
			/**
			 * Disallow add new properties dynamicly (cause of its change type of object to stdObject, i dont want that)
			 */
			$this->$name = $value;
		}
		/**
		 * property exists and private or protected, do not touch. Keep OOP
		 */ else {
			//Do nothing
		}
	}

	public function set_jobber_id($value) {
		try {
			$this->jobber_id = $value;
		} catch (Exception $e) {
			throw new Exception("Id cannot be empty!" . $e->getMessage());
		}
	}

	public function getJobberList() {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM " . TB_COMPANY . " ORDER BY name");
		if ($this->db->num_rows()) {
			for ($i = 0; $i < $this->db->num_rows(); $i++) {
				$data = $this->db->fetch($i);
				$company = array(
					'id' => $data->company_id,
					'name' => $data->name,
					'address' => $data->address,
					'contact' => $data->contact,
					'phone' => $data->phone
				);
				$companies[] = $company;
			}
		}

		return $companies;
	}

	public function getJobberByName($jobber_name) {
		$query_select = "SELECT jobber_id FROM jobber WHERE name LIKE '" . mysql_real_escape_string($jobber_name) . "'";
		$this->db->query($query_select);
		if ($this->db->num_rows() > 0) {
			$result = $this->db->fetch_array(0);
		} else {
			$result = array('jobber_id' => 0);
		}

		return intval($result['jobber_id']);
	}

	public function save() {
		if ($this->jobber_id != NULL) {
			$query = "UPDATE jobber SET
								name = '" . mysql_escape_string($this->name) . "',
								address = '" . mysql_escape_string($this->address) . "',
								city = '" . mysql_escape_string($this->city) . "',
								zip = '" . mysql_escape_string($this->zip) . "',
								county = '" . mysql_escape_string($this->county) . "',
								state = '" . mysql_escape_string($this->state) . "',
								country = '" . mysql_escape_string($this->country) . "',
								phone = '" . mysql_escape_string($this->phone) . "',
								fax = '" . mysql_escape_string($this->fax) . "',
								contact = '" . mysql_escape_string($this->contact) . "',
								title = '" . mysql_escape_string($this->title) . "',
								email	= '" . mysql_escape_string($this->email) . "'
								WHERE jobber_id = {$this->jobber_id}";
			$this->db->query($query);
		} else {
			//	GCG Creation
			//$GCG = new GCG($this->db);
			//$gcgID = $GCG->create();

			$date = new DateTime();

			$query = "INSERT INTO jobber (name, address, city, zip, county, state, country, phone, fax, email, contact, title, creater_id, creation_date) VALUES (";

			$query.="'" . mysql_escape_string($this->name) . "', ";
			$query.="'" . mysql_escape_string($this->address) . "', ";
			$query.="'" . mysql_escape_string($this->city) . "', ";
			$query.="'" . mysql_escape_string($this->zip) . "', ";
			$query.="'" . mysql_escape_string($this->county) . "', ";
			$query.="'" . mysql_escape_string($this->state) . "', ";
			$query.= mysql_escape_string($this->country) . ", ";
			$query.="'" . mysql_escape_string($this->phone) . "', ";
			$query.="'" . mysql_escape_string($this->fax) . "', ";
			$query.="'" . mysql_escape_string($this->email) . "', ";
			$query.="'" . mysql_escape_string($this->contact) . "', ";
			$query.="'" . mysql_escape_string($this->title) . "', ";
			$query.= $this->creater_id . ", ";
			$query.= "'" . $date->format('Y-m-d') . "'";


			$query.=')';



			$this->db->query($query);

			$this->db->query("SELECT LAST_INSERT_ID() id");
			$this->jobber_id = $this->db->fetch(0)->id;
		}
		//----------------------------------------------------------------
		//GACL
		//----------------------------------------------------------------
		//   ADDING COMPANY
		//   CREATE ACO
		$gacl_api = new gacl_api();
		$acoID = $gacl_api->add_object('access', "jobber_" . $this->jobber_id, "jobber_" . $this->jobber_id, 0, 0, 'ACO');
		//   CREATE ARO GROUP
		$giantcomliance = $gacl_api->get_group_id("Giant Compliance");
		$aro_group_jobber = $gacl_api->add_group("jobber_" . $this->jobber_id, "jobber_" . $this->jobber_id, $giantcomliance, 'ARO');
		$aro_group_root = $gacl_api->get_group_id("root");

		//   CREATE ACL
		$acoArray = array('access' => array("jobber_" . $this->jobber_id));
		$jobberGroup = array($aro_group_jobber);
		$rootGroup = array($aro_group_root);

		$gacl_api->add_acl($acoArray, NULL, $jobberGroup, NULL, NULL, 1, 1, NULL, 'jobber\'s users has access to company ACO ');
		$gacl_api->add_acl($acoArray, NULL, $rootGroup, NULL, NULL, 1, 1, NULL, 'root\'s users has access to company ACO ');
		//-----------------------------------------------------------------
		//	save to trash_bin
		$this->save2trash('C', $this->jobber_id);

		if (mysql_error() == '') {
			return $this->jobber_id;
		} else {
			throw new Exception(mysql_error());
		}
	}

	function deleteJobber($company_id) {
		//$this->db->select_db(DB_NAME);
		//screening of quotation marks
		$company_id = mysql_real_escape_string($company_id);

		//	save to trash_bin
		$this->save2trash('D', $company_id);

		$this->db->query("SELECT * FROM " . TB_FACILITY . " WHERE company_id = " . $company_id);
		$facilitiesCount = $this->db->num_rows();
		$facilitiesToDelete = $this->db->fetch_all();
		if ($facilitiesCount > 0) {
			$facility = new Facility($this->db);
			$facility->setParentTrashRecord($this->trashRecord);
			for ($i = 0; $i < $facilitiesCount; $i++) {
				$facility->setTrashRecord(new Trash($this->db));
				$facility->deleteFacility($facilitiesToDelete[$i]->facility_id);
			}
		}

		$this->db->query("DELETE FROM " . TB_COMPANY . " WHERE company_id=" . $company_id);
	}

	//	Tracking System
	private function save2trash($CRUD, $companyID) {
		//	protect from SQL injections
		$companyID = mysql_real_escape_string($companyID);

		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_COMPANY, $companyID, $CRUD, $this->parentTrashRecord);
	}

}

?>
