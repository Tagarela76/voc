<?php
use VWM\Apps\Reminder\Entity\Reminder;
use VWM\Hierarchy\Facility;
use VWM\Apps\User\Entity\User;

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
        
        $rUManager = VOCApp::getInstance()->getService('reminderUser');
        $reminderUserList = $rUManager->getReminderUserListByFacility($facilityDetails['facility_id']);
        $usersList = array();
        foreach($reminderUserList as $reminderUser){
            if($reminderUser->getUserId() != 0){
               $user = new User();
               $user->setUserId($reminderUser->getUserId());
               $user->load();
               $userId = $reminderUser->getUserId();
               $name = $user->getUserName();
               $phone = $user->getMobile();
            }else{
               $userId = '-';
               $name = '-';
               $phone = '-';
            }
            $usersList[] = array(
                'user_id'=>$userId,
                'username' => $name,
                'email'=>$reminderUser->getEmail(),
                'mobile'=>$phone
            );
        }
        
        $this->smarty->assign('facilityId', $facilityDetails['facility_id']);
        $this->smarty->assign('usersList', $usersList);
        $this->smarty->assign('tpl', 'tpls/viewReminderUsers.tpl');
    }
    
    public function actionViewReminderUserDetails()
    {
        $db = VOCApp::getInstance()->getService('db');
        $rManager = VOCApp::getInstance()->getService('reminder');
        
        $userId = $this->getFromRequest('userId');
        $user = new User();
        $user->setUserId($userId);
        $user->load();
        
        $remindersCount = $rManager->countRemindersByUserId($userId);
        $url = "?" . $_SERVER["QUERY_STRING"];
        $url = preg_replace("/\&page=\d*/", "", $url);
        $pagination = new Pagination($remindersCount);
        $pagination->url = $url;
        $this->smarty->assign('pagination', $pagination);
        
        $reminderList = $rManager->getRemindersByUserId($userId, $pagination);
        
        $this->setNavigationUpNew('facility', $this->getFromRequest('facilityId'));
        $params = array("bookmark" => "reminderUsers");
        $this->setListCategoriesLeftNew('facility', $this->getFromRequest('facilityId'), $params);
        $this->setPermissionsNew('viewReminder');
        
        $this->smarty->assign('childCategoryItems', $reminderList);
        $this->smarty->assign('user', $user);
        $this->smarty->assign('facilityId', $this->getFromRequest('facilityId'));
        $this->smarty->assign('tpl', 'tpls/viewReminderUsersDetails.tpl');
		$this->smarty->display("tpls:index.tpl");
    }

    

}