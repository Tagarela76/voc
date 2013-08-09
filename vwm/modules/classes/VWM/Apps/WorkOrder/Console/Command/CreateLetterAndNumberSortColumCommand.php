<?php

namespace VWM\Apps\WorkOrder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateLetterAndNumberSortColumCommand extends Command
{
     protected function configure()
    {
        $this
                ->setName('CreateSortColums')
                ->setDescription('Create Letter And Number Colum for correct pfp sorting');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pfpManager = \VOCApp::getInstance()->getService('pfp');
        $count = 0;
        $pfps = $pfpManager->getAllPfp();
        foreach ($pfps as $pfp) {
            $weightLetterSort = $pfpManager->getWeightLetterFromPfpDescription($pfp);
            $weightNumberSort = $pfpManager->getWeightNumberFromPfpDescription($pfp);
            $pfp->setWeightLetterSort($weightLetterSort);
            $pfp->setWeightNumberSort($weightNumberSort);
            $id = $pfp->save();
            $count++;
            $output->writeln('update Pfp with id: '.$id);
        }
        $output->writeln($count." pfps where updated");
    }
}
?>
