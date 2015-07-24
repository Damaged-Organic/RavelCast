<?php
// AppBundle/Service/Utility/Yamler.php
namespace AppBundle\Service\Utility;

use Symfony\Component\Yaml\Yaml;

class Yamler
{
    const YAML_KEYS_DIRECTORY_PATH = "../src/AppBundle/Resources/cipher_files";

    private $fileName;

    public function setCipherFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    public function getCipherFileName()
    {
        return $this->fileName;
    }

    private function createIfNotExists($directory)
    {
        if( !file_exists($directory) )
            mkdir($directory, 0777, TRUE);
    }

    public function writeCipherFile($fileName, $key, $iv)
    {
        //Fail safe - create keys directory if not exists
        $this->createIfNotExists(self::YAML_KEYS_DIRECTORY_PATH);

        $filePath = self::YAML_KEYS_DIRECTORY_PATH . "/{$fileName}.yml";

        $yamlContent = [
            'key' => base64_encode($key),
            'iv'  => base64_encode($iv)
        ];

        $yamlContent = Yaml::dump($yamlContent);

        file_put_contents($filePath, $yamlContent);
    }

    public function readCipherFile($fileName)
    {
        $filePath = self::YAML_KEYS_DIRECTORY_PATH . "/{$fileName}.yml";

        if( !file_exists($filePath) )
            return FALSE;

        $yamlContent = Yaml::parse(file_get_contents($filePath));

        $yamlContent = [
            'key' => base64_decode($yamlContent['key']),
            'iv'  => base64_decode($yamlContent['iv'])
        ];

        return $yamlContent;
    }

    public function deleteCipherFile($fileName, $cliRootDir = NULL)
    {
        $filePath = self::YAML_KEYS_DIRECTORY_PATH . "/{$fileName}.yml";

        //TODO: this $cliPath variable is kind of a kludge
        if( $cliRootDir )
            $filePath = "{$cliRootDir}/{$filePath}";

        if( !file_exists($filePath) )
            return FALSE;

        unlink($filePath);
    }
}
