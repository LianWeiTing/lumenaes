<?php
/**
 * 自定义异常-openssl加密算法不支持。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class MethodNotSupportException extends Exception
{
    const MSG = 'Openssl encryption algorithm not supported';

    const ERROR_CODE = '9994';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
