<?php
/**
 * 自定义异常-iv 向量生失败。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class IVMissException extends Exception
{
    const MSG = 'IV generate failed';

    const ERROR_CODE = '9991';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
