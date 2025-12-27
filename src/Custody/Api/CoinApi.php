<?php

namespace Chainup\Waas\Custody\Api;

use Chainup\Waas\Utils\Result;

/**
 * Coin API - Cryptocurrency information operations
 * Provides methods for querying supported cryptocurrencies
 * 
 * @package Chainup\Waas\Custody\Api
 */
class CoinApi extends BaseApi
{
    /**
     * Get supported coin list
     * Retrieves information about all cryptocurrencies supported by the platform
     * 
     * @return Result List of supported coins with details
     * @throws \Exception On request failure
     */
    public function getCoinList()
    {
        return $this->post('/user/getCoinList', array());
    }
}
