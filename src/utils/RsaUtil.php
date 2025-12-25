<?php

namespace Chainup\Waas\Utils;

/**
 * RSA Encryption/Decryption Utility
 * Provides RSA encryption with private key and decryption with public key
 * Implements segment encryption/decryption for long data (matches Java SDK)
 * 
 * @package Chainup\Waas\Utils
 */
class RsaUtil
{
    /**
     * Max encrypt block size = 256 - 11 (PKCS1 padding) = 245 bytes
     * But to match Java SDK, we use 234 bytes
     */
    const MAX_ENCRYPT_BLOCK = 234;
    
    /**
     * Max decrypt block size = 256 bytes (for 2048-bit RSA key)
     */
    const MAX_DECRYPT_BLOCK = 256;

    /**
     * Format RSA public key to PEM format
     * Adds proper headers and line breaks if not present
     * 
     * @param string $publicKey Public key string
     * @return string Formatted PEM public key
     */
    public static function formatPublicKey($publicKey)
    {
        if (strpos($publicKey, '-----BEGIN PUBLIC KEY-----') !== false) {
            return $publicKey;
        }

        // Remove whitespace
        $publicKey = preg_replace('/\s+/', '', $publicKey);
        
        // Add line breaks every 64 characters
        $publicKey = chunk_split($publicKey, 64, "\n");
        
        // Add PEM headers
        return "-----BEGIN PUBLIC KEY-----\n" . $publicKey . "-----END PUBLIC KEY-----\n";
    }

    /**
     * Format RSA private key to PEM format
     * Adds proper headers and line breaks if not present
     * 
     * @param string $privateKey Private key string
     * @return string Formatted PEM private key
     */
    public static function formatPrivateKey($privateKey)
    {
        if (strpos($privateKey, '-----BEGIN') !== false) {
            return $privateKey;
        }

        // Remove whitespace
        $privateKey = preg_replace('/\s+/', '', $privateKey);
        
        // Add line breaks every 64 characters
        $privateKey = chunk_split($privateKey, 64, "\n");
        
        // Add PEM headers (RSA PRIVATE KEY format)
        return "-----BEGIN RSA PRIVATE KEY-----\n" . $privateKey . "-----END RSA PRIVATE KEY-----\n";
    }

    /**
     * Encrypt data using private key (segment encryption)
     * Matches Java SDK RSAHelper.encryptByPrivateKey()
     * 
     * @param string $data Data to encrypt
     * @param string $privateKey RSA private key in PEM format
     * @return string URL-safe base64 encoded encrypted data
     * @throws \Exception If encryption fails
     */
    public static function encryptWithPrivateKey($data, $privateKey)
    {
        $privateKey = self::formatPrivateKey($privateKey);
        
        $dataLen = strlen($data);
        $encryptedChunks = array();
        $offset = 0;

        // Segment encryption: encrypt in blocks of MAX_ENCRYPT_BLOCK
        while ($offset < $dataLen) {
            $blockSize = min(self::MAX_ENCRYPT_BLOCK, $dataLen - $offset);
            $chunk = substr($data, $offset, $blockSize);
            
            $encrypted = '';
            if (!openssl_private_encrypt($chunk, $encrypted, $privateKey, OPENSSL_PKCS1_PADDING)) {
                throw new \Exception('Failed to encrypt with private key: ' . openssl_error_string());
            }
            
            $encryptedChunks[] = $encrypted;
            $offset += $blockSize;
        }

        // Combine all encrypted chunks
        $combinedEncrypted = implode('', $encryptedChunks);
        
        // Return URL-safe base64 encoded result
        return Base64UrlSafe::encode($combinedEncrypted);
    }

    /**
     * Decrypt data using public key (segment decryption)
     * Matches Java SDK RSAHelper.decryptByPublicKey()
     * 
     * @param string $encryptedData URL-safe base64 encoded encrypted data
     * @param string $publicKey RSA public key in PEM format
     * @return string Decrypted data
     * @throws \Exception If decryption fails
     */
    public static function decryptWithPublicKey($encryptedData, $publicKey)
    {
        $publicKey = self::formatPublicKey($publicKey);
        
        // Decode from URL-safe base64
        $encrypted = Base64UrlSafe::decode($encryptedData);
        
        $dataLen = strlen($encrypted);
        $decryptedChunks = array();
        $offset = 0;

        // Segment decryption: decrypt in blocks of MAX_DECRYPT_BLOCK
        while ($offset < $dataLen) {
            $blockSize = min(self::MAX_DECRYPT_BLOCK, $dataLen - $offset);
            $chunk = substr($encrypted, $offset, $blockSize);
            
            $decrypted = '';
            if (!openssl_public_decrypt($chunk, $decrypted, $publicKey, OPENSSL_PKCS1_PADDING)) {
                throw new \Exception('Failed to decrypt with public key: ' . openssl_error_string());
            }
            
            $decryptedChunks[] = $decrypted;
            $offset += $blockSize;
        }

        // Combine all decrypted chunks
        return implode('', $decryptedChunks);
    }
}
