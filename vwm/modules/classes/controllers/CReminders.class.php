<?php

class CReminders extends Controller {

    function CReminders($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'reminders';
        $this->parent_category = 'facility';
    }

    function runAction() {

        $this->runCommon();
        $functionName = 'action' . ucfirst($this->action);
        if (method_exists($this, $functionName))
            $this->$functionName();
    }

    /**
     * bookmarkReminders($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkReminders($vars) {

        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
            
        $facility = new Facility($this->db);
		$companyID = $facilityDetails["company_id"];
		
        $remindersCount = $facility->countRemindersInFacility($facilityDetails['facility_id']);
        $url = "?".$_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/","", $url);
        $pagination = new Pagination($remindersCount);
		$pagination->url = $url; 
        $this->smarty->assign('pagination', $pagination);
        
        $remindersList = $facility->getRemindersList($facilityDetails['facility_id'],$pagination);
        if ($remindersList) {
            for ($i = 0; $i < count($remindersList); $i++) { 
				$dataChain = new TypeChain(date("y-m-d", $remindersList[$i]->date), 'date', $this->db, $companyID, 'company');
				$remindersList[$i]->date = $dataChain->formatOutput();
                $url = "?action=viewDetails&category=reminders&id=" . $remindersList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'];				
                $remindersList[$i]->url = $url;
            }
        }
		
        $this->smarty->assign("childCategoryItems", $remindersList);
		
        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js',
			'modules/js/autocomplete/jq,uery.autocomplete.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);

        //	set tpl
        $this->smarty->assign('tpl', 'tpls/remindersList.tpl');
    }
	
	private function actionAddItem() {
        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }

        $request = $this->getFromRequest();
        $request["id"] = $request["facilityID"];
        $request['parent_id'] = $request['facilityID'];
        $request['parent_category'] = 'facility';
        $this->smarty->assign('request', $request);

        $params = array("bookmark" => "reminders");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
        $this->setPermissionsNew('viewFacility');
		
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyID = $facilityDetails["company_id"];
		$this->smarty->assign('dataChain', new TypeChain(null, 'date', $this->db, $companyID, 'company'));
		$post = $this->getFromPost();		

		if (count($post) > 0) {
			$facilityID = $post['facility_id'];
			$reminders = new Reminders($this->db);
			$reminders->name = $post['name'];
			$reminders->date = new DateTime($post['date']);
			$reminders->facility_id = $facilityID; 
			$reminders->setValidationGroup("add");
			$violationList = $reminders->validate(); 
			if(count($violationList) == 0) {		
				$dataChain = new TypeChain($post['date'], 'date', $this->db, $companyID, 'company'); 
				$reminders->date = $dataChain->getTimestamp(); 
				$reminders->save();
				// redirect
				header("Location: ?action=browseCategory&category=facility&id=" . $facilityID . "&bookmark=reminders&notify=51");
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $post);
			}																	
		}
        //	set js scripts
		$jsSources = array(
			'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js',
			'modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js'
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$this->smarty->assign('request', $request);
		$this->smarty->assign('sendFormAction', '?action=addItem&category=' . $request['category'] . '&facilityID=' . $request['facilityID']);
		$this->smarty->assign('pleaseWaitReason', "Recalculating reminders at Facility.");
		$this->smarty->assign('tpl', 'tpls/addReminders.tpl');
		$this->smarty->display("tpls:index.tpl");
    }
	
	private function actionViewDetails() {

        $reminders = new Reminders($this->db, $this->getFromRequest('id'));
		
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "reminders");
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewReminders');

        $this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest('facilityID') . '&bookmark=reminders');
        $this->smarty->assign('deleteUrl', '?action=deleteItem&category=reminders&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
        $this->smarty->assign('editUrl', '?action=edit&category=reminders&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
		
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyID = $facilityDetails["company_id"];
		$dataChain = new TypeChain(date("y-m-d", $reminders->date), 'date', $this->db, $companyID, 'company');
		$reminders->date = $dataChain->formatOutput();
		$usersList = $reminders->getUsers();
		$usersName = array();
		foreach ($usersList as $user) {
			$usersName[] = $user["username"];
		}
		$usersList = implode(",", $usersName);
		$this->smarty->assign('reminders', $reminders);
		$this->smarty->assign("usersList", $usersList);
        //set js scripts
        $jsSources = array( "modules/js/checkBoxes.js",
						    "modules/js/autocomplete/jquery.autocomplete.js",
						    "modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
							"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
						    "modules/js/manageReminders.js");
        $this->smarty->assign('jsSources', $jsSources);
		
		$cssSources = array("modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css",
							);
		$this->smarty->assign('cssSources', $cssSources);
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewReminders.tpl');
        $this->smarty->display("tpls:index.tpl");
    }
	
	private function actionDeleteItem() {

        $req_id = $this->getFromRequest('id');
        if (!is_array($req_id))
            $req_id = array($req_id);
        $itemForDelete = array();
        if (!is_null($this->getFromRequest('id'))) {
            foreach ($req_id as $reminderID) {
                //	Access control
                if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
                    throw new Exception('deny');
                }
                $reminders = new Reminders($this->db, $reminderID);
                $delete = array();
                $delete["id"] = $reminders->id;
                $delete["name"] = $reminders->name;
                $delete["facility_id"] = $reminders->facility_id;
				$delete["date"] = $repairOrder->date;
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=reminders");
            //as ShowAddItem
            $params = array("bookmark" => "reminders");

            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }

    private function actionConfirmDelete() {

        foreach ($this->itemID as $ID) {

            $reminders = new Reminders($this->db, $ID);
            $facilityId = $reminders->facility_id;
            $reminders->delete();
        }
        header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=reminders&notify=52");
    }
	
	private function actionEdit() {
		
		//	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyID = $facilityDetails["company_id"];
		
        $reminders = new Reminders($this->db, $this->getFromRequest('id'));

        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "reminders");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewReminders');

		$post = $this->getFromPost();

		if (count($post) > 0) {
			$facilityID = $post['facility_id'];
			$reminders = new Reminders($this->db, $post['id']);
			$reminders->name = $post['name'];
			$reminders->date = new DateTime($post['date']);
			$violationList = $reminders->validate(); 
			if(count($violationList) == 0) { 
				$dataChain = new TypeChain($post['date'], 'date', $this->db, $companyID, 'company');   
				$reminders->date = $dataChain->getTimestamp(); //var_dump($reminders->date); die();
				$reminders->save();
				// redirect
				header("Location: ?action=viewDetails&category=reminders&id=" . $reminders->id . "&facilityID=" . $facilityID . "&notify=53");
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$this->smarty->assign('data', $post);
			}																	
		}

		$jsSources = array(
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js"
		);

		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array("modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css",
							);
		$this->smarty->assign('cssSources', $cssSources);

		$dataChain = new TypeChain(null, 'date', $this->db, $companyID, 'company');
		$this->smarty->assign('dataChain', $dataChain);
		$dataChain->setValue(date("y-m-d", $reminders->date));
		$reminders->date = $dataChain->formatOutput();

		$this->smarty->assign('data', $reminders);
		$this->smarty->assign('tpl', 'tpls/addReminders.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	private function actionLoadUsers() {

		$usersList = array();
		$userItem = array();
		$reminderUsers = array();
		$facilityId = $this->getFromRequest('facilityId');
		$remindId = $this->getFromRequest('remindId');
		$reminders = new Reminders($this->db, $remindId);
		$reminder2user = $reminders->getUsers();
		foreach ($reminder2user as $user) {
			$reminderUsers[] = $user["user_id"];
		}
		$user = new User($this->db);
		$users = $user->getUserListByFacility($facilityId);
		foreach ($users as $user) {
			$userItem['id'] = $user["user_id"];
			$userItem['name'] = $user["username"];
			if (in_array($user["user_id"], $reminderUsers)) {
				$userItem['check'] = 1;
			} else {
				$userItem['check'] = 0;
			}
			$usersList[] = $userItem;
		}
//	var_dump($usersList); die();
        $this->smarty->assign('remindId', $remindId);
		$this->smarty->assign('facilityId', $facilityId);
		$this->smarty->assign('usersList', $usersList);
		echo $this->smarty->fetch('tpls/manageReminders.tpl');
    }
    
    private function actionManageRemindToUser() {

		$remindId = $this->getFromRequest('remindId');
        $rowsToSet = $this->getFromRequest('rowsToSet');
        $rowsToUnSet = $this->getFromRequest('rowsToUnSet');
     
		$reminders = new Reminders($this->db, $remindId);
		foreach ($rowsToUnSet as $row) {
			$reminders->unSetRemind2User($row);
		}
        foreach ($rowsToSet as $row) {
			$reminders->setRemind2User($row);
		}
		// init for new result
		$reminders = new Reminders($this->db, $remindId); 
		
		$usersList = $reminders->users;
		$usersName = array();
		foreach ($usersList as $user) {
			$usersName[] = $user["username"];
		}
		$usersList = implode(",", $usersName);
		
		if (count($reminders->users) != 0) {
			$response = "Users: " . $usersList;
		}
		echo $response;
				
    }

}

?>