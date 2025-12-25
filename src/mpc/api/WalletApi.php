<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Wallet API - MPC wallet management operations
 * Provides methods for creating and managing MPC wallets
 * Uses snake_case naming for parameters (same as Java SDK)
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class WalletApi extends MpcBaseApi
{
    /**
     * Creates a new wallet
     * Pass in the specified wallet name to create a new wallet for the main wallet
     * 
     * @param array $params Wallet creation parameters (snake_case naming)
     *   - sub_wallet_name (string): Wallet name (max 50 characters) [required]
     *   - app_show_status (int): Display status: 1 (show), 2 (hide, default) [optional]
     * @return array Created wallet information with sub_wallet_id
     * @throws \Exception On request failure
     * @example
     * $wallet = $walletApi->createWallet(array(
     *     'sub_wallet_name' => 'My Wallet',
     *     'app_show_status' => 1
     * ));
     */
    public function createWallet($params)
    {
        if (empty($params['sub_wallet_name'])) {
            throw new \Exception('Parameter "sub_wallet_name" is required');
        }

        if (strlen($params['sub_wallet_name']) > 50) {
            throw new \Exception('Wallet name cannot be longer than 50 characters');
        }

        $response = $this->post('/api/mpc/sub_wallet/create', $params);
        return $this->validateResponse($response);
    }

    /**
     * Creates a wallet address
     * Create an address for a specified wallet and coin; the same wallet can have multiple addresses
     * 
     * @param array $params Address creation parameters (snake_case naming)
     *   - sub_wallet_id (int): Wallet ID [required]
     *   - symbol (string): Unique identifier for the coin (e.g., "ETH") [required]
     * @return array Created address information
     * @throws \Exception On request failure
     * @example
     * $address = $walletApi->createWalletAddress(array(
     *     'sub_wallet_id' => 123,
     *     'symbol' => 'ETH'
     * ));
     */
    public function createWalletAddress($params)
    {
        if (empty($params['sub_wallet_id'])) {
            throw new \Exception('Parameter "sub_wallet_id" is required');
        }

        if (empty($params['symbol'])) {
            throw new \Exception('Parameter "symbol" is required');
        }

        $response = $this->post('/api/mpc/sub_wallet/create/address', $params);
        return $this->validateResponse($response);
    }

    /**
     * Queries wallet address
     * List of wallet addresses
     * 
     * @param array $params Query parameters (snake_case naming)
     *   - sub_wallet_id (int): Wallet ID [required]
     *   - symbol (string): Unique identifier for the coin (e.g., "ETH") [required]
     *   - max_id (int): Starting address ID [optional, default: 0]
     * @return array Wallet address list
     * @throws \Exception On request failure
     * @example
     * $addresses = $walletApi->queryWalletAddress(array(
     *     'sub_wallet_id' => 123,
     *     'symbol' => 'ETH',
     *     'max_id' => 0
     * ));
     */
    public function queryWalletAddress($params)
    {
        if (empty($params['sub_wallet_id'])) {
            throw new \Exception('Parameter "sub_wallet_id" is required');
        }

        if (empty($params['symbol'])) {
            throw new \Exception('Parameter "symbol" is required');
        }

        $response = $this->post('/api/mpc/sub_wallet/get/address/list', $params);
        return $this->validateResponse($response);
    }

    /**
     * Gets wallet assets
     * Get the account assets under the specified wallet and coin
     * 
     * @param array $params Query parameters (snake_case naming)
     *   - sub_wallet_id (int): Wallet ID [required]
     *   - symbol (string): Unique identifier for the coin (e.g., "ETH") [required]
     * @return array Wallet asset information
     * @throws \Exception On request failure
     * @example
     * $assets = $walletApi->getWalletAssets(array(
     *     'sub_wallet_id' => 123,
     *     'symbol' => 'ETH'
     * ));
     */
    public function getWalletAssets($params)
    {
        if (empty($params['sub_wallet_id'])) {
            throw new \Exception('Parameter "sub_wallet_id" is required');
        }

        if (empty($params['symbol'])) {
            throw new \Exception('Parameter "symbol" is required');
        }

        $response = $this->get('/api/mpc/sub_wallet/assets', $params);
        return $this->validateResponse($response);
    }

    /**
     * Modifies the wallet display status
     * The display of the specified wallet in the App and web portal is essential for initiating transactions
     * 
     * @param array $params Update parameters (snake_case naming)
     *   - sub_wallet_ids (string): Wallet IDs (comma-separated string, e.g., "123,456") [required]
     *   - app_show_status (int): Display status: 1 (show), 2 (hide) [required]
     * @return Result Update result
     * @throws \Exception On request failure
     * @example
     * $result = $walletApi->changeWalletShowStatus(array(
     *     'sub_wallet_ids' => '123,456',
     *     'app_show_status' => 1
     * ));
     */
    public function changeWalletShowStatus($params)
    {
        if (empty($params['sub_wallet_ids'])) {
            throw new \Exception('Parameter "sub_wallet_ids" is required');
        }

        if(is_array($params['sub_wallet_ids'])) {
            $params['sub_wallet_ids'] = implode(',', $params['sub_wallet_ids']);
        }

        if (empty($params['app_show_status']) || 
            ($params['app_show_status'] !== 1 && $params['app_show_status'] !== 2)) {
            throw new \Exception('Parameter "app_show_status" is required and must be 1 or 2');
        }

        $response = $this->post('/api/mpc/sub_wallet/change_show_status', $params);
        return $this->validateResponse($response);
    }

    /**
     * Verifies address information
     * Input a specific address and get the response of the corresponding custody user and currency information
     * 
     * @param array $params Query parameters (snake_case naming)
     *   - address (string): Any address [required]
     *   - memo (string): If it's a Memo type, input the memo [optional]
     * @return array Address information
     * @throws \Exception On request failure
     * @example
     * $info = $walletApi->walletAddressInfo(array(
     *     'address' => '0x123...',
     *     'memo' => 'optional-memo'
     * ));
     */
    public function walletAddressInfo($params)
    {
        if (empty($params['address'])) {
            throw new \Exception('Parameter "address" is required');
        }

        $response = $this->get('/api/mpc/sub_wallet/address/info', $params);
        return $this->validateResponse($response);
    }
}
