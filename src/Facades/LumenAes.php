<?php
/**
 * LumenAes Facade。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES\Facades;

use Illuminate\Support\Facades\Facade;

class LumenAes extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lumenaes';
    }
}
