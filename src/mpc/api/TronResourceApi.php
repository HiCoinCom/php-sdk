<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Tron Resource API - TRON resource delegation operations
 * Provides methods for buying and querying TRON network resources (Energy/Bandwidth)
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class TronResourceApi extends MpcBaseApi
{
    /**
     * Creates delegate (Buy Tron Resource)
     * 
     * @param array $params Delegation parameters
     *   - request_id (string): Unique request ID [required]
     *   - buy_type (int): Buy type [optional]
     *   - resource_type (int): Resource type: 0 for energy, 1 for bandwidth [optional]
     *   - service_charge_type (string): Service charge type [required]
     *   - energy_num (int): Energy amount to purchase [optional]
     *   - net_num (int): Bandwidth amount to purchase [optional]
     *   - address_from (string): Address paying for resources [required]
     *   - address_to (string): Address to receive resources [optional]
     *   - contract_address (string): Contract address [optional]
     * @return array Delegation result with trans_id
     * @throws \Exception On request failure
     */
    public function createTronDelegate($params)
    {
        if (empty($params["request_id"])) {
            throw new \Exception("Required parameter: request_id");
        }
        if (empty($params["address_from"])) {
            throw new \Exception("Required parameter: address_from");
        }
        if (empty($params["service_charge_type"])) {
            throw new \Exception("Required parameter: service_charge_type");
        }

        if (!isset($params["buy_type"])) {
            $params["buy_type"] = 0;
        }

        if (($params["buy_type"] == 0 || $params["buy_type"] == 2)) {
            if (empty($params["address_to"]) || empty($params["contract_address"])) {
                throw new \Exception("For buy_type 0 or 2, address_to and contract_address are required");
            }
        }

        $response = $this->post("/api/mpc/tron/delegate", $params);
        return $this->validateResponse($response);
    }

    /**
     * Gets buy resource records
     * 
     * @param array $requestIds Request IDs array (up to 100) [required]
     * @return array Delegation records
     * @throws \Exception On request failure
     * @example
     * $records = $tronResourceApi->getBuyResourceRecords(array('req1', 'req2'));
     */
    public function getBuyResourceRecords($requestIds)
    {
        if (empty($requestIds) || !is_array($requestIds) || count($requestIds) === 0) {
            throw new \Exception("Parameter \"requestIds\" is required and must be a non-empty array");
        }

        $response = $this->post("/api/mpc/tron/delegate/trans_list", array(
            "ids" => implode(",", $requestIds)
        ));
        return $this->validateResponse($response);
    }

    /**
     * Synchronizes buy resource records
     * 
     * @param int $maxId Starting ID of delegation records [optional, default: 0]
     * @return array Synchronized delegation records
     * @throws \Exception On request failure
     */
    public function syncBuyResourceRecords($maxId = 0)
    {
        $response = $this->post("/api/mpc/tron/delegate/sync_trans_list", array(
            "max_id" => $maxId
        ));
        return $this->validateResponse($response);
    }
}
