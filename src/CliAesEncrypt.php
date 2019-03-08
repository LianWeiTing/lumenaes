<?php
/**
 * php cli 模式下 AES 加密组件。
 *
 * @author    fairyin <fairyin@126.com>
 * @copyright © 2019 imcn.vip
 * @version   v2.0
 */
namespace Fairyin\LumenAES;

use Exception;

class CliAesDecrypt
{
    protected $iv;

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
        $this->iv = $this->generateIV();
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
     * 根据当前加密类型长度生成随机向量。
     * @return mixed
     */
    private function generateIV()
    {
        try {
            if (!function_exists('openssl_cipher_iv_length')) {
                throw new Ex\OpensslFunctionMissException();
            }
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
}
