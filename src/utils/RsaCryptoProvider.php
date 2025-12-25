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
     * @var string RSA private key (PEM format) - for request encryption
     */
    private $privateKey;

    /**
     * @var string RSA public key (PEM format) - for response decryption
     */
    private $publicKey;

    /**
     * @var string RSA private key (PEM format) - for transaction signing
     */
    private $signPrivateKey;

    /**
     * Constructor
     * 
     * @param array $options Configuration options
     *   - string privateKey: RSA private key for request encryption
     *   - string publicKey: RSA public key for response decryption
     *   - string signPrivateKey: RSA private key for transaction signing (optional)
     */
    public function __construct($options = array())
    {
        $privateKey = isset($options['privateKey']) ? $options['privateKey'] : '';
        $publicKey = isset($options['publicKey']) ? $options['publicKey'] : '';
        $signPrivateKey = isset($options['signPrivateKey']) ? $options['signPrivateKey'] : '';

        $this->privateKey = RsaUtil::formatPrivateKey($privateKey);
        $this->publicKey = RsaUtil::formatPublicKey($publicKey);
        
        // Use signPrivateKey if provided, otherwise use privateKey
        if (!empty($signPrivateKey)) {
            $this->signPrivateKey = RsaUtil::formatPrivateKey($signPrivateKey);
        } else {
            $this->signPrivateKey = $this->privateKey;
        }
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

    /**
     * Sign data with private key using RSA-SHA256
     * Pure RSA signature function - does not process parameters
     * 
     * @param string $data Data to sign (can be plain text or MD5 hash)
     * @param bool $base64Encode Whether to base64 encode the signature (default: true)
     * @return string Signature (base64 encoded if $base64Encode is true, otherwise hex)
     * @throws \Exception If signing fails
     */
    public function sign($data, $base64Encode = true)
    {
        // Use signPrivateKey if available, otherwise use privateKey
        $keyToUse = !empty($this->signPrivateKey) ? $this->signPrivateKey : $this->privateKey;
        
        if (empty($keyToUse)) {
            throw new \Exception('Private key is required for signing');
        }

        $privateKeyResource = openssl_pkey_get_private($keyToUse);
        if (!$privateKeyResource) {
            throw new \Exception('Invalid private key for signing');
        }

        $signature = '';
        $success = openssl_sign($data, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);

        if (!$success) {
            throw new \Exception('Failed to sign data');
        }

        return $base64Encode ? base64_encode($signature) : bin2hex($signature);
    }
}
