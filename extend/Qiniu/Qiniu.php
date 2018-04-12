<?php

namespace Qiniu;


class Qiniu
{
    /**
     * Factory a client
     *
     * @param array $options
     * @return Client
     */
    public static function create(array $options)
    {
        return new Client($options);
    }
}