<?php
/**
 * AES 加密解密组件。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES;

use Exception;
use Fairyin\LumenAES\Exceptions as Ex;
use Illuminate\Support\Facades\Config;

class LumenOpensslAES
{
    /**
     * 加密算法。
     * @var string
     */
    protected $method;

    /**
     * 秘钥。
     * @var string
     */
    protected $key;

    /**
     * 向量。
     * @var string
     */
    protected $iv;

    /**
     * 偏移量。
     * @var int
     */
    protected $offset;

    /**
     * 版本信息。
     * @var string
     */
    protected $version;

    public function __construct($version = 'default')
    {
        $a_aes_config = Config::get('lumenaes.' . $version);
        if ($a_aes_config == null || !isset($a_aes_config['key'])) {
            throw new Ex\ConfigNotFoundException();
        } elseif (!isset($a_aes_config['key']) || strlen($a_aes_config['key']) !== 32) {
            throw new Ex\KeyLenNotMeetException();
        } elseif (!isset($a_aes_config['method']) || !in_array($a_aes_config['method'], openssl_get_cipher_methods(true))) {
            throw new Ex\MethodNotAllowedException();
        } elseif (!isset($a_aes_config['offset']) || ((int) $a_aes_config['offset']) <= 0 || ((int) $a_aes_config['offset']) >= 40) {
            throw new Ex\OffsetNotAllowedException();
        }
        $this->key = $a_aes_config['key'];
        $this->method = $a_aes_config['method'];
        $this->iv = $this->generateIV();
        $this->offset = (int) $a_aes_config['offset'];
        $this->version = $version;
    }

    /**
     * openssl aes 加密。
     * @param string $data 字符串
     */
    public function opensslAesEncrypt($data)
    {
        if (!is_string($data)) {
            throw new Ex\DataNotAllowedException();
        }
        try {
            $s_hash = base64_encode(openssl_encrypt($data, $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv));
            $s_salt = sha1($this->iv . $s_hash . $this->key);
            //偏移字符串（随机）
            $s_offset_str = sha1($this->iv . $this->key . time());
            $s_offset_start = substr($s_offset_str, strlen($s_offset_str) - $this->offset - 1, $this->offset);
            $s_offset_end = substr(sha1($this->iv . time()), 0, strlen($s_offset_str) - $this->offset);
            return $s_offset_end . $this->iv . $s_salt . $s_offset_start . $s_hash;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 9990);
        }
    }

    /**
     * hash解密方法。
     * @param string $hash Hash值
     * @return mixed
     */
    public function opensslAesDecrypt($hash)
    {
        try {
            //去除首混淆字符。
            $s_new_hash = substr($hash, 40 - $this->offset);
            //获取向量
            $this->iv = substr($s_new_hash, 0, $this->iv_length);
            //获取固定40位的盐
            $s_salt = substr($s_new_hash, $this->iv_length, 40);
            $s_hash_data = substr($s_new_hash, $this->iv_length + 40 + $this->offset);
            if (sha1($this->iv . $s_hash_data . $this->key) != $s_salt) {
                throw new Ex\SignNotMatchException();
            } elseif (strlen($this->iv) !== $this->iv_length) {
                throw new Ex\IVLenNotMeetException();
            }
            $s_data = openssl_decrypt(base64_decode($s_hash_data), $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv);

            return $s_data;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 9990);
        }
    }

    /**
     * App端解密。
     * @param  string $hash 加密后的hash值。
     * @return mixed
     */
    public function opensslAppDecrypt($hash, $offset = 8)
    {
        try {
            $s_iv = substr($hash, 0, 40);
            $s_hash = substr($hash, 40);
            $i_start = strlen($s_iv) % 3;
            $i_stop = strlen($s_iv) % 7;
            $iv = substr($s_iv, $i_start, $offset) . substr($s_iv, $i_stop, $offset);
            $data = openssl_decrypt(base64_decode($s_hash), $this->method, $this->key, OPENSSL_RAW_DATA, $iv);

            return $data;
        } catch (Exception $e) {
            throw new Exception("解密失败！");
        }
    }

    /**
     * 根据当前加密类型长度生成随机向量。
     * @return mixed
     */
    protected function generateIV()
    {
        try {
            if (!function_exists('openssl_cipher_iv_length')) {
                throw new Ex\OpensslFunctionMissException();
            }
            //获取向量字符串长度
            $this->iv_length = openssl_cipher_iv_length($this->method);
            $s_hash = sha1(time() . $this->key . time());
            $s_iv = substr($s_hash, rand(2, 20), 16);
            if (strlen($s_iv) !== 16) {
                throw new Ex\IVLengthNotMeetException();
            }

            return $s_iv;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 9990);
        }
    }

    /**
     * 获取版本信息。
     * @return string
     */
    public function getVersion()
    {
        if ('default' == $this->version) {
            if (!isset(Config::get('lumenaes.default')['version'])) {
                throw new Ex\ConfigNotFoundException();
            }

            return Config::get('lumenaes.default')['version'];
        }

        return $this->version;
    }
}
