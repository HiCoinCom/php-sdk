<?php

namespace Chainup\Waas\Custody\Api;

use Chainup\Waas\Utils\Result;

/**
 * Account API - Account and balance management operations
 * Provides methods for querying account balances and deposit addresses
 * 
 * @package Chainup\Waas\Custody\Api
 */
class AccountApi extends BaseApi
{
    /**
     * Get user account balance for a specific cryptocurrency
     * 
     * @param int $uid User ID
     * @param string $symbol Cryptocurrency symbol (e.g., 'BTC', 'ETH')
     * @return Result Account balance information
     * @throws \Exception On request failure
     */
    public function getUserAccount($uid, $symbol)
    {
        $params = array(
            'uid' => $uid,
            'symbol' => $symbol
        );
        return $this->post('/account/getByUidAndSymbol', $params);
    }

    /**
     * Get user deposit address for a specific cryptocurrency
     * 
     * @param int $uid User ID
     * @param string $symbol Cryptocurrency symbol (e.g., 'BTC', 'ETH')
     * @return Result Deposit address information
     * @throws \Exception On request failure
     */
    public function getUserAddress($uid, $symbol)
    {
        $params = array(
            'uid' => $uid,
            'symbol' => $symbol
        );
        return $this->post('/account/getDepositAddress', $params);
    }

    /**
     * Get company (merchant) account balance for a specific cryptocurrency
     * 
     * @param string $symbol Cryptocurrency symbol (e.g., 'BTC', 'ETH')
     * @return Result Company account information
     * @throws \Exception On request failure
     */
    public function getCompanyAccount($symbol)
    {
        $params = array(
            'symbol' => $symbol
        );
        return $this->post('/account/getCompanyBySymbol', $params);
    }

    /**
     * Get user address information by address
     * 
     * @param string $address Blockchain address to query
     * @return Result Address details
     * @throws \Exception On request failure
     */
    public function getUserAddressInfo($address)
    {
        $params = array(
            'address' => $address
        );
        return $this->post('/account/getDepositAddressInfo', $params);
    }

    /**
     * Sync user address list by max ID (pagination)
     * 
     * @param int $maxId Maximum address ID for pagination (0 for first sync)
     * @return Result Synced user address list
     * @throws \Exception On request failure
     */
    public function syncUserAddressList($maxId = 0)
    {
        $params = array(
            'max_id' => $maxId
        );
        return $this->post('/address/syncList', $params);
    }
}
