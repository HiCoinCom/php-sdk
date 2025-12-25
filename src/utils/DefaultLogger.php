<?php

namespace Chainup\Waas\Utils;

/**
 * Default Logger Implementation
 * Simple console logger that outputs to STDOUT/STDERR
 * Users can replace this with their own logger implementation
 * 
 * @package Chainup\Waas\Utils
 */
class DefaultLogger implements LoggerInterface
{
    /**
     * @var bool Enable/disable logging
     */
    private $enabled;

    /**
     * Constructor
     * 
     * @param bool $enabled Enable/disable logging
     */
    public function __construct($enabled = false)
    {
        $this->enabled = $enabled;
    }

    /**
     * Log debug information
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function debug($message, array $context = array())
    {
        if ($this->enabled) {
            $this->log('DEBUG', $message, $context);
        }
    }

    /**
     * Log informational message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function info($message, array $context = array())
    {
        if ($this->enabled) {
            $this->log('INFO', $message, $context);
        }
    }

    /**
     * Log warning message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function warning($message, array $context = array())
    {
        if ($this->enabled) {
            $this->log('WARNING', $message, $context);
        }
    }

    /**
     * Log error message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function error($message, array $context = array())
    {
        if ($this->enabled) {
            $this->log('ERROR', $message, $context, STDERR);
        }
    }

    /**
     * Internal logging method
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @param resource $output Output stream (STDOUT or STDERR)
     * @return void
     */
    private function log($level, $message, array $context = array(), $output = STDOUT)
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextStr}\n";
        
        fwrite($output, $logMessage);
    }
}
