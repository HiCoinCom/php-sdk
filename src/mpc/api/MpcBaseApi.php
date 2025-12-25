<?php

namespace Chainup\Waas\Mpc\Api;

use Chainup\Waas\Utils\HttpClient;
use Chainup\Waas\Utils\Result;
use Chainup\Waas\Utils\LoggerInterface;
use Chainup\Waas\Utils\CryptoProviderInterface;

/**
 * MPC Base API Class
 * Provides common functionality for all MPC API implementations
 * Implements the same encryption flow as Java SDK:
 * - Request: encrypt params with private key, send as {app_id, data}
 * - Response: decrypt data field with public key
 * 
 * @package Chainup\Waas\Mpc\Api
 */
abstract class MpcBaseApi
{
    /**
     * @var object MPC configuration
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
     * @param object $config MPC configuration object
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
            'charset' => 'utf-8'
        ));
        return json_encode($args, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Execute MPC API request with signing and encryption
     * Flow matches Java SDK:
     * 1. Serialize params to JSON
     * 2. Encrypt with private key  
     * 3. Send only app_id and encrypted data
     * 4. Decrypt response data with public key
     * 
     * @param string $method HTTP method (GET, POST)
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
            
            if ($this->config->debug) {
                $this->logger->debug('[MPC Request Args]: ' . $rawJson);
            }

            // Step 2: Encrypt with private key
            $encryptedData = '';
            if ($this->cryptoProvider) {
                try {
                    $encryptedData = $this->cryptoProvider->encryptWithPrivateKey($rawJson);
                    if ($this->config->debug) {
                        $this->logger->debug('[MPC Encrypted Data]: ' . substr($encryptedData, 0, 100) . '...');
                    }
                } catch (\Exception $e) {
                    $result->setCode(-3);
                    $result->setMsg('Failed to encrypt request data: ' . $e->getMessage());
                    return $result;
                }
            }

            // Step 3: Send request with app_id and encrypted data
            $requestData = array(
                'app_id' => $this->config->appId,
                'data' => $encryptedData
            );
            
            $responseBody = $this->httpClient->request($method, $path, $requestData);
            
            // Parse JSON response
            $response = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $result->setCode(-2);
                $result->setMsg('Failed to parse API response: ' . json_last_error_msg());
                return $result;
            }

            if ($this->config->debug) {
                $this->logger->debug('[MPC Response]: ' . json_encode($response));
            }

            // Step 4: Check if response has encrypted data field and decrypt
            if (isset($response['data']) && is_string($response['data'])) {
                // MPC API returns encrypted data, need to decrypt with public key
                if ($this->cryptoProvider) {
                    try {
                        $decrypted = $this->cryptoProvider->decryptWithPublicKey($response['data']);
                        if ($this->config->debug) {
                            $this->logger->debug('[MPC Decrypted]: ' . $decrypted);
                        }
                        // Parse decrypted JSON and return complete response
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
                        if ($this->config->debug) {
                            $this->logger->error('[MPC Decrypt Error]: ' . $e->getMessage());
                        }
                        // If decryption fails, might be an error response, return as-is
                        $result->setCode(isset($response['code']) ? $response['code'] : -1);
                        $result->setMsg(isset($response['msg']) ? $response['msg'] : 'Failed to decrypt response: ' . $e->getMessage());
                        $result->setData(isset($response['data']) ? $response['data'] : null);
                    }
                }
            } else {
                // Response is not encrypted (possibly an error)
                $result->setCode(isset($response['code']) ? $response['code'] : -1);
                $result->setMsg(isset($response['msg']) ? $response['msg'] : 'Unknown error');
                $result->setData(isset($response['data']) ? $response['data'] : null);
            }

        } catch (\Exception $e) {
            if ($this->config->debug) {
                $this->logger->error('[MPC Request Error]: ' . $e->getMessage());
            }
            $result->setCode(-1);
            $result->setMsg('Request failed: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Execute a POST request
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
     * Execute a GET request
     * 
     * @param string $path API path
     * @param array $data Request data
     * @return Result API response result
     */
    protected function get($path, $data = array())
    {
        return $this->executeRequest('GET', $path, $data);
    }

    /**
     * Validate response and handle errors
     * Deprecated: Now returns Result object directly
     * 
     * @param Result $result API response result
     * @return Result Validated response result
     */
    protected function validateResponse($result)
    {
        // Response is already in Result format, just return it
        return $result;
    }

    /**
     * Generate MPC withdrawal signature
     * Process:
     * 1. Sort parameters by key (ASCII order)
     * 2. Format as k1=v1&k2=v2 and convert to lowercase
     * 3. Generate MD5 hash of the sorted string
     * 4. Sign the MD5 hash with RSA-SHA256
     * 5. Return Base64 encoded signature
     * 
     * @param array $withdrawParams Withdrawal parameters
     * @return string Base64 encoded signature
     * @throws \Exception If signing fails
     */
    protected function generateWithdrawSign($withdrawParams)
    {
        if (empty($withdrawParams)) {
            throw new \Exception('Withdrawal parameters cannot be empty');
        }

        // Build sign params map with specific fields only
        $signParamsMap = array(
            'request_id' => isset($withdrawParams['request_id']) ? $withdrawParams['request_id'] : '',
            'sub_wallet_id' => isset($withdrawParams['sub_wallet_id']) ? (string)$withdrawParams['sub_wallet_id'] : '',
            'symbol' => isset($withdrawParams['symbol']) ? $withdrawParams['symbol'] : '',
            'address_to' => isset($withdrawParams['address_to']) ? $withdrawParams['address_to'] : '',
            'amount' => isset($withdrawParams['amount']) ? $withdrawParams['amount'] : '',
            'memo' => isset($withdrawParams['memo']) ? $withdrawParams['memo'] : '',
            'outputs' => isset($withdrawParams['outputs']) ? $withdrawParams['outputs'] : ''
        );

        // Sort and format parameters
        $signData = $this->sortParams($signParamsMap);
        if (empty($signData)) {
            throw new \Exception('Failed to generate sign data');
        }

        // Generate MD5 hash
        $md5Hash = md5($signData);

        // Sign the MD5 hash with RSA-SHA256
        return $this->cryptoProvider->sign($md5Hash, true);
    }

    /**
     * Sort parameters and format for signing
     * Matches JS SDK MpcSignUtil.paramsSort logic
     * Rules:
     * - Parameters formatted as k1=v1&k2=v2
     * - Keys sorted in ASCII ascending order
     * - Empty values excluded
     * - Amount field: remove trailing zeros (e.g., 1.0001000 -> 1.0001)
     * - Result converted to lowercase
     * 
     * @param array $params Parameters to sort
     * @return string Sorted and formatted parameter string
     */
    protected function sortParams($params)
    {
        if (empty($params)) {
            return '';
        }

        $sortedParams = array();

        // Process each parameter
        foreach ($params as $key => $value) {
            // Skip empty values
            if ($value === null || $value === '' || $value === false) {
                continue;
            }

            // Convert to string
            $value = (string)$value;

            // Remove trailing zeros from amount field
            if ($key === 'amount' && $value !== '') {
                // Remove trailing zeros after decimal point
                // e.g., "1.0001000" -> "1.0001", "1.0" -> "1"
                $value = preg_replace('/(\.[0-9]*?)0+$/', '$1', $value);
                $value = rtrim($value, '.');
            }

            $sortedParams[$key] = $value;
        }

        // Sort keys in ASCII order
        ksort($sortedParams, SORT_STRING);

        // Build parameter string
        $parts = array();
        foreach ($sortedParams as $key => $value) {
            $parts[] = $key . '=' . $value;
        }

        // Join with & and convert to lowercase
        return strtolower(implode('&', $parts));
    }

    /**
     * Generate MPC Web3 transaction signature
     * Process:
     * 1. Sort parameters by key (ASCII order)
     * 2. Format as k1=v1&k2=v2 and convert to lowercase
     * 3. Generate MD5 hash of the sorted string
     * 4. Sign the MD5 hash with RSA-SHA256
     * 5. Return Base64 encoded signature
     * 
     * @param array $web3Params Web3 transaction parameters
     *   - request_id (string): Request ID [required]
     *   - sub_wallet_id (string): Sub-wallet ID [required]
     *   - main_chain_symbol (string): Main chain symbol [required]
     *   - interactive_contract (string): Contract address [required]
     *   - amount (string): Amount [required]
     *   - input_data (string): Input data [required]
     * @return string Base64 encoded signature
     * @throws \Exception If signing fails
     */
    protected function generateWeb3Sign($web3Params)
    {
        if (empty($web3Params)) {
            throw new \Exception('Web3 transaction parameters cannot be empty');
        }

        // Build sign params map with specific fields only
        $signParamsMap = array(
            'request_id' => isset($web3Params['request_id']) ? $web3Params['request_id'] : '',
            'sub_wallet_id' => isset($web3Params['sub_wallet_id']) ? (string)$web3Params['sub_wallet_id'] : '',
            'main_chain_symbol' => isset($web3Params['main_chain_symbol']) ? $web3Params['main_chain_symbol'] : '',
            'interactive_contract' => isset($web3Params['interactive_contract']) ? $web3Params['interactive_contract'] : '',
            'amount' => isset($web3Params['amount']) ? $web3Params['amount'] : '',
            'input_data' => isset($web3Params['input_data']) ? $web3Params['input_data'] : ''
        );

        // Sort and format parameters
        $signData = $this->sortParams($signParamsMap);
        if (empty($signData)) {
            throw new \Exception('Failed to generate sign data');
        }

        // Generate MD5 hash
        $md5Hash = md5($signData);

        // Sign the MD5 hash with RSA-SHA256
        return $this->cryptoProvider->sign($md5Hash, true);
    }
}
