<?php

namespace Chainup\Waas\Utils;

/**
 * Base64 URL-Safe encoding and decoding utility
 * Provides URL-safe Base64 encoding/decoding as per RFC 4648
 * 
 * @package Chainup\Waas\Utils
 */
class Base64UrlSafe
{
    /**
     * URL-safe base64 encoding
     * Converts standard base64 to URL-safe format:
     * '+' -> '-'
     * '/' -> '_'
     * '=' -> '' (removed)
     * 
     * @param string $data Raw data to encode
     * @return string URL-safe base64 encoded string
     */
    public static function encode($data)
    {
        $base64 = base64_encode($data);
        // Convert to URL-safe format
        $urlSafe = str_replace(array('+', '/', '='), array('-', '_', ''), $base64);
        return $urlSafe;
    }

    /**
     * URL-safe base64 decoding
     * Converts URL-safe base64 back to standard format and decodes:
     * '-' -> '+'
     * '_' -> '/'
     * Adds padding '=' as needed
     * 
     * @param string $data URL-safe base64 encoded string
     * @return string Decoded raw data
     */
    public static function decode($data)
    {
        // Convert from URL-safe format back to standard base64
        $base64 = str_replace(array('-', '_'), array('+', '/'), $data);
        
        // Add padding if necessary
        $remainder = strlen($base64) % 4;
        if ($remainder) {
            $base64 .= str_repeat('=', 4 - $remainder);
        }
        
        return base64_decode($base64);
    }
}
