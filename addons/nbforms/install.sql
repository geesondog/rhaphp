CREATE TABLE IF NOT EXISTS `rh_forms` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `mid` int(11) NOT NULL COMMENT '公众号标识',
  `title` varchar(250) NOT NULL COMMENT '标题',
  `picurl` text CHARACTER SET utf8 NOT NULL COMMENT '封面URL',
  `content` text NOT NULL COMMENT '描述',
  `success_msg` varchar(250) CHARACTER SET utf8 NOT NULL COMMENT '保存成功提示',
  `attr_value` text CHARACTER SET utf8 NOT NULL COMMENT '表单属性',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `keyword` varchar(60) CHARACTER SET utf8 NOT NULL COMMENT '关键词',
  `template` varchar(60) CHARACTER SET utf8 NOT NULL DEFAULT 'default' COMMENT '模板',
  `jump_url` text NOT NULL COMMENT '提交成功后跳转地址',
  `reply_id` int(11) NOT NULL DEFAULT '0' COMMENT '回复规则 ID',
  `title2` varchar(250) DEFAULT NULL COMMENT '副标题',
  `content2` text COMMENT '规则说明'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rh_forms_values` (
  `id` int(11) NOT NULL COMMENT '自增 ID',
  `forms_id` int(11) NOT NULL COMMENT '表单 ID',
  `mid` int(11) NOT NULL COMMENT '公众号标识',
  `val` text CHARACTER SET utf8mb4 NOT NULL COMMENT '值',
  `create_time` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `rh_forms`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `rh_forms_values`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `rh_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';
ALTER TABLE `rh_forms_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增 ID';