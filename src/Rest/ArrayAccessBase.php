<?php
/**
 * Created by PhpStorm.
 * User: zhaoqing
 * Date: 16/4/13
 * Time: 下午9:11
 */

namespace Gomeplus\Comx\Rest;


abstract class ArrayAccessBase implements \ArrayAccess
{
    const UNACCESSIBLE = 0;
    const COULD_GET = 1;
    const COULD_SET = 2;
    const COULD_UNSET = 4;
    const WRITABLE = 6;
    const ACCESSIBLE = 7;

    abstract protected function getArrayAccessibleFields();
    private function getArrayAccessibleFieldSetting($name)
    {
        $settings = $this->getArrayAccessibleFields();
        if (!isset($settings[$name])) {
            return 0;
        }
        return $settings[$name];
    }
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $option = $this->getArrayAccessibleFieldSetting($offset);
        if (!$option) {
            return false;
        }
        return true;

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $option = $this->getArrayAccessibleFieldSetting($offset);
        if( ($option & self::COULD_GET) != self::COULD_GET){
            return null;
        }
        return call_user_func([$this, 'get'.ucfirst($offset)]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $option = $this->getArrayAccessibleFieldSetting($offset);
        if (($option & self::COULD_SET) != self::COULD_SET) {
            return;
        }
        call_user_func([$this, 'set'.ucfirst($offset)], $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $option = $this->getArrayAccessibleFieldSetting($offset);
        if (($option & self::COULD_UNSET) != self::COULD_UNSET) {
            return ;
        }
        call_user_func([$this, 'unset'.ucfirst($offset)]);
    }


} 