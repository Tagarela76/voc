<?php

use VWM\Apps\Reminder\Entity\Reminder;
use VWM\Hierarchy\Facility;
use VWM\Apps\User\Entity\User;
use VWM\Apps\Reminder\Entity\ReminderUser;
use VWM\Apps\Reminder\Manager\ReminderUserManager;

class CReminderUsers extends Controller
{

    function CReminderUsers($smarty, $xnyo, $db, $user, $action)
    {
        parent::Controller($smarty, $xnyo, $db, $user, $action);
        $this->category = 'reminderUsers';
    }

    /**
     * bookmarkReminder($vars)
     * @vars $vars array of variables: $facility, $facilityDetails, $moduleMap
     */
    protected function bookmarkReminderUsers($vars)
    {
        extract($vars);
        if (is_null($facilityDetails['facility_id'])) {
            throw new Exception('404');
        }
        $filterTab = $this->getFromRequest('tab');
       if(is_null($filterTab)){
          $filterTab = 'reminderUsers';
       }
        switch ($filterTab) {
            case 'reminderUsers':
                $this->viewReminderUser($facilityDetails['facility_id']);
                break;
            case 'reminderEmails':
                $this->viewReminderEmails($facilityDetails['facility_id']);
                break;
            default :
                throw new Exception('Incorrect Tab '.$tab);
                break;
        }
        $jsSources = array(
            "modules/js/reminderUserManager.js",
            );
        $this->smarty->assign('jsSources', $jsSources);
        $this->smarty->assign('filterTab', $filterTab);
        $this->smarty->assign('facilityId', $facilityDetails['facility_id']);
        
    }

    public function actionViewItemDetails()
    {
        $db = VOCApp::getInstance()->getService('db');
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        $rManager = VOCApp::getInstance()->getService('reminder');
        
        $reminderUserId = $this->getFromRequest('reminderUserId');
        $reminderUser = new ReminderUser();
        $reminderUser->setId($reminderUserId);
        $reminderUser->load();
        
        $remindersCount = $rUManager->countReminderListByReminderUserId($reminderUserId);
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($remindersCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);
        
        $reminderList = $rUManager->getReminderListByReminderUserId($reminderUserId, $pagination);

        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityId'));
        $params = array("bookmark" => "reminderUsers");
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityId'), $params);
        $this->setPermissionsNew('viewReminder');
        
        if($reminderUser->getUserId()!=0){
            $this->viewReminderUserDetails($reminderUser);
        }else{
            $this->viewReminderUserEmailDetails($reminderUser);
        }
        
        $this->smarty->assign('childCategoryItems', $reminderList);
        $this->smarty->assign('facilityId', $this->getFromRequest('facilityId'));
        $this->smarty->display("tpls:index.tpl");
        
    }

    /**
     * 
     * displary reminder User Details
     * 
     * @param ReminderUser $reminderUser
     */
    public function viewReminderUserEmailDetails($reminderUser)
    {
        $this->smarty->assign('reminderUser', $reminderUser);
        $this->smarty->assign('tpl', 'tpls/viewReminderUserEmailDetails.tpl');
    }
    /**
     * 
     * display reminder user details
     * 
     * @param VWM\Apps\Reminder\Entity\ReminderUser $reminderUser
     */
    public function viewReminderUserDetails($reminderUser)
    {
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        $user = $rUManager->getUserByReminderUserId($reminderUser->getId());
        
        $this->smarty->assign('user', $user);
        $this->smarty->assign('tpl', 'tpls/viewReminderUsersDetails.tpl');
        
    }
    /**
     * 
     * display reminder Emails
     * 
     * @param int $facilityId
     */
    public function viewReminderEmails($facilityId)
    {
        $reminderUserList = array();
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        
        $reminderUserListCount = $rUManager->countReminderUserListByFacilityId($facilityId, ReminderUserManager::UNREGISTERED_USERS);
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($reminderUserListCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);
        
        $reminderUserList = $rUManager->getReminderUserListByFacilityId($facilityId, ReminderUserManager::UNREGISTERED_USERS, $pagination);
        
        $this->smarty->assign('reminderUserList', $reminderUserList);
        $this->smarty->assign('tpl', 'tpls/viewReminderEmails.tpl');
    }

    /**
     * 
     * display reminder User List
     * 
     * @param int $facilityId
     */
    public function viewReminderUser($facilityId)
    {
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        $rUManager->getUsersWithReminderByFacilityId($facilityId);
        $reminderUserListCount = $rUManager->countUsersWithReminderByFacilityId($facilityId);
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($reminderUserListCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);
        
        $reminderUserList = $rUManager->getUsersWithReminderByFacilityId($facilityId, $pagination);
        
        $usersList = array();
        foreach ($reminderUserList as $reminderUser) {
            if ($reminderUser->getUserId() != 0) {
                $user = new User();
                $user->setUserId($reminderUser->getUserId());
                $user->load();
                $userId = $reminderUser->getUserId();
                $name = $user->getUserName();
                $phone = $user->getMobile();
            } else {
                $userId = '-';
                $name = '-';
                $phone = '-';
            }
            $usersList[] = array(
                'user_id' => $userId,
                'username' => $name,
                'email' => $reminderUser->getEmail(),
                'mobile' => $phone,
                'reminderUserId' => $reminderUser->getId()
            );
        }
       
        $this->smarty->assign('usersList', $usersList);
        $this->smarty->assign('tpl', 'tpls/viewReminderUsers.tpl');
    }
    
    /**
     * 
     * delete itim action
     * 
     * @throws Exception
     */
    protected function actionDeleteItem()
    {
        $reminderUserId = $this->getFromRequest('reminderUserId');
        $facilityId = $this->getFromRequest('facilityId');
        $itemForDelete = array();
        if (!is_null($reminderUserId)) {
            
                $reminder = new ReminderUser();
                $reminder->setId($reminderUserId);
                $reminder->load();
                $delete = array();
                $delete["id"] = $reminder->getId();
                $delete["name"] = $reminder->getEmail();
                $delete["facility_id"] = $reminder->getFacilityId();
                $itemForDelete[] = $delete;
        }
        if (!is_null($facilityId)) {
            $this->smarty->assign("cancelUrl", "?action=viewItemDetails&category=reminderUsers&reminderUserId=".$reminderUserId."&facilityId=".$facilityId);
            //as ShowAddItem
            $params = array("bookmark" => "reminderUsers");

            $this->setListCategoriesLeftNew('facility', $facilityId, $params);
            $this->setNavigationUpNew('facility', $facilityId);
            $this->setPermissionsNew('viewFacility');
        }

        $this->finalDeleteItemCommon($itemForDelete, $linkedNotify, $count, $info);
    }
    
    /**
     * 
     * confirm reminder user delete
     * 
     */
    protected function actionConfirmDelete()
    {
        foreach ($this->itemID as $id) {
            $reminderUser = new ReminderUser();
            $reminderUser->setId($id);
            $reminderUser->load();
            $facilityId = $reminderUser->getFacilityId();
            $reminderUser->delete();
        }
        header("Location: ?action=browseCategory&category=facility&id=".$facilityId."&bookmark=reminderUsers&tab=reminderEmails");
    }

}