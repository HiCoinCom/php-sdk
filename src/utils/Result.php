<?php

namespace Chainup\Waas\Utils;

/**
 * API Response Result
 * Represents standardized API response structure
 * 
 * @package Chainup\Waas\Utils
 */
class Result
{
    /**
     * @var string|int Response code
     */
    private $code;

    /**
     * @var string Response message
     */
    private $msg;

    /**
     * @var mixed Response data
     */
    private $data;

    /**
     * Check if response indicates an error
     * 
     * @return bool True if error, false otherwise
     */
    public function isError()
    {
        return $this->code != 0 && $this->code !== '0';
    }

    /**
     * Check if response is successful
     * 
     * @return bool True if successful, false otherwise
     */
    public function isSuccess()
    {
        return !$this->isError();
    }

    /**
     * Get response code
     * 
     * @return string|int Response code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set response code
     * 
     * @param string|int $code Response code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get response message
     * 
     * @return string Response message
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Set response message
     * 
     * @param string $msg Response message
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * Get response data
     * 
     * @return mixed Response data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response data
     * 
     * @param mixed $data Response data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Create Result from array
     * 
     * @param array $array Array with code, msg, data keys
     * @return Result
     */
    public static function fromArray($array)
    {
        $result = new self();
        $result->setCode($array['code'] ?? -1);
        $result->setMsg($array['msg'] ?? '');
        $result->setData($array['data'] ?? null);
        return $result;
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data
        );
    }
}
