<?php

namespace Chainup\Waas\Custody\Api;

use Chainup\Waas\Utils\Result;

/**
 * Billing API - Deposit, withdrawal and miner fee operations
 * Provides methods for withdraw requests and querying deposit/withdrawal records
 * 
 * @package Chainup\Waas\Custody\Api
 */
class BillingApi extends BaseApi
{
    /**
     * Create a withdrawal request
     * 
     * @param string $requestId Unique request ID (merchant generated)
     * @param int $fromUid Source user ID
     * @param string $toAddress Destination address
     * @param string $amount Withdrawal amount
     * @param string $symbol Cryptocurrency symbol (e.g., 'BTC', 'ETH')
     * @param string $memo Address memo/tag (for coins like XRP, EOS) - optional
     * @param string $remark Additional remark - optional
     * @return Result Withdrawal result
     * @throws \Exception On request failure
     */
    public function withdraw($requestId, $fromUid, $toAddress, $amount, $symbol, $memo = '', $remark = '')
    {
        $params = array(
            'request_id' => $requestId,
            'from_uid' => $fromUid,
            'to_address' => $toAddress,
            'amount' => $amount,
            'symbol' => $symbol
        );
        
        if (!empty($memo)) {
            $params['memo'] = $memo;
        }
        if (!empty($remark)) {
            $params['remark'] = $remark;
        }
        
        return $this->post('/billing/withdraw', $params);
    }

    /**
     * Get withdrawal records by request IDs
     * 
     * @param array $requestIdList List of request IDs
     * @return Result Withdrawal records
     * @throws \Exception On request failure
     */
    public function withdrawList($requestIdList)
    {
        if (empty($requestIdList)) {
            $result = new Result();
            $result->setCode(-2);
            $result->setMsg('Request ID list cannot be empty');
            return $result;
        }

        $params = array(
            'ids' => is_array($requestIdList) ? implode(',', $requestIdList) : $requestIdList
        );
        return $this->post('/billing/withdrawList', $params);
    }

    /**
     * Sync withdrawal records by max ID (pagination)
     * 
     * @param int $maxId Maximum transaction ID for pagination
     * @return Result Synced withdrawal records
     * @throws \Exception On request failure
     */
    public function syncWithdrawList($maxId = 0)
    {
        $params = array(
            'max_id' => $maxId
        );
        return $this->post('/billing/syncWithdrawList', $params);
    }

    /**
     * Get deposit records by WaaS IDs
     * 
     * @param array $waasIdList List of WaaS deposit IDs
     * @return Result Deposit records
     * @throws \Exception On request failure
     */
    public function depositList($waasIdList)
    {
        if (empty($waasIdList)) {
            $result = new Result();
            $result->setCode(-2);
            $result->setMsg('WaaS ID list cannot be empty');
            return $result;
        }

        $params = array(
            'ids' => is_array($waasIdList) ? implode(',', $waasIdList) : $waasIdList
        );
        return $this->post('/billing/depositList', $params);
    }

    /**
     * Sync deposit records by max ID (pagination)
     * 
     * @param int $maxId Maximum transaction ID for pagination
     * @return Result Synced deposit records
     * @throws \Exception On request failure
     */
    public function syncDepositList($maxId = 0)
    {
        $params = array(
            'max_id' => $maxId
        );
        return $this->post('/billing/syncDepositList', $params);
    }

    /**
     * Get miner fee records by WaaS IDs
     * 
     * @param array $waasIdList List of WaaS transaction IDs
     * @return Result Miner fee records
     * @throws \Exception On request failure
     */
    public function minerFeeList($waasIdList)
    {
        if (empty($waasIdList)) {
            $result = new Result();
            $result->setCode(-2);
            $result->setMsg('WaaS ID list cannot be empty');
            return $result;
        }

        $params = array(
            'ids' => is_array($waasIdList) ? implode(',', $waasIdList) : $waasIdList
        );
        return $this->post('/billing/minerFeeList', $params);
    }

    /**
     * Sync miner fee records by max ID (pagination)
     * 
     * @param int $maxId Maximum transaction ID for pagination
     * @return Result Synced miner fee records
     * @throws \Exception On request failure
     */
    public function syncMinerFeeList($maxId = 0)
    {
        $params = array(
            'max_id' => $maxId
        );
        return $this->post('/billing/syncMinerFeeList', $params);
    }
}
