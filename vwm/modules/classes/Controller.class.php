<?php

use VWM\Label\CompanyLevelLabel;
use VWM\Label\CompanyLabelManager;

class Controller {

	/**
	 * @var Smarty
	 */
    protected $smarty;
    /**
     * @var xnyo
     */
    protected $xnyo;
    /**
     *
     * @var db
     */
    protected $db;
    protected $user;
    protected $action;
    private $post;
    private $request;
    protected $category;
    protected $parentCategory;
    protected $filter;
    private $typeInformer;


	/**
	 * List of blocks
	 * @var array
	 */
	private $blocksToInsert = array(
		self::INSERT_AFTER_SEARCH => array(),
		self::INSERT_AFTER_VOC_GAUGE => array(),
		self::INSERT_NOX_LOG_BEFORE_NOX_GAUGE => array(),
		self::INSERT_AFTER_INDUSTRY_TYPES => array(),
        self::INSERT_AFTER_NOX_GAUGE => array(),
	);


	const INSERT_AFTER_SEARCH = 0;
	const INSERT_AFTER_VOC_GAUGE = 1;
	const INSERT_NOX_LOG_BEFORE_NOX_GAUGE = 2;
	const INSERT_AFTER_INDUSTRY_TYPES = 3;
    const INSERT_AFTER_NOX_GAUGE = 4;

    function Controller($smarty, $xnyo, $db, $user, $action) {
        $this->smarty = $smarty;
        $this->xnyo = $xnyo;
        $this->db = $db;
        $this->user = $user;
        $this->action = $action;
        $this->request = $_GET;
        $this->post = $_POST;

        if (isset($this->request['notify']) and is_numeric($this->request['notify'])) {
            $notifyc = new Notify(null, $db);

            $notify = $notifyc->getPopUpNotifyMessage($this->request['notify']);
            $this->smarty->assign("notify", $notify);
        }

        //	footer is always up to date :)
        $this->smarty->assign('footerYear', date('Y'));
    }

    protected function forward($controller, $function, $vars, $controllerType = 'main') { 

		switch ($controllerType) {
			case "vps":
				$className = "CV" . ucfirst($controller);
				break;
			case "admin":
				$className = "CA" . ucfirst($controller);
				break;
			case "sales":
				$className = "CS" . ucfirst($controller);
				break;

			case "supplier":
				$className = "CSup" . ucfirst($controller);
				break;
			default:
				$className = "C" . ucfirst($controller);
				break;
		}
		/*
        if ($controllerType == 'vps') {

        } else if ($controllerType == 'admin') {
            $className = "CA" . ucfirst($controller);
        } else {
            $className = "C" . ucfirst($controller);
        }
		*/
        //echo $className;
        //echo $function;
        //exit;

        if (class_exists($className)) {
            $controllerObj = new $className($this->smarty, $this->xnyo, $this->db, $this->user, $this->action);
        } else {			
            throw new Exception('404');
        }
				 
        if (method_exists($controllerObj, $function)) {
            $controllerObj->$function($vars);
        } else {
            throw new Exception('404');
        }

        return $controllerObj;
    }

    protected function runCommon($controllerType = 'main') {
        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($this->getFromRequest());

		switch ($controllerType){
			case "admin":
				$functionName = 'action' . ucfirst($this->action) . 'ACommon';
				break;
			case "vps":
				$functionName = 'action' . ucfirst($this->action) . 'VCommon';
				break;
			case "sales":
				$functionName = 'action' . ucfirst($this->action) . 'SCommon';
				break;
			case "supplier":
				$functionName = 'action' . ucfirst($this->action) . 'SupCommon';
				break;
			default :
				$functionName = 'action' . ucfirst($this->action) . 'Common';
		}
		/*
        if ($controllerType !== 'admin') {
            $functionName = 'action' . ucfirst($this->action) . 'Common';
        } elseif ($controllerType == 'admin') {
            $functionName = 'action' . ucfirst($this->action) . 'ACommon';
        } elseif ($controllerType == 'vps') {
            $functionName = 'action' . ucfirst($this->action) . 'VCommon';
        }
		*/
        if (method_exists($this, $functionName))
            $this->$functionName();
    }


	public function runAction() {		
		$this->runCommon();		
		$functionName = 'action'.ucfirst($this->action);
		if (method_exists($this,$functionName)) {
			$this->$functionName();
		}
	}

    protected function filterList($category, $dateFormat = false) {
        $this->filter = new Filter($this->db, $category);
        $this->smarty->assign('filterArray', $this->filter->getJsonFilterArray());
        $filterData = array
            (
            'filterField' => $this->getFromRequest('filterField'),
            'filterCondition' => $this->getFromRequest('filterCondition'),
            'filterValue' => $this->getFromRequest('filterValue'),
            'dateFormat' => $dateFormat
        );



        if ($this->getFromRequest('searchAction') == 'filter') {
            $this->smarty->assign('filterData', $filterData); // = 11/05/2010 00:00:00 and 11/05/2010 23:59:59
            $this->smarty->assign('searchAction', 'filter');
        }
        $filterStr = $this->filter->getSubQuery($filterData);

        return $filterStr;
    }

    protected function sortList($category, $defaultNum) {
        $sort = new Sort($this->db, $category, $defaultNum);
        $getSort = $this->getFromRequest('sort');
        if (isset($getSort)) {
            $sortStr = $sort->getSubQuerySort($this->getFromRequest('sort'));
            $this->smarty->assign('sort', $this->getFromRequest('sort'));
        } else {
            $sortStr = $sort->getSubQuerySort();
            $this->smarty->assign('sort', $defaultNum);
        }

        $getSearchAction = $this->getFromRequest('searchAction');
        if (isset($getSearchAction))
            $this->smarty->assign('searchAction', $this->getFromRequest('searchAction'));

        return $sortStr;
    }

    public function actionUserRequest() {
		$radioSelected = 'lost';
		if ($_POST['productAction'] == 'Submit'){
			$userRequest = new UserRequest($this->db);
			switch ($_POST['radioRequest']){
				case 'lost':
					//generate password and send it in e-mail
					$userID = $_POST['user_id'];
					$error = $userRequest->lostPassword($userID);
					break;
				case 'cancel':
					//delete user
					$userID = $_POST['user_id'];
					$action = 'delete';
					$userRequest->setAction($action);
					$this->db->query("SELECT username, email, accesslevel_id, company_id, facility_id, department_id FROM ".TB_USER." WHERE user_id=".$userID);
					$accesslevelID = $this->db->fetch(0)->accesslevel_id;
					$username = $this->db->fetch(0)->username;
					$email = $this->db->fetch(0)->email;
					switch ($accesslevelID){
						case 0:
							$categoryType = 'company';
							$categoryID = $this->db->fetch(0)->company_id;
							break;
						case 1:
							$categoryType = 'facility';
							$categoryID = $this->db->fetch(0)->facility_id;
							break;
						case 2:
							$categoryType = 'department';
							$categoryID = $this->db->fetch(0)->department_id;
							break;
					}
					$userRequest->setALL($action, $userID, $username, 'NULL', 'NULL', $email, 'NULL', 'NULL', $categoryType, $categoryID);
					$error = $userRequest->save();
					if ($error == '') {
						$userRequest->sendMail('User Request. Please, delete user: '.$username.".");
					}
					break;
				case 'username':
					//change or create new user
					//var_dump($_POST); die();
					if ($_POST['newUser'] == 'on'){
						$newUserName = $_POST['new_username'];
						$newAccessName = $_POST['new_accessname'];
						$email = $_POST['email'];
						$phone = $_POST['phone'];
						$mobile = $_POST['mobile'];
						$categoryType = $_POST['structureCategory'];
						$categoryID = $_POST['structure_id'];
						$action = 'add';
						$userRequest->setALL($action, 'NULL', 'NULL', $newUserName, $newAccessName, $email, $phone, $mobile, $categoryType, $categoryID);
						if ($newUserName != '' && $newAccessName != '' && $phone != '' && $mobile != '' && preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/', $email)){
							$error = $userRequest->save();
						} else {
							$error = 'Incorrect data!';
						}
						if ($error == '') {
							$userRequest->sendMail('Please, create new user.');
						}
					} else {
						$userID = $_POST['user_id'];
						$newUserName = $_POST['new_username'];
						$action = 'change';
						$userRequest->setAction($action);
						$this->db->query("SELECT username, email, accesslevel_id, company_id, facility_id, department_id FROM ".TB_USER." WHERE user_id=".$userID);
						$accesslevelID = $this->db->fetch(0)->accesslevel_id;
						$username = $this->db->fetch(0)->username;
						$email = $this->db->fetch(0)->email;
						switch ($accesslevelID){
							case 0:
								$categoryType = 'company';
								$categoryID = $this->db->fetch(0)->company_id;
								break;
							case 1:
								$categoryType = 'facility';
								$categoryID = $this->db->fetch(0)->facility_id;
								break;
							case 2:
								$categoryType = 'department';
								$categoryID = $this->db->fetch(0)->department_id;
								break;
						}
						$userRequest->setALL($action, $userID, $username, $newUserName, 'NULL', $email, 'NULL', 'NULL', $categoryType, $categoryID);
						if ($newUserName != ''){
							$error = $userRequest->save();
						}
						if ($error == ''){
							$userRequest->sendMail('Please, change username.');
						}
					}
					break;
				case 'password':
					//change old password to new password
					$oldPassword = $_POST['oldpass'];
					$newPassword = $_POST['newpass'];
					$repeatNewPassword = $_POST['renewpass'];
					$userID = $_SESSION['user_id'];
					$error = $userRequest->changePassword($userID, $oldPassword, $newPassword, $repeatNewPassword);
					break;
			}

			if ($this->getFromRequest('category') == 'company' && $error == ''){
				header('Location: ?action=browseCategory&category='.$this->getFromRequest('category').'&id='.$this->getFromRequest('id'));
			} elseif ($this->getFromRequest('category') == 'facility' && $error == ''){
				header('Location: ?action=browseCategory&category='.$this->getFromRequest('category').'&id='.$this->getFromRequest('id').'&bookmark=department');
			}
		}
        $request = $this->getFromRequest();

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);
        $this->noname($request, $this->user, $this->db, $this->smarty);

		$user = new User($this->db);
		if ($request['category']=='company'){
			$userList = $user->getUserListByCompany($request['id']);
			$facility = new Facility($this->db);
			$structureList = $facility->getFacilityListByCompany($request['id']);
		} elseif ($request['category']=='facility') {
			$userList = $user->getUserListByFacility($request['id']);
			$department = new Department($this->db);
			$structureList = $department->getDepartmentListByFacility($request['id']);
		}

		$this->smarty->assign('error', $error);
		$this->smarty->assign('structureList', $structureList);
		$this->smarty->assign('userList', $userList);
        $this->smarty->assign('accessname', $_SESSION['username']);
        $this->smarty->assign('request', $request);

        if (!$this->getFromPost('productAction') == 'Submit') {
            $this->smarty->assign("productReferer", $_SERVER["HTTP_REFERER"]);
        }
        $this->smarty->assign("tpl", "tpls/userRequestForm.tpl");
        $this->smarty->display("tpls:index.tpl");
    }

    public function actionCompanySetupRequest() {
		$cemail = new EMail();

		$to = array ("denis.nt@kttsoft.com", "dmitry.vd@kttsoft.com", "jgypsyn@gyantgroup.com ");

		$from = "newsetuprequest@vocwebmanager.com";

        $company = new Company($this->db);
        $facility = new Facility($this->db);
        $auth = $_SESSION['auth'];

        $category = $this->getFromRequest('category');
        $facilityDetails = array();
		$setupLevel = '';
		//var_dump($_POST);
		if ($_POST['submitForm'] == 'Submit'){
			$setupRequest = new SetupRequest($this->db);
			$setupRequest->setVOCMonthlyLimit($_POST['voc_monthly_limit']);
			$setupRequest->setVOCAnnualLimit($_POST['voc_annual_limit']);
			$setupRequest->setParentID($this->getFromRequest('id'));
			if ($this->getFromRequest('category') == 'company'){
				$setupRequest->setName($_POST['facility_name']);
				$setupRequest->setEPANumber($_POST['epa']);
				$setupRequest->setAddress($_POST['address']);
				$setupRequest->setCity($_POST['city']);
				$setupRequest->setCounty($_POST['county']);
				$setupRequest->setZipCode($_POST['zip_code']);
				$setupRequest->setEmail($_POST['email']);
				$setupRequest->setPhone($_POST['phone']);
				$setupRequest->setFax($_POST['fax']);
				$setupRequest->setContact($_POST['contact']);
				$setupRequest->setTitle($_POST['title']);
				$setupRequest->setCountryID($_POST['country']);
				if ($_POST['country'] == '215'){
					$setupRequest->setStateID($_POST['stateSelect']);
					$this->db->query("SELECT name FROM ".TB_STATE." state_id=".$_POST['stateSelect']);
					$setupRequest->setState($this->db->fetch(0)->name);
				} else {
					$setupRequest->setState($_POST['stateText']);
				}
				$error = $setupRequest->save('facility');
				if ($error == ''){
					$subject = "Facility Setup Request";
					$message = "Please, create new facility.\n";
					$message .= "Facility Name: ".$_POST['facility_name']."\n";
					$message .= "Creater email: ".$_POST['email'];
					$cemail->sendMail($from, $to, $subject, $message);
					header('Location: ?action=browseCategory&category=company&id='.$this->getFromRequest('id'));
					die();
				} else {
					$this->smarty->assign('error', $error);
					$this->smarty->assign('setupRequest', $setupRequest);
				}
			} elseif ($this->getFromRequest('category') == 'facility'){
				$setupRequest->setName($_POST['department_name']);
				$this->db->query("SELECT * FROM ".TB_FACILITY." WHERE facility_id=".$this->getFromRequest('id'));
				//var_dump("SELECT * FROM ".TB_FACILITY." WHERE facility_id=".$this->getFromRequest('id')); die();
				$createrEmail = html_entity_decode(mysql_escape_string($this->db->fetch(0)->email));
				$setupRequest->setEmail($createrEmail);
				$error = $setupRequest->save('department');
				if ($error == ''){
					$subject = "Department Setup Request";
					$message = "Please, create new department.\n";
					$message .= "Department Name: ".$_POST['department_name']."\n";
					$message .= "Creater email: ".$createrEmail;
					$cemail->sendMail($from, $to, $subject, $message);
					header('Location: ?action=browseCategory&category=facility&id='.$this->getFromRequest('id').'&bookmark=department');
					die();
				} else {
					$this->smarty->assign('error', $error);
					$this->smarty->assign('setupRequest', $setupRequest);
				}
			}
		} else {
			$setupRequest = new SetupRequest($this->db);
			$this->smarty->assign('setupRequest', $setupRequest);
		}
        switch ($category) {
            case 'company':
                $referFacilityID = false;
                $referCompanyID = $this->getFromRequest('id');
				$setupLevel = 'Facility';
				$country = new Country($this->db);
				$countryList = $country->getCountryList();
				$state = new State($this->db);
				$stateList = $state->getStateList($country->getCountryIDByName('USA'));
				$this->smarty->assign('countryList', $countryList);
				$this->smarty->assign('stateList', $stateList);
                break;
            case 'facility':
                $referFacilityID = $this->getFromRequest('id');
                $facilityDetails = $facility->getFacilityDetails($referFacilityID);
                $referCompanyID = $facilityDetails['company_id'];
				$setupLevel = 'Department';
                break;
            default:
                throw new Exception('deny');
                break;
        }

        $companyDetails = array();

        switch ($auth['accesslevel_id']) {
            case "3":
                //	super user
                $companyDetails = $company->getCompanyDetails($referCompanyID);
                break;
            case "0":
                //	company level
                $companyDetails = $company->getCompanyDetails($auth['company_id']);
                break;
            case "1":
                //	facility level
                //	rewrite facilityDetails if needed
                if ($referFacilityID != $auth['facility_id']) {
                    $facilityDetails = $facility->getFacilityDetails($auth['facility_id']);
                }
                break;
            default:
                throw new Exception('deny');
                break;
        }

		$request = $this->getFromRequest();

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);
        $this->noname($request, $this->user, $this->db, $this->smarty);

        $this->smarty->assign('accessname', $_SESSION['username']);
        $this->smarty->assign('request', $request);

        $this->smarty->assign('companyDetails', $companyDetails);
        $this->smarty->assign('facilityDetails', $facilityDetails);
		$this->smarty->assign('setupLevel', $setupLevel);

        $this->smarty->assign('tpl', 'tpls/companySetupRequestForm.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionAddNewProduct() {
        //  if form were submitted
        if ($this->getFromPost('productAction') == 'Submit') {

			$productRequest = new NewProductRequest($this->db);
			$productRequest->setSupplier($this->getFromPost('productSupplier'));
			$productRequest->setProductId($this->getFromPost('productId'));
			$productRequest->setName($this->getFromPost('productName'));
			$productRequest->setDescription($this->getFromPost('productDescription'));
			$productRequest->setUserId($_SESSION['user_id']);
			$productRequest->setMsdsId(0);
			$productRequest->setStatus(NewProductRequest::STATUS_NEW);

			$violationList = $productRequest->validate();
			if(count($violationList) == 0) {
				if(!$productRequest->save()) {
					throw new Exception('Failed to save request. This should not happen');
				}

				//TODO: needs complete rewrite
				if ($_FILES) {
					$strangeRequest = array('category' => $this->getFromRequest('category'),
						'id' => $this->getFromRequest('id'));
					$sSave = $this->noname($strangeRequest);
					$msds = new MSDS($this->db);
					$msRes = $msds->upload('basic');
					$save["companyID"] = $sSave['companyID'];
					$save["facilityID"] = $sSave['facilityID'];
					$save["departmentID"] = $sSave['departmentID'];
					$save['msds'] = $msRes['msdsResult'];
					$msds->addSheets($save);
					$msdsId = $this->db->getLastInsertedID();
				} else {
					$msdsId = 0;
				}

				$productRequest->setMsdsId($msdsId);
				$productRequest->save();

				$manager = new NewProductRequestManager($this->db);
				$manager->setEmailService(new EMail());
				$manager->sendNewEmailNotification($productRequest);

				header("Location:" . $this->getFromPost('productReferer') .
						"&message=".  urlencode('New Product Submitted')."&color=green");
                die();
			} else {
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('productRequest', $productRequest);
			}



            /*$prRequest = new NewProductRequest($this->db);
            $prRequest->setSupplier($this->getFromPost('productSupplier'));
            $prRequest->setProductId($this->getFromPost('productId'));
            $prRequest->setName($this->getFromPost('productName'));
            $prRequest->setDescription($this->getFromPost('productDescription'));
            $productReq['productSupplier'] = $this->getFromPost('productSupplier');
            $productReq['productId'] = $this->getFromPost('productId');
            $productReq['productName'] = $this->getFromPost('productName');
            $productReq['productDescription'] = $this->getFromPost('productDescription');
            $productReq['productReferer'] = $this->getFromPost('productReferer');
            $validationStatus = $prRequest->validate($productReq);
            if ($validationStatus["summary"] == "true") {
                $request = array('category' => $this->getFromRequest('category'), 'id' => $this->getFromRequest('id'));
                $sSave = $this->noname($request);
                $msds = new MSDS($this->db);
                $msRes = $msds->upload('basic');
                $save["companyID"] = $sSave['companyID'];
                $save["facilityID"] = $sSave['facilityID'];
                $save["departmentID"] = $sSave['departmentID'];
                $save['msds'] = $msRes['msdsResult'];
                $msds->addSheets($save);

                $tmpId = $this->db->getLastInsertedID();
                $prRequest->setMsdsId($tmpId);
                $msg = "New product requested. Later Denis will add more information to this email :)";
                $newProductMail = new EMail();
                $newProductMail->sendMail(
                        'newproductrequest@vocwebmanager.com', array('denis.nt@kttsoft.com', 'jgypsyn@gyantgroup.com'), 'New Product Request', $msg);
                $prRequest->save();
                $query = "UPDATE " . TB_MSDS_FILE . " SET product_id=" . $this->db->getLastInsertedID() . " WHERE msds_file_id=" . $tmpId;
                $this->db->query($query);
                header("Location:" . $productReq['productReferer'] . "&message=".  urlencode('New Product Submitted')."&color=green");  //  redirect
                die();
            } else {
                $this->smarty->assign('validStatus', $validationStatus);
                $this->smarty->assign('productSupplier', $productReq['productSupplier']);
                $this->smarty->assign('productId', $productReq['productId']);
                $this->smarty->assign('productName', $productReq['productName']);
                $this->smarty->assign('productDescription', $productReq['productDescription']);
                $this->smarty->assign('productReferer', $productReq['productReferer']);
            }*/
        }
        $request = $this->getFromRequest();

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);
        $this->noname($request, $this->user, $this->db, $this->smarty);

        $this->smarty->assign('accessname', $_SESSION['username']);
        $this->smarty->assign('request', $request);

		$referer = ($this->getFromPost('productReferer'))
				? $this->getFromPost('productReferer')
				: $_SERVER["HTTP_REFERER"];

        $this->smarty->assign("productReferer", $referer);

        $this->smarty->assign("tpl", "tpls/addNewProduct.tpl");
        $this->smarty->display("tpls:index.tpl");
    }

	public function actionShowTraining(){
		$request = $this->getFromRequest();
        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);
        $this->noname($request, $this->user, $this->db, $this->smarty);

		switch ($request['category']){
			case 'company':
				$trainingParts = array('login' => 'How to Login',
									   'overview' => 'Overview',
									   'report' => 'Create Report',
									   'graph' => 'Company at a Glance Graphs',
									   'payment' => 'Payment Process',
									   'training' => 'See Entire Video',
									   'npvideo' => 'New Product Video');
				break;
			case 'facility':
				$trainingParts = array('login' => 'How to Login',
									   'overview' => 'Overview',
									   'report' => 'Create Report',
									   'graph' => 'Facility at a Glance Graphs',
									   'msds' => 'How to Manage MSDS & Product Library',
									   'newproduct' => 'How to Add a New Product',
									   'management' => 'Equipment Management',
									   'eqgraph' => 'Equipment Graphs',
									   'payment' => 'Payment Process',
									   'training' => 'See Entire Video',
									   'npvideo' => 'New Product Video');
				break;
			case 'department':
				$trainingParts = array('login' => 'How to Login',
									   'overview' => 'Overview',
									   'pfpmix' => 'Pre Formaulated Mix',
									   'singlemix' => 'Single Mix Input',
									   'report' => 'Create Report',
									   'msds' => 'How to Manage MSDS & Product Library',
									   'newproduct' => 'How to Add a New Product',
									   'management' => 'Equipment Management',
									   'eqgraph' => 'Equipment Graphs',
									   'training' => 'See Entire Video',
									   'npvideo' => 'New Product Video');
				break;
		}

		$this->smarty->assign('trainingParts', $trainingParts);
        $this->smarty->assign('accessname', $_SESSION['username']);
        $this->smarty->assign('request', $request);

        $this->smarty->assign("referer", $_SERVER["HTTP_REFERER"]);
        $this->smarty->assign("tpl", "tpls/training.tpl");
        $this->smarty->display("tpls:index.tpl");
	}

	private function actionShowIssueReportCommon() {
        $request = $this->getFromRequest();

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);
        $this->noname($request, $this->user, $this->db, $this->smarty);

        $this->smarty->assign('accessname', $_SESSION['username']);
        $this->smarty->assign('request', $request);

        $this->smarty->assign("referer", $_SERVER["HTTP_REFERER"]);
        $this->smarty->assign("tpl", "tpls/issueReportForm.tpl");
        $this->smarty->display("tpls:index.tpl");
    }

    private function actionReportIssueCommon() {

        $request = $this->getFromRequest();
        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);



        if ($this->getFromPost("issueAction") == "Send") {
            //	Group issue details

            $issueDetails["title"] = $this->getFromPost("issueTitle");
            $issueDetails["description"] = $this->getFromPost("issueDescription");
            $issueDetails["referer"] = $this->getFromPost("referer");
            $issueDetails["creatorID"] = $this->getFromPost("user_id");

            $userID = $this->user->getLoggedUserID();

            if (!$userID) /* User id is not defined */ {
                throw new Exception("creatorID doesnot exists in POST.");
            } else {
                $issueDetails["creatorID"] = $userID;
            }

            //	Validate issue
            $validation = new Validation();
            $validationStatus = $validation->validateIssue($issueDetails);

            if ($validationStatus["summary"] == "true") {
                //	Add issue to DB
                $issue = new Issue($this->db);
                $issue->addIssue($issueDetails);

                //	Redirect to previous page
                header("Location:" . $issueDetails["referer"] . "&message=Issuereported&color=green");
            } else {
                //	Incorrect input
                $this->smarty->assign("issueTitle", $issueDetails["title"]);
                $this->smarty->assign("issueDescription", $issueDetails["description"]);
                $this->smarty->assign("referer", $issueDetails["referer"]);



                /* 	the modern style */
                $notifyc = new Notify(null, $this->db);
                $notify = $notifyc->getPopUpNotifyMessage(401);
                $this->smarty->assign("notify", $notify);

                $this->smarty->assign("validStatus", $validationStatus);

                $this->noname();

                $this->smarty->assign('accessname', $_SESSION['username']);
                $this->smarty->assign('request', $request);

//				$title = new Titles($smarty);
//				$title->titleIssueReport();

                $this->smarty->assign("tpl", "tpls/issueReportForm.tpl");
                $this->smarty->display("tpls:index.tpl");
            }
        } else {
            //	Discard issue
            header("Location:" . $_POST["referer"]);
        }
    }

    private function actionSendReportCommon() {

        $request = $this->getFromRequest();
        $this->smarty->assign("request", $request);
        $this->noname();

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($request);

        switch ($request['category']) {
            case 'company':
                $companyID = $request['id'];
                break;
            case 'facility':
                $facility = new Facility($this->db);
                $facilityDetails = $facility->getFacilityDetails($request['id']);
				$facilityID = $request['id'];
                $companyID = $facilityDetails['company_id'];
                break;
            case 'department':
                $company = new Company($this->db);
				$department = new \VWM\Hierarchy\Department($this->db, $request['id']);
				$facilityID = $department->getFacilityId();
                $companyID = $company->getCompanyIDbyDepartmentID($request['id']);
                break;
        }

        $reportType = $request['reportType'];

        if (!$this->user->checkAccess('reports', $companyID)) {

            throw new Exception('deny');
        }

        //	OK, this company has access to this module, so let's setup..

        $ms = new ModuleSystem($this->db); //	TODO: show?
        $moduleMap = $ms->getModulesMap();



        $mReport = new $moduleMap['reports'];



        $params = array(
            'db' => $this->db,
            'reportType' => $reportType,
            'companyID' => $companyID,
            'request' => $request,
			'facilityID'=>$facilityID,
        );

        $result = $mReport->prepareSendReport($params);
        //var_dump($result);
        //exit;
        foreach ($result as $key => $data) {
            $this->smarty->assign($key, $data);
        }
 
        //	set js scripts
        $jsSources = array(
            'modules/js/reports.js',
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js'
        );
        $this->smarty->assign('jsSources', $jsSources);
        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);
        $this->smarty->assign('backUrl', '?action=createReport&category=' . $request['category'] . '&id=' . $request['id']);

        $this->smarty->display("tpls:index.tpl");
    }

    private function actionCreateReportCommon() {
        $request = $this->getFromRequest();
        $this->noname($request);
        $this->smarty->assign('request', $request);
        $this->smarty->assign('accessname', $_SESSION['username']);

        //titles new!!! {panding}
        $title = new TitlesNew($this->smarty, $tihs->db);

        $title->getTitle($request);

        switch ($request['category']) {
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

        $reportType = $this->getFromRequest('reportType');

        if (!$this->user->checkAccess('reports', $companyID)) {
            throw new Exception('deny');
        }
        //	OK, this company has access to this module, so let's setup..

        $ms = new ModuleSystem($this->db); //	TODO: show?
        $moduleMap = $ms->getModulesMap();
        $mReport = new $moduleMap['reports'];
        $result = $mReport->getAvailableReportsList($this->db, $companyID);
        $this->smarty->assign('reports', $result);



        $this->smarty->assign('tpl', 'reports/design/createReport.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    private function actionSettingsCommon() {
        $this->smarty->assign('request', $this->getFromRequest());
		
		if($_REQUEST['category']=='department'){
			$this->smarty->assign('departmentId', $_REQUEST['id']);
		}else{
			$this->smarty->assign('departmentId', 0);
		}
		
        $cfd = $this->noname();


        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($this->getFromRequest());

        //	Get rule list
        $rule = new Rule($this->db);
        $ruleList = $rule->getRuleList();
        //$cfd =  getCompanyFacilityDepartment($db);	//	Company Facility Department
        $customizedRuleList = $rule->getCustomizedRuleList($_SESSION['user_id'], $cfd['companyID'], $cfd['facilityID'], $cfd['departmentID']);
        $this->smarty->assign('ruleList', $ruleList);
        $this->smarty->assign('customizedRuleList', $customizedRuleList);

		// i need do this because js don't want work with empty value(company level)
		if (!isset($cfd['facilityID'])) {
			$cfd['facilityID'] = "false";
		}
        $this->smarty->assign('cfd', $cfd);
        $this->smarty->assign('userID', $_SESSION['user_id']);
        $emailNotifications = new EmailNotifications($this->db);
        $this->smarty->assign('notificationsList', $emailNotifications->getAllLimits());
        $this->smarty->assign('notificationsListSelected', $emailNotifications->getLimitsListByUser($_SESSION['user_id']));

        if (isset($request['bookmark'])) {
            $backUrl = "?action=browseCategory&category=" . $this->getFromRequest('category') . "&id=" . $this->getFromRequest('id') . "&bookmark=" . $this->getFromRequest('bookmark');
            switch ($this->getFromRequest('bookmark')) {
                case 'inventory':
                    $backUrl .= "&tab=material";
                    break;
                case 'solventplan':
                case 'carbonfootprint':
                    $backUrl .= "&tab=month";
                    break;
                case 'wastestorage':
                    $backUrl .= "&tab=active";
                    break;
            }
        } elseif ($this->getFromRequest('category') == 'company') {
            $backUrl = "?action=browseCategory&category=" . $this->getFromRequest('category') . "&id=" . $this->getFromRequest('id');
        } else {
            $backUrl = "?action=viewDetails&category=" . $this->getFromRequest('category') . "&id=" . $this->getFromRequest('id');
        }
        $this->smarty->assign('backUrl', $backUrl);

        $this->smarty->display("tpls:settings.tpl");
    }

    private function actionConfirmDeleteCommon() {
        if ($this->getFromPost('confirm') != 'Yes')
            throw new Exception('404');

        $this->smarty->assign("accessname", $_SESSION["username"]);
        $itemsCount = $this->getFromPost('itemsCount');
        for ($i = 0; $i < $itemsCount; $i++) {
            if (!is_null($this->getFromPost('item_' . $i))) {
                $itemID[] = $this->getFromPost('item_' . $i);
            }
        }
        $this->overCategoryType = $this->getFromPost('itemID');

        //we will need this var in future, dont delete it:
        $this->successDeleteInventories = true;
        $this->itemID = $itemID;
    }

    private function actionDeleteItemCommon() {

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($this->getFromRequest());
        $this->smarty->assign("request", $this->getFromRequest());
        $this->smarty->assign("accessname", $_SESSION["username"]);
    }

    protected function finalDeleteItemACommon($itemForDelete) {
        $this->smarty->assign('parent', $this->parent_category);

        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($this->getFromRequest());
        $this->smarty->assign("request", $this->getFromRequest());
        if (count($itemForDelete) == 1) {
            $this->smarty->assign('tpl', 'tpls/deleteCategory.tpl');
        } else {
            $this->smarty->assign('tpl', 'tpls/deleteCategories.tpl');
        }

        $this->smarty->assign("itemForDelete", $itemForDelete);
        $this->smarty->assign("itemsCount", count($itemForDelete));

        $this->smarty->display("tpls:index.tpl");
    }

    protected function finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info) {
        $notify = new Notify($this->smarty);
        switch (count($itemForDelete)) {
            case 0:
                $notify->notSelected($this->getFromRequest('category'));
                $this->smarty->assign("tpl", "tpls/deleteCategories.tpl");
                break;
            case 1:
                $this->smarty->assign("tpl", "tpls/deleteCategory.tpl");
                break;
            default:
                $notify->warnDelete($this->getFromRequest('category'), "", $linkedNotify, $count, $info);
                $this->smarty->assign("tpl", "tpls/deleteCategories.tpl");
                break;
        }
        $this->smarty->assign("itemForDelete", $itemForDelete);
        $this->smarty->assign("itemType", $this->getFromRequest('category'));
        $this->smarty->assign("itemsCount", count($itemForDelete));
        $this->smarty->display("tpls:index.tpl");
    }

    private function actionViewDetailsCommon() {
        $title = new TitlesNew($this->smarty, $this->db);
        $title->getTitle($this->request);
        $this->smarty->assign("request", $this->request);
        $this->smarty->assign("accessname", $_SESSION["username"]);
    }

    private function actionViewDetailsACommon() {
        $this->smarty->assign('parent', $this->parent_category);
        $this->smarty->assign('request', $this->getFromRequest());
    }

    private function actionBrowseCategoryCommon() {
        $paramsForListLeft = array();
        if (!is_null($this->getFromRequest('bookmark'))) {
            $paramsForListLeft ['bookmark'] = $this->getFromRequest('bookmark');
        }
        if (!is_null($this->getFromRequest('tab'))) {
            $paramsForListLeft ['tab'] = $this->getFromRequest('tab');
        }

        $this->setListCategoriesLeftNew($this->getFromRequest('category'),
				$this->getFromRequest('id'), $paramsForListLeft); //TODO add in all Controls paramsForListLeft!
        $this->setNavigationUpNew($this->getFromRequest('category'),
				$this->getFromRequest('id'));
        $this->setPermissionsNew($this->getFromRequest('category'));

        $this->smarty->assign('accessname', $_SESSION['username']);
        $this->smarty->assign('request', $this->request);

        //	Access control
		if (!$this->user->checkAccess($this->getFromRequest('category'),
				$this->getFromRequest('id'))) {
            throw new Exception('deny');
        }       

		//TODO: create getAllLabels();
		// set label List
        if ($this->getFromRequest('category') == 'facility'
				|| $this->getFromRequest('category') == 'department') {
			
            $companyLevelLabel = new CompanyLevelLabel($this->db);
            $companyLevelLabelRepairOrder = $companyLevelLabel->getRepairOrderLabel();
            $companyLevelLabelPaintShopProduct = $companyLevelLabel->getPaintShopProductLabel();
            $companyLevelLabelBodyShopProduct = $companyLevelLabel->getBodyShopProductLabel();
            $companyLevelLabelDetailingShopProduct = $companyLevelLabel->getDetailingShopProductLabel();
            $companyLevelLabelFuelAndOilsProduct = $companyLevelLabel->getFuelAndOilProductLabel();
            $companyLevelLabelPowderCoating = $companyLevelLabel->getPowderCoating();

			
            //$facility = new Facility($this->db);
            if ($this->getFromRequest('category') == 'facility') { //repair order label on facility level
                $facility = new VWM\Hierarchy\Facility($this->db,$this->getFromRequest('id'));
            } else {
                $department = new VWM\Hierarchy\Department($this->db, $this->getFromRequest('id'));
				$facility = $department->getFacility();
            }
            
            $company = $facility->getCompany();
            $repairOrderLabel = $company->getIndustryType()->getLabelManager()
					->getLabel($companyLevelLabelRepairOrder->label_id)
					->getLabelText();
            $paintShopProductLabel = $company->getIndustryType()
					->getLabelManager()
					->getLabel($companyLevelLabelPaintShopProduct->label_id)
					->getLabelText();
            $bodyShopProductLabel = $company->getIndustryType()
					->getLabelManager()
					->getLabel($companyLevelLabelBodyShopProduct->label_id)
					->getLabelText();
            $detailingShopProductLabel = $company->getIndustryType()
					->getLabelManager()
					->getLabel($companyLevelLabelDetailingShopProduct->label_id)
					->getLabelText();
            $fuelAndOilsProductLabel = $company->getIndustryType()
					->getLabelManager()
					->getLabel($companyLevelLabelFuelAndOilsProduct->label_id)
					->getLabelText();
            $powderCoatingLabel = $company->getIndustryType()
					->getLabelManager()
					->getLabel($companyLevelLabelPowderCoating->label_id)
					->getLabelText();
			
            $this->smarty->assign('repairOrderLabel', $repairOrderLabel);
            $this->smarty->assign('paintShopProductLabel', $paintShopProductLabel);
            $this->smarty->assign('bodyShopProductLabel', $bodyShopProductLabel);
            $this->smarty->assign('detailingShopProductLabel', $detailingShopProductLabel);
            $this->smarty->assign('fuelAndOilsProductLabel', $fuelAndOilsProductLabel);
            $this->smarty->assign('powderCoatingLabel', $powderCoatingLabel);
        }

    }

    private function actionBrowseCategoryACommon() {
        $this->smarty->assign('parent', $this->parent_category);
        $this->smarty->assign('request', $this->getFromRequest());
    }

	private function actionBrowseCategorySCommon() {
        $this->smarty->assign('parent', $this->parent_category);
        $this->smarty->assign('request', $this->getFromRequest());
    }

    private function actionAddItemCommon() {
        $title = new TitlesNew($this->smarty, $this->db);
        $request = $_GET;
        $request['parent_category'] = $this->parent_category;
        $request['parent_id'] = $this->getFromRequest($this->parent_category . 'ID');
        $title->getTitle($request);
        $this->smarty->assign('request', $request);
        $this->smarty->assign("accessname", $_SESSION["username"]);
    }

    private function actionAddItemACommon() {
        $this->smarty->assign("request", $this->getFromRequest());
        $this->smarty->assign('parent', $this->parent_category);
    }


    private function actionEditCommon() {
        $title = new TitlesNew($this->smarty, $this->db);
        $request = $_GET;
        $title->getTitle($request);

        $this->smarty->assign('request', $request);
        $this->smarty->assign("accessname", $_SESSION["username"]);
    }

    private function actionEditACommon() {
        $this->smarty->assign("request", $this->getFromRequest());
        $this->smarty->assign('parent', $this->parent_category);
    }

    //	voc indicator
    protected function setIndicator($vocLimit, $totalUsage) {
        $this->smarty->assign('vocLimit', $vocLimit);
        $this->smarty->assign('currentUsage', round($totalUsage, 2));
        $pxCount = round(200 * $totalUsage / $vocLimit);
        if ($pxCount > 200) {
            $pxCount = 200;
        }




        $this->smarty->assign('pxCount', $pxCount); //	200px - indicator length
    }

    protected function convertSearchItemsToArray($query) {
        $firstStep = explode(',', $query);
        foreach ($firstStep as $item) {
            $secondStep = explode(';', $item);
            foreach ($secondStep as $finalItem) {
                $finalItems[] = trim($finalItem);
            }
        }
        return $finalItems;
    }

    protected function setListCategoriesLeftNew($category, $id, $params = null) {
        $tail = '';
        if (!is_null($params)) {
            foreach ($params as $key => $value) {
                $tail .= "&$key=$value";
            }
        }
        switch ($category) {
            case "company":
                $companyObj = new Company($this->db);
                $companyList = $companyObj->getCompanyList();
                foreach ($companyList as $key => $company) {
                    $url = "?action=browseCategory&category=company&id=" . $company['id'] . $tail;
                    $companyList[$key]['url'] = $url;
                }
                $this->smarty->assign("upCategory", $companyList);
                $this->smarty->assign("upCategoryName", LABEL_LEFT_COMPANIES_TITLE);
                break;
            case "carbonfootprint":
            case "facility":
                $facility = new Facility($this->db);
                $facilityDetails = $facility->getFacilityDetails($id);
                $facilityList = $facility->getFacilityListByCompany($facilityDetails['company_id']);
                for ($i = 0; $i < count($facilityList); $i++) {
                    $url = "?action=browseCategory&category=facility&id=" . $facilityList[$i]['id'] . (($tail == '') ? "&bookmark=department" : $tail);
                    $facilityList[$i]['url'] = $url;
                }
                $this->smarty->assign("upCategory", $facilityList);
                $this->smarty->assign("upCategoryName", LABEL_LEFT_FACILITIES_TITLE);
                break;
            case "department":

				$departments = new Department($this->db);

				$moreThanOneDepartmentAssigned = false;
				$groups = VOCApp::getInstance()
					->getAccessControl()
					->getUserGroups($_SESSION['accessname']);
				// standart is one for level (Department Level)
				// and second for id (department_777)
				$departmentList = array();
				if(count($groups) > 2) {
					$moreThanOneDepartmentAssigned = true;
					foreach ($groups as $group) {
						$groupArray = explode('_', $group);
						if($groupArray[0] == 'department') {
							$departmentDetails = $departments->getDepartmentDetails($groupArray[1]);
							$departmentList[] = array(
								'id'=> $departmentDetails['department_id'],
								'name'=>$departmentDetails['name']
							);
						}
					}
				} elseif($this->xnyo->user['accesslevel_id'] != 2) {
					$departmentDetails = $departments->getDepartmentDetails($id);
					$departmentList = $departments->getDepartmentListByFacility($departmentDetails['facility_id']);
				}

                for ($i = 0; $i < count($departmentList); $i++) {
                    $url = "?action=browseCategory&category=department&id=" . $departmentList[$i]['id'] . (($tail == '') ? "&bookmark=mix" : $tail);
                    $departmentList[$i]['url'] = $url;
                }
                $this->smarty->assign("upCategory", $departmentList);
                $this->smarty->assign("upCategoryName", LABEL_LEFT_DEPARTMENTS_TITLE);
                break;
            case "sales":
                $inventoryManager = new InventoryManager($this->db);
                $jobberDetails = $inventoryManager->getJobberDetails($id);
                $jobberList = $inventoryManager->getJobberList();

                for ($i = 0; $i < count($jobberList); $i++) {
					$supplierIDS = $inventoryManager->getSuppliersByJobberID($jobberList[$i]['jobber_id']);
                    $url = "?action=browseCategory&category=sales&bookmark=clients&jobberID={$jobberList[$i]['jobber_id']}&supplierID={$supplierIDS[0]['supplier_id']}";
                    $jobberList[$i]['url'] = $url;
					$jobberList[$i]['id'] = $jobberList[$i]['jobber_id'];

                }
                $this->smarty->assign("upCategory", $jobberList);
                $this->smarty->assign("upCategoryName", LABEL_LEFT_DEPARTMENTS_TITLE);
                break;
        }
        $this->smarty->assign("leftCategoryID", $id);
    }

    protected function setNavigationUpNew($category, $id) {
        switch ($category) {
            case "root":
                $this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
                break;
            case "sales":
                $this->smarty->assign('urlRoot', '?action=browseCategory&category=root');

                $company = new Company($this->db);
                $companyDetails = $company->getCompanyDetails($id);

                $this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=" . $id);
                $this->smarty->assign('companyName', $companyDetails['name']);
                $this->smarty->assign('address', $companyDetails['address']);
                $this->smarty->assign('contact', $companyDetails['contact']);
                $this->smarty->assign('phone', $companyDetails['phone']);
                break;
            case "company":
                $this->smarty->assign('urlRoot', '?action=browseCategory&category=root');

                $company = new Company($this->db);
                $companyDetails = $company->getCompanyDetails($id);

                $this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=" . $id);
                $this->smarty->assign('companyName', $companyDetails['name']);
                $this->smarty->assign('address', $companyDetails['address']);
                $this->smarty->assign('contact', $companyDetails['contact']);
                $this->smarty->assign('phone', $companyDetails['phone']);
                break;
            case "facility":
                $facility = new Facility($this->db);
                $facilityDetails = $facility->getFacilityDetails($id);

                $company = new Company($this->db);
                $companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

                $this->smarty->assign("companyName", $companyDetails['name']);
                $this->smarty->assign("facilityName", $facilityDetails['name']);
                $this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
                $this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=" . $facilityDetails['company_id']);
                $this->smarty->assign('urlFacility', "?action=browseCategory&category=facility&id=" . $id . "&bookmark=department");

                $this->smarty->assign('address', $facilityDetails['address']);
                $this->smarty->assign('contact', $facilityDetails['contact']);
                $this->smarty->assign('phone', $facilityDetails['phone']);
                break;
            case "department":
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($id);

                $facility = new Facility($this->db);
                $facilityDetails = $facility->getFacilityDetails($departmentDetails['facility_id']);

                $company = new Company($this->db);
                $companyDetails = $company->getCompanyDetails($facilityDetails['company_id']);

                $this->smarty->assign("departmentName", $departmentDetails['name']);
                $this->smarty->assign("facilityName", $facilityDetails['name']);
                $this->smarty->assign("companyName", $companyDetails['name']);
                $this->smarty->assign('urlRoot', '?action=browseCategory&category=root');
                $this->smarty->assign('urlCompany', "?action=browseCategory&category=company&id=" . $facilityDetails['company_id']);
                $this->smarty->assign('urlFacility', "?action=browseCategory&category=facility&id=" . $departmentDetails['facility_id'] . "&bookmark=department");
                $this->smarty->assign('urlDepartment', "?action=browseCategory&category=department&id=" . $id . "&bookmark=mix");
                break;
        }
    }

    protected function setPermissionsNew($category) {
		
        switch ($category) {
            case "root":
                $permissions['viewItem'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['addItem'] = $this->user->isHaveAccessTo('add', 'company') ? true : false;
                $permissions['deleteItem'] = $this->user->isHaveAccessTo('delete', 'company') ? true : false;
                if ($permissions['deleteItem'] == true || $permissions['addItem'] == true) {
                    $permissions['showSelectAll'] = true;
                }
                break;
            case "company":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['viewCategory'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['deleteCategory'] = $this->user->isHaveAccessTo('delete', 'company') ? true : false;
                $permissions['viewItem'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['addItem'] = $this->user->isHaveAccessTo('add', 'facility') ? true : false;
                $permissions['deleteItem'] = $this->user->isHaveAccessTo('delete', 'facility') ? true : false;
                if ($permissions['deleteItem'] == true || $permissions['addItem'] == true) {
                    $permissions['showSelectAll'] = true;
                }
                break;

            case "facility":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['department']['view'] = $this->user->isHaveAccessTo('view', 'department') ? true : false;
                $permissions['department']['edit'] = $this->user->isHaveAccessTo('edit', 'department') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['viewCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['viewItem'] = $this->user->isHaveAccessTo('view', 'department') ? true : false;
                $permissions['addItem'] = $this->user->isHaveAccessTo('add', 'department') ? true : false;
                $permissions['deleteItem'] = $this->user->isHaveAccessTo('delete', 'department') ? true : false;
                $permissions['deleteCategory'] = $this->user->isHaveAccessTo('delete', 'facility') ? true : false;
                if ($permissions['deleteItem'] == true || $permissions['addItem'] == true) {
                    $permissions['showSelectAll'] = true;
                }

                $permissions['data']['view'] = $this->user->isHaveAccessTo('view', 'data') ? true : false;
                $permissions['data']['edit'] = $this->user->isHaveAccessTo('edit', 'data') ? true : false;
                $permissions['data']['add'] = $this->user->isHaveAccessTo('add', 'data') ? true : false;
                $permissions['data']['delete'] = $this->user->isHaveAccessTo('delete', 'data') ? true : false;
                if ($permissions['data']['delete'] == true || $permissions['data']['add'] == true) {
                    $permissions['data']['showSelectAll'] = true;
                }

                break;

            case "department":

                $permissions['showOverCategory'] = true;//$this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['department']['view'] = $this->user->isHaveAccessTo('view', 'department') ? true : false;
                $permissions['deleteCategory'] = $this->user->isHaveAccessTo('delete', 'department') ? true : false;
                $permissions['viewCategory'] = $this->user->isHaveAccessTo('view', 'department') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;

                $permissions['equipment']['view'] = $this->user->isHaveAccessTo('view', 'equipment') ? true : false;
                $permissions['equipment']['edit'] = $this->user->isHaveAccessTo('edit', 'equipment') ? true : false;
                $permissions['equipment']['add'] = $this->user->isHaveAccessTo('add', 'equipment') ? true : false;
                $permissions['equipment']['delete'] = $this->user->isHaveAccessTo('delete', 'equipment') ? true : false;
                if ($permissions['equipment']['delete'] == true || $permissions['equipment']['add'] == true) {
                    $permissions['equipment']['showSelectAll'] = true;
                }

                $permissions['user']['view'] = $this->user->isHaveAccessTo('view', 'user') ? true : false;
                $permissions['user']['edit'] = $this->user->isHaveAccessTo('edit', 'user') ? true : false;
                $permissions['user']['add'] = $this->user->isHaveAccessTo('add', 'user') ? true : false;
                $permissions['user']['delete'] = $this->user->isHaveAccessTo('delete', 'user') ? true : false;
                if ($permissions['user']['delete'] == true || $permissions['user']['add'] == true) {
                    $permissions['user']['showSelectAll'] = true;
                }

                $permissions['data']['view'] = $this->user->isHaveAccessTo('view', 'data') ? true : false;
                $permissions['data']['edit'] = $this->user->isHaveAccessTo('edit', 'data') ? true : false;
                $permissions['data']['add'] = $this->user->isHaveAccessTo('add', 'data') ? true : false;
                $permissions['data']['delete'] = $this->user->isHaveAccessTo('delete', 'data') ? true : false;
                if ($permissions['data']['delete'] == true || $permissions['data']['add'] == true) {
                    $permissions['data']['showSelectAll'] = true;
                }
                break;
            //-----------------------------------------------------------------------------------
            case "insideDepartment":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['department']['view'] = $this->user->isHaveAccessTo('view', 'department') ? true : false;
                $permissions['deleteCategory'] = $this->user->isHaveAccessTo('delete', 'department') ? true : false;
                $permissions['viewCategory'] = $this->user->isHaveAccessTo('view', 'department') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;

                $permissions['equipment']['view'] = $this->user->isHaveAccessTo('view', 'equipment') ? true : false;
                $permissions['equipment']['edit'] = $this->user->isHaveAccessTo('edit', 'equipment') ? true : false;
                $permissions['equipment']['add'] = $this->user->isHaveAccessTo('add', 'equipment') ? true : false;
                $permissions['equipment']['delete'] = $this->user->isHaveAccessTo('delete', 'equipment') ? true : false;
                if ($permissions['equipment']['delete'] == true || $permissions['equipment']['add'] == true) {
                    $permissions['equipment']['showSelectAll'] = true;
                }

                $permissions['user']['view'] = $this->user->isHaveAccessTo('view', 'user') ? true : false;
                $permissions['user']['edit'] = $this->user->isHaveAccessTo('edit', 'user') ? true : false;
                $permissions['user']['add'] = $this->user->isHaveAccessTo('add', 'user') ? true : false;
                $permissions['user']['delete'] = $this->user->isHaveAccessTo('delete', 'user') ? true : false;
                if ($permissions['user']['delete'] == true || $permissions['user']['add'] == true) {
                    $permissions['user']['showSelectAll'] = true;
                }

                $permissions['data']['view'] = $this->user->isHaveAccessTo('view', 'data') ? true : false;
                $permissions['data']['edit'] = $this->user->isHaveAccessTo('edit', 'data') ? true : false;
                $permissions['data']['add'] = $this->user->isHaveAccessTo('add', 'data') ? true : false;
                $permissions['data']['delete'] = $this->user->isHaveAccessTo('delete', 'data') ? true : false;
                if ($permissions['data']['delete'] == true || $permissions['data']['add'] == true) {
                    $permissions['data']['showSelectAll'] = true;
                }

                break;
            case "sales":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['viewCategory'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['deleteCategory'] = $this->user->isHaveAccessTo('delete', 'company') ? true : false;
                $permissions['viewItem'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['addItem'] = $this->user->isHaveAccessTo('add', 'facility') ? true : false;
                $permissions['deleteItem'] = $this->user->isHaveAccessTo('delete', 'facility') ? true : false;
                if ($permissions['deleteItem'] == true || $permissions['addItem'] == true) {
                    $permissions['showSelectAll'] = true;
                }
                break;
            case "viewRoot":
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                break;
            case "viewCompany":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['edit'] = $this->user->isHaveAccessTo('edit', 'company') ? true : false;
                $permissions['company']['delete'] = $this->user->isHaveAccessTo('delete', 'company') ? true : false;
                break;
            case "viewFacility":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['facility']['edit'] = $this->user->isHaveAccessTo('edit', 'facility') ? true : false;
                $permissions['facility']['delete'] = $this->user->isHaveAccessTo('delete', 'facility') ? true : false;
                break;
            case "viewDepartment":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['department']['edit'] = $this->user->isHaveAccessTo('edit', 'department') ? true : false;
                $permissions['department']['delete'] = $this->user->isHaveAccessTo('delete', 'department') ? true : false;
                break;
            case "viewEquipment":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['equipment']['edit'] = $this->user->isHaveAccessTo('edit', 'equipment') ? true : false;
                $permissions['equipment']['delete'] = $this->user->isHaveAccessTo('delete', 'equipment') ? true : false;
                break;
            case "viewUser":
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['user']['edit'] = $this->user->isHaveAccessTo('edit', 'user') ? true : false;
                $permissions['user']['delete'] = $this->user->isHaveAccessTo('delete', 'user') ? true : false;
                break;
            case "viewData":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['data']['edit'] = $this->user->isHaveAccessTo('edit', 'data') ? true : false;
                $permissions['data']['delete'] = $this->user->isHaveAccessTo('delete', 'data') ? true : false;
                break;
            case "viewInsideDepartment":
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['facility']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                break;

			case "viewRepairOrder":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['repairOrder']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['repairOrder']['edit'] = $this->user->isHaveAccessTo('edit', 'repairOrder') ? true : false;
                $permissions['repairOrder']['delete'] = $this->user->isHaveAccessTo('delete', 'repairOrder') ? true : false;
                break;

			case "viewReminder":
                $permissions['showOverCategory'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['root']['view'] = $this->user->isHaveAccessTo('view', 'root') ? true : false;
                $permissions['company']['view'] = $this->user->isHaveAccessTo('view', 'company') ? true : false;
                $permissions['reminder']['view'] = $this->user->isHaveAccessTo('view', 'facility') ? true : false;
                $permissions['reminder']['edit'] = $this->user->isHaveAccessTo('edit', 'reminder') ? true : false;
                $permissions['reminder']['delete'] = $this->user->isHaveAccessTo('delete', 'reminder') ? true : false;
                break;
        }
        $this->smarty->assign('permissions', $permissions);
		return $permissions;
    }

    function noname($request=null) {
        if ($request == null)
            $request = $this->request;
        switch ($request['category']) {
            case 'company':
                $companyID = $request['id'];
                $facilityID = null;
                $departmentID = null;
                $bookmark = null;
                //	set permissions
                $this->setListCategoriesLeftNew($request['category'], $request['id']);
                $this->setNavigationUpNew($request['category'], $request['id']);
                $this->setPermissionsNew($request['category']);
                $this->smarty->assign('categoryName', 'company');
                break;
            case 'facility':
                $facility = new Facility($this->db);
                $facilityDetails = $facility->getFacilityDetails($request['id']);
                $companyID = $facilityDetails['company_id'];
                $facilityID = $request['id'];
                $departmentID = null;
                $bookmark = 'department';
                //	set permissions
                $this->setListCategoriesLeftNew($request['category'], $request['id']);
                $this->setNavigationUpNew($request['category'], $request['id']);
                $this->setPermissionsNew($request['category']);
                $this->smarty->assign('categoryName', 'facility');
                break;
            case 'department':
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($request['id']);
                $company = new Company($this->db);
                $companyID = $company->getCompanyIDbyDepartmentID($request['id']);
                $facilityID = $departmentDetails['facility_id'];
                $departmentID = $request['id'];
                $bookmark = 'mix';
                //	set permissions
                $this->setListCategoriesLeftNew($request['category'], $request['id']);
                $this->setNavigationUpNew($request['category'], $request['id']);
                $this->setPermissionsNew($request['category']);
                $this->smarty->assign('categoryName', 'department');
                break;
            case 'mix':
                $mix = new Mix($this->db);
                $mixDetails = $mix->getMixDetails($request['id']);
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($mixDetails['department_id']);
                $company = new Company($this->db);
                $companyID = $company->getCompanyIDbyDepartmentID($mixDetails['department_id']);
                $facilityID = $departmentDetails['facility_id'];
                $departmentID = $mixDetails['department_id'];
                $bookmark = 'mix';
                //	set permissions
                $this->setListCategoriesLeftNew('department', $departmentID);
                $this->setNavigationUpNew('department', $departmentID);
                $this->setPermissionsNew('department');
                $this->smarty->assign('categoryName', 'department');
                break;
            case 'equipment':
                $equipment = new Equipment($this->db);
                $equipmentDetails = $equipment->getEquipmentDetails($request['id'], true);
                $department = new Department($this->db);
                $departmentDetails = $department->getDepartmentDetails($equipmentDetails['department_id']);
                $company = new Company($this->db);
                $companyID = $company->getCompanyIDbyDepartmentID($equipmentDetails['department_id']);
                $facilityID = $departmentDetails['facility_id'];
                $departmentID = $equipmentDetails['department_id'];
                $bookmark = 'equipment';
                $this->setListCategoriesLeftNew('department', $departmentID);
                $this->setNavigationUpNew('department', $departmentID);
                $this->setPermissionsNew('department');
                $this->smarty->assign('categoryName', 'department');
                break;
        }
        return array(
            'companyID' => $companyID,
            'facilityID' => $facilityID,
            'departmentID' => $departmentID,
            'bookmark' => $bookmark
        );
    }

    protected function getFromRequest($key = null) {
        if (isset($key)) {
            if (isset($this->request[$key]))
                return $this->request[$key];
            else
                return null;
        }
        else
            return $this->request;
    }

    protected function getFromPost($key = null) {
        if (isset($key)) {
            if (isset($this->post[$key]))
                return $this->post[$key];
            else
                return null;
        }
        else
            return $this->post;
    }

    function setBookmarks($category) {
        switch ($category) {
            case "invoices":

                $subCategoryList = array('All', 'Paid', 'Canceled', 'Due');
                foreach ($subCategoryList as $subCategoryName) {
                    $bookmark['label'] = $subCategoryName;
                    $bookmark['name'] = $subCategoryName;
                    $bookmark['url'] = "vps.php?action=viewList&category=invoices&subCategory=" . $subCategoryName;
                    $bookmarks[] = $bookmark;
                }
                $this->smarty->assign("bookmark", $bookmarks);

                break;

            case "billing":

                $subCategoryList = array('My Billing Plan', 'Available Billing Plans');
                foreach ($subCategoryList as $subCategoryName) {
                    $bookmark['label'] = $subCategoryName;
                    $bookmark['name'] = str_replace(" ", "", $subCategoryName);
                    $bookmark['url'] = "vps.php?action=viewDetails&category=billing&subCategory=" . $bookmark['name'];
                    $bookmarks[] = $bookmark;
                }
                $this->smarty->assign("bookmark", $bookmarks);

                break;
        }
    }


	/**
	 * Insert tpl block into parent template
	 * @param string $path Example "tpls/productTypesDropDown.tpl"
	 * @param string $whereToInsert Example Controller::INSERT_AFTER_SEARCH
	 * @return boolean true on success, false on failure (no such $whereToInsert)
	 */
	public function insertTplBlock($path, $whereToInsert) {
		if (array_key_exists($whereToInsert, $this->blocksToInsert)) {
			array_push($this->blocksToInsert[$whereToInsert], $path);

			$this->smarty->assign('blocksToInsert', $this->blocksToInsert); 

			return true;
		} else {
			//	no such $whereToInsert
			return false;
		}

	}

	 //	nox indicator
    protected function setNoxIndicator($noxLimit, $totalUsage, $noxPeriod) {
        $this->smarty->assign('noxLimit', $noxLimit);
	//	$this->smarty->assign('noxLog', 'true');
        $this->smarty->assign('noxCurrentUsage', round($totalUsage, 2));
        $pxNoxCount = round(200 * $totalUsage / $noxLimit);
        if ($pxNoxCount > 200) {
            $pxNoxCount = 200;
        }
		$this->smarty->assign('noxPeriod', $noxPeriod);
        $this->smarty->assign('noxPxCount', $pxNoxCount); //	200px - indicator length
    }

	protected function render() {
		$this->smarty->display("tpls:index.tpl");
	}
    
    //	product QTY indicator
    protected function setQtyProductIndicator($limit, $currenProductQty, $qtyPeriod) {
        $this->smarty->assign('qtyProductLimit', $limit);
        $this->smarty->assign('currenProductQty', round($currenProductQty, 2));
        $pxQtyProductCount = round(200 * $currenProductQty / $limit); 
        if ($pxQtyProductCount > 200) {
            $pxQtyProductCount = 200;
        }
		$this->smarty->assign('qtyPeriod', $qtyPeriod);
        $this->smarty->assign('pxQtyProductCount', $pxQtyProductCount); //	200px - indicator length
    }
	
	 protected function setTimeProductIndicator($limit, $currenProductTime, $timePeriod) {
		 
        $this->smarty->assign('timeProductLimit', $limit);
        //$this->smarty->assign('currenProductTime', round($currenProductTime, 2));
		$this->smarty->assign('currenProductTime', round($currenProductTime, 3));
        $timeProductCount = round(200 * $currenProductTime / $limit); 
        if ($timeProductCount > 200) {
            $timeProductCount = 200;
        }
		$this->smarty->assign('timePeriod', $timePeriod);
        $this->smarty->assign('timeProductCount', $timeProductCount); //	200px - indicator length
    }
}

?>