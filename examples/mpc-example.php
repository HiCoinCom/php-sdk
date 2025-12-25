<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Chainup\Waas\Mpc\MpcClient;

/**
 * MPC API Usage Example
 * 
 * This example demonstrates how to use the MPC SDK for various operations
 */

// Configuration
$domain = 'https://openapi.chainup.com';
$appId = '';
$rsaPrivateKey = '';
$waasPublicKey = '';

// Optional: Sign private key for transaction signing (if needed)
$signPrivateKey = '';

// Create MPC Client using Builder pattern
$mpcClient = MpcClient::newBuilder()
    ->setDomain($domain)
    ->setAppId($appId)
    ->setRsaPrivateKey($rsaPrivateKey)
    ->setWaasPublicKey($waasPublicKey)
    ->setSignPrivateKey($signPrivateKey)  // Optional: for transaction signing
    ->setDebug(false)
    ->build();

// ====================
// 1. Wallet Operations
// ====================
echo "=== Wallet Operations ===\n";

$walletApi = $mpcClient->getWalletApi();

// Create wallet
try {
    $result = $walletApi->createWallet(array(
        'sub_wallet_name' => 'Test Wallet ',
        'app_show_status' => 1
    ));
    if ($result->isSuccess()) {
        echo "Wallet created: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Create wallet address
try {
    $result = $walletApi->createWalletAddress(array(
        'sub_wallet_id' => 1000537,
        'symbol' => 'USDTERC20',
        'count' => 2
    ));
    if ($result->isSuccess()) {
        echo "Address created: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Query wallet address
try {
    $result = $walletApi->queryWalletAddress(array(
        'sub_wallet_id' => 1000537,
        'symbol' => 'USDTERC20',
        'max_id'=>0,
    ));
    if ($result->isSuccess()) {
        $data = $result->getData();
        echo "Wallet addresses: " . count($data ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get wallet assets
try {
    $result = $walletApi->getWalletAssets(array(
        'sub_wallet_id' => 1000537,
        'symbol' => 'USDTERC20',
    ));
    if ($result->isSuccess()) {
        echo "Wallet assets: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
// Change wallet show status
try {
    $result = $walletApi->changeWalletShowStatus(array(
        'sub_wallet_ids' => array(1000537),
        'app_show_status' => 1
    ));
    if ($result->isSuccess()) {
        echo "Show status changed: " . json_encode($result->isSuccess()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get wallet address info
try {
    $result = $walletApi->walletAddressInfo(array(
        'address' => '0x633A84Ee0ab29d911e5466e5E1CB9cdBf5917E72'
    ));
    if ($result->isSuccess()) {
        echo "Address info: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 2. Deposit Operations
// ====================
echo "\n=== Deposit Operations ===\n";

$depositApi = $mpcClient->getDepositApi();

// Get deposit records
try {
    $result = $depositApi->getDepositRecords(array(
       '2000000000', '5067926'
    ));
    if ($result->isSuccess()) {
        echo "Deposit records: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Sync deposit records
try {
    $result = $depositApi->syncDepositRecords(1);
    if ($result->isSuccess()) {
        $data = $result->getData();
        echo "Synced deposits: " . count($data ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 3. Withdrawal Operations
// ====================
echo "\n=== Withdrawal Operations ===\n";

$withdrawApi = $mpcClient->getWithdrawApi();


// Create withdrawal (with transaction signing)
try {
    $result = $withdrawApi->withdraw(array(
        'request_id' => '12345678',
            'sub_wallet_id' => 1000537,
            'symbol' => 'DOGE',
            'address_to' => 'DKjL5JXqCWF4V7DMRZt3nzr8ckg3nD4VDk',
            'amount' => '5',
            'remark' => 'Test withdrawal',
            'outputs' => "[{\"address_to\":\"DKjL5JXqCWF4V7DMRZt3nzr8ckg3nD4VDk\", \"amount\":\"2\"},{\"address_to\":\"DKjL5JXqCWF4V7DMRZt3nzr8ckg3nD4VDk\", \"amount\":\"3\"}]",
            'need_transaction_sign' => true,
    ));
    if ($result->isSuccess()) {
        echo "Withdrawal with sign created: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get withdrawal records
try {
    $result = $withdrawApi->getWithdrawRecords(
      array('123456781', 'withdraw_002')
    );
    if ($result->isSuccess()) {
        $data = $result->getData();
        echo "Withdrawal records: " . count($data ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Sync withdrawal records
try {
    $result = $withdrawApi->syncWithdrawRecords(12);
    if ($result->isSuccess()) {
        $data = $result->getData();
        echo "Synced withdrawals: " . count($data ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 4. Web3 Operations
// ====================
echo "\n=== Web3 Operations ===\n";

$web3Api = $mpcClient->getWeb3Api();

// Create Web3 transaction (with signature)
try {
    $result = $web3Api->createWeb3Trans(array(
        'request_id' => 'web3_sign_' . time(),
        'sub_wallet_id' => 123,
        'main_chain_symbol' => 'ETH',
        'interactive_contract' => '0xabcdef1234567890',
        'amount' => '500000000000000000',
        'gas_price' => '25',
        'gas_limit' => '50000',
        'input_data' => '0xa9059cbb',
        'trans_type' => '1',
        'need_transaction_sign' => true  // Requires signPrivateKey in config
    ));
    if ($result->isSuccess()) {
        echo "Web3 transaction with sign created: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get Web3 records
try {
    $result = $web3Api->getWeb3Records(array('web3_001', 'web3_002'));
    if ($result->isSuccess()) {
        $data = $result->getData();
        echo "Web3 records: " . count($data ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Sync Web3 records
try {
    $result = $web3Api->syncWeb3Records(0);
    if ($result->isSuccess()) {
        $data = $result->getData();
        echo "Synced Web3 records: " . count($data ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 5. Auto Sweep Operations
// ====================
echo "\n=== Auto Sweep Operations ===\n";

$autoSweepApi = $mpcClient->getAutoSweepApi();

// Get auto-collect sub wallets
try {
    $result = $autoSweepApi->autoCollectSubWallets('USDTERC20');
    if ($result->isSuccess()) {
        echo "Auto-collect sub wallets: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Set auto-collect symbol
try {
    $result = $autoSweepApi->setAutoCollectSymbol(array(
        'symbol' => 'USDTERC20',
        'collect_min' => '100.000000',
        'fueling_limit' => '10.000000'
    ));
    if ($result->isSuccess()) {
        echo "Auto-collect configured: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Sync auto-collect records
try {
    $result = $autoSweepApi->syncAutoCollectRecords(0);
    if ($result->isSuccess()) {
        echo "Synced auto-collect records: " . count($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 6. TRON Resource Operations
// ====================
echo "\n=== TRON Resource Operations ===\n";

$tronApi = $mpcClient->getTronResourceApi();

// Create TRON delegate (buy energy)
try {
    $result = $tronApi->createTronDelegate(array(
         'request_id' => '12345678902',
            'resource_type' => 1,
            'buy_type' => 0,
            'energy_num' => 32000,
            'address_from' => 'TPjJg9FnzQuYBd6bshgaq7rkH4s36zju5S',
            'address_to' => 'TGmBzYfBBtMfFF8v9PweTaPwn3WoB7aGPd',
            'contract_address' => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
            'service_charge_type' => '10010'
    ));
    if ($result->isSuccess()) {
        echo "TRON delegate created: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get buy resource records
try {
    $result = $tronApi->getBuyResourceRecords(array('12345678902', 'tron_002'));
    if ($result->isSuccess()) {
        echo "Resource records: " . count($result->getData() ?: []) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Sync buy resource records
try {
    $result = $tronApi->syncBuyResourceRecords(0);
    if ($result->isSuccess()) {
        echo "Synced resource records: " . count($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 7. Notification Operations
// ====================
echo "\n=== Notification Operations ===\n";

$notifyApi = $mpcClient->getNotifyApi();

// Decrypt notification (webhook callback)
$encryptedNotification = 'Af-uUJj8a2-Og7E5CwzANv4vo8NMf-z-DijwrIuK74Or8eRveM7G_-f0ErtX4WurcVrjdWC-tqU0BDhBwiDijbdyCFBvYB5UmLnHL_Rg13amhQTM-kaHoh-U9WPhYB3vGRwWkTwJ_aETERVVciAvoTf5CalqydMSe8G3KNz-ymrSVUe92DfW5ZdDKJm1hNYYteGJvg0hk--GRiPybPv2W78NlTLyWmXq094megsVzZv-KlsEGPUvPoBnEJ0Xu__AO-l-GfCG4rVO4rb8J01Nq_0Q9eRKcKWq0ci7MfnPPLMhtAWwRvSd3U8PUNHOLqGaJzOLraFnuFUHn90h7T23_DeAduA2W6dto99qb8YQ_iVnMnOKfE0Ls7Vv5S2qhgQJ0nl-BA3PPPOwW37cMb-wTbi3ZezU_S1NQEbrruEChkPhTaK0AqsM6mESV8wGflcWx3N9XPv6QatJ9zedBnkfJ4bJ4Vy2rUEtQF8eVc6zXhV8PuDRiSMf0V0yxzMjE6o9z0s087KSAqFphitlHvQMPJ29FUnyvCe_Czr5WPuhl89GOZjERE2uoNTfHqAlZVzMamoPv4y0qyIjJTufAQm-WwrQK9kGesky7eCiOXVdtR9UhEYpzEJSgXxENjUrHMx6D2AlEzlr17a2DgI-WrWB7oUnyiNnf__ElmLPPkJBdFUfzJByQkLxkUB0FLvTWdVbiIRPmPpdgb7jkhJsHUSOH0NmULqu8bYiEQtGfqRJh8I98qDzHWwfE_VAbqwATj2oD959Fm1eInBqh7eXGoy2WR3o00VpPrNvoE4eJNmw3WpVzlRF7ZVwOpcWRT-dHTShz9mB2Etk9P8D4rGmMZyXHkt4aGUJkE1b3cOEjzkOEFX8CaNe-VHiBYhIyFzMetn7mfIFB0hl565FGEumbhDKNNz_m9T2qPM5k4BQ9fLWUt_WJAVdC81_piIlBOQfYPDbdYoc_9ser1p-Jy5cgTyOMdWuSWC3jMsT09xr8dMcLkKmd39khGidAvGqOOPL1ST0';
try {
    $notifyData = $notifyApi->notifyRequest($encryptedNotification);
    if ($notifyData) {
        echo "Notification decrypted: " . json_encode($notifyData) . "\n";
        echo "Side: " . $notifyData['side'] . "\n";  // 'deposit' or 'withdraw'
        echo "Sub wallet ID: " . $notifyData['sub_wallet_id'] . "\n";
    } else {
        echo "Failed to decrypt notification\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ====================
// 8. Workspace Operations
// ====================
echo "\n=== Workspace Operations ===\n";

$workspaceApi = $mpcClient->getWorkSpaceApi();

// Get supported main chains
try {
    $result = $workspaceApi->getSupportMainChain();
    if ($result->isSuccess()) {
        echo "Supported main chains: " . count($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get coin details
try {
    $result = $workspaceApi->getCoinDetails(array(
        'symbol' => 'USDTERC20',
        'max_id' => '0',
        'limit' => '100',
    ));
    if ($result->isSuccess()) {
        echo "Coin details: " . count($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Get last block height
try {
    $result = $workspaceApi->getLastBlockHeight('ETH');
    if ($result->isSuccess()) {
        echo "Last block height: " . json_encode($result->getData()) . "\n";
    } else {
        echo "Error: " . $result->getMsg() . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Example completed ===\n";
