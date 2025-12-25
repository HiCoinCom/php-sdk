# ChainUp Custody MPC API 中文文档

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](../LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D5.6-8892BF.svg)](https://www.php.net/)

ChainUp Custody MPC（多方计算）API SDK 完整中文文档。

## 目录

- [简介](#简介)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置说明](#配置说明)
- [API 参考](#api-参考)
  - [钱包 API](#钱包-api)
  - [充值 API](#充值-api)
  - [提现 API](#提现-api)
  - [Web3 API](#web3-api)
  - [自动归集 API](#自动归集-api)
  - [TRON 资源 API](#tron-资源-api)
  - [通知 API](#通知-api)
  - [工作空间 API](#工作空间-api)
- [错误处理](#错误处理)
- [代码示例](#代码示例)

## 简介

MPC（多方计算）模块提供了一种安全且去中心化的加密货币钱包管理方式。与传统托管方案不同，MPC 技术将私钥生成和签名操作分散到多方，消除了单点故障。

### 核心特性

- ✅ **多方计算** - 分布式密钥生成和签名
- ✅ **多钱包支持** - 支持创建和管理多个子钱包
- ✅ **多链支持** - 支持 50+ 区块链网络
- ✅ **交易签名** - 使用 MPC 技术进行安全交易签名
- ✅ **自动归集** - 从子钱包自动归集资金
- ✅ **TRON 资源** - TRON 能量和带宽管理
- ✅ **Web3 集成** - 支持智能合约交互
- ✅ **Result 模式** - 所有 API 统一的结果处理

## 安装

### 环境要求

- PHP >= 5.6
- GuzzleHTTP >= 6.5 或 >= 7.0
- OpenSSL 扩展

### 使用 Composer 安装

```bash
composer require chainup-waas/sdk
```

## 快速开始

### 初始化 MPC 客户端

```php
<?php
require_once 'vendor/autoload.php';

use Chainup\Waas\Mpc\MpcClient;

// 创建 MPC 客户端
$mpcClient = MpcClient::newBuilder()
    ->setDomain('https://openapi.chainup.com')
    ->setAppId('your-app-id')
    ->setRsaPrivateKey('your-rsa-private-key')
    ->setWaasPublicKey('chainup-public-key')
    ->setSignPrivateKey('your-sign-private-key')  // 用于交易签名
    ->setDebug(false)
    ->build();
```

### 基础使用示例

```php
// 获取钱包 API
$walletApi = $mpcClient->getWalletApi();

// 创建新钱包
$result = $walletApi->createWallet(array(
    'sub_wallet_name' => '我的钱包',
    'app_show_status' => 1
));

if ($result->isSuccess()) {
    $walletId = $result->getData()['sub_wallet_id'];
    echo "钱包创建成功！ID: {$walletId}\n";
} else {
    echo "错误: " . $result->getMsg() . "\n";
}
```

## 配置说明

### Builder 模式

MPC 客户端使用 Builder 模式进行灵活配置：

```php
$mpcClient = MpcClient::newBuilder()
    ->setDomain($domain)           // 必需：API 端点
    ->setAppId($appId)              // 必需：您的应用 ID
    ->setRsaPrivateKey($rsaKey)     // 必需：用于请求加密的 RSA 私钥
    ->setWaasPublicKey($waasKey)    // 必需：用于响应解密的 WaaS 公钥
    ->setSignPrivateKey($signKey)   // 可选：用于交易签名的私钥
    ->setDebug(true)                // 可选：启用调试模式
    ->build();
```

### 密钥类型说明

1. **RSA 私钥** (`rsaPrivateKey`)

   - 用于加密 API 请求
   - 格式：PEM 格式私钥

2. **WaaS 公钥** (`waasPublicKey`)

   - ChainUp 的公钥，用于解密响应
   - 由 ChainUp 提供

3. **签名私钥** (`signPrivateKey`)
   - 用于签名区块链交易（提现、Web3）
   - 可选，但执行交易操作时必需
   - 格式：PEM 格式私钥

## API 参考

### 钱包 API

管理 MPC 钱包和地址。

#### 创建钱包

创建新的子钱包。

```php
$walletApi = $mpcClient->getWalletApi();

$result = $walletApi->createWallet(array(
    'sub_wallet_name' => '我的钱包',  // 必需：钱包名称
    'app_show_status' => 1           // 必需：1=显示, 2=隐藏
));
```

**参数：**

- `sub_wallet_name` (string, 必需)：钱包名称
- `app_show_status` (int, 必需)：显示状态（1=显示, 2=隐藏）

**返回：** Result 对象，包含 `sub_wallet_id`

#### 创建钱包地址

为子钱包生成新地址。

```php
$result = $walletApi->createWalletAddress(array(
    'sub_wallet_id' => 123456,     // 必需：钱包 ID
    'chain_symbol' => 'ETH',       // 必需：区块链符号
    'symbol' => 'USDT',            // 必需：代币符号
    'address_count' => 1,          // 可选：地址数量（默认：1）
    'address_type' => 1            // 可选：地址类型（默认：1）
));
```

**返回：** Result 对象，包含生成的地址信息

#### 查询钱包地址

查询钱包的地址列表。

```php
$result = $walletApi->queryWalletAddress(array(
    'sub_wallet_id' => 123456,
    'symbol' => 'USDT',
    'max_id' => 0,        // 可选：分页参数
    'page_size' => 100    // 可选：每页数量
));
```

#### 获取钱包资产

获取钱包的资产余额。

```php
$result = $walletApi->getWalletAssets(array(
    'sub_wallet_id' => 123456,
    'symbol' => 'USDT'
));
```

**返回：** 余额信息，包括 `normal_balance`（可用余额）、`collecting_balance`（归集中余额）、`lock_balance`（锁定余额）

#### 修改钱包显示状态

更新钱包的显示状态。

```php
$result = $walletApi->changeWalletShowStatus(array(
    'sub_wallet_ids' => array(123, 456),  // 或逗号分隔的字符串
    'app_show_status' => 1                // 1=显示, 2=隐藏
));
```

#### 钱包地址信息

验证并获取地址信息。

```php
$result = $walletApi->walletAddressInfo(array(
    'address' => '0x1234...',
    'memo' => ''           // 可选：对于需要 memo 的链
));
```

---

### 充值 API

跟踪和管理充值记录。

#### 获取充值记录

根据 ID 查询充值记录。

```php
$depositApi = $mpcClient->getDepositApi();

$result = $depositApi->getDepositRecords(array(
    'deposit_001',
    'deposit_002'
));
```

**返回：** 充值记录数组

#### 同步充值记录

同步最近的充值记录。

```php
$result = $depositApi->syncDepositRecords(
    1,          // max_id：从此 ID 开始（0 表示全部）
    100         // page_size：每次请求记录数（可选）
);
```

**返回：** 分页的充值记录

---

### 提现 API

处理和跟踪提现操作。

#### 提现（带交易签名）

从钱包提现加密货币。

```php
$withdrawApi = $mpcClient->getWithdrawApi();

$result = $withdrawApi->withdraw(array(
    'request_id' => 'withdraw_' . time(),  // 必需：唯一 ID
    'sub_wallet_id' => 123456,             // 必需：源钱包
    'symbol' => 'USDT',                    // 必需：代币符号
    'amount' => '10.5',                    // 必需：提现金额
    'address_to' => '0x1234...',           // 必需：接收地址
    'memo' => '',                          // 可选：memo 链需要
    'remark' => '测试提现'                  // 可选：内部备注
));
```

**注意：** 需要在 MpcClient 中配置 `signPrivateKey`。

**返回：** 提现记录，包含交易 ID

#### 获取提现记录

根据请求 ID 查询提现记录。

```php
$result = $withdrawApi->getWithdrawRecords(array(
    'withdraw_001',
    'withdraw_002'
));
```

#### 同步提现记录

同步最近的提现记录。

```php
$result = $withdrawApi->syncWithdrawRecords(
    1,          // max_id：从此 ID 开始
    100         // page_size：可选
);
```

---

### Web3 API

与智能合约交互。

#### 创建 Web3 交易

执行智能合约交易。

```php
$web3Api = $mpcClient->getWeb3Api();

$result = $web3Api->createWeb3Trans(array(
    'request_id' => 'web3_' . time(),      // 必需：唯一 ID
    'sub_wallet_id' => 123456,             // 必需：钱包 ID
    'main_chain_symbol' => 'ETH',          // 必需：区块链
    'interactive_contract' => '0xabc...',  // 必需：合约地址
    'trans_type' => 1,                     // 必需：交易类型
    'amount' => '0',                       // 必需：ETH 金额（通常为 0）
    'input_data' => '0x...',               // 必需：编码的函数调用
    'gas_price' => '20',                   // 可选：gas 价格（Gwei）
    'gas_limit' => '21000',                // 可选：gas 限制
    'remark' => '合约调用'                  // 可选：备注
));
```

**交易类型：**

- `1`：合约调用（默认）
- `2`：代币转账
- `3`：NFT 转账

**返回：** 交易记录，包含 txid

#### 加速 Web3 交易

通过提高 gas 价格加速待处理的交易。

```php
$result = $web3Api->accelerationWeb3Trans(array(
    'request_id' => 'web3_001',    // 必需：原始请求 ID
    'gas_price' => '50'            // 必需：新的 gas 价格（Gwei）
));
```

#### 获取 Web3 记录

查询 Web3 交易记录。

```php
$result = $web3Api->getWeb3Records(array(
    'web3_001',
    'web3_002'
));
```

#### 同步 Web3 记录

同步最近的 Web3 交易记录。

```php
$result = $web3Api->syncWeb3Records(
    0,          // max_id
    100         // page_size：可选
);
```

---

### 自动归集 API

自动资金归集配置。

#### 配置子钱包自动归集

为特定钱包配置自动归集。

```php
$autoSweepApi = $mpcClient->getAutoSweepApi();

$result = $autoSweepApi->autoCollectSubWallets(array(
    'sub_wallet_ids' => array(123, 456),  // 必需：钱包 ID
    'symbol' => 'USDT',                   // 必需：代币符号
    'collect_threshold' => '100',         // 必需：触发归集的最小金额
    'chain_symbol' => 'ETH'               // 必需：区块链
));
```

#### 设置代币自动归集

启用/禁用代币的自动归集。

```php
$result = $autoSweepApi->setAutoCollectSymbol(array(
    'symbol' => 'USDT',
    'status' => 1          // 1=启用, 0=禁用
));
```

#### 同步自动归集记录

获取自动归集执行记录。

```php
$result = $autoSweepApi->syncAutoCollectRecords(
    0,          // max_id
    100         // page_size：可选
);
```

---

### TRON 资源 API

管理 TRON 能量和带宽。

#### 创建 TRON 资源委托

委托 TRON 资源（能量/带宽）。

```php
$tronApi = $mpcClient->getTronResourceApi();

$result = $tronApi->createTronDelegate(array(
    'request_id' => 'tron_' . time(),      // 必需：唯一 ID
    'address_from' => 'TXxx...',           // 必需：源地址
    'address_to' => 'TYyy...',             // 必需：接收地址
    'contract_address' => '',              // buy_type 为 0,2 时必需
    'resource_type' => 1,                  // 必需：0=带宽, 1=能量
    'buy_type' => 1,                       // 必需：0=委托, 1=租用, 2=合约租用
    'energy_num' => 32000,                 // 可选：能量数量
    'net_num' => 0                         // 可选：带宽数量
));
```

**购买类型：**

- `0`：委托（为他人质押）
- `1`：租用（购买临时资源）
- `2`：合约专用租用

#### 获取资源购买记录

查询 TRON 资源购买记录。

```php
$result = $tronApi->getBuyResourceRecords(array(
    'tron_001',
    'tron_002'
));
```

#### 同步资源购买记录

同步 TRON 资源购买历史。

```php
$result = $tronApi->syncBuyResourceRecords(
    0,          // max_id
    100         // page_size：可选
);
```

---

### 通知 API

处理加密的通知回调。

#### 解密通知

解密来自 ChainUp 回调的通知数据。

```php
$notifyApi = $mpcClient->getNotifyApi();

// 在您的通知端点中
$encryptedData = $_POST['data'];  // 来自回调

try {
    $decryptedData = $notifyApi->decryptNotification($encryptedData);

    // 处理通知
    $type = $decryptedData['type'];  // 例如：'deposit', 'withdraw' 等

    // 返回成功响应
    echo "success";
} catch (\Exception $e) {
    echo "解密通知失败: " . $e->getMessage();
}
```

**注意：** 始终在响应中返回 "success" 以确认收到。

---

### 工作空间 API

查询区块链和代币配置。

#### 获取支持的主链

获取支持的区块链网络列表。

```php
$workspaceApi = $mpcClient->getWorkspaceApi();

$result = $workspaceApi->getSupportMainChain();
```

**返回：** 支持的区块链数组及其配置

#### 获取币种详情

获取特定代币的详细信息。

```php
$result = $workspaceApi->getCoinDetails(array(
    'coin_net' => 'Ethereum',
    'symbol' => 'USDT'
));
```

**返回：** 代币配置，包括小数位数、地址、费用等

#### 获取最新区块高度

获取区块链的最新区块高度。

```php
$result = $workspaceApi->getLastBlockHeight(array(
    'symbol' => 'ETH'
));
```

**返回：** 当前区块高度

---

## 错误处理

所有 API 方法返回 `Result` 对象，包含以下方法：

### Result 对象方法

```php
// 检查请求是否成功
if ($result->isSuccess()) {
    // 成功：code == 0
    $data = $result->getData();
}

// 检查请求是否失败
if ($result->isError()) {
    // 错误：code != 0
    $errorMsg = $result->getMsg();
    $errorCode = $result->getCode();
}

// 获取响应代码
$code = $result->getCode();  // 0 = 成功，其他 = 错误

// 获取响应消息
$message = $result->getMsg();

// 获取响应数据
$data = $result->getData();  // 错误时为 null
```

### 常见错误代码

| 代码  | 含义                        |
| ----- | --------------------------- |
| 0     | 成功                        |
| -1    | 请求失败（网络/服务器错误） |
| -2    | JSON 解析失败               |
| -3    | 加密失败                    |
| -4    | 解密失败                    |
| 10001 | 参数错误                    |
| 10002 | 签名验证失败                |
| 10003 | 余额不足                    |
| 10004 | 地址未找到                  |

### 异常处理

始终将 API 调用包装在 try-catch 块中：

```php
try {
    $result = $walletApi->createWallet(array(
        'sub_wallet_name' => '我的钱包',
        'app_show_status' => 1
    ));

    if ($result->isSuccess()) {
        // 处理成功响应
        $data = $result->getData();
    } else {
        // 处理业务逻辑错误
        echo "错误: " . $result->getMsg();
    }
} catch (\Exception $e) {
    // 处理异常（验证、网络等）
    echo "异常: " . $e->getMessage();
}
```

## 代码示例

### 完整工作流示例

```php
<?php
require_once 'vendor/autoload.php';

use Chainup\Waas\Mpc\MpcClient;

// 初始化客户端
$mpcClient = MpcClient::newBuilder()
    ->setDomain('https://openapi.chainup.com')
    ->setAppId('your-app-id')
    ->setRsaPrivateKey($rsaPrivateKey)
    ->setWaasPublicKey($waasPublicKey)
    ->setSignPrivateKey($signPrivateKey)
    ->build();

// 1. 创建钱包
$walletApi = $mpcClient->getWalletApi();
$result = $walletApi->createWallet(array(
    'sub_wallet_name' => '交易钱包',
    'app_show_status' => 1
));

if (!$result->isSuccess()) {
    die("创建钱包失败: " . $result->getMsg());
}

$walletId = $result->getData()['sub_wallet_id'];
echo "钱包已创建: {$walletId}\n";

// 2. 创建地址
$result = $walletApi->createWalletAddress(array(
    'sub_wallet_id' => $walletId,
    'chain_symbol' => 'ETH',
    'symbol' => 'USDT'
));

if ($result->isSuccess()) {
    $address = $result->getData()['address'];
    echo "地址已创建: {$address}\n";
}

// 3. 查询余额
$result = $walletApi->getWalletAssets(array(
    'sub_wallet_id' => $walletId,
    'symbol' => 'USDT'
));

if ($result->isSuccess()) {
    $balance = $result->getData()['normal_balance'];
    echo "当前余额: {$balance} USDT\n";
}

// 4. 提现资金
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
    echo "提现已提交: {$txid}\n";
} else {
    echo "提现失败: " . $result->getMsg() . "\n";
}
```

### 分页示例

```php
// 使用分页同步所有充值记录
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

    // 更新 maxId 以获取下一页
    if (count($records) > 0) {
        $lastRecord = end($records);
        $maxId = $lastRecord['id'];
    }

} while (count($records) == 100);  // 如果页面已满则继续

echo "总记录数: " . count($allRecords) . "\n";
```

### Web3 合约调用示例

```php
// 示例：调用 ERC20 代币的 approve() 方法
$web3Api = $mpcClient->getWeb3Api();

// 编码函数调用: approve(address spender, uint256 amount)
// 函数签名: 0x095ea7b3
// Spender 地址（32 字节）: 0x000000000000000000000000742d35Cc6634C0532925a3b844Bc9e7595f0bEb
// 金额（32 字节）: 0x00000000000000000000000000000000000000000000d3c21bcecceda1000000 (1000 USDT)

$inputData = '0x095ea7b3' .
    '000000000000000000000000742d35Cc6634C0532925a3b844Bc9e7595f0bEb' .
    '00000000000000000000000000000000000000000000d3c21bcecceda1000000';

$result = $web3Api->createWeb3Trans(array(
    'request_id' => 'approve_' . time(),
    'sub_wallet_id' => 123456,
    'main_chain_symbol' => 'ETH',
    'interactive_contract' => '0xdAC17F958D2ee523a2206206994597C13D831ec7',  // USDT 合约
    'trans_type' => 1,
    'amount' => '0',
    'input_data' => $inputData,
    'gas_limit' => '100000'
));

if ($result->isSuccess()) {
    echo "授权交易已提交\n";
}
```

## 最佳实践

### 1. 安全性

- **切勿提交私钥**到版本控制系统
- 将密钥存储在环境变量或安全密钥管理系统中
- 开发和生产环境使用不同的密钥
- 定期轮换密钥

### 2. 错误处理

- 访问数据前始终检查 `isSuccess()`
- 同时处理 Result 错误和异常
- 记录错误以便调试

### 3. 请求 ID

- 为每个交易使用唯一的 `request_id`
- 包含时间戳以便追踪：`'withdraw_' . time()`
- 在数据库中存储请求 ID 以便对账

### 4. 分页

- 使用 `max_id` 进行高效分页
- 批量处理记录
- 优雅处理部分结果

### 5. 回调处理

- 验证并解密回调数据
- 始终返回 "success" 以确认
- 异步处理回调
- 实现幂等性（同一回调可能多次到达）

## 支持

- **文档**: https://custodydocs.chainup.com
- **API 参考**: https://custodydocs.chainup.com/zh_CN/latest/API-MPC/index.html
- **邮箱**: custody@chainup.com
- **GitHub**: https://github.com/ChainUp-Custody/php-sdk

## 许可证

MIT License - 详见 [LICENSE](../LICENSE) 文件。

---

由 [ChainUp Custody](https://custody.chainup.com) 用心打造 ❤️
