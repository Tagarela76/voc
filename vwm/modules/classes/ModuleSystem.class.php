<?php
//we should require gacl & gacl_api cause it's no autoloader for it, but we use it!
require_once('modules/phpgacl/gacl.class.php');
require_once('modules/phpgacl/gacl_api.class.php');

/**
 * Project:     VOC WEB MANAGER
 * File:        ModuleSystem.class.php
 *
 * Turn on/off some modules for different companies.  
 *
 * @copyright 2010, KaTeT-Software
 */

class ModuleSystem {
	
	/**
	 * @var db xnyo database variable
	 */
	private $db;
	
	/**
	 * @var array Map module name to module class
	 */
	private $map = array(
		'docs' => 'MDocContainer',
		'inventory'	=> 'MInventory',
		'waste_streams' => 'MWasteStreams',
		'reduction' => 'MReductionScheme',
		'carbon_footprint' => 'MCarbonFootprint',
		'logbook' => 'MLogbook',
		'reports' => 'MReports',
		'regupdate' => 'MRegAct'
	);
	
	
	/**
	 * @var array Data from module2company table
	 */
	//public $module2company = array();	
	
	/**
	 * @var integer Company ID for which load modules
	 */
	private $companyID;
	
	/**
	 * @var array of loaded modules
	 */
	//public $loadedModules = array();
	
	const TB_MODULE = 'module'; 
	//const TB_MODULE2COMPANY = 'module2company';
	//const TB_MODULE_SEQUENCE = 'module_seq';	



    function ModuleSystem($db, $companyID = null) {
    	$this->db = $db;
    	
//    	if (!is_null($companyID)) {    		
//    		$this->companyID = mysql_escape_string($companyID);
//    		$this->_load();
//    	}
    	
    }
    
    /**
     * try to load class in case it's class from module
     */
    public function classAutoloderForModules($class_name) {
    	$filePath = site_path.'extensions'.DIRSEP;
    	$fileName = DIRSEP.'classes'.DIRSEP.$class_name.'.class.php';
    	foreach ($this->map as $folder => $name) {
    		if (file_exists($filePath.$folder.$fileName)) {
    			include ($filePath.$folder.$fileName);
    			return true;
    		}
    	}
    	return false;
    }
  
    /**
     * perform an action over the category
     * @param string $action Action to perform
     * @param string $category Category on which to perform the action
     * @param array $params Array of parameters needed to perform action
     */
//    function perform($action,$category,$params) {
//    	$query = "SELECT * FROM ".self::TB_MODULE_SEQUENCE." WHERE action = '".$action."' AND category = '".$category."' ";
//    	$this->db->query($query);
//    	
//    	if ($this->db->num_rows() > 0) {
//    		$count = $this->db->num_rows();
//    		for($i=0; $i<$count; $i++) {
//    			$data = $this->db->fetch($i);
//    			$action_modules[$data->sequence] = $data->module;
//    		}
//    	} else {
//    		return false;
//    	} 
//    	for ($i=0; $i<$count; $i++) {
//    		$function = array($this->map[$action_modules[$i]],$action."_".$category);
//    		   		
//    		$params = call_user_func($function, $params);
//    	}  	
//    	return $params;
//    } 
    
    
        
	/**
	 * load module
	 * @param string $module Try to load following module
	 * @return Module loaded module
	 */    
//    private function _getModule($module) {
//    	if (count($this->module2company) == 0) {
//    		$this->_loadModule2Company();
//    	}  
//    	    	    	
//    	//	check for module exist
//    	if (!isset($this->map[$module])) {
//    		return false;
//    	}    	
//    	
//    	//	does module turned on? 
//    	if (!$this->_checkAccess($module)) {    		
//    		return false;
//    	}    	
//    	
//    	$moduleObj = new $this->map[$module];
//    	$this->loadedModules[$module] = $moduleObj;
//    	
//    	return $moduleObj;
//    }
    


    
    /**
     * load all available modules
     */
//    private function _load() {
//    	if (!$this->_loadModule2Company()) {
//    		return false;
//    	}
//    	
//    	foreach ($this->map as $module=>$class) {
//    		$this->_getModule($module);
//    	}
//    }
    

    
    
//    private function _loadModule2Company($companyID) {
//    	$query = "SELECT * FROM ".self::TB_MODULE2COMPANY." WHERE company_id = ".$this->companyID."";
//    	$this->db->query($query);
//    	
//    	if ($this->db->num_rows() > 0) {
//    		$this->module2company = $this->db->fetch_all();
//    		return true;
//    	} else {
//    		return false;
//    	}
//    }
    
    
    
//    private function _checkAccess($module) {       
//    	foreach ($this->module2company as $data) {    		
//    		if ($module == $data->module && $data->on_off == 'on') {
//    			return true;    			
//    		}
//    	}
//    	return false;
//    }
    
    function selectAllModules()
    {
    	$query = "SELECT * FROM ".TB_MODULE;
    	$this->db->query($query); 
    	$modules=$this->db->fetch_all();	
    	return $modules;
    }
    
	    
    function getDefaultModuleList()
    {
    	/*$query = "SELECT * FROM ".self::TB_MODULE2COMPANY;
    	$this->db->query($query);
    	$result=$this->db->fetch_all();
    	
    	
    	foreach ($result as $value)
    	{
    		$arr[$value->module][$value->company_id]=$value->on_off;    		
    	}     	
    	return $arr;*/
    	
    	
    	$gacl_api = new gacl_api();
    	$query = "SELECT * FROM ".TB_COMPANY;  
    	$this->db->query($query);
    	$companies=$this->db->fetch_all();

    	$query = "SELECT * FROM ".TB_MODULE;
    	$this->db->query($query); 
    	$modules=$this->db->fetch_all();
    	
    	foreach ($companies as $com )
    	{    		
    		//	плохо
    		//$query = "SELECT * FROM ".TB_MODULE;
    		//$this->db->query($query); 
    		//$modules=$this->db->fetch_all();    		

    		//	gacl is slow, shit!
    		foreach ($modules as $mod)
    		{
    			$aclSearchResult = $gacl_api->search_acl('access', $mod->name, false, false, 'company_'.$com->company_id, false, false, false, false);    			
    			    			
    			$acl_res= $gacl_api->get_acl($aclSearchResult[0]);    			
    			$arr[$mod->name][$com->company_id]=$acl_res['allow'];    			
    		}
    	} 
    	return $arr;      	  	
    } 
    
    function getCompaniesWhereIsModule($module)
    {    		
    	$gacl_api = new gacl_api();
    	$query = "SELECT name, company_id FROM ".TB_COMPANY;  
    	$this->db->query($query);
    	$companies=$this->db->fetch_all();    	
    	$defaultCompanies=array();
    	
    	foreach ($companies as $com )
    	{
    		$aclSearchResult = $gacl_api->search_acl('access', $module, false, false, 'company_'.$com->company_id, false, false, false, false);    			
    		$acl_res= $gacl_api->get_acl($aclSearchResult[0]);    			
    		if ($acl_res['allow']==1)
    			$defaultCompanies[]=$com;    		
    	}
    	return $defaultCompanies;
    } 
    
    //bool $status; 
    private function insertAcls($module, $status, $company_id)
    {
    	
    	$gacl_api = new gacl_api();
    	$acoArray = array('access'=>array($module));    	
		$aro_group_company=$gacl_api->get_group_id ("company_".$company_id);
		$companyGroup = array($aro_group_company);
		$gacl_api->add_acl($acoArray,NULL,$companyGroup,NULL,NULL,$status,1,NULL,'company users has access to module ACO ');
		$aro_group_root=$gacl_api->get_group_id("root");		
		$rootGroup = array($aro_group_root);	
		$gacl_api->add_acl($acoArray,NULL,$rootGroup,NULL,NULL,$status,1,NULL,'root users has access to module ACO ');
		
		$query = "SELECT * FROM ".TB_FACILITY." WHERE company_id = ".$company_id;
		$this->db->query($query);
		$facilities=$this->db->fetch_all();		
		
		foreach($facilities as $fac)
		{
			$aro_group_facility=$gacl_api->get_group_id ("facility_".$fac->facility_id);
			$facilityGroup = array($aro_group_facility);
			$gacl_api->add_acl($acoArray,NULL,$facilityGroup,NULL,NULL,$status,1,NULL,'');
			
			$query = "SELECT * FROM ".TB_DEPARTMENT." WHERE facility_id = ".$fac->facility_id;
			$this->db->query($query);
			$departments=$this->db->fetch_all();
			foreach ($departments as $dep)
			{
				$aro_group_department=$gacl_api->get_group_id ("department_".$dep->department_id);
				$departmentGroup = array($aro_group_department);
				$gacl_api->add_acl($acoArray,NULL,$departmentGroup,NULL,NULL,$status,1,NULL,'');
			}
		}		
    }
    
     //bool $status; 
    private function editAcls($module, $status, $company_id)
    {    	
    	$gacl_api = new gacl_api();
    	$acoArray = array('access'=>array($module));    	
		$aro_group_company=$gacl_api->get_group_id ("company_".$company_id);
		$companyGroup = array($aro_group_company);
		$company_acl_id=$gacl_api->search_acl('access', $module, false, false, 'company_'.$company_id, false, false, false, false);		
		$gacl_api->edit_acl($company_acl_id[0],$acoArray,NULL,$companyGroup,NULL,NULL,$status,1,NULL,'company users has access to module ACO ');		
		$aro_group_root=$gacl_api->get_group_id("root");		
		$rootGroup = array($aro_group_root);
		
		$root_acl_id=$gacl_api->search_acl('access', $module, false, false, 'root', false, false, false, false);	
		$gacl_api->edit_acl($root_acl_id[0],$acoArray,NULL,$rootGroup,NULL,NULL,$status,1,NULL,'root users has access to module ACO ');
		
		$query = "SELECT * FROM ".TB_FACILITY." WHERE company_id = ".$company_id;
		$this->db->query($query);
		$facilities=$this->db->fetch_all();		
		
		foreach($facilities as $fac)
		{
			$aro_group_facility=$gacl_api->get_group_id ("facility_".$fac->facility_id);
			$facilityGroup = array($aro_group_facility);
			
			$facility_acl_id=$gacl_api->search_acl('access', $module, false, false, "facility_".$fac->facility_id, false, false, false, false);	
			$gacl_api->edit_acl($facility_acl_id[0],$acoArray,NULL,$facilityGroup,NULL,NULL,$status,1,NULL,'');
			
			$query = "SELECT * FROM ".TB_DEPARTMENT." WHERE facility_id = ".$fac->facility_id;
			$this->db->query($query);
			$departments=$this->db->fetch_all();
			foreach ($departments as $dep)
			{
				$aro_group_department=$gacl_api->get_group_id ("department_".$dep->department_id);
				$departmentGroup = array($aro_group_department);
				$department_acl_id=$gacl_api->search_acl('access', $module, false, false, "department_".$dep->department_id, false, false, false, false);	
				$gacl_api->edit_acl($department_acl_id[0],$acoArray,NULL,$departmentGroup,NULL,NULL,$status,1,NULL,'');
			}
		}		
    }
    
//    function insertOrUpdateModule($module, $status, $company_id)
//    {
//    	$module = mysql_escape_string($module);
//    	$status = mysql_escape_string($status);
//    	$company_id = mysql_escape_string($company_id);
//    	
//    	$query = "SELECT * FROM ".self::TB_MODULE2COMPANY." WHERE ";
//    	$query.= "module = '".$module."' AND company_id = ".$company_id;
//    	$this->db->query($query);
//    	$arrModuls=$this->db->fetch(0);
//    	
//    	$access = ($status=="on") ? 1:0;
//    	    	
//    	
//    	if (isset($arrModuls->id))
//    	{      		
//    		$query = "UPDATE ".self::TB_MODULE2COMPANY." SET on_off = '".$status."' WHERE company_id = ".$company_id." AND  module = '".$module."'";
//    		$this->db->exec($query); 
//    		
//    		$this->editAcls($module, $access, $company_id);       		
//    	}   
//    	else
//    	{       		
//	    	$query = "INSERT ".self::TB_MODULE2COMPANY." (module, company_id, on_off) VALUES (";
//	    	$query.= "'".$module."' , ".$company_id." , '".$status."')";
//	    	$this->db->exec($query);
//	    	
//	    	$this->insertAcls($module, $access, $company_id); 
//    	}    	
//    	   	
//    }            
    
    
    /**
     * 
     * Set access for company to module
     * @param string $module
     * @param string $status '0' or '1'
     * @param integer $companyID
     */
    public function setModule2company($module, $status, $companyID) {
    	$module = mysql_escape_string($module);
    	$status = mysql_escape_string($status);
    	$companyID = mysql_escape_string($companyID);
    	    	
    	//	check acls
    	$aclSearchResult = $this->searchModule2company($module, $companyID);
    	
    	if (count($aclSearchResult) > 0) {
    		//	we have ACL's for company!
    		foreach ($aclSearchResult as $aclID) {
    			
    			$this->editAcls($module, $status, $companyID, $aclID);
    		}
    	} else {
    		//	add ACLs
    		$this->insertAcls($module, $status, $companyID);
    		
    	}
    } 
    
    public function getModulesMap() {
    	return $this->map;
    }        
    
    
    
    public function searchModule2company($module, $companyID) {    	
    	$gacl_api = new gacl_api();
    	//	check acls
    	return $gacl_api->search_acl('access', $module, false, false, 'company_'.$companyID, false, false, false, false);
    }  
}
?>