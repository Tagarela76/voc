<?php

namespace VWM\Apps\Reminder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \VWM\Apps\Reminder\Listener\ReminderListener;
use \VWM\Apps\Reminder\Event\EventReminder;
use VWM\Framework\Model;

/**
 * ReminderCommand Reminde user
 *
 */
class ReminderCommand extends Command
{
    protected function configure()
    {
        $this
                ->setName('reminder')
                ->setDescription('remind voc user');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = '';
        $rManager = \VOCApp::getInstance()->getService('reminder');
        
        $reminders = $rManager->getCurrentReminders();
        
        foreach ($reminders as $reminder){
            
            $result.= $rManager->sendRemindToUser($reminder->getId());
            //update reminder using observer pattern
            //get event dispatcher
            $dispatcher = \VOCApp::getInstance()->getService('eventDispatcher');
            $subscriper = new \VWM\Apps\Reminder\Subscriber\ReminderSubscriber();
            $dispatcher->addSubscriber($subscriper);
            $event = new EventReminder($reminder);
            $dispatcher->dispatch(\VWM\Apps\EventDispatcher\StoreEvents::SET_NEXT_REMINDER_TIME, $event);
        }
        
        $output->writeln($result);
    }
}
?>
