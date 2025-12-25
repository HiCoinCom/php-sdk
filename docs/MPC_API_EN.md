# ChainUp Custody MPC API Documentation

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](../LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D5.6-8892BF.svg)](https://www.php.net/)

Complete documentation for ChainUp Custody MPC (Multi-Party Computation) API SDK.

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [API Reference](#api-reference)
  - [Wallet API](#wallet-api)
  - [Deposit API](#deposit-api)
  - [Withdraw API](#withdraw-api)
  - [Web3 API](#web3-api)
  - [Auto Sweep API](#auto-sweep-api)
  - [TRON Resource API](#tron-resource-api)
  - [Notification API](#notification-api)
  - [Workspace API](#workspace-api)
- [Error Handling](#error-handling)
- [Code Examples](#code-examples)

## Introduction

The MPC (Multi-Party Computation) module provides a secure and decentralized approach to cryptocurrency wallet management. Unlike traditional custody solutions, MPC technology distributes private key generation and signing across multiple parties, eliminating single points of failure.

### Key Features

- ✅ **Multi-Party Computation** - Distributed key generation and signing
- ✅ **Multiple Wallets** - Support for creating and managing multiple sub-wallets
- ✅ **Multi-Chain Support** - Support for 50+ blockchain networks
- ✅ **Transaction Signing** - Secure transaction signing with MPC technology
- ✅ **Auto Sweep** - Automatic fund collection from sub-wallets
- ✅ **TRON Resources** - TRON energy and bandwidth management
- ✅ **Web3 Integration** - Smart contract interaction support
- ✅ **Result Pattern** - Unified result handling across all APIs

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

### Initialize MPC Client

```php
<?php
require_once 'vendor/autoload.php';

use Chainup\Waas\Mpc\MpcClient;

// Create MPC Client
$mpcClient = MpcClient::newBuilder()
    ->setDomain('https://openapi.chainup.com')
    ->setAppId('your-app-id')
    ->setRsaPrivateKey('your-rsa-private-key')
    ->setWaasPublicKey('chainup-public-key')
    ->setSignPrivateKey('your-sign-private-key')  // For transaction signing
    ->setDebug(false)
    ->build();
```

### Basic Usage Example

```php
// Get Wallet API
$walletApi = $mpcClient->getWalletApi();

// Create a new wallet
$result = $walletApi->createWallet(array(
    'sub_wallet_name' => 'My Wallet',
    'app_show_status' => 1
));

if ($result->isSuccess()) {
    $walletId = $result->getData()['sub_wallet_id'];
    echo "Wallet created successfully! ID: {$walletId}\n";
} else {
    echo "Error: " . $result->getMsg() . "\n";
}
```

## Configuration

### Builder Pattern

The MPC Client uses the Builder pattern for flexible configuration:

```php
$mpcClient = MpcClient::newBuilder()
    ->setDomain($domain)           // Required: API endpoint
    ->setAppId($appId)              // Required: Your app ID
    ->setRsaPrivateKey($rsaKey)     // Required: RSA private key for request encryption
    ->setWaasPublicKey($waasKey)    // Required: WaaS public key for response decryption
    ->setSignPrivateKey($signKey)   // Optional: Private key for transaction signing
    ->setDebug(true)                // Optional: Enable debug mode
    ->build();
```

### Key Types

1. **RSA Private Key** (`rsaPrivateKey`)

   - Used for encrypting API requests
   - Format: PEM format private key

2. **WaaS Public Key** (`waasPublicKey`)

   - ChainUp's public key for decrypting responses
   - Provided by ChainUp

3. **Sign Private Key** (`signPrivateKey`)
   - Used for signing blockchain transactions (withdraw, web3)
   - Optional but required for transaction operations
   - Format: PEM format private key

## API Reference

### Wallet API

Manage MPC wallets and addresses.

#### Create Wallet

Create a new sub-wallet.

```php
$walletApi = $mpcClient->getWalletApi();

$result = $walletApi->createWallet(array(
    'sub_wallet_name' => 'My Wallet',  // Required: wallet name
    'app_show_status' => 1             // Required: 1=show, 2=hide
));
```

**Parameters:**

- `sub_wallet_name` (string, required): Wallet name
- `app_show_status` (int, required): Display status (1=show, 2=hide)

**Returns:** Result object with `sub_wallet_id`

#### Create Wallet Address

Generate a new address for a sub-wallet.

```php
$result = $walletApi->createWalletAddress(array(
    'sub_wallet_id' => 123456,     // Required: wallet ID
    'chain_symbol' => 'ETH',       // Required: blockchain symbol
    'symbol' => 'USDT',            // Required: token symbol
    'address_count' => 1,          // Optional: number of addresses (default: 1)
    'address_type' => 1            // Optional: address type (default: 1)
));
```

**Returns:** Result object with generated address information

#### Query Wallet Address

Query addresses for a wallet.

```php
$result = $walletApi->queryWalletAddress(array(
    'sub_wallet_id' => 123456,
    'symbol' => 'USDT',
    'max_id' => 0,        // Optional: pagination
    'page_size' => 100    // Optional: items per page
));
```

#### Get Wallet Assets

Get asset balance for a wallet.

```php
$result = $walletApi->getWalletAssets(array(
    'sub_wallet_id' => 123456,
    'symbol' => 'USDT'
));
```

**Returns:** Balance information including `normal_balance`, `collecting_balance`, `lock_balance`

#### Change Wallet Show Status

Update wallet display status.

```php
$result = $walletApi->changeWalletShowStatus(array(
    'sub_wallet_ids' => array(123, 456),  // Or comma-separated string
    'app_show_status' => 1                // 1=show, 2=hide
));
```

#### Wallet Address Info

Verify and get address information.

```php
$result = $walletApi->walletAddressInfo(array(
    'address' => '0x1234...',
    'memo' => ''           // Optional: for memo-required chains
));
```

---

### Deposit API

Track and manage deposits.

#### Get Deposit Records

Query deposit records by IDs.

```php
$depositApi = $mpcClient->getDepositApi();

$result = $depositApi->getDepositRecords(array(
    'deposit_001',
    'deposit_002'
));
```

**Returns:** Array of deposit records

#### Sync Deposit Records

Sync recent deposit records.

```php
$result = $depositApi->syncDepositRecords(
    1,          // max_id: start from this ID (0 for all)
    100         // page_size: records per request (optional)
);
```

**Returns:** Paginated deposit records

---

### Withdraw API

Process and track withdrawals.

#### Withdraw (with Transaction Signing)

Withdraw cryptocurrency from a wallet.

```php
$withdrawApi = $mpcClient->getWithdrawApi();

$result = $withdrawApi->withdraw(array(
    'request_id' => 'withdraw_' . time(),  // Required: unique ID
    'sub_wallet_id' => 123456,             // Required: source wallet
    'symbol' => 'USDT',                    // Required: token symbol
    'amount' => '10.5',                    // Required: withdrawal amount
    'address_to' => '0x1234...',           // Required: recipient address
    'memo' => '',                          // Optional: for memo chains
    'remark' => 'Test withdrawal'          // Optional: internal note
));
```

**Note:** Requires `signPrivateKey` to be configured in MpcClient.

**Returns:** Withdrawal record with transaction ID

#### Get Withdrawal Records

Query withdrawal records by request IDs.

```php
$result = $withdrawApi->getWithdrawRecords(array(
    'withdraw_001',
    'withdraw_002'
));
```

#### Sync Withdrawal Records

Sync recent withdrawal records.

```php
$result = $withdrawApi->syncWithdrawRecords(
    1,          // max_id: start from this ID
    100         // page_size: optional
);
```

---

### Web3 API

Interact with smart contracts.

#### Create Web3 Transaction

Execute a smart contract transaction.

```php
$web3Api = $mpcClient->getWeb3Api();

$result = $web3Api->createWeb3Trans(array(
    'request_id' => 'web3_' . time(),      // Required: unique ID
    'sub_wallet_id' => 123456,             // Required: wallet ID
    'main_chain_symbol' => 'ETH',          // Required: blockchain
    'interactive_contract' => '0xabc...',  // Required: contract address
    'trans_type' => 1,                     // Required: transaction type
    'amount' => '0',                       // Required: ETH amount (usually 0)
    'input_data' => '0x...',               // Required: encoded function call
    'gas_price' => '20',                   // Optional: gas price (Gwei)
    'gas_limit' => '21000',                // Optional: gas limit
    'remark' => 'Contract call'            // Optional: note
));
```

**Transaction Types:**

- `1`: Contract call (default)
- `2`: Token transfer
- `3`: NFT transfer

**Returns:** Transaction record with txid

#### Accelerate Web3 Transaction

Speed up a pending transaction by increasing gas price.

```php
$result = $web3Api->accelerationWeb3Trans(array(
    'request_id' => 'web3_001',    // Required: original request ID
    'gas_price' => '50'            // Required: new gas price (Gwei)
));
```

#### Get Web3 Records

Query Web3 transaction records.

```php
$result = $web3Api->getWeb3Records(array(
    'web3_001',
    'web3_002'
));
```

#### Sync Web3 Records

Sync recent Web3 transaction records.

```php
$result = $web3Api->syncWeb3Records(
    0,          // max_id
    100         // page_size: optional
);
```

---

### Auto Sweep API

Automatic fund collection configuration.

#### Auto Collect Sub Wallets

Configure automatic sweep for specific wallets.

```php
$autoSweepApi = $mpcClient->getAutoSweepApi();

$result = $autoSweepApi->autoCollectSubWallets(array(
    'sub_wallet_ids' => array(123, 456),  // Required: wallet IDs
    'symbol' => 'USDT',                   // Required: token symbol
    'collect_threshold' => '100',         // Required: minimum amount to trigger
    'chain_symbol' => 'ETH'               // Required: blockchain
));
```

#### Set Auto Collect Symbol

Enable/disable auto-sweep for a token.

```php
$result = $autoSweepApi->setAutoCollectSymbol(array(
    'symbol' => 'USDT',
    'status' => 1          // 1=enable, 0=disable
));
```

#### Sync Auto Collect Records

Get auto-sweep execution records.

```php
$result = $autoSweepApi->syncAutoCollectRecords(
    0,          // max_id
    100         // page_size: optional
);
```

---

### TRON Resource API

Manage TRON energy and bandwidth.

#### Create TRON Delegate

Delegate TRON resources (energy/bandwidth).

```php
$tronApi = $mpcClient->getTronResourceApi();

$result = $tronApi->createTronDelegate(array(
    'request_id' => 'tron_' . time(),      // Required: unique ID
    'address_from' => 'TXxx...',           // Required: source address
    'address_to' => 'TYyy...',             // Required: recipient address
    'contract_address' => '',              // Required for buy_type 0,2
    'resource_type' => 1,                  // Required: 0=bandwidth, 1=energy
    'buy_type' => 1,                       // Required: 0=delegate, 1=rent, 2=rent for contract
    'energy_num' => 32000,                 // Optional: energy amount
    'net_num' => 0                         // Optional: bandwidth amount
));
```

**Buy Types:**

- `0`: Delegate (stake for others)
- `1`: Rent (purchase temporary resources)
- `2`: Rent for specific contract usage

#### Get Buy Resource Records

Query TRON resource purchase records.

```php
$result = $tronApi->getBuyResourceRecords(array(
    'tron_001',
    'tron_002'
));
```

#### Sync Buy Resource Records

Sync TRON resource purchase history.

```php
$result = $tronApi->syncBuyResourceRecords(
    0,          // max_id
    100         // page_size: optional
);
```

---

### Notification API

Handle encrypted notification callbacks.

#### Decrypt Notification

Decrypt notification data from ChainUp callbacks.

```php
$notifyApi = $mpcClient->getNotifyApi();

// In your notification endpoint
$encryptedData = $_POST['data'];  // From callback

try {
    $decryptedData = $notifyApi->decryptNotification($encryptedData);

    // Process the notification
    $type = $decryptedData['type'];  // e.g., 'deposit', 'withdraw', etc.

    // Return success response
    echo "success";
} catch (\Exception $e) {
    echo "Failed to decrypt notification: " . $e->getMessage();
}
```

**Note:** Always return "success" in the response to acknowledge receipt.

---

### Workspace API

Query blockchain and token configuration.

#### Get Supported Main Chains

Get list of supported blockchain networks.

```php
$workspaceApi = $mpcClient->getWorkspaceApi();

$result = $workspaceApi->getSupportMainChain();
```

**Returns:** Array of supported blockchains with configuration

#### Get Coin Details

Get detailed information about a specific token.

```php
$result = $workspaceApi->getCoinDetails(array(
    'coin_net' => 'Ethereum',
    'symbol' => 'USDT'
));
```

**Returns:** Token configuration including decimals, addresses, fees, etc.

#### Get Last Block Height

Get the latest block height for a blockchain.

```php
$result = $workspaceApi->getLastBlockHeight(array(
    'symbol' => 'ETH'
));
```

**Returns:** Current block height

---

## Error Handling

All API methods return a `Result` object with the following methods:

### Result Object Methods

```php
// Check if request was successful
if ($result->isSuccess()) {
    // Success: code == 0
    $data = $result->getData();
}

// Check if request failed
if ($result->isError()) {
    // Error: code != 0
    $errorMsg = $result->getMsg();
    $errorCode = $result->getCode();
}

// Get response code
$code = $result->getCode();  // 0 = success, other = error

// Get response message
$message = $result->getMsg();

// Get response data
$data = $result->getData();  // null if error
```

### Common Error Codes

| Code  | Meaning                               |
| ----- | ------------------------------------- |
| 0     | Success                               |
| -1    | Request failed (network/server error) |
| -2    | JSON parse failed                     |
| -3    | Encryption failed                     |
| -4    | Decryption failed                     |
| 10001 | Parameter error                       |
| 10002 | Signature verification failed         |
| 10003 | Insufficient balance                  |
| 10004 | Address not found                     |

### Exception Handling

Always wrap API calls in try-catch blocks:

```php
try {
    $result = $walletApi->createWallet(array(
        'sub_wallet_name' => 'My Wallet',
        'app_show_status' => 1
    ));

    if ($result->isSuccess()) {
        // Process successful response
        $data = $result->getData();
    } else {
        // Handle business logic error
        echo "Error: " . $result->getMsg();
    }
} catch (\Exception $e) {
    // Handle exception (validation, network, etc.)
    echo "Exception: " . $e->getMessage();
}
```

## Code Examples

### Complete Workflow Example

```php
<?php
require_once 'vendor/autoload.php';

use Chainup\Waas\Mpc\MpcClient;

// Initialize client
$mpcClient = MpcClient::newBuilder()
    ->setDomain('https://openapi.chainup.com')
    ->setAppId('your-app-id')
    ->setRsaPrivateKey($rsaPrivateKey)
    ->setWaasPublicKey($waasPublicKey)
    ->setSignPrivateKey($signPrivateKey)
    ->build();

// 1. Create a wallet
$walletApi = $mpcClient->getWalletApi();
$result = $walletApi->createWallet(array(
    'sub_wallet_name' => 'Trading Wallet',
    'app_show_status' => 1
));

if (!$result->isSuccess()) {
    die("Failed to create wallet: " . $result->getMsg());
}

$walletId = $result->getData()['sub_wallet_id'];
echo "Wallet created: {$walletId}\n";

// 2. Create an address
$result = $walletApi->createWalletAddress(array(
    'sub_wallet_id' => $walletId,
    'chain_symbol' => 'ETH',
    'symbol' => 'USDT'
));

if ($result->isSuccess()) {
    $address = $result->getData()['address'];
    echo "Address created: {$address}\n";
}

// 3. Check balance
$result = $walletApi->getWalletAssets(array(
    'sub_wallet_id' => $walletId,
    'symbol' => 'USDT'
));

if ($result->isSuccess()) {
    $balance = $result->getData()['normal_balance'];
    echo "Current balance: {$balance} USDT\n";
}

// 4. Withdraw funds
$withdrawApi = $mpcClient->getWithdrawApi();
$result = $withdrawApi->withdraw(array(
    'request_id' => 'withdraw_' . time(),
    'sub_wallet_id' => $walletId,
    'symbol' => 'USDT',
    'amount' => '10.5',
    'address_to' => '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb'
));

if ($result->isSuccess()) {
    $txid = $result->getData()['txid'];
    echo "Withdrawal submitted: {$txid}\n";
} else {
    echo "Withdrawal failed: " . $result->getMsg() . "\n";
}
```

### Pagination Example

```php
// Sync all deposit records with pagination
$depositApi = $mpcClient->getDepositApi();
$maxId = 0;
$allRecords = array();

do {
    $result = $depositApi->syncDepositRecords($maxId, 100);

    if (!$result->isSuccess()) {
        break;
    }

    $records = $result->getData() ?: array();
    $allRecords = array_merge($allRecords, $records);

    // Update maxId for next page
    if (count($records) > 0) {
        $lastRecord = end($records);
        $maxId = $lastRecord['id'];
    }

} while (count($records) == 100);  // Continue if page is full

echo "Total records: " . count($allRecords) . "\n";
```

### Web3 Contract Call Example

```php
// Example: Call approve() on ERC20 token
$web3Api = $mpcClient->getWeb3Api();

// Encode function call: approve(address spender, uint256 amount)
// Function signature: 0x095ea7b3
// Spender address (32 bytes): 0x000000000000000000000000742d35Cc6634C0532925a3b844Bc9e7595f0bEb
// Amount (32 bytes): 0x00000000000000000000000000000000000000000000d3c21bcecceda1000000 (1000 USDT)

$inputData = '0x095ea7b3' .
    '000000000000000000000000742d35Cc6634C0532925a3b844Bc9e7595f0bEb' .
    '00000000000000000000000000000000000000000000d3c21bcecceda1000000';

$result = $web3Api->createWeb3Trans(array(
    'request_id' => 'approve_' . time(),
    'sub_wallet_id' => 123456,
    'main_chain_symbol' => 'ETH',
    'interactive_contract' => '0xdAC17F958D2ee523a2206206994597C13D831ec7',  // USDT contract
    'trans_type' => 1,
    'amount' => '0',
    'input_data' => $inputData,
    'gas_limit' => '100000'
));

if ($result->isSuccess()) {
    echo "Approval transaction submitted\n";
}
```

## Best Practices

### 1. Security

- **Never commit private keys** to version control
- Store keys in environment variables or secure key management systems
- Use different keys for development and production
- Rotate keys periodically

### 2. Error Handling

- Always check `isSuccess()` before accessing data
- Handle both Result errors and exceptions
- Log errors for debugging

### 3. Request IDs

- Use unique `request_id` for each transaction
- Include timestamp for easy tracking: `'withdraw_' . time()`
- Store request IDs in your database for reconciliation

### 4. Pagination

- Use `max_id` for efficient pagination
- Process records in batches
- Handle partial results gracefully

### 5. Callback Handling

- Validate and decrypt callback data
- Always return "success" to acknowledge
- Process callbacks asynchronously
- Implement idempotency (same callback may arrive multiple times)

## Support

- **Documentation**: https://custodydocs-en.chainup.com
- **API Reference**: https://custodydocs-en.chainup.com/api-references/intro/custody-apis
- **Email**: custody@chainup.com
- **GitHub**: https://github.com/ChainUp-Custody/php-sdk

## License

MIT License - see [LICENSE](../LICENSE) file for details.

---

Made with ❤️ by [ChainUp Custody](https://custody.chainup.com)
