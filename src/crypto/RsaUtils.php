<?php

namespace chainup\waas\crypto;

use chainup\waas\base64\Base64UrlSafe;

class RsaUtils
{
    /**
     * 格式化公钥
     * @param $publicKey string 公钥
     * @return string
     */
    public static function FormatterPublicKey($publicKey){
        if (false !== strpos($publicKey, '-----BEGIN PUBLIC KEY-----')) return $publicKey;

        $str = chunk_split($publicKey, 64, PHP_EOL);//在每一个64字符后加一个\n
        $publicKey = "-----BEGIN PUBLIC KEY-----".PHP_EOL.$str."-----END PUBLIC KEY-----";

        return $publicKey;
    }

    /**
     * 格式化私钥
     * @param $privateKey string 公钥
     * @return string
     */
    public static function FormatterPrivateKey($privateKey){
        if (false !==strpos($privateKey, '-----BEGIN RSA PRIVATE KEY-----')) return $privateKey;

        $str = chunk_split($privateKey, 64, PHP_EOL);//在每一个64字符后加一个\n
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----".PHP_EOL.$str."-----END RSA PRIVATE KEY-----";

        return $privateKey;
    }

    /**
     *  私钥加密（分段加密）
     *  @param str    需要加密字符串
     */
    public static function Encrypt($str, $privateKey) {
        $crypted = array();
//        $data = json_encode($str);
        $data = $str;
        $dataArray = str_split($data, 234);
        foreach($dataArray as $subData){
            $subCrypted = null;
            openssl_private_encrypt($subData, $subCrypted, $privateKey);
            $crypted[] = $subCrypted;
        }
        $crypted = implode('',$crypted);
        return Base64UrlSafe::Encode($crypted);
    }

    /**
     *  公钥解密（分段解密）
     *  @encrypstr  加密字符串
     */
    public static function Decrypt($encryptstr, $publickKey) {
        // echo $encryptstr;exit;
        $encryptstr = Base64UrlSafe::Decode($encryptstr);
        $decrypted = array();
        $dataArray = str_split($encryptstr, 256);

        foreach($dataArray as $subData){
            $subDecrypted = null;
            openssl_public_decrypt($subData, $subDecrypted, $publickKey);
            $decrypted[] = $subDecrypted;
        }
        $decrypted = implode('',$decrypted);
        // openssl_public_decrypt(base64_decode($encryptstr),$decryptstr,$this->pub_key);
        return $decrypted;
    }
}