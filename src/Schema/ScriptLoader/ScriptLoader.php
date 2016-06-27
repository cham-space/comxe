<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/29
 * Time: 下午4:16
 */

namespace Gomeplus\Comx\Schema\ScriptLoader;

use Gomeplus\Comx\Schema\SecurityException;

class ScriptLoader {

    const CONF_FILE_SUFFIX = '.php';

    const CONF_PATH_SECTION = '/scripts';

    private $apiConfPath;

    public function __construct($pathToHome)
    {
        $this->apiConfPath = $pathToHome . self::CONF_PATH_SECTION;
    }

    /**
     * @param $scriptName
     * @return mixed
     * @throws CannotFindScriptException
     * @throws InvalidScriptException
     * @throws SecurityException
     */
    public function load($scriptName)
    {
        $target = $this->apiConfPath . DIRECTORY_SEPARATOR . $scriptName . self::CONF_FILE_SUFFIX;

        $realPath = realpath($target);
        if ((!$realPath) || is_dir($realPath)) {
            throw new CannotFindScriptException('file dose not exist: ' . $target);
        }

        if (0 !== strpos($realPath, $this->apiConfPath . DIRECTORY_SEPARATOR)) {
            throw new SecurityException('invalid script path: ' . $target);
        }

        $result = (include $target);
        if (!is_callable($result)) {
            throw new InvalidScriptException('invalid script, not callable '.$target);
        }
        return $result;
    }
}