<?php

namespace Chainup\Waas\Custody\Api;

use Chainup\Waas\Utils\HttpClient;
use Chainup\Waas\Utils\Result;
use Chainup\Waas\Utils\LoggerInterface;
use Chainup\Waas\Utils\CryptoProviderInterface;

/**
 * Base API Class
 * Provides common functionality for all WaaS API implementations
 * Implements the same encryption flow as Java/JS SDK:
 * - Request: encrypt params with private key, send as {app_id, data}
 * - Response: decrypt data field with public key
 * 
 * @package Chainup\Waas\Custody\Api
 */
abstract class BaseApi
{
    /**
     * @var object WaaS configuration
     */
    protected $config;

    /**
     * @var HttpClient HTTP client instance
     */
    protected $httpClient;

    /**
     * @var LoggerInterface Logger instance
     */
    protected $logger;

    /**
     * @var CryptoProviderInterface Crypto provider instance
     */
    protected $cryptoProvider;

    /**
     * Constructor
     * 
     * @param object $config WaaS configuration object
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->httpClient = new HttpClient($config);
        $this->logger = $config->logger;
        $this->cryptoProvider = $config->cryptoProvider;
    }

    /**
     * Build request args JSON with common parameters
     * Matches Java/JS SDK: args.setCharset(), args.setTime(), args.toJson()
     * 
     * @param array $data API-specific request data
     * @return string JSON string of request args
     */
    protected function buildRequestArgs($data = array())
    {
        $args = array_merge($data, array(
            'time' => time() * 1000, // milliseconds
            'charset' => $this->config->charset
        ));
        return json_encode($args, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Execute API request with signing and encryption
     * Flow matches Java/JS SDK:
     * 1. Serialize params to JSON
     * 2. Encrypt with private key
     * 3. Send only app_id and encrypted data
     * 4. Decrypt response data with public key
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path API path
     * @param array $data Request data
     * @return Result API response result
     */
    protected function executeRequest($method, $path, $data = array())
    {
        $result = new Result();

        try {
            // Step 1: Build request args JSON
            $rawJson = $this->buildRequestArgs($data);

            $this->logger->debug("[WaaS Request Args] {$rawJson}");

            // Step 2: Encrypt with private key using crypto provider
            $encryptedData = $this->cryptoProvider->encryptWithPrivateKey($rawJson);

            $this->logger->debug("[WaaS Encrypted Data] " . substr($encryptedData, 0, 100) . "...");

            // Step 3: Send request with only app_id and encrypted data
            $requestData = array(
                'app_id' => $this->config->appId,
                'data' => $encryptedData
            );

            $responseBody = $this->httpClient->request($method, $path, $requestData);

            $this->logger->debug("[WaaS Response] {$responseBody}");

            // Step 4: Parse response and decrypt data if needed
            $response = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $result->setCode(-2);
                $result->setMsg('Invalid JSON response: ' . $responseBody);
                return $result;
            }

            // Check if response has encrypted data field and decrypt using crypto provider
            if (isset($response['data']) && is_string($response['data'])) {
                try {
                    $decrypted = $this->cryptoProvider->decryptWithPublicKey($response['data']);
                    
                    $this->logger->debug("[WaaS Decrypted] {$decrypted}");

                    // Parse decrypted JSON
                    $decryptedData = json_decode($decrypted, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $result->setCode(isset($decryptedData['code']) ? $decryptedData['code'] : 0);
                        $result->setMsg(isset($decryptedData['msg']) ? $decryptedData['msg'] : '');
                        $result->setData(isset($decryptedData['data']) ? $decryptedData['data'] : null);
                    } else {
                        $result->setCode(-4);
                        $result->setMsg('Failed to parse decrypted response');
                    }
                } catch (\Exception $e) {
                    $this->logger->error("[WaaS Decrypt Error] " . $e->getMessage());
                    $result->setCode(-3);
                    $result->setMsg('Failed to decrypt response: ' . $e->getMessage());
                }
            } else {
                // Response is not encrypted (possibly an error)
                $result->setCode(isset($response['code']) ? $response['code'] : -1);
                $result->setMsg(isset($response['msg']) ? $response['msg'] : 'Unknown error');
                $result->setData(isset($response['data']) ? $response['data'] : null);
            }

        } catch (\Exception $e) {
            $this->logger->error("[WaaS Request Error] " . $e->getMessage());
            $result->setCode(-1);
            $result->setMsg('Request failed: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Execute POST request
     * 
     * @param string $path API path
     * @param array $data Request data
     * @return Result API response result
     */
    protected function post($path, $data = array())
    {
        return $this->executeRequest('POST', $path, $data);
    }

    /**
     * Execute GET request
     * 
     * @param string $path API path
     * @param array $data Request data
     * @return Result API response result
     */
    protected function get($path, $data = array())
    {
        return $this->executeRequest('GET', $path, $data);
    }
}
