<?php

namespace Qiniu;

class Util
{
    /**
     * Encode uri
     *
     * @param string $str
     * @return mixed
     */
    public static function uriEncode($str)
    {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($str));
    }
}