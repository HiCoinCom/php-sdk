<?php

namespace Chainup\Waas\Custody;

use Chainup\Waas\Utils\LoggerInterface;
use Chainup\Waas\Utils\DefaultLogger;
use Chainup\Waas\Utils\CryptoProviderInterface;
use Chainup\Waas\Utils\RsaCryptoProvider;

/**
 * WaaS Configuration Class
 * Stores configuration parameters for WaaS (Wallet-as-a-Service) API client
 * 
 * @package Chainup\Waas\Custody
 */
class WaasConfig
{
    /**
     * @var string API host URL
     */
    public $host;

    /**
     * @var string Application ID
     */
    public $appId;

    /**
     * @var string User's RSA private key (PEM format)
     */
    public $privateKey;

    /**
     * @var string ChainUp's RSA public key (PEM format)
     */
    public $publicKey;

    /**
     * @var string API version
     */
    public $version = 'v2';

    /**
     * @var string Character encoding
     */
    public $charset = 'UTF-8';

    /**
     * @var bool Debug mode flag
     */
    public $debug = false;

    /**
     * @var LoggerInterface Logger instance
     */
    public $logger;

    /**
     * @var CryptoProviderInterface Crypto provider instance
     */
    public $cryptoProvider;

    /**
     * Constructor
     * 
     * @param array $options Configuration options
     */
    public function __construct($options = array())
    {
        $this->host = isset($options['host']) ? $options['host'] : 'https://openapi.chainup.com';
        $this->appId = isset($options['appId']) ? $options['appId'] : '';
        $this->privateKey = isset($options['privateKey']) ? $options['privateKey'] : '';
        $this->publicKey = isset($options['publicKey']) ? $options['publicKey'] : '';
        $this->version = isset($options['version']) ? $options['version'] : 'v2';
        $this->charset = isset($options['charset']) ? $options['charset'] : 'UTF-8';
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
        
        // Initialize logger
        if (isset($options['logger']) && $options['logger'] instanceof LoggerInterface) {
            $this->logger = $options['logger'];
        } else {
            $this->logger = new DefaultLogger($this->debug);
        }
        
        // Initialize crypto provider
        if (isset($options['cryptoProvider']) && $options['cryptoProvider'] instanceof CryptoProviderInterface) {
            $this->cryptoProvider = $options['cryptoProvider'];
        } else {
            // Create default RSA crypto provider if keys are provided
            if (!empty($this->privateKey) && !empty($this->publicKey)) {
                $this->cryptoProvider = new RsaCryptoProvider(array(
                    'privateKey' => $this->privateKey,
                    'publicKey' => $this->publicKey
                ));
            }
        }
    }

    /**
     * Validate configuration
     * 
     * @throws \Exception If required fields are missing
     */
    public function validate()
    {
        if (empty($this->host)) {
            throw new \Exception('WaasConfig: host is required');
        }
        if (empty($this->appId)) {
            throw new \Exception('WaasConfig: appId is required');
        }
        if (!$this->cryptoProvider) {
            throw new \Exception('WaasConfig: cryptoProvider is required (or provide privateKey and publicKey)');
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
        return $this->host . '/api/' . $this->version . $path;
    }
}
