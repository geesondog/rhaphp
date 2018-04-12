<?php
return array(
    'name' => '投票',
    'addon' => 'touPiao',
    'desc' => '一款多功能微信在线投票应用，活动推广、营销利器。',
    'version' => '1.0',
    'author' => 'Geeson',
    'logo' => 'logo.jpg',
    'entry_url' => 'touPiao/vote/index',
    'install_sql' => 'install.sql',
    'upgrade_sql' => '',
    'menu' => [
        [
            'name' => '投票管理',
            'url' => 'touPiao/Admin/index',
            'icon' => ''
        ],
    ],
    'config' => array(
        [
            'name' => 'login_type',
            'title' => '登录方式',
            'type' => 'radio',
            'value' => [
                0 => [
                    'title' => '授权登录',
                    'value' => '1',
                    'checked' => '1'
                ],
                1 => [
                    'title' => '回复|菜单',
                    'value' => '2',
                    'checked' => '0'
                ]
            ],
            'placeholder' => '',
            'tip' => '认证公众号请选择授权登录，没有认证的请选择回复关键词。',
        ],
        [
            'name' => 'vote_title',
            'title' => '标题',
            'type' => 'text',
            'value' => '',
            'placeholder' => '',
            'tip' => '投票活动标题',
        ],
        [
            'name' => 'vote_logo',
            'title' => '图标',
            'type' => 'image',
            'value' => '',
            'placeholder' => '',
            'tip' => '分享到微信或者朋友时，标题前面的小图标，拥有分享权限的公众号有效。',
        ],
        [
            'name' => 'vote_desc',
            'title' => '描述',
            'type' => 'textarea',
            'value' => '',
            'placeholder' => '',
            'tip' => '分享发送朋友时，可以看到的描述内容。',
        ],
        [
        'name' => 'banner',
        'title' => 'banner图',
        'type' => 'images',
        'value' => '',
        'placeholder' => '',
        'tip' => '主页面上的滚动图，建使用1-3张，太多会影响页面打开速度。',
        ],
        [
            'name' => 'start_time',
            'title' => '开始时间',
            'type' => 'date',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'end_time',
            'title' => '结束时间',
            'type' => 'date',
            'value' => '',
            'placeholder' => '',
            'tip' => '',
        ],
        [
            'name' => 'number_of_times',
            'title' => '投票限制',
            'type' => 'text',
            'value' => '1',
            'placeholder' => '',
            'tip' => '每人每天可投票次数。',
        ],
        [
            'name' => 'text_color',
            'title' => '风格颜色',
            'type' => 'text',
            'value' => '#ffb800',
            'placeholder' => '',
            'tip' => '页面风格颜色，你方可使用你喜欢的颜色。',
        ],
        [
            'name' => 'rule',
            'title' => '活动规则',
            'type' => 'textarea',
            'value' => '',
            'placeholder' => '',
            'tip' => '活动的规则，说明支持HTML代码。',
        ],
    ),

);