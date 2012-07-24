<?php

require_once ("modules/Validate.php");

class Validation {

	var $noYes = array('NO', 'YES');
	var $db;

	function Validation($db) {
		$this->db = $db;
	}

	public function isUniqueRule($ruleData) {
		$result = array();
		$fail = false;
		$rule = new Rule($this->db);
		$ruleFields = $rule->ruleNrMap;
		foreach ($ruleFields as $ruleField) {
			if ($ruleData[$ruleField] == "") {
				//	skip
				continue;
			}
			$query = "SELECT * FROM " . TB_RULE . " WHERE " . $ruleField . " = '" . $ruleData[$ruleField] . "'";
			if (isset($ruleData['rule_id'])) {
				$query .= " and rule_id!= '" . $ruleData['rule_id'] . "' ";
			}
			$query .= " LIMIT 1 ";
			$this->db->query($query);

			if ($this->db->num_rows() == 0) {
				$result[$ruleField] = true;
			} else {
				$result[$ruleField] = false;
				$fail = true;
			}
		}
		return ($fail) ? $result : true;
	}

	public function isUniqueMsds($msdsData) {
		$query = "SELECT * FROM " . TB_MSDS . " WHERE cas='" . $msdsData["cas"] . "' and cas_desc='" . $msdsData["cas_desc"] . "' and voclx='" . $msdsData["voclx"] . "' and vocwx='" . $msdsData["vocwx"] . "' and temp_vp='" . $msdsData["temp_vp"] . "' and msds_id!=" . $msdsData["msds_id"];

		//$this->db->select_db(DB_NAME);
		$this->db->query($query);

		if ($this->db->num_rows() == 0) {
			return true;
		} else {
			return false;
		}
	}

	public function isUniqueUsage($usageData) {
		$query = "SELECT * FROM " . TB_USAGE . " WHERE description='" . $usageData["description"] . "'" .
				" AND department_id='" . $usageData['department_id'] . "'";

		//$this->db->select_db(DB_NAME);
		$this->db->query($query);

		$data = $this->db->fetch(0);

		if ($this->db->num_rows() != 0 && $data->mix_id != $usageData['mix_id']) {
			return false;
		} else {
			return true;
		}
	}

	public function isUniqueName($itemType, $itemName, $parrentID = 'none', $itemID = "", $type = "") {
		//$this->db->select_db(DB_NAME);
		$itemName = trim($itemName);
		switch ($itemType) {
			case 'company':
				$query = "SELECT * FROM " . TB_COMPANY . " WHERE name='" . $itemName . "'";
				break;

			case 'jobber':
				$query = "SELECT * FROM jobber WHERE name='" . $itemName . "'";
				break;

			case 'facility':
				if ($parrentID != 'none') {
					$query = "SELECT * FROM " . TB_FACILITY . " WHERE name='" . $itemName . "' and company_id=" . $parrentID;
				} else {
					echo "Error: unsigned parametr <b>parrentID</b> <br>";
					return false;
				}
				break;

			case 'department':
				if ($parrentID != 'none') {
					$query = "SELECT * FROM " . TB_DEPARTMENT . " WHERE name='" . $itemName . "' and facility_id=" . $parrentID;
				} else {
					echo "Error: unsigned parametr <b>parrentID</b> <br>";
					return false;
				}
				break;

			case "product":
				$query = "SELECT * FROM " . TB_PRODUCT . " WHERE product_nr='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM " . TB_PRODUCT . " WHERE product_nr='" . $itemName . "' and product_id!=" . $itemID;
				}
				break;

			case "inventory":
				if ($parrentID != 'none') {
					$query = "SELECT * FROM " . TB_INVENTORY . " WHERE name='" . $itemName . "' AND facility_id = '" . $parrentID . "' ";
					if ($itemID != "") {
						$query = "SELECT * FROM " . TB_INVENTORY . " WHERE name='" . $itemName . "' and id!=" . $itemID . " and facility_id='" . $parrentID . "' ";
					}
					if ($type != "") {
						$query .= " AND type='" . $type . "' ";
					}
				} else {//for  compatibility reasons
					$query = "SELECT * FROM " . TB_INVENTORY . " WHERE name='" . $itemName . "' ";
					if ($itemID != "") {
						$query = "SELECT * FROM " . TB_INVENTORY . " WHERE name='" . $itemName . "' and id!='" . $itemID . "' ";
					}
					if ($type != "") {
						$query .= " AND type='" . $type . "' ";
					}
				}
				/* $query = "SELECT * FROM ".TB_INVENTORY." WHERE inventory_name='".$itemName."'";
				  if ($itemID!="") {
				  $query = "SELECT * FROM ".TB_INVENTORY." WHERE inventory_name='".$itemName."' and inventory_id!=".$itemID;
				  } */
				break;

			case "equipment":
				$query = "SELECT * FROM " . TB_EQUIPMENT . " WHERE equipment_name='" . $itemName . "'";
				break;

			case "density":
				$query = "SELECT * FROM " . TB_DENSITY . " WHERE density_type='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_DENSITY . " WHERE density_type='" . $itemName . "' and density_id!=" . $itemID;
				break;

			case "apmethod":
				$query = "SELECT * FROM " . TB_APMETHOD . " WHERE apmethod_desc='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_APMETHOD . " WHERE apmethod_desc='" . $itemName . "' and apmethod_id!=" . $itemID;
				break;

			case "coat":
				$query = "SELECT * FROM " . TB_COAT . " WHERE coat_desc='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_COAT . " WHERE coat_desc='" . $itemName . "' and coat_id!=" . $itemID;
				break;

			case "country":
				$query = "SELECT * FROM " . TB_COUNTRY . " WHERE name='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_COUNTRY . " WHERE name='" . $itemName . "' and country_id!=" . $itemID;
				break;

			case "substrate":
				$query = "SELECT * FROM " . TB_SUBSTRATE . " WHERE substrate_desc='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_SUBSTRATE . " WHERE substrate_desc='" . $itemName . "' and substrate_id!=" . $itemID;
				break;

			case "supplier":
				$query = "SELECT * FROM " . TB_SUPPLIER . " WHERE supplier='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_SUPPLIER . " WHERE supplier='" . $itemName . "' and supplier_id!=" . $itemID;
				break;

			case "type":
				$query = "SELECT * FROM " . TB_TYPE . " WHERE type_desc='" . $itemName . "'";
				if ($itemID != "")
					$query = "SELECT * FROM " . TB_TYPE . " WHERE type_desc='" . $itemName . "' and type_id!=" . $itemID;
				break;

			case "component":
				$query = "SELECT * FROM " . TB_COMPONENT . " WHERE cas='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM " . TB_COMPONENT . " WHERE cas='" . $itemName . "' and component_id!=" . $itemID;
				}
				break;

			case "unittype":
				$query = "SELECT * FROM " . TB_UNITTYPE . " WHERE name='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM " . TB_UNITTYPE . " WHERE name='" . $itemName . "' and unittype_id!=" . $itemID;
				}
				break;

			case "msds":
				$query = "SELECT * FROM " . TB_MSDS . " WHERE cas='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM " . TB_MSDS . " WHERE cas='" . $itemName . "' and msds_id!=" . $itemID;
				}
				break;

			case "lol":
				$query = "SELECT * FROM `" . TB_LOL . "` WHERE name='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM `" . TB_LOL . "` WHERE name='" . $itemName . "' and lol_id!=" . $itemID;
				}
				break;

			case "formulas":
				$query = "SELECT * FROM `" . TB_FORMULA . "` WHERE formula_desc='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM `" . TB_FORMULA . "` WHERE formula_desc='" . $itemName . "' and formula_id!=" . $itemID;
				}
				break;

			case "agency":
				$query = "SELECT * FROM `" . TB_AGENCY . "` WHERE name='" . $itemName . "'";
				if ($itemID != "") {
					$query = "SELECT * FROM `" . TB_AGENCY . "` WHERE name='" . $itemName . "' and agency_id!=" . $itemID;
				}
				break;
			case "accessory":
				if ($parrentID != 'none') {
					$query = "SELECT * FROM " . TB_ACCESSORY . " WHERE name='" . $itemName . "' AND jobber_id=" . (int) $parrentID;
					if ($itemID != "") {
						$query = "SELECT * FROM " . TB_ACCESSORY . " WHERE name='" . $itemName . "' AND jobber_id=" . (int) $parrentID . " AND id!=" . (int) $itemID;
					}
				} else {
					$query = "SELECT * FROM " . TB_ACCESSORY . " WHERE name='" . $itemName . "'";
					if ($itemID != "") {
						$query = "SELECT * FROM " . TB_ACCESSORY . " WHERE name='" . $itemName . "' AND id!=" . (int) $itemID;
					}
				}
				break;
			case "nox":
				$sql = '';
				if ($parrentID != 'none') {
					$sql = " AND department_id = {$parrentID} ";
				}
				$query = "SELECT * FROM `nox` WHERE description='" . $itemName . "'" . " " . $sql;
				if ($itemID != "") {
					$query = "SELECT * FROM `nox` WHERE description='" . $itemName . "' and nox_id!=" . $itemID . " " . $sql;
				}
				break;
				
			case 'workOrder':
				if ($parrentID != 'none') {
					$query = "SELECT * FROM " . TB_WORK_ORDER . " WHERE number='" . $itemName . "' and facility_id=" . $parrentID;
				} else {
					echo "Error: unsigned parametr <b>parrentID</b> <br>";
					return false;
				}
				break;	
		}


		$this->db->query($query);
		if ($this->db->num_rows() == 0) {
			return true;
		} else {
			return false;
		}
	}

	function check_email($email) {
		$email = html_entity_decode($email);
		$email = trim($email);
		if ($this->noYes[Validate::email($email)] == 'YES') {
			return true;
		} else {
			return false;
		}
	}

	function check_id($id) {
		$id = trim($id);
		$parametrs = array('min' => 0, 'max' => 99999999999);
		if ($this->noYes[Validate::number($id, $parametrs)] == 'YES') {
			return true;
		} else {
			return false;
		}
	}

	function check_zip($zip) {
		$zip = trim($zip);
		if (strlen($zip) > 0) {
			//$parametrs=array ('min'=>0, 'max'=>99999);
			//if ($this->noYes[Validate::number($zip, $parametrs)] == 'YES') {
			return true;
			//}
		}
		return false;
	}

	function check_tab_localization_string($string) {
		if ($string == '') {
			return false;
		}

		if (strlen($string) > 120) {
			return false;
		}

		return true;
	}

	function check_state($state) {
		$state = trim($state);
		if (strlen($state) <= LEN_STATE && strlen($state) >= 0) {
			return true;
		}
		return false;
	}

	function check_countryID($countryID) {
		$countryID = trim($countryID);
		$parametrs = array('min' => 0, 'max' => 300);
		if ($this->noYes[Validate::number($countryID, $parametrs)] == 'YES') {
			return true;
		} else {
			return false;
		}
	}

	function check_city($city) {
		$city = trim($city);
		if (strlen($city) <= LEN_CITY && strlen($city) > 0) {
			return true;
		}
		return false;
	}

	function check_phone($phone) {
		$phone = trim($phone);
		if (strlen($phone) <= LEN_PHONE && strlen($phone) > 0) {
			return true;
		}
		return false;
	}

	function check_mobile($mobile) {
		$mobile = trim($mobile);
		if (strlen($mobile) <= LEN_MOBILE && strlen($mobile) > 0) {
			return true;
		}
		return false;
	}

	function check_nameCompany($name) {
		$name = trim($name);
		if (strlen($name) <= LEN_NAME_COMPANY && strlen($name) > 0) {
			return true;
		}
		return false;
	}

	function check_nameFacility($name) {
		$name = trim($name);
		if (strlen($name) <= LEN_NAME_FACILITY) {
			return true;
		}
		return false;
	}

	function check_nameDepartment($name) {
		$name = trim($name);
		if (strlen($name) <= LEN_NAME_DEPARTMENT) {
			return true;
		}
		return false;
	}

	function check_username($username) {
		$username = trim($username);
		if (strlen($username) <= LEN_USERNAME) {
			return true;
		}
		return false;
	}

	function check_accessname($accessname) {
		$accessname = trim($accessname);
		if (strlen($accessname) <= LEN_ACCESSNAME) {
			return true;
		}
		return false;
	}

	function check_address($address) {
		$address = trim($address);
		if (strlen($address) <= LEN_ADDRESS && strlen($address) > 0) {
			return true;
		}
		return false;
	}

	function check_fax($fax) {
		$fax = trim($fax);
		if (strlen($fax) <= LEN_FAX && strlen($fax) >= 0) {
			return true;
		}
		return false;
	}

	function check_contact($contact) {
		$contact = trim($contact);
		if (strlen($contact) <= LEN_CONTACT && strlen($contact) > 0) {
			return true;
		}
		return false;
	}

	function check_title($title) {
		$title = trim($title);
		if (strlen($title) <= LEN_TITLE && strlen($title) > 0) {
			return true;
		}
		return false;
	}

	function check_epa($epa) {
		$epa = trim($epa);
		if (strlen($epa) <= LEN_FACILITY_EPA && strlen($epa) > 0) {
			return true;
		}
		return false;
	}

	function check_product_nr($product_nr) {
		$product_nr = trim($product_nr);
		if (strlen($product_nr) <= LEN_PRODUCT_NR && strlen($product_nr) > 0) {
			return true;
		}
		return false;
	}

	function check_product_desc($product_desc) {
		$product_desc = trim($product_desc);
		if (strlen($product_desc) <= LEN_PRODUCT_DESC && strlen($product_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_component_id($component_id) {
		$component_id = trim($component_id);
		if (strlen($component_id) <= LEN_COMPONENT_ID && strlen($component_id) > 0) {
			return true;
		}
		return false;
	}

	function check_densityuse($densityuse) {
		$densityuse = trim($densityuse);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($densityuse, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_densitytype_id($densitytype_id) {
		$densitytype_id = trim($densitytype_id);
		if (strlen($densitytype_id) <= LEN_DENSITYTYPE_ID && strlen($densitytype_id) > 0) {
			return true;
		}
		return false;
	}

	function check_unittype_id($unittype_id) {
		$unittype_id = trim($unittype_id);
		if (strlen($unittype_id) <= LEN_UNITTYPE_ID && strlen($unittype_id) > 0) {
			return true;
		}
		return false;
	}

	function check_rule($rule) {
		$rule = trim($rule);
		if (strlen($rule) <= LEN_RULE && strlen($rule) > 0) {
			return true;
		}
		return false;
	}

	function check_coat_id($coat_id) {
		$coat_id = trim($coat_id);
		if (strlen($coat_id) <= LEN_COAT_ID && strlen($coat_id) > 0) {
			return true;
		}
		return false;
	}

	function check_substrate_id($substrate_id) {
		$substrate_id = trim($substrate_id);
		if (strlen($substrate_id) <= LEN_SUBSTRATE_ID && strlen($substrate_id) > 0) {
			return true;
		}
		return false;
	}

	function check_apmethod_id($apmethod_id) {
		$apmethod_id = trim($apmethod_id);
		if (strlen($apmethod_id) <= LEN_APMETHOD_ID && strlen($apmethod_id) > 0) {
			return true;
		}
		return false;
	}

	function check_inventory_name($inventory_name) {
		$inventory_name = trim($inventory_name);
		if (strlen($inventory_name) <= LEN_INVENTORY_NAME && strlen($inventory_name) > 0) {
			return true;
		}
		return false;
	}

	function check_inventory_desc($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		if (strlen($inventory_desc) <= LEN_INVENTORY_DESC && strlen($inventory_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_quantity($quantity) {
		$quantity = trim($quantity);
		$parametrs = array('min' => 0.000000000001, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($quantity, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_quantity_inv($quantity) {
		$quantity = trim($quantity);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($quantity, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_equip_desc($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		if (strlen($inventory_desc) <= LEN_EQUIP_DESC && strlen($inventory_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_permit($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		if (strlen($inventory_desc) <= LEN_PERMIT && strlen($inventory_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_expire($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		if (strlen($inventory_desc) <= LEN_EXPIRE && strlen($inventory_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_voc_pct($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		$parametrs = array('min' => 0, 'max' => 100, 'decimal' => ',.');
		if ($this->noYes[Validate::number($inventory_desc, $parametrs)] == 'YES') {
			return true;
			echo "yes";
		}
		return false;
	}

	function check_voc_desc($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		if (strlen($inventory_desc) <= LEN_VOC_DESC && strlen($inventory_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_pm_pct($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		$parametrs = array('min' => 0, 'max' => 100, 'decimal' => ',.');
		if ($this->noYes[Validate::number($inventory_desc, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_pm_desc($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		if (strlen($inventory_desc) <= LEN_PM_DESC && strlen($inventory_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_daily($inventory_desc) {
		$inventory_desc = trim($inventory_desc);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($inventory_desc, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	function check_apmethod_desc($apmethod_desc) {
		$apmethod_desc = trim($apmethod_desc);
		if (strlen($apmethod_desc) <= LEN_APMETHOD_DESC && strlen($apmethod_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_coat_desc($coat_desc) {
		$coat_desc = trim($coat_desc);
		if (strlen($coat_desc) <= LEN_COAT_DESC && strlen($coat_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_density_type($density_type) {
		$density_type = trim($density_type);
		if (strlen($density_type) <= LEN_DENSITY_TYPE && strlen($density_type) > 0) {
			return true;
		}
		return false;
	}

	function check_hazardous_class($density_type) {
		$density_type = trim($density_type);
		if (strlen($density_type) <= LEN_HAZARDOUS_TYPE && strlen($density_type) > 0) {
			return true;
		}
		return false;
	}

	function check_substrate_desc($substrate_desc) {
		$substrate_desc = trim($substrate_desc);
		if (strlen($substrate_desc) <= LEN_SUBSTRATE_DESC && strlen($substrate_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_country_name($country_name) {
		$country_name = trim($country_name);
		if (strlen($country_name) <= LEN_COUNTRY_NAME && strlen($country_name) > 0) {
			return true;
		}
		return false;
	}

	function check_state_name($state_name) {
		$state_name = trim($state_name);
		if (strlen($state_name) <= LEN_STATE_NAME && strlen($state_name) > 0) {
			return true;
		}
		return false;
	}

	function check_county($county) {
		$county = trim($county);
		if (strlen($county) <= LEN_STATE_NAME) {
			return true;
		}
		return false;
	}

	function check_supplier($supplier) {
		$supplier = trim($supplier);
		if (strlen($supplier) <= LEN_SUPPLIER && strlen($supplier) > 0) {
			return true;
		}
		return false;
	}

	function check_type_desc($type_desc) {
		$type_desc = trim($type_desc);
		if (strlen($type_desc) <= LEN_TYPE_DESC && strlen($type_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_cas($cas) {
		$cas = trim($cas);
		if (strlen($cas) <= LEN_CAS && strlen($cas) > 0) {
			return true;
		}
		return false;
	}

	function check_cas_desc($cas_desc) {
		$cas_desc = trim($cas_desc);
		if (strlen($cas_desc) <= LEN_CAS_DESC && strlen($cas_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_voclx($voclx) {
		$voclx = trim($voclx);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($voclx, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_vocwx($vocwx) {
		$vocwx = trim($vocwx);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($vocwx, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_temp_vp($temp_vp) {
		$temp_vp = trim($temp_vp);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($temp_vp, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_mm_hg($temp_vp) {
		$temp_vp = trim($temp_vp);
		$parametrs = array('min' => 0, 'max' => 99999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($temp_vp, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_rule_nr($rule_nr) {
		$rule_nr = trim($rule_nr);
		if (strlen($rule_nr) <= LEN_RULE_NR && strlen($rule_nr) > 0) {
			return true;
		}
		return false;
	}

	function check_rule_desc($rule_desc) {
		$rule_desc = trim($rule_desc);
		if (strlen($rule_desc) <= LEN_RULE_DESC && strlen($rule_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_product_code($product_code) {
		$product_code = trim($product_code);
		if (strlen($product_code) <= LEN_PRODUCT_CODE && strlen($product_code) > 0) {
			return true;
		}
		return false;
	}

	function check_comp_name($comp_name) {
		$comp_name = trim($comp_name);
		if (strlen($comp_name) <= LEN_COMP_NAME && strlen($comp_name) > 0) {
			return true;
		}
		return false;
	}

	function check_comp_weight($comp_weight) {
		$comp_weight = trim($comp_weight);
		$parametrs = array('min' => 0, 'max' => 9999999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($comp_weight, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_boiling_range($comp_weight) {
		$comp_weight = trim($comp_weight);
		$parametrs = array('min' => 0, 'max' => 9999999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($comp_weight, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_comp_density($comp_density) {
		$comp_density = trim($comp_density);
		$parametrs = array('min' => 0, 'max' => 100, 'decimal' => ',.');
		if ($this->noYes[Validate::number($comp_density, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_voc_limit($voc_limit) {
		$voc_limit = trim($voc_limit);
		$parametrs = array('min' => 0, 'max' => 99999999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($voc_limit, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_monthly_nox_limit($monthly_nox_limit) {
		$monthly_nox_limit = trim($monthly_nox_limit);
		$parametrs = array('min' => 0, 'max' => 99999999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($monthly_nox_limit, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_specific_gravity($voc_limit) {
		$voc_limit = trim($voc_limit);
		$parametrs = array('min' => 0, 'max' => 99999999999999, 'decimal' => ',.');
		if ($this->noYes[Validate::number($voc_limit, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	function check_description($description) {
		$description = trim($description);
		if (strlen($description) <= LEN_DESCRIPTION && strlen($description) > 0) {
			return true;
		}
		return false;
	}

	function check_name($name) {
		$name = trim($name);
		if (strlen($name) <= LEN_NAME && strlen($name) > 0) {
			return true;
		}
		return false;
	}

	function check_unittype_desc($unittype_desc) {
		$unittype_desc = trim($unittype_desc);
		if (strlen($unittype_desc) <= LEN_UNITTYPE_DESC && strlen($unittype_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_lol_name($lol_name) {
		$lol_name = trim($lol_name);
		if (strlen($lol_name) <= LEN_LOL_NAME && strlen($lol_name) > 0) {
			return true;
		}
		return false;
	}

	function check_formula($formula) {
		$formula = trim($formula);
		if (strlen($formula) <= LEN_FORMULA && strlen($formula) > 0) {
			return true;
		}
		return false;
	}

	function check_formula_desc($formula_desc) {
		$formula_desc = trim($formula_desc);
		if (strlen($formula_desc) <= LEN_FORMULA_DESC && strlen($formula_desc) > 0) {
			return true;
		}
		return false;
	}

	function check_agency_name($name, $isMain = 0) {
		$name = trim($name);
		if (strlen($name) <= LEN_AGENCY_NAME && (( strlen($name) > 0 && $isMain == 1) || ($isMain == 0 && strlen($name) >= 0))) {
			return true;
		}
		return false;
	}

	function check_expire_date($expireDate) {
		$expireDate = trim($expireDate);
		if (trim($expireDate) != "") {
			return true;
		}
		return false;
	}

	function check_percent_value($percent) {
		$percent = trim($percent);
		$parametrs = array('min' => 0, 'max' => 100, 'decimal' => ',.');
		if ($this->noYes[Validate::number($percent, $parametrs)] == 'YES') {
			return true;
		}
		return false;
	}

	/**
	 * Check string for float/integer value in it
	 * @param string $float
	 * @return boolean true if float, false if not
	 */
	public static function isFloat($float) {
		$float = trim($float);
		$parametrs = array('min' => 0, 'max' => 99999999999999, 'decimal' => ',.');
		return Validate::number($float, $parametrs);
	}

	/**
	 * Check string for percent value
	 * @param string $percent may contain % sign
	 * @return boolean
	 */
	public static function isPercent($percent, $signIncluded = false) {
		$percent = trim($percent);
		if ($signIncluded) {
			if (substr($percent, -1) != "%") {
				return false;
			}
			$percent = trim(substr($percent, 0, -1));
		}
		$parametrs = array('min' => 0, 'max' => 1000, 'decimal' => ',.');
		return Validate::number($percent, $parametrs);
	}

	function validateRegData($data) {
		$result['summary'] = 'true';

		if (isset($data['email'])) {
			if ($this->check_email($data['email'])) {
				$result['email'] = 'success';
			} else {
				$result['email'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		/*
		  if (isset($data['supplier'])) {
		  $result['supplier']='success';
		  } else {
		  $result['supplier']='failed';
		  $result['summary']='false';
		  }

		  if (isset($data['jobber'])) {
		  $result['jobber']='success';
		  } else {
		  $result['jobber']='failed';
		  $result['summary']='false';
		  }
		 */

		if (isset($data['id'])) {
			if ($this->check_id($data['id'])) {
				$result['id'] = 'success';
			} else {
				$result['id'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['zip'])) {
			if ($this->check_zip($data['zip'])) {
				$result['zip'] = 'success';
			} else {
				$result['zip'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['state'])) {
			if ($this->check_state($data['state'])) {
				$result['state'] = 'success';
			} else {
				$result['state'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['country_id'])) {
			if ($this->check_countryID($data['countryID'])) {
				$result['country_id'] = 'success';
			} else {
				$result['country_id'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['city'])) {

			if ($this->check_city($data['city'])) {
				$result['city'] = 'success';
			} else {
				$result['city'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['phone'])) {
			if ($this->check_phone($data['phone'])) {
				$result['phone'] = 'success';
			} else {
				$result['phone'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['mobile'])) {
			if ($this->check_mobile($data['mobile'])) {
				$result['mobile'] = 'success';
			} else {
				$result['mobile'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['name'])) {
			if ($this->check_nameCompany($data['name'])) {
				$result['name'] = 'success';
			} else {
				$result['name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['epa'])) {
			if ($this->check_epa($data['epa'])) {
				$result['epa'] = 'success';
			} else {
				$result['epa'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['title'])) {
			if ($this->check_title($data['title'])) {
				$result['title'] = 'success';
			} else {
				$result['title'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['contact'])) {
			if ($this->check_contact($data['contact'])) {
				$result['contact'] = 'success';
			} else {
				$result['contact'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['fax'])) {
			if ($this->check_fax($data['fax'])) {
				$result['fax'] = 'success';
			} else {
				$result['fax'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['address'])) {
			if ($this->check_address($data['address'])) {
				$result['address'] = 'success';
			} else {
				$result['address'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['description'])) {
			if ($this->check_description($data['description'])) {
				$result['description'] = 'success';
			} else {
				$result['description'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['quantity'])) {
			if ($this->check_quantity($data['quantity'])) {
				$result['quantity'] = 'success';
			} else {
				$result['quantity'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voc_limit'])) {
			if ($this->check_voc_limit($data['voc_limit'])) {
				$result['voc_limit'] = 'success';
			} else {
				$result['voc_limit'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['monthly_nox_limit'])) {
			if ($this->check_monthly_nox_limit($data['monthly_nox_limit'])) {
				$result['monthly_nox_limit'] = 'success';
			} else {
				$result['monthly_nox_limit'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voc_annual_limit'])) {
			if ($this->check_voc_limit($data['voc_annual_limit'])) {
				$result['voc_annual_limit'] = 'success';
			} else {
				$result['voc_annual_limit'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voc'])) {
			if ($this->check_vocwx($data['voc'])) {
				$result['voc'] = 'success';
			} else {
				$result['voc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voclx'])) {
			if ($this->check_voclx($data['voclx'])) {
				$result['voclx'] = 'success';
			} else {
				$result['voclx'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['vocwx'])) {
			if ($this->check_vocwx($data['vocwx'])) {
				$result['vocwx'] = 'success';
			} else {
				$result['vocwx'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['creationTime'])) {
			if ($this->check_creation_time($data['creationTime'])) {
				$result['creationTime'] = 'success';
			} else {
				$result['creationTime'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['waste'])) {
			//	percent
			if ($data['waste']['unittypeClass'] == 'percent') {
				if ($this->check_percent_value($data['waste']['value'])) {
					$result['waste']['value'] = 'success';
				} else {
					$result['waste']['value'] = 'failed';
					$result['summary'] = 'false';
				}
				//	weight
			} else {
				if ($this->check_quantity_inv($data['waste']['value'])) {
					$result['waste']['value'] = 'success';
				} else {
					$result['waste']['value'] = 'failed';
					$result['summary'] = 'false';
				}
			}
		}

		return $result;
	}

	public function validateRegDataProduct($data) {

		$result['summary'] = 'true';

		if (isset($data['components'])) {
			$result['isComponents'] = 'success';
		} else {
			$result['isComponents'] = 'failed';
			$result['summary'] = 'false';
		}

		if (isset($data['density'])) {
			if ($this->check_densityuse($data['density'])) {
				$result['density'] = 'success';
			} else {
				$result['density'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['percent_volatile_weight'])) {
			if ($this->check_percent_value($data['percent_volatile_weight'])) {
				$result['percent_volatile_weight'] = 'success';
			} else {
				$result['percent_volatile_weight'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['percent_volatile_volume'])) {
			if ($this->check_percent_value($data['percent_volatile_volume'])) {
				$result['percent_volatile_volume'] = 'success';
			} else {
				$result['percent_volatile_volume'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['product_id'])) {
			if (trim($data['product_id']) == '' || $data['product_id'] == 0) {
				$result['product_id'] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['product_id'] = 'success';
			}
		}

		if (isset($data['product_nr'])) {
			if ($this->check_product_nr($data['product_nr'])) {
				$result['product_nr'] = 'success';
			} else {
				$result['product_nr'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['name'])) {
			if ($this->check_product_desc($data['name'])) {
				$result['name'] = 'success';
			} else {
				$result['name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['vocwx'])) {
			if ($this->check_vocwx($data['vocwx'])) {
				$result['vocwx'] = 'success';
			} else {
				$result['vocwx'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voclx'])) {
			if ($this->check_voclx($data['voclx'])) {
				$result['voclx'] = 'success';
			} else {
				$result['voclx'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['specific_gravity'])) {
			if ($this->check_specific_gravity($data['specific_gravity'])) {
				$result['specific_gravity'] = 'success';
			} else {
				$result['specific_gravity'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['hazardous_class'])) {
			if ($this->check_hazardous_class($data['hazardous_class'])) {
				$result['hazardous_class'] = 'success';
			} else {
				$result['hazardous_class'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['boiling_range_from'])) {
			if ($this->check_boiling_range($data['boiling_range_from'])) {
				$result['boiling_range_from'] = 'success';
			} else {
				$result['boiling_range_from'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['boiling_range_to'])) {
			if ($this->check_boiling_range($data['boiling_range_to'])) {
				$result['boiling_range_to'] = 'success';
			} else {
				$result['boiling_range_to'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if ($result['boiling_range_to'] == 'success' && $result['boiling_range_from'] == 'success') {
			if ($data['boiling_range_to'] < $data['boiling_range_from']) {
				$result['boiling_range_from'] = 'failed';
				$result['boiling_range_to'] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['boiling_range_from'] = 'success';
				$result['boiling_range_to'] = 'success';
			}
		}

		if (isset($data['quantity'])) {
			if ($this->check_quantity($data['quantity'])) {
				$result['quantity'] = 'success';
			} else {
				$result['quantity'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		return $result;
	}

	public function validateRegDataEquipment($data) {

		$result['summary'] = 'true';

		if (isset($data['expire_date'])) {
			if ($this->check_expire_date($data['expire_date'])) {
				$result['expire_date'] = 'success';
			} else {
				$result['expire_date'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['equip_desc'])) {
			if ($this->check_equip_desc($data['equip_desc'])) {
				$result['equip_desc'] = 'success';
			} else {
				$result['equip_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['permit'])) {
			if ($this->check_permit($data['permit'])) {
				$result['permit'] = 'success';
			} else {
				$result['permit'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['expire'])) {
			if ($this->check_expire($data['expire'])) {
				$result['expire'] = 'success';
			} else {
				$result['expire'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voc_pct'])) {
			if ($this->check_voc_pct($data['voc_pct'])) {
				$result['voc_pct'] = 'success';
			} else {
				$result['voc_pct'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voc_desc'])) {
			if ($this->check_voc_desc($data['voc_desc'])) {
				$result['voc_desc'] = 'success';
			} else {
				$result['voc_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['pm_pct'])) {
			if ($this->check_pm_pct($data['pm_pct'])) {
				$result['pm_pct'] = 'success';
			} else {
				$result['pm_pct'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['pm_desc'])) {
			if ($this->check_pm_desc($data['pm_desc'])) {
				$result['pm_desc'] = 'success';
			} else {
				$result['pm_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['daily'])) {
			if ($this->check_daily($data['daily'])) {
				$result['daily'] = 'success';
			} else {
				$result['daily'] = 'failed';
				$result['summary'] = 'false';
			}
		}


		return $result;
	}

	public function validateRegDataInventory($data, $productCount) {

		$result['summary'] = 'true';


		if (isset($data['inventory_name'])) {
			if ($this->check_inventory_name($data['inventory_name'])) {
				$result['inventory_name'] = 'success';
			} else {
				$result['inventory_name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['inventory_desc'])) {
			if ($this->check_inventory_desc($data['inventory_desc'])) {
				$result['inventory_desc'] = 'success';
			} else {
				$result['inventory_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		$quantityValues = array('quantity', 'OSuse', 'CSuse');

		if ($productCount == 0) {
			$result['summary'] = 'false';
			$result['product'] = 'failed';
		}

		for ($i = 0; $i < $productCount; $i++) {
			foreach ($quantityValues as $value) {
				if (isset($data['products'][$i][$value])) {
					if ($data['products'][$i][$value] == '') {
						$result['products'][$i][$value] = 'success';
					} else if ($this->check_quantity_inv($data['products'][$i][$value])) {
						$result['products'][$i][$value] = 'success';
					} else {
						$result['products'][$i][$value] = 'failed';
						$result['summary'] = 'false';
					}
				}
			}
		}

		return $result;
	}

	public function validateRegDataAdminClasses($data) {

		$result['summary'] = 'true';

		if (isset($data['apmethod_desc'])) {
			if ($this->check_apmethod_desc($data['apmethod_desc'])) {
				$result['apmethod_desc'] = 'success';
			} else {
				$result['apmethod_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['coat_desc'])) {
			if ($this->check_coat_desc($data['coat_desc'])) {
				$result['coat_desc'] = 'success';
			} else {
				$result['coat_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['density_type'])) {
			if ($this->check_density_type($data['density_type'])) {
				$result['density_type'] = 'success';
			} else {
				$result['density_type'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['substrate'])) {
			if ($this->check_substrate_desc($data['substrate'])) {
				$result['substrate'] = 'success';
			} else {
				$result['substrate'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['substrate_desc'])) {
			if ($this->check_substrate_desc($data['substrate_desc'])) {
				$result['description'] = 'success';
			} else {
				$result['description'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['country_name'])) {
			if ($this->check_country_name($data['country_name'])) {
				$result['country_name'] = 'success';
			} else {
				$result['country_name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['country'])) {
			if ($this->check_country_name($data['country'])) {
				$result['country'] = 'success';
			} else {
				$result['country'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['state_name'])) {
			if ($this->check_state_name($data['state_name'])) {
				$result['state_name'] = 'success';
			} else {
				$result['state_name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['state'])) {
			if ($this->check_state_name($data['state'])) {
				$result['state'] = 'success';
			} else {
				$result['state'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['supplier'])) {
			if ($this->check_supplier($data['supplier'])) {
				$result['supplier'] = 'success';
			} else {
				$result['supplier'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['type'])) {
			if ($this->check_type_desc($data['type'])) {
				$result['type'] = 'success';
			} else {
				$result['type'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['cas'])) {
			if ($this->check_cas($data['cas'])) {
				$result['cas'] = 'success';
			} else {
				$result['cas'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['cas_desc'])) {
			if ($this->check_cas_desc($data['cas_desc'])) {
				$result['cas_desc'] = 'success';
			} else {
				$result['cas_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['voclx'])) {
			if ($this->check_voclx($data['voclx'])) {
				$result['voclx'] = 'success';
			} else {
				$result['voclx'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['vocwx'])) {
			if ($this->check_vocwx($data['vocwx'])) {
				$result['vocwx'] = 'success';
			} else {
				$result['vocwx'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['temp_vp'])) {
			if ($this->check_temp_vp($data['temp_vp'])) {
				$result['temp_vp'] = 'success';
			} else {
				$result['temp_vp'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['country_id'])) {
			if ($this->check_countryID($data['country_id'])) {
				$result['country_id'] = 'success';
			} else {
				$result['country_id'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['city'])) {
			if ($this->check_city($data['city'])) {
				$result['city'] = 'success';
			} else {
				$result['city'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['county'])) {
			if ($this->check_county($data['county'])) {
				$result['county'] = 'success';
			} else {
				$result['county'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['zip'])) {
			if ($this->check_zip($data['zip'])) {
				$result['zip'] = 'success';
			} else {
				$result['zip'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['rule_nr'])) {
			if ($this->check_rule_nr($data['rule_nr'])) {
				$result['rule_nr'] = 'success';
			} else {
				$result['rule_nr'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['rule_desc'])) {
			if ($this->check_rule_desc($data['rule_desc'])) {
				$result['description'] = 'success';
			} else {
				$result['description'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['product_code'])) {
			if ($this->check_product_code($data['product_code'])) {
				$result['product_code'] = 'success';
			} else {
				$result['product_code'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['comp_name'])) {
			if ($this->check_comp_name($data['comp_name'])) {
				$result['comp_name'] = 'success';
			} else {
				$result['comp_name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['comp_weight'])) {
			if ($this->check_comp_weight($data['comp_weight'])) {
				$result['comp_weight'] = 'success';
			} else {
				$result['comp_weight'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['comp_density'])) {
			if ($this->check_comp_density($data['comp_density'])) {
				$result['comp_density'] = 'success';
			} else {
				$result['comp_density'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['description']) && !isset($data['agency_id'])) {
			if ($this->check_description($data['description'])) {
				$result['description'] = 'success';
			} else {
				$result['description'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['name'])) {
			if ($this->check_name($data['name'])) {
				$result['name'] = 'success';
			} else {
				$result['name'] = 'failed';
				$result['summary'] = 'false';
			}
		}


		if (isset($data['lol_name'])) {
			if ($this->check_name($data['lol_name'])) {
				$result['lol_name'] = 'success';
			} else {
				$result['lol_name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['formula'])) {
			if ($this->check_formula($data['formula'])) {
				$result['formula'] = 'success';
			} else {
				$result['formula'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['formula_desc'])) {
			if ($this->check_formula_desc($data['formula_desc'])) {
				$result['formula_desc'] = 'success';
			} else {
				$result['formula_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['name'])) {
			if ($this->check_agency_name($data['name'], 1)) {
				$result['name'] = 'success';
			} else {
				$result['name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['name_us']) || isset($data['name_eu']) || isset($data['name_cn'])) {
			if ($this->check_agency_name($data['name_us'])) {
				$result['name_us'] = 'success';
			} else {
				$result['name_us'] = 'failed';
				$result['summary'] = 'false';
			}
			if ($this->check_agency_name($data['name_eu'])) {
				$result['name_eu'] = 'success';
			} else {
				$result['name_eu'] = 'failed';
				$result['summary'] = 'false';
			}
			if ($this->check_agency_name($data['name_cn'])) {
				$result['name_cn'] = 'success';
			} else {
				$result['name_cn'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['address'])) {
			if ($this->check_address($data['address'])) {
				$result['address'] = 'success';
			} else {
				$result['address'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['phone'])) {
			if ($this->check_phone($data['phone'])) {
				$result['phone'] = 'success';
			} else {
				$result['phone'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['contact'])) {
			if ($this->check_contact($data['contact'])) {
				$result['contact'] = 'success';
			} else {
				$result['contact'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['emissionFactor'])) {
			if ($this->check_vocwx($data['emissionFactor'])) {
				$result['emissionFactor'] = 'success';
			} else {
				$result['emissionFactor'] = 'failed';
				$result['summary'] = 'false';
			}
		}


		return $result;
	}

	public function validateRegDataMakeInventory($data, $productCount) {

		$result['summary'] = 'true';

		if (isset($data['inventory_name'])) {
			if ($this->check_inventory_name($data['inventory_name'])) {
				$result['inventory_name'] = 'success';
			} else {
				$result['inventory_name'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		if (isset($data['inventory_desc'])) {
			if ($this->check_inventory_desc($data['inventory_desc'])) {
				$result['inventory_desc'] = 'success';
			} else {
				$result['inventory_desc'] = 'failed';
				$result['summary'] = 'false';
			}
		}


		for ($i = 0; $i < $productCount; $i++) {

			if (isset($data['products'][$i]['quantity'])) {
				if ($this->check_quantity($data['products'][$i]['quantity'])) {
					$result[$i]['quantity'] = 'success';
				} else {
					$result[$i]['quantity'] = 'failed';
					$result['summary'] = 'false';
				}
			}
		}




		return $result;
	}

	public function validateRegDataUsage($data) {

		$result['summary'] = 'true';

		if (isset($data['description'])) {
			if ($this->check_description($data['description'])) {
				$result['description'] = 'success';
			} else {
				$result['description'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		for ($i = 0; $i < count($data['products']); $i++) {

			if (isset($data['products'][$i]['quantity'])) {
				if ($this->check_quantity($data['products'][$i]['quantity'])) {
					$result['products'][$i]['quantity'] = 'success';
				} else {
					$result['products'][$i]['quantity'] = 'failed';
					$result['summary'] = 'false';
				}
			}
		}

		return $result;
	}

	public function validateNewComponent($data) {
		$result['summary'] = 'true';

		if (isset($data['temp_vp'])) {
			if ($this->check_temp_vp($data['temp_vp'])) {
				$result['temp_vp'] = 'success';
			} else {
				$result['temp_vp'] = 'failed';
				$result['summary'] = 'false';
			}
		}
		/*
		  if (isset($data['mm_hg'])) {
		  if ($this->check_mm_hg($data['mm_hg'])) {
		  $result['mm_hg']='success';
		  } else {
		  $result['mm_hg']='failed';
		  $result['summary']='false';
		  }
		  }
		 */
		if (isset($data['weight'])) {
			if ($this->check_comp_weight($data['weight'])) {
				$result['weight'] = 'success';
			} else {
				$result['weight'] = 'failed';
				$result['summary'] = 'false';
			}
		}

		return $result;
	}

	public function validateIssue($issue) {
		$result["summary"] = "true";

		$result["title"] = "failed";
		if (isset($issue["title"])) {
			$issue["title"] = trim($issue["title"]);
			$titleLength = strlen($issue["title"]);

			if ($titleLength > 0 && $titleLength < 120) {
				$result["title"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}

		$result["description"] = "failed";
		if (isset($issue["description"])) {
			$issue["description"] = trim($issue["description"]);
			$descLength = strlen($issue["description"]);

			if ($descLength > 0) {
				$result["description"] = "success";
			} else {
				$result["summary"] = "false";
			}
		} else {
			$result["summary"] = "false";
		}

		return $result;
	}

	public function checkWeight2Volume($productID, $unitTypeID) {
//		$this->db->select_db(DB_NAME);
		$unittype = new Unittype($this->db);
		$WeightOrVolume = $unittype->isWeightOrVolume($unitTypeID);
		$query = "SELECT vocwx, percent_volatile_weight, percent_volatile_volume FROM " . TB_PRODUCT . " WHERE product_id = " . $productID;
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {
			$data = $this->db->fetch(0);
			switch ($WeightOrVolume) {
				case 'weight':
					if (empty($data->percent_volatile_weight) || $data->percent_volatile_weight == '0.000') {
						if ((!empty($data->vocwx) && $data->vocwx != '0.00')/* && (!empty($data->percent_volatile_volume) && $data->percent_volatile_volume != '0.000') */) {
							if (!$this->checkDensitySet($productID)) {
								return 'weight2volumeConflict';
							} else {
								return true;
							}
						} else {
							return true; //in that case we add product with voc = 0 and throw warning in mix->calculateCurrantUsage
						}
					} else {
						return true; //all ok!
					}
					break;
				case 'volume':
					if ((empty($data->vocwx) || $data->vocwx == '0.00')/* && (empty($data->percent_volatile_volume) || $data->percent_volatile_volume == '0.000') */) {
						if (!empty($data->percent_volatile_weight) && $data->percent_volatile_weight != '0.000') {
							if (!$this->checkDensitySet($productID)) {
								return 'volume2weightConflict';
							} else {
								return true;
							}
						} else {
							return true; //in that case we add product with voc = 0 and throw warning in mix->calculateCurrantUsage
						}
					} else {
						return true; //all ok!
					}
					break;
				default:
				//if needs type = distance
			}
		}
	}

	public function checkWaste($mixRecord, $wasteUnitTypeID) {
		if (!$wasteUnitTypeID) {
			return true; // waste was set in %
		}
		$isError = false;
		$unittype = new Unittype($this->db);
		$isDensity = true;
		$wasteUnitDetails = $unittype->getUnittypeDetails($wasteUnitTypeID);
		$mixUnitTypeID = $mixRecord->getUnitType();
		$mixUnitTypeType = $unittype->isWeightOrVolume($mixUnitTypeID);
		$wasteUnitTypeType = $unittype->isWeightOrVolume($wasteUnitTypeID);
		if ($wasteUnitTypeType === $mixUnitTypeType) {
			return true;
		} else {
			$density = $mixRecord->getProduct()->getDensity();
			if (empty($density) || $density == '0.00') {
				return false;
			} else {
				return true;
			}
		}
	}

	public function checkDensitySet($productID) {
		$query = "SELECT density FROM " . TB_PRODUCT . " WHERE product_id = " . $productID;
		$this->db->query($query);

		if ($this->db->num_rows() > 0) {
			$density = $this->db->fetch(0)->density;
			if (empty($density) || $density == '0.00') {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function check_creation_time($mmddyyyy) {
		if (preg_match('/^\d{2}\-\d{2}\-\d{4}$/', $mmddyyyy)) {
			$mm = substr($mmddyyyy, 0, 2);
			$dd = substr($mmddyyyy, 3, 2);
			$yyyy = substr($mmddyyyy, 6, 4);
			$currentDate = getdate();
			if ($currentDate['year'] > $yyyy || ($currentDate['year'] == $yyyy && $currentDate['mon'] > $mm) ||
					($currentDate['year'] == $yyyy && $currentDate['mon'] == $mm && $currentDate['mday'] >= $dd)) {
				return true;
			} else
				return false;
		} else
			return false;
	}

	public function validateAccessoryUsage($form, TypeChain $dateChain) {
		$result["summary"] = true;

		if (!$dateChain->getTimestamp()) {
			$result["summary"] = false;
			$result["date"] = "Wrong date format";
		}

		//	process usage
		if (!$this->check_quantity($form['usage'])) {
			$result["summary"] = false;
			$result["usage"] = "Usage should be countable";
		}

		return $result;
	}

	public function validateNoxBurner(NoxBurner $burner) {
		$result = array(
			'summary' => true
		);

		if (!$this->check_name($burner->model)) {
			$result['summary'] = 'false';
			$result['model'] = 'failed';
		}

		if (!$this->check_name($burner->serial)) {
			$result['summary'] = 'false';
			$result['serial'] = 'failed';
		}

		if (!$this->check_id($burner->manufacturer_id)) {
			$result['summary'] = 'false';
			$result['manufacturer_id'] = 'failed';
		}

		if (!$this->check_quantity($burner->input)) {
			$result['summary'] = 'false';
			$result['input'] = 'failed';
		}

		if (!$this->check_quantity($burner->output)) {
			$result['summary'] = 'false';
			$result['output'] = 'failed';
		}

		if (!$this->check_quantity($burner->btu)) {
			$result['summary'] = 'false';
			$result['btu'] = 'failed';
		}

		return $result;
	}

	public function validateNoxEmission(NoxEmission $noxEmission) {
		$result = array(
			'summary' => true
		);

		if (!$this->check_name($noxEmission->description)) {
			$result['summary'] = 'false';
			$result['description'] = 'failed';
		} else {
			// check for duplicate names
			if (!$noxEmission->nox_id
					&& $result['summary'] == 'true'
					&& !$this->isUniqueName("nox", $noxEmission->description, $noxEmission->department_id)) {
				$result['summary'] = 'false';
				$result['description'] = 'alreadyExist';
			}
		}

		if (!$this->check_quantity($noxEmission->gas_unit_used) && !empty($noxEmission->gas_unit_used)) {
			$result['summary'] = 'false';
			$result['gas_unit_used'] = 'failed';
		}

		if (!$this->check_expire_date($noxEmission->start_time)) {
			$result['summary'] = 'false';
			$result['start_time'] = 'failed';
		}

		if (!$this->check_expire_date($noxEmission->end_time)) {
			$result['summary'] = 'false';
			$result['end_time'] = 'failed';
		}

		if ($noxEmission->end_time <= $noxEmission->start_time) {
			$result['summary'] = 'false';
			$result['end_time'] = 'failed';
		}

		if (!$this->check_id($noxEmission->burner_id)) {
			$result['summary'] = 'false';
			$result['burner_id'] = 'failed';
		}

		return $result;
	}

	public function validateRegDataEquipmentLighting(EquipmentLighting $equipmentLighting, $equipmentLightingId) {

		$result['summary'] = 'true';
		$parametrs = array('min' => 0, 'max' => 1000);

		if (isset($equipmentLighting->name)) {
			if ($equipmentLighting->name == "") {
				$result['eq_lighting_name_' . $equipmentLightingId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_lighting_name_' . $equipmentLightingId] = 'success';
			}
		}
		if (isset($equipmentLighting->voltage)) {
			if ($equipmentLighting->voltage == "") {
				$result['eq_lighting_voltage_' . $equipmentLightingId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_lighting_voltage_' . $equipmentLightingId] = 'success';
			}
		}
		if (isset($equipmentLighting->size)) {
			if ($equipmentLighting->size == "") {
				$result['eq_lighting_size_' . $equipmentLightingId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_lighting_size_' . $equipmentLightingId] = 'success';
			}
		}
		if (isset($equipmentLighting->wattage)) {
			if ($equipmentLighting->wattage == "" || $this->noYes[Validate::number($equipmentLighting->wattage, $parametrs)] == 'NO') {

				$result['eq_lighting_wattage_' . $equipmentLightingId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_lighting_wattage_' . $equipmentLightingId] = 'success';

			}
		}

		return $result;
	}

	public function validateRegDataEquipmentFilter(EquipmentFilter $equipmentFilter, $equipmentFilterId) {

		$result = array();
		$result['summary'] = 'true';
		$parametrs = array('min' => 0, 'max' => 1000);

		if (isset($equipmentFilter->name)) {
			if ($equipmentFilter->name == '') {
				$result['eq_filter_name_' . $equipmentFilterId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_filter_name_' . $equipmentFilterId] = 'success';
			}
		}

		if (isset($equipmentFilter->height_size)) {
			if ($equipmentFilter->height_size == "" || $this->noYes[Validate::number($equipmentFilter->height_size, $parametrs)] == 'NO') {

				$result['eq_filter_height_size_' . $equipmentFilterId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_filter_height_size_' . $equipmentFilterId] = 'success';

			}
		}
		if (isset($equipmentFilter->width_size)) {
			if ($equipmentFilter->width_size == "" || $this->noYes[Validate::number($equipmentFilter->width_size, $parametrs)] == 'NO') {

				$result['eq_filter_width_size_' . $equipmentFilterId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_filter_width_size_' . $equipmentFilterId] = 'success';

			}
		}
		if (isset($equipmentFilter->length_size)) {
			if ($equipmentFilter->length_size == "" || $this->noYes[Validate::number($equipmentFilter->length_size, $parametrs)] == 'NO') {

				$result['eq_filter_length_size_' . $equipmentFilterId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_filter_length_size_' . $equipmentFilterId] = 'success';

			}
		}
		if (isset($equipmentFilter->qty)) {
			if ($equipmentFilter->qty == "" || $this->noYes[Validate::number($equipmentFilter->qty, $parametrs)] == 'NO') {

				$result['eq_filter_quantity_' . $equipmentFilterId] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['eq_filter_quantity_' . $equipmentFilterId] = 'success';
			}
		}

		return $result;
	}
	
		public function validateRegDataWorkOrder(WorkOrder $workOrder) {

		$result = array();
		$result['summary'] = 'true';

		if (isset($workOrder->number)) {
			if ($workOrder->number == '') {
				$result['number'] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['number'] = 'success';
			}
		}

		if (isset($workOrder->description)) {
			if ($workOrder->description == '') {
				$result['description'] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['description'] = 'success';
			}
		}
		
		if (isset($workOrder->customer_name)) {
			if ($workOrder->customer_name == '') {
				$result['customer_name'] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['customer_name'] = 'success';
			}
		}
		
		if (isset($workOrder->status)) {
			if ($workOrder->status == '') {
				$result['status'] = 'failed';
				$result['summary'] = 'false';
			} else {
				$result['status'] = 'success';
			}
		}

		return $result;
	}

}

?>