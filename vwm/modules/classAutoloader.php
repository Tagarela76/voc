<?php
function __autoload($class_name) {
	
	$filePath = site_path.'modules'.DIRSEP.'classes'.DIRSEP;
	$controllersFilePath=$filePath.'controllers'.DIRSEP; 
	$localizedFilePath = $filePath; 
	
	if (REGION != DEFAULT_REGION) {		
		$localizedFilePath .= REGION.DIRSEP; 
	}
	
	
	//	Check for Rule-class
	if (strpos($class_name, "RuleN")) {
		$subDir = 'rule';
	} else {
		$subDir = '';
	}
	
	$filename .= $class_name.'.class.php';
	
	$controllersFilePath.=$filename;
	
	if ($subDir == '') {
		$filePath .= $filename;
		$localizedFilePath .= $filename;
	} else {
		$filePath .= $subDir.DIRSEP.$filename;
		$localizedFilePath .= $subDir.DIRSEP.$filename;
	}
		
	
	//	TRY TO LOAD CONTROLLER CLASS
	if (file_exists($controllersFilePath) == false)	{	
		//	TRY TO LOAD LOCALIZED CLASS
		if (file_exists($localizedFilePath) == false) {
			//	TRY TO LOAD ORIGINAL CLASS	
			if (file_exists($filePath) == false) {
				// TRY TO LOAD MODULE CLASS
				if (file_exists(site_path.'modules'.DIRSEP.'classes'.DIRSEP.'ModuleSystem.class.php')) {
					require_once(site_path.'modules'.DIRSEP.'classes'.DIRSEP.'ModuleSystem.class.php');
					$ms = new ModuleSystem(null);
					return $ms->classAutoloderForModules($class_name);
				} else {
					return false;
				}
			} else {
				include ($filePath);			
			}				
		} else {
			include ($localizedFilePath);
		}
	}else {		
		include ($controllersFilePath);
	}
	
}
?>
