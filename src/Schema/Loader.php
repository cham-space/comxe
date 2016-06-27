<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/19
 * Time: 下午12:13
 */

namespace Gomeplus\Comx\Schema;

use Gomeplus\Comx\Debug\DebugTrait;
use Gomeplus\Comx\Rest\RequestMessage;
use Gomeplus\Comx\Schema\DataDecor\AbstractDecor;
use Gomeplus\Comx\Schema\DataDecor\DecorFactory;
use Gomeplus\Comx\Schema\DataDecor\RootDecor;

class Loader {

    use DebugTrait;

    const CONF_FILE_SUFFIX = '.json';

    const CONF_PATH_SECTION = DIRECTORY_SEPARATOR.'apis';

    private $apiConfPath;

    public function __construct($pathToHome)
    {
        $this->apiConfPath = $pathToHome . self::CONF_PATH_SECTION;
    }

    /**
     * @param $path
     * @param string $method
     * @return DataDecor\AbstractDecor
     * @throws CannotFindSchemaException
     * @throws DataDecor\UnknownDecorTypeException
     * @throws Exception
     * @throws \Comos\Config\Exception
     */
    public function load($path, $method = RequestMessage::METHOD_GET)
    {

        $target = $this->apiConfPath . $path . DIRECTORY_SEPARATOR . $method . self::CONF_FILE_SUFFIX;

        $realPath = realpath($target);
        if ((!$realPath) || is_dir($realPath)) {
            throw new CannotFindSchemaException('file dose not exist: ' . $target);
        }

        if (0 !== strpos($realPath, $this->apiConfPath . DIRECTORY_SEPARATOR)) {
            throw new SecurityException('invalid conf path: ' . $target);
        }
        self::appendDebugInfo('Load json: '.$realPath);
        $conf = \Comos\Config\Loader::fromJsonFile($realPath);
        /**
         * @var RootDecor $decor
         */
        $decor = DecorFactory::create($conf, AbstractDecor::TYPE_ROOT);
        $decor->setPath($path)->setMethod($method);
        return $decor;
    }

    public function getAllApis()
    {
        $apis = [];
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->apiConfPath,
                \RecursiveDirectoryIterator::SKIP_DOTS|\RecursiveDirectoryIterator::KEY_AS_FILENAME)
        );

        foreach ($it as $filename => $fileInfo)
        {
            /**
             * @var \SplFileInfo $fileInfo
             */
            $ms = null;
            if (
               (!preg_match('/^(get)|(post)|(delete)|(put).json$/i', $filename, $ms)) ||
               (!$fileInfo->isReadable()) ||
               ($fileInfo->isDir()) ||
               (preg_match('/(\\/\\.)|(\\\\\\.)/', $fileInfo->getRealPath(), $ms))
            ) {
                self::appendDebugInfo("skip file:".$fileInfo->getRealPath());
                continue;
            }
            $startPos = strlen($this->apiConfPath);
            $path = substr($fileInfo->getPath(), $startPos);
            $ps = explode('.', $filename);
            $method = array_shift($ps);
            $apis[] = $this->load($path, $method);
        }
        
        return $apis;
    }
}