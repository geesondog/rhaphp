<?php

namespace Qiniu;

class Result implements \ArrayAccess
{
    public $error;
    public $data;
    public $response;
    public $debug;

    /**
     * @param Response $response
     * @param Request  $request
     */
    public function __construct($response, $request)
    {
        if ($response instanceof Response) {
            $this->data = $response->data;
            if ($response->error) {
                $this->error = $response->error;
            } else if (!empty($this->data['error'])) {
                $this->error = $this->data['error'];
            }
            $this->response = $response;
            $this->debug = array(
                'log' => $response->headers['x-log'],
                'id'  => isset($response->headers['x-reqid']) ? $response->headers['x-reqid'] : null
            );
        } else if ($request instanceof Request) {
            $this->data = false;
            $this->error = $request->error;
        }
    }

    /**
     * Is ok?
     *
     * @return bool
     */
    public function ok()
    {
        return !$this->error;
    }

    /**
     * Alias data
     *
     * @return bool
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     *       The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     * </p>
     * @param mixed $value  <p>
     *                      The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}