<?php

class CCommon extends Controller
{
    function CCommon($smarty,$xnyo,$db,$user,$action) {
    	parent::Controller($smarty,$xnyo,$db,$user,$action);
    }

    function runAction()
	{
		try{

		$this->runCommon();
		}catch (Exception $e){
			throw new Exception("My Defined Exception! $e");
		}
		$functionName='action'.ucfirst($this->action);

		if (method_exists($this,$functionName))
		{
			$this->$functionName();
		}
		else
			throw new Exception('404');
	}

	private function actionCleanIndustriesOne() {
		$sql = "SELECT type, count(it.id) CNT " .
				"FROM `industry_type` it LEFT JOIN `product2type` p2t ON it.id = p2t.type_id " .
				"WHERE p2t.product_id IS NULL ".
				"AND it.parent IS NOT NULL " .
				"GROUP BY type ORDER BY count(it.id) DESC";
		$this->db->query($sql);
		die($sql);
		if ($this->db->num_rows() == 0) {
			echo "no potential errors found";
			return ;
		}

		$typesWithProblems = $this->db->fetch_all_array();
		foreach ($typesWithProblems as $typeWithProblems) {
			if($typeWithProblems['CNT'] <= 2) {
				continue;
			}
			$sql = "SELECT it.id " .
				"FROM `industry_type` it LEFT JOIN `product2type` p2t ON it.id = p2t.type_id " .
				"WHERE p2t.product_id IS NULL ".
				"AND it.type = '{$typeWithProblems['type']}' " ;
			$this->db->query($sql);
			if($this->db->num_rows() == 0) {
				continue;
			}

			$ids2delete = $this->db->fetch_all_array();
			$cleanIds2delete = array();
			foreach ($ids2delete as $id2delete) {
				$cleanIds2delete[] = $id2delete['id'];
			}

			$sql = "DELETE FROM industry_type WHERE id IN (".implode(',', $cleanIds2delete).")";
			$this->db->exec($sql);
		}

	}
	
	private function actionCleanIndustriesTwo() { 
		$sql = "SELECT it.`type`, count(it.`type`) cnt
				FROM `industry_type` it
				WHERE it.parent IS NOT NULL
				GROUP BY it.`type`, it.`parent`
				ORDER BY count(it.`type`) DESC";
		$this->db->query($sql);

		if ($this->db->num_rows() == 0) {
			echo "no potential errors found";
			return ;
		}
		$typesWithProblems = array();
		$types = $this->db->fetch_all_array();
		foreach ($types as $type) {
			if ($type['cnt'] > 1){
				$typesWithProblems[] = $type['type'];
			}
		}
		foreach ($typesWithProblems as $typeWithProblems) {
			$sql = "SELECT *
					FROM `industry_type`
					WHERE parent IS NOT NULL
					AND `type` = '" . $typeWithProblems . "'
					GROUP BY `parent`";
			$this->db->query($sql);
			$selectTypes = $this->db->fetch_all_array();
			
			foreach ($selectTypes as $selectType) {
				$sql = "UPDATE `product2type` " .
						"SET `type_id`= " . $selectType['id'] . "
						WHERE type_id IN (
							SELECT `id` 
							FROM `industry_type`
							WHERE `type` = '" . $typeWithProblems . "'
						    AND `parent` = 	" . $selectType['parent'] . ")";
				$this->db->query($sql);

				$sql = "DELETE
						FROM `industry_type`
						WHERE `id`<>" . $selectType['id'] . "
						AND `type` = '" . $typeWithProblems . "'
						AND `parent` = 	" . $selectType['parent']; 
				$this->db->query($sql);
			}
	
		}

	}


    private function actionRenameAllPFPs() {
        $sql = "SELECT * FROM ".TB_PFP." ";
        $this->db->query($sql);

        $pfps = $this->db->fetch_all();
        foreach ($pfps as $pfp) {
            $sql = "SELECT * " .
                "FROM ".TB_PFP2PRODUCT." pfp2p " .
                "JOIN ".TB_PRODUCT." p ON pfp2p.product_id = p.product_id " .
                "WHERE pfp2p.preformulated_products_id = {$pfp->id} ORDER BY pfp2p.isPrimary DESC";
            $this->db->query($sql);

            $numrows = $this->db->num_rows();
            if ($numrows == 0) {
                continue;
            }

            $description = array();
            for($i=0;$i<$numrows;$i++) {
                $product = $this->db->fetch($i);
                if($i == 0) {
                    $description = array(
                        $product->name,
                    );
                }
                $description[] = $product->product_nr;

            }

            $description = implode(' / ', $description);

            $sql = "UPDATE ".TB_PFP." SET description = '{$this->db->sqltext($description)}' WHERE id = {$pfp->id}";
            $this->db->exec($sql);
        }

    }

	/**
	 *
	 * Refresh usage stats. Useful when unsync happens
	 */
	private function actionPersic2()
	{
		echo "<h1>Start Presic2 =) (Recalc Usage_stats)</h1>";
		$query = 'TRUNCATE TABLE '.TB_USAGE_STATS.'';
		$this->db->exec($query);

		$department = new Department($this->db);
		$query = "SELECT * FROM mix";
		$this->db->query($query);
		$mixList = $this->db->fetch_all();
		foreach ($mixList as $mix)
		{
			$date = new DateTime();
			$date->setTimestamp($mix->creation_time);
			$mixCreationMonth =  $date->format('m');//substr($mix->creation_time,5,2);
			$mixCreationYear = $date->format('Y');//substr($mix->creation_time,0,4);

			echo $mixCreationYear . "<br/>";
			$department->incrementUsage($mixCreationMonth, $mixCreationYear, $mix->voc, $mix->department_id);
		}
		echo "<h1>DONE</h1>";
	}

	private function actionCreateSales() {

		$query = "DROP TABLE IF EXISTS `contacts`; DROP TABLE IF EXISTS `Contacts`;
				CREATE TABLE IF NOT EXISTS `contacts` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `company` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `contact` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `phone` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `fax` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `government_agencies` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `affiliations` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `industry` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `comments` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `state` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `zip_code` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `country_id` int(11) DEFAULT NULL,
				  `state_id` int(11) DEFAULT NULL,
				  `mail` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
				  `cellphone` varchar(25) CHARACTER SET utf8 DEFAULT NULL,
				  `type` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;";

		$this->db->query($query);

		$error = mysql_error();
		if($error) {
			echo "<b>MySQL Error:</b><br/>$error<br/>In query:<br/>$query";
		}

		$query = "DROP TABLE IF EXISTS `contacts_type`;# MySQL returned an empty result set (i.e. zero rows).

CREATE TABLE IF NOT EXISTS `contacts_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;# MySQL returned an empty result set (i.e. zero rows).


INSERT INTO `contacts_type` (`id`, `name`) VALUES
(1, 'contacts'),
(2, 'government'),
(3, 'affiliations');# Affected rows: 3";

		$error = mysql_error();
		if($error) {
			echo "<br/><b>MySQL Error:</b><br/>$error<br/>In query:<br/>$query";
		}



		echo "DONE";
	}

	private function actionLedokol()
	{
		$query = 	"INSERT INTO emission_factor (name, unittype_id, emission_factor) VALUES " .
					"('Aviation spirit', 5, 3128), " .
					"('Aviation turbine fuel', 5, 3150), " .
					"('Blast furnace gas', 34, 0.97), " .
					"('Burning oil/kerosene/paraffin', 4, 2.518), " .
					"('Coke oven gas', 34, 0.15), " .
					"('Coking coal', 5, 2810), " .
					"('Colliery methane', 34, 0.18), " .
					"('Diesel', 4, 2.630), " .
					"('Fuel oil', 5, 3223), " .
					"('Gas oil', 4, 2.674), " .
					"('Industrial coal', 5, 2457), " .
					"('Liquid petroleum gas (LPG)', 4, 1.495), " .
					"('Lubricants', 5, 3171), " .
					"('Waste', 5, 275), " .
					"('Naphtha', 5, 3131), " .
					"('Natural gas', 34, 0.185), " .
					"('Other petroleum gas', 34, 0.21), " .
					"('Petrol', 4, 2.315), " .
					"('Petroleum coke', 5, 3410), " .
					"('Refinery miscellaneous', 34, 0.245), " .
					"('Scrap tyres', 5, 2003), " .
					"('Solid smokeless fuel', 5, 2810), " .
					"('Sour gas', 34, 0.24), " .
					"('Waste solvents', 5, 1597), " .
					"('Electricity', 34, 0.537) ";

		$this->db->exec($query);
	}

	private function actionStorageDensity()
	{
		//recalc density for waste storages!!
		$query = "SELECT * FROM `storage` "."WHERE density_unit_id IS NULL ";
		$this->db->query($query);
		$data = $this->db->fetch_all();//var_dump($query);var_dump($db);
		$densityObj = new Density($this->db,1); // 1 - density_unit_id for default
		$unittype = new Unittype($this->db);
		$unittypeConverter = new UnitTypeConverter();
		$weightUnittype = $unittype->getDescriptionByID($densityObj->getNumerator());
		$volumeUnittype = $unittype->getDescriptionByID($densityObj->getDenominator());
		$done = 0; $failed = 0;
		foreach ($data as $record)
		{
			$weight = $unittypeConverter->convertFromTo($record->capacity_weight,$unittype->getDescriptionByID($record->weight_unittype),$weightUnittype);
			$volume = $unittypeConverter->convertFromTo($record->capacity_volume,$unittype->getDescriptionByID($record->volume_unittype),$volumeUnittype);
			$density = $weight/$volume;//var_dump(round($density,4));
			$query = "UPDATE `storage` SET density='".round($density,4)."', density_unit_id='1' WHERE storage_id='$record->storage_id' ";
			if($this->db->query($query))
			{
				$done++;
			}
			else
			{
				$failed++;
			}
		}
		echo "<p>Calculated density: ".$done."</p><p>Failed: ".$failed."</p>";
	}

	private function actionSendSubReport()
	{
		$request = $this->getFromRequest();
		$title = new TitlesNew($this->smarty, $this->db);

		$title->getTitle($request);

		switch ($request['itemID'])
		{
			case 'company':
				$companyID = $request['id'];
				break;
			case 'facility':
				$facility = new Facility($this->db);
				$facilityDetails = $facility->getFacilityDetails($request['id']);
				$companyID = $facilityDetails['company_id'];
				break;
			case 'department':
				$company = new Company($this->db);
				$companyID = $company->getCompanyIDbyDepartmentID($request['id']);
				break;
		}

		$reportType = $request['reportType'];

		if (!$this->user->checkAccess('reports', $companyID))
		{
			throw new Exception('deny');
		}
		//	OK, this company has access to this module, so let's setup..

		$ms = new ModuleSystem($this->db);	//	TODO: show?
		$moduleMap = $ms->getModulesMap();
		$mReport = new $moduleMap['reports'];
		$params = array(
						'db' => $this->db,
						'xnyo' => $this->xnyo,
						'companyID' => $companyID,
						'request' => $request
						);
		$report = $mReport->prepareSendSubReport($params);
	}

	private function actionSendContactEmail() {


			$cemail = new EMail();

			/**
			 * denis.nt@kttsoft.com
dmitry.vd@kttsoft.com
jgypsyn@gyantgroup.com
			 *
			 *
			 */

			$to = array ("denis.nt@kttsoft.com",
				"dmitry.vd@kttsoft.com", "jgypsyn@gyantgroup.com "
			);

			//$from = "authentification@vocwebmanager.com";
			$from = AUTH_SENDER."@".DOMAIN;

			$name = strip_tags($_POST['name']);
			$email = strip_tags($_POST['email']);
			$phone = strip_tags($_POST['phone']);
			$comment = strip_tags($_POST['comment']);



			$theme = "Contact form from ".DOMAIN." by ".$name;


			$message .= "Name: $name \r\n\r\n";
			$message .= "Email: $email \r\n\r\n";
			$message .= "Phone: $phone \r\n\r\n";
			$message .= "Comment: $comment \r\n\r\n";



			$cemail->sendMail($from, $to, $theme, $message);

			echo '<html><head><meta http-equiv="refresh" content="3; url=../voc_web_manager.html"><title>mail result</title></head><body>' . "Mail sent successfully. Redirect to main page in 3 seconds." . '</body></html>';
	}

	private function actionRequestRepresentativeForms(){
			$cemail = new EMail();
			$cUserRequest = new UserRequest($this->db);
			$cSetupRequest = new SetupRequest($this->db);

			$to = array ("denis.nt@kttsoft.com", "dmitry.vd@kttsoft.com", "jgypsyn@gyantgroup.com ");
			//$to = "dmitry.ds@kttsoft.com";

			//$from = "authentification@vocwebmanager.com";
			$from = AUTH_SENDER."@".DOMAIN;
			$theme = "Company setup request";

			$message .= "Company Name: ".$_POST['name']."\r\n\r\n";
			$message .= "Email:".$_POST['email']."\r\n\r\n";

			//var_dump($_POST); die();
			switch ($_POST['postType']) {
				case 'representativeCompany':
					$cSetupRequest->setName($_POST['name']);
					$cSetupRequest->setAddress($_POST['address']);
					$cSetupRequest->setCity($_POST['city']);
					$this->db->query("SELECT country_id FROM ".TB_COUNTRY." WHERE name='".$_POST['country']."'");
					if ($this->db->num_rows() > 0){
						$countryID = $this->db->fetch(0)->country_id;
						$cSetupRequest->setCountryID($countryID);
					} else {
						$error = "Incorrect Country!";
					}
					if ($countryID == 215){
						$this->db->query("SELECT state_id FROM ".TB_STATE." WHERE name='".$_POST['state']."'");
						if ($this->db->num_rows() > 0){
							$stateID = $this->db->fetch(0)->state_id;
							$cSetupRequest->setState($_POST['state']);
							$cSetupRequest->setStateID($stateID);
						} else {
							$error = "Incorrect State!";
						}
					} else {
						$cSetupRequest->setState($_POST['state']);
						$cSetupRequest->setStateID('NULL');
					}
					$cSetupRequest->setCounty('NULL');
					$cSetupRequest->setParentID('NULL');
					$cSetupRequest->setZipCode($_POST['zip']);
					$cSetupRequest->setPhone($_POST['phone']);
					$cSetupRequest->setContact($_POST['contact']);
					$cSetupRequest->setEmail($_POST['email']);
					$cSetupRequest->setFax($_POST['fax']);
					$cSetupRequest->setTitle($_POST['title']);
					$errorSave = $cSetupRequest->save('company');
					if ($errorSave == ''){
						$cemail->sendMail($from, $to, $theme, $message);
					} else {
						$error = $errorSave;
					}
					break;
				case 'newUserRequest':
					$this->db->query("SELECT company_id FROM ".TB_COMPANY." WHERE name='".$_POST['companyname']."'");
					if ($this->db->num_rows() > 0){
						$companyID = $this->db->fetch(0)->company_id;
						$cUserRequest->setALL('add', 'NULL', 'NULL', $_POST['username'], $_POST['accessname'], $_POST['useremail'], $_POST['phone'], $_POST['mobile'], 'company', $companyID);
						$cUserRequest->setCreaterID('NULL');
						$error = $cUserRequest->save();
						$cUserRequest->sendMail('Please, create new user.');
					} else {
						$error = "Incorrect Company Name!";
					}
					break;
				case 'forgotPassword':
					$this->db->query("SELECT user_id FROM ".TB_USER." WHERE accessname='".$_POST['user']."'");
					if ($this->db->num_rows() > 0){
						$userID = $this->db->fetch(0)->user_id;
						$error = $cUserRequest->lostPassword($userID);
					} else {
						$error = "Incorrect User Name!";
					}
					break;
			}

			if ($error == ''){
				echo '<html><head><meta http-equiv="refresh" content="3; url=../voc_web_manager.html"><title>mail result</title></head><body>' . "Mail sent successfully. Redirect to main page in 3 seconds." . '</body></html>';
			} else {
				echo '<html><head><meta http-equiv="refresh" content="3; url=../voc_web_manager.html"><title>mail result</title></head><body>' . $error .' Redirect to main page in 3 seconds.'. '</body></html>';
			}
	}

	private function actionMsdsUploaderBasic()
	{

		//little hack
		$request = array('category'=>$this->getFromRequest('itemID'), 'id'=>$this->getFromRequest('id'));
		$this->smarty->assign('request', $request);
		$cfd = $this->noname($request, $this->user, $this->db, $this->smarty);

		$step = 'assign';

		$msds = new MSDS($this->db);
		$result = $msds->upload('basic');



		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);
		$request = $this->getFromRequest();
		$title->getTitle($request);

		$product = new Product($this->db);
		$recognized = array();
		$unrecognized = array();
		foreach($result['msdsResult'] as $msdsResult)
		{
			if ($msdsResult['isRecognized'])
			{
				$recognized[] = $msdsResult;
			}
			else
			{
				$unrecognized[] = $msdsResult;
			}
		}

		//errors
		foreach($result['filesWithError'] as $fileWithError)
		{
			$failedSheet['msdsName'] = $fileWithError['name'];
			$failedSheet['reason'] = $fileWithError['error'];
			$failedSheets[] = $failedSheet;
		}

		$cntFailed = count($failedSheets);
		$this->smarty->assign('cntFailed', $cntFailed);
		$this->smarty->assign('failedSheets', $failedSheets);

		$productList = $product->getFormatedProductList($cfd['companyID']);
		$this->smarty->assign('productList', $productList);

		$cnt['recognized'] = count($recognized);
		$cnt['unrecognized'] = count($unrecognized);
		$maxCnt = max($cnt);
		$this->smarty->assign('cnt', $cnt);
		$this->smarty->assign('maxCnt', $maxCnt);
		$this->smarty->assign('recognized', $recognized);

		$this->smarty->assign('unrecognized', $unrecognized);

//		$title = new Titles($smarty);
//		$title->titleMsdsUploader($step,"Basic");

		$this->smarty->assign('step', $step);
		$this->smarty->display('tpls:msdsUploader.tpl');
	}

	private function actionMsdsUploader()
	{

		if ($this->getFromRequest('button') != "Back")
		{
			$step = $this->getFromRequest('step');
		}
		else
		{
			$step = "main";
		}
		//fullNavigation($_GET['itemID'], $user, $db, $smarty, $xnyo);
		//little hack
		$request = array('category'=>$this->getFromRequest('itemID'), 'id'=>$this->getFromRequest('id'));
		$this->smarty->assign('request', $request);
		$cfd = $this->noname($request);
		//titles new!!! {panding}
		$title = new TitlesNew($this->smarty, $this->db);
		$title->getTitle($this->getFromRequest());

		switch ($step)
		{
			case "main":
				if ($this->getFromRequest('basic') == "yes")
				{
					$this->smarty->assign("basic","yes");
				}
				else
				{
					$this->smarty->assign("basic","no");

					//	Set company ID
					$userDetails = $this->user->getUserDetails($_SESSION['user_id'], true);
					$companyID = ($userDetails['accesslevel_id'] != 3) ? $userDetails['company_id'] : 0;
					$this->smarty->assign("companyID",$companyID);

					//	If sandbox then use special URL
					/*if (REGION !== DEFAULT_REGION) {
						$swfUrl = (ENVIRONMENT == "server") ? "modules/flash/".REGION."/uploader.swf?companyID=".$companyID
																		: "modules/flash/".REGION."/sandbox/uploader.swf?companyID=".$companyID;
					} else {
						$swfUrl = (ENVIRONMENT == "server") ? "modules/flash/uploader.swf?companyID=".$companyID
																		: "modules/flash/sandbox/uploader.swf?companyID=".$companyID;
					}*/
					$voc2vps = new VOC2VPS($this->db);
					$customerLimits = $voc2vps->getCustomerLimits($companyID);

					$swfUrl = "modules/flash/uploader.swf?companyID=".$companyID .
							"&memoryLimit=".$customerLimits['memory']['current_value'] .
							"&MSDSLimit=".$customerLimits['MSDS']['current_value'] .
							"&memoryMaxLimit=".$customerLimits['memory']['max_value'] .
							"&MSDSMaxLimit=".$customerLimits['MSDS']['max_value'];

					$this->smarty->assign("swfUrl", $swfUrl);
				}
				$this->smarty->assign("step",$step);
				$this->smarty->display("tpls:msdsUploader.tpl");
							break;

			case "save":
				//recognized sheets
				$cnt['recognized'] = $this->getFromRequest('sheetRecCount');
				for ($i=0;$i<$cnt['recognized'];$i++)
				{
					$assignment['msdsName'] = $this->getFromRequest('sheetRec_'.$i);
					$assignment['realName'] = $this->getFromRequest('sheetRecRealName_'.$i);
					$assignment['failed'] = FALSE;
					if(!is_null($this->getFromRequest('product2sheetRec_'.$i)))
					{
						$assignment['productID'] = $this->getFromRequest('product2sheetRec_'.$i);
					}
					else
					{
						$assignment['productID'] = NULL;
					}
					$assignments[] = $assignment;
				}

				//unrecognized sheets
				$cnt['unrecognized'] = $this->getFromRequest('sheetUnrecCount');
				for ($i=0;$i<$cnt['unrecognized'];$i++)
				{
					$assignment['msdsName'] = $this->getFromRequest('sheetUnrec_'.$i);
					$assignment['realName'] = $this->getFromRequest('sheetUnrecRealName_'.$i);
					$assignment['failed'] = FALSE;
					if(!is_null($this->getFromRequest('product2sheetUnrec_'.$i)))
					{
						$assignment['productID'] = $this->getFromRequest('product2sheetUnrec_'.$i);
					}
					else
					{
						$assignment['productID'] = NULL;
					}
						$assignments[] = $assignment;
				}

				//getting company/facilty/department id
				$save['companyID'] = $cfd['companyID'];
				$save['facilityID'] = $cfd['facilityID'];
				$save['departmentID'] = $cfd['departmentID'];;

				$msds = new MSDS($this->db);
				$result = $msds->validateAssignments($assignments);

				for ($i=0;$i<count($result);$i++)
				{
					if($result[$i]['status'] == "ok")
					{
						$msdsArray['name'] = $assignments[$i]['msdsName'];
						$msdsArray['real_name'] = $assignments[$i]['realName'];
						$msdsArray['size'] = filesize("../msds/".$msdsArray['real_name']);
						$msdsArray['productID'] = $assignments[$i]['productID'];
						$save['msds'][] = $msdsArray;
					}
					else
					{
						$failedSheet['msdsName'] = $result[$i]['msdsName'];
						switch ($result[$i]['reason'])
						{
							case "alreadyAssigned":
								$failedSheet['reason'] = "This product is already assigned to other MSDS sheet.";
								break;
							case "multiple":
								$failedSheet['reason'] = "More than one sheet is assigned to one product.";
								break;
						}
						$assignments[$i]['failed'] = TRUE;
						$failedSheets[] = $failedSheet;
					}
				}

				if ($failedSheets)
				{	//back to assign step
					$step = "assign";
					foreach($assignments as $assignment)
					{

						if (!empty($assignment['productID']))
						{

							$sheet['name'] = $assignment['msdsName'];
							$sheet['real_name'] = $assignment['realName'];
							$sheet['product_id'] = $assignment['productID'];
							$sheet['failed'] = $assignment['failed'];

							$recognized[] = $sheet;


						}
						else
						{
							$sheet['name'] = $assignment['msdsName'];
							$sheet['real_name'] = $assignment['realName'];
							$unrecognized[] = $sheet;
						}
					}
					$this->smarty->assign('recognized',$recognized);
					$this->smarty->assign('unrecognized',$unrecognized);

					$cnt['recognized'] = count($recognized);
					$cnt['unrecognized'] = count($unrecognized);
					$maxCnt = max($cnt);
					$this->smarty->assign('cnt', $cnt);
					$this->smarty->assign('maxCnt', $maxCnt);

					$product = new Product($this->db);
					$productList = $product->getFormatedProductList($cfd['companyID']);
					$this->smarty->assign('productList', $productList);

					$cntFailed = count($failedSheets);
					$this->smarty->assign('cntFailed',$cntFailed);
					$this->smarty->assign('failedSheets',$failedSheets);

//					$title = new Titles($smarty);
//					$title->titleMsdsUploader($step,"Basic");

					$this->smarty->assign('step', 'assign');
					$this->smarty->display('tpls:msdsUploader.tpl');
				}
				else
				{ // finish upload
					$msds->addSheets($save);

					//save vps limits
					$userDetails = $this->user->getUserDetails($_SESSION['user_id'], true);
					$companyID = ($userDetails['accesslevel_id'] != 3) ? 0 : $userDetails['company_id'];
					if ($userDetails['accesslevel_id'] != 3)
					{
						$voc2vps = new VOC2VPS($this->db);
						$customerLimits = $voc2vps->getCustomerLimits($userDetails['company_id']);

						$MSDSLimit = array (
											'limit_id' 		=> 1,
											'current_value' => $customerLimits['MSDS']['current_value']+count($save['msds']),
											'max_value' 	=> $customerLimits['MSDS']['max_value']
											);
						$voc2vps->setCustomerLimitByID($userDetails['company_id'], $MSDSLimit);

						$totalSize = 0;
						foreach ($save['msds'] as $file)
						{
							$totalSize += $file['size'];
						}
						$sizeMb = round($totalSize/1024/1024,2);
						$memoryLimit = array (
												'limit_id' => 2,
												'current_value' => $customerLimits['memory']['current_value']+$sizeMb,
												'max_value' => $customerLimits['memory']['max_value']
											);
						$voc2vps->setCustomerLimitByID($userDetails['company_id'], $memoryLimit);
					}

					//going back
					if (!empty($save['departmentID']))
					{
						header("Location: ?action=browseCategory&category=department&id=".$save['departmentID']."&bookmark=mix");
					}
					elseif (!empty($save['facilityID']))
					{
						header("Location: ?action=browseCategory&category=facility&id=".$save['facilityID']."&bookmark=department");
					}
					elseif (!empty($save['companyID']))
					{
						header("Location: ?action=browseCategory&category=company&id=".$save['companyID']."");
					}
					elseif ($this->getFromRequest('category') == 'root')
					{
						header("Location: ?action=browseCategory&category=root");
					}
				}
				break;

			case "edit":

				$productID = $this->getFromRequest('productID');

				$product = new Product($this->db);
				$productDetails = $product->getProductDetails($productID);
				$this->smarty->assign('productDetails',$productDetails);
				$msds = new MSDS($this->db);

				$pagination = new Pagination($msds->getUnlinkedMsdsSheetsCount());
				$pagination->url = "?action=msdsUploader&step=edit&productID=655&itemID=".urlencode($this->getFromRequest('itemID'))."&id=".urlencode($this->getFromRequest('id'));

				$unlinkedMsdsSheets = $msds->getUnlinkedMsdsSheets($pagination);
				$this->smarty->assign('unlinkedMsdsSheets',$unlinkedMsdsSheets);

//				$title = new Titles($smarty);
//				$title->titleEditItem("MSDS Sheets");

				$this->smarty->assign('pagination',$pagination);
				$this->smarty->assign('step', 'edit');
				$this->smarty->display('tpls:msdsUploader.tpl');
				break;

			case "saveEdit":
				$selectedSheetID = $this->getFromRequest('selectedSheet');
				$productID = $this->getFromRequest('productID');

				$msds = new MSDS($this->db);
				$msds->linkSheetToProduct($selectedSheetID, $productID);

				$product = new Product($this->db);
				$productDetails = $product->getProductDetails($productID);

				$notify=new Notify($this->smarty);
				$notify->successEdited("product", $productDetails['product_nr']);
				//showCategory("product", $_GET['id'], $db, $xnyo, $smarty, $user);
				header("Location: ?action=browseCategory&category=".urlencode($this->getFromRequest('itemID'))."&id=".urlencode($this->getFromRequest('id'))."&bookmark=product");
				break;
		}
	}

	private function actionChangeMixesCreationDateFromDateToTimestamp() {
		echo "<p></p>";

		$this->db->beginTransaction();

		$query = "select mix_id, creation_time from mix";

		echo "<p>Get mixes..</p>";

		$this->db->query($query);

		$mixes = $this->db->fetch_all_array();
		$count = count($mixes);

		echo "<p>Mixes count: $count</p>";

		$query_drop_column = "ALTER TABLE mix DROP COLUMN creation_time";

		echo "<p>Drop column creation_time..</p>";

		$this->db->query($query_drop_column);

		echo "<p>Dropped</p>";

		$query_create_column = "ALTER TABLE mix ADD COLUMN creation_time int";

		echo "<p>Create column creation_time INT...</p>";

		$this->db->query($query_create_column);

		echo "<p>Created</p>";

		echo "<p>Update timestamps to mixes..</p>";


		for($i = 0; $i < $count; $i++) {

			$timestamp = strtotime($mixes[$i]['creation_time']);

			$update_query = "UPDATE mix SET creation_time = $timestamp WHERE mix_id = {$mixes[$i]['mix_id']}";
			//echo "<p>$update_query</p>";
			$this->db->query($update_query);
		}

		echo "<p>Updated</p>";

		echo "<p><b style='color:Green;'><h1>DONE</h1></b></p>";
	}

	private function actionLogout() {
		$this->user->logout();
	}
}
?>
