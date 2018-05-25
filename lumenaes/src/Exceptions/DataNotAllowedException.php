<?php
/**
 * 自定义异常-传入待加密参数不符合。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class DataNotAllowedException extends Exception
{
    const MSG = 'Only input of string data is allowed';

    const ERROR_CODE = '9995';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
