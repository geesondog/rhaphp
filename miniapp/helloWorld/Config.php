<?php
return array(
    'name' => 'helloWorld',
    'addon' => 'helloWorld',
    'desc' => 'helloWorld',
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
                ],
                [
                    'name' => '你好,上海',
                    'url' => 'helloWorld/Index/index2',
                    'icon' => ''
                ]
            ],
        ],
        [
            'name' => '你好,我的家',
            'url' => '',
            'icon' => '&#xe8ff;',
            'child' => [
                [
                    'name' => '你好,爸爸',
                    'url' => 'helloWorld/Index/index3',
                    'icon' => '&#xe670;'
                ],
                [
                    'name' => '你好,妈妈',
                    'url' => 'helloWorld/Index/index4',
                    'icon' => ''
                ]
            ],
        ],

        ['name' => '你好,未来的自己',
            'url' => 'helloWorld/Index/index5',
            'icon' => '&#xe654;'
        ],
        ['name' => '你好,二哈',
            'url' => 'helloWorld/Index/index6',
            'icon' => '&#xe632;'
        ],
        ['name' => '你好,程序嫒',
            'url' => 'helloWorld/Index/index7',
            'icon' => '&#xe88b;'
        ],
        ['name' => '你好,现在的自己',
            'url' => 'helloWorld/Index/index8',
            'icon' => '&#xe878;'
        ],
    ],
    'config' => array(
        [
            'name' => 'name',
            'title' => '名称',
            'type' => 'text',
            'value' => '',
            'placeholder' => '请输入关名称',
            'tip' => '这里是提示，比喻：名称请填写真实性名',
        ],
        [
            'name' => 'select',
            'title' => '四大城市',
            'type' => 'select',
            'value' => [
                0 => [
                    'title' => '北京',
                    'value' => '1',
                    'selected' => '1'
                ],
                1 => [
                    'title' => '上海',
                    'value' => '2',
                    'selected' => '0'
                ],
                2 => [
                    'title' => '广州',
                    'value' => '3',
                    'selected' => '0'
                ]
                ,
                3 => [
                    'title' => '深圳',
                    'value' => '4',
                    'selected' => '0'
                ]
            ],
            'placeholder' => '',
            'tip' => '你的城市',
        ],
        [
            'name' => 'likes',
            'title' => '喜欢谁？',
            'type' => 'checkbox',
            'value' => [
                0 => [
                    'name' => 'ldh',
                    'title' => '刘德华',
                    'value' => '0',
                    'checked' => '0'
                ],
                1 => [
                    'name' => 'fbb',
                    'title' => '冰冰',
                    'value' => '1',
                    'checked' => '1'
                ],
                2 => [
                    'name' => 'fj',
                    'title' => '凤姐',
                    'value' => '1',
                    'checked' => '1'
                ]
            ],
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'time',
            'title' => '时间',
            'type' => 'date',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'sex',
            'title' => '性别',
            'type' => 'radio',
            'value' => [
                0 => [
                    'title' => '男',
                    'value' => '0',
                    'checked' => '0'
                ],
                1 => [
                    'title' => '女',
                    'value' => '1',
                    'checked' => '1'
                ]
            ],
            'placeholder' => '',
            'tip' => '如果你男与女都不是，系统认为你是条汉子。',
        ],
        [
            'name' => 'content',
            'title' => '描述',
            'type' => 'textarea',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'myFile',
            'title' => '上传文件',
            'type' => 'file',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'myImage',
            'title' => '上传图片',
            'type' => 'image',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'myImages',
            'title' => '多图上传',
            'type' => 'images',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],

    )
);