<?php
namespace common\components;

class Md5Hex {

    /**
     * 16进制转string拼接
     * @param array $bytes [description]
     * @return [type] [description]
     * @dateTime 2021-01-05T10:18:31+0800
     */
    public function encodeHexString(array $bytes)
    {
        $LOWER = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
        $length = count($bytes);
        $charArr = [];
        foreach ($bytes as $value) {
            $value = intval($value);
            $charArr[] = $LOWER[$this->uright(0xF0 & $value, 4)];
            $charArr[] = $LOWER[0x0F & $value];
        }
        return implode("", $charArr);
    }

    /** php 无符号右移 */
    public function uright($a, $n)
    {
        $c = 2147483647 >> ($n - 1);
        return $c & ($a >> $n);
    }

    /**
     * 模拟DigestUtils.md5
     * @param    [string]                   $string 加密字符
     * @return   [array]                           加密之后的byte数组
     * @dateTime 2021-01-05T09:28:33+0800
     */
    public static function md5Hex($string)
    {
        return unpack("c*", md5($string, true));
    }

}
