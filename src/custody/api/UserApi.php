<?php

namespace Chainup\Waas\Custody\Api;

use Chainup\Waas\Utils\Result;

/**
 * User API - User management and registration operations
 * Provides methods for user registration, information retrieval
 * 
 * @package Chainup\Waas\Custody\Api
 */
class UserApi extends BaseApi
{
    /**
     * Register a new user using mobile phone
     * 
     * @param string $country Country code (e.g., '86')
     * @param string $mobile Mobile phone number
     * @return Result User registration result containing uid
     * @throws \Exception On request failure
     */
    public function registerMobileUser($country, $mobile)
    {
        $params = array(
            'country' => $country,
            'mobile' => $mobile
        );
        return $this->post('/user/createUser', $params);
    }

    /**
     * Register a new user using email
     * 
     * @param string $email Email address
     * @return Result User registration result containing uid
     * @throws \Exception On request failure
     */
    public function registerEmailUser($email)
    {
        $params = array(
            'email' => $email
        );
        return $this->post('/user/registerEmail', $params);
    }

    /**
     * Get user information by mobile phone
     * 
     * @param string $country Country code (e.g., '86')
     * @param string $mobile Mobile phone number
     * @return Result User information
     * @throws \Exception On request failure
     */
    public function getMobileUser($country, $mobile)
    {
        $params = array(
            'country' => $country,
            'mobile' => $mobile
        );
        return $this->post('/user/info', $params);
    }

    /**
     * Get user information by email
     * 
     * @param string $email User email
     * @return Result User information
     * @throws \Exception On request failure
     */
    public function getEmailUser($email)
    {
        $params = array(
            'email' => $email
        );
        return $this->post('/user/info', $params);
    }

    /**
     * Sync user list by max ID (pagination)
     * 
     * @param int $maxId Maximum user ID for pagination (0 for first sync)
     * @return Result Synced user list
     * @throws \Exception On request failure
     */
    public function syncUserList($maxId = 0)
    {
        $params = array(
            'max_id' => $maxId
        );
        return $this->post('/user/syncList', $params);
    }
}
