<?php

namespace Chainup\Waas\Utils;

/**
 * Crypto Provider Interface
 * Defines encryption and decryption methods
 * Allows users to implement custom encryption (different algorithms, HSM, etc.)
 * 
 * @package Chainup\Waas\Utils
 */
interface CryptoProviderInterface
{
    /**
     * Encrypt data with private key
     * Used for signing requests to prove identity
     * 
     * @param string $data Data to encrypt
     * @return string Encrypted data (usually base64 encoded)
     * @throws \Exception If encryption fails
     */
    public function encryptWithPrivateKey($data);

    /**
     * Decrypt data with public key
     * Used for verifying and decrypting responses from server
     * 
     * @param string $encryptedData Encrypted data (usually base64 encoded)
     * @return string Decrypted data
     * @throws \Exception If decryption fails
     */
    public function decryptWithPublicKey($encryptedData);
}
