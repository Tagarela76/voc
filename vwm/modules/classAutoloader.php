<?php
// REWRITE ME TO HANDLE vendor folder

function __autoload($class_name) {

    //  tweak for PHP 5.3 namespaces
	$class_name = str_replace('\\', '/', $class_name);
	if(substr($class_name, 0, 7) =='Symfony') {
		require_once(site_path.DIRSEP.'..'.DIRSEP.'vendor'.DIRSEP.'Symfony'.DIRSEP.'lib'.DIRSEP.$class_name.'.php');
		return true;
	}

    if (substr($class_name, 0, 6) == 'Pimple') {
        require_once(site_path.DIRSEP.'..'.DIRSEP.'vendor'.DIRSEP.'Pimple-master'.DIRSEP.'lib'.DIRSEP.$class_name.'.php');
		return true;
    }

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
    //echo $filename;

	if ($subDir == '') {
		$filePath .= $filename;
		$localizedFilePath .= $filename;
	} else {
		$filePath .= $subDir.DIRSEP.$filename;
		$localizedFilePath .= $subDir.DIRSEP.$filename;
	}
		//echo "<br/>Controller: ".$controllersFilePath . "<br/>";
        //


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
        //echo "yes<br/>";
		include ($controllersFilePath);
	}
	//echo($filePath.$localizedFilePath.$controllersFilePath.'+');
}
?>
