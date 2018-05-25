<?php
/**
 * 自定义异常-秘钥key长度不符合。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class KeyLenNotMeetException extends Exception
{
    const MSG = 'Key not meet the requirements';

    const ERROR_CODE = '9993';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
