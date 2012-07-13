<?php
require_once(site_path.'modules/phpgacl/gacl.class.php');
require_once(site_path.'modules/phpgacl/gacl_api.class.php');

class Company {
	
	private $db;
	private $trashRecord;
	
	
	
	function Company($db) {	//	Constructor
		$this->db=$db;
	}
	
	
	
	//	setter injection http://wiki.agiledev.ru/doku.php?id=ooad:dependency_injection	
	public function setTrashRecord(iTrash $trashRecord) {
		$this->trashRecord = $trashRecord;		
	}
	
	
	
	
	public function getCompanyList() {
		//$this->db->select_db(DB_NAME);
		$this->db->query("SELECT * FROM ".TB_COMPANY." ORDER BY name");
		if ($this->db->num_rows()) {
			for ($i=0; $i < $this->db->num_rows(); $i++) {
				$data=$this->db->fetch($i);
				$company=array(
					'id'			=>	$data->company_id,
					'name'			=>	$data->name,
					'address'		=>	$data->address,
					'contact'		=>	$data->contact,
					'phone'			=>	$data->phone
				);
				$companies[]=$company;
			}
		}
		
		return $companies;
	}
	
	public function addNewCompany($companyData) {
		
		//screening of quotation marks
		foreach ($companyData as $key=>$value)
		{
			$companyData[$key]=mysql_real_escape_string($value);;
		}
		
		//$this->db->select_db(DB_NAME);		
		
		//	GCG Creation
		$GCG = new GCG($this->db);
		$gcgID = $GCG->create();	
		
		$voc2vps = new VOC2VPS($this->db);
		$configs = $voc2vps->loadConfigs();
		$trialPeriod = $configs['trial_period'];
        
        $trialPeriod = new DateTime();
        $trialPeriod->add( new DateInterval("P".intval($configs['trial_period'])."D") );

		$query="INSERT INTO ".TB_COMPANY." (name, address, city, zip, county, state, country, phone, fax, email, contact, title, gcg_id, creater_id, creation_date, voc_unittype_id) VALUES (";
		
		$query.="'".$companyData["name"]."', ";
		$query.="'".$companyData["address"]."', ";
		$query.="'".$companyData["city"]."', ";
		$query.="'".$companyData["zip"]."', ";
		$query.="'".$companyData["county"]."', ";
		$query.="'".$companyData["state"]."', ";
		$query.=$companyData["country"].", ";
		$query.="'".$companyData["phone"]."', ";
		$query.="'".$companyData["fax"]."', ";
		$query.="'".$companyData["email"]."', ";
		$query.="'".$companyData["contact"]."', ";
		$query.="'".$companyData["title"]."', ";
		$query.=$gcgID.", ";
		$query.=$companyData["creater_id"].", '";
		$query.=((VERSION=="standalone")?"NULL,": $trialPeriod->format('Y-m-d')."',");
		$query.=$companyData["voc_unittype_id"];
		
		$query.=')';
        
	     
						
		$this->db->query($query);
		
		$this->db->query("SELECT LAST_INSERT_ID() id");
		$company_id = $this->db->fetch(0)->id;
		
//		//add new Company in Bridge
//		$query = "SELECT company_id FROM ".TB_COMPANY." order by company_id DESC Limit 1";
//		$this->db->query($query);
//		$data = $this->db->fetch(0);
//		
//		if (isset($data->company_id)) {
//			
//		$company_id = (int)$data->company_id;	 
//		$companyDetails = $this->getCompanyDetails($company_id, true);				
//		$companyDetails['period_end_date'] = $companyDetails['trial_end_date'];
//		$companyDetails['deadline_counter'] = "NULL";
//		$companyDetails['status'] = 'off';
//		$bridge->addNewCustomer($company_id, $companyDetails);
//		 
//		}
//		//end of Bridge	
		
		//----------------------------------------------------------------
		//GACL
		//----------------------------------------------------------------
		//   ADDING COMPANY
		//   CREATE ACO
		$gacl_api = new gacl_api();
		$acoID = $gacl_api->add_object('access', "company_".$company_id, "company_".$company_id, 0, 0, 'ACO');
		//   CREATE ARO GROUP
		$giantcomliance= $gacl_api->get_group_id("Giant Compliance");
		$aro_group_company = $gacl_api->add_group("company_".$company_id, "company_".$company_id, $giantcomliance, 'ARO');
		$aro_group_root=$gacl_api->get_group_id("root");
		
		//   CREATE ACL
		$acoArray = array('access'=>array("company_".$company_id));		
		$companyGroup = array($aro_group_company);
		$rootGroup = array($aro_group_root);
				
		$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,1,1,NULL,'company\'s users has access to company ACO ');
		$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,1,1,NULL,'root\'s users has access to company ACO ');
		//-----------------------------------------------------------------
		
		//	save to trash_bin
		$this->save2trash('C', $company_id);	
		
		
		return $company_id;
	}
	
	public function getCompanyDetails($company_id, $vanilla=false) {
		$company_id=mysql_real_escape_string($company_id);
		//$this->db->select_db(DB_NAME);
		$this->db->query('SELECT * FROM '.TB_COMPANY.' WHERE company_id = \''.$company_id.'\'');
		$companyDetails=$this->db->fetch_array(0);
		if (!$vanilla) {
			$reg = new Registration($this->db);
			//	Set State
			if ($reg->isOwnState($companyDetails['country']))
			{
				//	have own state list
				$companyDetails["state"] = $reg->getState($companyDetails['state']);
			}
			
			//	Set Country
			$companyDetails["country"] = $reg->getCountry($companyDetails['country']);
		}
		return $companyDetails;
	}
	
	public function setCompanyDetails($companyData) {
		//$this->db->select_db(DB_NAME);
		
		//screening of quotation marks
		foreach ($companyData as $key=>$value)
		{
			$companyData[$key]=mysql_real_escape_string($value);
		}
		
		//	save to trash_bin
		$this->save2trash('U', $companyData['company_id']);
		
		$companyDetails = $this->getCompanyDetails($companyData['company_id']);
		if ($companyDetails['voc_unittype_id'] != $companyData['voc_unittype_id']) {
			//	voc unit type was changed!
			//	recalculate mixes and limit
			$this->onChangeVOCUnittype($companyDetails['voc_unittype_id'], $companyData['voc_unittype_id'], $companyData['company_id']);
		}		
		
		$query="UPDATE ".TB_COMPANY." SET ";
		
		$query.="name='".$companyData['name']."', ";
		$query.="address='".$companyData['address']."', ";
		$query.="city='".$companyData['city']."', ";
		$query.="zip='".$companyData['zip']."', ";
		$query.="county='".$companyData['county']."', ";
		$query.="state='".$companyData['state']."', ";
		$query.="country=".$companyData['country'].", ";
		$query.="phone='".$companyData['phone']."', ";
		$query.="fax='".$companyData['fax']."', ";
		$query.="email='".$companyData['email']."', ";
		$query.="contact='".$companyData['contact']."', ";
		$query.="title='".$companyData['title']."', ";
		$query.="voc_unittype_id=".(int)$companyData['voc_unittype_id'];
		$query.=" WHERE company_id=".$companyData['company_id'];
		
		$this->db->query($query);
		
//		//save details of Company in Bridge
//		$bridge = new Bridge($this->db);
//		$bridge->setCustomerDetails($companyData['company_id'], $companyData);
//		//end of Bridge
		
	}
	

	
	function deleteCompany($company_id) {
		//$this->db->select_db(DB_NAME);
		
		//screening of quotation marks
		$company_id=mysql_real_escape_string($company_id);
		
		//	save to trash_bin
		$this->save2trash('D', $company_id);	
		
		$this->db->query("SELECT * FROM ".TB_FACILITY." WHERE company_id = ".$company_id);
		$facilitiesCount = $this->db->num_rows();
		$facilitiesToDelete = $this->db->fetch_all();
		if ($facilitiesCount > 0) {
			$facility = new Facility($this->db);
			$facility->setParentTrashRecord($this->trashRecord);
			for ($i=0; $i<$facilitiesCount; $i++) {
				$facility->setTrashRecord(new Trash($this->db));				
				$facility->deleteFacility($facilitiesToDelete[$i]->facility_id);
			}
		}
		
		$this->db->query("DELETE FROM ".TB_COMPANY." WHERE company_id=".$company_id);
		
//		//delete company from Bridge XML
//		$bridge = new Bridge($this->db);
//		$bridge->deleteCustomer($company_id);
//		//end of Bridge
		
		/*
		 why delete data if we potentially will rollback it?
		
		//unassign all products from company
		$product = new Product($this->db);
		$product->unassignProductFromCompany(false, $company_id);
		*/				
	}
	
	
	
	
	public function clearCompany(){		
		//$this->db->select_db(DB_NAME);
    	
    	$query = "DELETE FROM ".TB_COMPANY;
    	$this->db->query($query);
    	
//    	//delete all companies from Bridge XML
//		$bridge = new Bridge($this->db);
//		$bridge->deleteAllCustomers();
//		//end of Bridge
		
    	$gacl= array ("gacl_acl","gacl_acl_sections","gacl_acl_seq","gacl_aco","gacl_aco_map","gacl_aco_sections",
    				  "gacl_aco_sections_seq","gacl_aco_seq","gacl_aro","gacl_aro_groups","gacl_aro_groups_id_seq",
    				  "gacl_aro_groups_map","gacl_aro_map","gacl_aro_sections","gacl_aro_sections_seq","gacl_aro_seq",
    				  "gacl_axo","gacl_axo_groups","gacl_axo_groups_map","gacl_axo_map","gacl_axo_sections","gacl_axo_sections_seq",
    				  "gacl_axo_seq","gacl_groups_aro_map","gacl_groups_axo_map","gacl_phpgacl");    	  
    				  
		foreach($gacl as $value) {
			$query = "DELETE FROM ".$value;
    		$this->db->query($query);
		} 
    	
	}
	
	public function fillCompany(){
		$this->db->select_db(DB_IMPORT);    	
    	$query = "INSERT INTO ".DB_NAME.".".TB_COMPANY." SELECT * FROM ".DB_IMPORT.".".TB_COMPANY;
    	$this->db->query($query);  
    	
    	$gacl= array ("gacl_acl","gacl_acl_sections","gacl_acl_seq","gacl_aco","gacl_aco_map","gacl_aco_sections",
    				  "gacl_aco_sections_seq","gacl_aco_seq","gacl_aro","gacl_aro_groups","gacl_aro_groups_id_seq",
    				  "gacl_aro_groups_map","gacl_aro_map","gacl_aro_sections","gacl_aro_sections_seq","gacl_aro_seq",
    				  "gacl_axo","gacl_axo_groups","gacl_axo_groups_map","gacl_axo_map","gacl_axo_sections","gacl_axo_sections_seq",
    				  "gacl_axo_seq","gacl_groups_aro_map","gacl_groups_axo_map","gacl_phpgacl");
    	
		foreach($gacl as $value) {
			$query = "INSERT INTO ".DB_NAME.".".$value." SELECT * FROM ".DB_IMPORT.".".$value;
    		$this->db->query($query);
		}  	
	}
	
	
	public function getCompanyIDbyDepartmentID($departmentID) {
		$departmentID=mysql_real_escape_string($departmentID);
		//$this->db->select_db(DB_NAME);
		$query = "SELECT c.company_id " .
				"FROM ".TB_DEPARTMENT." d, ".TB_FACILITY." f, ".TB_COMPANY." c " .
				"WHERE d.facility_id = f.facility_id " .
				"AND f.company_id = c.company_id " .
				"AND d.department_id = ".$departmentID;
		$this->db->query($query);
		
		if ($this->db->num_rows()) {
			$data = $this->db->fetch(0);
			return $data->company_id;
		} else {
			return false;
		} 				
	}
	
	
	
	/**
	 * Recalcultes all mixes according to new unit type. 
	 * Calling after default VOC unittype changing
	 * 
	 * @param integer Old unittype ID
	 * @param integer New unittype ID
	 * @param integer Company ID 
	 * @return boolean Always return true
	 */
	public function onChangeVOCUnittype($oldUnittypeID, $newUnittypeID, $companyID) {
		//$this->db->select_db(DB_NAME);
		
		$companyID=mysql_real_escape_string($companyID);
		
		$unittype = new Unittype($this->db);			
		$unittypeConverter = new UnitTypeConverter($unittype->getDescriptionByID($newUnittypeID));
		
		//	MIX RECALC		
		$query = "SELECT m.mix_id, m.voc " .
				"FROM ".TB_FACILITY." f,  ".TB_DEPARTMENT." d, ".TB_USAGE." m " .
				"WHERE m.department_id = d.department_id " .
				"AND d.facility_id = f.facility_id " .
				"AND f.company_id = ".$companyID;
		$this->db->query($query);
		
		if ($this->db->num_rows() > 0) {			
			$data = $this->db->fetch_all();			
			foreach ($data as $row) {
				$newVoc = $unittypeConverter->convertToDefault($row->voc, $unittype->getDescriptionByID($oldUnittypeID));
				$query = "UPDATE ".TB_USAGE." SET voc = ".$newVoc." WHERE mix_id = ".$row->mix_id."";		
				$this->db->exec($query);
			}
		}
		
		//	FACILITY LIMIT RECALC
		$query = "SELECT facility_id, voc_limit FROM ".TB_FACILITY." WHERE company_id = ".$companyID."";
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {			
			$data = $this->db->fetch_all();			
			foreach ($data as $row) {
				$newVocLimit = $unittypeConverter->convertToDefault($row->voc_limit, $unittype->getDescriptionByID($oldUnittypeID));
				$query = "UPDATE ".TB_FACILITY." SET voc_limit = ".$newVocLimit." WHERE facility_id = ".$row->facility_id."";				
				$this->db->exec($query);
			}
		}
		
		//	DEPARTMENT LIMIT RECALC
		$query = "SELECT d.department_id, d.voc_limit " .
				"FROM ".TB_FACILITY." f, ".TB_DEPARTMENT." d " .
				"WHERE d.facility_id = f.facility_id " .
				"AND f.company_id = ".$companyID."";
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {			
			$data = $this->db->fetch_all();			
			foreach ($data as $row) {
				$newVocLimit = $unittypeConverter->convertToDefault($row->voc_limit, $unittype->getDescriptionByID($oldUnittypeID));
				$query = "UPDATE ".TB_DEPARTMENT." SET voc_limit = ".$newVocLimit." WHERE department_id = ".$row->department_id."";						
				$this->db->exec($query);
			}
		}
				
		//	EQUIPMENT DAILY LIMIT RECALC
		$query = "SELECT e.equipment_id, e.daily " .
				"FROM ".TB_FACILITY." f, ".TB_DEPARTMENT." d, ".TB_EQUIPMENT." e " .
				"WHERE d.facility_id = f.facility_id " .
				"AND e.department_id = d.department_id " .
				"AND f.company_id = ".$companyID."";
		$this->db->query($query);
		if ($this->db->num_rows() > 0) {			
			$data = $this->db->fetch_all();			
			foreach ($data as $row) {
				$newDailyLimit = $unittypeConverter->convertToDefault($row->voc_limit, $unittype->getDescriptionByID($oldUnittypeID));
				$query = "UPDATE ".TB_EQUIPMENT." SET daily = ".$newDailyLimit." WHERE equipment_id = ".$row->equipment_id."";				
				$this->db->exec($query);
			}
		}
				
		return true;
	}
	
	
	
	
	//	Tracking System
	private function save2trash($CRUD, $companyID) {
		//	protect from SQL injections
		$companyID=mysql_real_escape_string($companyID);		
		
		$tm = new TrackManager($this->db);
		$this->trashRecord = $tm->save2trash(TB_COMPANY, $companyID, $CRUD, $this->parentTrashRecord);
		

		//	DEPRECATED July 16, 2010
//		$companyID=mysql_real_escape_string($companyID);		
//		
//		if (isset($this->trashRecord)) {	
//			$query = "SELECT * FROM ".TB_COMPANY." WHERE company_id = ".$companyID;
//			$this->db->query($query);
//			$dataRows = $this->db->fetch_all();
//			
//			foreach ($dataRows as $dataRow) {
//				$companyRecords = TrackingSystem::properties2array($dataRow);		
//				$this->trashRecord->setTable(TB_COMPANY);		
//				$this->trashRecord->setData(json_encode($companyRecords[0]));
//				$this->trashRecord->setUserID($_SESSION['user_id']);
//				$this->trashRecord->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//				$this->trashRecord->setDate(time());	//	current time
//				$this->trashRecord->save();	
//			}			
//
////			//	load and save dependencies
//			if ($CRUD != 'D') {
//				if (false !== ($dependencies = $this->trashRecord->getDependencies(TrackingSystem::HIDDEN_DEPENDENCIES))) {				
//					foreach ($dependencies as $dependency) {
//						$parentID = ($dependency->getParentObj() !== null) ? $dependency->getParentObj()->getID() : null;
//						$dependency->setUserID($_SESSION['user_id']);
//						$dependency->setCRUD($CRUD);		//	C - Create, U - update, D - delete
//						$dependency->setDate(time());	//	current time					
//						$dependency->setReferrer($parentID);
//						$dependency->save();												
//					}
//				}		
//			}			
//		}		

	}			
}
?>
