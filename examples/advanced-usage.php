<?php
/**
 * ChainUp Custody PHP SDK - Advanced Usage Examples
 * 
 * Demonstrates custom Logger and CryptoProvider implementations
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Chainup\Waas\Custody\WaasClient;
use Chainup\Waas\Utils\LoggerInterface;
use Chainup\Waas\Utils\CryptoProviderInterface;

// ============================================================
// Example 1: Custom File Logger Implementation
// ============================================================

/**
 * Custom File Logger
 * Logs all SDK operations to a file
 */
class FileLogger implements LoggerInterface
{
    private $logFile;

    public function __construct($logFile = '/tmp/waas-sdk.log')
    {
        $this->logFile = $logFile;
    }

    public function debug($message, array $context = array())
    {
        $this->writeLog('DEBUG', $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->writeLog('INFO', $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->writeLog('WARNING', $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->writeLog('ERROR', $message, $context);
    }

    private function writeLog($level, $message, array $context = array())
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logLine = "[{$timestamp}] [{$level}] {$message} {$contextStr}\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND);
    }
}

// ============================================================
// Example 2: Custom Database Logger Implementation
// ============================================================

/**
 * Custom Database Logger
 * Logs all SDK operations to a database (example using PDO)
 */
class DatabaseLogger implements LoggerInterface
{
    private $pdo;

    public function __construct($dsn, $username, $password)
    {
        // Example: 'mysql:host=localhost;dbname=mydb'
        $this->pdo = new PDO($dsn, $username, $password);
    }

    public function debug($message, array $context = array())
    {
        $this->insertLog('DEBUG', $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->insertLog('INFO', $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->insertLog('WARNING', $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->insertLog('ERROR', $message, $context);
    }

    private function insertLog($level, $message, array $context = array())
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO sdk_logs (level, message, context, created_at) VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$level, $message, json_encode($context)]);
    }
}

// ============================================================
// Example 3: Custom Crypto Provider (e.g., HSM Integration)
// ============================================================

/**
 * Custom HSM Crypto Provider
 * Uses Hardware Security Module for encryption/decryption
 * This is a mock example - replace with actual HSM integration
 */
class HsmCryptoProvider implements CryptoProviderInterface
{
    private $hsmClient;
    private $keyId;

    public function __construct($hsmEndpoint, $keyId)
    {
        // Initialize HSM client (this is pseudo-code)
        // $this->hsmClient = new HsmClient($hsmEndpoint);
        $this->keyId = $keyId;
    }

    public function encryptWithPrivateKey($data)
    {
        // Use HSM to encrypt data
        // return $this->hsmClient->sign($this->keyId, $data);
        
        // Fallback to regular RSA for this example
        throw new \Exception('HSM integration not implemented in this example');
    }

    public function decryptWithPublicKey($encryptedData)
    {
        // Use HSM to decrypt data
        // return $this->hsmClient->verify($this->keyId, $encryptedData);
        
        // Fallback to regular RSA for this example
        throw new \Exception('HSM integration not implemented in this example');
    }
}

// ============================================================
// Example 4: Using Custom Logger with SDK
// ============================================================

echo "=== Example 1: Using Custom File Logger ===\n\n";

try {
    // Create custom file logger
    $fileLogger = new FileLogger('/tmp/chainup-waas.log');

    // Build client with custom logger
    $client = WaasClient::newBuilder()
        ->setHost('https://openapi.chainup.com')
        ->setAppId('your-app-id')
        ->setPrivateKey('your-private-key')
        ->setPublicKey('chainup-public-key')
        ->setLogger($fileLogger)  // Use custom logger
        ->build();

    // All SDK operations will now be logged to file
    $userApi = $client->getUserApi();
    // $result = $userApi->registerEmailUser('user@example.com');

    echo "Logs are written to: /tmp/chainup-waas.log\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================
// Example 5: Using Both Custom Logger and Debug Mode
// ============================================================

echo "\n=== Example 2: Custom Logger + Debug Mode ===\n\n";

try {
    $fileLogger = new FileLogger('/tmp/chainup-debug.log');

    $client = WaasClient::newBuilder()
        ->setHost('https://openapi.chainup.com')
        ->setAppId('your-app-id')
        ->setPrivateKey('your-private-key')
        ->setPublicKey('chainup-public-key')
        ->setLogger($fileLogger)
        ->setDebug(true)  // Enable debug mode
        ->build();

    echo "Logger will capture all debug information\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// ============================================================
// Example 6: Multiple Loggers (Composite Pattern)
// ============================================================

/**
 * Composite Logger
 * Logs to multiple destinations simultaneously
 */
class CompositeLogger implements LoggerInterface
{
    private $loggers = array();

    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    public function debug($message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->debug($message, $context);
        }
    }

    public function info($message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->info($message, $context);
        }
    }

    public function warning($message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->warning($message, $context);
        }
    }

    public function error($message, array $context = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->error($message, $context);
        }
    }
}

echo "\n=== Example 3: Composite Logger (Multiple Destinations) ===\n\n";

try {
    // Create composite logger
    $compositeLogger = new CompositeLogger();
    $compositeLogger->addLogger(new FileLogger('/tmp/waas-main.log'));
    // $compositeLogger->addLogger(new DatabaseLogger(...));  // Uncomment when DB is configured
    
    $client = WaasClient::newBuilder()
        ->setHost('https://openapi.chainup.com')
        ->setAppId('your-app-id')
        ->setPrivateKey('your-private-key')
        ->setPublicKey('chainup-public-key')
        ->setLogger($compositeLogger)
        ->build();

    echo "Logs will be written to multiple destinations\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Advanced Examples Complete ===\n";
echo "\nKey Takeaways:\n";
echo "1. Implement LoggerInterface for custom logging (file, DB, syslog, etc.)\n";
echo "2. Implement CryptoProviderInterface for custom encryption (HSM, KMS, etc.)\n";
echo "3. Use setLogger() and setCryptoProvider() in builder\n";
echo "4. No more hardcoded echo statements - full control over logging\n";
echo "5. Flexible encryption - can integrate with any security infrastructure\n";
