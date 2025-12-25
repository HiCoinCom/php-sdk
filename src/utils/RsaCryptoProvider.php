<?php

namespace Chainup\Waas\Utils;

/**
 * Default RSA Crypto Provider
 * Implements CryptoProviderInterface using RSA encryption
 * Handles RSA encryption and decryption with segment processing
 * Matches Java/JS SDK implementation
 * 
 * @package Chainup\Waas\Utils
 */
class RsaCryptoProvider implements CryptoProviderInterface
{
    /**
     * @var string RSA private key (PEM format)
     */
    private $privateKey;

    /**
     * @var string RSA public key (PEM format)
     */
    private $publicKey;

    /**
     * Constructor
     * 
     * @param string $privateKey RSA private key
     * @param string $publicKey RSA public key
     */
    public function __construct($privateKey, $publicKey)
    {
        $this->privateKey = RsaUtil::formatPrivateKey($privateKey);
        $this->publicKey = RsaUtil::formatPublicKey($publicKey);
    }

    /**
     * Encrypt data with private key (segment encryption)
     * 
     * @param string $data Data to encrypt
     * @return string URL-safe base64 encoded encrypted data
     * @throws \Exception If encryption fails
     */
    public function encryptWithPrivateKey($data)
    {
        return RsaUtil::encryptWithPrivateKey($data, $this->privateKey);
    }

    /**
     * Decrypt data with public key (segment decryption)
     * 
     * @param string $encryptedData URL-safe base64 encoded encrypted data
     * @return string Decrypted data
     * @throws \Exception If decryption fails
     */
    public function decryptWithPublicKey($encryptedData)
    {
        return RsaUtil::decryptWithPublicKey($encryptedData, $this->publicKey);
    }
}
