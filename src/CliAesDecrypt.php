<?php
/**
 * php cli 模式下 AES 解密组件。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2019 imcn.vip
 * @version   v2.0
 */
namespace Fairyin\LumenAES;

use Exception;

class CliAesDecrypt
{
    protected $key;

    protected $method;

    protected $offset;

    public function __construct()
    {
        $s_key = env('AES_KEY', '');
        $i_offset = env('AES_OFFSET', 0);
        $s_method = env('AES_METHOD', '');
        if ('' == $s_key || (0 >= $i_offset && $i_offset >= 40) || '' == $s_method) {
            throw new Exception("cli config error");
        }
        $this->key = $s_key;
        $this->offset = $i_offset;
        $this->method = $s_method;
    }

    /**
     * hash解密方法。
     * @param string $hash Hash值
     * @return mixed
     */
    public function opensslAesDecrypt($hash)
    {
        try {
            if (!function_exists('openssl_cipher_iv_length')) {
                echo date('Y-m-d H:i:s', time()) . ' | openssl_cipher_iv_length not found'. PHP_EOL;

                return false;
            }
            //获取向量字符串长度
            $iv_length = openssl_cipher_iv_length($this->method);
            //去除首混淆字符。
            $s_new_hash = substr($hash, 40 - $this->offset);
            //获取向量
            $iv = substr($s_new_hash, 0, $iv_length);
            //获取固定40位的盐
            $s_salt = substr($s_new_hash, $iv_length, 40);
            $s_hash_data = substr($s_new_hash, $iv_length + 40 + $this->offset);
            if (sha1($iv . $s_hash_data . $this->key) != $s_salt) {
                echo date('Y-m-d H:i:s', time()) . ' | salt error : ' . PHP_EOL;

                return false;
            } elseif (strlen($iv) !== $iv_length) {
                echo date('Y-m-d H:i:s', time()) . ' | iv length error : ' . PHP_EOL;

                return false;
            }
            $s_data = openssl_decrypt(base64_decode($s_hash_data), $this->method, $this->key, OPENSSL_RAW_DATA, $iv);

            return $s_data;
        } catch (Exception $e) {
            echo date('Y-m-d H:i:s', time()) . ' | Exception message : ' . $e->getMessage() . PHP_EOL;

            return false;
        }
    }
}
