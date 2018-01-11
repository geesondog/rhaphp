<?php

namespace Qiniu;

class Response
{
    public $code;
    public $protocol;
    public $message;
    public $body;
    public $error;
    public $headers = array();
    public $data = array();

    /**
     * @param string      $response
     * @param bool|string $error
     */
    public function __construct($response, $error = false)
    {
        if (is_string($response) && ($parsed = $this->parse($response))) {
            foreach ($parsed as $key => $value) {
                $this->{$key} = $value;
            }
        }
        $this->error = $error;
    }

    /**
     * Parse response
     *
     * @param $response
     * @return array
     */
    public static function parse($response)
    {
        $body_pos = strpos($response, "\r\n\r\n");
        $header_string = substr($response, 0, $body_pos);
        if (strpos($header_string, 'HTTP/1.1 100 Continue') !== false) {
            $head_pos = $body_pos + 4;
            $body_pos = strpos($response, "\r\n\r\n", $head_pos);
            $header_string = substr($response, $head_pos, $body_pos - $head_pos);
        }
        $header_lines = explode("\r\n", $header_string);

        $headers = array();
        $code = false;
        $body = false;
        $protocol = null;
        $message = null;
        $data = array();

        foreach ($header_lines as $index => $line) {
            if ($index === 0) {
                preg_match("/^(HTTP\/\d\.\d) (\d{3}) (.*?)$/", $line, $match);
                list(, $protocol, $code, $message) = $match;
                $code = (int)$code;
                continue;
            }
            list($key, $value) = explode(":", $line);
            $headers[strtolower(trim($key))] = trim($value);
        }

        if (is_numeric($code)) {
            $body_string = substr($response, $body_pos + 4);
            if (!empty($headers['transfer-encoding']) && $headers['transfer-encoding'] == 'chunked') {
                $body = self::decodeChunk($body_string);
            } else {
                $body = (string)$body_string;
            }
            $result['header'] = $headers;
        }

        // 自动解析数据
        if (strpos($headers['content-type'], 'json')) {
            $data = json_decode($body, true);
        }

        return $code ? array(
            'code'     => $code,
            'body'     => $body,
            'headers'  => $headers,
            'message'  => $message,
            'protocol' => $protocol,
            'data'     => $data
        ) : false;
    }

    /**
     * Get header
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function header($key, $default = null)
    {
        $key = strtolower($key);
        return !isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }

    /**
     * Is error?
     *
     * @return bool
     */
    public function error()
    {
        return !!$this->error;
    }


    /**
     * Is response cachable?
     *
     * @return bool
     */
    public function cachable()
    {
        return $this->code >= 200 && $this->code < 300 || $this->code == 304;
    }

    /**
     * Is empty?
     *
     * @return bool
     */
    public function bare()
    {
        return in_array($this->code, array(201, 204, 304));
    }

    /**
     * Is 200 ok?
     *
     * @return bool
     */
    public function ok()
    {
        return $this->code === 200;
    }

    /**
     * Is successful?
     *
     * @return bool
     */
    public function success()
    {
        return $this->code >= 200 && $this->code < 300;
    }

    /**
     * Is redirect?
     *
     * @return bool
     */
    public function redirect()
    {
        return in_array($this->code, array(301, 302, 303, 307));
    }

    /**
     * Is forbidden?
     *
     * @return bool
     */
    public function forbidden()
    {
        return $this->code === 403;
    }

    /**
     * Is found?
     *
     * @return bool
     */
    public function notFound()
    {
        return $this->code === 404;
    }

    /**
     * Is client error?
     *
     * @return bool
     */
    public function clientError()
    {
        return $this->code >= 400 && $this->code < 500;
    }

    /**
     * Is server error?
     *
     * @return bool
     */
    public function serverError()
    {
        return $this->code >= 500 && $this->code < 600;
    }

    /**
     * Decode chunk
     *
     * @param $str
     * @return string
     */
    protected static function decodeChunk($str)
    {
        $body = '';
        while ($str) {
            $chunk_pos = strpos($str, "\r\n") + 2;
            $chunk_size = hexdec(substr($str, 0, $chunk_pos));
            $str = substr($str, $chunk_pos);
            $body .= substr($str, 0, $chunk_size);
        }
        return $body;
    }
}