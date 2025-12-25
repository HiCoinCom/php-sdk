# ChainUp Custody PHP SDK

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D5.6-8892BF.svg)](https://www.php.net/)
[![Version](https://img.shields.io/badge/version-2.0.0-green.svg)](https://github.com/ChainUp-Custody/php-sdk)

Official PHP SDK for ChainUp Custody WaaS (Wallet-as-a-Service) and MPC (Multi-Party Computation) APIs.

## Features

- ✅ **WaaS (Custody) API** - Full support for custody wallet operations
- ✅ **Builder Pattern** - Flexible and intuitive client configuration
- ✅ **Type-Safe** - Well-defined API interfaces and response structures
- ✅ **RSA Encryption** - Secure request/response encryption
- ✅ **Error Handling** - Comprehensive error handling and validation
- ✅ **MPC Support** - Reserved structure for MPC wallet functionality
- ✅ **PSR-4 Autoloading** - Modern PHP package structure
- ✅ **PSR Compliant** - Follows PHP-FIG standards (PSR-1, PSR-4, PSR-12)

## Documentation

- [API Documentation](https://custodydocs.chainup.com/en/latest/API-WaaS-V2/index.html)
- [中文文档](https://custodydocs.chainup.com/zh_CN/latest/API-WaaS-V2/index.html)

## Installation

### Requirements

- PHP >= 5.6
- GuzzleHTTP >= 6.5 or >= 7.0
- OpenSSL extension

### Using Composer

```bash
composer require chainup-waas/sdk
```

## Quick Start

### 1. Initialize WaaS Client

```php
<?php
require_once 'vendor/autoload.php';

use Chainup\Waas\Custody\WaasClient;

// Create client using Builder pattern
$client = WaasClient::newBuilder()
    ->setHost('https://openapi.chainup.com')
    ->setAppId('your-app-id')
    ->setPrivateKey('your-private-key')
    ->setPublicKey('chainup-public-key')
    ->setDebug(true)  // Optional: enable debug mode
    ->build();
```

### 2. User Operations

```php
// Get UserApi instance
$userApi = $client->getUserApi();

// Register user by email
$result = $userApi->registerEmailUser('user@example.com');
if ($result->isSuccess()) {
    echo "User UID: " . $result->getData()['uid'];
} else {
    echo "Error: " . $result->getMsg();
}
```

### 3. Account Operations

```php
$accountApi = $client->getAccountApi();

// Get user balance
$result = $accountApi->getUserAccount(12345, 'BTC');
if ($result->isSuccess()) {
    $balance = $result->getData()['balance'];
    echo "Balance: {$balance} BTC";
}
```

### 4. Billing Operations

```php
$billingApi = $client->getBillingApi();

// Create withdrawal
$result = $billingApi->withdraw(
    'withdraw_001',   // request_id (unique)
    12345,            // from_uid
    '0x1234...',      // to_address
    '1.5',            // amount
    'ETH'             // symbol
);
```

### 5. Async Notification Operations

```php
$asyncNotifyApi = $client->getAsyncNotifyApi();

// Decrypt notification callback data
$encryptedData = $_POST['data']; // From WaaS callback
$notifyData = $asyncNotifyApi->notifyRequest($encryptedData);
if ($notifyData !== null) {
    echo "Notification type: " . $notifyData['side']; // 'deposit' or 'withdraw'
    // Process the notification
}

// Decrypt withdrawal verification request
$verifyData = $asyncNotifyApi->verifyRequest($encryptedVerifyData);

// Encrypt withdrawal verification response
$response = $asyncNotifyApi->verifyResponse(array(
    'request_id' => 'xxx',
    'status' => 1  // 1=approve, 2=reject
));
```

## Project Structure

```
php-sdk/
├── src/
│   ├── custody/              # WaaS (Custody) module
│   │   ├── api/              # API implementations
│   │   │   ├── BaseApi.php
│   │   │   ├── UserApi.php
│   │   │   ├── AccountApi.php
│   │   │   ├── BillingApi.php
│   │   │   ├── CoinApi.php
│   │   │   └── AsyncNotifyApi.php
│   │   ├── WaasClient.php    # Main WaaS client
│   │   └── WaasConfig.php    # Configuration
│   ├── mpc/                  # MPC module (reserved)
│   │   ├── MpcClient.php
│   │   └── MpcConfig.php
│   ├── utils/                # Utility classes
│   │   ├── Base64UrlSafe.php
│   │   ├── HttpClient.php
│   │   ├── Result.php
│   │   └── RsaUtil.php
│   ├── client/               # Legacy (v1.x - deprecated)
│   ├── base64/               # Legacy (v1.x - deprecated)
│   └── crypto/               # Legacy (v1.x - deprecated)
├── examples/                 # Usage examples
│   └── waas-example.php
├── composer.json
├── index.php
├── CHANGELOG.md
├── LICENSE
└── README.md
```

## Migration from v1.x

The v1.x code is still available for backward compatibility but deprecated.

### Old Way (v1.x - Deprecated)

```php
use chainup\waas\client\Config;
use chainup\waas\client\WaasClient;

$config = new Config();
$config->setAppid($appId);
$config->setDomain($domain);
$config->setUserPrivateKey($privateKey);
$config->setWaasPublicKey($publicKey);

$client = new WaasClient($config);
$result = $client->CreateEmailUser($email);
```

### New Way (v2.0 - Recommended)

```php
use Chainup\Waas\Custody\WaasClient;

$client = WaasClient::newBuilder()
    ->setHost($host)
    ->setAppId($appId)
    ->setPrivateKey($privateKey)
    ->setPublicKey($publicKey)
    ->build();

$result = $client->getUserApi()->registerEmailUser($email);
```

## Examples

See [examples/waas-example.php](examples/waas-example.php) for complete working examples.

## Standards Compliance

This SDK follows PHP-FIG standards:

- **PSR-1**: Basic Coding Standard
- **PSR-4**: Autoloading Standard
- **PSR-12**: Extended Coding Style Guide

## Support

- Documentation: https://custodydocs.chainup.com
- Email: custody@chainup.com

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

---

Made with ❤️ by [ChainUp Custody](https://custody.chainup.com)
