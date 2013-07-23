<?php

namespace VWM\Apps\Logbook\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VWM\Framework\Model;

/**
 * Create Logbook TODO to Remind user to create logbook in the future;
 */
class LogbookToDoCreateCommand extends Command
{
    protected function configure()
    {
        $this
                ->setName('createLogbookToDo')
                ->setDescription('creating logbookTODO to remind user to create some logbook');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $result = array();
        $currentRecurringLogbookList = $lbManager->getCurrentRecurringLogbookList();
        foreach ($currentRecurringLogbookList as $currentRecurringLogbook){
            $logbookRecordToDo = $currentRecurringLogbook->convertToLogbookRecordToDo();
            $logbookRecordToDo->setDateTime(time());
            $logbookRecordToDo->save();
            $nextLogbookToDoTime = $lbManager->calculateNextLogbookDate($currentRecurringLogbook->getPeriodicity(), $currentRecurringLogbook->getNextDate());
            $currentRecurringLogbook->setNextDate($nextLogbookToDoTime);
            $currentRecurringLogbook->save();
            $result[]=$currentRecurringLogbook->getId();
        }
        
        $result = implode(',', $result);
        $output->writeln('create such logbooks: '.$result);
    }

}
?>
