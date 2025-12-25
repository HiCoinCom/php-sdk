<?php

namespace Chainup\Waas\Custody\Api;

/**
 * Async Notify API - Asynchronous notification management
 * Provides methods for decrypting and managing webhook notifications
 * 
 * @package Chainup\Waas\Custody\Api
 */
class AsyncNotifyApi extends BaseApi
{
    /**
     * Decrypts deposit and withdrawal notification parameters
     * Used to decrypt encrypted notification data received from WaaS callbacks
     * 
     * @param string $cipher Encrypted notification data
     * @return array|null Decrypted notification arguments, or null if decryption fails
     * @example
     * $notifyData = $asyncNotifyApi->notifyRequest($encryptedData);
     * if ($notifyData) {
     *     echo "Notify type: " . $notifyData['side']; // 'deposit' or 'withdraw'
     * }
     */
    public function notifyRequest($cipher)
    {
        if (empty($cipher)) {
            if ($this->config->debug) {
                $this->logger->debug('[AsyncNotify] Cipher cannot be empty');
            }
            return null;
        }

        try {
            // Decrypt the cipher text using public key
            $raw = $this->cryptoProvider->decryptWithPublicKey($cipher);
            
            if ($this->config->debug) {
                $this->logger->debug('[AsyncNotify] Decrypted data: ' . $raw);
            }

            if (!$raw) {
                $this->logger->error('[AsyncNotify] Decrypt cipher returned null');
                return null;
            }

            // Parse JSON to notification arguments
            $notify = json_decode($raw, true);
            if (!$notify || json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('[AsyncNotify] JSON decode failed: ' . json_last_error_msg());
                return null;
            }

            return $notify;
        } catch (\Exception $e) {
            $this->logger->error('[AsyncNotify] Failed to decrypt notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decrypts withdrawal secondary verification request parameters
     * Used to decrypt verification request data for withdrawal operations
     * that require additional confirmation
     * 
     * @param string $cipher Encrypted verification request data
     * @return array|null Decrypted withdrawal arguments, or null if decryption fails
     * @example
     * $withdrawData = $asyncNotifyApi->verifyRequest($encryptedData);
     * if ($withdrawData) {
     *     echo "Withdraw request: " . $withdrawData['request_id'];
     * }
     */
    public function verifyRequest($cipher)
    {
        if (empty($cipher)) {
            if ($this->config->debug) {
                $this->logger->debug('[AsyncNotify] VerifyRequest cipher cannot be empty');
            }
            return null;
        }

        try {
            // Decrypt the cipher text
            $raw = $this->cryptoProvider->decryptWithPublicKey($cipher);
            
            if ($this->config->debug) {
                $this->logger->debug('[AsyncNotify] VerifyRequest decrypted data: ' . $raw);
            }

            if (!$raw) {
                $this->logger->error('[AsyncNotify] VerifyRequest decrypt returned null');
                return null;
            }

            // Parse JSON to withdrawal arguments
            $withdraw = json_decode($raw, true);
            if (!$withdraw || json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error('[AsyncNotify] VerifyRequest JSON decode failed: ' . json_last_error_msg());
                return null;
            }

            return $withdraw;
        } catch (\Exception $e) {
            $this->logger->error('[AsyncNotify] Failed to decrypt verify request: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Encrypts the secondary verification withdrawal response data
     * Used to encrypt the response data when confirming or rejecting
     * a withdrawal that requires secondary verification
     * 
     * @param array $withdraw Withdrawal arguments to encrypt
     * @return string|null Encrypted response data, or null if encryption fails
     * @example
     * $responseData = $asyncNotifyApi->verifyResponse(array(
     *     'request_id' => 'xxx',
     *     'status' => 1 // 1=approve, 2=reject
     * ));
     */
    public function verifyResponse($withdraw)
    {
        if (empty($withdraw)) {
            $this->logger->error('[AsyncNotify] VerifyResponse withdraw cannot be empty');
            return null;
        }

        try {
            // Convert to JSON string
            $withdrawJson = json_encode($withdraw, JSON_UNESCAPED_UNICODE);

            // Encrypt with private key
            $raw = $this->cryptoProvider->encryptWithPrivateKey($withdrawJson);
            
            if (!$raw) {
                $this->logger->error('[AsyncNotify] VerifyResponse encrypt returned null');
                return null;
            }

            return $raw;
        } catch (\Exception $e) {
            $this->logger->error('[AsyncNotify] Failed to encrypt verify response: ' . $e->getMessage());
            return null;
        }
    }
}
