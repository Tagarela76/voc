<?php

namespace VWM\Apps\Reminder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VWM\Apps\Reminder\Event\EventReminder;
use VWM\Apps\Reminder\VWMReminderEvents\ReminderEvents;

/**
 * ReminderCommand Reminde user
 *
 */
class BeforehandReminderCommand extends Command
{
    protected function configure()
    {
        $this
                ->setName('beforehandReminder')
                ->setDescription('remind about reminder voc users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = '';
        $rManager = \VOCApp::getInstance()->getService('reminder');

        $reminders = $rManager->getBeforehandCurrentReminders();
        if (!$reminders) {
            $result .= 'No reminders to send';
            $output->writeln($result);
            return;
        }

        foreach ($reminders as $reminder) {

            $result .= $rManager->sendBeforehandReminderToUser($reminder);
            
            //update reminder using observer pattern
            //get event dispatcher
            $dispatcher = \VOCApp::getInstance()->getService('eventDispatcher');
            $subscriper = new \VWM\Apps\Reminder\Subscriber\BeforehandReminderSubscriber();
            $dispatcher->addSubscriber($subscriper);
            $event = new EventReminder($reminder);
            $dispatcher->dispatch(ReminderEvents::SEND_BEFOREHAND_REMINDER, $event);
        }

        $output->writeln($result);
    }
}
