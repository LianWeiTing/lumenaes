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
use Fairyin\LumenAES\Exceptions as Ex;
use Illuminate\Support\ServiceProvider;

class OpensslAesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('lumenaes', function ($app) {
            try {
                $s_default_version = isset(Config::get('lumenaes.default')['version']) ? Config::get('lumenaes.default')['version'] : false;
                if ($s_default_version === false) {
                    throw new Ex\ConfigNotFoundException();
                }
                $s_path = parse_url($_SERVER['REQUEST_URI'])['path'];
                $a_urls = explode('/', $s_path);
                $s_new_version = (isset($a_urls[1]) && $a_urls[1] != '') ? $a_urls[1] : $s_default_version;
                if ($s_new_version == '') {
                    throw new \Exception('路由异常！');
                } elseif ($s_default_version != $s_new_version) {
                    $a_all_versions = Config::get('lumenaes.all_versions');
                    if (!$a_all_versions) {
                        throw new Ex\ConfigNotFoundException();
                    } elseif (!in_array($s_new_version, $a_all_versions)) {
                        $s_new_version = $s_default_version;
                    }

                    return new LumenOpensslAES($s_new_version);
                }

                return new LumenOpensslAES();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        });
    }

    public function boot()
    {
    }
}
