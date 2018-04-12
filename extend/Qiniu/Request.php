<?php

namespace Qiniu;

class Request
{
    /**
     * @var array Default options
     */
    public $options = array(
        'method'   => 'GET',
        'url'      => null,
        'headers'  => array(),
        'body'     => '',
        'form'     => array(),
        'files'    => array(),
        'encoding' => 'utf-8',
        'timeout'  => 60
    );

    /**
     * @var Response
     */
    public $response;

    /**
     * @var string
     */
    public $error;

    /**
     * Create request
     *
     * @param array $options
     * @return Request
     */
    public static function create($options = null)
    {
        return new self($options);
    }

    /**
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $options = array('url' => $options);
        }
        $this->options = (array)$options + $this->options;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Request
     */
    public function url($url = null)
    {
        if ($url) {
            $this->options['url'] = $url;
            return $this;
        } else {
            return $this->options['url'];
        }
    }

    /**
     * Set headers
     *
     * @param string|array $key
     * @param string       $value
     * @return Request
     */
    public function header($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->header($k, $v);
            }
        } else {
            $this->options['headers'][strtolower($key)] = $value;
        }
        return $this;
    }

    /**
     * @param string|array $key
     * @param string       $value
     * @return Request
     */
    public function form($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->form($k, $v);
            }
        } else {
            $this->options['form'][$key] = $value;
        }
        return $this;
    }

    /**
     * Alias form
     *
     * @param string|array $key
     * @param string       $value
     * @return Request
     */
    public function data($key, $value = null)
    {
        return $this->form($key, $value);
    }

    /**
     * Attach file
     *
     * @param string $file
     * @param string $filename
     * @param string $name
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return Request
     */
    public function file($file, $filename = null, $name = 'file')
    {
        if (is_array($file)) {
            foreach ($file as $f) {
                $this->file($f[0], isset($f[1]) ? $f[1] : null, isset($f[2]) ? $f[2] : 'file');
            }
        } else {
            if ($filename === null) {
                if (!is_file($file)) throw new \InvalidArgumentException("Can not upload non-exists file: $file");

                if (($content = file_get_contents($file)) === false) throw new \Exception("Can not read file: $file");
            } else {
                $content = $file;
            }
            $this->options['files'][$name] = array(basename($filename), $content);
        }
        return $this;
    }

    /**
     * Get request
     *
     * @param string|array $url
     * @param array        $data
     * @return Response
     */
    public function get($url = null, $data = null)
    {
        return $this->send($url, $data, 'GET');
    }

    /**
     * Post request
     *
     * @param string|array $url
     * @param array        $data
     * @return Response
     */
    public function post($url = null, $data = null)
    {
        return $this->send($url, $data, 'POST');
    }

    /**
     * Upload files
     *
     * @param string|array $file
     * @param array        $options
     * @param string       $name
     * @return Response|bool
     */
    public function upload($file, $name = 'file', $options = null)
    {
        if (is_array($name)) {
            $name = $options ? $options : 'file';
            $options = $name;
        }
        $this->file($file, $name);
        return $this->send($options, null, 'POST');
    }

    /**
     * Put request
     *
     * @param string|array $url
     * @param array        $data
     * @return Response
     */
    public function put($url = null, $data = null)
    {
        return $this->send($url, $data, 'PUT');
    }

    /**
     * Delete request
     *
     * @param string|array $url
     * @param null         $data
     * @return Response
     */
    public function delete($url = null, $data = null)
    {
        return $this->send($url, $data, 'DELETE');
    }

    /**
     * Send request
     *
     * @param string|array $url
     * @param array        $data
     * @param string       $method
     * @return Response|bool
     */
    public function send($url = null, $data = null, $method = null)
    {
        $options = array();
        if ($url) $options = is_array($url) ? $url : array('url' => $url);
        if ($data && is_array($data)) $options['form'] = $data;
        if ($method) $options['method'] = $method;

        $this->options = $options + $this->options;

        if ($this->parseOptions() === false) {
            return false;
        }

        $options = $this->options;
        $url = parse_url($options['url']);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $options['url']);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        if ($url['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        if ($options['timeout'] > 0) curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
        if ($options['body']) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }
        if ($options['headers']) {
            $headers = array();
            foreach ($options['headers'] as $key => $value) {
                $headers[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $error = false;
        if (($response = curl_exec($ch)) === false) {
            $error = curl_error($ch);
        }
        return $this->response = new Response($response, $error);
    }

    /**
     * Parse the options and data
     */
    protected function parseOptions()
    {
        $options = & $this->options;
        if (!$options['url'] || !$options['method']) {
            $this->error = "Options url, method is required";
            return false;
        }

        if ($options['method'] !== 'GET') {
            if ($options['files']
                || isset($options['headers']['content-type'])
                && strpos($options['headers']['content-type'], 'multipart/form-data') === 0
            ) {
                list($boundary, $body) = self::buildMultiForm($options['form'], $options['files']);
                $options['headers']['content-type'] = 'multipart/form-data; boundary=' . $boundary;
                $options['body'] = $body;
            } elseif ($options['form']) {
                $options['headers']['content-type'] = 'application/x-www-form-urlencoded';
                $options['body'] = http_build_query($options['form']);
            }
        } else if ($options['form']) {
            $options['url'] = $options['url'] . (strpos($options['url'], '?') ? '&' : '?') . http_build_query($options['form']);
        }

        if (!empty($options['headers']['content-type']) && $options['encoding']) {
            $options['headers']['content-type'] .= '; charset=' . $options['encoding'];
        }
        return true;
    }

    /**
     * Parse multi form
     *
     * @param array $form
     * @param       $files
     * @throws \InvalidArgumentException
     * @return array
     */
    protected static function buildMultiForm($form, $files)
    {
        $data = array();
        $boundary = md5(uniqid());

        foreach ($form as $name => $val) {
            $data[] = '--' . $boundary;
            $data[] = "Content-Disposition: form-data; name=\"$name\"";
            $data[] = '';
            $data[] = $val;
        }

        foreach ($files as $name => $file) {
            $data[] = '--' . $boundary;
            $filename = str_replace(array("\\", "\""), array("\\\\", "\\\""), $file[0]);
            $data[] = "Content-Disposition: form-data; name=\"$name\"; filename=\"$filename\"";
            $data[] = 'Content-Type: application/octet-stream';
            $data[] = '';
            $data[] = $file[1];
        }

        $data[] = '--' . $boundary . '--';
        $data[] = '';

        $body = implode("\r\n", $data);
        return array($boundary, $body);
    }
}