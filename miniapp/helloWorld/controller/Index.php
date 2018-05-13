<?php

namespace miniapp\helloWorld\controller;


use app\common\controller\MiniappAddon;

class Index extends MiniappAddon
{

    public function index()
    {
        echo '<h1 style="text-align: center">你好中国</h1>';
        $this->fetch();

    }


}