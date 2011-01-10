<?php

	require('config/constants.php');
		
		define ('DIRSEP', DIRECTORY_SEPARATOR);
	
	$site_path = realpath(dirname(__FILE__) . DIRSEP) . DIRSEP; 
	define ('site_path', $site_path);
	
	function __autoload($class_name) { 
        $filename .= $class_name.'.class.php';
        $file = site_path . 'modules' . DIRSEP . $filename;
		if (file_exists($file) == false) {
				return false;
        }
		include ($file);
	}
	
	//	--START XNYO
	require ('modules/xnyo/xnyo.class.php');
	
	$xnyo = new Xnyo;
	$xnyo->auth_type='sql';

	$xnyo->database_type = DB_TYPE;
	$xnyo->db_host = DB_HOST;
	$xnyo->db_user = DB_USER;
	$xnyo->db_passwd = DB_PASS;
	$xnyo->start();
	
	
	$xnyo->logout_redirect_url='site/voc_web_manager.html';
	
	require ('modules/xnyo/smarty/startSmarty.php');
		
	$xnyo->filter_get_var('action', 'text');
	
	
	echo $_GET["action"]."<br>";
	
	if (!isset($_GET["action"]))
	{
		//	No action
		//	Show Login page
		
		echo "SET ACTION PLEASE!!!";
	}
	else
	{
		
		switch ($_GET["action"]) {
			case "getList":
			
				$xnyo->filter_get_var('item', 'text');
				switch ($_GET["item"]){
					
					case "apmethod":
						echo "List of apmethods:<br>";
						$apmethod=new Apmethod($db);
						print_r($apmethod->getApmethodList());
					break;
					
					case "coat":
						echo "List of coat:<br>";
						$coat=new Coat($db);
						print_r($coat->getCoatList());
					break;
					
					case "component":
						echo "List of components:<br>";
						$component=new Component($db);
						print_r($component->getComponentList());
					break;
					
					case "country":
						echo "List of countries:<br>";
						$country=new Country($db);
						print_r($country->getCountryList());
					break;
					
					case "density":
						echo "List of densities:<br>";
						$density=new Density($db);
						print_r($density->getDensityList());
					break;
					
					case "msds":
						echo "List of Msds:<br>";
						$msds=new Msds($db);
						print_r($msds->getMsdsList());
					break;
					
					case "rule":
						echo "List of rules:<br>";
						$rule=new Rule($db);
						print_r($rule->getRuleList());
					break;
					
					case "state":
						echo "List of states:<br>";
						$state=new State($db);
						print_r($state->getStateList());
					break;
					
					case "substrate":
						echo "List of substrates:<br>";
						$substrate=new Substrate($db);
						print_r($substrate->getSubstrateList());
					break;
					
					case "supplier":
						echo "List of suppliers:<br>";
						$supplier=new Supplier($db);
						print_r($supplier->getSupplierList());
					break;
					
					case "unittype":
						echo "List of unittypes:<br>";
						$unittype=new Unittype($db);
						print_r($unittype->getUnittypeList());
					break;
					
					case "type":
						echo "List of types:<br>";
						$type=new Type($db);
						print_r($type->getTypeList());
					break;
					
					case "lol":
						echo "List of lols:<br>";
						$lol=new Lol($db);
						print_r($lol->getLolList());
					break;
	
				}
				
			break;
			
			case "addItem":

				$xnyo->filter_get_var('item', 'text');
				switch ($_GET["item"]){
					
					case "apmethod":

						$xnyo->filter_get_var('apmethod_desc', 'text');
						$apmethodData=array(
											"apmethod_desc"	=>	$_GET["apmethod_desc"]
						);
						print_r($apmethodData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($apmethodData);
						
						if (!($validation->isUniqueName("apmethod", $apmethodData["apmethod_desc"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=apmethod&apmethod_desc=Privet
						$apmethod=new Apmethod($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$apmethod->addNewApmethod($apmethodData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "coat":

						$xnyo->filter_get_var('coat_desc', 'text');
						$coatData=array(
											"coat_desc"	=>	$_GET["coat_desc"]
						);
						print_r($coatData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($coatData);
						
						if (!($validation->isUniqueName("coat", $coatData["coat_desc"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=coat&coat_desc=Privet
						$coat=new Coat($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$coat->addNewCoat($coatData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "component":
						$xnyo->filter_get_var('country', 'text');
						$xnyo->filter_get_var('state', 'text');
						$xnyo->filter_get_var('msds_id', 'text');
						$xnyo->filter_get_var('product_code', 'text');
						$xnyo->filter_get_var('comp_name', 'text');
						$xnyo->filter_get_var('comp_type', 'text');
						$xnyo->filter_get_var('comp_weight', 'text');
						$xnyo->filter_get_var('comp_density', 'text');
						$xnyo->filter_get_var('description', 'text');
						$xnyo->filter_get_var('supplier', 'text');
						$componentData=array(
											"country"	=>	$_GET["country"],
											"state"	=>	$_GET["state"],
											"msds_id"	=>	$_GET["msds_id"],
											"product_code"	=>	$_GET["product_code"],
											"comp_name"	=>	$_GET["comp_name"],
											"comp_type"	=>	$_GET["comp_type"],
											"comp_weight"	=>	$_GET["comp_weight"],
											"comp_density"	=>	$_GET["comp_density"],
											"description"	=>	$_GET["description"],
											"supplier"	=>	$_GET["supplier"]
						);
						print_r($componentData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($componentData);
						
						if (!($validation->isUniqueName("component", $componentData["product_code"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=component&country=1&state=2&msds_id=1&product_code=4324&comp_name=bugaga&comp_type=1&comp_weight=21&comp_density=12&description=dfgdfgdfgdfgdfgdf&supplier=1
						$component=new Component($db);
						echo "<br>".$validateStatus['summary']."<br>";
						
						
						
						if ($validateStatus['summary'] == 'true') {
							$component->addNewComponent($componentData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "country":

						$xnyo->filter_get_var('name', 'text');
						$countryData=array(
											"name"	=>	$_GET["name"]
						);
						print_r($countryData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($countryData);
						
						if (!($validation->isUniqueName("country", $countryData["name"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=country&name=Privet
						$country=new Country($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$country->addNewCountry($countryData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "density":

						$xnyo->filter_get_var('density_type', 'text');
						$densityData=array(
											"density_type"	=>	$_GET["density_type"]
						);
						print_r($densityData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($densityData);
						
						if (!($validation->isUniqueName("density", $densityData["density_type"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=density&density_type=Privet
						$density=new Density($db);
						echo "<br>".$validateStatus['summary']."<br>";
						
						
						
						if ($validateStatus['summary'] == 'true') {
							$density->addNewDensity($densityData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "msds":

						$xnyo->filter_get_var('cas', 'text');
						$xnyo->filter_get_var('cas_desc', 'text');
						$xnyo->filter_get_var('voclx', 'text');
						$xnyo->filter_get_var('vocwx', 'text');
						$xnyo->filter_get_var('temp_vp', 'text');
						
						$msdsData=array(
											"cas"	=>	$_GET["cas"],
											"cas_desc"	=>	$_GET["cas_desc"],
											"voclx"	=>	$_GET["voclx"],
											"vocwx"	=>	$_GET["vocwx"],
											"temp_vp"	=>	$_GET["temp_vp"]
						);
						print_r($msdsData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($msdsData);
						
						if (!($validation->isUniqueMsds($msdsData)))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						// test_modules.php?action=addItem&item=msds&cas=15&cas_desc=test2&voclx=5&vocwx=4&temp_vp=100
						$msds=new Msds($db);
						echo "<br>".$validateStatus['summary']."<br>";
						print_r($validateStatus);
						if ($validateStatus['summary'] == 'true') {
							$msds->addNewMsds($msdsData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "rule":
						$xnyo->filter_get_var('country', 'text');
						$xnyo->filter_get_var('state', 'text');
						$xnyo->filter_get_var('county', 'text');
						$xnyo->filter_get_var('city', 'text');
						$xnyo->filter_get_var('postal', 'text');
						$xnyo->filter_get_var('rule_nr', 'text');
						$xnyo->filter_get_var('rule_desc', 'text');
						
						$ruleData=array(
											"country"	=>	$_GET["country"],
											"state"	=>	$_GET["state"],
											"county"	=>	$_GET["county"],
											"city"	=>	$_GET["city"],
											"postal"	=>	$_GET["postal"],
											"rule_nr"	=>	$_GET["rule_nr"],
											"rule_desc"	=>	$_GET["rule_desc"]
						);
						print_r($ruleData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($ruleData);
						
						if (!($validation->isUniqueRule($ruleData)))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=rule&country=2&state=3&county=&city=Dnepr&postal=12345&rule_nr=1231&rule_desc=new+test+rule
						$rule=new Rule($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$rule->addNewRule($ruleData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "state":

						$xnyo->filter_get_var('name', 'text');
						$xnyo->filter_get_var('country_id', 'text');
						$stateData=array(
											"name"	=>	$_GET["name"],
											"country_id"	=>	$_GET["country_id"]
						);
						print_r($stateData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($stateData);
						
						if (!($validation->isUniqueName("state", $stateData["name"], $stateData["country_id"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=state&name=Privet&country_id=1
						$state=new State($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$state->addNewState($stateData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "substrate":

						$xnyo->filter_get_var('substrate_desc', 'text');
						$substrateData=array(
											"substrate_desc"	=>	$_GET["substrate_desc"]
						);
						print_r($substrateData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($substrateData);
						
						if (!($validation->isUniqueName("substrate", $substrateData["substrate_desc"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=substrate&substrate_desc=Privet
						$substrate=new Substrate($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$substrate->addNewSubstrate($substrateData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "supplier":

						$xnyo->filter_get_var('supplier', 'text');
						$supplierData=array(
											"supplier"	=>	$_GET["supplier"]
						);
						print_r($supplierData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($supplierData);
						
						if (!($validation->isUniqueName("supplier", $supplierData["supplier"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=supplier&supplier=Privet
						$supplier=new Supplier($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$supplier->addNewSupplier($supplierData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "type":

						$xnyo->filter_get_var('type_desc', 'text');
						$typeData=array(
											"type_desc"	=>	$_GET["type_desc"]
						);
						print_r($typeData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($typeData);
						
						if (!($validation->isUniqueName("type", $typeData["type_desc"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=type&type_desc=Privet
						$type=new Type($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$type->addNewType($typeData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "lol":

						$xnyo->filter_get_var('name', 'text');
						$xnyo->filter_get_var('cas', 'text');
						$lolData=array(
											"name"	=>	$_GET["name"],
											"cas"	=>	$_GET["cas"]
						);
						print_r($lolData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($lolData);
						
						if (!($validation->isUniqueName("lol", $lolData["name"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=lol&name=Privet&cas=34343
						$lol=new Lol($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$lol->addNewLol($lolData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
					case "unittype":
						$xnyo->filter_get_var('name', 'text');
						$xnyo->filter_get_var('unittype_desc', 'text');
						$xnyo->filter_get_var('formula', 'text');
						$unittypeData=array(
											"name"	=>	$_GET["name"],
											"unittype_desc"	=>	$_GET["unittype_desc"],
											"formula"	=>	$_GET["formula"]
						);
						print_r($unittypeData);
						$validation=new Validation($db);
						$validateStatus=$validation->validateRegDataAdminClasses($unittypeData);
						
						if (!($validation->isUniqueName("unittype", $unittypeData["name"])))
						{
							$validateStatus['summary'] = 'false';
							$validateStatus['name'] = 'alredyExist';
						}
						
						// test_modules.php?action=addItem&item=unittype&name=privet&unittype_desc=gdfgdfgdf&formula=
						$unittype=new Unittype($db);
						echo "<br>".$validateStatus['summary']."<br>";
						if ($validateStatus['summary'] == 'true') {
							$unittype->addNewUnittype($unittypeData);
							
							echo "<br>GOOD!";
						}
						else
						{
							echo "SMTH IS WRONG!!!";
							
							
						}
					break;
					
					
				}
			break;
			
			
			case "deleteItem":
				$xnyo->filter_get_var('item', 'text');
				$xnyo->filter_get_var('id', 'text');
				$id=$_GET["id"];
				
				//test_modules.php?action=deleteItem&item=&id=
				switch ($_GET["item"]){
					
					case "apmethod":
						$apmethod=new Apmethod($db);
						$apmethod->deleteApmethod($id);
					break;
					
					case "coat":
						$coat=new Coat($db);
						$coat->deleteCoat($id);
					break;
					
					case "component":
						$component=new Component($db);
						$component->deleteComponent($id);
					break;
					
					case "state":
						$state=new State($db);
						$state->deleteState($id);
					break;
					
					case "country":
						$country=new Country($db);
						$country->deleteCountry($id);
					break;
					
					case "density":
						$density=new Density($db);
						$density->deleteDensity($id);
					break;
					
					case "lol":
						$lol=new Lol($db);
						$lol->deleteLol($id);
					break;
					
					case "msds":
						$msds=new Msds($db);
						$msds->deleteMsds($id);
					break;
					
					case "rule":
						$rule=new Rule($db);
						$rule->deleteRule($id);
					break;
					
					case "substrate":
						$substrate=new Substrate($db);
						$substrate->deleteSubstrate($id);
					break;
					
					case "supplier":
						$supplier=new Supplier($db);
						$supplier->deleteSupplier($id);
					break;
					
					case "type":
						$type=new Type($db);
						$type->deleteType($id);
					break;
					
					case "unittype":
						$unittype=new Unittype($db);
						$unittype->deleteUnittype($id);
					break;

				}

				
			break;
			
			
			case "viewDetails":
				$xnyo->filter_get_var('item', 'text');
				$xnyo->filter_get_var('id', 'text');
				$id=$_GET["id"];
				
				//test_modules.php?action=viewDetails&item=&id=
				switch ($_GET["item"]){
					
					case "apmethod":
						$apmethod=new Apmethod($db);
						print_r($apmethod->getApmethodDetails($id));
						
					break;
					
					case "coat":
						$coat=new Coat($db);
						print_r($coat->getCoatDetails($id));
					break;
					
					case "component":
						$component=new Component($db);
						print_r($component->getComponentDetails($id));
					break;
					
					case "state":
						$state=new State($db);
						print_r($state->getStateDetails($id));
					break;
					
					case "country":
						$country=new Country($db);
						print_r($country->getCountryDetails($id));
					break;
					
					case "density":
						$density=new Density($db);
						print_r($density->getDensityDetails($id));
					break;
					
					case "lol":
						$lol=new Lol($db);
						print_r($lol->getLolDetails($id));
					break;
					
					case "msds":
						$msds=new Msds($db);
						print_r($msds->getMsdsDetails($id));
					break;
					
					case "rule":
						$rule=new Rule($db);
						print_r($rule->getRuleDetails($id, true));
					break;
					
					case "substrate":
						$substrate=new Substrate($db);
						print_r($substrate->getSubstrateDetails($id));
					break;
					
					case "supplier":
						$supplier=new Supplier($db);
						print_r($supplier->getSupplierDetails($id));
					break;
					
					case "type":
						$type=new Type($db);
						print_r($type->getTypeDetails($id));
					break;
					
					case "unittype":
						$unittype=new Unittype($db);
						print_r($unittype->getUnittypeDetails($id));
					break;
					
					
					
				}

			break;
			
			
			case "setDetails":
				$xnyo->filter_get_var('item', 'text');
				$xnyo->filter_get_var('id', 'text');
				$id=$_GET["id"];
				
				//test_modules.php?action=setDetails&item=&id=
				switch ($_GET["item"]){
					
					case "apmethod":
						$xnyo->filter_get_var('apmethod_desc', 'text');
						$regData=array(
											"apmethod_id"	=>	$id,
											"apmethod_desc"	=>	$_GET["apmethod_desc"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$apmethod=new Apmethod($db);
						if ($validateStatus["summary"] == "true") {
							
							$apmethod->setApmethodDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "coat":
						$xnyo->filter_get_var('coat_desc', 'text');
						$regData=array(
											"coat_id"	=>	$id,
											"coat_desc"	=>	$_GET["coat_desc"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$coat=new Coat($db);
						if ($validateStatus["summary"] == "true") {
							
							$coat->setCoatDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "component":
						$xnyo->filter_get_var('country', 'text');
						$xnyo->filter_get_var('state', 'text');
						$xnyo->filter_get_var('msds_id', 'text');
						$xnyo->filter_get_var('product_code', 'text');
						$xnyo->filter_get_var('comp_name', 'text');
						$xnyo->filter_get_var('comp_type', 'text');
						$xnyo->filter_get_var('comp_weight', 'text');
						$xnyo->filter_get_var('comp_density', 'text');
						$xnyo->filter_get_var('description', 'text');
						$xnyo->filter_get_var('supplier', 'text');
						$regData=array(
											"country"	=>	$_GET["country"],
											"component_id"	=>	$id,
											"state"	=>	$_GET["state"],
											"msds_id"	=>	$_GET["msds_id"],
											"product_code"	=>	$_GET["product_code"],
											"comp_name"	=>	$_GET["comp_name"],
											"comp_type"	=>	$_GET["comp_type"],
											"comp_weight"	=>	$_GET["comp_weight"],
											"comp_density"	=>	$_GET["comp_density"],
											"description"	=>	$_GET["description"],
											"supplier"	=>	$_GET["supplier"],
											"sara"	=>	"yes"
						);
						
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						
						// test_modules.php?action=addItem&item=component&country=1&state=2&msds_id=1&product_code=4324&comp_name=bugaga&comp_type=1&comp_weight=21&comp_density=12&description=dfgdfgdfgdfgdfgdf&supplier=1
						$component=new Component($db);
						if ($validateStatus["summary"] == "true") {
							
							$component->setComponentDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
					break;
					
					
					case "country":
						$xnyo->filter_get_var('name', 'text');
						$regData=array(
											"country_id"	=>	$id,
											"name"	=>	$_GET["name"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$country=new Country($db);
						if ($validateStatus["summary"] == "true") {
							
							$country->setCountryDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "density":
						$xnyo->filter_get_var('density_type', 'text');
						$regData=array(
											"density_id"	=>	$id,
											"density_type"	=>	$_GET["density_type"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$density=new Density($db);
						if ($validateStatus["summary"] == "true") {
							
							$density->setDensityDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "lol":
						$xnyo->filter_get_var('name', 'text');
						$xnyo->filter_get_var('cas', 'text');
						$regData=array(
											"lol_id"	=>	$id,
											"name"	=>	$_GET["name"],
											"cas"	=>	$_GET["cas"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$lol=new Lol($db);
						if ($validateStatus["summary"] == "true") {
							
							$lol->setLolDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "msds":
						$xnyo->filter_get_var('cas', 'text');
						$xnyo->filter_get_var('cas_desc', 'text');
						$xnyo->filter_get_var('voclx', 'text');
						$xnyo->filter_get_var('vocwx', 'text');
						$xnyo->filter_get_var('temp_vp', 'text');
						$regData=array(
											"msds_id"	=>	$id,
											"cas"	=>	$_GET["cas"],
											"cas_desc"	=>	$_GET["cas_desc"],
											"voclx"	=>	$_GET["voclx"],
											"vocwx"	=>	$_GET["vocwx"],
											"temp_vp"	=>	$_GET["temp_vp"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$msds=new Msds($db);
						if ($validateStatus["summary"] == "true") {
							
							$msds->setMsdsDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "rule":
						$xnyo->filter_get_var('country', 'text');
						$xnyo->filter_get_var('state', 'text');
						$xnyo->filter_get_var('county', 'text');
						$xnyo->filter_get_var('city', 'text');
						$xnyo->filter_get_var('postal', 'text');
						$xnyo->filter_get_var('rule_nr', 'text');
						$xnyo->filter_get_var('rule_desc', 'text');
						$regData=array(
											"rule_id"	=>	$id,
											"country"	=>	$_GET["country"],
											"state"	=>	$_GET["state"],
											"county"	=>	$_GET["county"],
											"city"	=>	$_GET["city"],
											"postal"	=>	$_GET["postal"],
											"rule_nr"	=>	$_GET["rule_nr"],
											"rule_desc"	=>	$_GET["rule_desc"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$rule=new Rule($db);
						if ($validateStatus["summary"] == "true") {
							
							$rule->setRuleDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "state":
						$xnyo->filter_get_var('country_id', 'text');
						$xnyo->filter_get_var('name', 'text');
						$regData=array(
											"state_id"	=>	$id,
											"country_id"	=>	$_GET["country_id"],
											"name"	=>	$_GET["name"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$state=new State($db);
						if ($validateStatus["summary"] == "true") {
							
							$state->setStateDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "substrate":
						$xnyo->filter_get_var('substrate_desc', 'text');
						$regData=array(
											"substrate_id"	=>	$id,
											"substrate_desc"	=>	$_GET["substrate_desc"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$substrate=new Substrate($db);
						if ($validateStatus["summary"] == "true") {
							
							$substrate->setSubstrateDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "supplier":
						$xnyo->filter_get_var('supplier', 'text');
						$regData=array(
											"supplier_id"	=>	$id,
											"supplier"	=>	$_GET["supplier"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$supplier=new Supplier($db);
						if ($validateStatus["summary"] == "true") {
							
							$supplier->setSupplierDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "type":
						$xnyo->filter_get_var('type_desc', 'text');
						$regData=array(
											"type_id"	=>	$id,
											"type_desc"	=>	$_GET["type_desc"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$type=new Type($db);
						if ($validateStatus["summary"] == "true") {
							
							$type->setTypeDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					case "unittype":
						$xnyo->filter_get_var('name', 'text');
						$xnyo->filter_get_var('formula', 'text');
						$xnyo->filter_get_var('unittype_desc', 'text');
						$regData=array(
											"unittype_id"	=>	$id,
											"name"	=>	$_GET["name"],
											"unittype_desc"	=>	$_GET["unittype_desc"],
											"formula"	=>	$_GET["formula"]
						);
						$validate=new Validation();
						$validateStatus=$validate->validateRegDataAdminClasses($regData);
						
						$unittype=new Unittype($db);
						if ($validateStatus["summary"] == "true") {
							
							$unittype->setUnittypeDetails($regData);
							
						}
						else
						{
							echo "SMTH WRONG!!!";
							print_r($validateStatus);
							
						}
						
					break;
					
					
					
					
				}
			
			
			break;
			
			
			
		}
		
		
		
		
	}
	
?>