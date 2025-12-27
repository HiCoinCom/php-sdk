<?php

namespace Chainup\Waas\Custody;

use Chainup\Waas\Custody\Api\UserApi;
use Chainup\Waas\Custody\Api\AccountApi;
use Chainup\Waas\Custody\Api\BillingApi;
use Chainup\Waas\Custody\Api\CoinApi;
use Chainup\Waas\Custody\Api\AsyncNotifyApi;

/**
 * WaaS Client - Main entry point for WaaS API operations
 * Provides factory methods for creating API instances
 * Uses Builder pattern for flexible configuration
 * 
 * @package Chainup\Waas\Custody
 */
class WaasClient
{
    /**
     * @var WaasConfig Configuration object
     */
    private $config;

    /**
     * Private constructor - use Builder to create instances
     * 
     * @param WaasConfig $config WaaS configuration object
     */
    private function __construct(WaasConfig $config)
    {
        $this->config = $config;
        $this->config->validate();
    }

    /**
     * Internal factory method for Builder to create instances
     * Not intended for public use - use newBuilder() instead
     * 
     * @param WaasConfig $config WaaS configuration object
     * @return WaasClient WaasClient instance
     * @internal
     */
    public static function createFromConfig(WaasConfig $config)
    {
        return new self($config);
    }

    /**
     * Get UserApi instance for user-related operations
     * 
     * @return UserApi UserApi instance
     */
    public function getUserApi()
    {
        return new UserApi($this->config);
    }

    /**
     * Get AccountApi instance for account-related operations
     * 
     * @return AccountApi AccountApi instance
     */
    public function getAccountApi()
    {
        return new AccountApi($this->config);
    }

    /**
     * Get BillingApi instance for billing and transaction operations
     * 
     * @return BillingApi BillingApi instance
     */
    public function getBillingApi()
    {
        return new BillingApi($this->config);
    }

    /**
     * Get CoinApi instance for coin and blockchain operations
     * 
     * @return CoinApi CoinApi instance
     */
    public function getCoinApi()
    {
        return new CoinApi($this->config);
    }

    /**
     * Get AsyncNotifyApi instance for asynchronous notification operations
     * 
     * @return AsyncNotifyApi AsyncNotifyApi instance
     */
    public function getAsyncNotifyApi()
    {
        return new AsyncNotifyApi($this->config);
    }

    /**
     * Create a new Builder instance for configuring WaasClient
     * 
     * @return WaasClientBuilder Builder instance
     */
    public static function newBuilder()
    {
        return new WaasClientBuilder();
    }
}

/**
 * Builder class for constructing WaasClient instances
 * Implements the Builder pattern for flexible configuration
 * 
 * @package Chainup\Waas\Custody
 */
class WaasClientBuilder
{
    /**
     * @var array Configuration options
     */
    private $options = array();

    /**
     * Set the API host URL
     * 
     * @param string $host API host URL
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setHost($host)
    {
        $this->options['host'] = $host;
        return $this;
    }

    /**
     * Set the application ID
     * 
     * @param string $appId Application ID
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setAppId($appId)
    {
        $this->options['appId'] = $appId;
        return $this;
    }

    /**
     * Set the RSA private key
     * 
     * @param string $privateKey RSA private key
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setPrivateKey($privateKey)
    {
        $this->options['privateKey'] = $privateKey;
        return $this;
    }

    /**
     * Set the ChainUp public key
     * 
     * @param string $publicKey ChainUp public key
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setPublicKey($publicKey)
    {
        $this->options['publicKey'] = $publicKey;
        return $this;
    }

    /**
     * Set custom logger instance
     * 
     * @param \Chainup\Waas\Utils\LoggerInterface $logger Logger instance
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setLogger($logger)
    {
        $this->options['logger'] = $logger;
        return $this;
    }

    /**
     * Set custom crypto provider instance
     * 
     * @param \Chainup\Waas\Utils\CryptoProviderInterface $cryptoProvider Crypto provider instance
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setCryptoProvider($cryptoProvider)
    {
        $this->options['cryptoProvider'] = $cryptoProvider;
        return $this;
    }

    /**
     * Set the API version
     * 
     * @param string $version API version (default: 'v2')
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setVersion($version)
    {
        $this->options['version'] = $version;
        return $this;
    }

    /**
     * Set the charset encoding
     * 
     * @param string $charset Charset encoding (default: 'UTF-8')
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setCharset($charset)
    {
        $this->options['charset'] = $charset;
        return $this;
    }

    /**
     * Enable or disable debug mode
     * 
     * @param bool $debug Debug flag
     * @return WaasClientBuilder This builder instance for chaining
     */
    public function setDebug($debug)
    {
        $this->options['debug'] = $debug;
        return $this;
    }

    /**
     * Build and return a configured WaasClient instance
     * 
     * @return WaasClient Configured WaasClient instance
     * @throws \Exception If required configuration is missing
     */
    public function build()
    {
        $config = new WaasConfig($this->options);
        return WaasClient::createFromConfig($config);
    }
}
