<?php
/**
 * AsyncNotify API Example
 * Demonstrates how to use AsyncNotifyApi for decrypting webhook notifications
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Chainup\Waas\Custody\WaasClient;

// Configuration
$appId = '';
$privateKey = '';
$publicKey = '';
$apiHost = 'https://openapi.chainup.com';

try {
    // Create WaaS Client
    $client = WaasClient::newBuilder()
        ->setHost($apiHost)
        ->setAppId($appId)
        ->setPrivateKey($privateKey)
        ->setPublicKey($publicKey)
        ->setVersion('v2')
        ->setDebug(false)
        ->build();

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

    echo "\n=== Example Complete ===\n";
    echo "\nNote: In a real webhook scenario, you would:\n";
    echo "1. Receive encrypted data from WaaS callback (\$_POST['data'])\n";
    echo "2. Use notifyRequest() to decrypt and parse the notification\n";
    echo "3. Process the notification (update database, send confirmation, etc.)\n";
    echo "4. For withdrawal verification, use verifyRequest() to decrypt the request\n";
    echo "5. Use verifyResponse() to encrypt your approval/rejection response\n";

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
