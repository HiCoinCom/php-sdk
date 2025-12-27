<?php

namespace Chainup\Waas\Mpc;

use Chainup\Waas\Utils\RsaUtil;
use Chainup\Waas\Utils\LoggerInterface;
use Chainup\Waas\Utils\DefaultLogger;
use Chainup\Waas\Utils\CryptoProviderInterface;
use Chainup\Waas\Utils\RsaCryptoProvider;

/**
 * MPC Configuration Class
 * Stores configuration parameters for MPC (Multi-Party Computation) API client
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
     * @var string RSA private key (PEM format)
     */
    public $rsaPrivateKey;

    /**
     * @var string WaaS server public key for decrypting responses (PEM format)
     */
    public $waasPublicKey;

    /**
     * @var string API key for authentication
     */
    public $apiKey;

    /**
     * @var string RSA private key for signing (preserves original format)
     */
    public $signPrivateKey;

    /**
     * @var CryptoProviderInterface Custom crypto provider implementation
     */
    public $cryptoProvider;

    /**
     * @var LoggerInterface Logger instance
     */
    public $logger;

    /**
     * @var bool Enable debug mode
     */
    public $debug;

    /**
     * @var string Character set (default: utf-8)
     */
    public $charset = 'utf-8';

    /**
     * Creates a new MPC configuration
     * 
     * @param array $options Configuration options
     *   - string domain: API domain URL
     *   - string appId: Application ID
     *   - string rsaPrivateKey: RSA private key (required if no cryptoProvider)
     *   - string waasPublicKey: WaaS server public key for decrypting responses
     *   - string apiKey: API key for authentication
     *   - string signPrivateKey: RSA private key for signing
     *   - CryptoProviderInterface cryptoProvider: Custom crypto provider implementation
     *   - LoggerInterface logger: Custom logger implementation
     *   - bool debug: Enable debug mode (default: false)
     */
    public function __construct($options = array())
    {
        $this->domain = isset($options['domain']) ? $options['domain'] : 'https://openapi.chainup.com';
        $this->appId = isset($options['appId']) ? $options['appId'] : '';
        $this->apiKey = isset($options['apiKey']) ? $options['apiKey'] : '';
        $this->debug = isset($options['debug']) ? $options['debug'] : false;

        // Auto-format RSA keys to proper PEM format
        if (isset($options['rsaPrivateKey'])) {
            $this->rsaPrivateKey = RsaUtil::formatPrivateKey($options['rsaPrivateKey']);
        } else {
            $this->rsaPrivateKey = '';
        }

        if (isset($options['waasPublicKey'])) {
            $this->waasPublicKey = RsaUtil::formatPublicKey($options['waasPublicKey']);
        } else {
            $this->waasPublicKey = '';
        }

        // Format sign private key
        if (isset($options['signPrivateKey'])) {
            $this->signPrivateKey = RsaUtil::formatPrivateKey($options['signPrivateKey']);
        } else {
            $this->signPrivateKey = '';
        }

        // Set logger
        if (isset($options['logger']) && $options['logger'] instanceof LoggerInterface) {
            $this->logger = $options['logger'];
        } else {
            $this->logger = new DefaultLogger($this->debug);
        }

        // Set crypto provider
        if (isset($options['cryptoProvider']) && $options['cryptoProvider'] instanceof CryptoProviderInterface) {
            $this->cryptoProvider = $options['cryptoProvider'];
        } else if ($this->rsaPrivateKey) {
            // Create default RSA crypto provider with separate signing key
            $this->cryptoProvider = new RsaCryptoProvider(array(
                'privateKey' => $this->rsaPrivateKey,
                'publicKey' => $this->waasPublicKey,
                'signPrivateKey' => $this->signPrivateKey  // Use signPrivateKey for transaction signing
            ));
        } else {
            $this->cryptoProvider = null;
        }
    }

    /**
     * Validates the configuration
     * 
     * @return bool True if configuration is valid
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

        // Either cryptoProvider or rsaPrivateKey must be provided
        if (!$this->cryptoProvider && empty($this->rsaPrivateKey)) {
            throw new \Exception('MpcConfig: rsaPrivateKey is required (or provide cryptoProvider)');
        }

        return true;
    }

    /**
     * Gets the full API URL
     * 
     * @param string $path API path
     * @return string Full API URL
     */
    public function getUrl($path)
    {
        return rtrim($this->domain, '/') . $path;
    }
}
