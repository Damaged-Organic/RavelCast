<?php
// AppBundle/Entity/Manager/CipherFileManager.php
namespace AppBundle\Entity\Manager;

use Exception;

use AppBundle\Entity\Manager\Contract\AbstractCustomManager,
    AppBundle\Entity\CipherFile;

class CipherFileManager extends AbstractCustomManager
{
    public function persist($cipherFile)
    {
        if( !($cipherFile instanceof CipherFile) )
            return $this->create()
                ->setError("CipherFileManager::persist - object should be an instance of CipherFile");

        try {
            $this->_manager->persist($cipherFile);
            $this->_manager->flush();
        } catch(Exception $EX) {
            $cipherFile
                ->setError("CipherFileManager::persist - Cannot persist entity");
        }

        return $cipherFile;
    }

    public function remove($cipherFile)
    {
        if( !($cipherFile instanceof CipherFile) )
            return $this->create()
                ->setError('CipherFileManager::remove - object should be an instance of CipherFile');

        $this->_manager->remove($cipherFile);
        $this->_manager->flush();
    }

    public function removeExpired($expiredCipherFile)
    {
        foreach($expiredCipherFile as $cipherFile) {
            $this->_manager->remove($cipherFile);
        }

        $this->_manager->flush();
    }

    public function findByFileName($fileName)
    {
        $cipherFile = $this->_manager->getRepository('AppBundle:CipherFile')
            ->findOneBy(['fileName' => $fileName]);

        if( !$cipherFile )
            return $this->create()
                ->setError('CipherFileManager::findByFileName - no record found');

        return $cipherFile;
    }

    public function findExpired()
    {
        return $this->_manager->getRepository('AppBundle:CipherFile')
            ->findExpired();
    }
}
