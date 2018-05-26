<?php
/**
 * AES 加密解密服务初始化。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES;

use Illuminate\Support\ServiceProvider;

class OpensslAesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('lumenaes', function ($app) {
            //秘钥
            $s_key = env('AES_KEY');
            $s_method = env('AES_METHOD', 'AES-256-CBC');
            $i_offset = (int) env('AES_OFFSET', 6);

            return new LumenOpensslAES($s_key, $s_method, $i_offset);
        });
    }

    public function boot()
    {
    }
}
