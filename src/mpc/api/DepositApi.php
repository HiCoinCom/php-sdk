<?php

namespace Chainup\Waas\Mpc\Api;

/**
 * Deposit API - MPC deposit management operations
 * Provides methods for querying deposit records
 * 
 * @package Chainup\Waas\Mpc\Api
 */
class DepositApi extends MpcBaseApi
{
    /**
     * Gets receiving records
     * Get all wallet receiving records under the workspace, and return up to 100 records
     * 
     * @param array $params Query parameters
     *   - ids (array): Receiving IDs (up to 100) [required]
     * @return array Deposit records
     * @throws \Exception On request failure
     * @example
     * $deposits = $depositApi->getDepositRecords(array(
     *     'ids' => array(123, 456, 789)
     * ));
     */
    public function getDepositRecords($ids = array())
    {
        if (empty($ids) || !is_array($ids) || count($ids) === 0) {
            throw new \Exception('Parameter "ids" is required and must be a non-empty array');
        }

        $response = $this->get('/api/mpc/billing/deposit_list', array(
            'ids' => implode(',', $ids)
        ));
        return $this->validateResponse($response);
    }

    /**
     * Synchronizes transfer(deposit) records
     * Get all wallet receiving records under the workspace
     * 
     * @param array $params Query parameters
     *   - max_id (int): Receiving record initial ID [optional, default: 0]
     * @return array Synchronized deposit records
     * @throws \Exception On request failure
     * @example
     * $deposits = $depositApi->syncDepositRecords(array('max_id' => 0));
     */
    public function syncDepositRecords($maxId = 0)
    {
        $response = $this->get('/api/mpc/billing/sync_deposit_list', array(
            'max_id' => $maxId
        ));
        return $this->validateResponse($response);
    }
}