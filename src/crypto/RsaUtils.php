<?php

namespace chainup\waas\crypto;

use chainup\waas\base64\Base64UrlSafe;

class RsaUtils
{
    /**
     * format the public key
     * @param $publicKey string public key
     * @return string
     */
    public static function FormatterPublicKey($publicKey){
        if (false !== strpos($publicKey, '-----BEGIN PUBLIC KEY-----')) return $publicKey;

        $str = chunk_split($publicKey, 64, PHP_EOL);//Add a \n after every 64 characters
        $publicKey = "-----BEGIN PUBLIC KEY-----".PHP_EOL.$str."-----END PUBLIC KEY-----";

        return $publicKey;
    }

    /**
     * format the private key
     * @param $privateKey string private key
     * @return string
     */
    public static function FormatterPrivateKey($privateKey){
        if (false !==strpos($privateKey, '-----BEGIN RSA PRIVATE KEY-----')) return $privateKey;

        $str = chunk_split($privateKey, 64, PHP_EOL);//Add a \n after every 64 characters
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----".PHP_EOL.$str."-----END RSA PRIVATE KEY-----";

        return $privateKey;
    }

    /**
     *  Private Key Encryption (Segmented Encryption)
     *  @param str    need to encrypt string
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
     *  Public key decryption (segment decryption)
     *  @encrypstr  encrypted string
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