<?php

namespace Chainup\Waas\Utils;

/**
 * Crypto Provider Interface
 * Defines encryption, decryption, and signing methods
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

    /**
     * Sign data with private key
     * Used for signing MPC transactions (withdraw, web3, etc.)
     * Pure RSA-SHA256 signature function
     * 
     * @param string $data Data to sign (can be plain text or hash)
     * @param bool $base64Encode Whether to base64 encode the signature (default: true)
     * @return string Signature (base64 or hex encoded)
     * @throws \Exception If signing fails
     */
    public function sign($data, $base64Encode = true);
}
