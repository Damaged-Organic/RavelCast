<?php
// AppBundle/Command/GarbageCollector/CipherFileCollectorCommand.php
namespace AppBundle\Command\GarbageCollector;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class CipherFileCollectorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ravelcast:collect_garbage:cipher_file')
            ->setDescription('Collect expired Cipher File')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_cipherFileManager = $this->getContainer()->get('entity_manager.cipher_file');
        $_yamler            = $this->getContainer()->get('service.utility.yamler');

        $expiredCipherFile = $_cipherFileManager->findExpired();

        //TODO: this $cliRootDir variable is kind of a kludge
        $cliRootDir = $this->getContainer()->get('kernel')->getRootDir();

        foreach($expiredCipherFile as $cipherFile) {
            $_yamler->deleteCipherFile($cipherFile->getFileName(), $cliRootDir);
        }

        $_cipherFileManager->removeExpired($expiredCipherFile);
    }
}
