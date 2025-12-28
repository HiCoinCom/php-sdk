<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Web3 API - MPC Web3 transaction operations
 * Provides methods for creating, accelerating, and querying Web3 transactions
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class Web3Api extends MpcBaseApi
{
    /**
     * Creates a Web3 transaction
     * 
     * @param array $params Transaction parameters
     *   - request_id (string): Unique request ID [required]
     *   - sub_wallet_id (int): Sub-wallet ID [required]
     *   - main_chain_symbol (string): Main chain coin symbol, e.g. "ETH" [required]
     *   - interactive_contract (string): Interactive contract address [required]
     *   - amount (string): Transfer amount [required]
     *   - gas_price (string): Gas price in Gwei [required]
     *   - gas_limit (string): Gas limit [required]
     *   - input_data (string): Hexadecimal data for contract transaction [required]
     *   - trans_type (string): Transaction type: 0=Authorization, 1=Other [required]
     *   - from (string): Transaction initiation address [optional]
     *   - dapp_name (string): Dapp name [optional]
     *   - dapp_url (string): Dapp URL [optional]
     *   - dapp_img (string): Dapp image [optional]
     *   - need_transaction_sign (bool): Whether transaction signature is required [optional, default: false]
     * @return array Created transaction result
     * @throws \Exception On request failure
     * @example
     * $result = $web3Api->createWeb3Trans(array(
     *     'request_id' => 'unique-id',
     *     'sub_wallet_id' => 123,
     *     'main_chain_symbol' => 'ETH',
     *     'interactive_contract' => '0x123...',
     *     'amount' => '1000000000000000000',
     *     'gas_price' => '20',
     *     'gas_limit' => '21000',
     *     'input_data' => '0x',
     *     'trans_type' => '1'
     * ));
     */
    public function createWeb3Trans($params)
    {
        // Validate required parameters
        if (empty($params["request_id"]) || empty($params["sub_wallet_id"]) || 
            empty($params["main_chain_symbol"]) || empty($params["interactive_contract"]) ||
            !isset($params["amount"]) || empty($params["gas_price"]) || 
            empty($params["gas_limit"]) || !isset($params["input_data"]) || 
            !isset($params["trans_type"])) {
            throw new \Exception("Required parameters: request_id, sub_wallet_id, main_chain_symbol, interactive_contract, amount, gas_price, gas_limit, input_data, trans_type");
        }

        $needTransactionSign = !empty($params["need_transaction_sign"]);

        // Check if signPrivateKey is configured when signature is required
        if ($needTransactionSign && empty($this->config->signPrivateKey)) {
            throw new \Exception('MPC web3 transaction requires signPrivateKey in config when need_transaction_sign is true');
        }

        $requestData = array(
            "request_id" => $params["request_id"],
            "sub_wallet_id" => $params["sub_wallet_id"],
            "main_chain_symbol" => $params["main_chain_symbol"],
            "interactive_contract" => $params["interactive_contract"],
            "amount" => $params["amount"],
            "gas_price" => $params["gas_price"],
            "gas_limit" => $params["gas_limit"],
            "input_data" => $params["input_data"],
            "trans_type" => $params["trans_type"]
        );

        // Add optional parameters
        if (!empty($params["from"])) {
            $requestData["from"] = $params["from"];
        }

        if (!empty($params["dapp_name"])) {
            $requestData["dapp_name"] = $params["dapp_name"];
        }

        if (!empty($params["dapp_url"])) {
            $requestData["dapp_url"] = $params["dapp_url"];
        }

        if (!empty($params["dapp_img"])) {
            $requestData["dapp_img"] = $params["dapp_img"];
        }

        // Generate signature if needed
        if ($needTransactionSign) {
            $signParams = array(
                'request_id' => $params['request_id'],
                'sub_wallet_id' => (string)$params['sub_wallet_id'],
                'main_chain_symbol' => $params['main_chain_symbol'],
                'interactive_contract' => $params['interactive_contract'],
                'amount' => $params['amount'],
                'input_data' => $params['input_data']
            );

            $sign = $this->generateWeb3Sign($signParams);
            if (empty($sign)) {
                throw new \Exception('Failed to generate web3 transaction signature');
            }

            $requestData['sign'] = $sign;
        }

        $response = $this->post("/api/mpc/web3/trans/create", $requestData);
        return $this->validateResponse($response);
    }

    /**
     * Accelerates a Web3 transaction
     * 
     * @param array $params Acceleration parameters
     *   - trans_id (int): Web3 transaction ID [required]
     *   - gas_price (string): New gas price in Gwei (higher than original) [required]
     *   - gas_limit (string): New gas limit [required]
     * @return array Acceleration result
     * @throws \Exception On request failure
     * @example
     * $result = $web3Api->accelerationWeb3Trans(array(
     *     'trans_id' => 12345,
     *     'gas_price' => '50',
     *     'gas_limit' => '21000'
     * ));
     */
    public function accelerationWeb3Trans($params)
    {
        if (empty($params["trans_id"]) || empty($params["gas_price"]) || empty($params["gas_limit"])) {
            throw new \Exception("Required parameters: trans_id, gas_price, gas_limit");
        }

        $requestData = array(
            "trans_id" => $params["trans_id"],
            "gas_price" => $params["gas_price"],
            "gas_limit" => $params["gas_limit"]
        );

        $response = $this->post("/api/mpc/web3/pending", $requestData);
        return $this->validateResponse($response);
    }

    /**
     * Gets Web3 transaction records
     * Get all Web3 transaction records under a wallet, maximum of 100 records
     * 
     * @param array $requestIds Request IDs array (up to 100) [required]
     * @return array Web3 transaction records
     * @throws \Exception On request failure
     * @example
     * $records = $web3Api->getWeb3Records(array('req-1', 'req-2'));
     */
    public function getWeb3Records($requestIds = array())
    {
        if (empty($requestIds) || !is_array($requestIds) || 
            count($requestIds) === 0) {
            throw new \Exception("Parameter \"request_ids\" is required and must be a non-empty array");
        }

        $response = $this->get("/api/mpc/web3/trans_list", array(
            "ids" => implode(",", $requestIds)
        ));
        return $this->validateResponse($response);
    }

    /**
     * Synchronizes Web3 transaction records
     * Get all Web3 transaction records under a wallet, maximum of 100 records
     * 
     * @param int $maxId Starting ID of Web3 transactions [optional, default: 0]
     * @return array Synchronized Web3 records
     * @throws \Exception On request failure
     * @example
     * $records = $web3Api->syncWeb3Records(0);
     */
    public function syncWeb3Records($maxId = 0)
    {
        $response = $this->get("/api/mpc/web3/sync_trans_list", array(
            "max_id" => $maxId
        ));
        return $this->validateResponse($response);
    }
}
