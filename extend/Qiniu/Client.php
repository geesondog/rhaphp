<?php

namespace Qiniu;


class Client
{
    /**
     * @var array Configs
     */
    protected $options = array(
        'access_key' => null,
        'secret_key' => null,
        'bucket'     => null,
        'domain'     => null,
        'timeout'    => '3600',
        'upload_url' => 'http://up.qiniu.com',
        'rs_url'     => 'http://rs.qbox.me',
        'rsf_url'    => 'http://rsf.qbox.me',
        'base_url'   => null
    );

    /**
     * @var array Image url options
     */
    protected $image_options = array(
        'imageView' => array(
            'mode'   => array('type' => 'no-key'),
            'w'      => array('alias' => 'width'),
            'h'      => array('alias' => 'height'),
            'q'      => array('alias' => 'quality'),
            'format' => array('alias' => 'f')
        ),
        'imageMogr' => array(
            '_path'       => 'v2',
            'auto-orient' => array('type' => 'no-value', 'default' => true),
            'thumbnail'   => array('alias' => array('thumb', 't')),
            'gravity'     => array('alias' => 'g'),
            'crop'        => array('alias' => 'c'),
            'quality'     => array('alias' => 'q'),
            'rotate'      => array('alias' => 'r'),
            'format'      => array('alias' => 'f')
        )
    );

    protected $batch = false;
    protected $batch_operators = array();

    /**
     * @var Mac
     */
    protected $mac;

    public function __construct(array $options)
    {
        $this->options = $options + $this->options;

        if (!$this->options['access_key'] || !$this->options['secret_key'] || !$this->options['bucket']) {
            throw new \InvalidArgumentException("Options access_key, secret_key and bucket required");
        }

        $this->mac = new Mac($this->options['access_key'], $this->options['secret_key']);

        if (!$this->options['base_url']) $this->options['base_url'] = $this->options['domain'] ? $this->options['domain'] : ('http://' . $this->options['bucket'] . '.qiniudn.com');
    }

    /**
     * Upload file
     *
     * @param string $path
     * @param string $key
     * @throws \InvalidArgumentException
     * @return bool|Result
     */
    public function uploadFile($path, $key)
    {
        if (!is_file($path)) throw new \InvalidArgumentException("Can not upload non-exists file: $path");
        return $this->uploadRequest(file_get_contents($path), $key);
    }

    /**
     * Upload string
     *
     * @param string $string
     * @param string $key
     * @return bool|Result
     */
    public function upload($string, $key)
    {
        return $this->uploadRequest($string, $key);
    }

    /**
     * Get file stats
     *
     * @param string $key
     * @return bool|Result|Client
     */
    public function stat($key)
    {
        $uri = '/stat/' . Util::uriEncode("{$this->options['bucket']}:$key");

        if ($this->batch) {
            $this->batch_operators[] = array($uri, $key);
            return $this;
        } else {
            return $this->operateRequest($uri, $key);
        }
    }

    /**
     * Move file
     *
     * @param string $key
     * @param string $new_key
     * @return bool|Result|Client
     */
    public function move($key, $new_key)
    {
        $uri = '/move/' . Util::uriEncode("{$this->options['bucket']}:$key") . '/' . Util::uriEncode("{$this->options['bucket']}:$new_key");

        if ($this->batch) {
            $this->batch_operators[] = array($uri, $key);
            return $this;
        } else {
            return $this->operateRequest($uri, $new_key);
        }
    }

    /**
     * Copy file
     *
     * @param string $key
     * @param string $new_key
     * @return bool|Result|Client
     */
    public function copy($key, $new_key)
    {
        $uri = '/copy/' . Util::uriEncode("{$this->options['bucket']}:$key") . '/' . Util::uriEncode("{$this->options['bucket']}:$new_key");

        if ($this->batch) {
            $this->batch_operators[] = array($uri, $key);
            return $this;
        } else {
            return $this->operateRequest($uri, $new_key);
        }
    }

    /**
     * Delete file
     *
     * @param string $key
     * @return bool|Result|Client
     */
    public function delete($key)
    {
        $uri = '/delete/' . Util::uriEncode("{$this->options['bucket']}:$key");

        if ($this->batch) {
            $this->batch_operators[] = array($uri, $key);
            return $this;
        } else {
            return $this->operateRequest($uri, $key);
        }
    }

    /**
     *
     * @param $prefix
     * @return bool|Result
     */
    public function ls($prefix = '')
    {
        $query = array('bucket' => $this->options['bucket']) + (is_array($prefix) ? $prefix : array('prefix' => $prefix));
        $uri = '/list?' . http_build_query($query);
        return $this->operateRequest($uri, null, $this->options['rsf_url']);
    }

    /**
     * Start batch mode
     *
     * @return $this
     */
    public function batch()
    {
        $this->batch = true;
        return $this;
    }

    /**
     * Exec the batch
     *
     * @return Result
     * @throws \RuntimeException
     */
    public function exec()
    {
        if (!$this->batch) throw new \RuntimeException("Not in batch mode!");

        $ops = array();
        foreach ($this->batch_operators as $operator) {
            $ops[] = 'op=' . $operator[0];
        }

        $url = $this->options['rs_url'] . '/batch';

        $request = Request::create(array(
            'url'    => $url,
            'body'   => join('&', $ops),
            'method' => 'POST'
        ));
        $token = $this->mac->signRequest('/batch', join('&', $ops));
        $request->header('authorization', 'QBox ' . $token);

        $result = new Result($response = $request->send(), $request);
        if (!$result->ok()) {
            return $result;
        }

        if (is_array($result->data)) {
            foreach ($result->data as $i => $item) {
                $r = new Result($response, $request);
                $r->data = isset($item['data']) ? $item['data'] : array();
                if ($item['code'] !== 200) {
                    $r->error = $item['error'];
                }
                $r->response = null;
                $r->data['url'] = $this->options['base_url'] . '/' . $this->batch_operators[$i][1];
                $result->data[$i] = $r;
            }
        }

        // Reset batch mode
        $this->batch = false;
        $this->batch_operators = array();

        return $result;
    }

    /**
     * Get image info
     *
     * @param string $key
     * @return Result
     */
    public function imageInfo($key)
    {
        return $this->imageRequest($key, 'imageInfo');
    }

    /**
     * Get exif info
     *
     * @param string $key
     * @return Result
     */
    public function exif($key)
    {
        return $this->imageRequest($key, 'exif');
    }

    /**
     * Generate imageView url
     *
     * @param string $key
     * @param array  $options
     * @return Result
     */
    public function imageView($key, array $options)
    {
        return $this->imageUrl($key, 'imageView', $options);
    }

    /**
     * Generate imageMogr url
     *
     * @param string $key
     * @param array  $options
     * @return string
     */
    public function imageMogr($key, array $options)
    {
        return $this->imageUrl($key, 'imageMogr', $options);
    }

    /**
     * Generate image url
     *
     * @param string $key
     * @param string $type
     * @param array  $options
     * @return string
     */
    protected function imageUrl($key, $type, array $options)
    {
        $paths = array($type);
        foreach ($this->image_options[$type] as $field => $opt) {
            if ($field == '_path') {
                $paths[] = $opt;
                continue;
            }

            $look = array($field);
            $value = null;
            $type = isset($opt['type']) ? $opt['type'] : '';

            if (isset($opt['alias'])) {
                $look = array_merge($look, (array)$opt['alias']);
            }

            foreach ($look as $f) {
                if (isset($options[$f])) {
                    $value = $options[$f];
                    break;
                }
            }

            if (!$value && isset($opt['default'])) $value = $opt['default'];

            if ($value !== null) {
                switch ($type) {
                    case 'no-key':
                        $paths[] = $value;
                        break;
                    case 'no-value':
                        if ($value) $paths[] = $field;
                        break;
                    default:
                        $paths[] = $field;
                        $paths[] = $value;
                }
            }
        }
        return $this->options['base_url'] . '/' . $key . '?' . join('/', $paths);
    }

    /**
     * Image info request
     *
     * @param string $key
     * @param string $type
     * @return Result
     */
    protected function imageRequest($key, $type)
    {
        $url = $this->options['base_url'] . '/' . $key . '?' . $type;
        $request = Request::create($url);
        return new Result($request->get(), $request);
    }

    /**
     * Operate request
     *
     * @param string $uri
     * @param string $key
     * @param string $host
     * @return bool|Result
     */
    protected function operateRequest($uri, $key, $host = null)
    {
        $url = ($host ? $host : $this->options['rs_url']) . $uri;
        $request = Request::create(array(
            'url'          => $url,
            'method'       => 'POST',
            'content-type' => 'application/x-www-form-urlencoded'
        ));
        $token = $this->mac->signRequest($uri);
        $request->header('authorization', 'QBox ' . $token);
        $result = new Result($request->send(), $request);
        if ($result->ok() && $key) {
            $result->data['url'] = $this->options['base_url'] . '/' . $key;
        }
        return $result;
    }

    /**
     * Upload request
     *
     * @param string       $body
     * @param string|array $key
     * @param array        $policy
     * @return bool|Result
     */
    public function uploadRequest($body, $key, $policy = null)
    {
        $options = (is_string($key) ? array('key' => $key) : array()) + array(
                'filename' => null
            );

        $policy = (array)$policy + array(
                'scope'        => $this->options['bucket'],
                'deadline'     => time() + 3600,
                'callbackUrl'  => null,
                'callbackBody' => null,
                'returnUrl'    => null,
                'returnBody'   => null,
                'asyncOps'     => null,
                'endUser'      => null
            );

        foreach ($policy as $k => $v) {
            if ($v === null) unset($policy[$k]);
        }

        $token = $this->mac->signWithData(json_encode($policy));
        $request = Request::create(array(
            'url'     => $this->options['upload_url'],
            'method'  => 'POST',
            'headers' => array(
                'content-type' => 'multipart/form-data'
            ),
            'form'    => array(
                'token' => $token,
                'key'   => $options['key']
            )
        ))->file($body, basename($options['filename'] ? $options['filename'] : $options['key']));
        $result = new Result($request->send(), $request);
        if ($result->ok()) {
            $result->data['url'] = $this->options['base_url'] . '/' . $result->data['key'];
        }
        return $result;
    }
}