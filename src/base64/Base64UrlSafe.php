<?php

namespace chainup\waas\base64;


class Base64UrlSafe
{
    /**
     * URL base64解码
     * '-' -> '+'
     * '_' -> '/'
     * 字符串长度%4的余数，补'='
     * @param base64 $string
     */
    public static function Decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * URL base64编码
     * '+' -> '-'
     * '/' -> '_'
     * '=' -> ''
     * @param raw $string
     */
    public static function Encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}