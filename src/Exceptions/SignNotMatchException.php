<?php
/**
 * 自定义异常 - sign 验证失败。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class SignNotMatchException extends Exception
{
    const MSG = 'sign not match';

    const ERROR_CODE = '9998';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
