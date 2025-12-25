<?php

namespace Chainup\Waas\Mpc;

/**
 * MPC Configuration Class
 * Stores configuration parameters for MPC (Multi-Party Computation) API client
 * This is reserved for future MPC wallet functionality
 * 
 * @package Chainup\Waas\Mpc
 */
class MpcConfig
{
    /**
     * @var string API domain URL
     */
    public $domain;

    /**
     * @var string Application ID
     */
    public $appId;

    /**
     * @var string RSA private key for signing requests
     */
    public $rsaPrivateKey;

    /**
     * @var string WaaS server public key for decrypting responses
     */
    public $waasPublicKey;

    /**
     * @var string API key for authentication
     */
    public $apiKey;

    /**
     * @var bool Debug mode flag
     */
    public $debug = false;

    /**
     * Constructor
     * 
     * @param array $options Configuration options
     */
    public function __construct($options = array())
    {
        $this->domain = isset($options['domain']) ? $options['domain'] : '';
        $this->appId = isset($options['appId']) ? $options['appId'] : '';
        $this->rsaPrivateKey = isset($options['rsaPrivateKey']) ? $options['rsaPrivateKey'] : '';
        $this->waasPublicKey = isset($options['waasPublicKey']) ? $options['waasPublicKey'] : '';
        $this->apiKey = isset($options['apiKey']) ? $options['apiKey'] : '';
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
    }

    /**
     * Validate configuration
     * 
     * @throws \Exception If required fields are missing
     */
    public function validate()
    {
        if (empty($this->domain)) {
            throw new \Exception('MpcConfig: domain is required');
        }
        if (empty($this->appId)) {
            throw new \Exception('MpcConfig: appId is required');
        }
        if (empty($this->rsaPrivateKey)) {
            throw new \Exception('MpcConfig: rsaPrivateKey is required');
        }
    }

    /**
     * Get full API URL
     * 
     * @param string $path API path
     * @return string Full URL
     */
    public function getUrl($path)
    {
        return $this->domain . $path;
    }
}
