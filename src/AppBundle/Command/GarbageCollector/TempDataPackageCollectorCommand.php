<?php
// AppBundle/Command/GarbageCollector/TempDataPackageCollectorCommand.php
namespace AppBundle\Command\GarbageCollector;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class TempDataPackageCollectorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ravelcast:collect_garbage:temp_data_package')
            ->setDescription('Collect hanged Temp Data Packages')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('entity_manager.temp_data_package')
            ->removeHanged();
    }
}
