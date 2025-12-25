<?php

namespace Chainup\Waas\Utils;

/**
 * Logger Interface
 * Defines logging methods for SDK operations
 * Allows users to implement custom logging (file, syslog, third-party services, etc.)
 * 
 * @package Chainup\Waas\Utils
 */
interface LoggerInterface
{
    /**
     * Log debug information
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function debug($message, array $context = array());

    /**
     * Log informational message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function info($message, array $context = array());

    /**
     * Log warning message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function warning($message, array $context = array());

    /**
     * Log error message
     * 
     * @param string $message Log message
     * @param array $context Additional context data
     * @return void
     */
    public function error($message, array $context = array());
}
