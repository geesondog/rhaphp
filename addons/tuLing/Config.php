<?php
return array(
    'name' => '图灵机器人',
    'addon' => 'tuLing',
    'desc' => '微信智能聊天机器人插件，可在微信端开启机器人聊天模式',
    'version' => '1.0',
    'author' => 'jinmandou',
    'logo' => 'logo.png',
    'menu_show' => '1',
    'entry_url' => '',                   // 如果这应用需要入口的，比较微信商城，那么填写入口，写法 应用名称/ 控制器/方法
    //  'install_sql' => 'install.sql',  // 数据库安装文件
    'upgrade_sql' => '',                //升级的数据库文件,如果目录存在这个文件，后台自动出现升级
    'config' => array(// 应用配置参数
        [
            'name' => 'can_voice',
            'title' => '是否开启语音聊天',
            'type' => 'radio',
            'value' => [
                0 => [
                    'title' => '不开启',
                    'value' => '0',
                    'checked' => '1'
                ],
                1 => [
                    'title' => '开启',
                    'value' => '1',
                    'checked' => '0'
                ]
            ],
            'placeholder' => '',
            'tip' => '开启语音聊天，需要在微信后台开启语音识别功能。'
        ],
         'api_url'=> [
                        'name' => 'api_url',
			'title' => '图灵API地址',
			'type' => 'text',
			'placeholder' => 'http://www.tuling123.com/openapi/api',
			'value' => 'http://www.tuling123.com/openapi/api',
			'tip' => ''
                    ],
	'api_key'=> [   'name'=>	'api_key',
			'title' => '图灵API KEY',
			'type' => 'text',
			'placeholder' => '',
			'value' => '',
			'tip' => '<a href="http://www.tuling123.com/web/robot_access!index.action?cur=l_05" target="_blank">前往图灵机器人官网申请API</a>'
		],
	'enter_tip' =>	[ 'name'=>'enter_tip' ,
			'title' => '进入聊天提示语',
			'type' => 'textarea',
			'placeholder' => '你想聊点什么呢',
			'value' => '',
			'tip' => '用户发送关键词进入机器人聊天模式时回复给用户的内容'
		],
	'keep_time' =>	['name'=>	'keep_time' ,
			'title' => '会话保持时间',
			'type' => 'text',
			'placeholder' => '300',
			'value' => '',
			'tip' => '在此时间范围内，用户一直处在机器人聊天模式中，默认300秒（5分钟）'
		],
	'exit_keyword'=>[   'name' =>'exit_keyword' ,
			'title' => '退出聊天关键词',
			'type' => 'text',
			'placeholder' => '不聊了',
			'value' => '',
			'tip' => '用户发送此关键词主动退出机器人聊天模式'
		],
		[   'name'=>'exit_tip',
			'title' => '退出聊天提示语',
			'type' => 'textarea',
			'placeholder' => '下次无聊的时候可以再找我聊天哦',
			'value' => '',
			'tip' => '用户退出机器人聊天模式时回复给用户的内容'
		]
	)
);