<?php

namespace VWM\Apps\Reminder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VWM\Framework\Model;

/**
 * ReminderCommand Reminde user
 *
 * @author tagarela
 */
class ReminderCommand extends Command
{
    protected function configure()
    {
        $this
                ->setName('demo:reminder')
                ->setDescription('remind voc user');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = '';
        $rManager = \VOCApp::getInstance()->getService('reminder');
        $reminders = $rManager->getCurrentReminders();
        foreach ($reminders as $reminder){
            $result.= $rManager->sendRemindToUser($reminder->getId());
        }
        
        $output->writeln($result);
    }
}
?>
