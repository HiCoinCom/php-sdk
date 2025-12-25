<?php
/**
 * Webhook Callback Handler Example
 * 
 * This file demonstrates how to handle webhook callbacks from ChainUp Custody
 * Place this file on your web server and configure the webhook URL in ChainUp console
 * 
 * Example webhook URL: https://yourdomain.com/webhook-handler.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Chainup\Waas\Custody\WaasClient;

// Configuration - Replace with your actual credentials
$appId = 'your-app-id';
$privateKey = 'your-private-key';
$publicKey = 'chainup-public-key';
$apiHost = 'https://openapi.chainup.com';

// Log file path
$logFile = __DIR__ . '/webhook.log';

/**
 * Write to log file
 */
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

/**
 * Process deposit notification
 */
function processDepositNotification($data) {
    writeLog("Processing deposit notification:");
    writeLog(json_encode($data, JSON_PRETTY_PRINT));
    
    // Example: Update your database
    // $db->updateDepositStatus($data['uid'], $data['symbol'], $data['amount']);
    
    // Example: Send notification to user
    // $notificationService->sendDepositConfirmation($data['uid'], $data['amount'], $data['symbol']);
    
    return true;
}

/**
 * Process withdrawal notification
 */
function processWithdrawalNotification($data) {
    writeLog("Processing withdrawal notification:");
    writeLog(json_encode($data, JSON_PRETTY_PRINT));
    
    // Example: Update your database
    // $db->updateWithdrawalStatus($data['request_id'], $data['status']);
    
    // Example: Send notification to user
    // $notificationService->sendWithdrawalConfirmation($data['uid'], $data['amount'], $data['symbol']);
    
    return true;
}

try {
    // Create WaaS Client
    $client = WaasClient::newBuilder()
        ->setHost($apiHost)
        ->setAppId($appId)
        ->setPrivateKey($privateKey)
        ->setPublicKey($publicKey)
        ->build();
    
    $asyncNotifyApi = $client->getAsyncNotifyApi();
    
    // Get POST data
    $postData = $_POST;
    
    // Log raw request
    writeLog("Received webhook request:");
    writeLog("POST data: " . json_encode($postData));
    
    // Extract encrypted data
    if (!isset($postData['data'])) {
        writeLog("ERROR: Missing 'data' field in POST request");
        http_response_code(400);
        echo json_encode(array('code' => 400, 'msg' => 'Missing data field'));
        exit;
    }
    
    $encryptedData = $postData['data'];
    
    // Decrypt notification using notifyRequest
    $notifyData = $asyncNotifyApi->notifyRequest($encryptedData);
    
    if ($notifyData === null) {
        writeLog("ERROR: Failed to decrypt notification data");
        http_response_code(400);
        echo json_encode(array('code' => 400, 'msg' => 'Invalid data format'));
        exit;
    }
    
    writeLog("Notification decrypted successfully");
    writeLog("Decrypted data: " . json_encode($notifyData));
    
    // Determine notification type and process accordingly
    $notificationType = isset($notifyData['side']) ? $notifyData['side'] : 'unknown';
    
    $success = false;
    switch ($notificationType) {
        case 'deposit':
            $success = processDepositNotification($notifyData);
            break;
            
        case 'withdraw':
        case 'withdrawal':
            $success = processWithdrawalNotification($notifyData);
            break;
            
        default:
            writeLog("WARNING: Unknown notification type: $notificationType");
            writeLog("Data: " . json_encode($notifyData));
            $success = true; // Still return success to avoid retries
            break;
    }
    
    if ($success) {
        writeLog("Webhook processed successfully");
        http_response_code(200);
        echo json_encode(array('code' => 0, 'msg' => 'Success'));
    } else {
        writeLog("ERROR: Failed to process webhook");
        http_response_code(500);
        echo json_encode(array('code' => 500, 'msg' => 'Processing failed'));
    }
    
} catch (\Exception $e) {
    writeLog("EXCEPTION: " . $e->getMessage());
    writeLog("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode(array(
        'code' => 500,
        'msg' => 'Internal server error'
    ));
}
