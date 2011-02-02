<?php

class CAModulars extends Controller {

    function CAModulars($smarty,$xnyo,$db,$user,$action) {
		parent::Controller($smarty,$xnyo,$db,$user,$action);
		$this->category='modulars';
		$this->parent_category='modulars';		
	}
	
	function runAction() {
		$this->runCommon('admin');
		$functionName='action'.ucfirst($this->action);				
		if (method_exists($this,$functionName))			
			$this->$functionName();		
	}
	
    private function actionBrowseCategory() {
	    $modSystem = new ModuleSystem($this->db);
	    $modules = $modSystem->selectAllModules();
	    $map = $modSystem->getModulesMap();
	    
	    $company = new Company($this->db);
	    $companyList = $company->getCompanyList();
	    $gacl_api = new gacl_api();//get link for this shit!));
	    
	    $defaultModuleList=$modSystem->getDefaultModuleList();
	    
	    //next will check is all modules has its ACOs
	    foreach($modules as $mod)
		{
		    $ACOs=$gacl_api->get_object('access',true,'ACO');											
		    $isACOmodule=false;					
		    foreach ($ACOs as $ACO)
			{						
			    $obj_data=$gacl_api->get_object_data($ACO,'ACO');
			    if ($mod->name === $obj_data[0][1]) {
				    $isACOmodule=true;				
				    break;
			    }		
			}				
		    if (!$isACOmodule)
			    $acoID = $gacl_api->add_object('access', $mod->name, $mod->name, 0, 0, 'ACO');	
		}
		//end of check
	    
	    if (!is_null($this->getFromPost('modularButton'))) 
	    {
		    switch ($this->getFromPost('modularButton')) 
			{
				case "save": 
					$checkedByCompanies = array();
					$modularID = $this->getFromPost('modularID');
					foreach ($modularID as $value)
					{
						$value = substr($value,6);
						$pos = strpos($value,'_');
						$checkedByCompanies[substr($value,0,$pos)] []= substr($value,$pos+1);									
					}
					for ($i=0;$i<count($companyList);$i++)
					{
						for ($j=0;$j<count($modules);$j++)
						{	
							$status	= (in_array($modules[$j]->id,$checkedByCompanies[$companyList[$i]["id"]]))?1:0;
							if ($defaultModuleList[$modules[$j]->name][$companyList[$i]["id"]]!= $status && class_exists($map[$modules[$j]->name]))
							{    					
								$modSystem->setModule2company($modules[$j]->name, $status, $companyList[$i]["id"]); 
								$defaultModuleList[$modules[$j]->name][$companyList[$i]["id"]] = $status; //to view them without reloding page!   									
							}								
						}
					}
					//header("Location: ?action=browseCategory&categoryID=modulars");
					//die();	
					break;
			}									
	    }
	    
	    $this->smarty->assign('defaultModuleList',$defaultModuleList);			
	    $this->smarty->assign('companyList',$companyList);
	    $this->smarty->assign('modules',$modules);
	    if (VERSION == 'standalone') {
		    $this->smarty->assign('showInstall','true');
	    }
	    $this->smarty->assign('doNotShowControls',true);
	    $jsSources = array(
		    'modules/js/checkBoxes.js'								
	    );
	    
	    $this->smarty->assign('jsSources', $jsSources);
	    $this->smarty->assign('tpl', 'tpls/modulars.tpl');
	    $this->smarty->display("tpls:index.tpl");
	    
    }
}
?>