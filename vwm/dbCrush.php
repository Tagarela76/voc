<?php
	define ('DIRSEP', DIRECTORY_SEPARATOR);
	$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 
	define ('site_path', $site_path);
	
	include("config/constants.php");
	
	//	Include Class Autoloader
	require_once('modules/classAutoloader.php');

	//	Start xnyo Framework
	include ('modules/xnyo/startXnyo.php');

	$xnyo->filter_get_var("action", "text");

	if (isset($_GET["action"])) {
		switch ($_GET["action"]) {
			case "genUserTable":
				$xnyo->filter_get_var("count", "int");
				
				if (isset($_GET["count"])) {
					$count = $_GET["count"];
				} else {
					echo "count not defined!";
					break;
				}
				
				$user = new User($db);
				
				for ($i=0; $i<$count; $i++) {
					$userDetails["username"] = generateName(15);
					$userDetails["accessname"] = generateName(10);
					$userDetails["password"] = hash("md5", "password");
					$userDetails["phone"] = generateName(12);
					$userDetails["mobile"] = generateName(12);
					$userDetails["email"] = generateName(25);
					$userDetails["accesslevel_id"] = 0;
					$userDetails["company_id"] = 0;
					$userDetails["facility_id"] = 0;
					$userDetails["department_id"] = 0;
					$userDetails["grace"] = 0;
					$userDetails["creater_id"] = 666;
					
					$user->addUser($userDetails);
				}
				break;
			
			case "genProductTable":
				$xnyo->filter_get_var("count", "int");
				
				if (isset($_GET["count"])) {
					$count = $_GET["count"];
				} else {
					echo "count not defined!";
					break;
				}
				
				$product = new Product($db);
				
				for ($i=0; $i<$count; $i++) {
					$productData["product_nr"] = generateName(15);
					$productData["name"] = generateName(25);
					$productData["inventory_id"] = 0;
					$productData["voclx"] = 666;
					$productData["vocwx"] = 0;
					$productData["density"] = 0;
					$productData["coating_id"] = 108;
					$productData["specialty_coating"] = YES;
					$productData["aerosol"] = NO;
					$productData["specific_gravity"] = 0;
					$productData["boiling_range_from"] = 0;
					$productData["boiling_range_to"] = 0;
					$productData["supplier_id"] = 17;
					

					
					$product->addNewProduct2($productData);
				}
				break;
				
			default:
				echo "Unknown action";
		}
	}
	
	function generateName($length) {
		$name="";
		
		for ($i=0; $i<$length; $i++) {
			$name .= chr(rand(65, 90));
		}
		
		return $name;
	}
?>
