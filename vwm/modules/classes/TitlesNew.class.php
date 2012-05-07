<?php
class TitlesNew {
	private $smarty;
	private $db;
	private $title;
	
	function TitlesNew($smarty, $db) {
		$this->smarty = $smarty;
		$this->db = $db;
	}
	
	function getTitle($request) {
		$this->title = VOCNAME;

		switch($request['action']) {
			case 'browseCategory':
				switch($request['category']) {
					case "root":
						$this->title .= ": Companies";
						break;
					case "company":
						$company = new Company($this->db);
						$companyDetails = $company->getCompanyDetails($request['id']);
						$this->title .= ": Facilities in company ".$companyDetails['name'];
						break;
					case "facility":
						$facility = new Facility($this->db);
						$facilityDetails = $facility->getFacilityDetails($request['id']);
						$company = new Company($this->db);
						$companyDetails = $company->getcompanyDetails($facilityDetails['company_id']);
						
						switch($request['bookmark']) {
							case "inventory":
								if (class_exists("Inventory")) {
									switch ($request['tab']) {
										case Inventory::PAINT_ACCESSORY:
											$this->title .= ": Paint Accessories Inventories of facility " .$facilityDetails['name']. 
												" in company " .$companyDetails['name'];
											break;
										case Inventory::PAINT_MATERIAL:
											$this->title .= ": Paint Products Inventories of facility " .$facilityDetails['name']. 
												" in company " .$companyDetails['name'];
											break;
									}
								}
								break;
							case "department":
								$this->title .= ": Departments of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;
							case "docs":
								$this->title .= ": Documents of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;							
							case "solventplan":
								$this->title .= ": Solvent Plan of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;
							case "reduction":
								$this->title .= ": Reduction Scheme of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;
							case "carbonfootprint":
								$this->title .= ": Carbon Footprint of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;
							case "logbook":
								$this->title .= ": Logbook of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;
							case "wastestorage":
								$this->title .= ": Waste Storage of facility " .$facilityDetails['name']. 
									" in company " .$companyDetails['name'];
								break;
						}
						break;
					case "department":
						$department = new Department($this->db);
						$departmentDetails = $department->getDepartmentDetails($request['id']);
						switch($request['bookmark']) {
							case 'accessory':
								$this->title .= ": Accessories of department " .$departmentDetails['name'];
								break;
							case 'product':
								$this->title .= ": Products of department " .$departmentDetails['name'];
								break;
							case 'inventory':
								$this->title .= ": Inventories of department " .$departmentDetails['name'];
								break;
							case 'equipment':
								$this->title .= ": Equipments of department " .$departmentDetails['name'];
								break;
							case 'mix':
								$this->title .= ": Mixes of department " .$departmentDetails['name'];
								break;
							case 'nox':
								$this->title .= ": NOx emissions of department " .$departmentDetails['name'];
								break;
							case 'pfpLibrary':
								$this->title .= ": Pre Formulated Products of department " .$departmentDetails['name'];
								break;
						}
						break;
				}
				break;
			case 'viewDetails':	
/*				switch ($request["category"]) {
					case "company":
						break;
					case "facility":
						break;
					case "department":
						break;
					case "inventory":
						break;
					case "equipment":
						break;
					case "product":
						break;
					case "mix":	
						break;
					
				}*/
				$this->title .= ": View " .$request['category']. " information";
				break;
			case 'addItem':
				$tab = '';
/*				switch($request['category']) {
					case "company":
						break;
					case "facility":
						break;
					case "department":
						break;
					case "inventory":*/
						if (class_exists("Inventory")) {
							switch ($request['tab']) {
								case Inventory::PAINT_MATERIAL:
									$tab = "Paint Products ";
									break;
								case Inventory::PAINT_ACCESSORY:
									$tab = "Paint Accessories ";
									break;
							}
						}
/*						break;
					case "equipment":
						break;
					case "mix":
						break;
				}	*/
				$this->title .= ": Add new " .$tab.$request['category'];
				break;
			case 'edit':
				$tab = '';
/*				switch($request['category']) {
					case 'company':
						break;
					case 'facility':
						break;
					case 'department':
						break;
					case 'inventory':*/
						if (class_exists("Inventory")) {
							switch ($request['tab']) {
								case Inventory::PAINT_MATERIAL:
									$tab = "Paint Products ";
									break;
								case Inventory::PAINT_ACCESSORY:
									$tab = "Paint Accessories ";
									break;
							}
						}
/*						break;
					case 'equipment':
						break;
					case 'mix':
						break;
				}	*/
				$this->title .= ": Edit " .$tab.$request['category']. " information";
				break;
			case 'auth':	
				$this->title .= "";
				break;
			case "msdsUploaderMain":	
				$this->title .= "MSDS Uploader";
				break;
			case "deleteItem":
				$delete = "Delete ";
				if (count($request['id']) > 1) {
					switch($request['category']) {
						case 'company':	
							$categories = "companies";
							break;
						case 'facility':
							$categories = "facilities";
							break;
						case 'department':
							$categories = "departments";
							break;
						case 'inventory':
							$categories = "inventories";
							break;
						case 'equipment':
							$categories = "equipment";
							break;
						case 'mix':
							$categories = "mixes";
							break;
						case 'MSDS Sheet':
							$categories = "";
							break;
					}
				} else {
					$categories = $request['category'];
					switch($request['category']) {
						case 'company':	
							$company = new Company($this->db);
							$companyDetails = $company->getCompanyDetails($request['id'][0]);
							$categories .= " " .$companyDetails['name'];
							break;
						case 'facility':
							$facility = new Facility($this->db);
							$facilityDetails = $facility->getFacilityDetails($request['id'][0]);
							$company = new Company($this->db);
							$companyDetails = $company->getcompanyDetails($facilityDetails['company_id']);
							$categories .= " " .$facilityDetails['name']. " from company " .$companyDetails['name'];
							break;
						case 'department':
							$department = new Department($this->db);
							$departmentDetails = $department->getDepartmentDetails($request['id'][0]);
							$categories .= " " .$departmentDetails['name'];
							break;
						case 'inventory':
							if (class_exists("Inventory")) {
								$inventory = new Inventory($this->db);
								$inventory->setId($request['id'][0]);
								$inventoryDetails = $inventory->getName();//getInventoryDetails($request['id'][0]);
								$categories .= " " .$inventoryDetails;
							}
							break;
						case 'equipment':
							$department = new Department($this->db);
							$departmentDetails = $department->getDepartmentDetails($request['departmentID']);
							$equipment = new Equipment($this->db);
							$equipmentDetails = $equipment->getEquipmentDetails($request['id'][0]);						
							$categories .= " " .$equipmentDetails['equip_desc']. " from department " .$departmentDetails['name'];
							break;
						case 'mix':
							$mix = new Mix($this->db);
							$mixDetails = $mix->getMixDetails($request['id'][0]);
							$categories .= " " .$mixDetails['description'];
							break;
						case 'MSDS Sheet':
							$delete = "Unlink ";
							break;
					}				
				}
				$this->title .= ": " .$delete.$categories;
				break;
			case "confirmDelete":	
				$this->title .= ": Confirm delete";
				break;
			case "logout":	
				$this->title .= "";
				break;
			case "groupProducts":	
				$this->title .= "";
				break;
			case "confirmMakeInventory":						//maybe no need?
				$this->title .= "";
				break;
			case "createReport":	
				$this->title .= ": Create Report";
				break;
			case "sendReport":	
				
				switch ($request['reportType']) {
					case "productQuants":
						$reportName = "Product List";
						break;
					case "toxicCompounds":
						$reportName = "Toxic Compounds";
						break;
					case "vocLogs":
						$reportName = "Daily Emissions";
						break;
					case "mixQuantRule":
						$reportName = "Product Usage by Rule Summary";
						break;
					case "chemClass":
						$reportName = "Chemical Classification Summary Form";
						break;
					case "exemptCoat":
						$reportName = "Exempt Coating Operations";
						break;
					case "projectCoat":
						$reportName = "Project Coating Report";
						break;
					case "VOCbyRules":
						$reportName = "VOC Summary for each Rule";
						break;
					case "SummVOC":
						$reportName = "Monthly VOC summary total";
						break;
					case "ReclaimedCredit":
						$reportName = "Reclaimed Credit Log";
						break;					
					
				}
				$this->title .= ": Create Report \"" .$reportName. "\"";				//send or create?
				break;
			case "sendSubReport":	
				$this->title .= "";
				break;
			case "test":	
				$this->title .= " Test";
				break;
			case "baikonur":	
				$this->title .= " Baikonur";
				break;
			case "baikonur2":	
				$this->title .= " Baikonur 2";
				break;
			case "showIssueReport":	
				$this->title .= ": Issue Report";				//?need to correct
				break;
			case "reportIssue":	
				$this->title .= ": Issue Report";				//?need to correct
				break;
			case "utils":	
				$this->title .= "";
				break;
			case "settings":
				$this->title .= ": Settings for " .$request['category'];	
				switch($request['category']) {
					case 'company':	
						$company = new Company($this->db);
						$companyDetails = $company->getCompanyDetails($request['id']);
						$this->title .= " " .$companyDetails['name'];
						break;
					case 'facility':
						$facility = new Facility($this->db);
						$facilityDetails = $facility->getFacilityDetails($request['id']);
						$company = new Company($this->db);
						$companyDetails = $company->getcompanyDetails($facilityDetails['company_id']);
						$this->title .= " " .$facilityDetails['name']. " in company " .$companyDetails['name'];
						break;
					case 'department':
						$department = new Department($this->db);
						$departmentDetails = $department->getDepartmentDetails($request['id']);
						$this->title .= " " .$departmentDetails['name'];
						break;
				}
				break;
			case "msdsUploader":	
				$this->title .= ": MSDS Uploader";
				break;
			case "msdsUploaderBasic":	
				$this->title .= ": Basic MSDS Uploader";
				break;	
		}
		$this->smarty->assign("title", $this->title);
	}

}
?>