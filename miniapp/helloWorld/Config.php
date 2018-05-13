<?php
return array(
    'name' => '你好世界',
    'addon' => 'helloWorld',
    'desc' => '我是你好世界的小程序',
    'version' => '1.0',
    'author' => 'Geeson',
    'logo' => 'logo.jpg',
    'install_sql' => '',
    'upgrade_sql' => '',
    'menu' => [//后台菜单列表
        [
            'name' => '你好,世界',
            'url' => '',
            'icon' => '&#xe893;',
            'child' => [
                [
                    'name' => '你好,中国',
                    'url' => 'helloWorld/Index/index',
                    'icon' => ''
                ]
            ]
        ]
    ]
);