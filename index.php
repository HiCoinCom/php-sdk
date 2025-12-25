<?php
/**
 * ChainUp Custody PHP SDK
 * Main entry point for WaaS (Wallet-as-a-Service) and MPC (Multi-Party Computation) APIs
 * 
 * @package Chainup\Waas
 * @author ChainUp Custody
 * @version 2.0.0
 * @license MIT
 * 
 * Documentation: https://custodydocs.chainup.com
 * GitHub: https://github.com/ChainUp-Custody/php-sdk
 */

// Auto-load Composer dependencies
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// WaaS (Custody) Client
use Chainup\Waas\Custody\WaasClient;
use Chainup\Waas\Custody\WaasConfig;

// MPC Client (Reserved for future use)
use Chainup\Waas\Mpc\MpcClient;
use Chainup\Waas\Mpc\MpcConfig;

// Utility classes
use Chainup\Waas\Utils\Result;
use Chainup\Waas\Utils\RsaUtil;
use Chainup\Waas\Utils\Base64UrlSafe;
use Chainup\Waas\Utils\HttpClient;
use Chainup\Waas\Utils\LoggerInterface;
use Chainup\Waas\Utils\DefaultLogger;
use Chainup\Waas\Utils\CryptoProviderInterface;
use Chainup\Waas\Utils\RsaCryptoProvider;

/**
 * Example Usage:
 * 
 * // Create WaaS client using Builder pattern
 * $client = WaasClient::newBuilder()
 *     ->setHost('https://openapi.chainup.com')
 *     ->setAppId('your-app-id')
 *     ->setPrivateKey('your-private-key')
 *     ->setPublicKey('chainup-public-key')
 *     ->setDebug(true)
 *     ->build();
 * 
 * // Advanced: Use custom logger
 * $client = WaasClient::newBuilder()
 *     ->setHost('https://openapi.chainup.com')
 *     ->setAppId('your-app-id')
 *     ->setPrivateKey('your-private-key')
 *     ->setPublicKey('chainup-public-key')
 *     ->setLogger(new MyCustomLogger())  // Implement LoggerInterface
 *     ->build();
 * 
 * // Advanced: Use custom crypto provider (e.g., HSM)
 * $client = WaasClient::newBuilder()
 *     ->setHost('https://openapi.chainup.com')
 *     ->setAppId('your-app-id')
 *     ->setCryptoProvider(new MyHsmCryptoProvider())  // Implement CryptoProviderInterface
 *     ->build();
 * 
 * // Use UserApi
 * $userApi = $client->getUserApi();
 * $result = $userApi->registerEmailUser('user@example.com');
 * 
 * if ($result->isSuccess()) {
 *     $data = $result->getData();
 *     echo "User UID: " . $data['uid'];
 * } else {
 *     echo "Error: " . $result->getMsg();
 * }
 * 
 * // Use AccountApi
 * $accountApi = $client->getAccountApi();
 * $result = $accountApi->getUserAccount(12345, 'BTC');
 * 
 * // Use BillingApi
 * $billingApi = $client->getBillingApi();
 * $result = $billingApi->withdraw(
 *     'withdraw_001',  // request_id
 *     12345,           // from_uid
 *     '0x1234...',     // to_address
 *     '1.5',           // amount
 *     'ETH'            // symbol
 * );
 * 
 * // Use CoinApi
 * $coinApi = $client->getCoinApi();
 * $result = $coinApi->getCoinList();
 */
