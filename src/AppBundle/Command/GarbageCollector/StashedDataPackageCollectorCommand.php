<?php
// AppBundle/Command/GarbageCollector/StashedDataPackageCollectorCommand.php
namespace AppBundle\Command\GarbageCollector;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class StashedDataPackageCollectorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ravelcast:collect_garbage:stashed_data_package')
            ->setDescription('Collect expired Stashed Data Packages')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('entity_manager.stashed_data_package')
            ->removeExpired();
    }
}
