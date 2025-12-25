<?php

namespace Chainup\Waas\Utils;

use GuzzleHttp\Client;

/**
 * HTTP Client for WaaS API communication
 * Handles HTTP requests to the WaaS API
 * 
 * @package Chainup\Waas\Utils
 */
class HttpClient
{
    /**
     * @var object Configuration object
     */
    private $config;

    /**
     * @var Client Guzzle HTTP client
     */
    private $client;

    /**
     * @var LoggerInterface Logger instance
     */
    private $logger;

    /**
     * Constructor
     * 
     * @param object $config Configuration object with host, debug settings
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->client = new Client();
        $this->logger = $config->logger;
    }

    /**
     * Execute HTTP request
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path API path
     * @param array $data Request data
     * @param array $headers Additional headers
     * @return string Response body
     * @throws \Exception If request fails
     */
    public function request($method, $path, $data = array(), $headers = array())
    {
        $url = $this->config->getUrl($path);

        $options = array(
            'headers' => array_merge(
                array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'ChainUp-WaaS-PHP-SDK/2.0',
                    'Accept' => 'application/json'
                ),
                $headers
            ),
            'timeout' => 30,
        );

        // Set request data based on method
        if ($method === 'POST') {
            $options['form_params'] = $data;
        } else if ($method === 'GET') {
            $options['query'] = $data;
        }

        $this->logger->debug("[HTTP Request] {$method} {$url}", array('data' => $data));

        try {
            $response = $this->client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            $this->logger->debug("[HTTP Response] Status: {$statusCode}");
            $this->logger->debug("[HTTP Body] {$body}");

            if ($statusCode !== 200) {
                throw new \Exception("HTTP request failed with status {$statusCode}: {$body}");
            }

            return $body;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $this->logger->error("[HTTP Error] " . $e->getMessage());
            throw new \Exception("HTTP request error: " . $e->getMessage());
        }
    }

    /**
     * Execute POST request
     * 
     * @param string $path API path
     * @param array $data Request data
     * @return string Response body
     */
    public function post($path, $data = array())
    {
        return $this->request('POST', $path, $data);
    }

    /**
     * Execute GET request
     * 
     * @param string $path API path
     * @param array $data Request data
     * @return string Response body
     */
    public function get($path, $data = array())
    {
        return $this->request('GET', $path, $data);
    }
}
