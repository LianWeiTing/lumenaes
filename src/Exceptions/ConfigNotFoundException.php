<?php
/**
 * 自定义异常 - 配置文件加载失败！。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Exceptions;

use Exception;

class ConfigNotFoundException extends Exception
{
    const MSG = 'config not found';

    const ERROR_CODE = '9999';

    public function __construct()
    {
        throw new Exception(static::MSG, static::ERROR_CODE);
    }
}
