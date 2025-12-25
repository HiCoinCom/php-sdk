<?php

namespace Chainup\Waas\Mpc;

/**
 * MPC Client - Main entry point for MPC API operations
 * Provides factory methods for creating MPC API instances
 * Uses Builder pattern for flexible configuration
 * This is reserved for future MPC wallet functionality
 * 
 * @package Chainup\Waas\Mpc
 */
class MpcClient
{
    /**
     * @var MpcConfig Configuration object
     */
    private $config;

    /**
     * Private constructor - use Builder to create instances
     * 
     * @param MpcConfig $config MPC configuration object
     */
    private function __construct(MpcConfig $config)
    {
        $this->config = $config;
        $this->config->validate();
    }

    /**
     * Create a new Builder instance for configuring MpcClient
     * 
     * @return MpcClientBuilder Builder instance
     */
    public static function newBuilder()
    {
        return new MpcClientBuilder();
    }
}

/**
 * Builder class for constructing MpcClient instances
 * Implements the Builder pattern for flexible configuration
 * 
 * @package Chainup\Waas\Mpc
 */
class MpcClientBuilder
{
    /**
     * @var array Configuration options
     */
    private $options = array();

    /**
     * Set the API domain URL
     * 
     * @param string $domain API domain URL
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setDomain($domain)
    {
        $this->options['domain'] = $domain;
        return $this;
    }

    /**
     * Set the application ID
     * 
     * @param string $appId Application ID
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setAppId($appId)
    {
        $this->options['appId'] = $appId;
        return $this;
    }

    /**
     * Set the RSA private key
     * 
     * @param string $rsaPrivateKey RSA private key
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setRsaPrivateKey($rsaPrivateKey)
    {
        $this->options['rsaPrivateKey'] = $rsaPrivateKey;
        return $this;
    }

    /**
     * Set the WaaS public key
     * 
     * @param string $waasPublicKey WaaS public key
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setWaasPublicKey($waasPublicKey)
    {
        $this->options['waasPublicKey'] = $waasPublicKey;
        return $this;
    }

    /**
     * Set the API key
     * 
     * @param string $apiKey API key
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setApiKey($apiKey)
    {
        $this->options['apiKey'] = $apiKey;
        return $this;
    }

    /**
     * Enable or disable debug mode
     * 
     * @param bool $debug Debug flag
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setDebug($debug)
    {
        $this->options['debug'] = $debug;
        return $this;
    }

    /**
     * Build and return a configured MpcClient instance
     * 
     * @return MpcClient Configured MpcClient instance
     * @throws \Exception If required configuration is missing
     */
    public function build()
    {
        $config = new MpcConfig($this->options);
        return new MpcClient($config);
    }
}
