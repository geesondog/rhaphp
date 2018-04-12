<?php
return array(
    'name' => '万能表单',
    'addon' => 'nbforms',
    'desc' => '万能的表单设计器，报名、预约、信息提交等等。',
    'version' => '1.0',
    'author' => 'Geeson',
    'logo' => 'logo.png',
    'menu_show' => '1',
    'entry_url' => '',
    'install_sql' => 'install.sql',
    'upgrade_sql' => '',
    'menu' => [
        [
            'name' => '新增表单',
            'url' => 'nbforms/Forms/addForm',
            'icon' => ''
        ],
        [
            'name' => '表单列表',
            'url' => 'nbforms/Forms/lists',
            'icon' => ''
        ],
    ],


);