<?php
/**
 * ChainUp Custody PHP SDK - WaaS (Custody) Example
 * 
 * This example demonstrates how to use the WaaS Client for Custody API operations
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Chainup\Waas\Custody\WaasClient;

// Configuration
$appId = '';
$privateKey = '';
$publicKey = '';
$apiHost = 'https://openapi.chainup.com';

try {
    // ========== Create WaaS Client using Builder Pattern ==========
    $client = WaasClient::newBuilder()
        ->setHost($apiHost)
        ->setAppId($appId)
        ->setPrivateKey($privateKey)
        ->setPublicKey($publicKey)
        ->setVersion('v2')
        ->setDebug(false)  // Enable debug mode
        ->build();

    echo "=== ChainUp Custody WaaS Client Example ===\n\n";

    // ========== User API Examples ==========
    echo "1. User API Examples\n";
    echo "-------------------\n";
    
    $userApi = $client->getUserApi();
    
    // Register user by email
    echo "Registering user by email...\n";
    $result = $userApi->registerEmailUser('user1@example.com');
    if ($result->isSuccess()) {
        echo "Success! User UID: " . $result->getData()['uid'] . "\n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
    
    // Register user by mobile
    echo "\nRegistering user by mobile...\n";
    $result = $userApi->registerMobileUser('86', '13800000002');
    if ($result->isSuccess()) {
        echo "Success! User UID: " . $result->getData()['uid'] . "\n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
     
    // Get user info
    echo "\nGetting user info by mobile...\n";
    $result = $userApi->getMobileUser('86', '13800000002');
    if ($result->isSuccess()) {
        $userInfo = $result->getData();
        echo "User Info: \n";
        print_r($userInfo);
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
    
    // Get user info
    echo "\nGetting user info by email...\n";
    $result = $userApi->getEmailUser('user1@example.com');
    if ($result->isSuccess()) {
        $userInfo = $result->getData();
        echo "User Info: \n";
        print_r($userInfo);
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

     // Get user info
    echo "\nGetting user list...\n";
    $result = $userApi->syncUserList();
    if ($result->isSuccess()) {
        $userInfo = $result->getData();
        echo "User Info: \n";
        //print_r($userInfo);
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

    // ========== Account API Examples ==========
    echo "\n2. Account API Examples\n";
    echo "----------------------\n";
    
    $accountApi = $client->getAccountApi();
    
    // Get user account balance
    echo "Getting user account balance...\n";
    $result = $accountApi->getUserAccount(15036904, 'APTOS');
    if ($result->isSuccess()) {
        $account = $result->getData();
        // Print actual data structure to see what fields are available
        print_r($account);
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
    
    // Get deposit address
    echo "\nGetting deposit address...\n";
    $result = $accountApi->getUserAddress(15036904, 'APTOS');
    if ($result->isSuccess()) {
        $address = $result->getData();
        echo "Deposit Address: {$address['address']}\n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

     // Sync deposit address
    echo "\nSync deposit address...\n";
    $result = $accountApi->syncUserAddressList(0);
    if ($result->isSuccess()) {
        $address = $result->getData();
        //print_r($address);
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
    
    // Get company account
    echo "\nGetting company account...\n";
    $result = $accountApi->getCompanyAccount('APTOS');
    if ($result->isSuccess()) {
        $account = $result->getData();
        print_r($account);
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

    // ========== Billing API Examples ==========
    echo "\n3. Billing API Examples\n";
    echo "----------------------\n";
    
    $billingApi = $client->getBillingApi();
    
    // Create withdrawal
    echo "Creating withdrawal...\n";
    $result = $billingApi->withdraw(
        '123456781',  // Unique request ID
        15036904,                 // From UID
        '0x0f1dc222af5ea2660ff84ae91adc48f1cb2d4991f1e6569dd24d94599c335a06',  // To address
        '0.01',                 // Amount
        'APTOS'                  // Symbol
    );
    if ($result->isSuccess()) {
        echo "Withdrawal created successfully!\n";
        print_r($result->getData());
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
    
    // Sync deposit list
    echo "\nSyncing deposit list...\n";
    $result = $billingApi->syncDepositList(0);
    if ($result->isSuccess()) {
        $deposits = $result->getData() ?: array();
        echo "Found " . count($deposits) . " deposits\n";
        foreach ($deposits as $deposit) {
            echo "  - {$deposit['symbol']}: {$deposit['amount']}\n";
            break;
        }
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

    echo "\nGet deposit list...\n";
    $result = $billingApi->depositList([5950279,231]);
    if ($result->isSuccess()) {
        $deposits = $result->getData() ?: array();
        echo "Found " . count($deposits) . " deposits\n";
        foreach ($deposits as $deposit) {
            echo "  - {$deposit['symbol']}: {$deposit['amount']}\n";
            break;
        }
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }
    
    // Sync withdrawal list
    echo "\nSyncing withdrawal list...\n";
    $result = $billingApi->syncWithdrawList(0);
    if ($result->isSuccess()) {
        $withdrawals = $result->getData() ?: array();
        echo "Found " . count($withdrawals) . " withdrawals\n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

     echo "\nget withdrawal list...\n";
    $result = $billingApi->withdrawList(["12345678"]);
    if ($result->isSuccess()) {
        $withdrawals = $result->getData() ?: array();
        echo "Found " . count($withdrawals) . " withdrawals\n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }


  // Sync minerFee list
    echo "\nSyncing minerFee list...\n";
    $result = $billingApi->syncMinerFeeList(0);
    if ($result->isSuccess()) {
        $minerFees = $result->getData() ?: array();
        echo "Found " . count($minerFees) . " miner fees  \n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

     echo "\nget minerFee list...\n";
    $result = $billingApi->minerFeeList(["12345678"]);
    if ($result->isSuccess()) {
        $minerFees = $result->getData() ?: array();
        echo "Found " . count($minerFees) . " miner fees  \n";
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

    // ========== Coin API Examples ==========
    echo "\n4. Coin API Examples\n";
    echo "-------------------\n";
    
    $coinApi = $client->getCoinApi();
    
    // Get supported coin list
    echo "Getting supported coin list...\n";
    $result = $coinApi->getCoinList();
    if ($result->isSuccess()) {
        $coins = $result->getData();
        echo "Supported coins: " . count($coins) . "\n";
        foreach (array_slice($coins, 0, 5) as $coin) {
            $coinName = isset($coin['symbol_alias']) ? $coin['symbol_alias'] : $coin['symbol'];
            echo "  - {$coin['symbol']}: {$coinName} (Network: {$coin['coin_net']})\n";
            break;
        }
    } else {
        echo "Error: [{$result->getCode()}] {$result->getMsg()}\n";
    }

    echo "=== ChainUp AsyncNotify API Example ===\n\n";

    $asyncNotifyApi = $client->getAsyncNotifyApi();

    // ========== Example 1: Decrypt Notification Request ==========
    echo "1. Decrypt Notification Request\n";
    echo "-------------------------------\n";
    
    // Simulate receiving encrypted notification data from WaaS callback
    // In real scenario, this would come from $_POST['data'] or similar
    $encryptedNotify = "jhoA9MtGotqWxqEtB27SwCtJCo9JSIxh2B6m8CItrPQj2gsm6rw-ti1qY5tNP52qXg60FLK49cFj-a84m-57z8aT-Vo-YyJPTcM8Qpuyjj5Pf8tAcbBjBHganULYNPjCCkzgH5n5dlMZIp0tmpc7nV7Pp6hi63KjGGNTfAAbWp7QOVukAsQeQyBFPeKhlVEhq8xqQEN2yg_T1jHRUjIdlTDn2LG_i2tI0MlDpPg5FHL6cViSVM23WBPhJnAFOOrGhaqq06YtVG2m8_x_pLTyI5ZK61Bv0HnDUuIkDuRqNXyhko0sG9uGuKWJ3maWfUc9bSb0VcWPHeWnYUrcE2M9TVtwTEKdcImqZnvjc12YUh_Oz2a9VNls_XN_gTRbeIiTUGsiXX1Yq6OkCCxrsCgD0AXz0KOX4uphZldXq17ZO7sU21-b1y0rsk0qY6PbKRYpp4hhdeKpEfB2gckhf1rc9h17j0ufri4LqsE4EccGuQD4JcSrT5RLY4QRil4wdIO9ZPmhb-Od3zqT9OYPSvPg0QVCVpw-Tn17WfsZw2xB9gO8uzvGcvz9TfUrI8zKg6b6roTR9xt0m0oqMCyhrjAlU35QUh54MHAWI22A3WJkR4d4KhTOrq-2KuCg7Obi3SCoZmVWb28tztUwN6ttc4PJmM370g_YNCiv5Q6F95QgozYAGpu7Kc8ckcsORixNAUpqTCYaZHmST7bxCXDGPaL45H4zHe6IkU-Tf06rY7DoKeMgjGTz3Pb8hrXRXdSCYz9y0MjwGledXqnLiww0Dn_q-qWgOqQs6NeiLG5IqWKJG2e0buav2l_fH-biflRHjpidaTvFnTMUPf9k9-ygWwiWDzM9OD0X-mNdEI6WNe_27O9CtmUTxlBgRJ2tYyhF32a3flQXaA4m34PPXD_HyxFYRQXfqTt_7uaV7NinsnwN8Ll9ccFdXw8BuANu8j24zvBP0zvUyo9d1ywqn0Cw2wt-vPUWF7sZifTLkdr9O7mcAN08ByaIc1MR5ULI-lUsfi6U";
    
    echo "Decrypting notification data...\n";
    $notifyData = $asyncNotifyApi->notifyRequest($encryptedNotify);
    
    if ($notifyData !== null) {
        echo "Notification decrypted successfully!\n";
        echo "Notification type: " . ($notifyData['side'] ?? 'unknown') . "\n";
        echo "Full data:\n";
        print_r($notifyData);
    } else {
        echo "Failed to decrypt notification (this is expected with dummy data)\n";
    }

    // ========== Example 2: Decrypt Withdrawal Verification Request ==========
    echo "\n2. Decrypt Withdrawal Verification Request\n";
    echo "------------------------------------------\n";
    
    // Simulate receiving encrypted withdrawal verification request
    $encryptedVerify = "jhoA9MtGotqWxqEtB27SwCtJCo9JSIxh2B6m8CItrPQj2gsm6rw-ti1qY5tNP52qXg60FLK49cFj-a84m-57z8aT-Vo-YyJPTcM8Qpuyjj5Pf8tAcbBjBHganULYNPjCCkzgH5n5dlMZIp0tmpc7nV7Pp6hi63KjGGNTfAAbWp7QOVukAsQeQyBFPeKhlVEhq8xqQEN2yg_T1jHRUjIdlTDn2LG_i2tI0MlDpPg5FHL6cViSVM23WBPhJnAFOOrGhaqq06YtVG2m8_x_pLTyI5ZK61Bv0HnDUuIkDuRqNXyhko0sG9uGuKWJ3maWfUc9bSb0VcWPHeWnYUrcE2M9TVtwTEKdcImqZnvjc12YUh_Oz2a9VNls_XN_gTRbeIiTUGsiXX1Yq6OkCCxrsCgD0AXz0KOX4uphZldXq17ZO7sU21-b1y0rsk0qY6PbKRYpp4hhdeKpEfB2gckhf1rc9h17j0ufri4LqsE4EccGuQD4JcSrT5RLY4QRil4wdIO9ZPmhb-Od3zqT9OYPSvPg0QVCVpw-Tn17WfsZw2xB9gO8uzvGcvz9TfUrI8zKg6b6roTR9xt0m0oqMCyhrjAlU35QUh54MHAWI22A3WJkR4d4KhTOrq-2KuCg7Obi3SCoZmVWb28tztUwN6ttc4PJmM370g_YNCiv5Q6F95QgozYAGpu7Kc8ckcsORixNAUpqTCYaZHmST7bxCXDGPaL45H4zHe6IkU-Tf06rY7DoKeMgjGTz3Pb8hrXRXdSCYz9y0MjwGledXqnLiww0Dn_q-qWgOqQs6NeiLG5IqWKJG2e0buav2l_fH-biflRHjpidaTvFnTMUPf9k9-ygWwiWDzM9OD0X-mNdEI6WNe_27O9CtmUTxlBgRJ2tYyhF32a3flQXaA4m34PPXD_HyxFYRQXfqTt_7uaV7NinsnwN8Ll9ccFdXw8BuANu8j24zvBP0zvUyo9d1ywqn0Cw2wt-vPUWF7sZifTLkdr9O7mcAN08ByaIc1MR5ULI-lUsfi6U";
    
    echo "Decrypting withdrawal verification request...\n";
    $verifyData = $asyncNotifyApi->verifyRequest($encryptedVerify);
    
    if ($verifyData !== null) {
        echo "Verification request decrypted successfully!\n";
        echo "Request ID: " . ($verifyData['request_id'] ?? 'unknown') . "\n";
        echo "Full data:\n";
        print_r($verifyData);
    } else {
        echo "Failed to decrypt verification request (this is expected with dummy data)\n";
    }

    // ========== Example 3: Encrypt Withdrawal Verification Response ==========
    echo "\n3. Encrypt Withdrawal Verification Response\n";
    echo "-------------------------------------------\n";
    
    // Prepare withdrawal verification response
    $withdrawResponse = array(
        'request_id' => 'withdraw_12345',
        'status' => 1  // 1 = approve, 2 = reject
    );
    
    echo "Encrypting withdrawal verification response...\n";
    $encryptedResponse = $asyncNotifyApi->verifyResponse($withdrawResponse);
    
    if ($encryptedResponse !== null) {
        echo "Response encrypted successfully!\n";
        echo "Encrypted data (first 100 chars): " . substr($encryptedResponse, 0, 100) . "...\n";
    } else {
        echo "Failed to encrypt response\n";
    }

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== Example Complete ===\n";
