<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Notify API - MPC notification handling operations
 * Provides methods for decrypting MPC async notifications (webhooks)
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class NotifyApi extends MpcBaseApi
{
    /**
     * Decrypts deposit and withdrawal notification parameters
     * Used to decrypt encrypted notification data received from MPC callbacks
     * 
     * @param string $cipher Encrypted notification data
     * @return array|null Decrypted notification arguments, or null if decryption fails
     * @example
     * $notifyData = $notifyApi->notifyRequest($encryptedData);
     * if ($notifyData) {
     *     echo "Notify type: " . $notifyData["side"]; // "deposit" or "withdraw"
     *     echo "Sub wallet ID: " . $notifyData["sub_wallet_id"];
     * }
     */
    public function notifyRequest($cipher)
    {
        if (empty($cipher)) {
            if ($this->config->debug) {
                $this->logger->debug("[MpcNotify] Cipher cannot be empty");
            }
            return null;
        }

        try {
            // Decrypt the cipher text using crypto provider
            $raw = $this->cryptoProvider->decryptWithPublicKey($cipher);
            
            if ($this->config->debug) {
                $this->logger->debug("[MpcNotify] Decrypted data: " . $raw);
            }

            if (!$raw) {
                $this->logger->error("[MpcNotify] Decrypt cipher returned null");
                return null;
            }

            // Parse JSON to notification arguments
            $notify = json_decode($raw, true);
            if (!$notify || json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error("[MpcNotify] JSON decode failed: " . json_last_error_msg());
                return null;
            }

            return $notify;
        } catch (\Exception $error) {
            $this->logger->error("[MpcNotify] Failed to decrypt notification: " . $error->getMessage());
            return null;
        }
    }
}
