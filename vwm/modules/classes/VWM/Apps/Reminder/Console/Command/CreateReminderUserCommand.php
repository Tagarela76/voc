<?php

namespace VWM\Apps\Reminder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VWM\Hierarchy\CompanyManager;
use VWM\Apps\Reminder\Entity\ReminderUser;

/**
 * Command for creating user reminders
 */
class CreateReminderUserCommand extends Command
{

    protected function configure()
    {
        $this
                ->setName('createReminderUser')
                ->setDescription('create reminder user from user registrated in Voc');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fManager = new \VWM\Hierarchy\FacilityManager();
        $uManager = \VOCApp::getInstance()->getService('user');
        $logger = \VOCApp::getInstance()->getService('errorLogger');
        
        $userList = array();
        
        $facilityList = $fManager->getAllFacilityList();
        foreach ($facilityList as $facility) {
            $users = $uManager->getUserListByFacilityId($facility->getFacilityId());
            $userList = array_merge($userList, $users);
        }

        $count = 0;
        $errors = 0;
        foreach ($userList as $user) {
            $reminderUser = new ReminderUser();
            $reminderUser->setUserId($user->getUserId());
            $reminderUser->setEmail($user->getEmail());
            $reminderUser->setFacilityId($user->getFacilityId());
            $id = $reminderUser->save();
            if (!$id) {
                $log->addError('can\'t save reminder user with id:' . $user->getUserId() . ', mail:' . $user->getEmail());
                $errors++;
            }else{
                $count++;
            }
        }
        $output->writeln($count." users were saved. Errors:".$errors);
    }

}
?>
