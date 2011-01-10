<?php
/**
 * VOC WEB Manager Installer 
*/

// Sanity check.
if ( false ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Error: PHP is not running</title>
</head>
<body>
	<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
	<h2>Error: PHP is not running</h2>
	<p>VOC WEB Manager requires that your web server is running PHP. Your server does not have PHP installed, or PHP is turned off.</p>
</body>
</html>
<?php
}


require_once ('modules/xnyo/xnyo.class.php');
	
$xnyo = new Xnyo();
$xnyo->start();

//	Start Smarty templates engine	
require_once ('modules/xnyo/smarty/Smarty.class.php');

$site_path = getcwd().DIRECTORY_SEPARATOR; 
define ('site_path', $site_path);
	
//	Include Class Autoloader
require_once('modules/classAutoloader.php');
	
$smarty = new Smarty();		 
$smarty->caching = false;
$smarty->template_dir = 'design/install/';	
$smarty->compile_dir = 'template_c/install/';


$xnyo->filter_post_var('step','text');

$step = isset( $_POST['step'] ) ? $_POST['step'] : 1;

$iErrors= new InstallErrors();

switch ($step)
{
	case 1: 
		$summary_valid=true;
		$php_extensions=array();
		
		$mysql_check = ( extension_loaded('mysql')) ? 1 : 0;		
		if ($mysql_check==0)
		{
			$summary_valid=false;
		}		
		$php_extensions[]=array('name' => 'MySQL', 'check' => $mysql_check, 'error'=>$iErrors->mysqlError());
		
		$session_check = ( extension_loaded('session')) ? 1 : 0;		
		if ($session_check==0)
		{
			$summary_valid=false;
		}
		$php_extensions[]=array('name' => 'Session', 'check' => $session_check, 'error'=>$iErrors->sessionError());		
		
		$xml_check = ( extension_loaded('xml')) ? 1 : 0;		
		if ($xml_check==0)
		{
			$summary_valid=false;
		}
		$php_extensions[]=array('name' => 'Xml', 'check' => $xml_check,'error'=>$iErrors->xmlError());
		
		$zendoptimizer_check = extension_loaded('Zend Optimizer') ? 1 : 0;	
		if ($zendoptimizer_check==0)
		{
			$summary_valid=false;
		}
		$php_extensions[]=array('name' => 'Zend Optimizer', 'check' => $zendoptimizer_check,'error'=>$iErrors->zendError());
		
		$jsonsupport_check = extension_loaded('json') ? 1 : 0;	
		if ($jsonsupport_check==0)
		{
			$summary_valid=false;
		}
		$php_extensions[]=array('name' => 'Json support', 'check' => $jsonsupport_check,'error'=>$iErrors->jsonError());	
		
		$mbstring_check = extension_loaded('mbstring') ? 1 : 0;	
		if ($mbstring_check==0)
		{
			$summary_valid=false;
		}
		$php_extensions[]=array('name' => 'Mbstring', 'check' => $mbstring_check,'error'=>$iErrors->mbstringError());	
		
		$phpvariables_check = $_SERVER["HTTP_HOST"] != ""  ? 1 : 0;	
		if ($phpvariables_check ==0)
		{
			$summary_valid=false;
		}
		$php_extensions[]=array('name' => 'PHP Variables', 'check' => $phpvariables_check,'error'=>$iErrors->phpVariablesError());
		
		$step = $summary_valid ? 2 : 1;
		$smarty->assign('step',$step);		
		$smarty->assign('php_extensions',$php_extensions);	
				
		$smarty->display('first_step.tpl');
		break;
		
	case 2:			
		$summary_valid=true;
		$folders=array();
		
		$tmp_folder_check= is_writable("tmp") ? 1 : 0;
		if ($tmp_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'vwm/tmp folder', 'check' => $tmp_folder_check, 'error'=>$iErrors->folderPermissionsError());		
		
		$cache_folder_check= is_writable("cache") ? 1 : 0;
		if ($cache_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'vwm/cache folder', 'check' => $cache_folder_check, 'error'=>$iErrors->folderPermissionsError());
		
		$extensions_folder_check= is_writable("extensions") ? 1 : 0;
		if ($extensions_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'vwm/extensions folder', 'check' => $extensions_folder_check, 'error'=>$iErrors->folderPermissionsError());
		
		$tempatec_folder_check= is_writable("template_c") ? 1 : 0;
		if ($tempatec_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'vwm/template_c folder', 'check' => $tempatec_folder_check, 'error'=>$iErrors->folderPermissionsError());
		
		$msds_folder_check= is_writable("../msds") ? 1 : 0;
		if ($msds_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'msds folder', 'check' => $msds_folder_check, 'error'=>$iErrors->folderPermissionsError());
		
		$bridge_folder_check= is_writable("../bridge") ? 1 : 0;
		if ($bridge_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'bridge folder', 'check' => $bridge_folder_check, 'error'=>$iErrors->folderPermissionsError());
		
		$docs_folder_check= is_writable("../docs") ? 1 : 0;
		if ($docs_folder_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'docs folder', 'check' => $docs_folder_check, 'error'=>$iErrors->folderPermissionsError());
		
		$instconstantsphp_file_check= is_writable("config/installConstants.php") ? 1 : 0;
		if ($instconstantsphp_file_check==0)
		{
			$summary_valid=false;
		}
		$files[]=array('name' => 'vwm/config/installConstants.php', 'check' => $instconstantsphp_file_check,'error'=>$iErrors->filePermissionsError());	
		
		$step = $summary_valid ? 3 : 2;
		$smarty->assign('step',$step);	
		$smarty->assign('folders',$files);
				
		$smarty->display('second_step.tpl');
		break;
	case 3:
		require_once ('modules/classes/InstallTables.class.php');
		$summary_valid=true;
		$xnyo->filter_post_var('form','text');				
		if ($_POST['form']=='isForm')
		{
			$xnyo->filter_post_var('hostdb','text');
			$xnyo->filter_post_var('login','text');
			$xnyo->filter_post_var('pwd','text');
			$xnyo->filter_post_var('namedb','text');
			$xnyo->filter_post_var('dbCombo','text');
			$xnyo->filter_post_var('region','text');
			
			
			$data=array(
				'hostdb'=>trim($_POST['hostdb']),
				'login'=>trim($_POST['login']),
				'pwd'=>trim($_POST['pwd']),
				'namedb'=>trim($_POST['namedb']),
				'dbCombo'=>$_POST['dbCombo'],
				'region'=>$_POST['region']	
			);
			
			$link = mysql_connect($data['hostdb'], $data['login'],$data['pwd']);
  			if( !$link ) 
  			{
  				$smarty->assign('connectingFail',true);
  				$summary_valid=false;  				
  			}  			
  			else
  			{  			
	  			$query = "SHOW DATABASES"; 
	  		  	$dbs = mysql_query($query); 
	  			if(!$dbs) exit(mysql_error()); 
				$flag = false; 
				  
				while($data_base = mysql_fetch_array($dbs,MYSQL_NUM)) 
				{ 
					if($data_base[0] == $data['namedb']) 
				    { 
				    	$flag = true; 
				      	break; 
				    } 
				}
				
				$isAdminUser=false;	
				$update=false;			
												  			
	  			switch ($data['dbCombo'])
	  			{
	  				case 'newDB':
	  					if ($flag)
	  					{
	  						$smarty->assign('dbAlreadyExist',true);
	  						$summary_valid=false;
	  					}
	  					else
	  					{
	  						$sql = 'CREATE DATABASE '.$_POST['namedb'];
							mysql_query($sql, $link); 
	  					}
	  				break;
	  				case 'oldDB':
	  					if (!$flag)
	  					{
		  					$smarty->assign('dbNoExist',true);
		  					$summary_valid=false;
	  					}
	  					else
	  					{
	  						$update=true;
	  						mysql_select_db($data['namedb'],$link) or die(mysql_error());
	  						$query='SELECT COUNT(*) FROM `user` WHERE  `accesslevel_id`=3 LIMIT 1';
	  						if ($query)
	  						{
		  						$result=mysql_query($query,$link) or die(mysql_error());
		  						$row = mysql_fetch_row($result);	
		  						if($row[0]>0)  
	  								$isAdminUser=true;		
	  						}				
	  					}	  					
	  				break;
	  			}
	  			if ($summary_valid==true)
	  			{	  				
	  				$installTablesObj = new InstallTables($link,$_POST['namedb']);
					$installTablesObj->createAllTables($update);
					$f = fopen("config/installConstants.php", "w");
           			$s = 	"<?php\n".							
							"define('DB_HOST', '".$_POST['hostdb']."');      	// database host    \n".
							"define('DB_USER', '".$_POST['login']."');   		// username         \n".
							"define('DB_PASS', '".$_POST['pwd']."');   			// password         \n".
							"define('DB_NAME', '".$_POST['namedb']."');      	// database name    \n".	
							"define('REGION',  '".$_POST['region']."');			// localization		\n".			
							"?>";
            		fputs($f, $s);
            		fclose($f);
            		if (!$isAdminUser)
            		{
            			mysql_close($link);
            			$xnyo = new Xnyo;
						$xnyo->auth_type='sql';					
						$xnyo->database_type = 'mysql';
						$xnyo->db_host = $_POST['hostdb'];
						$xnyo->db_user = $_POST['login'];
						$xnyo->db_passwd = $_POST['pwd'];
						$xnyo->start();
						$smarty = new Smarty();		 
						$smarty->caching = false;
						$smarty->template_dir = 'design/install/';	
						$smarty->compile_dir = 'template_c/install/';
						$xnyo->filter_post_var('hostdb','text');
						$xnyo->filter_post_var('login','text');
						$xnyo->filter_post_var('pwd','text');
						$xnyo->filter_post_var('namedb','text');
						$xnyo->filter_post_var('dbCombo','text');
						$xnyo->filter_post_var('region','text');
						
						$admPassword=generate_password(7); 
						
						$db->select_db($_POST['namedb']);					
            			$user=new User($db, $xnyo);
            			$data = array (
								'username'			=>	'root',
								'accessname'		=>	'root',
								'password'			=>	$admPassword,
								'confirm_password'	=>	$admPassword,
								'phone'				=>	'0',
								'mobile'			=>	'0',
								'email'				=>	'this@is.email',
								'accesslevel_id'	=>	3,
								'grace'				=>	14
							);						
						$user->addUser($data);           		           		         		           		
	            		$smarty->assign('admPassword',$admPassword);	            		
            		}
            		$smarty->assign('isAdminUser',$isAdminUser);
            		$address= str_replace('install.php','',$_SERVER['PHP_SELF']);
            		$address = preg_replace('/vwm\/$/','',$address);
            		$smarty->assign('address',$address);              		
					$smarty->display('final.tpl');					
					die();					
	  			}  			
  			}
		}	
		else
		{
			$data=array(
				'hostdb'=>'localhost',				
				'namedb'=>'voc',
				'dbCombo'=>'newDB'	
			);
		}			
		$smarty->assign('data',$data);		
		$smarty->assign('summary_valid',$summary_valid);
		$smarty->display('third_step.tpl');
		die();		
		break;
}

function generate_password($number)
{
	$arr = array('a','b','c','d','e','f',					
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 'A','B','C','D','E','F',
                 'G','H','I','J','K','L',
                 'M','N','O','P','R','S',
                 'T','U','V','X','Y','Z',
                 '1','2','3','4','5','6',
                 '7','8','9','0','(',')',
				 '[',']','!','?','&','^',
				 '%','@','*','$','<','>',
				 '|','+','{','}','~');
    $pass = "";
    for($i = 0; $i < $number; $i++)
    {      
      $index = rand(0, count($arr) - 1);
      $pass .= $arr[$index];
    }
    return $pass;
}

?>
