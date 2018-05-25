<?php
/**
 * AES 加密解密组件。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2018 imcn.vip
 * @version   v1.0
 */

namespace Fairyin\LumenAES;

use Fairyin\LumenAES\Exceptions as Ex;

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

    public function __construct($key, $method, $offset)
    {
        if (strlen($key) !== 32) {
            throw new Ex\KeyLenNotMeetException();
        }
        if (!in_array($method, openssl_get_cipher_methods(true))) {
            throw new Ex\MethodNotAllowedException();
        }
        if (!is_int($offset) || $offset <=0 || $offset >= 40) {
            throw new Ex\OffsetNotAllowedException();
        }
        $this->method = $method;
        $this->iv = $this->generateIV();
        $this->key = $key;
        $this->offset = $offset;
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
            $s_hash = bin2hex(openssl_encrypt($data, $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv));
            //二进制字符串转换为十六进制字符值，长度32位
            $s_iv = bin2hex($this->iv);
            $s_salt = sha1($s_iv . $s_hash . $this->key);
            //偏移字符串（随机）
            $s_offset_str = sha1($s_iv . $this->key . time());
            $s_offset_start = substr($s_offset_str, strlen($s_offset_str) - $this->offset - 1, $this->offset);
            $s_offset_end = substr(sha1($s_iv . time()), 0, strlen($s_offset_str) - $this->offset);

            return $s_offset_end . $s_iv . $s_hash . $s_salt . $s_offset_start;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage, 9990);
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
            //去除首尾混淆字符。
            $hash = substr($hash, 40 - $this->offset);
            $hash = substr($hash, 0, -$this->offset);
            //获取32位的十六进制向量
            $s_iv = substr($hash, 0, 32);
            //获取固定40位的盐
            $s_salt = substr($hash, -40);
            $s_hash = substr($hash, 0, - 40);
            $s_hash_data = substr($s_hash, 32);
            if (sha1($s_hash . $this->key) != $s_salt) {
                throw new Ex\SignNotMatchException();
            }
            $this->iv = hex2bin($s_iv);

            if (strlen($this->iv) !== $this->iv_length) {
                throw new Ex\IVLenNotMeetException();
            }
            $s_data = openssl_decrypt(hex2bin($s_hash_data), $this->method, $this->key, OPENSSL_RAW_DATA, $this->iv);

            return $s_data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 9990);
        }
    }

    /**
     * 根据当前加密类型长度生成随机向量。
     * @return mixed
     */
    protected function generateIV()
    {
        try {
            if (!function_exists('openssl_random_pseudo_bytes')) {
                throw new Ex\OpensslFunctionMissException();
            }
            //获取向量字符串长度
            $this->iv_length = openssl_cipher_iv_length($this->method);
            //随机生成 iv_length 位向量 crypto_strong 是否使用强加密
            $s_er_iv = openssl_random_pseudo_bytes($this->iv_length, $crypto_strong);
            if (false === $s_er_iv && false === $crypto_strong) {
                throw new Ex\IVMissException();
            }
            if (strlen($s_er_iv) !== 16) {
                throw new Ex\IVLengthNotMeetException();
            }

            return $s_er_iv;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 9990);
        }
    }
}
