<?php
/**
 * 自定义异常-向量IV长度不符合。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class IVLenNotMeetException extends Exception
{
    const MSG = 'IV not meet the requirements';

    const ERROR_CODE = '9996';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
