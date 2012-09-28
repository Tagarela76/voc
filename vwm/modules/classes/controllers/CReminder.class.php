<?php

class CReminder extends Controller {

    function CReminder($smarty, $xnyo, $db, $user, $action) {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'reminder';
        $this->parent_category = 'facility';
    }

    /**
     * bookmarkReminder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkReminder($vars) {

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
                $url = "?action=viewDetails&category=reminder&id=" . $remindersList[$i]->id . "&facilityID=" . $facilityDetails['facility_id'];				
                $remindersList[$i]->url = $url;
            }
        }
		
        $this->smarty->assign("childCategoryItems", $remindersList);
		
        //	set js scripts
        $jsSources = array(
            'modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js',
            'modules/js/checkBoxes.js',
			'modules/js/autocomplete/jquery.autocomplete.js');
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);

        //	set tpl
        $this->smarty->assign('tpl', 'tpls/remindersList.tpl');
    }
	
	protected function actionAddItem() {
        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }
		$user = new User($this->db);
        $request = $this->getFromRequest();
        $request["id"] = $request["facilityID"];
        $request['parent_id'] = $request['facilityID'];
        $request['parent_category'] = 'facility';
        $this->smarty->assign('request', $request);

        $params = array("bookmark" => "reminder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setNavigationUpNew('facility', $this->getFromRequest("facilityID"));
        $this->setPermissionsNew('viewFacility');
		
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyID = $facilityDetails["company_id"];
		$this->smarty->assign('dataChain', new TypeChain(null, 'date', $this->db, $companyID, 'company'));
		$post = $this->getFromPost();
		$facilityId = $request['facilityID'];
		$usersList = $user->getUserListByFacility($facilityId);
		$usersName = array();
		$user_id = array();
		foreach ($usersList as $user) {
			$usersName[] = $user["username"];
			$user_id[] = $user["user_id"];
		} 
		$usersList = implode(",", $usersName);
		$this->smarty->assign('usersList', $usersList);
		$this->smarty->assign('user_id', $user_id);
		
		if (count($post) > 0) {
			$user = new User($this->db);
			$facilityID = $post['facility_id'];
			$reminder = new Reminder($this->db);
			$reminder->name = $post['name'];
			$reminder->date = $post['date'];
			$reminder->facility_id = $facilityID; 
			$userList = $post['user_id'];
			$reminderUsers = array();
			$reminderUser = array();
			foreach ($userList as $userId) {
				$userDetails = $user->getUserDetails($userId);
				$reminderUser["user_id"] = $userId;
				$reminderUser["username"] = $userDetails["username"];
				$reminderUser["email"] = $userDetails["email"];
				$reminderUsers[] = $reminderUser;
			}
			$reminder->setValidationGroup("add");
			$reminder->setUsers($reminderUsers);
			VOCApp::get_instance()->setCustomerID($companyID);
			VOCApp::get_instance()->setDateFormat(NULL);

			$violationList = $reminder->validate(); 
			if(count($violationList) == 0) {		
				$dataChain = new TypeChain($reminder->date, 'date', $this->db, $companyID, 'company'); 
				$reminder->date = $dataChain->getTimestamp(); 
				$reminder->save();
				// set remind to users
				foreach ($userList as $userId) {
					$reminder->setRemind2User($userId);
				}
				// redirect
				header("Location: ?action=browseCategory&category=facility&id=" . $facilityID . "&bookmark=reminder&notify=51");
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$usersName = array();
				$user_id = array();
				foreach ($reminder->users as $user) {
					$usersName[] = $user["username"];
					$user_id[] = $user["user_id"];
				}
				$usersList = implode(",", $usersName);	
				$this->smarty->assign('usersList', $usersList); 
				$this->smarty->assign('user_id', $user_id); 
				$this->smarty->assign('data', $post);
			}																	
		}
        //	set js scripts
		$jsSources = array(
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/checkBoxes.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
			"modules/js/manageReminders.js"
		);
		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
		$this->smarty->assign('cssSources', $cssSources);
		$post = new stdClass();
		$post->facility_id = $facilityId;
		$post->id = 0;
		
		$this->smarty->assign('data', $post);
		$this->smarty->assign('request', $request);		
		$this->smarty->assign('sendFormAction', '?action=addItem&category=' . $request['category'] . '&facilityID=' . $request['facilityID']);
		$this->smarty->assign('pleaseWaitReason', "Recalculating reminders at Facility.");
		$this->smarty->assign('tpl', 'tpls/addReminder.tpl');
		$this->smarty->display("tpls:index.tpl");
    }
	
	protected function actionViewDetails() {

        $reminder = new Reminder($this->db, $this->getFromRequest('id'));
		
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "reminder");
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewReminder');

        $this->smarty->assign('backUrl', '?action=browseCategory&category=facility&id=' . $this->getFromRequest('facilityID') . '&bookmark=reminder');
        $this->smarty->assign('deleteUrl', '?action=deleteItem&category=reminder&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
        $this->smarty->assign('editUrl', '?action=edit&category=reminder&id=' . $this->getFromRequest('id') . '&facilityID=' . $this->getFromRequest("facilityID"));
		
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyID = $facilityDetails["company_id"];
		$dataChain = new TypeChain(date("y-m-d", $reminder->date), 'date', $this->db, $companyID, 'company');
		$reminder->date = $dataChain->formatOutput();
		$usersList = $reminder->getUsers();
		$usersName = array();
		foreach ($usersList as $user) {
			$usersName[] = $user["username"];
		}
		$usersList = implode(",", $usersName);
		$this->smarty->assign('reminder', $reminder);
		$this->smarty->assign("usersList", $usersList);
        //set js scripts
        $jsSources = array( "modules/js/checkBoxes.js",
						    "modules/js/autocomplete/jquery.autocomplete.js");
        $this->smarty->assign('jsSources', $jsSources);
		
		$cssSources = array("modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css",
							);
		$this->smarty->assign('cssSources', $cssSources);
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewReminder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }
	
	protected function actionDeleteItem() {

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
                $reminder = new Reminder($this->db, $reminderID);
                $delete = array();
                $delete["id"] = $reminder->id;
                $delete["name"] = $reminder->name;
                $delete["facility_id"] = $reminder->facility_id;
				$delete["date"] = $reminder->date;
                $itemForDelete[] = $delete;
            }
        }
        if (!is_null($this->getFromRequest('facilityID'))) {
            $this->smarty->assign("cancelUrl", "?action=browseCategory&category=facility&id=" . $this->getFromRequest('facilityID') . "&bookmark=reminder");
            //as ShowAddItem
            $params = array("bookmark" => "reminder");

            $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
            $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }

    protected function actionConfirmDelete() {

        foreach ($this->itemID as $ID) {

            $reminder = new Reminder($this->db, $ID);
            $facilityId = $reminder->facility_id;
            $reminder->delete();
        }
        header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=reminder&notify=52");
    }
	
	protected function actionEdit() {
		//var_dump(VOCApp::get_instance()->getCustomerID()); die();
		//	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }
		$facility = new Facility($this->db);
		$facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
		$companyID = $facilityDetails["company_id"];
		
        $reminder = new Reminder($this->db, $this->getFromRequest('id'));

        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "reminder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewReminder');

		$post = $this->getFromPost();

		if (count($post) > 0) {
			$facilityID = $post['facility_id'];
			$reminder = new Reminder($this->db, $post['id']);
			$reminder->name = $post['name'];
			$reminder->date = $post['date'];
			$userList = $post['user_id'];
			$reminderUsers = array();
			$reminderUser = array();
			$user = new User($this->db);
			foreach ($userList as $userId) {
				$userDetails = $user->getUserDetails($userId);
				$reminderUser["user_id"] = $userId;
				$reminderUser["username"] = $userDetails["username"];
				$reminderUser["email"] = $userDetails["email"];
				$reminderUsers[] = $reminderUser;
			}
			$reminder->setUsers($reminderUsers);
			
			VOCApp::get_instance()->setCustomerID($companyID);
			VOCApp::get_instance()->setDateFormat(NULL); 
			$violationList = $reminder->validate(); 
			if(count($violationList) == 0) { 
				$dataChain = new TypeChain($reminder->date, 'date', $this->db, $companyID, 'company');   
				$reminder->date = $dataChain->getTimestamp(); 
				$reminder->save(); 
				// unset all users from remind
				$reminder->unSetRemind2User();
				// set remind to users
				foreach ($userList as $userId) {
					$reminder->setRemind2User($userId);
				}
				// redirect
				header("Location: ?action=viewDetails&category=reminder&id=" . $reminder->id . "&facilityID=" . $facilityID . "&notify=53");
			} else {						
				$notifyc = new Notify(null, $this->db);
				$notify = $notifyc->getPopUpNotifyMessage(401);
				$this->smarty->assign("notify", $notify);						
				$this->smarty->assign('violationList', $violationList);
				$user_id = array();
				foreach ($reminder->users as $user) {
					$usersName[] = $user["username"];
					$user_id[] = $user["user_id"];
				}
				$usersList = implode(",", $usersName);
				$this->smarty->assign('usersList', $usersList);
				$this->smarty->assign('user_id', $user_id);
				$this->smarty->assign('data', $post);
			}																	
		}
		$jsSources = array(
			"modules/js/checkBoxes.js",
			"modules/js/autocomplete/jquery.autocomplete.js",
			"modules/js/jquery-ui-1.8.2.custom/js/jquery-ui-1.8.2.custom.min.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/numeric/jquery.numeric.js",
			"modules/js/jquery-ui-1.8.2.custom/jquery-plugins/timepicker/jquery-ui-timepicker-addon.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/external/jquery.bgiframe-2.1.1.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.core.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.widget.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.mouse.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.draggable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.position.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.resizable.js",
			"modules/js/jquery-ui-1.8.2.custom/development-bundle/ui/jquery.ui.dialog.js",
			"modules/js/manageReminders.js"
		);

		$this->smarty->assign('jsSources', $jsSources);

		$cssSources = array("modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css",
							);
		$this->smarty->assign('cssSources', $cssSources);

		$dataChain = new TypeChain(null, 'date', $this->db, $companyID, 'company');
		$this->smarty->assign('dataChain', $dataChain);
		$dataChain->setValue(date("y-m-d", $reminder->date));
		$reminder->date = $dataChain->formatOutput();
		
		$usersList = $reminder->getUsers();
		$usersName = array();
		$user_id = array();
		foreach ($usersList as $user) {
			$usersName[] = $user["username"];
			$user_id[] = $user["user_id"];
		}
		$usersList = implode(",", $usersName);
		$this->smarty->assign('usersList', $usersList);
		$this->smarty->assign('user_id', $user_id);
		$this->smarty->assign('data', $reminder);
		$this->smarty->assign('tpl', 'tpls/addReminder.tpl');
		$this->smarty->display("tpls:index.tpl");
	}
	
	protected function actionLoadUsers() {

		$usersList = array();
		$userItem = array();
		$reminderUsers = array();
		$facilityId = $this->getFromRequest('facilityId');
		$remindId = $this->getFromRequest('remindId');
		$reminder = new Reminder($this->db, $remindId);
		
		$user = new User($this->db);
		$users = $user->getUserListByFacility($facilityId);
		if ($remindId == 0) {
			$reminder2user = $users;
		} else {
			$reminder2user = $reminder->getUsers();
		}
		foreach ($reminder2user as $user) {
			$reminderUsers[] = $user["user_id"];
		}
		
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

        $this->smarty->assign('remindId', $remindId);
		$this->smarty->assign('facilityId', $facilityId);
		$this->smarty->assign('usersList', $usersList);
		echo $this->smarty->fetch('tpls/manageReminders.tpl');
    }
    
    protected function actionSetRemindToUser() {

        $rowsToSet = $this->getFromRequest('rowsToSet');
		$response = "";
		$user = new User($this->db);
		$usersName = array();
		foreach ($rowsToSet as $id) {
			$accessName = $user->getAccessnameByID($id);
			$usersName[] = $user->getUsernamebyAccessname($accessName);
			$response .= "<input type='hidden' name='user_id[]' value='$id' />";
		}
		$response .= implode(",", $usersName);

		echo $response;
				
    }

}

?>