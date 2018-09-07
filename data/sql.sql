CREATE TABLE IF NOT EXISTS `rh_addons` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '插件名称',
  `addon` varchar(50) NOT NULL COMMENT '标识名',
  `desc` text COMMENT '描述',
  `version` varchar(10) NOT NULL COMMENT '版本号',
  `author` varchar(50) NOT NULL COMMENT '作者姓名',
  `logo` text COMMENT 'LOGO',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '插件配置',
  `entry_url` varchar(160) NOT NULL COMMENT '前端入口',
  `admin_url` varchar(160) NOT NULL COMMENT '后台入口',
  `menu_show` tinyint(1) NOT NULL COMMENT '是否在菜单显示1：显示0：隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用表';

CREATE TABLE IF NOT EXISTS `rh_addon_info` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `addon` varchar(50) NOT NULL COMMENT '插件标识',
  `infos` text NOT NULL COMMENT '配置信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件配置参数表';

CREATE TABLE IF NOT EXISTS `rh_admin` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `admin_name` varchar(60) NOT NULL COMMENT '管理员登录',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1：正常2：禁用',
  `ip` varchar(16) DEFAULT NULL COMMENT '登录 IP',
  `last_time` int(10) DEFAULT NULL COMMENT '最后登录时间',
  `rand_str` varchar(180) NOT NULL COMMENT '密码附加字符',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '超级管理 ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_material` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `type` varchar(50) DEFAULT NULL COMMENT '素材类型',
  `title` varchar(255) DEFAULT NULL COMMENT '素材名称|标题',
  `url` varchar(500) DEFAULT NULL COMMENT '素材资源地址|图文封面',
  `description` text COMMENT '图文素材描述',
  `content` mediumtext COMMENT '文本素材内容',
  `detail` text COMMENT '图文素材详情',
  `link` varchar(255) DEFAULT NULL COMMENT '图文链接',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `media_id` varchar(120) DEFAULT NULL COMMENT '媒体 ID',
  `from_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:本地1：公众号',
  `path` varchar(500) DEFAULT NULL COMMENT '资源路径'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号素材表';

CREATE TABLE IF NOT EXISTS `rh_media_news` (
  `news_id` int(11) NOT NULL COMMENT '自增 ID',
  `mid` int(11) NOT NULL COMMENT '公众号标识',
  `media_id` varchar(120) DEFAULT NULL COMMENT '媒体 ID',
  `title` text COMMENT '标题',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1:文本2:单图文3:多图文',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已上传，0未上传,3已经群发'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_media_news_list` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `news_id` int(11) NOT NULL COMMENT '主题 ID',
  `cover` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT '封面',
  `thumb_media_id` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT '媒体 ID',
  `author` varchar(80) CHARACTER SET utf8 DEFAULT NULL COMMENT '作者',
  `title` varchar(180) DEFAULT NULL COMMENT '标题',
  `content_source_url` text CHARACTER SET utf8 COMMENT '链接',
  `content` mediumtext COMMENT '内容',
  `digest` text COMMENT '描述',
  `show_cover_pic` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1为显示，0为不显示',
  `status_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已上传，2未上传',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rh_media_news_material` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `mid` int(11) NOT NULL COMMENT '公众号标识',
  `url` text COMMENT '地址',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1图片：2视频',
  `path` varchar(500) DEFAULT NULL COMMENT '本地路径'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_member_group` (
  `gid` int(11) NOT NULL COMMENT '组 ID',
  `mpid` int(11) NOT NULL COMMENT '公众号标识',
  `group_name` varchar(60) NOT NULL COMMENT '组等级名称',
  `up_score` int(11) NOT NULL DEFAULT '0' COMMENT '升级积分条件',
  `up_money` int(11) NOT NULL DEFAULT '0' COMMENT '升级消费金额条件',
  `up_type` int(11) NOT NULL DEFAULT '0' COMMENT '升级条件类型0为或：1为且',
  `discount` int(11) NOT NULL DEFAULT '0' COMMENT '折扣率',
  `description` varchar(250) NOT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_member_wealth_record` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员 ID',
  `mpid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号标识',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `time` int(10) DEFAULT NULL COMMENT '时间',
  `type` tinyint(1) NOT NULL COMMENT '1为积分，2金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_menu` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `pid` int(5) NOT NULL COMMENT '上级ID',
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `url` varchar(180) NOT NULL COMMENT 'Url函数地址',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  `icon` varchar(180) DEFAULT NULL COMMENT '图标',
  `child` varchar(5) DEFAULT NULL,
  `shows` varchar(5) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

INSERT INTO `rh_menu` (`id`, `pid`, `name`, `url`, `sort`, `icon`, `child`, `shows`) VALUES
(1, 0, '公众号', 'mp/mp/index', 0, '&#xe63a;', '', ''),
(2, 0, '系统管理', 'admin/system/index', 3, '&#xe620;', '', ''),
(13, 1, '微信功能', 'null', 0, '&#xe60a;', '', ''),
(14, 13, '自动回复', 'mp/mp/autoreply', 0, '', '', ''),
(15, 13, '自定义菜单', 'mp/mp/menu', 2, '', '', ''),
(16, 13, '功能配置', 'mp/mp/mpsetting', 3, '', '', ''),
(17, 13, '二维码/转化链接 ', 'mp/mp/qrcode', 4, '', '', ''),
(18, 13, '素材管理', 'mp/material/index', 5, '', '', ''),
(19, 1, '粉丝会员', 'null', 2, '&#xe654;', '', ''),
(20, 19, '粉丝管理', 'mp/friends/index', 0, '', '', ''),
(21, 2, '微信平台', 'null', 0, '&#xe60a;', '', ''),
(22, 21, '公众号管理', 'mp/index/mplist', 1, '', '', ''),
(23, 22, '增加公众号', 'mp/index/addmp', 1, '', '', ''),
(24, 22, '公众号列表', 'mp/index/mplist', 2, '', '', ''),
(25, 2, '菜单设置', 'NULL', 1, '&#xe670;', '', ''),
(26, 25, '菜单列表', 'admin/system/menulist', 1, '', '', ''),
(27, 25, '增加菜单', 'admin/system/addmenu', 2, '', '', ''),
(28, 22, '公众号列表', 'mp/index/mplist', 0, '', '', ''),
(29, 22, '接入信息', 'mp/index/index', 0, '', '', ''),
(30, 17, '增加二维码', 'mp/mp/qrcodeadd', 0, '', '', ''),
(32, 14, '增加关键词', 'mp/mp/addkeyword', 2, '', '', ''),
(41, 14, '特殊消息', 'mp/mp/special', 0, '', '', ''),
(43, 0, '应用中心', 'admin/app/index', 4, '&#xe635;', '', ''),
(45, 43, '应用管理', 'null', 1, '&#xe617;', '', ''),
(46, 19, '授权&注册', 'mp/member/index', 1, '', NULL, NULL),
(47, 13, '消息管理', 'mp/message/messagelist', 1, '', NULL, NULL),
(48, 47, '回复消息', 'mp/message/replymsg', 0, '', NULL, NULL),
(50, 26, '修改菜单', 'admin/system/updatemenu', 0, '', NULL, NULL),
(51, 22, '修改公众号', 'mp/index/updatemp', 0, '', NULL, NULL),
(52, 2, '后台管理', 'NULL', 2, '&#xe663;', NULL, NULL),
(53, 52, '管理成员', 'admin/system/adminmember', 0, '', NULL, NULL),
(54, 53, '更改密码', 'admin/system/updatepwd', 0, '', NULL, NULL),
(55, 53, '增加成员', 'admin/system/addadminmember', 0, '', NULL, NULL),
(56, 45, '微信公众号', 'admin/app/index', 0, '', NULL, NULL),
(57, 52, '系统升级', 'admin/upgrade/index', 1, '', NULL, NULL),
(58, 13, '图文群发', 'mp/mp/newslist', 6, '', NULL, NULL),
(59, 58, '增加图文', 'mp/mp/addnews', 0, '', NULL, NULL),
(60, 58, '修改图文', 'mp/mp/editnews', 0, '', NULL, NULL),
(61, 0, '小程序', 'miniapp/miniapp/topnav', 2, '', NULL, NULL),
(62, 21, '小程序管理', 'mp/index/miniapplists', 2, '', NULL, NULL),
(63, 62, '增加小程序', 'mp/index/addminiapp', 0, '', NULL, NULL),
(64, 62, '修改小程序', 'mp/index/upminiapp', 0, '', NULL, NULL),
(65, 62, '小程序配置信息', 'mp/index/miniappsetinginfo', 0, '', NULL, NULL),
(66, 45, '微信小程序', 'admin/miniapp/index', 1, '', NULL, NULL),
(67, 61, 'Mini Program功能', 'NULL', 0, '&#xe670;', NULL, NULL),
(68, 67, '功能配置', 'miniapp/miniapp/setting', 3, '', NULL, NULL),
(69, 67, '会员管理', 'miniapp/miniapp/userlist', 0, '&#xe68b;', NULL, NULL),
(70, 67, '消息管理', 'miniapp/miniapp/getmsglist', 1, '', NULL, NULL),
(71, 67, '数据分析', 'miniapp/index/index', 2, '', NULL, NULL),
(72, 70, '回复消息', 'miniapp/miniapp/replymsg', 0, '', NULL, NULL),
(73, 74, '应用商店', 'admin/appstore/index', 2, '', NULL, NULL),
(74, 43, 'official Service', 'NULL', 1, '&#xe619;', NULL, NULL),
(75, 73, '应用中心-注册', 'admin/appstore/register', 0, '', NULL, NULL),
(76, 73, '用户登录', 'admin/appstore/login', 0, '', NULL, NULL),
(77, 74, '应用升级', 'admin/appstore/upgrade', 3, '', NULL, NULL);

CREATE TABLE IF NOT EXISTS `rh_miniapp` (
  `id` int(10) unsigned NOT NULL COMMENT '自增ID',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `name` varchar(50) DEFAULT NULL COMMENT '小程序名称',
  `appid` varchar(50) DEFAULT NULL COMMENT 'AppId',
  `appsecret` varchar(50) DEFAULT NULL COMMENT 'AppSecret',
  `origin_id` varchar(50) DEFAULT NULL COMMENT '小程序原始ID',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '1认证2普通',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常，2：审核中）',
  `token` varchar(50) DEFAULT NULL COMMENT '公众号标识',
  `encodingaeskey` varchar(50) DEFAULT NULL COMMENT '消息加解密秘钥',
  `addon` varchar(50) DEFAULT NULL COMMENT '应用扩展标识',
  `desc` text COMMENT '描述',
  `logo` varchar(255) DEFAULT NULL COMMENT 'logo',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '当前使用',
  `close_msg` text COMMENT '关闭提示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号表';

CREATE TABLE IF NOT EXISTS `rh_miniapp_addon` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '插件名称',
  `addon` varchar(50) NOT NULL COMMENT '标识名',
  `desc` text COMMENT '描述',
  `version` varchar(10) NOT NULL COMMENT '版本号',
  `author` varchar(50) NOT NULL COMMENT '作者姓名',
  `logo` text COMMENT 'LOGO',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '插件配置'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用表';

CREATE TABLE IF NOT EXISTS `rh_miniapp_addon_info` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '小程序标识',
  `addon` varchar(50) NOT NULL COMMENT '插件标识',
  `infos` text NOT NULL COMMENT '配置信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件配置参数表';

CREATE TABLE IF NOT EXISTS `rh_miniapp_msg` (
  `msg_id` int(11) NOT NULL COMMENT '自增 ID',
  `openid` varchar(64) DEFAULT NULL COMMENT 'openid',
  `mpid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号标识',
  `type` varchar(32) DEFAULT NULL COMMENT '消息类型',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '消息内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未回复，1已回复',
  `is_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1为回复内容',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_miniapp_user` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(64) NOT NULL COMMENT 'openid',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
  `avatarurl` varchar(255) DEFAULT NULL COMMENT '头像',
  `gender` tinyint(1) DEFAULT NULL COMMENT '性别',
  `relname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `signature` text COMMENT '个性签名',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `language` varchar(50) DEFAULT NULL COMMENT '使用语言',
  `country` varchar(50) DEFAULT NULL COMMENT '国家',
  `province` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '省',
  `city` varchar(50) DEFAULT NULL COMMENT '城市',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `score` int(10) DEFAULT '0' COMMENT '积分',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金钱',
  `unionid` varchar(64) DEFAULT NULL COMMENT 'unionid字段',
  `password` varchar(64) DEFAULT NULL COMMENT '密码',
  `create_time` int(10) DEFAULT '0' COMMENT '注册时间',
  `last_time` int(10) DEFAULT '586969200' COMMENT '最后交互时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小程序会员';

CREATE TABLE IF NOT EXISTS `rh_mp` (
  `id` int(10) unsigned NOT NULL COMMENT '自增ID',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `name` varchar(50) NOT NULL COMMENT '公众号名称',
  `appid` varchar(50) DEFAULT NULL COMMENT 'AppId',
  `appsecret` varchar(50) DEFAULT NULL COMMENT 'AppSecret',
  `origin_id` varchar(50) NOT NULL COMMENT '公众号原始ID',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '公众号类型（1：普通订阅号；2：认证订阅号；3：普通服务号；4：认证服务号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常，2：审核中）',
  `valid_token` varchar(40) DEFAULT NULL COMMENT '接口验证Token',
  `valid_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已接入；0未接入',
  `token` varchar(50) DEFAULT NULL COMMENT '公众号标识',
  `encodingaeskey` varchar(50) DEFAULT NULL COMMENT '消息加解密秘钥',
  `mp_number` varchar(50) DEFAULT NULL COMMENT '微信号',
  `desc` text COMMENT '描述',
  `logo` varchar(255) DEFAULT NULL COMMENT 'logo',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `login_name` varchar(50) DEFAULT NULL COMMENT '公众号登录名',
  `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '当前使用'
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='公众号表';

INSERT INTO `rh_mp` (`id`, `user_id`, `name`, `appid`, `appsecret`, `origin_id`, `type`, `status`, `valid_token`, `valid_status`, `token`, `encodingaeskey`, `mp_number`, `desc`, `logo`, `qrcode`, `create_time`, `login_name`, `is_use`) VALUES
(1, 1, '测试公众号', 'wxb8862c9e7cb27654', 'c280978c74a8749d74b0e504224bdf35', 'gh_d059d8896214f', 1, 1, 'MIL4umO8pWIkfNhvNx01uBDupfUqS7J4', 1, 'gVzaHNIx9RiO40KiXbJScNN2t7SLF2gl', 'ofVvs4t2lbhQBrzZ2JhFWu7P2q07SHTGmeCfIwHu8pL', 'demomp', '系统维护中，请稍后。', '', '', 1505629364, NULL, 1);

CREATE TABLE IF NOT EXISTS `rh_mp_friends` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(64) NOT NULL COMMENT 'openid',
  `nickname` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '昵称',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注',
  `subscribe_time` int(10) DEFAULT NULL COMMENT '关注时间',
  `unsubscribe_time` int(10) DEFAULT NULL COMMENT '取消关注时间',
  `relname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `signature` text COMMENT '个性签名',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `is_bind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否绑定',
  `language` varchar(50) DEFAULT NULL COMMENT '使用语言',
  `country` varchar(50) DEFAULT NULL COMMENT '国家',
  `province` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '省',
  `city` varchar(50) DEFAULT NULL COMMENT '城市',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `group_id` int(10) DEFAULT '0' COMMENT '分组ID',
  `groupid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号分组标识',
  `tagid_list` varchar(255) DEFAULT NULL COMMENT '标签',
  `score` int(10) DEFAULT '0' COMMENT '积分',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金钱',
  `latitude` varchar(50) DEFAULT NULL COMMENT '纬度',
  `longitude` varchar(50) DEFAULT NULL COMMENT '经度',
  `location_precision` varchar(50) DEFAULT NULL COMMENT '精度',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0:公众号粉丝1：注册会员',
  `unionid` varchar(64) DEFAULT NULL COMMENT 'unionid字段',
  `password` varchar(64) DEFAULT NULL COMMENT '密码',
  `last_time` int(10) DEFAULT '586969200' COMMENT '最后交互时间',
  `subscribe_scene` varchar(255) DEFAULT NULL,
  `qr_scene` varchar(255) DEFAULT NULL,
  `qr_scene_str` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号粉丝表';

CREATE TABLE IF NOT EXISTS `rh_mp_menu` (
  `id` bigint(16) unsigned NOT NULL,
  `mp_id` int(11) DEFAULT '0' COMMENT '公众号标识',
  `index` bigint(20) DEFAULT '0',
  `pindex` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `type` varchar(24) DEFAULT NULL COMMENT '菜单类型 null主菜单 link链接 keys关键字 event事件',
  `name` varchar(256) DEFAULT NULL COMMENT '菜单名称',
  `content` text COMMENT '文字内容',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(0禁用1启用)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信自定义菜单表';

CREATE TABLE IF NOT EXISTS `rh_mp_msg` (
  `msg_id` int(11) NOT NULL COMMENT '自增 ID',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '上一条消息 ID',
  `openid` varchar(64) DEFAULT NULL COMMENT 'openid',
  `mpid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号标识',
  `type` varchar(32) DEFAULT NULL COMMENT '消息类型',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '消息内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未回复，1已回复',
  `is_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1为公众号回复',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_mp_reply` (
  `reply_id` int(11) NOT NULL COMMENT '自增ID',
  `type` varchar(60) NOT NULL COMMENT '回复类型：text,images,news,voice,music,video',
  `title` varchar(250) DEFAULT NULL COMMENT '标题(适用图文)',
  `content` text COMMENT '文本内容',
  `url` varchar(500) DEFAULT NULL COMMENT '资源地址',
  `link` varchar(500) DEFAULT NULL COMMENT '连接(图片连接，图文连接等)',
  `status_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:永久0：临时',
  `media_id` varchar(120) DEFAULT NULL COMMENT '媒体ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_mp_rule` (
  `id` int(10) NOT NULL COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号ID',
  `addon` varchar(50) DEFAULT NULL COMMENT '插件标识',
  `keyword` varchar(80) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '关键词内容',
  `type` varchar(50) DEFAULT NULL COMMENT '触发类型：text,addon,images,news,voice,music,video',
  `event` varchar(50) DEFAULT NULL COMMENT '特殊事件如:关注、取关等',
  `entry_id` int(10) DEFAULT NULL COMMENT '功能入口ID',
  `reply_id` int(10) DEFAULT NULL COMMENT '自动回复ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1开户:0关闭)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号响应规则';

CREATE TABLE `rh_payment` (
  `payment_id` int(11) NOT NULL COMMENT '自增 ID',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户 ID',
  `openid` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'OPENID',
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '标题|商品名称',
  `order_number` varchar(32) NOT NULL DEFAULT '0' COMMENT '订单号',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '交易类型（1为微信2为支付宝）',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0：未完成交易1：完成关键交易）',
  `create_time` int(10) NOT NULL COMMENT '交易时间',
  `mpid` int(11) NOT NULL COMMENT '公众号标识',
  `remark` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  `attach` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '附加数据',
  `refund` tinyint(1) DEFAULT '0' COMMENT '1：申请退款中2：退款完成'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rh_qrcode` (
  `id` int(10) unsigned NOT NULL COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `scene_id` int(32) DEFAULT NULL COMMENT '场景值ID',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `qr_type` char(32) DEFAULT '0' COMMENT '二维码类型',
  `scene_str` varchar(255) DEFAULT NULL COMMENT '场景值字符串',
  `expire` int(10) DEFAULT NULL COMMENT '过期时间',
  `ticket` varchar(150) DEFAULT NULL COMMENT '二维码Ticket',
  `short_url` varchar(255) DEFAULT NULL COMMENT '二维码短地址',
  `qrcode_url` text NOT NULL COMMENT '二维码原始地址',
  `url` varchar(255) DEFAULT NULL COMMENT '二维码图片解析后的地址',
  `create_time` int(10) DEFAULT NULL COMMENT '二维码创建时间',
  `scan_count` int(11) NOT NULL DEFAULT '0' COMMENT '扫码次数',
  `gz_count` int(11) NOT NULL DEFAULT '0' COMMENT '关注数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_qrcode_data` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `scene_id` int(11) NOT NULL COMMENT '场景 ID',
  `openid` varchar(64) NOT NULL COMMENT 'openid',
  `create_time` varchar(60) NOT NULL COMMENT '扫码时间',
  `mpid` int(11) NOT NULL COMMENT '公众号标识',
  `qrcode_id` int(11) NOT NULL COMMENT '二维码ID',
  `scan_count` int(11) NOT NULL DEFAULT '1' COMMENT '扫码次数',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:默认1:扫码关注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_redpack` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `openid` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'openid',
  `order_number` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT '单号',
  `mpid` int(11) NOT NULL DEFAULT '0' COMMENT '公众号标识',
  `money` decimal(10,2) NOT NULL COMMENT '红包金额',
  `nick_name` varchar(255) DEFAULT NULL COMMENT '提供方名称',
  `send_name` varchar(255) DEFAULT NULL COMMENT '发送者名称',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `addon` varchar(60) DEFAULT NULL COMMENT '应用扩展标识',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1：正常0：过期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rh_setting` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `mpid` int(11) NOT NULL COMMENT '公众号标识',
  `name` varchar(180) NOT NULL COMMENT '配置项名称',
  `value` text NOT NULL COMMENT '配置值',
  `cate` varchar(30) DEFAULT NULL COMMENT '分类'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_syn_openid` (
  `id` int(11) NOT NULL,
  `mpid` int(11) NOT NULL,
  `openid` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rh_picture` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL COMMENT '文件名称',
  `mpid` int(11) NOT NULL,
  `thumb` varchar(255) NOT NULL COMMENT '缩略图',
  `picture` varchar(255) NOT NULL COMMENT '原图',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:公众号，2:小程序',
  `create_time` int(11) NOT NULL,
  `reduce` varchar(255) NOT NULL COMMENT '质量缩小正方图'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rh_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL COMMENT '会员 ID',
  `mpid` int(11) NOT NULL COMMENT '公众标识',
  `name` varchar(60) NOT NULL COMMENT '收货人',
  `phone` varchar(18) NOT NULL COMMENT '手机号码',
  `address` text NOT NULL COMMENT '详细地址',
  `take_type` varchar(60) NOT NULL COMMENT '收货时间类型',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1默认地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员收货地址';

CREATE TABLE `rh_prefix_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='队列任务表';

ALTER TABLE `rh_picture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mpid` (`mpid`),
  ADD KEY `name` (`name`);

ALTER TABLE `rh_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addon` (`addon`);

ALTER TABLE `rh_addon_info`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_admin`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`);

ALTER TABLE `rh_media_news`
  ADD PRIMARY KEY (`news_id`);

ALTER TABLE `rh_media_news_list`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_media_news_material`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_member_group`
  ADD PRIMARY KEY (`gid`);

ALTER TABLE `rh_member_wealth_record`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_menu`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_miniapp`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_miniapp_addon`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_miniapp_addon_info`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_miniapp_msg`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `openid` (`openid`),
  ADD KEY `mpid` (`mpid`);

ALTER TABLE `rh_miniapp_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mpid` (`mpid`),
  ADD KEY `openid` (`openid`),
  ADD KEY `unionid` (`unionid`);

ALTER TABLE `rh_mp`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_mp_friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`),
  ADD KEY `mpid` (`mpid`);

ALTER TABLE `rh_mp_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wechat_menu_pid` (`pindex`) USING BTREE;

ALTER TABLE `rh_mp_msg`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `openid` (`openid`);

ALTER TABLE `rh_mp_reply`
  ADD PRIMARY KEY (`reply_id`);

ALTER TABLE `rh_mp_rule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keyword` (`keyword`);

ALTER TABLE `rh_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `openid` (`openid`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `mpid` (`mpid`),
  ADD KEY `order_number` (`order_number`);

ALTER TABLE `rh_qrcode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mpid` (`mpid`),
  ADD KEY `ticket` (`ticket`),
  ADD KEY `keyword` (`keyword`);

ALTER TABLE `rh_qrcode_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`),
  ADD KEY `mpid` (`mpid`);

ALTER TABLE `rh_redpack`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_setting`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_syn_openid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`),
  ADD KEY `mpid` (`mpid`);

ALTER TABLE `rh_picture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `rh_addons`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_addon_info`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_material`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_media_news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_media_news_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_media_news_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_member_group`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT COMMENT '组 ID';
ALTER TABLE `rh_member_wealth_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',AUTO_INCREMENT=77;
ALTER TABLE `rh_miniapp`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_miniapp_addon`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_miniapp_addon_info`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_miniapp_msg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_miniapp_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_mp`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',AUTO_INCREMENT=2;
ALTER TABLE `rh_mp_friends`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_mp_menu`
  MODIFY `id` bigint(16) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `rh_mp_msg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_mp_reply`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_mp_rule`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_qrcode`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键';
ALTER TABLE `rh_qrcode_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_redpack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
ALTER TABLE `rh_syn_openid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;