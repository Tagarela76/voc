<?php

namespace VWM\Apps\Logbook\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Logbook Pending Record to Remind user to create logbook in the future;
 */
class LogbookPendingRecordCreateCommand extends Command
{
    protected function configure()
    {
        $this
                ->setName('createLogbookPending')
                ->setDescription('creating logbook Pending Record to remind user to create some logbook');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lbManager = \VOCApp::getInstance()->getService('logbook');
        $result = array();
        $currentRecurringLogbookList = $lbManager->getCurrentRecurringLogbookList();
        
        if(empty($currentRecurringLogbookList)){
            $output->writeln('No any users');
            return;
        } 
        foreach ($currentRecurringLogbookList as $currentRecurringLogbook){
            $logbookPendingRecord = $currentRecurringLogbook->convertToLogbookPendingRecord();
            $logbookPendingRecord->setDateTime(time());
            $logbookPendingRecord->save();
            $nextLogbookPendingRecordTime = $lbManager->calculateNextLogbookDate($currentRecurringLogbook->getPeriodicity(), $currentRecurringLogbook->getNextDate());
            $currentRecurringLogbook->setNextDate($nextLogbookPendingRecordTime);
            $currentRecurringLogbook->save();
            $result[]=$currentRecurringLogbook->getId();
        }
        
        $result = implode(',', $result);
        $output->writeln('create such logbooks: '.$result);
    }

}
?>
