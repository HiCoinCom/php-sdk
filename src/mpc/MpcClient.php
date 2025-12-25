<?php

namespace Chainup\Waas\Mpc;

use Chainup\Waas\Mpc\Api\WalletApi;
use Chainup\Waas\Mpc\Api\DepositApi;
use Chainup\Waas\Mpc\Api\WithdrawApi;
use Chainup\Waas\Mpc\Api\Web3Api;
use Chainup\Waas\Mpc\Api\AutoSweepApi;
use Chainup\Waas\Mpc\Api\NotifyApi;
use Chainup\Waas\Mpc\Api\WorkSpaceApi;
use Chainup\Waas\Mpc\Api\TronResourceApi;

/**
 * MPC Client - Main entry point for MPC (Multi-Party Computation) API operations
 * Provides factory methods for creating API instances
 * Uses Builder pattern for flexible configuration
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
     * Internal factory method for Builder to create instances
     * Not intended for public use - use newBuilder() instead
     * 
     * @param MpcConfig $config MPC configuration object
     * @return MpcClient MpcClient instance
     * @internal
     */
    public static function createFromConfig(MpcConfig $config)
    {
        return new self($config);
    }

    /**
     * Get WalletApi instance for wallet management operations
     * 
     * @return WalletApi WalletApi instance
     */
    public function getWalletApi()
    {
        return new WalletApi($this->config);
    }

    /**
     * Get DepositApi instance for deposit operations
     * 
     * @return DepositApi DepositApi instance
     */
    public function getDepositApi()
    {
        return new DepositApi($this->config);
    }

    /**
     * Get WithdrawApi instance for withdrawal operations
     * 
     * @return WithdrawApi WithdrawApi instance
     */
    public function getWithdrawApi()
    {
        return new WithdrawApi($this->config);
    }

    /**
     * Get Web3Api instance for Web3 operations
     * 
     * @return Web3Api Web3Api instance
     */
    public function getWeb3Api()
    {
        return new Web3Api($this->config);
    }

    /**
     * Get AutoSweepApi instance for auto-sweep operations
     * 
     * @return AutoSweepApi AutoSweepApi instance
     */
    public function getAutoSweepApi()
    {
        return new AutoSweepApi($this->config);
    }

    /**
     * Get NotifyApi instance for notification operations
     * 
     * @return NotifyApi NotifyApi instance
     */
    public function getNotifyApi()
    {
        return new NotifyApi($this->config);
    }

    /**
     * Get WorkSpaceApi instance for workspace operations
     * 
     * @return WorkSpaceApi WorkSpaceApi instance
     */
    public function getWorkSpaceApi()
    {
        return new WorkSpaceApi($this->config);
    }

    /**
     * Get TronResourceApi instance for TRON resource operations
     * 
     * @return TronResourceApi TronResourceApi instance
     */
    public function getTronResourceApi()
    {
        return new TronResourceApi($this->config);
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
     * Set the WaaS server public key
     * 
     * @param string $waasPublicKey WaaS server public key
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setWaasPublicKey($waasPublicKey)
    {
        $this->options['waasPublicKey'] = $waasPublicKey;
        return $this;
    }

    /**
     * Set the API key for authentication
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
     * Set the RSA private key for signing
     * 
     * @param string $signPrivateKey RSA private key for signing
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setSignPrivateKey($signPrivateKey)
    {
        $this->options['signPrivateKey'] = $signPrivateKey;
        return $this;
    }

    /**
     * Set the logger instance
     * 
     * @param \Chainup\Waas\Utils\LoggerInterface $logger Logger instance
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setLogger($logger)
    {
        $this->options['logger'] = $logger;
        return $this;
    }

    /**
     * Set the crypto provider instance
     * 
     * @param \Chainup\Waas\Utils\CryptoProviderInterface $cryptoProvider Crypto provider instance
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setCryptoProvider($cryptoProvider)
    {
        $this->options['cryptoProvider'] = $cryptoProvider;
        return $this;
    }

    /**
     * Enable or disable debug mode
     * 
     * @param bool $debug Debug mode flag
     * @return MpcClientBuilder This builder instance for chaining
     */
    public function setDebug($debug)
    {
        $this->options['debug'] = $debug;
        return $this;
    }

    /**
     * Build the MpcClient instance
     * 
     * @return MpcClient MpcClient instance
     * @throws \Exception If configuration is invalid
     */
    public function build()
    {
        $config = new MpcConfig($this->options);
        return MpcClient::createFromConfig($config);
    }
}
