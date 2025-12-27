<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Withdraw API - MPC withdrawal management operations
 * Provides methods for initiating withdrawals and querying withdrawal records
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class WithdrawApi extends MpcBaseApi
{
    /**
     * Initiates a transfer (withdrawal)
     * 
     * @param array $params Withdrawal parameters
     *   - request_id (string): Unique request ID [required]
     *   - sub_wallet_id (int): Sub-wallet ID [required]
     *   - symbol (string): Coin symbol (e.g., "USDTERC20") [required]
     *   - amount (string): Withdrawal amount [required]
     *   - address_to (string): Destination address [required]
     *   - from (string): Specify the transfer coin address [optional]
     *   - memo (string): Address memo (for coins that require it) [optional]
     *   - remark (string): Withdrawal remark [optional]
     *   - outputs (string): UTXO outputs (for BTC-like coins) [optional]
     *   - need_transaction_sign (bool): Whether to sign the transaction [optional, default: false]
     * @return array Withdrawal result with withdraw_id
     * @throws \Exception On request failure
     * @example
     * $result = $withdrawApi->withdraw(array(
     *     'request_id' => 'unique-id',
     *     'sub_wallet_id' => 123,
     *     'symbol' => 'ETH',
     *     'amount' => '0.1',
     *     'address_to' => '0x123...'
     * ));
     */
    public function withdraw($params)
    {
        if (empty($params['request_id']) || empty($params['sub_wallet_id']) ||
            empty($params['symbol']) || empty($params['amount']) || empty($params['address_to'])) {
            throw new \Exception('Required parameters: request_id, sub_wallet_id, symbol, amount, address_to');
        }

        $needTransactionSign = !empty($params['need_transaction_sign']);
        
        // Check if signPrivateKey is configured when signature is required
        if ($needTransactionSign && empty($this->config->signPrivateKey)) {
            throw new \Exception('MPC withdrawal requires signPrivateKey in config when need_transaction_sign is true');
        }

        $requestData = array(
            'request_id' => $params['request_id'],
            'sub_wallet_id' => $params['sub_wallet_id'],
            'symbol' => $params['symbol'],
            'amount' => $params['amount'],
            'address_to' => $params['address_to']
        );

        if (!empty($params['from'])) {
            $requestData['from'] = $params['from'];
        }

        if (!empty($params['memo'])) {
            $requestData['memo'] = $params['memo'];
        }

        if (!empty($params['remark'])) {
            $requestData['remark'] = $params['remark'];
        }

        if (!empty($params['outputs'])) {
            $requestData['outputs'] = $params['outputs'];
        }

        // Generate signature if needed
        if ($needTransactionSign) {
            // Build signature parameters (specific fields only)
            $signParams = array(
                'request_id' => $params['request_id'],
                'sub_wallet_id' => (string)$params['sub_wallet_id'],
                'symbol' => $params['symbol'],
                'address_to' => $params['address_to'],
                'amount' => $params['amount'],
                'memo' => isset($params['memo']) ? $params['memo'] : '',
                'outputs' => isset($params['outputs']) ? $params['outputs'] : ''
            );
            
            // Use MpcBaseApi's generateWithdrawSign method
            $sign = $this->generateWithdrawSign($signParams);
            if (empty($sign)) {
                throw new \Exception('Failed to generate withdrawal signature');
            }
            
            $requestData['sign'] = $sign;
        }

        $response = $this->post('/api/mpc/billing/withdraw', $requestData);
        return $this->validateResponse($response);
    }

    /**
     * Gets transfer records
     * Get all wallet transfer records under the workspace, and return up to 100 records
     * 
     * @param array $params Query parameters
     *   - request_ids (array): Request IDs (up to 100) [required]
     * @return array Withdrawal records
     * @throws \Exception On request failure
     * @example
     * $records = $withdrawApi->getWithdrawRecords(array(
     *     'request_ids' => array('req-1', 'req-2')
     * ));
     */
    public function getWithdrawRecords($requestids  = array())
    {
        if (empty($requestids) || !is_array($requestids) || 
            count($requestids) === 0) {
            throw new \Exception('Parameter "request_ids" is required and must be a non-empty array');
        }

        $response = $this->get('/api/mpc/billing/withdraw_list', array(
            'ids' => implode(',', $requestids)
        ));
        return $this->validateResponse($response);
    }

    /**
     * Synchronizes transfer(withdraw) records
     * Get all wallet transfer records under the workspace, and return up to 100 records
     * 
     * @param array $params Query parameters
     *   - max_id (int): Starting ID of withdraw records [optional, default: 0]
     * @return array Synchronized withdrawal records
     * @throws \Exception On request failure
     * @example
     * $records = $withdrawApi->syncWithdrawRecords(array('max_id' => 0));
     */
    public function syncWithdrawRecords($maxId = 0)
    {
        $response = $this->get('/api/mpc/billing/sync_withdraw_list', array(
            'max_id' => $maxId
        ));
        return $this->validateResponse($response);
    }
}
