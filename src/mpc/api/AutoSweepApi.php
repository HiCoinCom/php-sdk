<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Auto Sweep API - MPC auto-sweep management operations
 * Provides methods for configuring and querying auto-sweep operations
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class AutoSweepApi extends MpcBaseApi
{
    /**
     * Gets auto-sweep wallets
     * 
     * @param string $symbol Unique identifier for the coin (e.g., "USDTERC20")
     * @return array Auto-sweep wallet information
     * @throws \Exception On request failure
     */
    public function autoCollectSubWallets($symbol)
    {
        if (empty($symbol)) {
            throw new \Exception("Parameter \"symbol\" is required");
        }

        $response = $this->get("/api/mpc/auto_collect/sub_wallets", array(
            "symbol" => $symbol
        ));
        return $this->validateResponse($response);
    }

    /**
     * Configures auto-sweep for coin
     * 
     * @param array $params Configuration parameters
     *   - symbol (string): Unique identifier for the coin (e.g., "USDTERC20") [required]
     *   - collect_min (string): Minimum amount for auto-sweep (up to 6 decimal places) [required]
     *   - fueling_limit (string): Maximum miner fee amount for auto-sweep [required]
     * @return array Configuration result
     * @throws \Exception On request failure
     */
    public function setAutoCollectSymbol($params)
    {
        if (empty($params["symbol"]) || empty($params["collect_min"]) || 
            empty($params["fueling_limit"])) {
            throw new \Exception("Required parameters: symbol, collect_min, fueling_limit");
        }

        $response = $this->post("/api/mpc/auto_collect/symbol/set", array(
            "symbol" => $params["symbol"],
            "collect_min" => $params["collect_min"],
            "fueling_limit" => $params["fueling_limit"]
        ));
        return $this->validateResponse($response);
    }

    /**
     * Synchronizes auto sweeping records
     * 
     * @param int $maxId Starting ID for sweeping records [optional, default: 0]
     * @return array Synchronized auto-sweep records
     * @throws \Exception On request failure
     */
    public function syncAutoCollectRecords($maxId = 0)
    {
        $response = $this->get("/api/mpc/billing/sync_auto_collect_list", array(
            "max_id" => $maxId
        ));
        return $this->validateResponse($response);
    }
}
