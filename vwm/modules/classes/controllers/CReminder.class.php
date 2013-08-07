<?php

use VWM\Apps\Reminder\Entity\Reminder;
use VWM\Apps\UnitType\Entity\UnitType;
use VWM\Apps\Reminder\Entity\ReminderUser;
use VWM\Apps\Reminder\Manager\ReminderUserManager;

class CReminder extends Controller
{

    function CReminder($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'reminder';
        $this->parent_category = 'facility';
    }

    /**
     * bookmarkReminder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkReminder($vars)
    {
        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }

        $facility = new Facility($this->db);
        $companyID = $facilityDetails["company_id"];

        $remindersCount = $facility->countRemindersInFacility($facilityDetails['facility_id']);
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($remindersCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);

        $remindersList = $facility->getRemindersList($facilityDetails['facility_id'], $pagination);
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

    protected function actionAddItem()
    {
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

        $rManager = VOCApp::getInstance()->getService('reminder');
        $rUManager = VOCApp::getInstance()->getService('reminderUser');

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
        //get registered users
        $userReminderList = $rUManager->getReminderUserListByFacilityId($facilityId, 'registered');
        $usersName = array();
        $user_id = array();
        $registeredReminderUserIds = array();
        foreach ($userReminderList as $userReminder) {
            $userDetails = $user->getUserDetails($userReminder->getUserId());
            $usersName[] = $userDetails["username"];
            $user_id[] = $userReminder->getId();
            $registeredReminderUserIds[] = $userReminder->getId();
        }
        $registeredReminderUserIds = implode(',', $registeredReminderUserIds);
        $usersList = implode(",", $usersName);
        $this->smarty->assign('usersList', $usersList);
        $this->smarty->assign('user_id', $user_id);
        $this->smarty->assign('registeredReminderUserIds', $registeredReminderUserIds);

        $reminder = new Reminder();
        $reminder->setFacilityId($facilityId);

        //get reminder timing list
        $reminderTimingList = $rManager->getReminderTimingList();
        $this->smarty->assign('reminderTimingList', $reminderTimingList);

        $reminderTypeList = $rManager->getReminderTypeList();
        $this->smarty->assign('reminderTypeList', $reminderTypeList);

        if (count($post) > 0) {

            $facilityID = $post['facility_id'];
            $active = !is_null($post['active']) ? 1 : 0;
            $reminder->setName($post['name']);
            $reminder->setDate($post['date']);
            $reminder->setDeliveryDate($post['date']);
            $reminder->setType($post['reminderType']);
            $reminder->setPriority($post['priority']);
            $reminder->setPeriodicity($post['periodicity']);
            $reminder->setFacilityId($facilityID);
            $reminder->setTimeNumber($post['timeNumber']);
            $reminder->setReminderUnitTypeId($post['reminderUnitTypeList']);
            $reminder->setActive($active);
            //set description if we need
            if ($post['reminderDescription'] != '') {
                $reminder->setDescription($post['reminderDescription']);
            }

            $reminderUsers = array();
            $reminderUserIds = array();

            /* GET REMINDERS USERS */
            $reminderUsers = array();

            $dataChain = new TypeChain($post['date'], 'date', $this->db, $companyID, 'company');
            $unixDateTime = $dataChain->getTimestamp();

            //get reminders user unregistered in VOC
            $reminderUsersIdsList = $post['reminderUsersIdsList'];
            $reminderUsersIdsList = explode(',', $reminderUsersIdsList);

            //get reminders user registered in VOC
            $registeredReminderUserIdsList = $post['user_id'];

            foreach ($reminderUsersIdsList as $reminderUsersId) {
                if ($reminderUsersId != '') {
                    $reminderUser = new ReminderUser();
                    $reminderUser->setId($reminderUsersId);
                    $reminderUser->load();
                    $reminderUsers[] = $reminderUser;
                }
            }

            //get reminders user registered in VOC 
            foreach ($registeredReminderUserIdsList as $registeredReminderUserId) {
                if ($registeredReminderUserId != '') {
                    $reminderUser = new ReminderUser();
                    $reminderUser->setId($registeredReminderUserId);
                    $reminderUser->load();
                    $reminderUsers[] = $reminderUser;
                }
            }

            $reminder->setValidationGroup("add");
            $reminder->setUsers($reminderUsers);
            VOCApp::getInstance()->setCustomerID($companyID);
            VOCApp::getInstance()->setDateFormat(NULL);
            //Check reminder if we need
            $showReminderBeforeHand = $this->getFromPost('showReminderBeforeContainer');
            if (!is_null($showReminderBeforeHand) && $post['date'] != '' && $post['timeNumber'] != '') {
                $beforehandReminderDate = $rManager->calculateTimeByNumberAndUnitType($unixDateTime, $post['timeNumber'], $post['reminderUnitTypeList']);
                $reminder->setBeforehandReminderDate($beforehandReminderDate);
            }

            $violationList = $reminder->validate();

            if (count($violationList) == 0) {
                $dataChain = new TypeChain($reminder->date, 'date', $this->db, $companyID, 'company');

                $reminder->setDate($dataChain->getTimestamp());
                $reminder->setDeliveryDate($dataChain->getTimestamp());
                $reminderId = $reminder->save();
                $rUManager->unSetReminder2ReminderUser($reminderId);
                //save reminder Users
                foreach ($reminderUsers as $reminderUser) {
                    $rUManager->setReminder2ReminderUser($reminderUser->getId(), $reminderId);
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
                $reminderUsersEmailList = array();
                $reminderUsersIdsList = array();
                foreach ($reminder->getUsers() as $userReminder) {
                    if ($userReminder->getUserId() != 0) {
                        $userDetails = $user->getUserDetails($userReminder->getUserId());
                        $usersName[] = $userDetails["username"];
                        $user_id[] = $userReminder->getId();
                    } else {
                        $reminderUsersEmailList[] = $userReminder->getEmail();
                        $reminderUsersIdsList[] = $userReminder->getId();
                    }
                }

                $reminderUsersEmailList = implode(',', $reminderUsersEmailList);
                $reminderUsersIdsList = implode(',', $reminderUsersIdsList);
                $usersList = implode(",", $usersName);
                $this->smarty->assign('usersList', $usersList);
                $this->smarty->assign('reminderUsersEmailList', $reminderUsersEmailList);
                $this->smarty->assign('reminderUsersIdsList', $reminderUsersIdsList);
                $this->smarty->assign('user_id', $user_id);
                $this->smarty->assign('data', $reminder);
            }
        }
        //get reminder unit Type List
        $utManager = VOCApp::getInstance()->getService('unitType');
        $reminderUnitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($reminder->getPeriodicity());

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
            "modules/js/manageReminders.js",
            "modules/js/reminderUserObject.js",
            "modules/js/jquery-ui-1.8.2.custom/jquery-plugins/json/jquery.json-2.2.min.js"
        );
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array('modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css');
        $this->smarty->assign('cssSources', $cssSources);
        $post = new stdClass();
        $post->facility_id = $facilityId;
        $post->id = 0;

        $this->smarty->assign('data', $reminder);
        $this->smarty->assign('showReminderBeforeHand', $showReminderBeforeHand);
        $this->smarty->assign('request', $request);
        $this->smarty->assign('sendFormAction', '?action=addItem&category=' . $request['category'] . '&facilityID=' . $request['facilityID']);
        $this->smarty->assign('pleaseWaitReason', "Recalculating reminders at Facility.");
        $this->smarty->assign('tpl', 'tpls/addReminder.tpl');
        $this->smarty->assign('reminderUnitTypeList', $reminderUnitTypeList);
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionViewDetails()
    {
        $db = VOCApp::getInstance()->getService('db');
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        $reminder = new Reminder();
        $reminder->setId($this->getFromRequest('id'));
        $reminder->load();

        $reminderManager = VOCApp::getInstance()->getService('reminder');
        $reminderTimingList = $reminderManager->getReminderTimingList();
        $this->smarty->assign('reminderTimingList', $reminderTimingList);

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

        $dataChain = new TypeChain(date("y-m-d", $reminder->getDate()), 'date', $this->db, $companyID, 'company');
        $reminder->setDate($dataChain->formatOutput());

        $user = new User($this->db);

        $dataChain = new TypeChain(date("y-m-d", $reminder->getDeliveryDate()), 'date', $this->db, $companyID, 'company');
        $reminder->setDeliveryDate($dataChain->formatOutput());


        $reminderUserListCount = $rUManager->countReminderUsersByReminderId($reminder->getId());
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($reminderUserListCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);

        $reminderUserList = $rUManager->getReminderUsersByReminderId($reminder->getId(), $pagination);
        $usersName = array();
        $userList = array();

        foreach ($reminderUserList as $reminderUser) {
            if ($reminderUser->getUserId() != 0) {
                $userDetails = $user->getUserDetails($reminderUser->getUserId());
                $usersName[] = $userDetails['username'];
                $mobile = $userDetails['username'];
                $userName = $userDetails['username'];
            } else {
                $mobile = '-';
                $userName = '-';
            }
            $userList[] = array(
                'user_id' => $reminderUser->getUserId(),
                'email' => $reminderUser->getEmail(),
                'username' => $userName,
                'mobile' => $mobile,
                'reminder_user_id' => $reminderUser->getId()
            );
        }

        $unitType = new UnitType($db);
        $unitType->setUnitTypeId($reminder->getReminderUnitTypeId());
        $unitType->load();

        $usersNames = implode(",", $usersName);
        $this->smarty->assign('unitType', $unitType);
        $this->smarty->assign('reminder', $reminder);
        $this->smarty->assign("userList", $userList);
        $this->smarty->assign("usersNames", $usersNames);
        $this->smarty->assign("facilityID", $this->getFromRequest('facilityID'));
        //set js scripts
        $jsSources = array("modules/js/checkBoxes.js",
            "modules/js/autocomplete/jquery.autocomplete.js");
        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array("modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css",
        );
        $this->smarty->assign('cssSources', $cssSources);
        //set tpl
        $this->smarty->assign('tpl', 'tpls/viewReminder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionDeleteItem()
    {
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
                $reminder = new Reminder();
                $reminder->setId($reminderID);
                $reminder->load();
                $delete = array();
                $delete["id"] = $reminder->getId();
                $delete["name"] = $reminder->getName();
                $delete["facility_id"] = $reminder->getFacilityId();
                $delete["date"] = $reminder->getDate();
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

    protected function actionConfirmDelete()
    {
        foreach ($this->itemID as $ID) {
            $reminder = new Reminder();
            $reminder->setId($ID);
            $reminder->load();
            $facilityId = $reminder->getFacilityId();
            $rUManager = \VOCApp::getInstance()->getService('reminderUser');
            $rUManager->unSetReminder2ReminderUser($ID);
            $reminder->delete();
        }
        header("Location: ?action=browseCategory&category=facility&id=" . $facilityId . "&bookmark=reminder&notify=52");
    }

    protected function actionEdit()
    {
        //	Access control
        if (!$this->user->checkAccess('facility', $this->getFromRequest("facilityID"))) {
            throw new Exception('deny');
        }
        $facility = new Facility($this->db);
        $facilityDetails = $facility->getFacilityDetails($this->getFromRequest('facilityID'));
        $companyID = $facilityDetails["company_id"];
        $user = new User($this->db);
        $rManager = VOCApp::getInstance()->getService('reminder');
        $rUManager = \VOCApp::getInstance()->getService('reminderUser');

        //get reminder timing list
        $reminderTimingList = $rManager->getReminderTimingList();
        $this->smarty->assign('reminderTimingList', $reminderTimingList);

        $reminderTypeList = $rManager->getReminderTypeList();
        $this->smarty->assign('reminderTypeList', $reminderTypeList);

        $reminder = new Reminder();
        $reminder->setId($this->getFromRequest('id'));
        $reminder->load();

        //check if delivery date is less then current date
        $currentDate = date("m.d.Y");
        $currentDate = explode('.', $currentDate);
        $currentDate = mktime('0', '0', '0', $currentDate[0], $currentDate[1], $currentDate[2]);
        if ($currentDate > $reminder->getDeliveryDate()) {
            $reminder->setDeliveryDate($currentDate);
        }

        $dataChain = new TypeChain(date("y-m-d", strtotime('+1 days', $reminder->getDeliveryDate())), 'date', $this->db, $companyID, 'company');
        $reminder->setDate($dataChain->formatOutput());

        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityID'));
        $params = array("bookmark" => "reminder");

        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityID'), $params);
        $this->setPermissionsNew('viewReminder');

        $post = $this->getFromPost();

        if (count($post) > 0) {

            $facilityID = $post['facility_id'];
            $active = !is_null($post['active']) ? 1 : 0;

            $reminder = new Reminder();
            $reminder->setId($post['id']);
            $reminder->load();

            $reminder->setActive($active);
            $reminder->setName($post['name']);
            $reminder->setDate($post['date']);
            $reminder->setType($post['reminderType']);
            $reminder->setPriority($post['priority']);
            $reminder->setPeriodicity($post['periodicity']);
            $reminder->setDeliveryDate($post['date']);
            $reminder->setTimeNumber($post['timeNumber']);

            //set description if we need
            if ($post['reminderDescription'] != '') {
                $reminder->setDescription($post['reminderDescription']);
            }
            /* SET REMINDER USERS */
            $registeredReminderUserIds = $post['user_id'];
            $reminderUsers = array();
            $reminderUser = array();

            //add users registered in Voc
            foreach ($registeredReminderUserIds as $registeredReminderUserId) {
                $reminderUser = new ReminderUser();
                $reminderUser->setId($registeredReminderUserId);
                $reminderUser->load();
                $reminderUsers[] = $reminderUser;
            }

            //add users unregistered in Voc
            $unregisteredReminderUserIds = $post['reminderUsersIdsList'];
            $unregisteredReminderUserIds = explode(',', $unregisteredReminderUserIds);

            foreach ($unregisteredReminderUserIds as $unregisteredReminderUserId) {
                $reminderUser = new ReminderUser();
                $reminderUser->setId($unregisteredReminderUserId);
                $reminderUser->load();
                $reminderUsers[] = $reminderUser;
            }

            $reminder->setUsers($reminderUsers);
            VOCApp::getInstance()->setCustomerID($companyID);
            VOCApp::getInstance()->setDateFormat(NULL);

            //Check reminder if we need
            $dataChain = new TypeChain($reminder->getDate(), 'date', $this->db, $companyID, 'company');
            $unixDateTime = $dataChain->getTimestamp();
            $showReminderBeforeHand = $this->getFromPost('showReminderBeforeContainer');

            if (!is_null($showReminderBeforeHand)) {
                $beforehandReminderDate = $rManager->calculateTimeByNumberAndUnitType($unixDateTime, $post['timeNumber'], $post['reminderUnitTypeList']);
                $reminder->setBeforehandReminderDate($beforehandReminderDate);
                $reminder->setReminderUnitTypeId($post['reminderUnitTypeList']);
            } else {
                $reminder->setBeforehandReminderDate(0);
                $reminder->setReminderUnitTypeId(0);
            }

            $violationList = $reminder->validate();

            if (count($violationList) == 0) {
                $dataChain = new TypeChain($reminder->getDate(), 'date', $this->db, $companyID, 'company');
                $reminder->setDate($dataChain->getTimestamp());
                $reminder->setDeliveryDate($dataChain->getTimestamp());
                $reminderId = $reminder->save();
                $rUManager->unSetReminder2ReminderUser($reminderId);
                //save reminder Users
                foreach ($reminderUsers as $reminderUser) {
                    $rUManager->setReminder2ReminderUser($reminderUser->getId(), $reminderId);
                }

                // redirect
                header("Location: ?action=viewDetails&category=reminder&id=" . $reminder->getId() . "&facilityID=" . $facilityID . "&notify=53");
            } else {
                $notifyc = new Notify(null, $this->db);
                $notify = $notifyc->getPopUpNotifyMessage(401);
                $this->smarty->assign("notify", $notify);
                $this->smarty->assign('violationList', $violationList);
                $user_id = array();

                foreach ($reminder->getUsers() as $user) {
                    $usersName[] = $user["username"];
                    $user_id[] = $user->getId();
                }
                $usersList = implode(",", $usersName);
                $this->smarty->assign('usersList', $usersList);
                $this->smarty->assign('user_id', $user_id);
                $this->smarty->assign('data', $reminder);
            }
        }
        //get reminder unit Type List
        $utManager = VOCApp::getInstance()->getService('unitType');
        $reminderUnitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($reminder->getPeriodicity());

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
            "modules/js/manageReminders.js",
            "modules/js/reminderUserObject.js"
        );

        $this->smarty->assign('jsSources', $jsSources);

        $cssSources = array("modules/js/jquery-ui-1.8.2.custom/css/smoothness/jquery-ui-1.8.2.custom.css",
        );
        $this->smarty->assign('cssSources', $cssSources);
        // i should set time independ of edit or view . So i check if post is empty - it is view and i get date as timestap. so i must format date
        $dataChain = new TypeChain(null, 'date', $this->db, $companyID, 'company');
        if (count($post) > 0) {
            // edit
            $dataChain->setValue($reminder->date);
        } else {
            // view
            $dataChain->setValue(date("y-m-d", $reminder->date));
        }
        $this->smarty->assign('dataChain', $dataChain);
        $reminder->date = $dataChain->formatOutput();
        /* GET USER REMINDER LIST */
        $reminderUsersList = $reminder->getUsers();
        $reminderUsersEmailList = array();
        $usersName = array();
        $user_id = array();
        $reminderUsersIdsList = array();
        foreach ($reminderUsersList as $reminderUsers) {
            if ($reminderUsers->getUserId() != 0) {
                //get users registered in VOC
                $userDetails = $user->getUserDetails($reminderUsers->getUserId());
                $usersName[] = $userDetails["username"];
                $user_id[] = $reminderUsers->getId();
            } else {
                //get user Emails unregistered voc
                $reminderUsersEmailList[] = $reminderUsers->getEmail();
                $reminderUsersIdsList[] = $reminderUsers->getId();
            }
        }
        $reminderUsersIdsList = implode(',', $reminderUsersIdsList);
        $reminderUsersEmailList = implode(',', $reminderUsersEmailList);

        $usersList = implode(",", $usersName);
        $this->smarty->assign('reminderUsersEmailList', $reminderUsersEmailList);
        $this->smarty->assign('reminderUsersIdsList', $reminderUsersIdsList);
        $this->smarty->assign('usersList', $usersList);
        $this->smarty->assign('user_id', $user_id);
        $this->smarty->assign('data', $reminder);
        $this->smarty->assign('reminderUnitTypeList', $reminderUnitTypeList);
        $this->smarty->assign('tpl', 'tpls/addReminder.tpl');
        $this->smarty->display("tpls:index.tpl");
    }

    protected function actionLoadUsers()
    {
        $usersList = array();
        $userItem = array();

        $facilityId = $this->getFromRequest('facilityId');
        $remindId = $this->getFromRequest('remindId');
        $reminderUsersIds = $this->getFromRequest('remindUsers');
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        //get Reminders Users by facilityId
        $reminderUsersList = $rUManager->getReminderUserListByFacilityId($facilityId, 'registered');
        $user = new User($this->db);

        foreach ($reminderUsersList as $reminderUser) {
            $userDetails = $user->getUserDetails($reminderUser->getUserId());
            $userItem['id'] = $reminderUser->getId();
            $userItem['name'] = $userDetails["username"];
            if (in_array($reminderUser->getId(), $reminderUsersIds)) {
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

    protected function actionSetRemindToUser()
    {
        $rowsToSet = $this->getFromRequest('rowsToSet');
        $response = "";
        $user = new User($this->db);
        $usersName = array();
        foreach ($rowsToSet as $id) {
            $reminder = new ReminderUser();
            $reminder->setId($id);
            $reminder->load();
            $accessName = $user->getAccessnameByID($reminder->getUserId());
            $usersName[] = $user->getUsernamebyAccessname($accessName);
            $response .= "<input type='hidden' name='user_id[]' id='user_id[]' value='$id' />";
        }
        $response .= implode(",", $usersName);

        echo $response;
    }

    /**
     * ajax method
     */
    public function actionGetReminderUnitTypeList()
    {
        $periodicity = $this->getFromPost('periodicity');
        $utManager = VOCApp::getInstance()->getService('unitType');
        $reminderUnitTypeList = $utManager->getTimeUnitTypeListByPeriodicity($periodicity);
        $this->smarty->assign('reminderUnitTypeList', $reminderUnitTypeList);
        $result = $this->smarty->fetch('tpls/reminderUnitTypeList.tpl');
        echo $result;
    }

    /*
     * ajax method open reminder user Dialog
     */

    public function actionLoadReminderUsers()
    {
        $facilityId = $this->getFromPost('facilityId');
        $reminderId = $this->getFromPost('reminderId');
        $reminderUsersIds = $this->getFromPost('selectedIds');

        $reminderUsersIds = explode(',', $reminderUsersIds);
        $facilityReminderUsersEmailListJSon = array();
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        //get Reminders Emails by facilityId
        $facilityReminderUsersEmailList = $rUManager->getReminderUserListByFacilityId($facilityId, ReminderUserManager::UNREGISTERED_USERS);
        foreach ($facilityReminderUsersEmailList as $facilityReminderUsersEmail) {
            $facilityReminderUsersEmailListJSon[] = $facilityReminderUsersEmail->getAttributes();
        }

        $facilityReminderUsersEmailListJSon = json_encode($facilityReminderUsersEmailListJSon);

        $this->smarty->assign('facilityReminderUsersEmailListJSon', $facilityReminderUsersEmailListJSon);
        $this->smarty->assign('facilityReminderUsersEmailList', $facilityReminderUsersEmailList);
        $this->smarty->assign('reminderUsersIds', $reminderUsersIds);
        $this->smarty->assign('facilityId', $facilityId);

        echo $this->smarty->fetch('tpls/reminderUsersContentDialog.tpl');
    }

    /**
     * ajax method save reminder
     */
    public function actionSaveReminderUser()
    {
        $facilityId = $this->getFromPost('facilityId');
        $email = $this->getFromPost('email');

        $reminderUser = new ReminderUser();
        $reminderUser->setUserId(0);
        $reminderUser->setFacilityId($facilityId);
        $reminderUser->setEmail($email);
        $violationList = $reminderUser->validate();
        $response = array();
        if (count($violationList) == 0) {
            $reminderUserId = $reminderUser->save();
            $response['error'] = false;
            $response['userId'] = $reminderUserId;
        } else {
            //send ViolationList by ajax response
            $error = array();
            $response['error'] = true;
            foreach ($violationList as $violation) {
                $error[$violation->getPropertyPath()] = $violation->getMessage();
            }
            $response['errorMessage'] = $error;
        }
        $response = json_encode($response);
        echo $response;
    }

}