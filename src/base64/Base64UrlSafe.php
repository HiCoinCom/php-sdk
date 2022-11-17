<?php

namespace chainup\waas\base64;


class Base64UrlSafe
{
    /**
     * URL base64 decoding
     * '-' -> '+'
     * '_' -> '/'
     * The remainder of the string length %4, supplemented with '='
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
     * URL base64 encoding
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