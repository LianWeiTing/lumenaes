<?php
/**
 * 自定义异常 - openssl_random_pseudo_bytes 方法不存在。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class OpensslFunctionMissException extends Exception
{
    const MSG = 'function openssl_random_pseudo_bytes not found';

    const ERROR_CODE = '9992';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
