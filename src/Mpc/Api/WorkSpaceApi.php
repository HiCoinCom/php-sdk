<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * WorkSpace API - MPC workspace management operations
 * Provides methods for querying supported chains, coins, and blockchain information
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class WorkSpaceApi extends MpcBaseApi
{
    /**
     * Gets supported main chains
     * 
     * @return array Supported main chains
     * @throws \Exception On request failure
     */
    public function getSupportMainChain()
    {
        $response = $this->get("/api/mpc/wallet/open_coin");
        return $this->validateResponse($response);
    }

    /**
     * Gets MPC workspace coin details
     * 
     * @param array $params Query parameters (snake_case naming)
     *   - symbol (string): Unique identifier for the coin (e.g., "USDTERC20") [optional]
     *   - base_symbol (string): Main chain coin symbol (e.g., "ETH") [optional]
     *   - open_chain (bool): true: opened coins, false: unopened coins [optional]
     *   - max_id (int): Starting ID of the currency [optional]
     *   - limit (int): Number of currencies to get (default: 1500) [optional]
     * @return array Coin details
     * @throws \Exception On request failure
     */
    public function getCoinDetails($params = array())
    {
        $response = $this->get("/api/mpc/coin_list", $params);
        return $this->validateResponse($response);
    }

    /**
     * Gets last block height
     * 
     * @param string $baseSymbol Main chain coin symbol (e.g., "ETH", "BTC")
     * @return array Block height information
     * @throws \Exception On request failure
     */
    public function getLastBlockHeight($baseSymbol)
    {
        if (empty($baseSymbol)) {
            throw new \Exception("Parameter \"baseSymbol\" is required");
        }

        $response = $this->get("/api/mpc/chain_height", array(
            "base_symbol" => $baseSymbol
        ));
        return $this->validateResponse($response);
    }
}
