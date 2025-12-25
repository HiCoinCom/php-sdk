# MPC API 使用指南

ChainUp WaaS PHP SDK - MPC (Multi-Party Computation) API 完整实现

## 目录结构

```
src/mpc/
├── MpcClient.php           # MPC 主客户端（支持 Builder 模式）
├── MpcConfig.php           # MPC 配置类
└── api/
    ├── MpcBaseApi.php      # 基础 API 类（处理加密/解密）
    ├── WalletApi.php       # 钱包管理 API
    ├── DepositApi.php      # 存款 API
    ├── WithdrawApi.php     # 提现 API
    ├── Web3Api.php         # Web3 交易 API
    ├── AutoSweepApi.php    # 自动归集 API
    ├── NotifyApi.php       # 通知/Webhook API
    ├── WorkSpaceApi.php    # 工作空间管理 API
    └── TronResourceApi.php # TRON 资源管理 API
```

## 快速开始

### 1. 安装

```bash
composer require chainup/waas-php-sdk
```

### 2. 初始化客户端

```php
<?php
require_once 'vendor/autoload.php';

use Chainup\Waas\Mpc\MpcClient;

// 使用 Builder 模式创建客户端
$mpcClient = MpcClient::newBuilder()
    ->setDomain('https://api.example.com')
    ->setAppId('your_app_id')
    ->setRsaPrivateKey($privateKey)
    ->setWaasPublicKey($publicKey)
    ->setDebug(true)
    ->build();
```

## API 使用示例

### 钱包管理 API (WalletApi)

```php
$walletApi = $mpcClient->getWalletApi();

// 创建钱包地址
$result = $walletApi->createAddress(array(
    'sub_wallet_id' => 'sub_wallet_001',
    'chain_symbol' => 'ETH'
));

// 获取地址列表
$result = $walletApi->getAddressList(array(
    'sub_wallet_id' => 'sub_wallet_001',
    'page_index' => 1,
    'page_length' => 10
));

// 获取钱包余额
$result = $walletApi->getBalance('sub_wallet_001', 'USDT');

// 获取支持的链列表
$result = $walletApi->getSupportedChains();

// 获取支持的币种列表
$result = $walletApi->getSupportedCoins('ETH');

// 验证地址有效性
$result = $walletApi->validateAddress('ETH', '0x1234567890abcdef');
```

### 存款 API (DepositApi)

```php
$depositApi = $mpcClient->getDepositApi();

// 获取存款列表
$result = $depositApi->getDepositList(array(
    'sub_wallet_id' => 'sub_wallet_001',
    'coin_symbol' => 'USDT',
    'page_index' => 1,
    'page_length' => 10
));

// 获取存款详情
$result = $depositApi->getDepositDetails('0x1234567890abcdef');

// 同步存款列表（增量同步）
$result = $depositApi->syncDepositList(array(
    'max_id' => 1000,
    'limit' => 100
));

// 分配存款地址
$result = $depositApi->allocateDepositAddress('sub_wallet_001', 'ETH');

// 获取最小存款金额
$result = $depositApi->getMinDepositAmount('USDT', 'ETH');
```

### 提现 API (WithdrawApi)

```php
$withdrawApi = $mpcClient->getWithdrawApi();

// 创建提现
$result = $withdrawApi->createWithdraw(array(
    'request_id' => 'withdraw_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'coin_symbol' => 'USDT',
    'amount' => '100.00',
    'to_address' => '0xabcdef1234567890',
    'memo' => '',  // 某些链需要 memo
    'remark' => 'Test withdrawal'
));

// 获取提现列表
$result = $withdrawApi->getWithdrawList(array(
    'sub_wallet_id' => 'sub_wallet_001',
    'page_index' => 1,
    'page_length' => 10
));

// 获取提现详情
$result = $withdrawApi->getWithdrawDetails('withdraw_123456');

// 估算提现手续费
$result = $withdrawApi->estimateWithdrawFee('USDT', '100.00', 'MEDIUM');

// 取消待处理的提现
$result = $withdrawApi->cancelWithdraw('withdraw_123456');

// 加速提现（RBF）
$result = $withdrawApi->speedUpWithdraw('withdraw_123456', 'HIGH');
```

### Web3 API (Web3Api)

```php
$web3Api = $mpcClient->getWeb3Api();

// 创建 Web3 交易
$result = $web3Api->createTransaction(array(
    'request_id' => 'web3_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'chain_symbol' => 'ETH',
    'from_address' => '0x1234567890abcdef',
    'to_address' => '0xabcdef1234567890',
    'value' => '1000000000000000000', // 1 ETH in wei
    'data' => '0x',
    'gas_limit' => '21000'
));

// 获取 Gas 价格
$result = $web3Api->getGasPrice('ETH');

// 估算 Gas
$result = $web3Api->estimateGas(array(
    'chain_symbol' => 'ETH',
    'from_address' => '0x1234567890abcdef',
    'to_address' => '0xabcdef1234567890',
    'value' => '1000000000000000000'
));

// 获取 Nonce
$result = $web3Api->getNonce('ETH', '0x1234567890abcdef');

// 调用合约（只读）
$result = $web3Api->callContract(array(
    'chain_symbol' => 'ETH',
    'contract_address' => '0xcontract_address',
    'data' => '0xencoded_method_call'
));

// 发送原始交易
$result = $web3Api->sendRawTransaction('ETH', '0xsigned_raw_tx');

// 获取交易回执
$result = $web3Api->getTransactionReceipt('ETH', '0xtxid');

// 获取 ERC20 余额
$result = $web3Api->getErc20Balance('ETH', '0xtoken_address', '0xwallet_address');
```

### 自动归集 API (AutoSweepApi)

```php
$autoSweepApi = $mpcClient->getAutoSweepApi();

// 创建归集规则
$result = $autoSweepApi->createSweepRule(array(
    'sub_wallet_id' => 'sub_wallet_001',
    'coin_symbol' => 'USDT',
    'to_address' => '0xabcdef1234567890',
    'amount_threshold' => '100.00'
));

// 更新归集规则
$result = $autoSweepApi->updateSweepRule(array(
    'rule_id' => 'rule_123',
    'amount_threshold' => '200.00',
    'status' => 1
));

// 获取归集规则列表
$result = $autoSweepApi->getSweepRuleList(array(
    'sub_wallet_id' => 'sub_wallet_001'
));

// 启用/禁用规则
$result = $autoSweepApi->enableSweepRule('rule_123');
$result = $autoSweepApi->disableSweepRule('rule_123');

// 删除归集规则
$result = $autoSweepApi->deleteSweepRule('rule_123');

// 获取归集交易列表
$result = $autoSweepApi->getSweepTransactionList(array(
    'sub_wallet_id' => 'sub_wallet_001'
));

// 手动触发归集
$result = $autoSweepApi->manualSweep(
    'sub_wallet_001',
    'USDT',
    '0xfrom_address',
    '0xto_address'
);
```

### 通知 API (NotifyApi)

```php
$notifyApi = $mpcClient->getNotifyApi();

// 注册 Webhook
$result = $notifyApi->registerWebhook(array(
    'callback_url' => 'https://your-domain.com/webhook',
    'event_types' => array('DEPOSIT', 'WITHDRAW', 'WEB3_TRANSACTION')
));

// 更新 Webhook
$result = $notifyApi->updateWebhook(array(
    'webhook_id' => 'webhook_123',
    'callback_url' => 'https://new-domain.com/webhook',
    'status' => 1
));

// 获取 Webhook 列表
$result = $notifyApi->getWebhookList();

// 删除 Webhook
$result = $notifyApi->deleteWebhook('webhook_123');

// 获取通知日志
$result = $notifyApi->getNotificationLogList(array(
    'webhook_id' => 'webhook_123',
    'status' => 2  // 失败的通知
));

// 重发失败的通知
$result = $notifyApi->resendNotification('log_123');

// 测试 Webhook
$result = $notifyApi->testWebhook('webhook_123');

// 验证 Webhook 签名
$isValid = $notifyApi->verifyWebhookSignature($payload, $signature, $timestamp);
```

### TRON 资源 API (TronResourceApi)

```php
$tronApi = $mpcClient->getTronResourceApi();

// 获取账户资源
$result = $tronApi->getAccountResources('TXYZabc123456789');

// 冻结 TRX 获取能量
$result = $tronApi->freezeTrx(array(
    'request_id' => 'freeze_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'from_address' => 'TXYZabc123456789',
    'amount' => '1000000', // 1 TRX = 1,000,000 SUN
    'resource_type' => 'ENERGY'
));

// 解冻 TRX
$result = $tronApi->unfreezeTrx(array(
    'request_id' => 'unfreeze_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'from_address' => 'TXYZabc123456789',
    'resource_type' => 'ENERGY'
));

// 委托资源
$result = $tronApi->delegateResource(array(
    'request_id' => 'delegate_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'from_address' => 'TXYZabc123456789',
    'to_address' => 'TRecipient123456789',
    'amount' => '1000000',
    'resource_type' => 'ENERGY',
    'lock' => false
));

// 取消委托
$result = $tronApi->undelegateResource(array(
    'request_id' => 'undelegate_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'from_address' => 'TXYZabc123456789',
    'to_address' => 'TRecipient123456789',
    'resource_type' => 'ENERGY'
));

// 获取委托列表
$result = $tronApi->getDelegationList(array(
    'from_address' => 'TXYZabc123456789'
));

// 估算能量消耗
$result = $tronApi->estimateEnergy(array(
    'from_address' => 'TXYZabc123456789',
    'to_address' => 'TRecipient123456789'
));

// 获取资源价格
$result = $tronApi->getResourcePrice('ENERGY');

// 租赁能量
$result = $tronApi->rentEnergy(array(
    'request_id' => 'rent_' . time(),
    'sub_wallet_id' => 'sub_wallet_001',
    'receiver_address' => 'TXYZabc123456789',
    'amount' => '100000',
    'duration' => 24  // 24小时
));

// 获取能量租赁订单列表
$result = $tronApi->getEnergyRentalList(array(
    'sub_wallet_id' => 'sub_wallet_001'
));
```

### 工作空间 API (WorkSpaceApi)

```php
$workspaceApi = $mpcClient->getWorkSpaceApi();

// 获取工作空间信息
$result = $workspaceApi->getWorkspaceInfo();

// 获取工作空间统计
$result = $workspaceApi->getWorkspaceStats(array(
    'start_time' => strtotime('-30 days') * 1000,
    'end_time' => time() * 1000
));

// 创建 API Key
$result = $workspaceApi->createApiKey(array(
    'key_name' => 'My API Key',
    'permissions' => array('READ', 'WRITE'),
    'ip_whitelist' => '192.168.1.1,192.168.1.2'
));

// 更新 API Key
$result = $workspaceApi->updateApiKey(array(
    'api_key' => 'your_api_key',
    'key_name' => 'Updated Name',
    'status' => 1
));

// 获取 API Key 列表
$result = $workspaceApi->getApiKeyList();

// 删除 API Key
$result = $workspaceApi->deleteApiKey('your_api_key');

// 获取成员列表
$result = $workspaceApi->getMemberList();

// 邀请成员
$result = $workspaceApi->inviteMember(array(
    'email' => 'user@example.com',
    'role' => 'OPERATOR'
));

// 更新成员角色
$result = $workspaceApi->updateMemberRole('member_123', 'ADMIN');

// 移除成员
$result = $workspaceApi->removeMember('member_123');

// 获取操作日志
$result = $workspaceApi->getOperationLogList(array(
    'page_index' => 1,
    'page_length' => 50
));
```

## 返回值处理

所有 API 调用返回 `Result` 对象，包含以下方法：

```php
$result = $walletApi->getBalance('sub_wallet_001', 'USDT');

// 检查是否成功
if ($result->isSuccess()) {
    // 获取数据
    $data = $result->getData();
    echo "Balance: " . $data['balance'];
} else {
    // 获取错误信息
    echo "Error code: " . $result->getCode();
    echo "Error message: " . $result->getMsg();
}

// 直接获取属性
$code = $result->getCode();  // 返回码，0 表示成功
$msg = $result->getMsg();    // 消息
$data = $result->getData();  // 数据
```

## 参数命名规范

所有参数使用 `snake_case` 命名（与 JS SDK 保持一致）：

```php
array(
    'sub_wallet_id' => 'xxx',    // ✅ 正确
    'subWalletId' => 'xxx',      // ❌ 错误
    'chain_symbol' => 'ETH',     // ✅ 正确
    'chainSymbol' => 'ETH'       // ❌ 错误
)
```

## 错误处理

```php
try {
    $result = $withdrawApi->createWithdraw($params);

    if ($result->isSuccess()) {
        // 成功处理
        $data = $result->getData();
    } else {
        // 业务错误处理
        $errorCode = $result->getCode();
        $errorMsg = $result->getMsg();

        // 根据错误码进行不同处理
        switch ($errorCode) {
            case 1001:
                // 余额不足
                break;
            case 1002:
                // 地址无效
                break;
            default:
                // 其他错误
                break;
        }
    }
} catch (\Exception $e) {
    // 异常处理（网络错误、配置错误等）
    echo "Exception: " . $e->getMessage();
}
```

## 日志调试

启用调试模式查看详细日志：

```php
$mpcClient = MpcClient::newBuilder()
    ->setDomain($domain)
    ->setAppId($appId)
    ->setRsaPrivateKey($privateKey)
    ->setWaasPublicKey($publicKey)
    ->setDebug(true)  // 启用调试模式
    ->build();
```

或使用自定义 Logger：

```php
use Chainup\Waas\Utils\LoggerInterface;

class MyLogger implements LoggerInterface
{
    public function debug($message) {
        // 自定义调试日志
    }

    public function info($message) {
        // 自定义信息日志
    }

    public function error($message) {
        // 自定义错误日志
    }
}

$mpcClient = MpcClient::newBuilder()
    ->setLogger(new MyLogger())
    ->build();
```

## 注意事项

1. **密钥安全**：妥善保管 RSA 私钥，不要硬编码在代码中
2. **请求幂等**：使用唯一的 `request_id` 确保请求幂等性
3. **错误处理**：始终检查返回结果并处理错误情况
4. **参数验证**：在调用 API 前验证必需参数
5. **超时设置**：根据网络情况调整 HTTP 超时时间
6. **速率限制**：注意 API 调用频率限制

## 示例代码

完整示例请参考 `examples/mpc-example.php`

## 技术支持

如有问题，请联系 ChainUp 技术支持或查看完整 API 文档。
