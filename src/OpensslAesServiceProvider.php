<?php
/**
 * AES 加密解密服务初始化。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class OpensslAesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('lumenaes', function ($app) {
            return new LumenOpensslAES();
        });
    }

    public function boot()
    {
    }
}
